<?php
include("pre.php");
include("define.php");//サーバ定義

/*

POSTすると、バッファ出力できないので、
POST一旦して、データをシリアライズ保存、ページリロードする!

if($_GET[r]<>1){
	echo "<script>location.href = 'index2b2.php?r=1';</script>"; 
}else{

		$_POST["TITL"]="ddd";
		$PHOTO[0] = "test1.jpeg";
		$PHOTO[1] = "test1.jpeg";
		//$MET = "1";	

		$i = 0;
		while($i < 4) {
			if (file_exists($PHOTO[$i])) {

		$ph=$PHOTO[$i];
		$ph2=$PHOTO[$i];
		$ph3=$PHOTO[$i];
					
		exec("convert {$ph} -colorspace gray -sketch 0x30+5 {$ph2}");//モノクロ
		//exec("convert {$ph} -sketch 0x20+135 {$ph2}");//カラー

		exec("convert {$ph2} -thumbnail 100x100 -background white -extent 100x100 \
  \( -size 100x100 xc:none -fill white -draw 'circle 50,50,50,0' \) \
  -compose CopyOpacity -composite icon.png");


		//exec("convert {$ph3} -colorspace gray {$ph3}");//モノクロ
		//exec("convert {$ph} -sketch 0x20+135 {$ph2}");//カラー

		exec("convert {$ph3} -thumbnail 100x100 -background white -extent 100x100 \
  \( -size 100x100 xc:none -fill white -draw 'circle 50,50,50,0' \) \
  -compose CopyOpacity -composite icon2.png");

			}

			$i++;
			
		}

}
*/

//------------------------------------------------//
//========投稿=====================================//
//------------------------------------------------//

//---------確認画面-------------//
if($_POST[sb]==""):
	$shori = new shori();
	list($SS,$TITL,$SHITA)=$shori->SS();//原稿作成
	echo $shori->showHtml(0);//出力
endif;

//---------投稿画面-------------//
if($_POST[sb]=="1"):
	//配列をファイルから読み込み
	$_dat = unserialize(file_get_contents("_dat.dat"));	
	$NO=$_dat["no"];//練習会回数
	$TITL=$_dat["titl"];//タイトル
	$YMD=$_dat["ymd"];//タイトル	
	$SHITA=$_dat["shita"];//下書き保存か
	$SS=$_dat["ss"];//本文
	//投稿
	$shori = new shori();
	$shori->SS2($NO,$TITL,$YMD,$SHITA,$SS);
	//出力
	echo $shori->showHtml(1);
endif;

//------------------------------------------------//
//========処理クラス=====================================//
//------------------------------------------------//

class shori{

