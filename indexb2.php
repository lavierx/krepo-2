<?php
//------------インクルード-----------------//
include("pre.php");
require_once("define.php");//サーバ定義

$NT=date("Y-m-d");
$ac_f=urldecode($_GET[ac]);

//------------POST処理-----------------//
if($_POST[ac]<>""):
	list(,$_mid)=explode("\t",$_POST[ac]);
	$ac_en=urlencode($_POST[ac]);
	echo "<script>location.href = 'indexb2.php?mid={$_mid}&ac={$ac_en}';</script>"; 
endif;

//------------オートコンプリート-----------------//
set_include_path(get_include_path() . PATH_SEPARATOR . PATH); //ライブラリパスを追加
include_once 'Crypt/Blowfish.php';
//include_once 'Crypt/BlowfishOld.php';

//データベースに接続 //////////////////////////////////////
$con = mysql_connect(DB_SERVER,DB_USER,DB_PW);
///////////////////////////////////////////////////////////

//UTF8の文字化け対策
mysql_query('SET NAMES utf8', $con); // ←これ
	
//データベースを選択////////////////////////////////////////
mysql_select_db(DB_NAME, $con);
////////////////////////////////////////////////////////////

//SQL文をセット/////////////////////////////////////////////
//$quryset = mysql_query("SELECT * FROM  `member` ORDER BY  `u_datetime` ASC LIMIT 0 , 300;");
//$quryset = mysql_query("SELECT * FROM `c_member_secure` LIMIT 0, 500;");
// FROM 表名1 INNER JOIN表名2 ON 表名1.フィールド名 = 表名2.フィールド名
$quryset = mysql_query("
SELECT  `c_member_id` ,  `nickname` ,  `image_filename` 
FROM  `c_member` 
;");

////////////////////////////////////////////////////////////

//１ループで１行データが取り出され、データが無くなるとループを抜ける
while ($data = mysql_fetch_array($quryset)){
	/*
	// Blowfishデコード
	$blowfish = new Crypt_Blowfish(ENCRYPT_KEY);
	//$blowfish = new Crypt_BlowfishOld(ENCRYPT_KEY);
	$bindata = $data[4]; // DBからselectした結果など
	$decoded = base64_decode($bindata); // バイナリに戻す
	$decrypted = $blowfish->decrypt($decoded);
	*/
        //列9を出力//////////////
        $ac.=<<<EOM
        "{$data[1]}\t{$data[0]}":null,\n
EOM;
}        

//------------mid指定あり時の処理-----------------//
if($_GET[mid]<>""):

$pg='<div class="progress">
      <div class="indeterminate"></div>
  	</div>
';	

	//SQL文をセット/////////////////////////////////////////////
	//$quryset = mysql_query("SELECT * FROM  `member` ORDER BY  `u_datetime` ASC LIMIT 0 , 300;");
	//$quryset = mysql_query("SELECT * FROM `c_member_secure` LIMIT 0, 500;");
	// FROM 表名1 INNER JOIN表名2 ON 表名1.フィールド名 = 表名2.フィールド名
	$quryset = mysql_query("
	SELECT  `c_member_id` ,  `nickname` ,  `image_filename` 
	FROM  `c_member` 
	WHERE  `c_member_id` =".$_GET[mid]."
	LIMIT 0 , 1");

	////////////////////////////////////////////////////////////

	//１ループで１行データが取り出され、データが無くなるとループを抜
	while ($data = mysql_fetch_array($quryset)){

		/*
		// Blowfishデコード
		$blowfish = new Crypt_Blowfish(ENCRYPT_KEY);
		//$blowfish = new Crypt_BlowfishOld(ENCRYPT_KEY);
		$bindata = $data[4]; // DBからselectした結果など
		$decoded = base64_decode($bindata); // バイナリに戻す
		$decrypted = $blowfish->decrypt($decoded);
		*/
	        //列9を出力//////////////
	        $MIid = mb_convert_encoding($data[0], "utf-8", "auto");
	        //////////////////////////

	      //列9を出力//////////////
	        $MIname = mb_convert_encoding($data[1], "utf-8", "auto");
	        //////////////////////////

	      //列9を出力//////////////
	        $MIface = mb_convert_encoding($data[2], "utf-8", "auto");
	        // m_1_1466069920.jpg
	        //////////////////////////
	}

	$quryset = mysql_query("
	SELECT value
	FROM `c_member_profile`
	WHERE `c_member_id` ={$_GET[mid]}
	AND `c_profile_id` =5
	LIMIT 0 , 1");

	//１ループで１行データが取り出され、データが無くなるとループを抜け
	while ($data = mysql_fetch_array($quryset)){
		$prof=$data[0];
	}	

	$ext = substr($MIface, strrpos($MIface, '.') + 1);


	$ph="../../OpenPNE2/var/img_cache/{$ext}/w180_h180/img_cache_{$MIface}";
	$ph=str_replace(".{$ext}", "_{$ext}.{$ext}", $ph);
	$ph2="icon.png";
	$ph3="icon2.png";


	if (file_exists($ph)) {
	/*
		$im = new Imagick($ph);
		//$im->thumbnailImage(350, 0);//リサイズ
		//$im->modulateImage(80, 180, 100);
		$im->sketchImage(10,0,135);
		//$im->spreadImage(2);
		//$im->oilPaintImage(2);
		$im->vignetteImage(0, 1, 10, 10);//円形ぼかし
		$im->writeImage($ph2);
		//クリア	
		$im->destroy();
	*/	

		exec("convert {$ph} -colorspace gray -sketch 0x30+5 {$ph2}");//モノクロ
		//exec("convert {$ph} -sketch 0x20+135 {$ph2}");//カラー

		exec("convert {$ph2} -thumbnail 100x100 -background white -extent 100x100 \
  \( -size 100x100 xc:none -fill white -draw 'circle 50,50,50,0' \) \
  -compose CopyOpacity -composite icon.png");


		/* 画像を２値化して、任意の色と透明色にかえる */

		/* ２値化して光が溢れるような画像に変換する */
		/* ２値化してマスク作成 */
		$im = new Imagick($ph);
		$im->blackThresholdImage('#808080');
		$im->whiteThresholdImage('#808080');
		/* 反転 */
		$im->negateImage(true);
		$im->paintTransparentImage("black", 0, 0);
		/* アルファチャネルをぼかす */
		$im->blurImage(20, 10, Imagick::CHANNEL_ALPHA);

		/* マスクを利用して切り取り */
		$im2 = new Imagick($ph);
		$im->compositeImage($im2, Imagick::COMPOSITE_IN, 0, 0, Imagick::CHANNEL_ALL);

		/* 白背景と合成 */
		$im3 = new Imagick();
		$im3->newPseudoImage($im->getImageWidth(), $im->getImageHeight(), "xc:white");
		$im3->compositeImage($im, Imagick::COMPOSITE_DEFAULT, 0, 0);

		$im3->blurImage(5,1.7);
		//$im3->radialBlurImage(1);

		$im3->writeImage($ph3);
		$im3->destroy();
		$im2->destroy();
		$im->destroy();

		//exec("convert {$ph3} -colorspace gray {$ph3}");//モノクロ
		//exec("convert {$ph} -sketch 0x20+135 {$ph2}");//カラー

		exec("convert {$ph3} -thumbnail 100x100 -background white -extent 100x100 \
  \( -size 100x100 xc:none -fill white -draw 'circle 50,50,50,0' \) \
  -compose CopyOpacity -composite icon2.png");

	}

		$icon_s=<<<EOM
		<img src=icon.png> <img width=100 src=icon2.png>

		<span class="switch">
	    <label>
	      モノクロ
	      <input name=mc value=1 type="checkbox">
	      <span class="lever"></span>
	      カラー
	    </label>
	  	</span>
EOM;
	//exit;

endif;

$html=<<<EOM
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>活動レポ作成くん</title>
<!--jquery
<script type="text/javascript" src="//code.jquery.com/jquery-2.2.4.min.js"></script>--->
<!--materializecss-->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/css/materialize.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/js/materialize.min.js"></script>
<!--fontawesome-->
<link href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome-animation/0.0.10/font-awesome-animation.css" type="text/css" media="all" />	
<script type="text/javascript" src="/etc/lib/ten.js"></script>
<!--animate.css-->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.0/animate.min.css">

<!--materializecss-->
<script>
$(document).ready(function(){
	//ドロップダウンメニュー初期化
	//$(".dropdown-trigger").dropdown();	
	//ハンバーガーメニュー初期化
  //$('.sidenav').sidenav();
	//セレクト初期化
  //$('select').formSelect();
  //テキストエリア初期化
	//$('input#input_text, textarea#textarea1').characterCounter();
	//$('input#input_text, textarea#textarea2').characterCounter();
  M.AutoInit();//すべてのイニシャライズをまとめて行う!
    //デートピッカー
  //$('.datepicker').datepicker();
  $('.datepicker').datepicker({
    i18n:{
        months:["1月", "2月", "3月", "4月", "5月", "6月", "7月", "8月", "9月", "10月", "11月", "12月"],
        monthsShort: ["1/", "2/", "3/", "4/", "5/", "6/", "7/", "8/", "9/", "10/", "11/", "12/"],
        weekdaysFull: ["日曜日", "月曜日", "火曜日", "水曜日", "木曜日", "金曜日", "土曜日"],
        weekdaysShort:  ["日", "月", "火", "水", "木", "金", "土"],
        weekdaysAbbrev: ["日", "月", "火", "水", "木", "金", "土"],
        cancel:"キャンセル",
    },
    format: "yyyy-mm-dd",
  });
  //タイムピッカー
  $('.timepicker').timepicker();

  //オートコンプリート
  $('input.autocomplete').autocomplete({
  data: {
    //"Apple": null,
    //"Microsoft": null,
    //"Google": 'https://placehold.it/250x250'
    {$ac}
  },
　});

});

$(document).ready(function() {
	$('t1, textarea#t1').characterCounter();
	$('t2, textarea#t2').characterCounter();
	$('t3, textarea#t3').characterCounter();
	$('t4, textarea#t4').characterCounter();
});

</script>

<!--スムーズスクロール-->
<script>
$(function(){
$("a#arrow[href^='#']").click(function() {
// #で始まるアンカーをクリックした場合に処理
var speed = 500; // ミリ秒
// スクロールスピード
var href= $(this).attr("href");
// アンカーの値を取得
var target = $(href == "#" || href == "" ? 'html' : href);
// 移動先取得
var position = target.offset().top;
// 移動先を数値で取得
$('body,html').animate({scrollTop:position}, speed, 'swing');
// スムーススクロール実行
return false;
});
});
</script>

<!--POPUP-->
<link rel="stylesheet" type="text/css" href="/lib/js/fb//jquery.fancybox-1.3.4.css" media="screen" />
<!--<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.3/jquery.min.js?ver=1.6.3"></script>-->
<script type="text/javascript" src="/lib/js/fb//jquery.gptop-1.0.js?ver=3.3"></script>
<script type="text/javascript" src="/lib/js/fb//jquery.fancybox-1.3.4.pack.js?ver=3.3"></script>
<script type="text/javascript">
jQuery(function($) {
$('#goto_top').gpTop();
$('.iframe').fancybox({
maxWidth	: 800,
maxHeight	: 600,
fitToView	: false,
width		: '90%',
height		: '90%',
autoSize	: false,
closeClick	: false,
openEffect	: 'none',
closeEffect	: 'none'
});
$('.over').fancybox({
'titlePosition'  : 'over'
});
$('a[rel*=lightbox]').fancybox(); 
});
</script>
<!--POPUP-->

<!--サプミットボタン・エンター無効化-->
<script>
    $(function(){
        $("input").on("keydown", function(e) {
            if ((e.which && e.which === 13) || (e.keyCode && e.keyCode === 13)) {
                return false;
            } else {
                return true;
            }
        });
    });
</script>

<style>
body,td{
font-size:13px;font-family:"游ゴシック", "Yu Gothic", YuGothic,meiryo;padding:5px;
font-weight:500;
}
textarea{
background-color:brown;
background-color:#00838f;
color:white;
font-size:13px;
}
input{
color:#880e4f
}
h1{
font-size:17px
}
td{
_width:250px
}

input0,textarea{
    font-size:13px;
    font-family: "游ゴシック", "Yu Gothic", YuGothic,'ヒラギノ角ゴ Pro W3', 'Hiragino Kaku Gothic Pro', 'Hiragino Kaku Gothic ProN', 'メイリオ', Meiryo;
    border: 1px solid #B9C9CE;
    border-radius:5px;
    padding: 12px 0.8em;
    box-shadow: inset 0 1px 2px rgba(0,0,0,0.2);
}
textarea:focus {
  border-color:#83B6C2;
    outline:none;
    box-shadow:2px 2px 4px rgba(143,183,222,0.6),-2px -2px 4px rgba(143,183,222,0.6),inset 0 1px 2px rgba(0,0,0,0.2);
}
::-webkit-input-placeholder {
    color:#AFAFAF;
}
:-moz-placeholder {
    color:#AFAFAF;
}
	
#a2{
text-align:center;	
text-decoration:none;	
padding:2px 5px;
border:1px solid white;
background-color:green;
color:white;
filter:alpha(opacity=50);
-moz-opacity: 0.5;
opacity: 0.5;
white-space: nowrap;  
width:190px;
_padding:30px;
font-size:13px;
}	
#a3{
font-size:13px;
text-align:center;	
text-decoration:none;	
padding:2px 5px;
border:1px solid white;
background-color:red;
color:white;
filter:alpha(opacity=50);
-moz-opacity: 0.5;
opacity: 0.5;
white-space: nowrap;  
width:190px;
_padding:30px;
}
		
#a3b{
font-size:13px;
text-align:center;	
text-decoration:none;	
padding:2px 5px;
border:1px solid white;
background-color:red;
color:aqua;
filter:alpha(opacity=50);
-moz-opacity: 0.5;
opacity: 0.5;
white-space: nowrap;  
width:190px;
_padding:30px;
}
#contents{
background:#b0c4de;
margin:0px !important;
padding:20px;
background:rgba(255,255,255,1);
}
body{
padding:0px !important;
background:#b0c4de;
margin:0px !important;
}
#header{
background:steelblue;
margin:0px !important;
padding:3px 20px;
}			
#header h1{
	color:white;
	font-size:33px;
	font-family: "游明朝体", "Yu Mincho", YuMincho, "Hiragino Kaku Gothic ProN", "Hiragino Kaku Gothic Pro", "メイリオ", Meiryo, "ＭＳ ゴシック", sans-serif;
}		
#xx1:hover{
 background-color:green !important;
}		
#xx2:hover{
 background-color:orange !important;
}		
textarea{
	height:200px;
}
.row{
	margin:40px 0px;
}

