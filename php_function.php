<?php 
//create log file
function system_log($text){
	$file_name = 'system_log.txt';
	$file = fopen($file_name, "a");
	if(is_array($text)){
		$text = 'Array : '.json_encode($text);
	}
	$cur_Date = date('Y-m-d H:i:s');
	$backtrack = debug_backtrace();
	$function = $backtrack[count($backtrack)-2];
	$location = 'Function = "'.$function['function'].'"; Line = '.$function['line'].';';
	$text = $cur_Date.' => '.$location.'  Log = "'.$text.'"; '.PHP_EOL;
	fwrite($file, $text);
}

// get Distance
function getDistance($lat1, $lon1, $lat2, $lon2, $unit = "m") {
	$theta = $lon1 - $lon2;
	$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
	$dist = acos($dist);
	$dist = rad2deg($dist);
	$miles = $dist * 60 * 1.1515;
	$unit = strtoupper($unit);
	if ($unit == "K") {	
		return ($miles * 1.609344);
	}elseif($unit == 'M'){
		return (($miles * 1.609344)*1000);
	} else if ($unit == "N") {
		return ($miles * 0.8684);
	} else {
		return $miles;
	}
}
	
// get Latitude and longtitude
function get_lat_long($address){
	$API_KEY = 'AIzaSyC70LnMBiqyXcmpnQeryzq0VK12o6P5pnw';
	$address = str_replace(" ", "+", $address);
	$url = "https://maps.googleapis.com/maps/api/geocode/json?address=".$address."&key=".$API_KEY."";
	$json = file_get_contents($url);
	$json = json_decode($json);
	if($json->status == 'ZERO_RESULTS'){
		$lat = 0;
		$long = 0; 	
	}else{
		$lat = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
		$long = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};	
	}
	
	$location = [$lat,$long];
	return $location;
}
// Send SMS

define('TWILLO_SID','AC35f35e45265ce9d1db4c878c03ce632b');
define('TWILLO_AUTH_TOKEN','a3b5d0f22954dabd9d5e4320147de9cb');
define('SITE_PHONE','972526284334'); 
function sendSms($to,$message){

	try {
		$client = new Services_Twilio(TWILLO_SID, TWILLO_AUTH_TOKEN); 
		$Message = array();
		$Message['from'] = '+'.ltrim(SITE_PHONE,'+');
		//$to = '91'.$to;
		//$to = '972'.$to; 
		$Message['to'] = '+'.ltrim($to,'+');
		$Message['body'] = $message;
		$load = array( 'From' => $Message['from'],	'To' => $Message['to'], 'Body' => $Message['body']);
		$api_response = $client->account->messages->create($load);
		
		$response = array();
		$response['status'] = $api_response->status;
		$response['error_code'] = $api_response->error_code;
		$response['error_message'] = $api_response->error_message;
		return true;
		return $response;
		
	} catch (Services_Twilio_RestException $e) {
		
		return false;
		$response = array();
		$response['status'] = $e->status;
		$response['message'] = $e->message;
		return $e;
		
	}
}
// Get Address Componant From 
function getAddressComponentsFromLatLng($args){
		$requvired = [	['latitude','latitude'] ,['longitude','longitude'] ];	
		if(! $this->checkAruguments($requvired,$args)){ return $this->_getStatusMessage(1, 1); }
		$lat = $args['latitude'];
		$lng = $args['longitude'];
		$geocode = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?latlng='.$lat.','.$lng.'&sensor=false');
		
		$api_response = array();
		$output= json_decode($geocode);
		for($j=0;$j<count($output->results[0]->address_components);$j++){
			$api_response[$output->results[0]->address_components[$j]->types[0]] = $output->results[0]->address_components[$j]->long_name;
		}
		$response = array('errNum'=>200,'errFlag'=>0,'errMsg'=>'ok','data'=>$api_response);
		
		return $response;
	}
// convert date
function convertDate($date){
		$date = date_parse_from_format('m-d-Y H:i:s',$date);
		$date = $date['year'].'-'.$date['month'].'-'.$date['day'].' '.$date['hour'].':'.$date['minute'].':'.$date['second'];
		return $date;	
	}
// send Email
function sendMail($EmailList ,$Subject ,$Message ,$CC = false,$BCC = false,$File = false ){
		
		$CC[] = SITE_EMAIL;
		$list = implode(', ',$EmailList);
		if($CC && is_array($CC)){
			$CC = implode(', ',$CC);	
		}
		if($BCC && is_array($BCC)){
			$BCC = implode(', ',$BCC);	
		}
		
		
		$header  = "MIME-Version: 1.0" . "\r\n";
		$header .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		$header .= "From: " . SITE_EMAIL . "\r\n";
		$header .= "Cc: " . $CC . "\r\n";
		if($BCC){
			$header .= "Bcc: " . $BCC . "\r\n";
		}
		
		
		$Message = wordwrap($Message,70);
		
		$flag = mail($list,$Subject,$Message,$header);
		return $flag;
	}
// parseQueryString
function parseQueryString($string){
		$response = explode('&',$string);
		$response_array = array();
		foreach($response as $obj){
			$temp = explode('=',$obj);
			$response_array[$temp[0]] = $temp[1];
		}
		return $response_array;
	}