	public function SS(){//投稿文生成	

		global $HP_DOMAIN;	
		global $FILE_DOMAIN;

		//-----------バッファ出力対策 1/3-------------//
		if($_GET[t]=="1"){
			//配列の中身をファイルに保存
  			//file_put_contents("_POST.dat", serialize($_POST));
  			//file_put_contents("PHOTO.dat", serialize($PHOTO));
  			//echo "<script>location.href = 'index2b2.php?t=1';</script>";   			
  			//配列をファイルから読み込み
  			$_POST = unserialize(file_get_contents("_POST.dat"));
  			$PHOTO = unserialize(file_get_contents("PHOTO.dat"));
		}
		//-----------バッファ出力対策-------------//		

		/*---------------------------------*/
		// POST受信
		/*---------------------------------*/
		$NO = $_POST["NO"];//練習会回数
		$TITL = $_POST["TITL"];//タイトル
		if($TITL==""){
			//exit( "タイトルなし！！" );
		}	
		$SHITA = $_POST["shita"];//下書き
		$SHITA2 = $_POST["shita2"];//似顔絵リサイズ

		$MET = $_POST["MET"];//絵画の処理方法

		$tit1 = $_POST["tit1"];//写真1の説明
		$tit2 = $_POST["tit2"];//写真2の説明
		$tit3 = $_POST["tit3"];//写真3の説明
		$tit4 = $_POST["tit4"];//写真4の説明

		$MSG1 = $_POST["MSG1"];//段落1
		$MSG2 = $_POST["MSG2"];//段落2
		$MSG3 = $_POST["MSG3"];//段落3
		$MSG4 = $_POST["MSG4"];//段落4

		//$YYY = $_POST["YYY"];//投稿日
		//$MMM = $_POST["MMM"];//投稿日
		//$DDD = $_POST["DDD"];//投稿日
		$YMD=strtotime($_POST["ymd"]);//投稿日

		//追加
		$ECODE = $_POST["ECODE"];//イベントコード
		$PROF = $_POST["PROF"];//プロフ


		/*---------------------------------*/
		// 写真アップ&絵画風処理
		/*---------------------------------*/

		//-----------バッファ出力対策 2/3-------------//
		if($_GET[t]==""):
		//-----------バッファ出力対策-------------//
			
		//UNIXタイムでファイル保存
		$time = time();

		//写真アップ(保存ファイル名は、files/UNIXタイム_1.jpg)
		//move_uploaded_file($_FILES["upfile1"]["tmp_name"], "files/" . $_FILES["upfile1"]["name"]);
		move_uploaded_file($_FILES["upfile1"]["tmp_name"], "files/" . $time . "_1.jpg");
		move_uploaded_file($_FILES["upfile2"]["tmp_name"], "files/" . $time . "_2.jpg");
		move_uploaded_file($_FILES["upfile3"]["tmp_name"], "files/" . $time . "_3.jpg");
		move_uploaded_file($_FILES["upfile4"]["tmp_name"], "files/" . $time . "_4.jpg");
		//似顔絵
		move_uploaded_file($_FILES["upfile5"]["tmp_name"], "files/" . $time . "_5.jpg");

		//配列にいれる
		$PHOTO[0] = "files/" . $time . "_1.jpg";
		$PHOTO[1] = "files/" . $time . "_2.jpg";
		$PHOTO[2] = "files/" . $time . "_3.jpg";
		$PHOTO[3] = "files/" . $time . "_4.jpg";
		$PHOTO[4] = "files/" . $time . "_5.jpg";//似顔絵

		//-----------バッファ出力対策 3/3-------------//
		//配列の中身をファイルに保存
		file_put_contents("_POST.dat", serialize($_POST));
		file_put_contents("PHOTO.dat", serialize($PHOTO));
		echo "<script>location.href = 'index2b2.php?t=1';</script>";exit;   			
		//配列をファイルから読み込み
		//$_POST = unserialize(file_get_contents("_POST.dat"));
		//$PHOTO = unserialize(file_get_contents("PHOTO.dat"));
		endif;
		//-----------バッファ出力対策-------------//

		//絵画風処理
		$i = 0;
		while($i < 4) {
			if($MET == "1"){
				/* 画像を絵画風に変換する(水彩画風) */
				if (file_exists($PHOTO[$i])) {
					$im = new Imagick($PHOTO[$i]);
					$im->thumbnailImage(320, 0);//リサイズ
					$im->sketchImage(10,0,135);//線画

					//$im->vignetteImage(0, 1, 10, 10);//円形ぼかし
					//外周ぼかし
					$im->setImageMatte(true);

					$it = $im->getPixelIterator();
					foreach($it as $py => $line){
					foreach($line as $px => $pixel){
					  $pixel->setColorValue(Imagick::COLOR_ALPHA,
					    ((1.0-pow(2*$px/(float)$im->getImageWidth()-1.0, 4))*
					    (1.0-pow(2*$py/(float)$im->getImageHeight()-1.0, 4)))
					  );
					}
					$it->syncIterator();
					}
					$im->writeImage('TEMP.png');
					/* 白背景と重ね合わせてjpegで保存 */
					$im2 = new Imagick();
					$im2->newImage($im->getImageWidth(), $im->getImageHeight(), "white");
					$im2->compositeImage($im, Imagick::COMPOSITE_DEFAULT, 0, 0);
					$im2->writeImage($PHOTO[$i]);

					$im2->destroy();
					$im->destroy();
				}
			}elseif($MET == "2"){
				/* 画像を絵画風に変換する(線画風) */
				if (file_exists($PHOTO[$i])) {
					$im = new Imagick($PHOTO[$i]);
					$im->thumbnailImage(350, 0);//リサイズ
					//$im->modulateImage(80, 180, 100);
					$im->sketchImage(10,0,135);
					//$im->spreadImage(2);
					//$im->oilPaintImage(2);
					$im->vignetteImage(0, 1, 10, 10);//円形ぼかし
					$im->writeImage($PHOTO[$i]);
					//クリア	
					$im->destroy();
				}
			}elseif($MET == "3"){
				/* 画像を絵画風に変換する(油絵風) */
				if (file_exists($PHOTO[$i])) {
					/*
					$im = new Imagick($PHOTO[$i]);
					$im->thumbnailImage(350, 0);//リサイズ
					//$im->modulateImage(80, 180, 100);
					//$im->sketchImage(10,0,135);
					//$im->spreadImage(2);
					$im->oilPaintImage(2);
					$im->vignetteImage(0, 1, 10, 10);//円形ぼかし
					$im->writeImage($PHOTO[$i]);
					//クリア	
					$im->destroy();*/

					//exit;

					/* ２値化してマスク作成 */
					$im = new Imagick($PHOTO[$i]);
					$im->blackThresholdImage('#808080');
					$im->whiteThresholdImage('#808080');
					/* 反転 */
					$im->negateImage(true);
					$im->paintTransparentImage("black", 0, 0);
					/* アルファチャネルをぼかす */
					$im->blurImage(20, 10, Imagick::CHANNEL_ALPHA);

					/* マスクを利用して切り取り */
					$im2 = new Imagick($PHOTO[$i]);
					$im->compositeImage($im2, Imagick::COMPOSITE_IN, 0, 0, Imagick::CHANNEL_ALL);

					/* 白背景と合成 */
					$im3 = new Imagick();
					$im3->newPseudoImage($im->getImageWidth(), $im->getImageHeight(), "xc:white");
					$im3->compositeImage($im, Imagick::COMPOSITE_DEFAULT, 0, 0);

					$im3->blurImage(5,1.7);
					//$im3->radialBlurImage(1);

					$im3->thumbnailImage(350, 0);//リサイズ

					$im3->writeImage($PHOTO[$i]);
					$im3->destroy();
					$im2->destroy();
					$im->destroy();

				}
			}elseif($MET == "5"){
				/* 画像を絵画風に変換する(油絵風) */
				if (file_exists($PHOTO[$i])) {
					$im = new Imagick($PHOTO[$i]);
					$im->thumbnailImage(350, 0);//リサイズ
					//$im->modulateImage(80, 180, 100);
					//$im->sketchImage(10,0,135);
					//$im->spreadImage(2);
					$im->oilPaintImage(2);
					//$im->vignetteImage(0, 1, 10, 10);//円形ぼかし
					$im->writeImage($PHOTO[$i]);
					//クリア	
					$im->destroy();
				}
			}	
			$i++;
		}

		//似顔絵のリサイズ
		if ((file_exists($PHOTO[4]))&($SHITA2==1)) {
			$im = new Imagick($PHOTO[4]);
			$im->thumbnailImage(120, 0);//リサイズ
			$im->writeImage($PHOTO[4]);
			//クリア	
			$im->destroy();
		}

		//似顔絵アイコン

		if (file_exists($PHOTO[4])) {
			//echo "aaaa".$_POST[mid];exit;
		}else{//似顔絵なし
			if($_POST[mid]<>""){//mid指定あり
				//copy("icon.png",);
				//echo $_POST[mid];exit; 
				$PHOTO[4] = "files/" . $time . "_5.png";//似顔絵
				if($_POST[mc]==1){
					rename("icon2.png", $PHOTO[4]);
				}else{
					rename("icon.png", $PHOTO[4]);
				}
			}
		}

		/*---------------------------------*/
		// HTML生成
		/*---------------------------------*/

		if(($MET==5)or($MET==3)):

		$SS=<<<EOM
		<img src="https://{$HP_DOMAIN}/site/wp-content/uploads/2014/08/00d84b4c52e31.png" />
		<!--more-->
		<link rel="stylesheet" href="https://{$HP_DOMAIN}/i/css/white.css" type="text/css" media="screen" />
		<link rel="stylesheet" href="https://{$HP_DOMAIN}/i/css/bk.css" type="text/css" media="screen" />	
		<h3>活動レポ</h3>	

		<div class="bk1"><img src="https://{$FILE_DOMAIN}/tano4/repo/{$PHOTO[0]}" alt="{$tit1}"  title="{$tit1}" style="display: inline; float: left; padding-right:10px;padding-bottom:5px" /></div>
		<img style="margin: 0px 0px 10px" src=https://{$HP_DOMAIN}/site/wp-content/uploads/2014/12/content-h323.gif><br/>{$MSG1}<br clear="all" />


		<div class="bk2"><img src="https://{$FILE_DOMAIN}/tano4/repo/{$PHOTO[1]}" alt="{$tit2}"  title="{$tit2}" style="display: inline; float: right; padding-left:10px;padding-bottom:5px" /></div>
		<img style="margin: 0px 0px 10px" src=https://{$HP_DOMAIN}/site/wp-content/uploads/2014/12/content-h323.gif><br/>{$MSG2}<br clear="all" />


		<div class="bk1"><img src="https://{$FILE_DOMAIN}/tano4/repo/{$PHOTO[2]}" alt="{$tit3}"  title="{$tit3}" style="display: inline; float: left; padding-right:10px;padding-bottom:5px" /></div>
		<img style="margin: 0px 0px 10px" src=https://{$HP_DOMAIN}/site/wp-content/uploads/2014/12/content-h323.gif><br/>{$MSG3}<br clear="all" />


		<div class="bk2"><img src="https://{$FILE_DOMAIN}/tano4/repo/{$PHOTO[3]}" alt="{$tit4}"  title="{$tit4}" style="display: inline; float: right; padding-left:10px;padding-bottom:5px" /></div>
		<img style="margin: 0px 0px 10px" src=https://{$HP_DOMAIN}/site/wp-content/uploads/2014/12/content-h323.gif><br/>{$MSG4}<br clear="all" />

		<div id=AAA>
		<img src="https://{$FILE_DOMAIN}/tano4/repo/{$PHOTO[4]}" alt="似顔絵"  title="似顔絵" style="max-width:120px;display: inline; float: left; padding-right:15px;padding-bottom:5px" />{$PROF}<br clear="all" />
		</div>

		<h3>曲名レポ</h3>	
		[kyokumei n={$ECODE}]
		<br clear="all" />
EOM;

		else:

		$SS="<img src=\"https://{$HP_DOMAIN}/site/wp-content/uploads/2014/08/00d84b4c52e31.png\" />
		<!--more-->
		<link rel=\"stylesheet\" href=\"https://{$HP_DOMAIN}/i/css/white.css\" type=\"text/css\" media=\"screen\" />	
		<h3>活動レポ</h3>	
		<img src=\"https://{$FILE_DOMAIN}/tano4/repo/".$PHOTO[0]."\" alt=\"".$tit1."\"  title=\"".$tit1."\" style=\"display: inline; float: left; padding-right:10px;padding-bottom:5px\" /><img style=\"margin: 0px 0px 10px\" src=https://{$HP_DOMAIN}/site/wp-content/uploads/2014/12/content-h323.gif><br/>".$MSG1."<br clear=\"all\" /><img src=\"https://{$FILE_DOMAIN}/tano4/repo/".$PHOTO[1]."\" alt=\"".$tit2."\"  title=\"".$tit2."\" style=\"display: inline; float: right; padding-left:10px;padding-bottom:5px\" /><img style=\"margin: 0px 0px 10px\" src=https://{$HP_DOMAIN}/site/wp-content/uploads/2014/12/content-h323.gif><br/>".$MSG2."<br clear=\"all\" /><img src=\"https://{$FILE_DOMAIN}/tano4/repo/".$PHOTO[2]."\" alt=\"".$tit3."\"  title=\"".$tit3."\" style=\"display: inline; float: left; padding-right:10px;padding-bottom:5px\" /><img style=\"margin: 0px 0px 10px\" src=https://{$HP_DOMAIN}/site/wp-content/uploads/2014/12/content-h323.gif><br/>".$MSG3."<br clear=\"all\" /><img src=\"https://{$FILE_DOMAIN}/tano4/repo/".$PHOTO[3]."\" alt=\"".$tit4."\"  title=\"".$tit4."\" style=\"display: inline; float: right; padding-left:10px;padding-bottom:5px\" /><img style=\"margin: 0px 0px 10px\" src=https://{$HP_DOMAIN}/site/wp-content/uploads/2014/12/content-h323.gif><br/>".$MSG4."<br clear=\"all\" />

		<div id=AAA>
		<img src=\"https://{$FILE_DOMAIN}/tano4/repo/".$PHOTO[4]."\" alt=\"似顔絵\"  title=\"似顔絵\" style=\"max-width:120px;display: inline; float: left; padding-right:15px;padding-bottom:5px\" />".$PROF."<br clear=\"all\" />
		</div>

		<h3>曲名レポ</h3>	
		[kyokumei n=".$ECODE."]
		<br clear=\"all\" />
		";

		endif;

		$filepath = "s.txt"; // ファイルへのパスを変数に格納
		$string = $SS; // 書き込みたい文字列を変数に格納
		 
		$fp = fopen($filepath, "w"); // 新規書き込みモードで開く
		@fwrite( $fp, $string, strlen($string) ); // ファイルへの書き込み
		fclose($fp);
		//ファイルへの書き込みは終了

		//------配列保存------//
		$_dat["no"]=$NO;//練習会回数
		$_dat["titl"]=$TITL;//タイトル
		$_dat["ymd"]=$YMD;//投稿日
		$_dat["shita"]=$SHITA;//下書き保存か
		$_dat["ss"]=$SS;//本文
		//配列の中身をファイルに保存
		file_put_contents("_dat.dat", serialize($_dat));

		//返り値
		return array($SS,$TITL,$SHITA);

	}