.container {
    margin: 0 auto;
    max-width: 1280px;
    width: 100%;
}
@media only screen and (min-width: 993px){
	.container {
	    width: 80%;
	}
	.rrr{
		text-align:right;
	}
}
label{
	color:#00838f;
	font-weight:700;
}
nav{
	text-align:center;
	_height:100px;
}
nav p{
	font-size:33px;
}
body {
    padding: 0px !important;
    background: silver;
    margin: 0px !important;
}
.bb{
	font-size:33px;
	text-align:left;
}
.bb1{
	font-size:23px;
	float:left;
	text-align:right;	
    font-family: FontAwesome,"Times New Roman","_ヒラギノ明朝 ProN W6", "_HiraMinProN-W6","_UD デジタル 教科書体 NK-R","游明朝体", "Yu Mincho", YuMincho,"游ゴシック体", YuGothic, "游ゴシック Medium", "YuGothic M","ヒラギノ角ゴ Pro W3", "Hiragino Kaku Gothic Pro", "メイリオ", Meiryo, sans-serif !important;
}
::placeholder {
  color: aliceblue;
  font-size: 1.4em;
    font-family: FontAwesome,"Times New Roman","_ヒラギノ明朝 ProN W6", "_HiraMinProN-W6","_UD デジタル 教科書体 NK-R","游明朝体", "Yu Mincho", YuMincho,"游ゴシック体", YuGothic, "游ゴシック Medium", "YuGothic M","ヒラギノ角ゴ Pro W3", "Hiragino Kaku Gothic Pro", "メイリオ", Meiryo, sans-serif !important;

}
.f1 {
    background: rgba(255,255,255, 0.1);
    padding: 20px;
    margin: -30px -7px -15px -7px;
    color: silver !important;
    border-radius: 10px;
	box-shadow: 2px 2px 4px inset;
}
.min{
    font-family: FontAwesome,"Times New Roman","ヒラギノ明朝 ProN W6", "HiraMinProN-W6","_UD デジタル 教科書体 NK-R","游明朝体", "Yu Mincho", YuMincho,"游ゴシック体", YuGothic, "游ゴシック Medium", "YuGothic M","ヒラギノ角ゴ Pro W3", "Hiragino Kaku Gothic Pro", "メイリオ", Meiryo, sans-serif !important;
    font-weight: normal !important; 
} 
body{
	background: url(cork.jpg) transparent;
}
</style>	
</head>
<BODY><DIV class="container">

	<nav class="cyan darken-2">
	<p style="font-weight:500;text-shadow: #fff 0px 1px 2px, #000 0px -1px 1px;"><a class=min href=indexb2.php>活動レポ作成くん2</a></p>
	</nav>

	{$pg}

	<DIV id=contents>

	<form action="indexb2.php" method="post" enctype="multipart/form-data" class="animated fadeIn">

	<!--練習会回数：<input type="text" name="NO" size="20" value="第99回練習会" style="padding-left:5px" /><br />-->

	<div class="f1 row" style="margin:0px 0px;">
    <div class="col s12">
      <div class="row">
        <div class="input-field col s7">
          <i class="material-icons prefix">account_circle</i>
          <input name=ac type="text" id="autocomplete-input" class="autocomplete" value="{$ac_f}" autocomplete="off">
          <label for="autocomplete-input">執筆者選択</label>
        </div>
        <div class="input-field col s3">
        	<button class="btn waves-effect waves-light btn-small  cyan darken-3" type="submit" name="action">確定
    		<i class="material-icons right">send</i>
  			</button>
		</div>        
      </div>
    </div>
  	</div>

  	</form>


	<form action="index2b2.php" method="post" enctype="multipart/form-data">

	<!--練習会回数：<input type="text" name="NO" size="20" value="第99回練習会" style="padding-left:5px" /><br />-->

    <div class="row">
	    <div class="col s12 l2">
	    	<i class="material-icons">filter_1</i> <b>タイトル</b><span style="color:red;font-size:11px;margin-left:2px;">*</span>
	    </div>    	
	    <div class="col s10 l8">
		<input type="text" name="TITL" size="60" value="×月度練習会Aの様子" style="padding-left:5px"/>
 		</div>
	    <div class="col s12 l2"> 		
 		<label>
            <input type="checkbox" name="shita" value="1">
            <span style="color:#00838f;font-weight:700;">下書き</span>
        </label>
		</div>
	</div>	


    <div class="row">
	    <div class="col s12 l2">
	    	<i class="material-icons">filter_2</i> <b>練習会の開催日</b><span style="color:red;font-size:11px;margin-left:2px;">*</span>
	    </div>    	
      	<div class="col s12 l5">
          <input name=ymd placeholder="" id="i2" type="text" class="datepicker validate" value='{$NT}'>
          <label for="i2">開催日</label>
      	</div>
      	<div class="col s12 l5">
      		イベントコード(通算練習会回数)：<input type="number" name="ECODE" style="width:100px;text-align:center" value="99" style="padding-left:5px" />
      	</div>	
    </div>  	

	 <!--画像加工-->
	 <div class="row">
	    <div class="col s12 l2">
	        <i class="material-icons">filter_3</i> <b>写真の処理</b><span style="color:red;font-size:11px;margin-left:2px;">*</span>
	    </div>
	    
	    <div class="col s12 l3 rrr">
	      <label>
			<input type="radio" name="MET" value="1">	
	        <span>線画風1</span>
	      </label>
	      <label style='margin-left:20px'>  
			<input type="radio" name="MET" value="2">	
	        <span>線画風2</span>
	      </label>
	    </div>  
	    
	    <div class="col s12 l4" style="text-align:left;">
	      <label>  
			<input type="radio" name="MET" value="3">	
	        <span>油絵風1</span>
	      </label>
	      <label style='margin-left:20px'>  
			<input type="radio" name="MET" value="5">	
	        <span>油絵風2</span>
	      </label>
	      <label style='margin-left:40px'>  
			<input type="radio" name="MET" value="4" checked>	
	        <span>加工なし</span>
	      </label>	    
	    </div>

	    <div class="col s12 l3">
	 		<label>
	          <input type="checkbox" name="shita2" value="1">
	          <span style="color:#00838f;font-weight:700;">似顔絵リサイズ</span>
	        </label>
	    </div>
	</div> 


	<div class="row">
	    <div class="col s12 l2 bb">
	        <div class=bb1>段落</div><i class="material-icons bb">looks_one</i><span style="color:red;font-size:11px;margin-left:2px;">*</span>
	    </div>
	    <div class="col s12 l5">
			<h1>写真1(練習会風景)：</h1>
			<input type="file" name="upfile1" size="10" /><br />
			<br />
			写真1の説明：<br />
			<input type=text name="tit1" size="30" value="当日イベントの様子です!">
		</div>
	    <div class="col s12 l5">
		<textarea id=t1 name="MSG1" cols=32 rows=14 placeholder="ここに練習会の様子を書き込みます(1)。"></textarea>
		</div>
	</div>


	<div class="row">
	    <div class="col s12 l2 bb">
	        <div class=bb1>段落</div><i class="material-icons bb">looks_two</i><span style="color:red;font-size:11px;margin-left:2px;">*</span>
	    </div>
	    <div class="col s12 l5">
			<h1>写真2(練習会風景)：</h1>
			<input type="file" name="upfile2" size="10" /><br />
			<br />
			写真2の説明：<br />
			<input type=text name="tit2" size="30" value="当日イベントの様子です!">
		</div>
	    <div class="col s12 l5">
		<textarea id=t2 name="MSG2" cols=32 rows=14 placeholder="ここに練習会の様子を書き込みます(2)。"></textarea>
		</div>
	</div>

	<div class="row">
	    <div class="col s12 l2 bb">
	        <div class=bb1>段落</div><i class="material-icons bb">looks_3</i><span style="color:red;font-size:11px;margin-left:2px;">*</span>
	    </div>
	    <div class="col s12 l5">
			<h1>写真3(集合写真)：</h1>
			<input type="file" name="upfile3" size="10" /><br />
			<br />
			写真3の説明：<br />
			<input type=text name="tit3" size="30" value="参加者全員で一枚!">
		</div>
	    <div class="col s12 l5">
		<textarea id=t3 name="MSG3" cols=32 rows=14 placeholder="ここに練習会の様子を書き込みます(3)。"></textarea>
		</div>
	</div>

	<div class="row">
	    <div class="col s12 l2 bb">
	        <div class=bb1>段落</div><i class="material-icons bb">looks_4</i><span style="color:red;font-size:11px;margin-left:2px;">*</span>
	    </div>
	    <div class="col s12 l5">
			<h1>写真4(二次会)：</h1>
			<input type="file" name="upfile4" size="10" /><br />
			<br />
			写真4の説明：<br />
			<input type=text name="tit4" size="30" value="二次会の様子です!">
		</div>
	    <div class="col s12 l5">
		<textarea id=t4 name="MSG4" cols=32 rows=14 placeholder="ここに練習会の様子を書き込みます(4)"></textarea>
		</div>
	</div>

	
	<h1><b><a style='color:dimgray' target=_blank href=https://{$HP_DOMAIN}/@/m>似顔絵</a>：</b></h1>
		<input type="file" name="upfile5" size="30" />{$icon_s}<br />
		<br />
		プロフ：<br />
		<textarea name="PROF" cols=62 rows=5 placeholder="活動レポ作者のプロフ">{$MIname}　{$prof}</textarea>

	<div style="margin:50px 0px;;text-align:center">
	<input id=xx1 style="font-size:30px;border:none;color: rgb(255,255,255);    box-shadow: 0 2px 5px 0 rgba(0,0,0,.26);    background-color: #ad1457; padding:10px 20px;" type="submit" value="送　信" />
	<INPUT id=xx2 style="font-size:30px;border:none;color: rgb(255,255,255);    box-shadow: 0 2px 5px 0 rgba(0,0,0,.26);    background-color: #00838f;  padding:10px 20px;margin-left:10px;" TYPE="reset" VALUE="リセット">
	</div>

	<!--mid-->
	<input type="hidden" name="mid" value="{$_GET[mid]}" />
	<input type="hidden" name="sb" value="" />

	</form>
	</DIV>

    <footer class="page-footer">
      <div class="container2" style='width:97%'>
        <div class="row">
          <div class="col l6 s12">
            <p><i class="0large medium material-icons" style='float:left'>blur_linear</i>
            <span class='min' style='font-size:19px;font-weight:_bold'>活動レポ作成くん</span>
            <br><span class='min' style='font-size:19px;font-weight:_bold'>HP記事作成用</span>

            </p>
            <br clear=all>     
            <p class="grey-text text-lighten-4 addr">　メンバー限り</p>
          </div>
          <div class="col l4 offset-l2 s12">
            <p class="white-text"><i class="fas fa-info fa-fw"></i>お問い合わせ</p>
            <ul>
              <li><a class="grey-text text-lighten-3" href="mailto:info@{$HP_DOMAIN}"><i class="fa fa-envelope"></i> info@{$HP_DOMAIN}</a></li>
            </ul>
          </div>
        </div>
      </div>
      <div class="footer-copyright">
        <div class="container2" style="text-align:center">
        <!--© 2018 Copyright Text-->
        <span class="_min" id=cc>　{$HP_DOMAIN}</span>
        <a class="grey-text text-lighten-4 right min" href="https://{$HP_DOMAIN}"><i class=min>official site</i></a>
        </div>
      </div>
    </footer>

	<!--移動-->
	<div style="position:fixed; bottom:5px; right:10vw"><a id=arrow href=#><i class="fas fa-angle-up" style="opacity:0.3;font-size:80px !important;"></i></a> <a id=arrow href=#end><i style="opacity:0.3;font-size:80px !important;" class="fas fa-angle-down"></i></a></div> 
	<span id=end></span>

</DIV></BODY>
</html>
EOM;

echo $html;
?>		  