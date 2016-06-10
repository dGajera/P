<?php //disable right click,select text,copy paste?>
<script language='JavaScript'> 
var message="Function Disabled!"; 
function clickIE4(){ 
if (event.button==2){ 
//alert(message); 
return false; } }
 function clickNS4(e){ 
 if (document.layers||document.getElementById&&!document.all){
	  if (e.which==2||e.which==3){ return false; 
	  } } } if (document.layers){ document.captureEvents(Event.MOUSEDOWN); document.onmousedown=clickNS4; } else if (document.all&&!document.getElementById){ document.onmousedown=clickIE4; } 
	  document.oncontextmenu=new Function("return false");
		 $(document).on('keydown', function(e){
    if(e.ctrlKey && e.which === 83){ // Check for the Ctrl key being pressed, and if the key = [S] (83)
        console.log('Ctrl+S!');
        e.preventDefault();
        return false;
    }
});
</script>


<!--<body class="disable-select">-->
<style>
.disable-select{-webkit-user-select: none;
-khtml-user-select: none;
-moz-user-select: none;
-ms-user-select: none;
-o-user-select: none;
user-select: none;}
</style>

<?php //full screen div?>
<script>
$(document).ready(function() {
  function setHeight() {
    windowHeight = $(window).innerHeight();
    $('.sidebar').css('min-height', windowHeight);
  };
  setHeight();
  
  $(window).resize(function() {
    setHeight();
  });
});
</script>