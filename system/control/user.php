<?php
error_reporting(0);

	require_once ('../inc/conn.php');
	$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : null;
	if ($_SERVER['HTTP_X_FORWARDED_FOR']){
		$ipaddr = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else{ 
		$ipaddr = $_SERVER['REMOTE_ADDR'];
	}
	
	if ($action=='new') {
		$IdUser = addslashes($_REQUEST['IdUser']);
		$namaUser = addslashes($_REQUEST['namaUser']);
		$Email = addslashes($_REQUEST['Email']);
		$nik = addslashes($_REQUEST['nik']);
		$no_tlp = addslashes($_REQUEST['no_tlp']);
		$KodeDealer = addslashes($_REQUEST['KodeDealer']);
		$tipe = addslashes($_REQUEST['tipe']);
		$divisi = addslashes($_REQUEST['divisi']);
		$department = addslashes($_REQUEST['department']);
		$tipeAju = addslashes($_REQUEST['tipeAju']);
		$IdAtasan = addslashes($_REQUEST['IdAtasan']);
		$posAkunStart = addslashes($_REQUEST['posAkunStart']);
		$posAkunEnd = addslashes($_REQUEST['posAkunEnd']);
		$posAkunHtgStart = addslashes($_REQUEST['posAkunHtgStart']);
		$posAkunHtgEnd = addslashes($_REQUEST['posAkunHtgEnd']);
		$passWord=md5('evopay');
		$statususer = addslashes($_REQUEST['statususer']);

		$akses = addslashes($_REQUEST['akses']);
		$r = explode("#", $akses);
		mssql_query("BEGIN TRAN");
		
			
			$qry_cek2 = mssql_query("select ltrim(rtrim(IdUser)) from sys_user where IdUser = '".ltrim(rtrim($IdUser))."' and isDel='1' ");
			$cek2 = mssql_num_rows($qry_cek2);
				
			if ($cek2==0) {
			
				$qry_cek = mssql_query("select ltrim(rtrim(IdUser)) from sys_user where IdUser = '".ltrim(rtrim($IdUser))."'");
				$cek = mssql_num_rows($qry_cek);
			
				if ($cek==0) {
			
					$query1=mssql_query("insert into sys_user (IdUser,namaUser,Email,nik,no_tlp,KodeDealer,tipe,divisi,department,tipeAju,IdAtasan,posAkunStart,posAkunEnd,posAkunHtgStart,posAkunHtgEnd,passWord, ipentry, tglentry, idstatus) 
						values ('$IdUser','$namaUser','$Email','$nik','$no_tlp','$KodeDealer','$tipe','$divisi','$department','$tipeAju','$IdAtasan','$posAkunStart','$posAkunEnd','$posAkunHtgStart','$posAkunHtgEnd','$passWord', '".$ipaddr."', getdate(), '".$statususer."')
					");
		
					for ($i=0; $i < count($r); $i++) { 
						$query2 = mssql_query("insert into sys_permission (IdUser,IdMenu) 
							values ('$IdUser','$r[$i]')");
					}
					
					if ($query1 and $query2) {
						mssql_query("COMMIT TRAN");
						echo "Data Saved!!";
					} else {
						mssql_query("ROLLBACK TRAN");
						echo "Failed!!";
					}
				
			/*
			apakah sama dengan yg sudah ada atau sama dengan user yg pernah dibuat & dihapus*/
				} else {
					mssql_query("ROLLBACK TRAN");
					echo "User ".$IdUser." sudah digunakan dan Aktif !!";
					
				}
					
			} else {
				mssql_query("ROLLBACK TRAN");
				echo "User ".$IdUser." sudah digunakan dan Tidak Aktif !!";
			}
			
		mssql_query("return");
		
	} else if ($action=='edit') {
		$IdUser = addslashes($_REQUEST['IdUser']);
		$namaUser = addslashes($_REQUEST['namaUser']);
		$Email = addslashes($_REQUEST['Email']);
		$nik = addslashes($_REQUEST['nik']);
		$no_tlp = addslashes($_REQUEST['no_tlp']);
		$KodeDealer = addslashes($_REQUEST['KodeDealer']);
		$tipe = addslashes($_REQUEST['tipe']);
		$divisi = addslashes($_REQUEST['divisi']);
		$department = addslashes($_REQUEST['department']);
		$tipeAju = addslashes($_REQUEST['tipeAju']);
		$IdAtasan = addslashes($_REQUEST['IdAtasan']);
		$posAkunStart = addslashes($_REQUEST['posAkunStart']);
		$posAkunEnd = addslashes($_REQUEST['posAkunEnd']);
		$posAkunHtgStart = addslashes($_REQUEST['posAkunHtgStart']);
		$posAkunHtgEnd = addslashes($_REQUEST['posAkunHtgEnd']);
		$akses = addslashes($_REQUEST['akses']);
		$statususer = addslashes($_REQUEST['statususer']);
		
		mssql_query("BEGIN TRAN");
			$stm1 = "update sys_user set namaUser='$namaUser',Email='$Email',KodeDealer='$KodeDealer',tipe='$tipe',divisi='$divisi',department='$department',tipeAju='$tipeAju',IdAtasan='$IdAtasan',posAkunStart='$posAkunStart',posAkunEnd='$posAkunEnd',posAkunHtgStart='$posAkunHtgStart',posAkunHtgEnd='$posAkunHtgEnd',nik='$nik',no_tlp='$no_tlp',
				ipedit = '".$ipaddr."', tgledit = getdate(), idstatus = '".$statususer."'
			where IdUser='".$IdUser."'";
			
			$qry = mssql_query($stm1);
			$qry2=mssql_query("delete from sys_permission where IdUser='".$IdUser."'");
			$r = explode("#", $akses);
			for ($i=0; $i < count($r); $i++) { 
				$qry3 = mssql_query("insert into sys_permission (IdUser,IdMenu) 
					values ('$IdUser','$r[$i]')");
			}

			if ($qry and $qry2 and $qry3) {
				mssql_query("COMMIT TRAN");
				echo "Data Saved!!";
			} else {
				mssql_query("ROLLBACK TRAN");
				echo "Failed!!";
				//echo $stm1;
			}
		mssql_query("return");
		 
	} else if ($action=='reset') {
		$IdUser = addslashes($_REQUEST['IdUser']);
		$passWord=md5('evopay');
		$prc=mssql_query("update sys_user set passWord='$passWord', ipedit = '".$ipaddr."', tgledit = getdate() where IdUser='".$IdUser."'");
		if ($prc) {
			echo "Data Save!! Password reset into : evopay";
		} else {
			echo "Failed!!";
		}
	} else if ($action=='delete') {
		$IdUser = addslashes($_REQUEST['IdUser']);
		$prc=mssql_query("update sys_user set isDel='1', tglDel=getdate(), ipdel = '".$ipaddr."' where IdUser='".$IdUser."'");
		if ($prc) {
			echo "Data Save!!";
		} else {
			echo "Failed!!";
		}
	}
?>