// Google Location API
function getGoogleApiResponse($current_latitude,$current_longitude,$destination_latitude,$destination_longitude){
		$API_KEY = 'AIzaSyC70LnMBiqyXcmpnQeryzq0VK12o6P5pnw';
		$API_DISTANCE_URL = 'https://maps.googleapis.com/maps/api/distancematrix/json';
		
		$Origins = $current_latitude . ',' . $current_longitude;
		$Destination = $destination_latitude. ',' . $destination_longitude;
		$api_key = $API_KEY;
		$api_url = $API_DISTANCE_URL . '?key=' . $api_key;
		$api_url .= '&origins=' . $Origins;
		$api_url .= '&sensor=false';
		$api_url .= '&destinations=' . $Destination;
		$response = (array) json_decode(file_get_contents($api_url));

		if($response['status'] != 'OK'){ return false; }
		if($response['rows'][0]->elements[0]->status != 'OK'){ return false; }
		
		return $response;
	}
// ResourceToArray 
function ResourceToArray($resource){
		if(gettype($resource)=="resource" && $resource){
			$array = array();
			while($row = mysql_fetch_assoc($resource)){
				$array[] = $row;
			}
			return $array;
		}else{
			return false;	
		}
	}
	
//week_start_end_by_date
function week_start_end_by_date($date, $format = 'Y-m-d') {

        //Is $date timestamp or date?
        if (is_numeric($date) AND strlen($date) == 10) {
            $time = $date;
        } else {
            $time = strtotime($date);
        }

        $week['week'] = date('W', $time);
        $week['year'] = date('o', $time);
        $week['year_week'] = date('oW', $time);
        $first_day_of_week_timestamp = strtotime($week['year'] . "W" . str_pad($week['week'], 2, "0", STR_PAD_LEFT));
        $week['first_day_of_week'] = date($format, $first_day_of_week_timestamp);
        $week['first_day_of_week_timestamp'] = $first_day_of_week_timestamp;
        $last_day_of_week_timestamp = strtotime($week['first_day_of_week'] . " +6 days");
        $week['last_day_of_week'] = date($format, $last_day_of_week_timestamp);
        $week['last_day_of_week_timestamp'] = $last_day_of_week_timestamp;

        return $week;
    }
//display output
function display($object){
	if(gettype($object)=="resource" && $object)
	{
		$No=mysql_num_fields($object)
		or
		die(mysql_error()."<br>");
		echo "<table class='table table-bordered' style='width:100%;text-align:center;' >";
		echo "<tr>";
		for($i=0;$i<$No;$i++)
		{
			$FieldName=mysql_field_name($object,$i)
			or
			die(mysql_error()."<br>");
			echo "<th><label>".$FieldName."</label></th>";
		}
		echo "</tr>";
		while($row=mysql_fetch_row($object))
		{
			echo "<tr>";
			for($i=0;$i<$No;$i++)
			{
				 echo "<td>".$row[$i]."</td>";
			}
			echo "</tr>";
		}
		echo "</table>";
		
	}else{
		echo '<pre style="max-height:256px;overflow:auto;">';
			if(is_object($object) || is_array($object)){
				print_r($object); 
			}else{
				echo $object; 
			}
		echo '</pre>';	
	}
	return true;
}

//query with regexp
$query = "SELECT * FROM `city_available` WHERE is_active = 1 AND City_Name REGEXP  '^".$args['string']."'; ";    
//unset value from array
$invoice = $this->ResourceToArray($this->database->ExecuteQuery($query));
foreach($invoice as &$i){
	unset($i['transaction_id'],$i['response'],$i['slave_id'])	;
}
return array('errNum' => 200,'errFlag' => 0,'errMsg' => 'ok','data'=>$invoice);

//call remote api using curl
	$message_string = 'hello';
	$message = ['status'=>'200','message'=>$message_string,'ordertype'=>$order_type,'appointmenttype'=>$app_type,'appointmentid'=>$appointment];
	$slave_id = 101;
	$data = array("id" => array($slave_id),"message" =>$message, "type" => 2); 
	$url = 'http://whiteglovesme.com/api6.php/send_push_to_customer';
	$str_data = json_encode($data);

	function sendPostData($url, $post){
	  $ch = curl_init($url);
	  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	  curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
	  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
	  $result = curl_exec($ch);
	  curl_close($ch);  // Seems like good practice
	  return $result;
	}
	echo sendPostData($url, $str_data);
		
/* GET ADDRESS FROM LAT. LONG. */
		$url  = "http://maps.googleapis.com/maps/api/geocode/json?latlng=".
		$driver_lat.",".$driver_long."&sensor=false";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		$json = curl_exec($ch);
		curl_close ($ch);
		$data = json_decode($json);
		$status = $data->status;
		$address = '';
		$address = $data->results[0]->formatted_address;
		$address =str_replace(" ","+",$address);
		
/* GET eta*/
		$cust_add = str_replace(" ","+",$appointment_detail['address_line1']);
		$apikey ='AIzaSyCbj955s8NfqEhzyvPApSphxGJTrE0tDZU';
	     $url_2= 'https://maps.googleapis.com/maps/api/distancematrix/json?origins='.$cust_add.'&destinations='.$address.'&mode=driving&key='.$apikey;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url_2);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		$json = curl_exec($ch);
		curl_close ($ch);
		$data = json_decode($json,true);
		$distance = $data[rows][0][elements][0][distance][text];
		$duration = $data[rows][0][elements][0][duration][text];
		if (strpos($duration, 'hours') !== false) {
			$duration = str_replace(" hours","h",$duration);
		}
		if (strpos($duration, 'hour') !== false) {
			$duration = str_replace(" hour","h",$duration);
		}
		if (strpos($duration, 'mins') !== false) {
			$duration = str_replace(" mins","m",$duration);
		}
		if (strpos($duration, 'min') !== false) {
			$duration = str_replace(" min","m",$duration);
		}
		if (strpos($duration, ' ') !== false) {
			$duration = str_replace(" "," : ",$duration);
		}