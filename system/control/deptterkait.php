<?php
	session_start();
	require_once ('../inc/conn.php');
	$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : null;
	if ($action=='new') {
		$KodeDealer = addslashes($_REQUEST['KodeDealer']);
		$divisi = addslashes($_REQUEST['divisi']);
		$department = addslashes($_REQUEST['department']);
		$user = addslashes($_REQUEST['user']);
		$user3 = addslashes($_REQUEST['user3']);
		$r = explode(";", $user3);
		
		$j = 1;
		for ($i=count($r);$i>=0;$i--){	
			if (!empty($r[$i])) {		
				$query1=mssql_query("insert into deptterkait (IdUser,levelvalidator, userentry, tglentry) 
								values ('".$r[$i]."','".$j."','".$_SESSION['UserID']."', getdate())");
				$j++;
			}
		}
		$query1=mssql_query("insert into deptterkait (IdUser,levelvalidator, userentry, tglentry) 
							values ('$user','".$j."','".$_SESSION['UserID']."', getdate())");
							
		if ($query1) {
			echo "Data Saved!!";
		} else {
			echo "Failed!!";
		}
		
	} else if ($action=='edit') {
		$KodeDealer = addslashes($_REQUEST['KodeDealer']);
		$divisi = addslashes($_REQUEST['divisi']);
		$department = addslashes($_REQUEST['department']);
		$user = addslashes($_REQUEST['user']);
		$user3 = addslashes($_REQUEST['user3']);
		
		$qry = mssql_query("update deptterkait set namaUser='$namaUser',Email='$Email',KodeDealer='$KodeDealer',tipe='$tipe',divisi='$divisi',department='$department',tipeAju='$tipeAju',IdAtasan='$IdAtasan',posAkunStart='$posAkunStart',posAkunEnd='$posAkunEnd',posAkunHtgStart='$posAkunHtgStart',posAkunHtgEnd='$posAkunHtgEnd',nik='$nik',no_tlp='$no_tlp' 
						where id='".$id."'");

		if ($qry) {
			echo "Data Saved!!";
		} else {
			echo "Failed!!";
		}
		
	} else if ($action=='delete') {
		$id = addslashes($_REQUEST['id']);
		$prc=mssql_query("delete from deptterkait where id='".$id."'");
		if ($prc) {
			echo "Data Save!!";
		} else {
			echo "Failed!!";
		}
	}
?>