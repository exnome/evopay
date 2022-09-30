<?php
	ini_set("max_execution_time", 0);
	set_time_limit(0);
	$tp = isset($_REQUEST['tp']) ? $_REQUEST['tp'] : null;
	
	/* Getting file name */
	$nobukti =  "VP".str_replace("/","",$_POST['nobukti']);
	$filename = $_FILES['file']['name'];
	$filename = str_replace("=","",str_replace("%","",str_replace("&","",str_replace("#","",str_replace(" ","_",$filename)))));
	$filename = $nobukti."__".$filename;
	
	/* Location */
	$location = "../files/".$filename;
	$uploadOk = 1;
	$imageFileType = pathinfo($location,PATHINFO_EXTENSION);
	/* Valid extensions */
	// if ($tp=='video') {
	// 	$valid_extensions = array("mkv","mp4");
	// } else if ($tp=='ebook') {
	// 	$valid_extensions = array("pdf");
	// } else if ($tp=='dokumen') {
		$valid_extensions = array("pdf", "doc", "xls", "jpg", "jpeg", "xlsx", "docx", "png", "zip", "rar");
	// } else {
	// 	$valid_extensions = array("jpg","jpeg","png");
	// }
	/* Check file extension */
	
	/*if(!in_array(strtolower($imageFileType), $valid_extensions)) {
	   $uploadOk = 0;
	}*/
	$uploadOk = 0;
	
	if ($_FILES['file']['type'] == "image/png" or $_FILES['file']['type'] == "image/jpeg" or $_FILES['file']['type'] == "application/msword" or $_FILES['file']['type'] == "application/vnd.openxmlformats-officedocument.wordprocessingml.document" or $_FILES['file']['type'] == "application/pdf" or $_FILES['file']['type'] == "application/vnd.rar" or $_FILES['file']['type'] == "application/vnd.ms-excel" or $_FILES['file']['type'] == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" or $_FILES['file']['type'] == "application/zip" or $_FILES['file']['type'] == "application/x-zip-compressed" or $_FILES['file']['type'] == "application/x-rar-compressed" or $_FILES['file']['type'] == "application/octet-stream" ) {	
		$uploadOk = 1;
	}
	
	
	if($uploadOk == 0){
	   echo 0;
	} else{
	   /* Upload file */
	   if(move_uploaded_file($_FILES['file']['tmp_name'],$location)){
			echo $filename;
	   } else{
			echo 0;
	   }
	}
?>