	public function SS2($NO,$TITL,$YMD,$SHITA,$SS){

		/*---------------------------------*/
		// 自動投稿
		/*---------------------------------*/
		global $HP_DOMAIN;	
		global $FILE_DOMAIN;

		//PEAR XML_PRCの読み出し
		require_once("XML/RPC.php");

		$host = "{$HP_DOMAIN}";
		$xmlrpc_path = "/site/xmlrpc.php";
		$appkey = '';
		$user = HP_USER;
		$passwd =HP_PW;

		//$c = new XML_RPC_client($xmlrpc_path, $host, 80);
		$c = new XML_RPC_client($xmlrpc_path, $host, 443);

		$appkey = new XML_RPC_Value($appkey, 'string');
		$username = new XML_RPC_Value( $user, 'string' );
		$passwd = new XML_RPC_Value( $passwd, 'string' );

		$message = new XML_RPC_Message(
		'blogger.getUsersBlogs',array($appkey, $username, $passwd)
		);

		$result = $c->send($message);

		if(!$result){
		exit('Could not connect to the server.');
		} else if( $result->faultCode() ){
		exit($result->faultString());
		}

		$blogs = XML_RPC_decode($result->value());

		$blog_id = new XML_RPC_Value($blogs[0]["blogid"], "string");

		//--------ここまでがBlogIDの取得----------------------------

		//$title = $NO;//記事タイトル
		$title = $TITL;
		$categories = array(
		new XML_RPC_Value("イベント記録", "string"),
		);
		$description = $SS;//投稿本文
		$content = new XML_RPC_Value(
		array(
		'title' => new XML_RPC_Value($title, 'string'),
		'categories' => new XML_RPC_Value($categories, 'array'),
		'description' => new XML_RPC_Value($description, 'string'),
		'wp_slug' => new XML_RPC_Value($NO,'string'),
		//'dateCreated' => new XML_RPC_Value(time(), 'dateTime.iso8601')
		//'dateCreated' => new XML_RPC_Value( mktime("17", "0", "0", $MMM, $DDD, $YYY), 'dateTime.iso8601' )
		'dateCreated' => new XML_RPC_Value( $YMD, 'dateTime.iso8601' )
		),
		'struct');

		//$publish = new XML_RPC_Value(1, "boolean");
		if($SHITA==1){
			$publish = new XML_RPC_Value(0, "boolean");
		}else{
			$publish = new XML_RPC_Value(1, "boolean");
		}	

		$message = new XML_RPC_Message(
		'metaWeblog.newPost',
		array($blog_id, $username, $passwd, $content, $publish)
		);

		$result = $c->send($message);

		if(!$result){
		exit('Could not connect to the server.');
		} else if( $result->faultCode() ){
		exit($result->faultString());
		}

	}

