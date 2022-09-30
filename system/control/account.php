<?php
	require_once ('../inc/conn.php');
	$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : null;
	if ($_SERVER['HTTP_X_FORWARDED_FOR']){
		$ipaddr = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else{ 
		$ipaddr = $_SERVER['REMOTE_ADDR'];
	}
	
	if ($action=='change-nama') {
		$IdUser = addslashes($_REQUEST['IdUser']);
		$namaUser = addslashes($_REQUEST['namaUser']);
		
		mssql_query("BEGIN TRAN");
			$sql = "update sys_user set namaUser='".$namaUser."', ipedit = '".$ipaddr."', tgledit = getdate() where IdUser = '".$IdUser."'";
			$qry=mssql_query($sql);
			if ($qry) {
				mssql_query("COMMIT TRAN");
				echo "Data Saved!!";
			} else {
				mssql_query("ROLLBACK TRAN");
				echo "Failed!!";
			}
		mssql_query("return");
	} else if ($action=='change-email') {
		$IdUser = addslashes($_REQUEST['IdUser']);
		$Email = addslashes($_REQUEST['Email']);
		mssql_query("BEGIN TRAN");
			$sql = "update sys_user set Email='".$Email."', ipedit = '".$ipaddr."', tgledit = getdate() where IdUser = '".$IdUser."'";
			$qry=mssql_query($sql);
			if ($qry) {
				mssql_query("COMMIT TRAN");
				echo "Data Saved!!";
			} else {
				mssql_query("ROLLBACK TRAN");
				echo "Failed!!";
			}
		mssql_query("return");
	} else if ($action=='change-password') {
		$IdUser = addslashes($_REQUEST['IdUser']);
		$oldpass = addslashes($_REQUEST['oldpass']);
		$currpass = md5($_REQUEST['currpass']);
		$newpass = md5($_REQUEST['newpass']);

		if ($currpass!=$oldpass) {
			echo "0#Failed! Current Password salah!";
		} else {
			$prc=mssql_query("update sys_user set passWord='$newpass', ipedit = '".$ipaddr."', tgledit = getdate() where IdUser='".$IdUser."'");
			if ($prc) {
				echo "1#Data Save!!";
			} else {
				echo "0#Failed!!";
			}
		}
	}
?>