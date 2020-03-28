<?php
echo $pre=<<<EOM

<!-- ====PRE===================-->
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
/*@media screen and ( max-width:1400px ){	
	#loading{
	position: absolute;
	left: 600px;
	top: 300px;
	}
}
@media screen and ( min-width:1401px ){
	#loading{
	position: absolute;
	left: 800px;
	top: 420px;
	}
}*/	
#loader-bg {
	position: fixed;
	width: 100%;
	height: 100%;
	top: 0px;
	left: 0px;
	background: #FFF;
	_background: transparent;
   _background: linear-gradient(90deg, #ffffff 0%, #dcdcdc 100%);
	z-index: 1;
}
</style>
<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js'></script>
<script>
$(function() {
	var h = $(window).height();
	$('.container').css('display','none');
	//$('.floatThead-container').css('display','none');
	$('#loader-bg ,#loader').height(h).css('display','block');
});
$(window).load(function () {
	//$('#loader-bg').delay(900).fadeOut(800);
	//$('#loader').delay(600).fadeOut(300);
	
	//$('#loader-bg').fadeOut(800);
	//$('#loader').fadeOut(300);

	$('#loader-bg').delay(1).fadeOut(1);
	$('#loader').delay(1).fadeOut(1);
	$('.container').css('display', 'block');
	//$('.floatThead-container').css('display', 'block');

	//$('#loader-bg').delay(1).fadeOut(1);
	//$('#loader').delay(1).fadeOut(1);
	//$('.container').css('display', 'block');
});
</script>
<!-- ====PRE===================-->
<div id="loader-bg" style="z-index:9999999999">
<div id="loading">

<!--loading部分-->
<!-- http://tobiasahlin.com/spinkit/ -->
<div class="spinner">
  <div class="rect1"></div>
  <div class="rect2"></div>
  <div class="rect3"></div>
  <div class="rect4"></div>
  <div class="rect5"></div>
</div>

<style>
.spinner {
  margin: 100px auto;
  width: 50px;
  height: 40px;
  text-align: center;
  font-size: 10px;

   	position: absolute;
	top: 30%;
	left: 50%;
	transform: translate(-30%, -50%);
 
}

.spinner > div {
  background-color: #333;
  background-color:#0097a7;

  height: 100%;
  width: 6px;
  display: inline-block;
  
  -webkit-animation: sk-stretchdelay 1.2s infinite ease-in-out;
  animation: sk-stretchdelay 1.2s infinite ease-in-out;
}

.spinner .rect2 {
  -webkit-animation-delay: -1.1s;
  animation-delay: -1.1s;
}

.spinner .rect3 {
  -webkit-animation-delay: -1.0s;
  animation-delay: -1.0s;
}

.spinner .rect4 {
  -webkit-animation-delay: -0.9s;
  animation-delay: -0.9s;
}

.spinner .rect5 {
  -webkit-animation-delay: -0.8s;
  animation-delay: -0.8s;
}

@-webkit-keyframes sk-stretchdelay {
  0%, 40%, 100% { -webkit-transform: scaleY(0.4) }  
  20% { -webkit-transform: scaleY(1.0) }
}

@keyframes sk-stretchdelay {
  0%, 40%, 100% { 
    transform: scaleY(0.4);
    -webkit-transform: scaleY(0.4);
  }  20% { 
    transform: scaleY(1.0);
    -webkit-transform: scaleY(1.0);
  }
}
</style>
<!--loading部分-->

</div>
</div>
<!-- ====PRE===================-->
EOM;

?>