	//------------------html出力---------------------//

	public function showHtml($sb){

		global $SS;
		global $TITL;
		global $SHITA;
		global $HP_DOMAIN;	
		global $FILE_DOMAIN;

		$html=<<<EOM
		<html>
		<head>
		<!--
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1">
		-->
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
		<style>
		body{
			padding:0px !important;
			background:silver;
			margin:0px !important;
		}
		.container {
		    margin: 0 auto;
		    max-width: 1280px;
		    width: 100%;
			background:rgba(255,255,255,1);
			padding:20px;
		}
		@media only screen and (min-width: 993px){
			.container {
			    width: 80%;
			}
		}
		nav{
			text-align:center;
			_height:100px;
			margin-bottom:20px;
		}
		nav p{
			font-size:33px;
			padding:0;
			margin:0;
		}					
		</style>
		</head>
EOM;

		if($sb<>1):

		$SHITA_s=$SHITA==1?"<span style='color:red'>下書き</span>":"";	

		$html.=<<<EOM
		<BODY><DIV class="container">
			<nav class="cyan darken-2">
				<p style="font-weight:500;text-shadow: #fff 0px 1px 2px, #000 0px -1px 1px;"><a class=min href=indexb2.php>{$TITL} {$SHITA_s}</a></p>
			</nav>

			<form action="index2b2.php" method="post" enctype="multipart/form-data" class="animated fadeIn">

			<div class="row">
			    <div class="col s12">
			    {$SS}
			    </div>
			</div>    

			 <div class="center-align">
			      <button id="_btn_id" class="btn waves-effect waves-light btn-large pink darken-2 center-align pulse" type="submit" name="_action" value=1 >投稿
			  			<i class="material-icons right">send</i>
			  	  </button>
			       <a href="#" onClick="history.back(); return false;">編集画面に戻る
			 	  <input type="hidden" name=sb value="1">
			 </div>

			</form>
		</DIV></BODY></html>
EOM;

		else:

		$html.=<<<EOM
		<BODY><DIV class="container">
			<nav class="cyan darken-2">
			<p style="font-weight:500;text-shadow: #fff 0px 1px 2px, #000 0px -1px 1px;"><a class=min href=indexb2.php>活動レポ作成くん2</a></p>
			</nav>

			<form action="index2b2.php" method="post" enctype="multipart/form-data" class="animated fadeIn">

			<div class="row">
			    <div class="col s12">
			    <h1>投稿完了</1>
			    {$_SS}
			    </div>
			</div>    

			 <div class="center-align">
			      <a href="https://{$HP_DOMAIN}/site/wp-admin/edit.php"><button style="margin-left:10px;" id="_btn_id" class="btn waves-effect waves-light btn-large  center-align " type="button" name="_action" value=2>サイトの編集画面へ
			        <i class="material-icons right">send</i>
			      </button></a>
			       <a href="https://{$FILE_DOMAIN}/tano4/repo/indexb2.php">トップに戻る</a>
			 </div>

			</form>
		</DIV></BODY></html>
EOM;

		endif;
		//返り値
		return $html;
	}// end function showHtml
}// end class shori	



///////////////////////////////////////////////////////////
		

///////////////////////////////////////////////////////////					

?>
