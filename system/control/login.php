<?php
	//error_reporting(0);
	session_start();
	require_once "../inc/conn.php";
	$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : null;
	if ($_SERVER['HTTP_X_FORWARDED_FOR']){
		$ipaddr = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else{ 
		$ipaddr = $_SERVER['REMOTE_ADDR'];
	}
	
	if ($action=='web') {
		$username=str_replace("'", "`", addslashes($_REQUEST['username']));
		$password=str_replace("'", "`", addslashes($_REQUEST['password']));
		
		$cek=mssql_num_rows(mssql_query("select * from sys_user where IdUser='".$username."' and passWord='".md5($password)."' and isnull(isDel,0)<>1"));
		if ($cek>0) {
			$dt=mssql_fetch_array(mssql_query("select * from sys_user where idUser='".$username."' and passWord='".md5($password)."'"));
			
			#----------------------- stage 3
			$status_user = $dt['idstatus'];
			$nama_user = $dt['namaUser'];
			
			if ($status_user==1) {
				mssql_query("update sys_user set lastLogin=getdate() where IdUser='".$username."'");
				mssql_query("insert into sys_log (IdUser, tglentry, ipentry, useragent) 
						values ('".$username."', getdate(), '".$ipaddr."', '".$_SERVER['HTTP_USER_AGENT']."')");
				
				$_SESSION['UserID']=$dt['IdUser'];
				$_SESSION['pwd']=$dt['passWord'];
				$_SESSION['UserName']=$dt['namaUser'];
				$_SESSION['level']=$dt['tipe'];
				$_SESSION['kodedealer']=$dt['KodeDealer'];
				$_SESSION['evo_dept']=$dt['department'];
				$_SESSION['evo_divisi']=$dt['divisi'];
				echo "1#Login Succes!#Wellcome $dt[namaUser]!";
			} else {
				echo "0#Login Failed!#".$nama_user.", user Anda dalam status Non Aktif. Silahkan menghubungi Administrator ";
			}
		} else {
			echo "0#Login Failed!#Please check your username and password!";
		}
		
		
	} else if ($action=='app') {
		/*
		1.  http://10.10.26.181/evopay/system/control/login.php?action=app&nik=0612-2174&id=VP00_25_11_20_001&tipe_validasi=biasa
			ID replace karakter  “/” menjadi “ _ “ := VP00_25_11_20_001   ->  VP00/25/11/20/001
		2.       http://10.10.26.181/evopay/system/control/login.php?action=app&nik=0612-2174&tipe_validasi=multi
		
		 http://localhost/evopay/index.php?action=app&nik=0612-2174&id=VP00_25_11_20_001&tipe_validasi=biasa
		 http://localhost/evopay/index.php?action=app&nik=0612-2174&tipe_validasi=multi
		*/

		$tipe_validasi = trim($_REQUEST['tipe_validasi']);
		
		$nik=str_replace("'", "`", trim($_REQUEST['nik']));
		$id=str_replace("'", "`", trim($_REQUEST['id']));
		$nobukti = str_replace("_","/",$id);
		
		$cek=mssql_num_rows(mssql_query("select * from sys_user where nik='".$nik."' and isnull(isDel,0)<>1"));
		$evo = mssql_fetch_array(mssql_query("select evo_id from DataEvo where nobukti = '".$nobukti."'"));
		if ($cek>0) {
			$dt=mssql_fetch_array(mssql_query("select * from sys_user where nik='".$nik."'"));
			mssql_query("update sys_user set lastLogin=getdate() where nik='".$nik."'");
			mssql_query("insert into sys_log (IdUser, tglentry, ipentry, useragent) values ('".$username."', getdate(), '".$ipaddr."', '".$_SERVER['HTTP_USER_AGENT']."')");
				
			$_SESSION['UserID']=$dt['IdUser'];
			$_SESSION['pwd']=$dt['passWord'];
			$_SESSION['UserName']=$dt['namaUser'];
			$_SESSION['level']=$dt['tipe'];
			$_SESSION['kodedealer']=$dt['KodeDealer'];
			$_SESSION['evo_dept']=$dt['department'];
			$_SESSION['evo_divisi']=$dt['divisi'];
			$KodeDealer = $dt['KodeDealer'];
			$evo_id = $evo['evo_id'];
			//include '../inc/koneksi.php';
			// echo $table;
			//if (!mssql_select_db("[$table]",$connCab)) {
				//echo "0#Login Failed!#Database Bulan Belum Ada, silahkan hubungi Admin Accounting!";
			//} else {
				//echo "1#Login Succes!#Wellcome $dt[namaUser]!#".$evo['evo_id'];
			//}
			echo "1#Login Succes!#Wellcome $dt[namaUser]!#".$evo['evo_id']."#".$tipe_validasi;
			
		} else {
			echo "0#Login Failed!#Please relogin!";
		}
	}
?>