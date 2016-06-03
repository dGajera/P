<?php /*---parallax scroll effect-----*/?>
<script>
	$(document).ready(function(){
   // cache the window object
   $window = $(window);
 
   $('li[data-type="background"]').each(function(){
     // declare the variable to affect the defined data-type
     var $scroll = $(this);
                     
      $(window).scroll(function() {
        // HTML5 proves useful for helping with creating JS functions!
        // also, negative value because we're scrolling upwards                             
        var yPos = -($window.scrollTop() / $scroll.data('speed')); 
         
        // background position
        var coords = '50% '+ yPos + 'px';
 
        // move the background
        $scroll.css({ backgroundPosition: coords });    
      }); // end window scroll
   });  // end section function
}); // close out script
</script>

    <li class="selected" data-type="background" data-speed="10">

<style>
.cd-hero-slider li:first-of-type  {
  background:url("../assets/banner1.jpg") ;
  background-attachment:fixed;
</style>

