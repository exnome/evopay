<?php
	//error_reporting(0);
	include '../inc/conn.php';
	$KodeDealer = addslashes($_REQUEST['KodeDealer']);
	include '../inc/koneksi.php';
	// echo $table;
	if (!mssql_select_db("[$table]")) {
		echo "0";
	} else {
		echo "1";
	}
	//echo "table : [$table]";
?>