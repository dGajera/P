
//)get location
$(document).ready( function() {
	$.getJSON("http://freegeoip.net/json/", function(result){
		alert('Country: ' + result.country_name + '\n' + 'Code: ' + result.country_code);
	});
}); 
//get current location
jQuery(function () {
	getLocation();
	function getLocation() {
		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(showPosition);
		} else { 
			//x.innerHTML = "Geolocation is not supported by this browser.";
		}
	}

	function showPosition(position) {
	var lat = parseFloat(position.coords.latitude);
				var lng = parseFloat(position.coords.longitude);
				var latlng = new google.maps.LatLng(lat, lng);
				var geocoder = geocoder = new google.maps.Geocoder();
				geocoder.geocode({ 'latLng': latlng }, function (results, status) {
					if (status == google.maps.GeocoderStatus.OK) {
						if (results[1]) {
							//alert("Location: " + results[1].formatted_address);
							jQuery('#pickupaddress').val(results[1].formatted_address);
						}
					}
				});
	}
})

//continuous call function
function page_ajax_3(){
	console.log("driver_accept_check");
	driver_accept_check();
	setInterval(function() {
			interval3 = driver_accept_check();
		}, 30000);	
}

//desktop notification
function desktopNotification(msg,icon,modarator) {
		
					
			  // Let's check if the browser supports notifications
			  if (!("Notification" in window)) {
				alert("This browser does not support desktop notification");
			  }

			  // Let's check if the user is okay to get some notification
			  else if (Notification.permission === "granted") {
				// If it's okay let's create a notification
			  var options = {
					body: msg,
					icon: icon,
					dir : "ltr"
				};
			  var notification = new Notification("Hi "+modarator+"!!",options);
			   
			  }

			  // Otherwise, we need to ask the user for permission
			  // Note, Chrome does not implement the permission static property
			  // So we have to check for NOT 'denied' instead of 'default'
			  else if (Notification.permission !== 'denied') {
				Notification.requestPermission(function (permission) {
				  // Whatever the user answers, we make sure we store the information
				  if (!('permission' in Notification)) {
					Notification.permission = permission;
				  }

				  // If the user is okay, let's create a notification
				  if (permission === "granted") {
					var options = {
						  body: msg,
						  icon: icon,
						  dir : "ltr"
					  };
					 var notification = new Notification("Hi "+modarator+"!!",options);
					
				  }
				
				});
			  }
			  return notification;
		}
//ajax
function refresh_desktop(){
		
		var type = 'dashboard'; 
		$.ajax({
			type: 'POST',
			url: '../ajax/dashboard_ajax.php',
			data: {type:type},
			success: function(data) {
			var d = data.split('@@@');
				console.log(d);
				
				
				if ($(".modrator_ajax").length > 0) {
					$('.incoming').html(d[0]);
					$('.incoming-badge').html(d[0]);
					$('.not_assigned-badge').html(d[1]);
					$('.not_assigned').html(d[1]);
					$('.pending').html(d[3]);
					$('.pending-badge').html(d[3]);
					$('.completed').html(d[4]);
					
				}
				if(d[0] > 0){
					
					var msg = d[6];
					var mod = d[7];
					var icn = 'http://whiteglovesme.com/admin/icon/hand-stop-o.png';
					var notificationC = desktopNotification(msg,icn,mod);	
					notificationC.onclick = function(){ var pagename = '../booking.php?action=new'; load_page(pagename); }
					$('#chatAudio')[0].play();
				}
			}
		});
	}

//simple ajax call
function refresh_admin_desktop(){
		console.log("im called");
		var type = 'dashboard'; 
		$.ajax({
			type: 'POST',
			url: 'ajax/dashboard_admin_ajax.php',
			data: {type:type},
			success: function(data) {
			console.log(data);
				
			}
		});
	}