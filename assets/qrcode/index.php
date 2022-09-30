<html>
<head></head>
<body>
<div id="wrapper">
<div id="barcode_div">
 <form method="post" action="">
  <input type="text" name="barcode_text">
  <input type="submit" name="generate_barcode" value="GENERATE">
 </form>
</div>
</div>
</body>
</html>
<?php
	if(isset($_POST['generate_barcode'])) {
		$text=$_POST['barcode_text'];
		$PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR;
	    $PNG_WEB_DIR = 'temp/';
	    include "phpqrcode/qrlib.php";    
	    if (!file_exists($PNG_TEMP_DIR)) { mkdir($PNG_TEMP_DIR); }
	    $filename = $PNG_TEMP_DIR.'test.png';
	    $errorCorrectionLevel = 'L';
	    $matrixPointSize = 3;
	    if (isset($text)) { 
	        //it's very important!
	        if (trim($text) == '')
	            die('data cannot be empty! <a href="?">back</a>');
	        // user data
	        $filename = $PNG_TEMP_DIR.'test'.md5($text.'|'.$errorCorrectionLevel.'|'.$matrixPointSize).'.png';
	        QRcode::png($text, $filename, $errorCorrectionLevel, $matrixPointSize, 2);    
	    } else {    
	        //default data
	        echo 'You can provide data in GET parameter: <a href="?data=like_that">like that</a><hr/>';    
	        QRcode::png('PHP QR Code :)', $filename, $errorCorrectionLevel, $matrixPointSize, 2);    
	    }    
	    //display generated file
	    //echo "<img alt='testing' src='barcode.php?codetype=Code39&size=40&text=".$text."&print=true'/>";
	    echo '<img src="'.$PNG_WEB_DIR.basename($filename).'" />';  
	    // QRtools::timeBenchmark(); 
	}
?>