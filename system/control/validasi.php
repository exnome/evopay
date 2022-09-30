<?php
	session_start();
	error_reporting(0);
	include ('../inc/conn.php');
	$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : null;
	
	if ($_SERVER['HTTP_X_FORWARDED_FOR']){
		$ipaddr = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else{ 
		$ipaddr = $_SERVER['REMOTE_ADDR'];
	}

	
	if ($action=='validasi') {
		$IdUser = addslashes($_REQUEST['IdUser']);
		//$level = addslashes($_REQUEST['level']);
		$level = addslashes($_SESSION['level']);
		
		$KodeDealer = addslashes($_REQUEST['KodeDealer']);
		$Tipe = addslashes($_REQUEST['Tipe']);
		$nobukti = addslashes("VP".$_REQUEST['nobukti']);
		$metode_bayar = addslashes($_REQUEST['metode_bayar']);
		//$val = addslashes($_REQUEST['val']);
		$val = ltrim(rtrim($_REQUEST['val']));
		
		$ketreject = "";
		$ketvalidasi = "";
		$ketvalidasi2 = "";
		$over = addslashes($_REQUEST['over']);
		$nextlevel_notif = "";
		$deptterkait_notif = "";
		
		$pesan = "";
		
		
		if (empty($IdUser)) {
			$pesan .= "0#Maaf, gagal melakukan validasi. User validasi kosong !";
			echo $pesan;
			return;
		}
		if (empty($level)) {
			$pesan .= "0#Maaf, gagal melakukan validasi. Level validasi kosong !";
			echo $pesan;
			return;
		}
		if (empty($nobukti)) {
			$pesan .= "0#Maaf, gagal melakukan validasi. NO Bukti kosong !";
			echo $pesan;
			return;
		}
		
		#mssql_query("SET IMPLICIT_TRANSACTIONS ON",$conns);
		mssql_query("BEGIN TRAN",$conns);
			$div = mssql_fetch_array(mssql_query("
											select divisi,case when tipe='HUTANG' then htg_stl_pajak else biaya_yg_dibyar end as totBayar, 
											isnull(ltrim(rtrim(deptterkait)),'') deptterkait,
											userentry, kode_vendor, idatasan
											from DataEvo where nobukti = '".$nobukti."'",$conns));
										
			$user = mssql_fetch_array(mssql_query("select divisi,department, tipe, idstatus from sys_user where IdUser = '".$IdUser."'",$conns));
			$skipdir = mssql_fetch_array(mssql_query('select skip_direksi,skip_direksi2 from settingAkun where id=1',$conns));
			
			$user_aju = mssql_fetch_array(mssql_query("select divisi,department, tipe, idatasan, idstatus from sys_user where IdUser = '".$div['userentry']."'",$conns));
			$level_aju = $user_aju['tipe'];
			$user_entry = $div['userentry'];
			$deptterkait = ltrim(rtrim($div['deptterkait']));
			
			$ketvalidasi = ""; 
			$ketvalidasi2 = "";
			
						
			if ($val=='Accept') {
				if ($KodeDealer=='2010') {
					$is_dealer = "0";
					if ($metode_bayar=='Pety Cash') {
						$batas_direksi1 = 0;
						$batas_direksi2 = 0;
								
						if ($div['totBayar']>$skipdir['skip_direksi2']) {			
							$batas_direksi1 = 1;
							$batas_direksi2 = 1;
								
						} else {
							// tanpa direksi 2
							if ($div['totBayar']>$skipdir['skip_direksi']) {
								$batas_direksi1 = 1;
								$batas_direksi2 = 0;
								
							} else { // tanpa direksi 1 dan 2
								$batas_direksi1 = 0;
								$batas_direksi2 = 0;
							}
						}
						
						$urutan = getUrutan($level,$is_dealer, $batas_direksi1, $batas_direksi2, $IdUser, $deptterkait, $level_aju, $nobukti);
						
						if ($level=='TAX') {
							$ketvalidasi = ""; $insert=true; $query3 = true;
							$nama_lvl = " and level = '".$level."'";
						
						} else if ($level=='ACCOUNTING') {
							$ketvalidasi = ""; $insert=true; $query3 = true;
							$nama_lvl = " and level = '".$level."'";
						
						} else if ($level=='SECTION HEAD') {
							$ketvalidasi = addslashes($_REQUEST['note_sectionhead']);
							$insert=true; $query3 = true;
							$nama_lvl = " and level = '".$level."'";
							
						} else if ($level=='DEPT. HEAD') {
							/*$cek = mssql_fetch_array(mssql_query("select top 1 level from DataEvoVal where nobukti='".$nobukti."' order by tglentry desc",$conns));
						
							if ($cek['level']=='DEPT. HEAD FINANCE / DIV. HEAD FAST' and $user['divisi']=='FINANCE and ACCOUNTING') {
								$ketvalidasi = addslashes($_REQUEST['note_div_head_fast']);
								$insert=true;
								$query3 = insertAcc($nobukti);
							} else {
								$ketvalidasi = addslashes($_REQUEST['note_dept_head']);
								$insert=true; $query3 = true;
								$nama_lvl = " and level = '".$level."'";
							}*/
							
							$cek = mssql_fetch_array(mssql_query("select top 1 level from DataEvoVal where nobukti='".$nobukti."' order by tglentry desc",$conns));
						
							if ($cek['level']=='DEPT. HEAD FINANCE' and $user['divisi']=='FINANCE and ACCOUNTING' and $user['department']=='FINANCE') {
								$ketvalidasi = addslashes($_REQUEST['note_dept_head_fin']);
								$insert=true; $query3 = true;
								//$nama_lvl = " and level in ('DEPT. HEAD FINANCE','DEPT. HEAD')";
								$nama_lvl = " and level in ('DEPT. HEAD FINANCE')";
							} else {
								$ketvalidasi = addslashes($_REQUEST['note_dept_head']);
								$insert=true; $query3 = true;
								$nama_lvl = " and level = '".$level."'";
							}
							
						} else if ($level=='DIV. HEAD') {
							$cek = mssql_fetch_array(mssql_query("select top 1 level from DataEvoVal where nobukti='".$nobukti."' order by tglentry desc",$conns));
						
							if ($cek['level']=='DEPT. HEAD FINANCE / DIV. HEAD FAST' and $user['divisi']=='FINANCE and ACCOUNTING') {
								$ketvalidasi = addslashes($_REQUEST['note_div_head_fast']);
								$insert=true;
								$query3 = insertAcc($nobukti);
								//$nama_lvl = " and level in ('DEPT. HEAD FINANCE / DIV. HEAD FAST','DIV. HEAD')";
								$nama_lvl = " and level in ('DEPT. HEAD FINANCE / DIV. HEAD FAST')";
							} else {
								$ketvalidasi = addslashes($_REQUEST['note_dept_head']);
								$insert=true; $query3 = true;
								$nama_lvl = " and level = '".$level."'";
							}
						
						
						} else if ($level=='DIREKSI') {
							$ketvalidasi = addslashes($_REQUEST['note_direksi']);
							$insert=true; $query3 = true;
							$nama_lvl = " and level = '".$level."'";
						
						} else if ($level=='DIREKSI 2') {
							$ketvalidasi = addslashes($_REQUEST['note_direksi2']);
							$insert=true; $query3 = true;
							$nama_lvl = " and level = '".$level."'";
						
						} else if ($level=='FINANCE') {
							$ketvalidasi = ""; $insert=true; $query3 = true;
							$nama_lvl = " and level = '".$level."'";
						
						// dept lain
						} else if ($level=='ADMIN') {
							$ketvalidasi = ""; $insert=true; $query3 = true;
							$nama_lvl = " and level = '".$level."'";
						}	

						
					} else {
						
						/*if ($div['totBayar']>$skipdir['skip_direksi2']) {
							//$urutan = "2,3,4,5,6,7,8,9,11";
							if ($user_aju['tipe']=="SECTION HEAD") {
								$urutan = "2,4,5,6,7,8,9,11";
							} else if ($user_aju['tipe']=="DEPT. HEAD") {
								$urutan = "2,5,6,7,8,9,11";
							} else if ($user_aju['tipe']=="DIV. HEAD") {
								$urutan = "2,6,7,8,9,11";
							} else if ($user_aju['tipe']=="DIREKSI") {	
								$urutan = "2,7,8,9,11";
							} else if ($user_aju['tipe']=="DIREKSI 2") {
								$urutan = "2,8,9,11";
							} else {
								$urutan = "2,3,4,5,6,7,8,9,11";
							}	
							
						} else {
							if ($div['totBayar']>$skipdir['skip_direksi']) {
								//$urutan = "2,3,4,5,6,8,9,11";
								if ($user_aju['tipe']=="SECTION HEAD") {
									$urutan = "2,4,5,6,8,9,11";
								} else if ($user_aju['tipe']=="DEPT. HEAD") {
									$urutan = "2,5,6,8,9,11";
								} else if ($user_aju['tipe']=="DIV. HEAD") {
									$urutan = "2,6,8,9,11";
								} else if ($user_aju['tipe']=="DIREKSI") {	
									$urutan = "2,8,9,11";
								} else if ($user_aju['tipe']=="DIREKSI 2") {
									$urutan = "2,9,11";
								} else {
									$urutan = "2,3,4,5,6,8,9,11";
								}
								
							} else {
								if ($user_aju['tipe']=="SECTION HEAD") {
									$urutan = "2,4,5,8,9,11";
								} else if ($user_aju['tipe']=="DEPT. HEAD") {
									$urutan = "2,5,8,9,11";
								} else if ($user_aju['tipe']=="DIV. HEAD") {
									$urutan = "2,8,9,11";
								} else if ($user_aju['tipe']=="DIREKSI") {	
									$urutan = "2,8,9,11";
								} else if ($user_aju['tipe']=="DIREKSI 2") {
									$urutan = "2,8,9,11";
								} else {
									$urutan = "2,3,4,5,8,9,11";
								}								
								$urutan = "2,3,4,5,8,9,11";
							}
						}*/
						
						if ($div['totBayar']>$skipdir['skip_direksi2']) {
							$batas_direksi1 = 1;
							$batas_direksi2 = 1;	
							
						} else {
							if ($div['totBayar']>$skipdir['skip_direksi']) {
								$batas_direksi1 = 1;
								$batas_direksi2 = 0;	
								
							} else {
								$batas_direksi1 = 0;
								$batas_direksi2 = 0;	
								
							}
						}
						
						$urutan = getUrutan($level,$is_dealer, $batas_direksi1, $batas_direksi2, $IdUser, $deptterkait, $level_aju, $nobukti);
						
						if ($level=='TAX') {
							$ketvalidasi = ""; $insert=true; $query3 = true;
							$nama_lvl = " and level = '".$level."'";
						
						} else if ($level=='ACCOUNTING') {
							$ketvalidasi = ""; $insert=true; $query3 = true;
							$nama_lvl = " and level = '".$level."'";
								
						} else if ($level=='SECTION HEAD') {
							$ketvalidasi = addslashes($_REQUEST['note_sectionhead']);
							$insert=true; $query3 = true;
							$nama_lvl = " and level = '".$level."'";
							
						} else if ($level=='DEPT. HEAD') {
							$cek = mssql_fetch_array(mssql_query("select top 1 level from DataEvoVal where nobukti='".$nobukti."' order by tglentry desc",$conns));
							
							if ($cek['level']=='DEPT. HEAD FINANCE' and $user['divisi']=='FINANCE and ACCOUNTING' and $user['department']=='FINANCE') {
								$ketvalidasi = addslashes($_REQUEST['note_div_head_fast']); 
								$insert=true; $query3 = true; 
								//$nama_lvl = " and level in ('DEPT. HEAD FINANCE','DEPT. HEAD')";
								$nama_lvl = " and level in ('DEPT. HEAD FINANCE')";
							} else {
								$ketvalidasi = addslashes($_REQUEST['note_dept_head']);
								$insert=true; $query3 = true;
								$nama_lvl = " and level = '".$level."'";
							}
							
						} else if ($level=='DIV. HEAD') {
							
							//$cek = mssql_fetch_array(mssql_query("select top 1 level from DataEvoVal where nobukti='".$nobukti."' order by tglentry desc"));
							$cek = mssql_fetch_array(mssql_query("select top 1 level from DataEvoVal a 
																inner join sys_level b on a.level = b.nama_lvl
																where a.nobukti='".$nobukti."' order by b.urutan desc",$conns));							
							
							if ($cek['level']=='DEPT. HEAD FINANCE / DIV. HEAD FAST' and $user['divisi']=='FINANCE and ACCOUNTING') {
								$ketvalidasi = addslashes($_REQUEST['note_div_head_fast']); 
								$insert=true;
								$query3 = insertAcc($nobukti);
								//$nama_lvl = " and level in ('DEPT. HEAD FINANCE / DIV. HEAD FAST','DIV. HEAD')";
								$nama_lvl = " and level in ('DEPT. HEAD FINANCE / DIV. HEAD FAST')";
						
							} else {
								$ketvalidasi = addslashes($_REQUEST['note_div_head']);
								$insert=true; $query3 = true;
								$nama_lvl = " and level = '".$level."'";
							}
							
							
						} else if ($level=='DIREKSI') {
							$ketvalidasi = addslashes($_REQUEST['note_direksi']);
							$insert=true; $query3 = true;
							$nama_lvl = " and level = '".$level."'";
						
						} else if ($level=='DIREKSI 2') {
							$ketvalidasi = addslashes($_REQUEST['note_direksi2']);
							$insert=true; $query3 = true;
							$nama_lvl = " and level = '".$level."'";
						
								
						} else if ($level=='FINANCE') {
							$ketvalidasi = "";
							$insert=true; $query3 = true;
							$nama_lvl = " and level = '".$level."'";
						 
						// dept lain
						} else if ($level=='ADMIN') {
							$ketvalidasi = ""; $insert=true; $query3 = true;
							$nama_lvl = " and level = '".$level."'";
						}	
						
					}
					
				// cabang	
				} else {
					$is_dealer = "1";
					$nama_lvl = " and level = '".$level."'";
					if ($Tipe=='HUTANG') {
												
						if ($user_aju['tipe']=="SECTION HEAD") {
							$urutan = "2,4,5,7";
						} else if ($user_aju['tipe']=="ADH") {
							$urutan = "2,5,7";
						} else if ($user_aju['tipe']=="KEPALA CABANG") {
							$urutan = "2,7";
						} else {
							$urutan = "2,3,4,5,7";
						}
						
						if ($level=='ACCOUNTING') {
							$ketvalidasi = "";
							$insert=true; $query3 = true;
						} else if ($level=='SECTION HEAD') {
							$ketvalidasi = addslashes($_REQUEST['note_sectionhead']);
							$insert=true; $query3 = true;
						} else if ($level=='ADH') {
							$ketvalidasi = addslashes($_REQUEST['note_adh']);
							$insert=true; $query3 = true;
						} else if ($level=='KEPALA CABANG') {
							$ketvalidasi = addslashes($_REQUEST['note_branch_manager']);
							$insert=true;
							$query3 = insertAcc($nobukti);
						}
					} else if ($Tipe=='BIAYA') {
						if ($metode_bayar=='Pety Cash') {
							//$urutan = "2,3,4,7";
							
							if ($user_aju['tipe']=="SECTION HEAD") {
								$urutan = "2,4,7";
							} else if ($user_aju['tipe']=="ADH") {
								$urutan = "2,7";
							} else if ($user_aju['tipe']=="KEPALA CABANG") {
								$urutan = "2,7";
							} else {
								$urutan = "2,3,4,7";
							}
							
							if ($level=='ACCOUNTING') {
								$ketvalidasi = "";
								$insert = true; $query3 = true;
							} else if ($level=='SECTION HEAD') {
								$ketvalidasi = addslashes($_REQUEST['note_sectionhead']);
								$insert = true; $query3 = true;
							} else if ($level=='ADH') {
								$ketvalidasi = addslashes($_REQUEST['note_adh']);
								$insert = true;
								$query3 = insertAcc($nobukti);
							}
						} else {
							if ($over=="0") {
								//$urutan = "2,3,4,5,7";
								if ($user_aju['tipe']=="SECTION HEAD") {
									$urutan = "2,4,5,7";
								} else if ($user_aju['tipe']=="ADH") {
									$urutan = "2,5,7";
								} else if ($user_aju['tipe']=="KEPALA CABANG") {
									$urutan = "2,7";
								} else {
									$urutan = "2,3,4,5,7";
								}
								
								if ($level=='ACCOUNTING') {
									$ketvalidasi = "";
									$insert = true; $query3 = true;
								} else if ($level=='SECTION HEAD') {
									$ketvalidasi = addslashes($_REQUEST['note_sectionhead']);
									$insert = true; $query3 = true;
								} else if ($level=='ADH') {
									$ketvalidasi = addslashes($_REQUEST['note_adh']);
									$insert = true;
									$query3 = insertAcc($nobukti);
								}
							} else if ($over=="1") {
								//$urutan = "2,3,4,5,6,7";
								if ($user_aju['tipe']=="SECTION HEAD") {
									$urutan = "2,4,5,6,7";
								} else if ($user_aju['tipe']=="ADH") {
									$urutan = "2,5,6,7";
								} else if ($user_aju['tipe']=="KEPALA CABANG") {
									$urutan = "2,6,7";
								} else {
									$urutan = "2,3,4,5,6,7";
								}
								
								
								if ($level=='ACCOUNTING') {
									$ketvalidasi = "";
									$insert = true; $query3 = true;
								} else if ($level=='SECTION HEAD') {
									$ketvalidasi = addslashes($_REQUEST['note_sectionhead']);
									$insert = true; $query3 = true;
								} else if ($level=='ADH') {
									$ketvalidasi = addslashes($_REQUEST['note_adh']);
									$insert = true; $query3 = true;
								} else if ($level=='KEPALA CABANG') {
									$ketvalidasi = addslashes($_REQUEST['note_branch_manager']);
									$insert = true; $query3 = true;
								} else if ($level=='OM') {
									$ketvalidasi = addslashes($_REQUEST['note_om']);
									$insert = true; 
									$query3 = insertAcc($nobukti);
								}
							}
						}
					}
				}
				
				if ($insert==true) {
					
				} else {
					$query1 = true;
				}
				
				
			} else if ($val=='Reject') {
				//$query1 = true; $query3 = true; 
				$query4 = true; $query5 = true; $query6 = true; $query7 = true;
				
				$ketreject = addslashes($_REQUEST['ketreject']);
				$query1 = mssql_query("update DataEvoTagihan set isreject=1, ipentry = '".$ipaddr."', useragent = '".$_SERVER['HTTP_USER_AGENT']."'
							where nobukti = '".$nobukti."'",$conns);
							
				$query3 = mssql_query("update DataEvo set status = 'Reject' where nobukti = '".$nobukti."'",$conns);	
				//echo "update DataEvo set status = 'Reject' where nobukti = '".$nobukti."'";
							
							
			}
			
			/*if ($div['deptterkait']==$_SESSION['evo_dept']) { 
				$ketvalidasi = addslashes($_REQUEST['note_deptterkait']);
				$ketvalidasi2 = addslashes($_REQUEST['note_deptterkait']);
			}*/
			
			if (!empty($_REQUEST['note_deptterkait'])) { 
				$ketvalidasi = addslashes($_REQUEST['note_deptterkait']);
				$ketvalidasi2 = addslashes($_REQUEST['note_deptterkait']);
			}
			
			$sql2 = "update DataEvoVal set validasi='".$val."',uservalidasi='".$IdUser."',tglvalidasi=getdate(),
						ketvalidasi='".$ketvalidasi."', ketvalidasi2='".$ketvalidasi2."', ketreject='".$ketreject."', 
						ipentry = '".$ipaddr."', useragent = '".$_SERVER['HTTP_USER_AGENT']."'
						where nobukti = '".$nobukti."' and ISNULL(validasi, '')='' $nama_lvl";
			
			$query2 = mssql_query($sql2,$conns);
			
			if ($query2) { // iki
				/*
				$sql21 = "update DataEvoVal set tglvalidasi=getdate(), ipentry = '".$ipaddr."', useragent = '".$_SERVER['HTTP_USER_AGENT']."'
						where nobukti = '".$nobukti."' and uservalidasi='".$IdUser."' ";			
				$query21 = mssql_query($sql21,$conns);
				*/
				$cek = false;
				
				$qry_cek = mssql_query("select nobukti 
										from dataevoval 
										where nobukti = '".$nobukti."' $nama_lvl and ISNULL(validasi, '') !='' and ISNULL(tglvalidasi, '') !=''
										and ISNULL(deptterkait, '') ='' ",$conns);
				$jml_cek = mssql_num_rows($qry_cek);
				
				if ($jml_cek==1) {	
					$cek = true;
				} else {
					$qry_cek = mssql_query("select nobukti 
											from dataevoval 
											where nobukti = '".$nobukti."' $nama_lvl and ISNULL(validasi, '') !='' and ISNULL(tglvalidasi, '') !=''
											and ISNULL(deptterkait, '') !='' ",$conns);
					$jml_cek = mssql_num_rows($qry_cek);
					
					if ($jml_cek==1) {							
						$cek = true;
					}
				}
				
				//$cek = true;
				if ($cek) {	
					
					if ($val=='Accept') {
						//echo $urutan;
						/*if (!empty($div['deptterkait']) and str_replace(" ","",$div['deptterkait'])!='') {
							$nextlvl_arr = getLevelDeptTerkait($div['deptterkait'],$nobukti, $level, $urutan, $div['divisi'], $KodeDealer, $is_dealer);
							$nextlvl = $nextlvl_arr['tipe'];
							$nextlvl_jml = $nextlvl_arr['jml'];
							if ($nextlvl_jml==0) {
								$depterkait_in = "";
							} else {
								$depterkait_in = $div['deptterkait'];
							}
						} else {
							$nextlvl = getLevel($div['divisi'],$KodeDealer,$nobukti,$urutan,$is_dealer,$level_aju);
						}*/
						
						if ($level=='TAX' or $level=='ACCOUNTING') {
							$nextlvl = getLevel($div['divisi'],$KodeDealer,$nobukti,$urutan,$is_dealer,$level_aju);
							
						} else {	
							
							if (!empty($div['deptterkait']) and str_replace(" ","",$div['deptterkait'])!='') {
								$nextlvl_arr = getLevelDeptTerkait($div['deptterkait'],$nobukti, $level, $urutan, $div['divisi'], $KodeDealer, $is_dealer);
								$nextlvl = $nextlvl_arr['tipe'];
								$nextlvl_jml = $nextlvl_arr['jml'];
								if ($nextlvl_jml==0) {
									$depterkait_in = "";
								} else {
									$depterkait_in = $div['deptterkait'];
								}
							} else {
								$nextlvl = getLevel($div['divisi'],$KodeDealer,$nobukti,$urutan,$is_dealer,$level_aju);
							}	
						}		
											
						$nextlvl = trim($nextlvl);
						
						if (!empty($nextlvl)) {
						
							if ($level=="DIREKSI 2") {
								$sql_cekkasir = mssql_query("select level from dataevoval where nobukti = '".$nobukti."' and level = 'KASIR'",$conns);
								$cekkasir = mysql_num_rows($sql_cekkasir);
								
								if ($cekkasir==0) {
									$sql1 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait) 
											values ('$nobukti','$KodeDealer','".$nextlvl."',getdate(), '".$depterkait_in."')";
									$query1 = mssql_query($sql1,$conns);
								} else {
									$query1 = true;
									$query3 = true;
									$query4 = true;
									$query5 = true;
									$query6 = true;
									$query7 = true;
								}
							} else {
								$sql1 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait) 
										values ('$nobukti','$KodeDealer','".$nextlvl."',getdate(), '".$depterkait_in."')";
								$query1 = mssql_query($sql1,$conns);
							}
							
						} else {
							$query1 = true;
						}					
						
						#----------------------------- status user 
						if ($query1) {
							if ($nextlvl=='SECTION HEAD' or $nextlvl=='DEPT. HEAD' or $nextlvl=='DIV. HEAD'  or $nextlvl=='DIREKSI'  or $nextlvl=='DIREKSI 2') {
								if ($level=="ACCOUNTING") {
									$user_approve = $user_entry;
								} else {
									$user_approve = $IdUser;
								}
								
								$sqlatasanx = "
											select  a.idstatus, a.tipe, (select b.idstatus from sys_user b where b.IdUser = a.IdAtasan) statusatasan, 
											a.IdAtasan, (select b.tipe from sys_user b where b.IdUser = a.IdAtasan) tipeatasan, 
											(select c.IdAtasan from sys_user c where c.IdUser = a.IdAtasan) idatasan2, 
											(select d.tipe from sys_user d where d.IdUser in (select c.IdAtasan from sys_user c where c.IdUser = a.IdAtasan)) tipeatasan2,
											 
											(select c.IdAtasan from sys_user c where c.IdUser in (select c.IdAtasan from sys_user c where c.IdUser = a.IdAtasan)) idatasan3,
											(select d.tipe from sys_user d 
												where d.IdUser in (select c.IdAtasan from sys_user c where c.IdUser 
													in (select c.IdAtasan from sys_user c where c.IdUser = a.IdAtasan))) tipeatasan3
											
											from sys_user a where a.IdUser = '".$user_approve."'";
								$user_ajux = mssql_fetch_array(mssql_query($sqlatasanx,$conns));
								//echo "<pre>$sqlatasan</pre>";
											
								$status_atasan = $user_ajux['statusatasan'];
								$tipe_atasan = $user_ajux['tipeatasan'];
								$atasan = $user_ajux['IdAtasan'];
								$idatasan2 = $user_ajux['idatasan2'];
								$nextlvlx = $nextlvl;
								
								if ($nextlvl==$tipe_atasan) {
									//if ($tipe_atasan=='SECTION HEAD' or $tipe_atasan=='DEPT. HEAD'  or $tipe_atasan=='DIV. HEAD') {
										if ($status_atasan==3 or $status_atasan==4) {
											/*
											1	Aktif
											2	Non Aktif
											3	ByPass
											4	Concurrent
											*/
											if ($status_atasan==3) {
												$sql2a = "update DataEvoVal set validasi = 'Accept', uservalidasi='########',
															tglvalidasi= NULL, ketvalidasi='########', 
															ipentry = '".$ipaddr."', useragent = '".$_SERVER['HTTP_USER_AGENT']."'
															where nobukti = '".$nobukti."' and level = '".$nextlvl."'";
											
											} else if ($status_atasan==4) {
												$sql2a = "update DataEvoVal set validasi = 'Accept', uservalidasi='".$idatasan2."',
															tglvalidasi= NULL, ketvalidasi='Concurrent', 
															ipentry = '".$ipaddr."', useragent = '".$_SERVER['HTTP_USER_AGENT']."'
															where nobukti = '".$nobukti."' and level = '".$nextlvl."'";
											
											}
												
											$query2a = mssql_query($sql2a,$conns);
											
											if (!empty($div['deptterkait']) and str_replace(" ","",$div['deptterkait'])!='') {
												$nextlvl_arr = getLevelDeptTerkait($div['deptterkait'],$nobukti, $nextlvl, $urutan, $div['divisi'], $KodeDealer, $is_dealer);
												$nextlvl = $nextlvl_arr['tipe'];
												$nextlvl_jml = $nextlvl_arr['jml'];
												if ($nextlvl_jml==0) {
													$depterkait_in = "";
												} else {
													$depterkait_in = $div['deptterkait'];
												}												
												$nextlvla = $nextlvl_arr['tipe'];
											
											} else {
												$nextlvla = getLevel($div['divisi'],$KodeDealer,$nobukti,$urutan,$is_dealer,$level_aju);
											
											}
											
											$nextlvla = trim($nextlvla);
							
											if (!empty($nextlvla)) {										
												$sqla1 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait) 
														values ('$nobukti','$KodeDealer','".$nextlvla."',getdate(), '".$depterkait_in."')";
												$querya1 = mssql_query($sqla1,$conns);
												
												$nextlevel_notif = $nextlvla;
												$deptterkait_notif = $depterkait_in;
							
											} else {
												$querya1 = true;
											}
											
										} else {
										
											$nextlevel_notif = $nextlvl;
											$deptterkait_notif = $depterkait_in;
										}
									//}
								}
							}
							
						}
						
						
						
						#------------------ multi validasi			
						/*
							ADMIN
							TAX
							ACCOUNTING
							SECTION HEAD
							DEPT. HEAD
							DIV. HEAD
							DIREKSI
							DIREKSI 2
							FINANCE
							DEPT. HEAD FINANCE / DIV. HEAD FAST
							KASIR
							*/		
							
						if ($status_atasan==3 or $status_atasan==4) {
							$level = $nextlvlx;
						
						} else {		
							$level = trim(addslashes($_SESSION['level']));
							$kodevendor = ltrim(rtrim($div['kode_vendor']));
							$div_user = $user['divisi'];
							$dept_user = $user['department'];
							// echo $level."__".$div_user;
						}
								
						if ($div['totBayar']>$skipdir['skip_direksi2']) {
							//$urutan = "2,3,4,5,6,7,8,9,11";
							/*
							ADMIN
							TAX
							ACCOUNTING
							SECTION HEAD
							DEPT. HEAD
							DIV. HEAD
							DIREKSI
							DIREKSI 2
							FINANCE
							DEPT. HEAD FINANCE
							DEPT. HEAD FINANCE / DIV. HEAD FAST
							KASIR
							*/
							//echo $level;
								
							if (trim($user_aju['divisi'])=='FINANCE and ACCOUNTING' or trim($user_aju['divisi'])=='all') {	
								if (trim($user_aju['department'])=='FINANCE') {
									
									//echo $level;
										
									if ($level=='DIREKSI 2') {									
										if ($kodevendor=="MBLT-0001" or $kodevendor=="PRTTAM") {
											$query4 = true;
											$query5 = true;
											$query6 = true;
											$query7 = true;
											
										} else {
											
											$user_multi = mssql_fetch_array(mssql_query("select tipe, iduser from sys_user_tipe 
																			where IdUser = '".$user_aju['idatasan']."' and tipe = 'FINANCE'",$conns ));
							
											$stdevoval = mssql_query("select CONVERT(varchar,tglvalidasi,120) tglvalidasi, validasi, 
																		 uservalidasi, ketvalidasi
																		 from DataEvoVal 
																		 where nobukti='".$nobukti."' and level = 'SECTION HEAD'
																		 and uservalidasi = '".$user_multi['iduser']."'
																		 order by tglentry desc",$conns);
											$dataevoval = mssql_fetch_array($stdevoval);
											$cekevoval = mssql_num_rows($stdevoval);
				
											$ketvalidasi_sect = $dataevoval['ketvalidasi']; 
											$tglvalidasi_sect = $dataevoval['tglvalidasi']; 
											$validasi_sect = $dataevoval['validasi']; 
											$uservalidasi_sect = $dataevoval['uservalidasi']; 
											
											if ($cekevoval>0) {
												
												$sql4 = "update DataEvoVal set validasi='".$validasi_sect."',uservalidasi='".$uservalidasi_sect."',
															tglvalidasi= '".$tglvalidasi_sect."', ketvalidasi='".$ketvalidasi_sect."', 
															ipentry = '".$ipaddr."', useragent = '".$_SERVER['HTTP_USER_AGENT']."'
															where nobukti = '".$nobukti."' and level = 'FINANCE'";
												$query4 = mssql_query($sql4,$conns);
												
												
												#-------------- dept head + releaser
												$user_multi = mssql_fetch_array(mssql_query("select tipe, iduser from sys_user_tipe 
																		where tipe = 'DEPT. HEAD FINANCE'",$conns ));
						
												$stdevoval = mssql_query("select CONVERT(varchar,tglvalidasi,120) tglvalidasi, validasi, 
																		 uservalidasi, ketvalidasi
																		 from DataEvoVal 
																		 where nobukti='".$nobukti."' and level = 'DEPT. HEAD'
																		  and uservalidasi = '".$user_multi['iduser']."'
																		 order by tglentry desc",$conns);
												$dataevoval = mssql_fetch_array($stdevoval);
												$cekevoval = mssql_num_rows($stdevoval);
												
												if ($cekevoval>0) {
													
													$ketvalidasi_sect = $dataevoval['ketvalidasi']; 
													$tglvalidasi_sect = $dataevoval['tglvalidasi']; 
													$validasi_sect = $dataevoval['validasi']; 
													$uservalidasi_sect = $dataevoval['uservalidasi']; 
													
													$sql5 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait, 
															validasi, uservalidasi, tglvalidasi, ketvalidasi, ipentry, useragent ) 
															values ('$nobukti','$KodeDealer','DEPT. HEAD FINANCE', getdate(), '',
															'$validasi_sect','$uservalidasi_sect', '$tglvalidasi_sect','$ketvalidasi_sect',
															'".$ipaddr."', '".$_SERVER['HTTP_USER_AGENT']."')";
													$query5 = mssql_query($sql5,$conns);	
													
														
													#-------------- div head + releaser
													$user_multi = mssql_fetch_array(mssql_query("select tipe, iduser from sys_user_tipe 
																			where tipe = 'DEPT. HEAD FINANCE / DIV. HEAD FAST'",$conns ));
							
													$stdevoval = mssql_query("select CONVERT(varchar,tglvalidasi,120) tglvalidasi, validasi, 
																			 uservalidasi, ketvalidasi
																			 from DataEvoVal 
																			 where nobukti='".$nobukti."' and level = 'DIV. HEAD'
																			  and uservalidasi = '".$user_multi['iduser']."'
																			 order by tglentry desc",$conns);
													$dataevoval = mssql_fetch_array($stdevoval);
													$cekevoval = mssql_num_rows($stdevoval);
													
													if ($cekevoval>0) {
														$ketvalidasi_sect = $dataevoval['ketvalidasi']; 
														$tglvalidasi_sect = $dataevoval['tglvalidasi']; 
														$validasi_sect = $dataevoval['validasi']; 
														$uservalidasi_sect = $dataevoval['uservalidasi']; 
														
														$sql6 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait, 
																validasi, uservalidasi, tglvalidasi, ketvalidasi, ipentry, useragent ) 
																values ('$nobukti','$KodeDealer','DEPT. HEAD FINANCE / DIV. HEAD FAST', getdate(), '',
																'$validasi_sect','$uservalidasi_sect', '$tglvalidasi_sect','$ketvalidasi_sect',
																'".$ipaddr."', '".$_SERVER['HTTP_USER_AGENT']."')";
														$query6 = mssql_query($sql6,$conns);	
														
														$insert=true;
														$query3 =  insertAcc($nobukti);
														
														#---------------- kasir
														if ($query3) {
															$sql7 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait) 
																	values ('$nobukti','$KodeDealer','KASIR',getdate(), '')";
															$query7 = mssql_query($sql7,$conns);
														}
													} else {
														$query4 = true;
														$query5 = true;
														$query6 = true;
														$query7 = true;
													}
													
												} else {
													$query4 = true;
													$query5 = true;
													$query6 = true;
													$query7 = true;
												}
												
											} else {
												$query4 = true;
												$query5 = true;
												$query6 = true;
												$query7 = true;
											}
											
										}
									
									
									} else if ($level=='DIREKSI') { // --- OK
									
										if ($kodevendor=="MBLT-0001" or $kodevendor=="PRTTAM") {									
											#-------------- fincek
											$user_multi = mssql_fetch_array(mssql_query("select tipe, iduser from sys_user_tipe 
																			where IdUser = '".$user_aju['idatasan']."' and tipe = 'FINANCE'",$conns ));
							
											$stdevoval = mssql_query("select CONVERT(varchar,tglvalidasi,120) tglvalidasi, validasi, 
																	 uservalidasi, ketvalidasi
																	 from DataEvoVal 
																	 where nobukti='".$nobukti."' and level = 'SECTION HEAD'
																	 and uservalidasi = '".$user_multi['iduser']."'
																	 order by tglentry desc",$conns);
											$dataevoval = mssql_fetch_array($stdevoval);
											$cekevoval = mssql_num_rows($stdevoval);
				
											if ($cekevoval>0) {
												$ketvalidasi_sect = $dataevoval['ketvalidasi']; 
												$tglvalidasi_sect = $dataevoval['tglvalidasi']; 
												$validasi_sect = $dataevoval['validasi']; 
												$uservalidasi_sect = $dataevoval['uservalidasi']; 
												
												$sql4 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait, 
														validasi, uservalidasi, tglvalidasi, ketvalidasi, ipentry, useragent ) 
														values ('$nobukti','$KodeDealer','FINANCE', getdate(), '',
														'$validasi_sect','$uservalidasi_sect', '$tglvalidasi_sect','$ketvalidasi_sect' ,
														'".$ipaddr."', '".$_SERVER['HTTP_USER_AGENT']."')";
												$query4 = mssql_query($sql4,$conns);	
												
												
												#-------------- dept head fin + releaser
												$user_multi = mssql_fetch_array(mssql_query("select tipe, iduser from sys_user_tipe 
																				where tipe = 'DEPT. HEAD FINANCE'",$conns ));
								
												$stdevoval = mssql_query("select CONVERT(varchar,tglvalidasi,120) tglvalidasi, validasi, 
																			 uservalidasi, ketvalidasi
																			 from DataEvoVal 
																			 where nobukti='".$nobukti."' and level = 'DEPT. HEAD'
																			 and uservalidasi = '".$user_multi['iduser']."'
																			 order by tglentry desc",$conns);
												$dataevoval = mssql_fetch_array($stdevoval);
												$cekevoval = mssql_num_rows($stdevoval);
					
												$ketvalidasi_sect = $dataevoval['ketvalidasi']; 
												$tglvalidasi_sect = $dataevoval['tglvalidasi']; 
												$validasi_sect = $dataevoval['validasi']; 
												$uservalidasi_sect = $dataevoval['uservalidasi']; 
												
												if ($cekevoval>0) {
													$sql5 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait, 
															validasi, uservalidasi, tglvalidasi, ketvalidasi, ipentry, useragent ) 
															values ('$nobukti','$KodeDealer','DEPT. HEAD FINANCE', getdate(), '',
															'$validasi_sect','$uservalidasi_sect', '$tglvalidasi_sect','$ketvalidasi_sect' ,
															'".$ipaddr."', '".$_SERVER['HTTP_USER_AGENT']."')";
													$query5 = mssql_query($sql5,$conns);
													
													
													#-------------- div head + releaser
													$user_multi = mssql_fetch_array(mssql_query("select tipe, iduser from sys_user_tipe 
																								where tipe = 'DEPT. HEAD FINANCE / DIV. HEAD FAST'",$conns ));
									
													$stdevoval = mssql_query("select CONVERT(varchar,tglvalidasi,120) tglvalidasi, validasi, 
																				 uservalidasi, ketvalidasi
																				 from DataEvoVal 
																				 where nobukti='".$nobukti."' and level = 'DIV. HEAD'
																				 and uservalidasi = '".$user_multi['iduser']."'
																				 order by tglentry desc",$conns);
													$dataevoval = mssql_fetch_array($stdevoval);
													$cekevoval = mssql_num_rows($stdevoval);
						
													if ($cekevoval>0) {
														$ketvalidasi_sect = $dataevoval['ketvalidasi']; 
														$tglvalidasi_sect = $dataevoval['tglvalidasi']; 
														$validasi_sect = $dataevoval['validasi']; 
														$uservalidasi_sect = $dataevoval['uservalidasi']; 
														
														$sql6 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait, 
																validasi, uservalidasi, tglvalidasi, ketvalidasi, ipentry, useragent ) 
																values ('$nobukti','$KodeDealer','DEPT. HEAD FINANCE / DIV. HEAD FAST', getdate(), '',
																'$validasi_sect','$uservalidasi_sect', '$tglvalidasi_sect','$ketvalidasi_sect' ,
																'".$ipaddr."', '".$_SERVER['HTTP_USER_AGENT']."')";
														$query6 = mssql_query($sql6,$conns);	
														
														$insert = true;
														$query3 =  insertAcc($nobukti);
														//$query3 = true;
														
														#---------------- kasir
														if ($query3) {
															$sql7 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait) 
																	values ('$nobukti','$KodeDealer','KASIR',getdate(), '')";
															$query7 = mssql_query($sql7,$conns);
														}
													} else {
														$query4 = true;
														$query5 = true;
														$query6 = true;
														$query7 = true;
													}
												} else {
													$query4 = true;
													$query5 = true;
													$query6 = true;
													$query7 = true;
												}		
											} else {
												$query4 = true;
												$query5 = true;
												$query6 = true;
												$query7 = true;
											}
											
											
										} else {
											$query4 = true;
											$query5 = true;
											$query6 = true;
											$query7 = true;
										}					
									
									} else if ($level=='FINANCE') { // --- OK
									
										if ($kodevendor=="MBLT-0001" or $kodevendor=="PRTTAM") {									
											#-------------- dept head fin + releaser
											$query4 = true;
											$user_multi = mssql_fetch_array(mssql_query("select tipe, iduser from sys_user_tipe 
																			where tipe = 'DEPT. HEAD FINANCE'",$conns ));
							
											$stdevoval = mssql_query("select CONVERT(varchar,tglvalidasi,120) tglvalidasi, validasi, 
																		 uservalidasi, ketvalidasi
																		 from DataEvoVal 
																		 where nobukti='".$nobukti."' and level = 'DEPT. HEAD'
																		 and uservalidasi = '".$user_multi['iduser']."'
																		 order by tglentry desc",$conns);
											$dataevoval = mssql_fetch_array($stdevoval);
											$cekevoval = mssql_num_rows($stdevoval);
				
											$ketvalidasi_sect = $dataevoval['ketvalidasi']; 
											$tglvalidasi_sect = $dataevoval['tglvalidasi']; 
											$validasi_sect = $dataevoval['validasi']; 
											$uservalidasi_sect = $dataevoval['uservalidasi']; 
											
											if ($cekevoval>0) {
											
												$sql5 = "update DataEvoVal set validasi='".$validasi_sect."',uservalidasi='".$uservalidasi_sect."',
															tglvalidasi= '".$tglvalidasi_sect."', ketvalidasi='".$ketvalidasi_sect."', 
															ipentry = '".$ipaddr."', useragent = '".$_SERVER['HTTP_USER_AGENT']."'
															where nobukti = '".$nobukti."' and level = 'DEPT. HEAD FINANCE'";
												$query5 = mssql_query($sql5,$conns);
												
												
												#-------------- div head + releaser
												$user_multi = mssql_fetch_array(mssql_query("select tipe, iduser from sys_user_tipe 
																							where tipe = 'DEPT. HEAD FINANCE / DIV. HEAD FAST'",$conns ));
								
												$stdevoval = mssql_query("select CONVERT(varchar,tglvalidasi,120) tglvalidasi, validasi, 
																			 uservalidasi, ketvalidasi
																			 from DataEvoVal 
																			 where nobukti='".$nobukti."' and level = 'DIV. HEAD'
																			 and uservalidasi = '".$user_multi['iduser']."'
																			 order by tglentry desc",$conns);
												$dataevoval = mssql_fetch_array($stdevoval);
												$cekevoval = mssql_num_rows($stdevoval);
					
												if ($cekevoval>0) {
													$ketvalidasi_sect = $dataevoval['ketvalidasi']; 
													$tglvalidasi_sect = $dataevoval['tglvalidasi']; 
													$validasi_sect = $dataevoval['validasi']; 
													$uservalidasi_sect = $dataevoval['uservalidasi']; 
													
													$sql6 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait, 
															validasi, uservalidasi, tglvalidasi, ketvalidasi, ipentry, useragent ) 
															values ('$nobukti','$KodeDealer','DEPT. HEAD FINANCE / DIV. HEAD FAST', getdate(), '',
															'$validasi_sect','$uservalidasi_sect', '$tglvalidasi_sect','$ketvalidasi_sect' ,
															'".$ipaddr."', '".$_SERVER['HTTP_USER_AGENT']."')";
													$query6 = mssql_query($sql6,$conns);	
													
													$insert = true;
													$query3 =  insertAcc($nobukti);
													//$query3 = true;
													
													#---------------- kasir
													if ($query3) {
														$sql7 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait) 
																values ('$nobukti','$KodeDealer','KASIR',getdate(), '')";
														$query7 = mssql_query($sql7,$conns);
													}
	
												} else {
													$query4 = true;
													$query5 = true;
													$query6 = true;
													$query7 = true;
												}	
												
											} else {
												$query4 = true;
												$query5 = true;
												$query6 = true;
												$query7 = true;
											}		
										
											
											
										}  else {	
											$user_multi = mssql_fetch_array(mssql_query("select tipe, iduser from sys_user_tipe 
																			where tipe = 'DEPT. HEAD FINANCE'",$conns ));
							
											$stdevoval = mssql_query("select CONVERT(varchar,tglvalidasi,120) tglvalidasi, validasi, 
																		 uservalidasi, ketvalidasi
																		 from DataEvoVal 
																		 where nobukti='".$nobukti."' and level = 'DEPT. HEAD'
																		 and uservalidasi = '".$user_multi['iduser']."'
																		 order by tglentry desc",$conns);
											$dataevoval = mssql_fetch_array($stdevoval);
											$cekevoval = mssql_num_rows($stdevoval);
				
											$ketvalidasi_sect = $dataevoval['ketvalidasi']; 
											$tglvalidasi_sect = $dataevoval['tglvalidasi']; 
											$validasi_sect = $dataevoval['validasi']; 
											$uservalidasi_sect = $dataevoval['uservalidasi']; 
											
											if ($cekevoval>0) {
												$query4 = true;	
												
												if ($cekevoval>0) {
													#---------------- releaser 1 
													$sql5 = "update DataEvoVal set validasi='".$validasi_sect."',uservalidasi='".$uservalidasi_sect."',
															tglvalidasi= '".$tglvalidasi_sect."', ketvalidasi='".$ketvalidasi_sect."', 
															ipentry = '".$ipaddr."', useragent = '".$_SERVER['HTTP_USER_AGENT']."'
															where nobukti = '".$nobukti."' and level = 'DEPT. HEAD FINANCE'";
													$query5 = mssql_query($sql5,$conns);
														
													
													#-------------- div head + releaser
													$user_multi = mssql_fetch_array(mssql_query("select tipe, iduser from sys_user_tipe 
																			where tipe = 'DEPT. HEAD FINANCE / DIV. HEAD FAST'",$conns ));
												
													$stdevoval = mssql_query("select CONVERT(varchar,tglvalidasi,120) tglvalidasi, validasi, 
																				 uservalidasi, ketvalidasi
																				 from DataEvoVal 
																				 where nobukti='".$nobukti."' and level = 'DIV. HEAD'
																				 and uservalidasi = '".$user_multi['iduser']."'
																				 order by tglentry desc",$conns);
													$dataevoval = mssql_fetch_array($stdevoval);
													$cekevoval = mssql_num_rows($stdevoval);
						
													if ($cekevoval>0) {
														$ketvalidasi_sect = $dataevoval['ketvalidasi']; 
														$tglvalidasi_sect = $dataevoval['tglvalidasi']; 
														$validasi_sect = $dataevoval['validasi']; 
														$uservalidasi_sect = $dataevoval['uservalidasi']; 
														
														$sql6 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait, 
																validasi, uservalidasi, tglvalidasi, ketvalidasi, ipentry, useragent ) 
																values ('$nobukti','$KodeDealer','DEPT. HEAD FINANCE / DIV. HEAD FAST', getdate(), '',
																'$val','$uservalidasi_sect', '$tglvalidasi_sect','$ketvalidasi_sect',
															'".$ipaddr."', '".$_SERVER['HTTP_USER_AGENT']."')";
														$query6 = mssql_query($sql6,$conns);	
														
														$insert=true;
														$query3 =  insertAcc($nobukti);
														
														#---------------- kasir
														if ($query3) {
															$sql7 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait) 
																	values ('$nobukti','$KodeDealer','KASIR',getdate(), '')";
															$query7 = mssql_query($sql7,$conns);
														}
														
													}   else {
														$query3 = true;
														$query4 = true;
														$query5 = true;
														$query6 = true;
														$query7 = true;
													}	
													
												}   else {
													$query3 = true;
													$query4 = true;
													$query5 = true;
													$query6 = true;
													$query7 = true;
												}	
																			
											} else {
												$query4 = true;
												$query5 = true;
												$query6 = true;
												$query7 = true;
											}
										
										}	
									
									} else if ($level=='DEPT. HEAD') {
										$user_multi = mssql_fetch_array(mssql_query("select tipe, iduser from sys_user_tipe 
																		where tipe = 'DEPT. HEAD FINANCE / DIV. HEAD FAST'" ,$conns));
						
										#-------------- div head + releaser
										$stdevoval = mssql_query("select CONVERT(varchar,tglvalidasi,120) tglvalidasi, validasi, 
																				 uservalidasi, ketvalidasi
																				 from DataEvoVal 
																				 where nobukti='".$nobukti."' and level = 'DIV. HEAD'
																				  and uservalidasi = '".$user_multi['iduser']."'
																				 order by tglentry desc",$conns);
										$dataevoval = mssql_fetch_array($stdevoval);
										$cekevoval = mssql_num_rows($stdevoval);
										
										$ketvalidasi_sect = $dataevoval['ketvalidasi']; 
										$tglvalidasi_sect = $dataevoval['tglvalidasi']; 
										$validasi_sect = $dataevoval['validasi']; 
										$uservalidasi_sect = $dataevoval['uservalidasi']; 
										
										if ($cekevoval>0) {
											$sql6 = "update DataEvoVal set validasi='".$validasi_sect."',uservalidasi='".$uservalidasi_sect."',
														tglvalidasi= '".$tglvalidasi_sect."', ketvalidasi='".$ketvalidasi_sect."', 
														ipentry = '".$ipaddr."', useragent = '".$_SERVER['HTTP_USER_AGENT']."'
														where nobukti = '".$nobukti."' and level = 'DEPT. HEAD FINANCE / DIV. HEAD FAST'";
											$query6 = mssql_query($sql6,$conns);
											
											$query4 = true;
											$query5 = true;
											$insert=true;
											$query3 =  insertAcc($nobukti);
											
											#---------------- kasir
											if ($query3) {
												$sql7 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait) 
														values ('$nobukti','$KodeDealer','KASIR',getdate(), '')";
												$query7 = mssql_query($sql7,$conns);
											}
										}	 else {
											$query4 = true;
											$query5 = true;
											$query6 = true;
											$query7 = true;
										
										}								
									
									
										
									} else {
										$query4 = true;
										$query5 = true;
										$query6 = true;
										$query7 = true;
									}
												
								} else if (trim($user_aju['department'])=='ACCOUNTING') { //????
									
									if ($level=='DEPT. HEAD') {
										$user_multi = mssql_fetch_array(mssql_query("select tipe, iduser from sys_user_tipe 
																		where tipe = 'DEPT. HEAD FINANCE / DIV. HEAD FAST'" ,$conns));
						
										#-------------- div head + releaser
										$stdevoval = mssql_query("select CONVERT(varchar,tglvalidasi,120) tglvalidasi, validasi, 
																 uservalidasi, ketvalidasi
																 from DataEvoVal 
																 where nobukti='".$nobukti."' and level = 'DIV. HEAD'
																  and uservalidasi = '".$user_multi['iduser']."'
																 order by tglentry desc",$conns);
										$dataevoval = mssql_fetch_array($stdevoval);
										$cekevoval = mssql_num_rows($stdevoval);
										
										$ketvalidasi_sect = $dataevoval['ketvalidasi']; 
										$tglvalidasi_sect = $dataevoval['tglvalidasi']; 
										$validasi_sect = $dataevoval['validasi']; 
										$uservalidasi_sect = $dataevoval['uservalidasi']; 
										
										if ($cekevoval>0) {
											$sql6 = "update DataEvoVal set validasi='".$validasi_sect."',uservalidasi='".$uservalidasi_sect."',
														tglvalidasi= '".$tglvalidasi_sect."', ketvalidasi='".$ketvalidasi_sect."', 
														ipentry = '".$ipaddr."', useragent = '".$_SERVER['HTTP_USER_AGENT']."'
														where nobukti = '".$nobukti."' and level = 'DEPT. HEAD FINANCE / DIV. HEAD FAST'";
											$query6 = mssql_query($sql6,$conns);
											
											$query4 = true;
											$query5 = true;
											$insert=true;
											$query3 =  insertAcc($nobukti);
											
											#---------------- kasir
											if ($query3) {
												$sql7 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait) 
														values ('$nobukti','$KodeDealer','KASIR',getdate(), '')";
												$query7 = mssql_query($sql7,$conns);
											}
										}	 else {
											$query4 = true;
											$query5 = true;
											$query6 = true;
											$query7 = true;
										}								
									
									} else {
										$query4 = true;
										$query5 = true;
										$query6 = true;
										$query7 = true;
									}
									
									
								}  else {
									$query4 = true;
									$query5 = true;
									$query6 = true;
									$query7 = true;
								}
								
							}  else {
								$query4 = true;
								$query5 = true;
								$query6 = true;
								$query7 = true;
							}
							
						} else {
							if ($div['totBayar']>$skipdir['skip_direksi']) {
								//$urutan = "2,3,4,5,6,8,9,11";
								/*
								ADMIN
								TAX
								ACCOUNTING
								SECTION HEAD
								DEPT. HEAD
								DIV. HEAD
								DIREKSI
								FINANCE
								DEPT. HEAD FINANCE
								DEPT. HEAD FINANCE / DIV. HEAD FAST
								KASIR
								*/		
										
								if (trim($user_aju['divisi'])=='FINANCE and ACCOUNTING' or trim($user_aju['divisi'])=='all') {	
									if (trim($user_aju['department'])=='FINANCE') {
										
										if ($level=='DIREKSI') {
											$user_multi = mssql_fetch_array(mssql_query("select tipe, iduser from sys_user_tipe 
																			where IdUser = '".$user_aju['idatasan']."' and tipe = 'FINANCE'",$conns ));
							
											$stdevoval = mssql_query("select CONVERT(varchar,tglvalidasi,120) tglvalidasi, validasi, 
																		 uservalidasi, ketvalidasi
																		 from DataEvoVal 
																		 where nobukti='".$nobukti."' and level = 'SECTION HEAD'
																		 and uservalidasi = '".$user_multi['iduser']."'
																		 order by tglentry desc",$conns);
											$dataevoval = mssql_fetch_array($stdevoval);
											$cekevoval = mssql_num_rows($stdevoval);
				
											$ketvalidasi_sect = $dataevoval['ketvalidasi']; 
											$tglvalidasi_sect = $dataevoval['tglvalidasi']; 
											$validasi_sect = $dataevoval['validasi']; 
											$uservalidasi_sect = $dataevoval['uservalidasi']; 
											
											if ($cekevoval>0) {
											
												$sql4 = "update DataEvoVal set validasi='".$validasi_sect."',uservalidasi='".$uservalidasi_sect."',
															tglvalidasi= '".$tglvalidasi_sect."', ketvalidasi='".$ketvalidasi_sect."', 
															ipentry = '".$ipaddr."', useragent = '".$_SERVER['HTTP_USER_AGENT']."'
															where nobukti = '".$nobukti."' and level = 'FINANCE'";
												$query4 = mssql_query($sql4,$conns);
												
												
												#-------------- dept head + releaser
												$user_multi = mssql_fetch_array(mssql_query("select tipe, iduser from sys_user_tipe 
																		where tipe = 'DEPT. HEAD FINANCE'",$conns ));
						
												$stdevoval = mssql_query("select CONVERT(varchar,tglvalidasi,120) tglvalidasi, validasi, 
																		 uservalidasi, ketvalidasi
																		 from DataEvoVal 
																		 where nobukti='".$nobukti."' and level = 'DEPT. HEAD'
																		  and uservalidasi = '".$user_multi['iduser']."'
																		 order by tglentry desc",$conns);
												$dataevoval = mssql_fetch_array($stdevoval);
												$cekevoval = mssql_num_rows($stdevoval);
												
												if ($cekevoval>0) {
													$ketvalidasi_sect = $dataevoval['ketvalidasi']; 
													$tglvalidasi_sect = $dataevoval['tglvalidasi']; 
													$validasi_sect = $dataevoval['validasi']; 
													$uservalidasi_sect = $dataevoval['uservalidasi']; 
													
													$sql5 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait, 
															validasi, uservalidasi, tglvalidasi, ketvalidasi, ipentry, useragent ) 
															values ('$nobukti','$KodeDealer','DEPT. HEAD FINANCE', getdate(), '',
															'$validasi_sect','$uservalidasi_sect', '$tglvalidasi_sect','$ketvalidasi_sect',
															'".$ipaddr."', '".$_SERVER['HTTP_USER_AGENT']."')";
													$query5 = mssql_query($sql5,$conns);	
													
														
													#-------------- div head + releaser
													$user_multi = mssql_fetch_array(mssql_query("select tipe, iduser from sys_user_tipe 
																			where tipe = 'DEPT. HEAD FINANCE / DIV. HEAD FAST'",$conns ));
							
													$stdevoval = mssql_query("select CONVERT(varchar,tglvalidasi,120) tglvalidasi, validasi, 
																			 uservalidasi, ketvalidasi
																			 from DataEvoVal 
																			 where nobukti='".$nobukti."' and level = 'DIV. HEAD'
																			  and uservalidasi = '".$user_multi['iduser']."'
																			 order by tglentry desc",$conns);
													$dataevoval = mssql_fetch_array($stdevoval);
													$cekevoval = mssql_num_rows($stdevoval);
													
													if ($cekevoval>0) {
														$ketvalidasi_sect = $dataevoval['ketvalidasi']; 
														$tglvalidasi_sect = $dataevoval['tglvalidasi']; 
														$validasi_sect = $dataevoval['validasi']; 
														$uservalidasi_sect = $dataevoval['uservalidasi']; 
														
														$sql6 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait, 
																validasi, uservalidasi, tglvalidasi, ketvalidasi, ipentry, useragent ) 
																values ('$nobukti','$KodeDealer','DEPT. HEAD FINANCE / DIV. HEAD FAST', getdate(), '',
																'$validasi_sect','$uservalidasi_sect', '$tglvalidasi_sect','$ketvalidasi_sect',
																'".$ipaddr."', '".$_SERVER['HTTP_USER_AGENT']."')";
														$query6 = mssql_query($sql6,$conns);	
														
														$insert=true;
														$query3 =  insertAcc($nobukti);
														
														#---------------- kasir
														if ($query3) {
															$sql7 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait) 
																	values ('$nobukti','$KodeDealer','KASIR',getdate(), '')";
															$query7 = mssql_query($sql7,$conns);
														}
														
													}   else {
														$query3 = true;
														$query4 = true;
														$query5 = true;
														$query6 = true;
														$query7 = true;
													}								
													
												} else {
													$query4 = true;
													$query5 = true;
													$query6 = true;
													$query7 = true;
												}
												
											}   else {
												$query3 = true;
												$query4 = true;
												$query5 = true;
												$query6 = true;
												$query7 = true;
											}								
											
										
										
										} else if ($level=='FINANCE') {	
											$user_multi = mssql_fetch_array(mssql_query("select tipe, iduser from sys_user_tipe 
																			where tipe = 'DEPT. HEAD FINANCE'",$conns ));
							
											$stdevoval = mssql_query("select CONVERT(varchar,tglvalidasi,120) tglvalidasi, validasi, 
																		 uservalidasi, ketvalidasi
																		 from DataEvoVal 
																		 where nobukti='".$nobukti."' and level = 'DEPT. HEAD'
																		 and uservalidasi = '".$user_multi['iduser']."'
																		 order by tglentry desc",$conns);
											$dataevoval = mssql_fetch_array($stdevoval);
											$cekevoval = mssql_num_rows($stdevoval);
				
											$ketvalidasi_sect = $dataevoval['ketvalidasi']; 
											$tglvalidasi_sect = $dataevoval['tglvalidasi']; 
											$validasi_sect = $dataevoval['validasi']; 
											$uservalidasi_sect = $dataevoval['uservalidasi']; 
											
											if ($cekevoval>0) {
												$query4 = true;	
												
												if ($cekevoval>0) {
													#---------------- releaser 1 
													$sql5 = "update DataEvoVal set validasi='".$validasi_sect."',uservalidasi='".$uservalidasi_sect."',
															tglvalidasi= '".$tglvalidasi_sect."', ketvalidasi='".$ketvalidasi_sect."', 
															ipentry = '".$ipaddr."', useragent = '".$_SERVER['HTTP_USER_AGENT']."'
															where nobukti = '".$nobukti."' and level = 'DEPT. HEAD FINANCE'";
													$query5 = mssql_query($sql5,$conns);
														
																											
													#-------------- div head + releaser
													$user_multi = mssql_fetch_array(mssql_query("select tipe, iduser from sys_user_tipe 
																			where tipe = 'DEPT. HEAD FINANCE / DIV. HEAD FAST'",$conns ));
												
													$stdevoval = mssql_query("select CONVERT(varchar,tglvalidasi,120) tglvalidasi, validasi, 
																				 uservalidasi, ketvalidasi
																				 from DataEvoVal 
																				 where nobukti='".$nobukti."' and level = 'DIV. HEAD'
																				 and uservalidasi = '".$user_multi['iduser']."'
																				 order by tglentry desc",$conns);
													$dataevoval = mssql_fetch_array($stdevoval);
													$cekevoval = mssql_num_rows($stdevoval);
						
													if ($cekevoval>0) {
														$ketvalidasi_sect = $dataevoval['ketvalidasi']; 
														$tglvalidasi_sect = $dataevoval['tglvalidasi']; 
														$validasi_sect = $dataevoval['validasi']; 
														$uservalidasi_sect = $dataevoval['uservalidasi']; 
														
														$sql6 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait, 
																validasi, uservalidasi, tglvalidasi, ketvalidasi, ipentry, useragent ) 
																values ('$nobukti','$KodeDealer','DEPT. HEAD FINANCE / DIV. HEAD FAST', getdate(), '',
																'$validasi_sect','$uservalidasi_sect', '$tglvalidasi_sect','$ketvalidasi_sect',
															'".$ipaddr."', '".$_SERVER['HTTP_USER_AGENT']."')";
														$query6 = mssql_query($sql6,$conns);	
														
														$insert=true;
														$query3 =  insertAcc($nobukti);
														
														#---------------- kasir
														if ($query3) {
															$sql7 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait) 
																	values ('$nobukti','$KodeDealer','KASIR',getdate(), '')";
															$query7 = mssql_query($sql7,$conns);
														}
													
													}   else {
														$query3 = true;
														$query4 = true;
														$query5 = true;
														$query6 = true;
														$query7 = true;
													}	
													
												}   else {
													$query3 = true;
													$query4 = true;
													$query5 = true;
													$query6 = true;
													$query7 = true;
												}	
																			
											} else {
												$query4 = true;
												$query5 = true;
												$query6 = true;
												$query7 = true;
											}
										
										// --- OK	
										}  else if ($level=='DEPT. HEAD') {
											$user_multi = mssql_fetch_array(mssql_query("select tipe, iduser from sys_user_tipe 
																			where tipe = 'DEPT. HEAD FINANCE / DIV. HEAD FAST'" ,$conns));
							
											#-------------- div head + releaser
											$stdevoval = mssql_query("select CONVERT(varchar,tglvalidasi,120) tglvalidasi, validasi, 
																					 uservalidasi, ketvalidasi
																					 from DataEvoVal 
																					 where nobukti='".$nobukti."' and level = 'DIV. HEAD'
																					  and uservalidasi = '".$user_multi['iduser']."'
																					 order by tglentry desc",$conns);
											$dataevoval = mssql_fetch_array($stdevoval);
											$cekevoval = mssql_num_rows($stdevoval);
											
											$ketvalidasi_sect = $dataevoval['ketvalidasi']; 
											$tglvalidasi_sect = $dataevoval['tglvalidasi']; 
											$validasi_sect = $dataevoval['validasi']; 
											$uservalidasi_sect = $dataevoval['uservalidasi']; 
											
											if ($cekevoval>0) {
												$sql6 = "update DataEvoVal set validasi='".$validasi_sect."',uservalidasi='".$uservalidasi_sect."',
															tglvalidasi= '".$tglvalidasi_sect."', ketvalidasi='".$ketvalidasi_sect."', 
															ipentry = '".$ipaddr."', useragent = '".$_SERVER['HTTP_USER_AGENT']."'
															where nobukti = '".$nobukti."' and level = 'DEPT. HEAD FINANCE / DIV. HEAD FAST'";
												$query6 = mssql_query($sql6,$conns);
												
												$query4 = true;
												$query5 = true;
												$insert=true;
												$query3 =  insertAcc($nobukti);
												
												#---------------- kasir
												if ($query3) {
													$sql7 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait) 
															values ('$nobukti','$KodeDealer','KASIR',getdate(), '')";
													$query7 = mssql_query($sql7,$conns);
												}
											}	 else {
												$query4 = true;
												$query5 = true;
												$query6 = true;
												$query7 = true;
											}							
										
										} else {
											$query4 = true;
											$query5 = true;
											$query6 = true;
											$query7 = true;
										}
									
									} else if (trim($user_aju['department'])=='ACCOUNTING') { //????
									
										if ($level=='DEPT. HEAD') {
											$user_multi = mssql_fetch_array(mssql_query("select tipe, iduser from sys_user_tipe 
																			where tipe = 'DEPT. HEAD FINANCE / DIV. HEAD FAST'" ,$conns));
							
											#-------------- div head + releaser
											$stdevoval = mssql_query("select CONVERT(varchar,tglvalidasi,120) tglvalidasi, validasi, 
																		 uservalidasi, ketvalidasi
																		 from DataEvoVal 
																		 where nobukti='".$nobukti."' and level = 'DIV. HEAD'
																		  and uservalidasi = '".$user_multi['iduser']."'
																		 order by tglentry desc",$conns);
											$dataevoval = mssql_fetch_array($stdevoval);
											$cekevoval = mssql_num_rows($stdevoval);
											
											$ketvalidasi_sect = $dataevoval['ketvalidasi']; 
											$tglvalidasi_sect = $dataevoval['tglvalidasi']; 
											$validasi_sect = $dataevoval['validasi']; 
											$uservalidasi_sect = $dataevoval['uservalidasi']; 
											
											if ($cekevoval>0) {
												$sql6 = "update DataEvoVal set validasi='".$validasi_sect."',uservalidasi='".$uservalidasi_sect."',
															tglvalidasi= '".$tglvalidasi_sect."', ketvalidasi='".$ketvalidasi_sect."', 
															ipentry = '".$ipaddr."', useragent = '".$_SERVER['HTTP_USER_AGENT']."'
															where nobukti = '".$nobukti."' and level = 'DEPT. HEAD FINANCE / DIV. HEAD FAST'";
												$query6 = mssql_query($sql6,$conns);
												
												$query4 = true;
												$query5 = true;
												$insert=true;
												$query3 =  insertAcc($nobukti);
												
												#---------------- kasir
												if ($query3) {
													$sql7 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait) 
															values ('$nobukti','$KodeDealer','KASIR',getdate(), '')";
													$query7 = mssql_query($sql7,$conns);
												}
											}	 else {
												$query4 = true;
												$query5 = true;
												$query6 = true;
												$query7 = true;
											}								
										
										} else {
											$query4 = true;
											$query5 = true;
											$query6 = true;
											$query7 = true;
										}
										
									}  else {
										$query4 = true;
										$query5 = true;
										$query6 = true;
										$query7 = true;
									}
									
								}  else {
									$query4 = true;
									$query5 = true;
									$query6 = true;
									$query7 = true;
								}
								
							} else {
								//$urutan = "2,3,4,5,8,9,11";
								
								/*ADMIN
								TAX
								ACCOUNTING
								SECTION HEAD
								DEPT. HEAD
								DIV. HEAD
								FINANCE
								DEPT. HEAD FINANCE
								DEPT. HEAD FINANCE / DIV. HEAD FAST
								KASIR
								*/
								if (trim($user_aju['divisi'])=='FINANCE and ACCOUNTING' or trim($user_aju['divisi'])=='all') {	
									if (trim($user_aju['department'])=='FINANCE') {
									
										// --- DIV HEAD
										if ($level=='DIV. HEAD') {	
											$user_multi = mssql_fetch_array(mssql_query("select tipe, iduser from sys_user_tipe 
																			where IdUser = '".$user_aju['idatasan']."' and tipe = 'FINANCE'",$conns ));
							
											$stdevoval = mssql_query("select CONVERT(varchar,tglvalidasi,120) tglvalidasi, validasi, 
																		 uservalidasi, ketvalidasi
																		 from DataEvoVal 
																		 where nobukti='".$nobukti."' and level = 'SECTION HEAD'
																		 and uservalidasi = '".$user_multi['iduser']."'
																		 order by tglentry desc",$conns);
											$dataevoval = mssql_fetch_array($stdevoval);
											$cekevoval = mssql_num_rows($stdevoval);
				
											$ketvalidasi_sect = $dataevoval['ketvalidasi']; 
											$tglvalidasi_sect = $dataevoval['tglvalidasi']; 
											$validasi_sect = $dataevoval['validasi']; 
											$uservalidasi_sect = $dataevoval['uservalidasi']; 
											
											if ($cekevoval>0) {
											
												#---------------------- fincek
												$sql4 = "update DataEvoVal set validasi='".$validasi_sect."',uservalidasi='".$uservalidasi_sect."',
															tglvalidasi= '".$tglvalidasi_sect."', ketvalidasi='".$ketvalidasi_sect."', 
															ipentry = '".$ipaddr."', useragent = '".$_SERVER['HTTP_USER_AGENT']."'
															where nobukti = '".$nobukti."' and level = 'FINANCE'";
												$query4 = mssql_query($sql4,$conns);	
												
												
												$user_multi = mssql_fetch_array(mssql_query("select tipe, iduser from sys_user_tipe 
																			where tipe = 'DEPT. HEAD FINANCE'",$conns ));
							
												$stdevoval = mssql_query("select CONVERT(varchar,tglvalidasi,120) tglvalidasi, validasi, 
																		 uservalidasi, ketvalidasi
																		 from DataEvoVal 
																		 where nobukti='".$nobukti."' and level = 'DEPT. HEAD'
																		 and uservalidasi = '".$user_multi['iduser']."'
																		 order by tglentry desc",$conns);
												$dataevoval = mssql_fetch_array($stdevoval);
												$cekevoval = mssql_num_rows($stdevoval);
					
												$ketvalidasi_sect = $dataevoval['ketvalidasi']; 
												$tglvalidasi_sect = $dataevoval['tglvalidasi']; 
												$validasi_sect = $dataevoval['validasi']; 
												$uservalidasi_sect = $dataevoval['uservalidasi']; 
												
												if ($cekevoval>0) {
													#---------------- releaser 1 
													$sql5 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait, 
															validasi, uservalidasi, tglvalidasi, ketvalidasi, ipentry, useragent ) 
															values ('$nobukti','$KodeDealer','DEPT. HEAD FINANCE', getdate(), '',
															'$validasi_sect','$uservalidasi_sect', '$tglvalidasi_sect','$ketvalidasi_sect',
															'".$ipaddr."', '".$_SERVER['HTTP_USER_AGENT']."')";
													$query5 = mssql_query($sql5,$conns);
																										
													#-------------- div head + releaser
													$sql6 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait, 
															validasi, uservalidasi, tglvalidasi, ketvalidasi, ipentry, useragent ) 
															values ('$nobukti','$KodeDealer','DEPT. HEAD FINANCE / DIV. HEAD FAST', getdate(), '',
															'$val','$IdUser', getdate(),'$ketvalidasi',
														'".$ipaddr."', '".$_SERVER['HTTP_USER_AGENT']."')";
													$query6 = mssql_query($sql6,$conns);	
													
													$insert=true;
													$query3 =  insertAcc($nobukti);
													
													#---------------- kasir
													if ($query3) {
														$sql7 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait) 
																values ('$nobukti','$KodeDealer','KASIR',getdate(), '')";
														$query7 = mssql_query($sql7,$conns);
													}
													
												}   else {
													$query3 = true;
													$query4 = true;
													$query5 = true;
													$query6 = true;
													$query7 = true;
												}	
																			
											} else {
												$query4 = true;
												$query5 = true;
												$query6 = true;
												$query7 = true;
											}
										
										// --- FINCEK	
										} else if ($level=='FINANCE') {	
											$user_multi = mssql_fetch_array(mssql_query("select tipe, iduser from sys_user_tipe 
																			where tipe = 'DEPT. HEAD FINANCE'",$conns ));
							
											$stdevoval = mssql_query("select CONVERT(varchar,tglvalidasi,120) tglvalidasi, validasi, 
																		 uservalidasi, ketvalidasi
																		 from DataEvoVal 
																		 where nobukti='".$nobukti."' and level = 'DEPT. HEAD'
																		 and uservalidasi = '".$user_multi['iduser']."'
																		 order by tglentry desc",$conns);
											$dataevoval = mssql_fetch_array($stdevoval);
											$cekevoval = mssql_num_rows($stdevoval);
				
											$ketvalidasi_sect = $dataevoval['ketvalidasi']; 
											$tglvalidasi_sect = $dataevoval['tglvalidasi']; 
											$validasi_sect = $dataevoval['validasi']; 
											$uservalidasi_sect = $dataevoval['uservalidasi']; 
											
											if ($cekevoval>0) {
												$query4 = true;	
												
												if ($cekevoval>0) {
													#---------------- releaser 1 
													$sql5 = "update DataEvoVal set validasi='".$validasi_sect."',uservalidasi='".$uservalidasi_sect."',
															tglvalidasi= '".$tglvalidasi_sect."', ketvalidasi='".$ketvalidasi_sect."', 
															ipentry = '".$ipaddr."', useragent = '".$_SERVER['HTTP_USER_AGENT']."'
															where nobukti = '".$nobukti."' and level = 'DEPT. HEAD FINANCE'";
													$query5 = mssql_query($sql5,$conns);
														
																										
													#-------------- div head + releaser
													$user_multi = mssql_fetch_array(mssql_query("select tipe, iduser from sys_user_tipe 
																			where tipe = 'DEPT. HEAD FINANCE / DIV. HEAD FAST'",$conns ));
												
													$stdevoval = mssql_query("select CONVERT(varchar,tglvalidasi,120) tglvalidasi, validasi, 
																				 uservalidasi, ketvalidasi
																				 from DataEvoVal 
																				 where nobukti='".$nobukti."' and level = 'DIV. HEAD'
																				 and uservalidasi = '".$user_multi['iduser']."'
																				 order by tglentry desc",$conns);
													$dataevoval = mssql_fetch_array($stdevoval);
													$cekevoval = mssql_num_rows($stdevoval);
						
													if ($cekevoval>0) {
														$ketvalidasi_sect = $dataevoval['ketvalidasi']; 
														$tglvalidasi_sect = $dataevoval['tglvalidasi']; 
														$validasi_sect = $dataevoval['validasi']; 
														$uservalidasi_sect = $dataevoval['uservalidasi']; 
														
														$sql6 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait, 
																validasi, uservalidasi, tglvalidasi, ketvalidasi, ipentry, useragent ) 
																values ('$nobukti','$KodeDealer','DEPT. HEAD FINANCE / DIV. HEAD FAST', getdate(), '',
																'$val','$uservalidasi_sect', '$tglvalidasi_sect','$ketvalidasi_sect',
															'".$ipaddr."', '".$_SERVER['HTTP_USER_AGENT']."')";
														$query6 = mssql_query($sql6,$conns);	
														
														$insert=true;
														$query3 =  insertAcc($nobukti);
														
														#---------------- kasir
														if ($query3) {
															$sql7 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait) 
																	values ('$nobukti','$KodeDealer','KASIR',getdate(), '')";
															$query7 = mssql_query($sql7,$conns);
														}
														
													}  else {
														$query3 = true;
														$query4 = true;
														$query5 = true;
														$query6 = true;
														$query7 = true;
													}	
													
													
												} else {
													$query3 = true;
													$query4 = true;
													$query5 = true;
													$query6 = true;
													$query7 = true;
												}	
																			
											} else {
												$query4 = true;
												$query5 = true;
												$query6 = true;
												$query7 = true;
											}
										
										// --- OK	
										} else if ($level=='DEPT. HEAD') {	
											$query4 = true;	
											$query5 = true;
													
											#-------------- div head + releaser
											$user_multi = mssql_fetch_array(mssql_query("select tipe, iduser from sys_user_tipe 
																	where tipe = 'DEPT. HEAD FINANCE / DIV. HEAD FAST'",$conns ));
										
											$stdevoval = mssql_query("select CONVERT(varchar,tglvalidasi,120) tglvalidasi, validasi, 
																		 uservalidasi, ketvalidasi
																		 from DataEvoVal 
																		 where nobukti='".$nobukti."' and level = 'DIV. HEAD'
																		 and uservalidasi = '".$user_multi['iduser']."'
																		 order by tglentry desc",$conns);
											$dataevoval = mssql_fetch_array($stdevoval);
											$cekevoval = mssql_num_rows($stdevoval);
				
											if ($cekevoval>0) {
												$ketvalidasi_sect = $dataevoval['ketvalidasi']; 
												$tglvalidasi_sect = $dataevoval['tglvalidasi']; 
												$validasi_sect = $dataevoval['validasi']; 
												$uservalidasi_sect = $dataevoval['uservalidasi']; 
												
												$sql6 = "update DataEvoVal set validasi='".$validasi_sect."',uservalidasi='".$uservalidasi_sect."',
															tglvalidasi= '".$tglvalidasi_sect."', ketvalidasi='".$ketvalidasi_sect."', 
															ipentry = '".$ipaddr."', useragent = '".$_SERVER['HTTP_USER_AGENT']."'
															where nobukti = '".$nobukti."' and level = 'DEPT. HEAD FINANCE / DIV. HEAD FAST'";
												$query6 = mssql_query($sql6,$conns);	
												
												$insert=true;
												$query3 =  insertAcc($nobukti);
												
												#---------------- kasir
												if ($query3) {
													$sql7 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait) 
															values ('$nobukti','$KodeDealer','KASIR',getdate(), '')";
													$query7 = mssql_query($sql7,$conns);
												}
												
											} else {
												$query4 = true;
												$query5 = true;
												$query6 = true;
												$query7 = true;
											}
										
										// --- OK	
										} else {
											$query4 = true;
											$query5 = true;
											$query6 = true;
											$query7 = true;
										}
										
									} else if (trim($user_aju['department'])=='ACCOUNTING') {
									
										// --- releaser 1
										if ($level=='DEPT. HEAD') {	
											$query4 = true;
											$query5 = true;
											
											$user_multi = mssql_fetch_array(mssql_query("select tipe, iduser from sys_user_tipe 
																			where tipe = 'DEPT. HEAD FINANCE / DIV. HEAD FAST'",$conns ));
												
											$stdevoval = mssql_query("select CONVERT(varchar,tglvalidasi,120) tglvalidasi, validasi, 
																	 uservalidasi, ketvalidasi
																	 from DataEvoVal 
																	 where nobukti='".$nobukti."' and level = 'DIV. HEAD'
																	 and uservalidasi = '".$user_multi['iduser']."'
																	 order by tglentry desc",$conns);
											$dataevoval = mssql_fetch_array($stdevoval);
											$cekevoval = mssql_num_rows($stdevoval);
				
											$ketvalidasi_sect = $dataevoval['ketvalidasi']; 
											$tglvalidasi_sect = $dataevoval['tglvalidasi']; 
											$validasi_sect = $dataevoval['validasi']; 
											$uservalidasi_sect = $dataevoval['uservalidasi']; 
											
											if ($cekevoval>0) {
												#-------------- div head + releaser
												$sql6 = "update DataEvoVal set validasi='".$validasi_sect."',uservalidasi='".$uservalidasi_sect."',
														tglvalidasi= '".$tglvalidasi_sect."', ketvalidasi='".$ketvalidasi_sect."', 
														ipentry = '".$ipaddr."', useragent = '".$_SERVER['HTTP_USER_AGENT']."'
														where nobukti = '".$nobukti."' and level = 'DEPT. HEAD FINANCE / DIV. HEAD FAST'";
												$query6 = mssql_query($sql6,$conns);	
												
												$insert=true;
												$query3 =  insertAcc($nobukti);
												
												#---------------- kasir
												if ($query3) {
													$sql7 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait) 
															values ('$nobukti','$KodeDealer','KASIR',getdate(), '')";
													$query7 = mssql_query($sql7,$conns);
												}
												
											
											} else {
												$query4 = true;
												$query5 = true;
												$query6 = true;
												$query7 = true;
											}
										
										// --- FINCEK	
										} else {
											$query4 = true;
											$query5 = true;
											$query6 = true;
											$query7 = true;
										}
																					
									} else {
										$query4 = true;
										$query5 = true;
										$query6 = true;
										$query7 = true;
									}
								
								} else {
									$query4 = true;
									$query5 = true;
									$query6 = true;
									$query7 = true;
								}
								
							}
						}
																	
					}
					
				} else {
					
				}
				
				
			}
			
			#echo "<pre>".$sql1."</pre>";	
			#echo "<pre>".$sql2."</pre>";	
			
			if ($query1) {
			
				if ($query2) {
				
					if ($query3) {
					
						if ($query4) {
					
							if ($query5) {
					
								if ($query6) {
								
									if ($query7) {
						
										mssql_query("COMMIT TRAN",$conns);
										
										if ($val=='Accept') {
											// $sql = "
											// 	select c.nik,c.email,c.no_tlp,c.namaUser as validator,d.namaUser as pengaju,
											// 	d.department,e.NamaDealer,b.tipe,tgl_pengajuan,metode_bayar,a.nobukti,namaVendor,keterangan,
											// 	case when b.tipe = 'HUTANG' then htg_stl_pajak else biaya_yg_dibyar end as nominal
											// 	from DataEvoVal a
											// 	inner join DataEvo b on a.nobukti=b.nobukti
											// 	inner join sys_user c on c.tipe = a.level and (c.divisi=b.divisi or c.divisi='all') 
											// 	inner join sys_user d on d.IdUser = b.userentry
											// 	inner join SPK00..dodealer e on b.kodedealer = e.kodedealer
											// 	where a.nobukti = '".$nobukti."' and ISNULL(validasi, '')='' 
											// 	and c.IdUser = case when (a.level='SECTION HEAD' or a.level='ADH') then b.IdAtasan else c.IdUser end
											// ";
											$sql = "
												select namaUser as pengaju,department,a.nobukti,NamaDealer,a.tipe,tgl_pengajuan,metode_bayar,namaVendor,keterangan,
												case when a.tipe = 'HUTANG' then htg_stl_pajak else biaya_yg_dibyar end as nominal,a.IdAtasan,a.kodedealer,a.divisi,
												isnull(a.deptterkait,'') deptterkait
												from DataEvo a inner join sys_user b on b.IdUser = a.userentry
												inner join SPK00..dodealer c on a.kodedealer = c.kodedealer
												where nobukti = '".$nobukti."'
											";
											$dt = mssql_fetch_array(mssql_query($sql,$conns));
											
											$sqlterkait = "select top 1 isnull(deptterkait,'') deptterkait, level, kodedealer
															from DataEvoval
															where nobukti = '".$nobukti."'
															order by idval desc";
											$dtterkait = mssql_fetch_array(mssql_query($sqlterkait,$conns));
											$nextlevel_notif = $dtterkait['level'];
											
											if (!empty($dtterkait['deptterkait']) and str_replace(" ","",$dtterkait['deptterkait'])!='') {
												
												if ($nextlevel_notif=='SECTION HEAD') {
													$s_section = "and kodedealer='".$dt['kodedealer']."' and tipe='".$nextlevel_notif."' and department='".$dt['deptterkait']."' 
																	and IdUser in (select IdUser from DeptTerkait)  ";
												
												} else if ($nextlevel_notif=='DEPT. HEAD FINANCE / DIV. HEAD FAST') {
													$s_section = "and kodedealer='".$dt['kodedealer']."' and (tipe='DIV. HEAD' or tipe='DEPT. HEAD') and divisi='FINANCE and ACCOUNTING' 
														and (department='FINANCE' or ISNULL(department, '')='') and IdUser in (select IdUser from DeptTerkait) ";
												
												} else if ($nextlevel_notif=='DEPT. HEAD FINANCE') {
													$s_section = "and kodedealer='".$dt['kodedealer']."' and  tipe='DEPT. HEAD' and divisi='FINANCE and ACCOUNTING' 
														and (department='FINANCE' or ISNULL(department, '')='') and IdUser in (select IdUser from DeptTerkait) ";
												
												} else if ($nextlevel_notif=='OM') {
													$s_section = "and kodedealer='all' and tipe='OM' and IdUser in (select IdUser from DeptTerkait) ";
												
												} else if ($nextlevel_notif=='ACCOUNTING') {
													$s_section = "and kodedealer='".$dt['kodedealer']."' and tipe='".$nextlevel_notif."' ";
													
												} else {
													if ($dtterkait['kodedealer']=='2010') {
														$s_section = "
															and kodedealer='".$dt['kodedealer']."' and tipe='".$nextlevel_notif."' and department='".$dt['deptterkait']."' and IdUser in (select IdUser from DeptTerkait) ";
													} else {
														$s_section = "and kodedealer='".$dt['kodedealer']."' and tipe='".$nextlevel_notif."' and department='".$dt['deptterkait']."'  and IdUser in (select IdUser from DeptTerkait) ";
													}
												}
												
												
											} else {
												if ($nextlevel_notif=='SECTION HEAD') {
													$s_section = "and kodedealer='".$dt['kodedealer']."' and tipe='".$nextlevel_notif."' and idUser = '".$dt['IdAtasan']."'";
												
												} else if ($nextlevel_notif=='DEPT. HEAD FINANCE / DIV. HEAD FAST') {
													$s_section = "and kodedealer='".$dt['kodedealer']."' and (tipe='DIV. HEAD' or tipe='DEPT. HEAD') and divisi='FINANCE and ACCOUNTING' 
														and (department='FINANCE' or ISNULL(department, '')='')";
												
												} else if ($nextlevel_notif=='DEPT. HEAD FINANCE') {
													$s_section = "and kodedealer='".$dt['kodedealer']."' and  tipe='DEPT. HEAD' and divisi='FINANCE and ACCOUNTING' 
														and (department='FINANCE' or ISNULL(department, '')='')";
												
												} else if ($nextlevel_notif=='OM') {
													$s_section = "and kodedealer='all' and tipe='OM'";
												
												} else if ($nextlevel_notif=='ACCOUNTING') {
													$s_section = "and kodedealer='".$dt['kodedealer']."' and tipe='".$nextlevel_notif."' ";
												
												} else if ($nextlevel_notif=='DIREKSI' or $nextlevel_notif =='DIREKSI 2') {
													$s_section = "and kodedealer='".$dt['kodedealer']."' and tipe='".$nextlevel_notif."'";
												
												} else {
													if ($dtterkait['kodedealer']=='2010') {
														if ($nextlevel_notif=='DIV. HEAD') {
															$s_section = "
																and kodedealer='".$dt['kodedealer']."' and tipe='".$nextlevel_notif."' 
																and (divisi='".$dt['divisi']."' or divisi='all') 
																and (department='".$dt['department']."' or ISNULL(department, '')='' or department='all')";
														} else {
															$s_section = "
																and kodedealer='".$dt['kodedealer']."' and tipe='".$nextlevel_notif."' 
																and (divisi='".$dt['divisi']."' or divisi='all') 
																and (department='".$dt['department']."' or ISNULL(department, '')='')";
														}
													} else {
														$s_section = "and kodedealer='".$dt['kodedealer']."' and tipe='".$nextlevel_notif."' and department='".$dt['department']."'";
													}
												}
												
											} 
											
											
											$sql2 = "select nik,email,no_tlp,namaUser as validator from sys_user where ISNULL(isDel,'')='' and idstatus = '1' $s_section";
											$rvld = mssql_query($sql2,$conns);
											$bodyIntra = ""; $nik=""; $email = ""; $no_tlp = ""; $bodyWa = "";
											
											while ($vld = mssql_fetch_array($rvld)) {
												$bodyIntra .= 'Kepada Yth. Bp/Ibu '.$vld['validator'].', ';
												$bodyIntra .= 'Kami informasikan permohonan Validasi Voucher Payment atas: ';
												$bodyIntra .= 'Nama Pengaju:'.$dt['pengaju'].', ';
												$bodyIntra .= 'Department: '.$dt['department'].', ';
												$bodyIntra .= 'Nomor Tagihan: '.$dt['nobukti'].', ';
												$bodyIntra .= 'Terimakasih untuk kerjasamanya.;';
							
												$bodyWa .= 'Mohon Validasi Voucher Payment '.$dt['nobukti'].' tanggal '.date('d/m/Y', strtotime($dt['tgl_pengajuan'])).', ';
												$bodyWa .= 'dari '.strtoupper($dt['pengaju']).' ('.$dt['department'].' Department '.$dt['NamaDealer'].'), ';
												$bodyWa .= 'guna membayar tagihan '.$dt['namaVendor'].', sebesar: '.number_format($dt['nominal'],0,",",".").', untuk '.$dt['keterangan'].'. Untuk melakukan validasi silahkan klik link http://evopay.nasmoco.net . Terima kasih.';
							
												$nik .= $vld['nik'].";";
												$email .= $vld['email'].";";
												$no_tlp .= $vld['no_tlp'].";";
											}
							
											$n_bodyIntra 	= substr($bodyIntra, 0, -1);
											$n_nik 			= substr($nik, 0, -1);
											$n_email 		= substr($email, 0, -1);
											$n_no_tlp 		= substr($no_tlp, 0, -1);
											$n_bodyWa 		= substr($bodyWa, 0, -1);
											$pesan .= "Transaksi telah berhasil di ".$val."!#".$dt['nobukti']."#".$n_bodyIntra."#".$n_nik."#".$n_email."#".$n_no_tlp."#".$n_bodyWa;
											
										} else {
											$pesan .= "Transaksi telah berhasil di ".$val."!";
										}
						
									}else{
										$pesan .= "0#Maaf, gagal melakukan validasi, gagal proses kasir";
										mssql_query("ROLLBACK TRAN",$conns);
									}
								}else{
									$pesan .= "0#Maaf, gagal melakukan validasi 6";
									mssql_query("ROLLBACK TRAN",$conns);
								}

							}else{
								$pesan .= "0#Maaf, gagal melakukan validasi 5";
								mssql_query("ROLLBACK TRAN",$conns);
							}

						}else{
							$pesan .= "0#Maaf, gagal melakukan validasi 4";
							mssql_query("ROLLBACK TRAN",$conns);
						}	
							
					}else{
						$pesan .= "0#Maaf, gagal input ke jurnal!";
						mssql_query("ROLLBACK TRAN",$conns);
					}
					
				} else {
					$pesan .= "0#Maaf, gagal melakukan validasi 2";
					mssql_query("ROLLBACK TRAN",$conns);
				}						
				
				
			} else {
			
				/*if (!$query1) { $pesan .= "0#Failed Query 1!<br/>".$sql1; }
				if (!$query2) { $pesan .= "0#Failed Query 2!<br/>".$sql2; }
				if (!$query3) { $pesan .= "0#Failed Query 3!<br/>".$query3; }				
				//if (!$query3) { $pesan .= "0#Failed Query 3!<br/>".$query3; }
				*/
				$pesan = "Maaf, gagal melakukan validasi 1";
				mssql_query("ROLLBACK TRAN",$conns);
			}
			
		mssql_query("return",$conns);
		echo $pesan;
		
	} else if ($action=='edit-hutang') {
		$IdUser = addslashes($_REQUEST['IdUser']);
		//$level = addslashes($_REQUEST['level']);
		$level = addslashes($_SESSION['level']);
		
		$KodeDealer = addslashes($_REQUEST['KodeDealer']);
		$Tipe = addslashes($_REQUEST['Tipe']);
		$nobukti = addslashes("VP".$_REQUEST['nobukti']);
		$tgl_bayar = addslashes($_REQUEST['tgl_bayar']);
		$trfPajak = addslashes($_REQUEST['trfPajak']);
		$htg_stl_pajak = str_replace(".", "", $_REQUEST['htg_stl_pajak']);
		$keterangan = addslashes($_REQUEST['keterangan']);
		
		$ppn = str_replace(".", "", $_REQUEST['ppn']);
		$dpp = str_replace(".", "", $_REQUEST['dpp']);
		$tipeppn = addslashes($_REQUEST['tipeppn']);
		$tipematerai = addslashes($_REQUEST['tipe_materai']);
		$materai = str_replace(".", "", $_REQUEST['nominal_materai']);
		
		$pesan = "";
		mssql_query("BEGIN TRAN");
			$r = explode("_cn_", $trfPajak);
			mssql_query("delete from DataEvoPos where nobukti = '".$nobukti."'");
			for ($i=0; $i < count($r); $i++) { 
				$s = explode("#", $r[$i]);
				$nominal = str_replace(".", "", $s[0]);
				$jns_pph = $s[1];
				$tarif_persen = $s[2];
				$nilai_pph = str_replace(".", "", $s[3]);
				$akun_pph = $s[4];
				$keteranganAKun = $s[5];
				if (!empty($nominal)) {
					$sql = "insert into DataEvoPos (nobukti,nominal,jns_pph,tarif_persen,nilai_pph,akun_pph,keteranganAkun) 
						values ('$nobukti','$nominal','$jns_pph','$tarif_persen','$nilai_pph','$akun_pph', '$keteranganAKun')";
					$query1 = mssql_query($sql,$conns);
				}
			}

			if ($level=='ACCOUNTING' or $level=='TAX') {
				$sql2 = "update DataEvo set htg_stl_pajak='".$htg_stl_pajak."',
						dpp = '".$dpp."' , ppn = '".$ppn."' , tipeppn = '".$tipeppn."', tipematerai = '".$tipematerai."', materai = '".$materai."'
						where nobukti = '".$nobukti."'";
				$query2 = mssql_query($sql2);
			} else if ($level=='ADH') {
				$sql2 = "update DataEvo set tgl_bayar='".$tgl_bayar."', keterangan='".$keterangan."',
					htg_stl_pajak='".$htg_stl_pajak."',
					dpp = '".$dpp."' , ppn = '".$ppn."' , tipeppn = '".$tipeppn."', tipematerai = '".$tipematerai."', materai = '".$materai."'
					 where nobukti =".$nobukti." ''";
				$query2 = mssql_query($sql2);
			}
			if ($query1 && $query2) {
				mssql_query("COMMIT TRAN");
				$pesan .= "Data Save!!";
			} else {
				if (!$query1) { $pesan .= "0#Failed Query 1!<br/>".$sql1; }
				if (!$query2) { $pesan .= "0#Failed Query 2!<br/>".$sql2; }
				mssql_query("ROLLBACK TRAN");
			}
		mssql_query("return");
		echo $pesan;
		
	} else if ($action=='edit-biaya') {
		$IdUser = addslashes($_REQUEST['IdUser']);
		//$level = addslashes($_REQUEST['level']);
		$level = addslashes($_SESSION['level']);
		
		$KodeDealer = addslashes($_REQUEST['KodeDealer']);
		$Tipe = addslashes($_REQUEST['Tipe']);
		$nobukti = addslashes("VP".$_REQUEST['nobukti']);
		$tgl_bayar = addslashes($_REQUEST['tgl_bayar']);
		$posbiaya = addslashes($_REQUEST['posbiaya']);
		$keterangan = addslashes($_REQUEST['keterangan']);
		$total_dpp = str_replace(".", "", $_REQUEST['total_dpp']);
		$biaya_yg_dibyar = str_replace(".", "", $_REQUEST['biaya_yg_dibyar']);
		$ppn = str_replace(".", "", $_REQUEST['ppn']);
		$dpp = str_replace(".", "", $_REQUEST['dpp']);
		$is_ppn = addslashes($_REQUEST['is_ppn']);

		mssql_query("BEGIN TRAN");
			$r = explode("_cn_", $posbiaya);
			mssql_query("delete from DataEvoPos where nobukti = '".$nobukti."'");
			for ($i=0; $i < count($r); $i++) { 
				$s = explode("#", $r[$i]);
				$pos_biaya = $s[0];
				$nominal = str_replace(".", "", $s[1]);
				$jns_pph = $s[2];
				$tarif_persen = $s[3];
				$nilai_pph = str_replace(".", "", $s[4]);
				$ketAkun = $s[5];
				$akun_pph = $s[6];
				$keteranganAKun = $s[7];
			
				if (!empty($pos_biaya) and !empty($nominal)) {
					$sql = "insert into DataEvoPos (nobukti,pos_biaya,nominal,jns_pph,tarif_persen,nilai_pph,ketAkun,akun_pph,keteranganAkun) 
						values ('$nobukti','$pos_biaya','$nominal','$jns_pph','$tarif_persen','$nilai_pph','$ketAkun','$akun_pph', '$keteranganAKun')";
					$query1 = mssql_query($sql,$conns);
				}
			}

			if ($level=='ACCOUNTING' or $level=='TAX') {
				$sql2 = "update DataEvo set total_dpp='".$total_dpp."',biaya_yg_dibyar='".$biaya_yg_dibyar."', 
							keterangan='".$keterangan."', dpp = '".$dpp."' , ppn = '".$ppn."' , is_ppn = '".$is_ppn."' 
							where nobukti = '".$nobukti."'";
				$query2 = mssql_query($sql2);
			} else if ($level=='ADH') {
				$sql2 = "update DataEvo set tgl_bayar='".$tgl_bayar."', keterangan='".$keterangan."',
							total_dpp='".$total_dpp."',biaya_yg_dibyar='".$biaya_yg_dibyar."', 
							dpp = '".$dpp."' , ppn = '".$ppn."' , is_ppn = '".$is_ppn."' 
							where nobukti =".$nobukti." ''";
				$query2 = mssql_query($sql2);
			}
			if ($query1 && $query2) {
				mssql_query("COMMIT TRAN");
				$pesan = "Data Save!!";
			} else {
				if (!$query1) { $pesan = "0#Failed Query 1!<br/>".$sql1; }
				if (!$query2) { $pesan = "0#Failed Query 2!<br/>".$sql2; }
				mssql_query("ROLLBACK TRAN");
			}
		mssql_query("return");
		echo $pesan;
	} else if ($action=='cekRab') {
		error_reporting(0);
		$month2 = array('01' => 'Jan','02' => 'Feb','03' => 'Mar','04' => 'Apr','05' => 'Mei','06' => 'Jun','07' => 'Jul','08' => 'Ags','09' => 'Sep','10' => 'Okt','11' => 'Nov','12' => 'Dsm');
		$KodeDealer = addslashes($_REQUEST['kodedealer']);
		$kodeakun = addslashes($_REQUEST['kodeakun']);
		$nom = addslashes($_REQUEST['nom']);
		include '../inc/koneksi.php';
		if ($msg=='0') {
			$total = "0";
		} else if ($msg=='1') {
			$total = "1";
		} else if (!mssql_select_db("[$table]",$connCab)) {
			$total = "2";
		} else if ($msg=='3') {
			$acc = "ACC".$kodecabang;
			$ra = "RA".$kodecabang;
			$sql3 = "
				SELECT Tahun,a.Kodegl, a.Namagl,Jan,Feb,Mar,Apr,Mei,Jun,
				case when ISNULL(julR,0)=0 then Jul else julR end as Jul,
				case when ISNULL(AgsR,0)=0 then Ags else AgsR end as Ags,
				case when ISNULL(SepR,0)=0 then Sep else SepR end as Sep,
				case when ISNULL(OktR,0)=0 then Okt else OktR end as Okt,
				case when ISNULL(NovR,0)=0 then Nov else NovR end as Nov,
				case when ISNULL(DsmR,0)=0 then Dsm else DsmR end as Dsm,
			";
				for ($a=1; $a <= 12; $a++) { 
					if (strlen($a)==1) { $hm = "0".$a; } else { $hm = $a; }
					$test = $month2[$hm];
					if (mssql_select_db("[$acc-".$tahun."".$hm."]",$connCab)) {
						$sql3 .= "
							(
								SELECT Case 
								When (G.Kategori in ('B','C') or (G.Kodegl < '40000000' and G.Typerek = '19')) then (G.JBulanIni)*-1
								When (G.Kategori = 'A') then (G.JBulanIni)
								When (G.Kategori in ('D','G') or (G.Kodegl >= '40000000' and G.Typerek = '19')) then (G.JBulanIni)*-1
								When (G.Kategori in ('E','F','H')) then (G.JBulanIni)
								End as realMei
								from [$acc-".$tahun."".$hm."]..glmst G
								inner join [$ra]..ra F on G.KodeGl=F.KodeGl
								where G.Kodegl = a.Kodegl and Tahun=b.Tahun
							) as real$test,
						";
					} else {
						$sql3 .= "'-' as real$test,";
					}
				}
			$sql3 .="
				NUll as selesai
				from [$table]..glmst a
				inner join [$ra]..ra b on a.KodeGl=b.KodeGl
				where a.Kodegl = '".$kodeakun."' and Tahun='".$tahun."'
			";
			// echo $sql3;
			$result = mssql_query($sql3,$connCab);
			$rowz = mssql_fetch_array($result);
			$rapbThun = 0;
			$realThun = 0;
			for ($a=1; $a <= 12; $a++) { 
				if (strlen($a)==1) { $hm = "0".$a; } else { $hm = $a; }
				$test2 = $month2[$hm];
				$rapbThun += round($rowz[$test2]);
				if ($rowz["real".$test2]=='-') {
					$realThun += 0;
					$real_ = '-';
				} else {
					$realThun += round($rowz["real".$test2]);
					$real_ = number_format($rowz["real".$test2],0,",",".");
				}
			}

			$rapbBln = $rowz[$month2[$bln]];
			$realBln = $rowz["real".$month2[$bln]];
			$rapbOg = 0;
			for ($i=1; $i <= $bln; $i++) { 
				if (strlen($i)==1) { $hm = "0".$i; } else { $hm = $i; }
				$rapbOg += $rowz[$month2[$hm]];
			}
			$realOg = 0;
			for ($i=1; $i <= $bln; $i++) { 
				if (strlen($i)==1) { $hm = "0".$i; } else { $hm = $i; }
				$realOg = $rowz["real".$month2[$hm]];
			}

			if (($rapbBln-$realBln)<=$nom) {
				echo "1";
			} else {
				echo "0";
			}
		}
	} else if ($action=='getdata') {
		$id = addslashes($_REQUEST['id']);
		$sql = "select *,(select count(evopos_id) from DataEvoPos where nobukti=a.nobukti) totPos from DataEvo a where evo_id = '".$id."'";
		$vw = mssql_fetch_array(mssql_query($sql));

		$sqlPos = "select evopos_id from DataEvoPos where nobukti='".$vw['nobukti']."'";
		$rslPos = mssql_query($sqlPos);
		$evopos = "";
		while ($dtPos = mssql_fetch_array($rslPos)) {
			$evopos .= $dtPos['evopos_id'].",";
		}
		$evopos_id = substr($evopos, 0,strlen($evopos)-1);

		echo $vw['status']."#".$vw['nobukti']."#".$vw['tgl_pengajuan']."#".$vw['upload_file']."#".$vw['upload_fp']."#".$vw['kode_vendor']."#".$vw['namaVendor']."#".$vw['metode_bayar']."#".$vw['benificary_account']."#".$vw['tgl_bayar']."#".$vw['nama_bank']."#".$vw['nama_pemilik']."#".$vw['email_penerima']."#".$vw['nama_alias']."#".$vw['nama_bank_pengirim']."#".$vw['tf_from_account']."#".$vw['realisasi_nominal']."#".$vw['is_ppn']."#".$vw['dpp']."#".$vw['ppn']."#".$vw['npwp']."#".$vw['no_fj']."#".$vw['total_dpp']."#".$vw['biaya_yg_dibyar']."#".$vw['keterangan']."#".$vw['totPos']."#".$evopos_id;
	} else if ($action=='getpos') {
		$id = addslashes($_REQUEST['id']);
		$sql = "select pos_biaya as kodeAkun,ketAkun,nominal,(jns_pph+'#'+convert(varchar,tarif_persen)) as trfPajak,jns_pph,tarif_persen,nilai_pph as nilaiPph from DataEvoPos where evopos_id = '".$id."'";
		$rsl = mssql_query($sql);
		$vw = mssql_fetch_array($rsl);
		echo $vw['kodeAkun']."-".$vw['ketAkun']."-".$vw['nominal']."-".$vw['trfPajak']."-".$vw['jns_pph']."-".$vw['tarif_persen']."-".$vw['nilaiPph'];
	
	} else if ($action=='validasimulti') {
		$vw = mssql_fetch_array(mssql_query("select a.*,b.namaUser, c.namaUser, c.department, c.divisi, c.namauser useraju
								from DataEvo a 
								left join sys_user b on b.IdUser=a.IdAtasan 
								left join sys_user c on c.IdUser=a.userentry
								where evo_id = '".$_REQUEST['evoid']."'"));
		
		/*
		
		$IdUser = addslashes($_REQUEST['IdUser']);
		$level = addslashes($_REQUEST['level']);
		$KodeDealer = addslashes($_REQUEST['KodeDealer']);
		$Tipe = addslashes($_REQUEST['Tipe']);
		$nobukti = addslashes("VP".$_REQUEST['nobukti']);
		$metode_bayar = addslashes($_REQUEST['metode_bayar']);
		*/
		$IdUser = addslashes($_REQUEST['IdUser']);
		//$IdUser = addslashes($_SESSION['UserID']);
		//$level = addslashes($_REQUEST['level']);
		$level = addslashes($_SESSION['level']);		
		$KodeDealer = addslashes($vw['kodedealer']);
		$Tipe = addslashes($vw['tipe']);
		$nobukti = addslashes($vw['nobukti']);
		$metode_bayar = addslashes($vw['metode_bayar']);
		//$val = addslashes($_REQUEST['val']);
		$val = ltrim(rtrim($_REQUEST['val']));
		
		$ketreject = "";
		$ketvalidasi = "";
		
		//$over = addslashes($_REQUEST['over']);
		/*$IdUser = addslashes($_REQUEST['IdUser']);
		//$level = addslashes($_REQUEST['level']);
		$level = addslashes($_SESSION['level']);
		$KodeDealer = addslashes($_REQUEST['KodeDealer']);
		$Tipe = addslashes($_REQUEST['Tipe']);
		$nobukti = addslashes("VP".$_REQUEST['nobukti']);
		$metode_bayar = addslashes($_REQUEST['metode_bayar']);
		//$val = addslashes($_REQUEST['val']);
		$val = ltrim(rtrim($_REQUEST['val']));
		*/
		$over = addslashes($_REQUEST['over']);
		
		$pesan = "";
		
		
		if (empty($IdUser)) {
			$pesan .= "0#Maaf, gagal melakukan validasi. User validasi kosong !";
			echo $pesan;
			return;
		}
		if (empty($level)) {
			$pesan .= "0#Maaf, gagal melakukan validasi. Level validasi kosong !";
			echo $pesan;
			return;
		}
		if (empty($nobukti)) {
			$pesan .= "0#Maaf, gagal melakukan validasi. NO Bukti kosong !";
			echo $pesan;
			return;
		}
		
			#mssql_query("SET IMPLICIT_TRANSACTIONS ON",$conns);
		mssql_query("BEGIN TRAN",$conns);
			$div = mssql_fetch_array(mssql_query("
											select divisi,case when tipe='HUTANG' then htg_stl_pajak else biaya_yg_dibyar end as totBayar, 
											isnull(ltrim(rtrim(deptterkait)),'') deptterkait,
											userentry, kode_vendor, idatasan
											from DataEvo where nobukti = '".$nobukti."'",$conns));
										
			$user = mssql_fetch_array(mssql_query("select divisi,department, tipe, idstatus from sys_user where IdUser = '".$IdUser."'",$conns));
			$skipdir = mssql_fetch_array(mssql_query('select skip_direksi,skip_direksi2 from settingAkun where id=1',$conns));
			
			$user_aju = mssql_fetch_array(mssql_query("select divisi,department, tipe, idatasan, idstatus from sys_user where IdUser = '".$div['userentry']."'",$conns));
			$level_aju = $user_aju['tipe'];
			$user_entry = $div['userentry'];
			$deptterkait = ltrim(rtrim($div['deptterkait']));
			
			$ketvalidasi = ""; 
			$ketvalidasi2 = "";
			
						
			if ($val=='Accept') {
				if ($KodeDealer=='2010') {
					$is_dealer = "0";
					if ($metode_bayar=='Pety Cash') {
						$batas_direksi1 = 0;
						$batas_direksi2 = 0;
								
						if ($div['totBayar']>$skipdir['skip_direksi2']) {			
							$batas_direksi1 = 1;
							$batas_direksi2 = 1;
								
						} else {
							// tanpa direksi 2
							if ($div['totBayar']>$skipdir['skip_direksi']) {
								$batas_direksi1 = 1;
								$batas_direksi2 = 0;
								
							} else { // tanpa direksi 1 dan 2
								$batas_direksi1 = 0;
								$batas_direksi2 = 0;
							}
						}
						
						$urutan = getUrutan($level,$is_dealer, $batas_direksi1, $batas_direksi2, $IdUser, $deptterkait, $level_aju, $nobukti);
						
						if ($level=='TAX') {
							$ketvalidasi = ""; $insert=true; $query3 = true;
							$nama_lvl = " and level = '".$level."'";
						
						} else if ($level=='ACCOUNTING') {
							$ketvalidasi = ""; $insert=true; $query3 = true;
							$nama_lvl = " and level = '".$level."'";
						
						} else if ($level=='SECTION HEAD') {
							$ketvalidasi = addslashes($_REQUEST['note_sectionhead']);
							$insert=true; $query3 = true;
							$nama_lvl = " and level = '".$level."'";
							
						} else if ($level=='DEPT. HEAD') {
							/*$cek = mssql_fetch_array(mssql_query("select top 1 level from DataEvoVal where nobukti='".$nobukti."' order by tglentry desc",$conns));
						
							if ($cek['level']=='DEPT. HEAD FINANCE / DIV. HEAD FAST' and $user['divisi']=='FINANCE and ACCOUNTING') {
								$ketvalidasi = addslashes($_REQUEST['note_div_head_fast']);
								$insert=true;
								$query3 = insertAcc($nobukti);
							} else {
								$ketvalidasi = addslashes($_REQUEST['note_dept_head']);
								$insert=true; $query3 = true;
								$nama_lvl = " and level = '".$level."'";
							}*/
							
							$cek = mssql_fetch_array(mssql_query("select top 1 level from DataEvoVal where nobukti='".$nobukti."' order by tglentry desc",$conns));
						
							if ($cek['level']=='DEPT. HEAD FINANCE' and $user['divisi']=='FINANCE and ACCOUNTING' and $user['department']=='FINANCE') {
								$ketvalidasi = addslashes($_REQUEST['note_dept_head_fin']);
								$insert=true; $query3 = true;
								//$nama_lvl = " and level in ('DEPT. HEAD FINANCE','DEPT. HEAD')";
								$nama_lvl = " and level in ('DEPT. HEAD FINANCE')";
							} else {
								$ketvalidasi = addslashes($_REQUEST['note_dept_head']);
								$insert=true; $query3 = true;
								$nama_lvl = " and level = '".$level."'";
							}
							
						} else if ($level=='DIV. HEAD') {
							$cek = mssql_fetch_array(mssql_query("select top 1 level from DataEvoVal where nobukti='".$nobukti."' order by tglentry desc",$conns));
						
							if ($cek['level']=='DEPT. HEAD FINANCE / DIV. HEAD FAST' and $user['divisi']=='FINANCE and ACCOUNTING') {
								$ketvalidasi = addslashes($_REQUEST['note_div_head_fast']);
								$insert=true;
								$query3 = insertAcc($nobukti);
								//$nama_lvl = " and level in ('DEPT. HEAD FINANCE / DIV. HEAD FAST','DIV. HEAD')";
								$nama_lvl = " and level in ('DEPT. HEAD FINANCE / DIV. HEAD FAST')";
							} else {
								$ketvalidasi = addslashes($_REQUEST['note_dept_head']);
								$insert=true; $query3 = true;
								$nama_lvl = " and level = '".$level."'";
							}
						
						
						} else if ($level=='DIREKSI') {
							$ketvalidasi = addslashes($_REQUEST['note_direksi']);
							$insert=true; $query3 = true;
							$nama_lvl = " and level = '".$level."'";
						
						} else if ($level=='DIREKSI 2') {
							$ketvalidasi = addslashes($_REQUEST['note_direksi2']);
							$insert=true; $query3 = true;
							$nama_lvl = " and level = '".$level."'";
						
						} else if ($level=='FINANCE') {
							$ketvalidasi = ""; $insert=true; $query3 = true;
							$nama_lvl = " and level = '".$level."'";
						
						// dept lain
						} else if ($level=='ADMIN') {
							$ketvalidasi = ""; $insert=true; $query3 = true;
							$nama_lvl = " and level = '".$level."'";
						}	

						
					} else {
						
						/*if ($div['totBayar']>$skipdir['skip_direksi2']) {
							//$urutan = "2,3,4,5,6,7,8,9,11";
							if ($user_aju['tipe']=="SECTION HEAD") {
								$urutan = "2,4,5,6,7,8,9,11";
							} else if ($user_aju['tipe']=="DEPT. HEAD") {
								$urutan = "2,5,6,7,8,9,11";
							} else if ($user_aju['tipe']=="DIV. HEAD") {
								$urutan = "2,6,7,8,9,11";
							} else if ($user_aju['tipe']=="DIREKSI") {	
								$urutan = "2,7,8,9,11";
							} else if ($user_aju['tipe']=="DIREKSI 2") {
								$urutan = "2,8,9,11";
							} else {
								$urutan = "2,3,4,5,6,7,8,9,11";
							}	
							
						} else {
							if ($div['totBayar']>$skipdir['skip_direksi']) {
								//$urutan = "2,3,4,5,6,8,9,11";
								if ($user_aju['tipe']=="SECTION HEAD") {
									$urutan = "2,4,5,6,8,9,11";
								} else if ($user_aju['tipe']=="DEPT. HEAD") {
									$urutan = "2,5,6,8,9,11";
								} else if ($user_aju['tipe']=="DIV. HEAD") {
									$urutan = "2,6,8,9,11";
								} else if ($user_aju['tipe']=="DIREKSI") {	
									$urutan = "2,8,9,11";
								} else if ($user_aju['tipe']=="DIREKSI 2") {
									$urutan = "2,9,11";
								} else {
									$urutan = "2,3,4,5,6,8,9,11";
								}
								
							} else {
								if ($user_aju['tipe']=="SECTION HEAD") {
									$urutan = "2,4,5,8,9,11";
								} else if ($user_aju['tipe']=="DEPT. HEAD") {
									$urutan = "2,5,8,9,11";
								} else if ($user_aju['tipe']=="DIV. HEAD") {
									$urutan = "2,8,9,11";
								} else if ($user_aju['tipe']=="DIREKSI") {	
									$urutan = "2,8,9,11";
								} else if ($user_aju['tipe']=="DIREKSI 2") {
									$urutan = "2,8,9,11";
								} else {
									$urutan = "2,3,4,5,8,9,11";
								}								
								$urutan = "2,3,4,5,8,9,11";
							}
						}*/
						
						if ($div['totBayar']>$skipdir['skip_direksi2']) {
							$batas_direksi1 = 1;
							$batas_direksi2 = 1;	
							
						} else {
							if ($div['totBayar']>$skipdir['skip_direksi']) {
								$batas_direksi1 = 1;
								$batas_direksi2 = 0;	
								
							} else {
								$batas_direksi1 = 0;
								$batas_direksi2 = 0;	
								
							}
						}
						
						$urutan = getUrutan($level,$is_dealer, $batas_direksi1, $batas_direksi2, $IdUser, $deptterkait, $level_aju, $nobukti);
						
						if ($level=='TAX') {
							$ketvalidasi = ""; $insert=true; $query3 = true;
							$nama_lvl = " and level = '".$level."'";
						
						} else if ($level=='ACCOUNTING') {
							$ketvalidasi = ""; $insert=true; $query3 = true;
							$nama_lvl = " and level = '".$level."'";
								
						} else if ($level=='SECTION HEAD') {
							$ketvalidasi = addslashes($_REQUEST['note_sectionhead']);
							$insert=true; $query3 = true;
							$nama_lvl = " and level = '".$level."'";
							
						} else if ($level=='DEPT. HEAD') {
							$cek = mssql_fetch_array(mssql_query("select top 1 level from DataEvoVal where nobukti='".$nobukti."' order by tglentry desc",$conns));
							
							if ($cek['level']=='DEPT. HEAD FINANCE' and $user['divisi']=='FINANCE and ACCOUNTING' and $user['department']=='FINANCE') {
								$ketvalidasi = addslashes($_REQUEST['note_div_head_fast']); 
								$insert=true; $query3 = true; 
								//$nama_lvl = " and level in ('DEPT. HEAD FINANCE','DEPT. HEAD')";
								$nama_lvl = " and level in ('DEPT. HEAD FINANCE')";
							} else {
								$ketvalidasi = addslashes($_REQUEST['note_dept_head']);
								$insert=true; $query3 = true;
								$nama_lvl = " and level = '".$level."'";
							}
							
						} else if ($level=='DIV. HEAD') {
							
							//$cek = mssql_fetch_array(mssql_query("select top 1 level from DataEvoVal where nobukti='".$nobukti."' order by tglentry desc"));
							$cek = mssql_fetch_array(mssql_query("select top 1 level from DataEvoVal a 
																inner join sys_level b on a.level = b.nama_lvl
																where a.nobukti='".$nobukti."' order by b.urutan desc",$conns));							
							
							if ($cek['level']=='DEPT. HEAD FINANCE / DIV. HEAD FAST' and $user['divisi']=='FINANCE and ACCOUNTING') {
								$ketvalidasi = addslashes($_REQUEST['note_div_head_fast']); 
								$insert=true;
								$query3 = insertAcc($nobukti);
								//$nama_lvl = " and level in ('DEPT. HEAD FINANCE / DIV. HEAD FAST','DIV. HEAD')";
								$nama_lvl = " and level in ('DEPT. HEAD FINANCE / DIV. HEAD FAST')";
						
							} else {
								$ketvalidasi = addslashes($_REQUEST['note_div_head']);
								$insert=true; $query3 = true;
								$nama_lvl = " and level = '".$level."'";
							}
							
							
						} else if ($level=='DIREKSI') {
							$ketvalidasi = addslashes($_REQUEST['note_direksi']);
							$insert=true; $query3 = true;
							$nama_lvl = " and level = '".$level."'";
						
						} else if ($level=='DIREKSI 2') {
							$ketvalidasi = addslashes($_REQUEST['note_direksi2']);
							$insert=true; $query3 = true;
							$nama_lvl = " and level = '".$level."'";
						
								
						} else if ($level=='FINANCE') {
							$ketvalidasi = "";
							$insert=true; $query3 = true;
							$nama_lvl = " and level = '".$level."'";
						 
						// dept lain
						} else if ($level=='ADMIN') {
							$ketvalidasi = ""; $insert=true; $query3 = true;
							$nama_lvl = " and level = '".$level."'";
						}	
						
					}
					
				// cabang	
				} else {
					$is_dealer = "1";
					$nama_lvl = " and level = '".$level."'";
					if ($Tipe=='HUTANG') {
												
						if ($user_aju['tipe']=="SECTION HEAD") {
							$urutan = "2,4,5,7";
						} else if ($user_aju['tipe']=="ADH") {
							$urutan = "2,5,7";
						} else if ($user_aju['tipe']=="KEPALA CABANG") {
							$urutan = "2,7";
						} else {
							$urutan = "2,3,4,5,7";
						}
						
						if ($level=='ACCOUNTING') {
							$ketvalidasi = "";
							$insert=true; $query3 = true;
						} else if ($level=='SECTION HEAD') {
							$ketvalidasi = addslashes($_REQUEST['note_sectionhead']);
							$insert=true; $query3 = true;
						} else if ($level=='ADH') {
							$ketvalidasi = addslashes($_REQUEST['note_adh']);
							$insert=true; $query3 = true;
						} else if ($level=='KEPALA CABANG') {
							$ketvalidasi = addslashes($_REQUEST['note_branch_manager']);
							$insert=true;
							$query3 = insertAcc($nobukti);
						}
					} else if ($Tipe=='BIAYA') {
						if ($metode_bayar=='Pety Cash') {
							//$urutan = "2,3,4,7";
							
							if ($user_aju['tipe']=="SECTION HEAD") {
								$urutan = "2,4,7";
							} else if ($user_aju['tipe']=="ADH") {
								$urutan = "2,7";
							} else if ($user_aju['tipe']=="KEPALA CABANG") {
								$urutan = "2,7";
							} else {
								$urutan = "2,3,4,7";
							}
							
							if ($level=='ACCOUNTING') {
								$ketvalidasi = "";
								$insert = true; $query3 = true;
							} else if ($level=='SECTION HEAD') {
								$ketvalidasi = addslashes($_REQUEST['note_sectionhead']);

								$insert = true; $query3 = true;
							} else if ($level=='ADH') {
								$ketvalidasi = addslashes($_REQUEST['note_adh']);
								$insert = true;
								$query3 = insertAcc($nobukti);
							}
						} else {
							if ($over=="0") {
								//$urutan = "2,3,4,5,7";
								if ($user_aju['tipe']=="SECTION HEAD") {
									$urutan = "2,4,5,7";
								} else if ($user_aju['tipe']=="ADH") {
									$urutan = "2,5,7";
								} else if ($user_aju['tipe']=="KEPALA CABANG") {
									$urutan = "2,7";
								} else {
									$urutan = "2,3,4,5,7";
								}
								
								if ($level=='ACCOUNTING') {
									$ketvalidasi = "";
									$insert = true; $query3 = true;
								} else if ($level=='SECTION HEAD') {
									$ketvalidasi = addslashes($_REQUEST['note_sectionhead']);
									$insert = true; $query3 = true;
								} else if ($level=='ADH') {
									$ketvalidasi = addslashes($_REQUEST['note_adh']);
									$insert = true;
									$query3 = insertAcc($nobukti);
								}
							} else if ($over=="1") {
								//$urutan = "2,3,4,5,6,7";
								if ($user_aju['tipe']=="SECTION HEAD") {
									$urutan = "2,4,5,6,7";
								} else if ($user_aju['tipe']=="ADH") {
									$urutan = "2,5,6,7";
								} else if ($user_aju['tipe']=="KEPALA CABANG") {
									$urutan = "2,6,7";
								} else {
									$urutan = "2,3,4,5,6,7";
								}
								
								
								if ($level=='ACCOUNTING') {
									$ketvalidasi = "";
									$insert = true; $query3 = true;
								} else if ($level=='SECTION HEAD') {
									$ketvalidasi = addslashes($_REQUEST['note_sectionhead']);
									$insert = true; $query3 = true;
								} else if ($level=='ADH') {
									$ketvalidasi = addslashes($_REQUEST['note_adh']);
									$insert = true; $query3 = true;
								} else if ($level=='KEPALA CABANG') {
									$ketvalidasi = addslashes($_REQUEST['note_branch_manager']);
									$insert = true; $query3 = true;
								} else if ($level=='OM') {
									$ketvalidasi = addslashes($_REQUEST['note_om']);
									$insert = true; 
									$query3 = insertAcc($nobukti);
								}
							}
						}
					}
				}
				
				if ($insert==true) {
					
				} else {
					$query1 = true;
				}
				
				
			} else if ($val=='Reject') {
				//$query1 = true; $query3 = true; 
				$query4 = true; $query5 = true; $query6 = true; $query7 = true;
				
				$ketreject = addslashes($_REQUEST['ketreject']);
				$query1 = mssql_query("update DataEvoTagihan set isreject=1, ipentry = '".$ipaddr."', useragent = '".$_SERVER['HTTP_USER_AGENT']."'
							where nobukti = '".$nobukti."'",$conns);
							
				$query3 = mssql_query("update DataEvo set status = 'Reject' where nobukti = '".$nobukti."'",$conns);	
				//echo "update DataEvo set status = 'Reject' where nobukti = '".$nobukti."'";
							
							
			}
			
			/*if ($div['deptterkait']==$_SESSION['evo_dept']) { 
				$ketvalidasi = addslashes($_REQUEST['note_deptterkait']);
				$ketvalidasi2 = addslashes($_REQUEST['note_deptterkait']);
			}*/
			
			if (!empty($_REQUEST['note_deptterkait'])) { 
				$ketvalidasi = addslashes($_REQUEST['note_deptterkait']);
				$ketvalidasi2 = addslashes($_REQUEST['note_deptterkait']);
			}
			
			$sql2 = "update DataEvoVal set validasi='".$val."',uservalidasi='".$IdUser."',tglvalidasi=getdate(),
						ketvalidasi='".$ketvalidasi."', ketvalidasi2='".$ketvalidasi2."', ketreject='".$ketreject."', 
						ipentry = '".$ipaddr."', useragent = '".$_SERVER['HTTP_USER_AGENT']."'
						where nobukti = '".$nobukti."' and ISNULL(validasi, '')='' $nama_lvl";
			
			$query2 = mssql_query($sql2,$conns);
			
			if ($query2) { // iki
				/*
				$sql21 = "update DataEvoVal set tglvalidasi=getdate(), ipentry = '".$ipaddr."', useragent = '".$_SERVER['HTTP_USER_AGENT']."'
						where nobukti = '".$nobukti."' and uservalidasi='".$IdUser."' ";			
				$query21 = mssql_query($sql21,$conns);
				*/
				$cek = false;
				
				$qry_cek = mssql_query("select nobukti 
										from dataevoval 
										where nobukti = '".$nobukti."' $nama_lvl and ISNULL(validasi, '') !='' and ISNULL(tglvalidasi, '') !=''
										and ISNULL(deptterkait, '') ='' ",$conns);
				$jml_cek = mssql_num_rows($qry_cek);
				
				if ($jml_cek==1) {	
					$cek = true;
				} else {
					$qry_cek = mssql_query("select nobukti 
											from dataevoval 
											where nobukti = '".$nobukti."' $nama_lvl and ISNULL(validasi, '') !='' and ISNULL(tglvalidasi, '') !=''
											and ISNULL(deptterkait, '') !='' ",$conns);
					$jml_cek = mssql_num_rows($qry_cek);
					
					if ($jml_cek==1) {							
						$cek = true;
					}
				}
				
				//$cek = true;
				if ($cek) {	
					
					if ($val=='Accept') {
						//echo $urutan;
						/*if (!empty($div['deptterkait']) and str_replace(" ","",$div['deptterkait'])!='') {
							$nextlvl_arr = getLevelDeptTerkait($div['deptterkait'],$nobukti, $level, $urutan, $div['divisi'], $KodeDealer, $is_dealer);
							$nextlvl = $nextlvl_arr['tipe'];
							$nextlvl_jml = $nextlvl_arr['jml'];
							if ($nextlvl_jml==0) {
								$depterkait_in = "";
							} else {
								$depterkait_in = $div['deptterkait'];
							}
						} else {
							$nextlvl = getLevel($div['divisi'],$KodeDealer,$nobukti,$urutan,$is_dealer,$level_aju);
						}*/
						
						if ($level=='TAX' or $level=='ACCOUNTING') {
							$nextlvl = getLevel($div['divisi'],$KodeDealer,$nobukti,$urutan,$is_dealer,$level_aju);
							
						} else {	
							
							if (!empty($div['deptterkait']) and str_replace(" ","",$div['deptterkait'])!='') {
								$nextlvl_arr = getLevelDeptTerkait($div['deptterkait'],$nobukti, $level, $urutan, $div['divisi'], $KodeDealer, $is_dealer);
								$nextlvl = $nextlvl_arr['tipe'];
								$nextlvl_jml = $nextlvl_arr['jml'];
								if ($nextlvl_jml==0) {
									$depterkait_in = "";
								} else {
									$depterkait_in = $div['deptterkait'];
								}
							} else {
								$nextlvl = getLevel($div['divisi'],$KodeDealer,$nobukti,$urutan,$is_dealer,$level_aju);
							}	
						}		
											
						$nextlvl = trim($nextlvl);
						
						if (!empty($nextlvl)) {
						
							if ($level=="DIREKSI 2") {
								$sql_cekkasir = mssql_query("select level from dataevoval where nobukti = '".$nobukti."' and level = 'KASIR'",$conns);
								$cekkasir = mysql_num_rows($sql_cekkasir);
								
								if ($cekkasir==0) {
									$sql1 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait) 
											values ('$nobukti','$KodeDealer','".$nextlvl."',getdate(), '".$depterkait_in."')";
									$query1 = mssql_query($sql1,$conns);
								} else {
									$query1 = true;
									$query3 = true;
									$query4 = true;
									$query5 = true;
									$query6 = true;
									$query7 = true;
								}
							} else {
								$sql1 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait) 
										values ('$nobukti','$KodeDealer','".$nextlvl."',getdate(), '".$depterkait_in."')";
								$query1 = mssql_query($sql1,$conns);
							}
							
						} else {
							$query1 = true;
						}					
						
						#----------------------------- status user 
						if ($query1) {
							if ($nextlvl=='SECTION HEAD' or $nextlvl=='DEPT. HEAD' or $nextlvl=='DIV. HEAD'  or $nextlvl=='DIREKSI'  or $nextlvl=='DIREKSI 2') {
								if ($level=="ACCOUNTING") {
									$user_approve = $user_entry;
								} else {
									$user_approve = $IdUser;
								}
								
								$sqlatasanx = "
											select  a.idstatus, a.tipe, (select b.idstatus from sys_user b where b.IdUser = a.IdAtasan) statusatasan, 
											a.IdAtasan, (select b.tipe from sys_user b where b.IdUser = a.IdAtasan) tipeatasan, 
											(select c.IdAtasan from sys_user c where c.IdUser = a.IdAtasan) idatasan2, 
											(select d.tipe from sys_user d where d.IdUser in (select c.IdAtasan from sys_user c where c.IdUser = a.IdAtasan)) tipeatasan2,
											 
											(select c.IdAtasan from sys_user c where c.IdUser in (select c.IdAtasan from sys_user c where c.IdUser = a.IdAtasan)) idatasan3,
											(select d.tipe from sys_user d 
												where d.IdUser in (select c.IdAtasan from sys_user c where c.IdUser 
													in (select c.IdAtasan from sys_user c where c.IdUser = a.IdAtasan))) tipeatasan3
											
											from sys_user a where a.IdUser = '".$user_approve."'";
								$user_ajux = mssql_fetch_array(mssql_query($sqlatasanx,$conns));
								//echo "<pre>$sqlatasan</pre>";
											
								$status_atasan = $user_ajux['statusatasan'];
								$tipe_atasan = $user_ajux['tipeatasan'];
								$atasan = $user_ajux['IdAtasan'];
								$idatasan2 = $user_ajux['idatasan2'];
								$nextlvlx = $nextlvl;
								
								if ($nextlvl==$tipe_atasan) {
									//if ($tipe_atasan=='SECTION HEAD' or $tipe_atasan=='DEPT. HEAD'  or $tipe_atasan=='DIV. HEAD') {
										if ($status_atasan==3 or $status_atasan==4) {
											/*
											1	Aktif
											2	Non Aktif
											3	ByPass
											4	Concurrent
											*/
											if ($status_atasan==3) {
												$sql2a = "update DataEvoVal set validasi = 'Accept', uservalidasi='########',
															tglvalidasi= NULL, ketvalidasi='########', 
															ipentry = '".$ipaddr."', useragent = '".$_SERVER['HTTP_USER_AGENT']."'
															where nobukti = '".$nobukti."' and level = '".$nextlvl."'";
											
											} else if ($status_atasan==4) {
												$sql2a = "update DataEvoVal set validasi = 'Accept', uservalidasi='".$idatasan2."',
															tglvalidasi= NULL, ketvalidasi='Concurrent', 
															ipentry = '".$ipaddr."', useragent = '".$_SERVER['HTTP_USER_AGENT']."'
															where nobukti = '".$nobukti."' and level = '".$nextlvl."'";
											
											}
												
											$query2a = mssql_query($sql2a,$conns);
											
											if (!empty($div['deptterkait']) and str_replace(" ","",$div['deptterkait'])!='') {
												$nextlvl_arr = getLevelDeptTerkait($div['deptterkait'],$nobukti, $nextlvl, $urutan, $div['divisi'], $KodeDealer, $is_dealer);
												$nextlvl = $nextlvl_arr['tipe'];
												$nextlvl_jml = $nextlvl_arr['jml'];
												if ($nextlvl_jml==0) {
													$depterkait_in = "";
												} else {
													$depterkait_in = $div['deptterkait'];
												}												
												$nextlvla = $nextlvl_arr['tipe'];
											
											} else {
												$nextlvla = getLevel($div['divisi'],$KodeDealer,$nobukti,$urutan,$is_dealer,$level_aju);
											
											}
											
											$nextlvla = trim($nextlvla);
							
											if (!empty($nextlvla)) {										
												$sqla1 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait) 
														values ('$nobukti','$KodeDealer','".$nextlvla."',getdate(), '".$depterkait_in."')";
												$querya1 = mssql_query($sqla1,$conns);
												
												$nextlevel_notif = $nextlvla;
												$deptterkait_notif = $depterkait_in;
							
											} else {
												$querya1 = true;
											}
											
										} else {
										
											$nextlevel_notif = $nextlvl;
											$deptterkait_notif = $depterkait_in;
										}
									//}
								}
							}
							
						}
						
						
						
						#------------------ multi validasi			
						/*
							ADMIN
							TAX
							ACCOUNTING
							SECTION HEAD
							DEPT. HEAD
							DIV. HEAD
							DIREKSI
							DIREKSI 2
							FINANCE
							DEPT. HEAD FINANCE / DIV. HEAD FAST
							KASIR
							*/		
							
						if ($status_atasan==3 or $status_atasan==4) {
							$level = $nextlvlx;
						
						} else {		
							$level = trim(addslashes($_SESSION['level']));
							$kodevendor = ltrim(rtrim($div['kode_vendor']));
							$div_user = $user['divisi'];
							$dept_user = $user['department'];
							// echo $level."__".$div_user;
						}
								
						if ($div['totBayar']>$skipdir['skip_direksi2']) {
							//$urutan = "2,3,4,5,6,7,8,9,11";
							/*
							ADMIN
							TAX
							ACCOUNTING
							SECTION HEAD
							DEPT. HEAD
							DIV. HEAD
							DIREKSI
							DIREKSI 2
							FINANCE
							DEPT. HEAD FINANCE
							DEPT. HEAD FINANCE / DIV. HEAD FAST
							KASIR
							*/
							//echo $level;
								
							if (trim($user_aju['divisi'])=='FINANCE and ACCOUNTING' or trim($user_aju['divisi'])=='all') {	
								if (trim($user_aju['department'])=='FINANCE') {
									
									//echo $level;
										
									if ($level=='DIREKSI 2') {									
										if ($kodevendor=="MBLT-0001" or $kodevendor=="PRTTAM") {
											$query4 = true;
											$query5 = true;
											$query6 = true;
											$query7 = true;
											
										} else {
											
											$user_multi = mssql_fetch_array(mssql_query("select tipe, iduser from sys_user_tipe 
																			where IdUser = '".$user_aju['idatasan']."' and tipe = 'FINANCE'",$conns ));
							
											$stdevoval = mssql_query("select CONVERT(varchar,tglvalidasi,120) tglvalidasi, validasi, 
																		 uservalidasi, ketvalidasi
																		 from DataEvoVal 
																		 where nobukti='".$nobukti."' and level = 'SECTION HEAD'
																		 and uservalidasi = '".$user_multi['iduser']."'
																		 order by tglentry desc",$conns);
											$dataevoval = mssql_fetch_array($stdevoval);
											$cekevoval = mssql_num_rows($stdevoval);
				
											$ketvalidasi_sect = $dataevoval['ketvalidasi']; 
											$tglvalidasi_sect = $dataevoval['tglvalidasi']; 
											$validasi_sect = $dataevoval['validasi']; 
											$uservalidasi_sect = $dataevoval['uservalidasi']; 
											
											if ($cekevoval>0) {
												
												$sql4 = "update DataEvoVal set validasi='".$validasi_sect."',uservalidasi='".$uservalidasi_sect."',
															tglvalidasi= '".$tglvalidasi_sect."', ketvalidasi='".$ketvalidasi_sect."', 
															ipentry = '".$ipaddr."', useragent = '".$_SERVER['HTTP_USER_AGENT']."'
															where nobukti = '".$nobukti."' and level = 'FINANCE'";
												$query4 = mssql_query($sql4,$conns);
												
												
												#-------------- dept head + releaser
												$user_multi = mssql_fetch_array(mssql_query("select tipe, iduser from sys_user_tipe 
																		where tipe = 'DEPT. HEAD FINANCE'",$conns ));
						
												$stdevoval = mssql_query("select CONVERT(varchar,tglvalidasi,120) tglvalidasi, validasi, 
																		 uservalidasi, ketvalidasi
																		 from DataEvoVal 
																		 where nobukti='".$nobukti."' and level = 'DEPT. HEAD'
																		  and uservalidasi = '".$user_multi['iduser']."'
																		 order by tglentry desc",$conns);
												$dataevoval = mssql_fetch_array($stdevoval);
												$cekevoval = mssql_num_rows($stdevoval);
												
												if ($cekevoval>0) {
													
													$ketvalidasi_sect = $dataevoval['ketvalidasi']; 
													$tglvalidasi_sect = $dataevoval['tglvalidasi']; 
													$validasi_sect = $dataevoval['validasi']; 
													$uservalidasi_sect = $dataevoval['uservalidasi']; 
													
													$sql5 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait, 
															validasi, uservalidasi, tglvalidasi, ketvalidasi, ipentry, useragent ) 
															values ('$nobukti','$KodeDealer','DEPT. HEAD FINANCE', getdate(), '',
															'$validasi_sect','$uservalidasi_sect', '$tglvalidasi_sect','$ketvalidasi_sect',
															'".$ipaddr."', '".$_SERVER['HTTP_USER_AGENT']."')";
													$query5 = mssql_query($sql5,$conns);	
													
														
													#-------------- div head + releaser
													$user_multi = mssql_fetch_array(mssql_query("select tipe, iduser from sys_user_tipe 
																			where tipe = 'DEPT. HEAD FINANCE / DIV. HEAD FAST'",$conns ));
							
													$stdevoval = mssql_query("select CONVERT(varchar,tglvalidasi,120) tglvalidasi, validasi, 
																			 uservalidasi, ketvalidasi
																			 from DataEvoVal 
																			 where nobukti='".$nobukti."' and level = 'DIV. HEAD'
																			  and uservalidasi = '".$user_multi['iduser']."'
																			 order by tglentry desc",$conns);
													$dataevoval = mssql_fetch_array($stdevoval);
													$cekevoval = mssql_num_rows($stdevoval);
													
													if ($cekevoval>0) {
														$ketvalidasi_sect = $dataevoval['ketvalidasi']; 
														$tglvalidasi_sect = $dataevoval['tglvalidasi']; 
														$validasi_sect = $dataevoval['validasi']; 
														$uservalidasi_sect = $dataevoval['uservalidasi']; 
														
														$sql6 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait, 
																validasi, uservalidasi, tglvalidasi, ketvalidasi, ipentry, useragent ) 
																values ('$nobukti','$KodeDealer','DEPT. HEAD FINANCE / DIV. HEAD FAST', getdate(), '',
																'$validasi_sect','$uservalidasi_sect', '$tglvalidasi_sect','$ketvalidasi_sect',
																'".$ipaddr."', '".$_SERVER['HTTP_USER_AGENT']."')";
														$query6 = mssql_query($sql6,$conns);	
														
														$insert=true;
														$query3 =  insertAcc($nobukti);
														
														#---------------- kasir
														if ($query3) {
															$sql7 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait) 
																	values ('$nobukti','$KodeDealer','KASIR',getdate(), '')";
															$query7 = mssql_query($sql7,$conns);
														}
													} else {
														$query4 = true;
														$query5 = true;
														$query6 = true;
														$query7 = true;
													}
													
												} else {
													$query4 = true;
													$query5 = true;
													$query6 = true;
													$query7 = true;
												}
												
											} else {
												$query4 = true;
												$query5 = true;
												$query6 = true;
												$query7 = true;
											}
											
										}
									
									
									} else if ($level=='DIREKSI') { // --- OK
									
										if ($kodevendor=="MBLT-0001" or $kodevendor=="PRTTAM") {									
											#-------------- fincek
											$user_multi = mssql_fetch_array(mssql_query("select tipe, iduser from sys_user_tipe 
																			where IdUser = '".$user_aju['idatasan']."' and tipe = 'FINANCE'",$conns ));
							
											$stdevoval = mssql_query("select CONVERT(varchar,tglvalidasi,120) tglvalidasi, validasi, 
																	 uservalidasi, ketvalidasi
																	 from DataEvoVal 
																	 where nobukti='".$nobukti."' and level = 'SECTION HEAD'
																	 and uservalidasi = '".$user_multi['iduser']."'
																	 order by tglentry desc",$conns);
											$dataevoval = mssql_fetch_array($stdevoval);
											$cekevoval = mssql_num_rows($stdevoval);
				
											if ($cekevoval>0) {
												$ketvalidasi_sect = $dataevoval['ketvalidasi']; 
												$tglvalidasi_sect = $dataevoval['tglvalidasi']; 
												$validasi_sect = $dataevoval['validasi']; 
												$uservalidasi_sect = $dataevoval['uservalidasi']; 
												
												$sql4 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait, 
														validasi, uservalidasi, tglvalidasi, ketvalidasi, ipentry, useragent ) 
														values ('$nobukti','$KodeDealer','FINANCE', getdate(), '',
														'$validasi_sect','$uservalidasi_sect', '$tglvalidasi_sect','$ketvalidasi_sect' ,
														'".$ipaddr."', '".$_SERVER['HTTP_USER_AGENT']."')";
												$query4 = mssql_query($sql4,$conns);	
												
												
												#-------------- dept head fin + releaser
												$user_multi = mssql_fetch_array(mssql_query("select tipe, iduser from sys_user_tipe 
																				where tipe = 'DEPT. HEAD FINANCE'",$conns ));
								
												$stdevoval = mssql_query("select CONVERT(varchar,tglvalidasi,120) tglvalidasi, validasi, 
																			 uservalidasi, ketvalidasi
																			 from DataEvoVal 
																			 where nobukti='".$nobukti."' and level = 'DEPT. HEAD'
																			 and uservalidasi = '".$user_multi['iduser']."'
																			 order by tglentry desc",$conns);
												$dataevoval = mssql_fetch_array($stdevoval);
												$cekevoval = mssql_num_rows($stdevoval);
					
												$ketvalidasi_sect = $dataevoval['ketvalidasi']; 
												$tglvalidasi_sect = $dataevoval['tglvalidasi']; 
												$validasi_sect = $dataevoval['validasi']; 
												$uservalidasi_sect = $dataevoval['uservalidasi']; 
												
												if ($cekevoval>0) {
													$sql5 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait, 
															validasi, uservalidasi, tglvalidasi, ketvalidasi, ipentry, useragent ) 
															values ('$nobukti','$KodeDealer','DEPT. HEAD FINANCE', getdate(), '',
															'$validasi_sect','$uservalidasi_sect', '$tglvalidasi_sect','$ketvalidasi_sect' ,
															'".$ipaddr."', '".$_SERVER['HTTP_USER_AGENT']."')";
													$query5 = mssql_query($sql5,$conns);
													
													
													#-------------- div head + releaser
													$user_multi = mssql_fetch_array(mssql_query("select tipe, iduser from sys_user_tipe 
																								where tipe = 'DEPT. HEAD FINANCE / DIV. HEAD FAST'",$conns ));
									
													$stdevoval = mssql_query("select CONVERT(varchar,tglvalidasi,120) tglvalidasi, validasi, 
																				 uservalidasi, ketvalidasi
																				 from DataEvoVal 
																				 where nobukti='".$nobukti."' and level = 'DIV. HEAD'
																				 and uservalidasi = '".$user_multi['iduser']."'
																				 order by tglentry desc",$conns);
													$dataevoval = mssql_fetch_array($stdevoval);
													$cekevoval = mssql_num_rows($stdevoval);
						
													if ($cekevoval>0) {
														$ketvalidasi_sect = $dataevoval['ketvalidasi']; 
														$tglvalidasi_sect = $dataevoval['tglvalidasi']; 
														$validasi_sect = $dataevoval['validasi']; 
														$uservalidasi_sect = $dataevoval['uservalidasi']; 
														
														$sql6 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait, 
																validasi, uservalidasi, tglvalidasi, ketvalidasi, ipentry, useragent ) 
																values ('$nobukti','$KodeDealer','DEPT. HEAD FINANCE / DIV. HEAD FAST', getdate(), '',
																'$validasi_sect','$uservalidasi_sect', '$tglvalidasi_sect','$ketvalidasi_sect' ,
																'".$ipaddr."', '".$_SERVER['HTTP_USER_AGENT']."')";
														$query6 = mssql_query($sql6,$conns);	
														
														$insert = true;
														$query3 =  insertAcc($nobukti);
														//$query3 = true;
														
														#---------------- kasir
														if ($query3) {
															$sql7 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait) 
																	values ('$nobukti','$KodeDealer','KASIR',getdate(), '')";
															$query7 = mssql_query($sql7,$conns);
														}
													} else {
														$query4 = true;
														$query5 = true;
														$query6 = true;
														$query7 = true;
													}
												} else {
													$query4 = true;
													$query5 = true;
													$query6 = true;
													$query7 = true;
												}		
											} else {
												$query4 = true;
												$query5 = true;
												$query6 = true;
												$query7 = true;
											}
											
											
										} else {
											$query4 = true;
											$query5 = true;
											$query6 = true;
											$query7 = true;
										}					
									
									} else if ($level=='FINANCE') { // --- OK
									
										if ($kodevendor=="MBLT-0001" or $kodevendor=="PRTTAM") {									
											#-------------- dept head fin + releaser
											$query4 = true;
											$user_multi = mssql_fetch_array(mssql_query("select tipe, iduser from sys_user_tipe 
																			where tipe = 'DEPT. HEAD FINANCE'",$conns ));
							
											$stdevoval = mssql_query("select CONVERT(varchar,tglvalidasi,120) tglvalidasi, validasi, 
																		 uservalidasi, ketvalidasi
																		 from DataEvoVal 
																		 where nobukti='".$nobukti."' and level = 'DEPT. HEAD'
																		 and uservalidasi = '".$user_multi['iduser']."'
																		 order by tglentry desc",$conns);
											$dataevoval = mssql_fetch_array($stdevoval);
											$cekevoval = mssql_num_rows($stdevoval);
				
											$ketvalidasi_sect = $dataevoval['ketvalidasi']; 
											$tglvalidasi_sect = $dataevoval['tglvalidasi']; 
											$validasi_sect = $dataevoval['validasi']; 
											$uservalidasi_sect = $dataevoval['uservalidasi']; 
											
											if ($cekevoval>0) {
											
												$sql5 = "update DataEvoVal set validasi='".$validasi_sect."',uservalidasi='".$uservalidasi_sect."',
															tglvalidasi= '".$tglvalidasi_sect."', ketvalidasi='".$ketvalidasi_sect."', 
															ipentry = '".$ipaddr."', useragent = '".$_SERVER['HTTP_USER_AGENT']."'
															where nobukti = '".$nobukti."' and level = 'DEPT. HEAD FINANCE'";
												$query5 = mssql_query($sql5,$conns);
												
												
												#-------------- div head + releaser
												$user_multi = mssql_fetch_array(mssql_query("select tipe, iduser from sys_user_tipe 
																							where tipe = 'DEPT. HEAD FINANCE / DIV. HEAD FAST'",$conns ));
								
												$stdevoval = mssql_query("select CONVERT(varchar,tglvalidasi,120) tglvalidasi, validasi, 
																			 uservalidasi, ketvalidasi
																			 from DataEvoVal 
																			 where nobukti='".$nobukti."' and level = 'DIV. HEAD'
																			 and uservalidasi = '".$user_multi['iduser']."'
																			 order by tglentry desc",$conns);
												$dataevoval = mssql_fetch_array($stdevoval);
												$cekevoval = mssql_num_rows($stdevoval);
					
												if ($cekevoval>0) {
													$ketvalidasi_sect = $dataevoval['ketvalidasi']; 
													$tglvalidasi_sect = $dataevoval['tglvalidasi']; 
													$validasi_sect = $dataevoval['validasi']; 
													$uservalidasi_sect = $dataevoval['uservalidasi']; 
													
													$sql6 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait, 
															validasi, uservalidasi, tglvalidasi, ketvalidasi, ipentry, useragent ) 
															values ('$nobukti','$KodeDealer','DEPT. HEAD FINANCE / DIV. HEAD FAST', getdate(), '',
															'$validasi_sect','$uservalidasi_sect', '$tglvalidasi_sect','$ketvalidasi_sect' ,
															'".$ipaddr."', '".$_SERVER['HTTP_USER_AGENT']."')";
													$query6 = mssql_query($sql6,$conns);	
													
													$insert = true;
													$query3 =  insertAcc($nobukti);
													//$query3 = true;
													
													#---------------- kasir
													if ($query3) {
														$sql7 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait) 
																values ('$nobukti','$KodeDealer','KASIR',getdate(), '')";
														$query7 = mssql_query($sql7,$conns);
													}
	
												} else {
													$query4 = true;
													$query5 = true;
													$query6 = true;
													$query7 = true;
												}	
												
											} else {
												$query4 = true;
												$query5 = true;
												$query6 = true;
												$query7 = true;
											}		
										
											
											
										}  else {	
											$user_multi = mssql_fetch_array(mssql_query("select tipe, iduser from sys_user_tipe 
																			where tipe = 'DEPT. HEAD FINANCE'",$conns ));
							
											$stdevoval = mssql_query("select CONVERT(varchar,tglvalidasi,120) tglvalidasi, validasi, 
																		 uservalidasi, ketvalidasi
																		 from DataEvoVal 
																		 where nobukti='".$nobukti."' and level = 'DEPT. HEAD'
																		 and uservalidasi = '".$user_multi['iduser']."'
																		 order by tglentry desc",$conns);
											$dataevoval = mssql_fetch_array($stdevoval);
											$cekevoval = mssql_num_rows($stdevoval);
				
											$ketvalidasi_sect = $dataevoval['ketvalidasi']; 
											$tglvalidasi_sect = $dataevoval['tglvalidasi']; 
											$validasi_sect = $dataevoval['validasi']; 
											$uservalidasi_sect = $dataevoval['uservalidasi']; 
											
											if ($cekevoval>0) {
												$query4 = true;	
												
												if ($cekevoval>0) {
													#---------------- releaser 1 
													$sql5 = "update DataEvoVal set validasi='".$validasi_sect."',uservalidasi='".$uservalidasi_sect."',
															tglvalidasi= '".$tglvalidasi_sect."', ketvalidasi='".$ketvalidasi_sect."', 
															ipentry = '".$ipaddr."', useragent = '".$_SERVER['HTTP_USER_AGENT']."'
															where nobukti = '".$nobukti."' and level = 'DEPT. HEAD FINANCE'";
													$query5 = mssql_query($sql5,$conns);
														
													
													#-------------- div head + releaser
													$user_multi = mssql_fetch_array(mssql_query("select tipe, iduser from sys_user_tipe 
																			where tipe = 'DEPT. HEAD FINANCE / DIV. HEAD FAST'",$conns ));
												
													$stdevoval = mssql_query("select CONVERT(varchar,tglvalidasi,120) tglvalidasi, validasi, 
																				 uservalidasi, ketvalidasi
																				 from DataEvoVal 
																				 where nobukti='".$nobukti."' and level = 'DIV. HEAD'
																				 and uservalidasi = '".$user_multi['iduser']."'
																				 order by tglentry desc",$conns);
													$dataevoval = mssql_fetch_array($stdevoval);
													$cekevoval = mssql_num_rows($stdevoval);
						
													if ($cekevoval>0) {
														$ketvalidasi_sect = $dataevoval['ketvalidasi']; 
														$tglvalidasi_sect = $dataevoval['tglvalidasi']; 
														$validasi_sect = $dataevoval['validasi']; 
														$uservalidasi_sect = $dataevoval['uservalidasi']; 
														
														$sql6 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait, 
																validasi, uservalidasi, tglvalidasi, ketvalidasi, ipentry, useragent ) 
																values ('$nobukti','$KodeDealer','DEPT. HEAD FINANCE / DIV. HEAD FAST', getdate(), '',
																'$val','$uservalidasi_sect', '$tglvalidasi_sect','$ketvalidasi_sect',
															'".$ipaddr."', '".$_SERVER['HTTP_USER_AGENT']."')";
														$query6 = mssql_query($sql6,$conns);	
														
														$insert=true;
														$query3 =  insertAcc($nobukti);
														
														#---------------- kasir
														if ($query3) {
															$sql7 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait) 
																	values ('$nobukti','$KodeDealer','KASIR',getdate(), '')";
															$query7 = mssql_query($sql7,$conns);
														}
														
													}   else {
														$query3 = true;
														$query4 = true;
														$query5 = true;
														$query6 = true;
														$query7 = true;
													}	
													
												}   else {
													$query3 = true;
													$query4 = true;
													$query5 = true;
													$query6 = true;
													$query7 = true;
												}	
																			
											} else {
												$query4 = true;
												$query5 = true;
												$query6 = true;
												$query7 = true;
											}
										
										}	
									
									} else if ($level=='DEPT. HEAD') {
										$user_multi = mssql_fetch_array(mssql_query("select tipe, iduser from sys_user_tipe 
																		where tipe = 'DEPT. HEAD FINANCE / DIV. HEAD FAST'" ,$conns));
						
										#-------------- div head + releaser
										$stdevoval = mssql_query("select CONVERT(varchar,tglvalidasi,120) tglvalidasi, validasi, 
																				 uservalidasi, ketvalidasi
																				 from DataEvoVal 
																				 where nobukti='".$nobukti."' and level = 'DIV. HEAD'
																				  and uservalidasi = '".$user_multi['iduser']."'
																				 order by tglentry desc",$conns);
										$dataevoval = mssql_fetch_array($stdevoval);
										$cekevoval = mssql_num_rows($stdevoval);
										
										$ketvalidasi_sect = $dataevoval['ketvalidasi']; 
										$tglvalidasi_sect = $dataevoval['tglvalidasi']; 
										$validasi_sect = $dataevoval['validasi']; 
										$uservalidasi_sect = $dataevoval['uservalidasi']; 
										
										if ($cekevoval>0) {
											$sql6 = "update DataEvoVal set validasi='".$validasi_sect."',uservalidasi='".$uservalidasi_sect."',
														tglvalidasi= '".$tglvalidasi_sect."', ketvalidasi='".$ketvalidasi_sect."', 
														ipentry = '".$ipaddr."', useragent = '".$_SERVER['HTTP_USER_AGENT']."'
														where nobukti = '".$nobukti."' and level = 'DEPT. HEAD FINANCE / DIV. HEAD FAST'";
											$query6 = mssql_query($sql6,$conns);
											
											$query4 = true;
											$query5 = true;
											$insert=true;
											$query3 =  insertAcc($nobukti);
											
											#---------------- kasir
											if ($query3) {
												$sql7 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait) 
														values ('$nobukti','$KodeDealer','KASIR',getdate(), '')";
												$query7 = mssql_query($sql7,$conns);
											}
										}	 else {
											$query4 = true;
											$query5 = true;
											$query6 = true;
											$query7 = true;
										
										}								
									
									
										
									} else {
										$query4 = true;
										$query5 = true;
										$query6 = true;
										$query7 = true;
									}
												
								} else if (trim($user_aju['department'])=='ACCOUNTING') { //????
									
									if ($level=='DEPT. HEAD') {
										$user_multi = mssql_fetch_array(mssql_query("select tipe, iduser from sys_user_tipe 
																		where tipe = 'DEPT. HEAD FINANCE / DIV. HEAD FAST'" ,$conns));
						
										#-------------- div head + releaser
										$stdevoval = mssql_query("select CONVERT(varchar,tglvalidasi,120) tglvalidasi, validasi, 
																 uservalidasi, ketvalidasi
																 from DataEvoVal 
																 where nobukti='".$nobukti."' and level = 'DIV. HEAD'
																  and uservalidasi = '".$user_multi['iduser']."'
																 order by tglentry desc",$conns);
										$dataevoval = mssql_fetch_array($stdevoval);
										$cekevoval = mssql_num_rows($stdevoval);
										
										$ketvalidasi_sect = $dataevoval['ketvalidasi']; 
										$tglvalidasi_sect = $dataevoval['tglvalidasi']; 
										$validasi_sect = $dataevoval['validasi']; 
										$uservalidasi_sect = $dataevoval['uservalidasi']; 
										
										if ($cekevoval>0) {
											$sql6 = "update DataEvoVal set validasi='".$validasi_sect."',uservalidasi='".$uservalidasi_sect."',
														tglvalidasi= '".$tglvalidasi_sect."', ketvalidasi='".$ketvalidasi_sect."', 
														ipentry = '".$ipaddr."', useragent = '".$_SERVER['HTTP_USER_AGENT']."'
														where nobukti = '".$nobukti."' and level = 'DEPT. HEAD FINANCE / DIV. HEAD FAST'";
											$query6 = mssql_query($sql6,$conns);
											
											$query4 = true;
											$query5 = true;
											$insert=true;
											$query3 =  insertAcc($nobukti);
											
											#---------------- kasir
											if ($query3) {
												$sql7 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait) 
														values ('$nobukti','$KodeDealer','KASIR',getdate(), '')";
												$query7 = mssql_query($sql7,$conns);
											}
										}	 else {
											$query4 = true;
											$query5 = true;
											$query6 = true;
											$query7 = true;
										}								
									
									} else {
										$query4 = true;
										$query5 = true;
										$query6 = true;
										$query7 = true;
									}
									
									
								}  else {
									$query4 = true;
									$query5 = true;
									$query6 = true;
									$query7 = true;
								}
								
							}  else {
								$query4 = true;
								$query5 = true;
								$query6 = true;
								$query7 = true;
							}
							
						} else {
							if ($div['totBayar']>$skipdir['skip_direksi']) {
								//$urutan = "2,3,4,5,6,8,9,11";
								/*
								ADMIN
								TAX
								ACCOUNTING
								SECTION HEAD
								DEPT. HEAD
								DIV. HEAD
								DIREKSI
								FINANCE
								DEPT. HEAD FINANCE
								DEPT. HEAD FINANCE / DIV. HEAD FAST
								KASIR
								*/		
										
								if (trim($user_aju['divisi'])=='FINANCE and ACCOUNTING' or trim($user_aju['divisi'])=='all') {	
									if (trim($user_aju['department'])=='FINANCE') {
										
										if ($level=='DIREKSI') {
											$user_multi = mssql_fetch_array(mssql_query("select tipe, iduser from sys_user_tipe 
																			where IdUser = '".$user_aju['idatasan']."' and tipe = 'FINANCE'",$conns ));
							
											$stdevoval = mssql_query("select CONVERT(varchar,tglvalidasi,120) tglvalidasi, validasi, 
																		 uservalidasi, ketvalidasi
																		 from DataEvoVal 
																		 where nobukti='".$nobukti."' and level = 'SECTION HEAD'
																		 and uservalidasi = '".$user_multi['iduser']."'
																		 order by tglentry desc",$conns);
											$dataevoval = mssql_fetch_array($stdevoval);
											$cekevoval = mssql_num_rows($stdevoval);
				
											$ketvalidasi_sect = $dataevoval['ketvalidasi']; 
											$tglvalidasi_sect = $dataevoval['tglvalidasi']; 
											$validasi_sect = $dataevoval['validasi']; 
											$uservalidasi_sect = $dataevoval['uservalidasi']; 
											
											if ($cekevoval>0) {
											
												$sql4 = "update DataEvoVal set validasi='".$validasi_sect."',uservalidasi='".$uservalidasi_sect."',
															tglvalidasi= '".$tglvalidasi_sect."', ketvalidasi='".$ketvalidasi_sect."', 
															ipentry = '".$ipaddr."', useragent = '".$_SERVER['HTTP_USER_AGENT']."'
															where nobukti = '".$nobukti."' and level = 'FINANCE'";
												$query4 = mssql_query($sql4,$conns);
												
												
												#-------------- dept head + releaser
												$user_multi = mssql_fetch_array(mssql_query("select tipe, iduser from sys_user_tipe 
																		where tipe = 'DEPT. HEAD FINANCE'",$conns ));
						
												$stdevoval = mssql_query("select CONVERT(varchar,tglvalidasi,120) tglvalidasi, validasi, 
																		 uservalidasi, ketvalidasi
																		 from DataEvoVal 
																		 where nobukti='".$nobukti."' and level = 'DEPT. HEAD'
																		  and uservalidasi = '".$user_multi['iduser']."'
																		 order by tglentry desc",$conns);
												$dataevoval = mssql_fetch_array($stdevoval);
												$cekevoval = mssql_num_rows($stdevoval);
												
												if ($cekevoval>0) {
													$ketvalidasi_sect = $dataevoval['ketvalidasi']; 
													$tglvalidasi_sect = $dataevoval['tglvalidasi']; 
													$validasi_sect = $dataevoval['validasi']; 
													$uservalidasi_sect = $dataevoval['uservalidasi']; 
													
													$sql5 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait, 
															validasi, uservalidasi, tglvalidasi, ketvalidasi, ipentry, useragent ) 
															values ('$nobukti','$KodeDealer','DEPT. HEAD FINANCE', getdate(), '',
															'$validasi_sect','$uservalidasi_sect', '$tglvalidasi_sect','$ketvalidasi_sect',
															'".$ipaddr."', '".$_SERVER['HTTP_USER_AGENT']."')";
													$query5 = mssql_query($sql5,$conns);	
													
														
													#-------------- div head + releaser
													$user_multi = mssql_fetch_array(mssql_query("select tipe, iduser from sys_user_tipe 
																			where tipe = 'DEPT. HEAD FINANCE / DIV. HEAD FAST'",$conns ));
							
													$stdevoval = mssql_query("select CONVERT(varchar,tglvalidasi,120) tglvalidasi, validasi, 
																			 uservalidasi, ketvalidasi
																			 from DataEvoVal 
																			 where nobukti='".$nobukti."' and level = 'DIV. HEAD'
																			  and uservalidasi = '".$user_multi['iduser']."'
																			 order by tglentry desc",$conns);
													$dataevoval = mssql_fetch_array($stdevoval);
													$cekevoval = mssql_num_rows($stdevoval);
													
													if ($cekevoval>0) {
														$ketvalidasi_sect = $dataevoval['ketvalidasi']; 
														$tglvalidasi_sect = $dataevoval['tglvalidasi']; 
														$validasi_sect = $dataevoval['validasi']; 
														$uservalidasi_sect = $dataevoval['uservalidasi']; 
														
														$sql6 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait, 
																validasi, uservalidasi, tglvalidasi, ketvalidasi, ipentry, useragent ) 
																values ('$nobukti','$KodeDealer','DEPT. HEAD FINANCE / DIV. HEAD FAST', getdate(), '',
																'$validasi_sect','$uservalidasi_sect', '$tglvalidasi_sect','$ketvalidasi_sect',
																'".$ipaddr."', '".$_SERVER['HTTP_USER_AGENT']."')";
														$query6 = mssql_query($sql6,$conns);	
														
														$insert=true;
														$query3 =  insertAcc($nobukti);
														
														#---------------- kasir
														if ($query3) {
															$sql7 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait) 
																	values ('$nobukti','$KodeDealer','KASIR',getdate(), '')";
															$query7 = mssql_query($sql7,$conns);
														}
														
													}   else {
														$query3 = true;
														$query4 = true;
														$query5 = true;
														$query6 = true;
														$query7 = true;
													}								
													
												} else {
													$query4 = true;
													$query5 = true;
													$query6 = true;
													$query7 = true;
												}
												
											}   else {
												$query3 = true;
												$query4 = true;
												$query5 = true;
												$query6 = true;
												$query7 = true;
											}								
											
										
										
										} else if ($level=='FINANCE') {	
											$user_multi = mssql_fetch_array(mssql_query("select tipe, iduser from sys_user_tipe 
																			where tipe = 'DEPT. HEAD FINANCE'",$conns ));
							
											$stdevoval = mssql_query("select CONVERT(varchar,tglvalidasi,120) tglvalidasi, validasi, 
																		 uservalidasi, ketvalidasi
																		 from DataEvoVal 
																		 where nobukti='".$nobukti."' and level = 'DEPT. HEAD'
																		 and uservalidasi = '".$user_multi['iduser']."'
																		 order by tglentry desc",$conns);
											$dataevoval = mssql_fetch_array($stdevoval);
											$cekevoval = mssql_num_rows($stdevoval);
				
											$ketvalidasi_sect = $dataevoval['ketvalidasi']; 
											$tglvalidasi_sect = $dataevoval['tglvalidasi']; 
											$validasi_sect = $dataevoval['validasi']; 
											$uservalidasi_sect = $dataevoval['uservalidasi']; 
											
											if ($cekevoval>0) {
												$query4 = true;	
												
												if ($cekevoval>0) {
													#---------------- releaser 1 
													$sql5 = "update DataEvoVal set validasi='".$validasi_sect."',uservalidasi='".$uservalidasi_sect."',
															tglvalidasi= '".$tglvalidasi_sect."', ketvalidasi='".$ketvalidasi_sect."', 
															ipentry = '".$ipaddr."', useragent = '".$_SERVER['HTTP_USER_AGENT']."'
															where nobukti = '".$nobukti."' and level = 'DEPT. HEAD FINANCE'";
													$query5 = mssql_query($sql5,$conns);
														
																											
													#-------------- div head + releaser
													$user_multi = mssql_fetch_array(mssql_query("select tipe, iduser from sys_user_tipe 
																			where tipe = 'DEPT. HEAD FINANCE / DIV. HEAD FAST'",$conns ));
												
													$stdevoval = mssql_query("select CONVERT(varchar,tglvalidasi,120) tglvalidasi, validasi, 
																				 uservalidasi, ketvalidasi
																				 from DataEvoVal 
																				 where nobukti='".$nobukti."' and level = 'DIV. HEAD'
																				 and uservalidasi = '".$user_multi['iduser']."'
																				 order by tglentry desc",$conns);
													$dataevoval = mssql_fetch_array($stdevoval);
													$cekevoval = mssql_num_rows($stdevoval);
						
													if ($cekevoval>0) {
														$ketvalidasi_sect = $dataevoval['ketvalidasi']; 
														$tglvalidasi_sect = $dataevoval['tglvalidasi']; 
														$validasi_sect = $dataevoval['validasi']; 
														$uservalidasi_sect = $dataevoval['uservalidasi']; 
														
														$sql6 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait, 
																validasi, uservalidasi, tglvalidasi, ketvalidasi, ipentry, useragent ) 
																values ('$nobukti','$KodeDealer','DEPT. HEAD FINANCE / DIV. HEAD FAST', getdate(), '',
																'$validasi_sect','$uservalidasi_sect', '$tglvalidasi_sect','$ketvalidasi_sect',
															'".$ipaddr."', '".$_SERVER['HTTP_USER_AGENT']."')";
														$query6 = mssql_query($sql6,$conns);	
														
														$insert=true;
														$query3 =  insertAcc($nobukti);
														
														#---------------- kasir
														if ($query3) {
															$sql7 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait) 
																	values ('$nobukti','$KodeDealer','KASIR',getdate(), '')";
															$query7 = mssql_query($sql7,$conns);
														}
													
													}   else {
														$query3 = true;
														$query4 = true;
														$query5 = true;
														$query6 = true;
														$query7 = true;
													}	
													
												}   else {
													$query3 = true;
													$query4 = true;
													$query5 = true;
													$query6 = true;
													$query7 = true;
												}	
																			
											} else {
												$query4 = true;
												$query5 = true;
												$query6 = true;
												$query7 = true;
											}
										
										// --- OK	
										}  else if ($level=='DEPT. HEAD') {
											$user_multi = mssql_fetch_array(mssql_query("select tipe, iduser from sys_user_tipe 
																			where tipe = 'DEPT. HEAD FINANCE / DIV. HEAD FAST'" ,$conns));
							
											#-------------- div head + releaser
											$stdevoval = mssql_query("select CONVERT(varchar,tglvalidasi,120) tglvalidasi, validasi, 
																					 uservalidasi, ketvalidasi
																					 from DataEvoVal 
																					 where nobukti='".$nobukti."' and level = 'DIV. HEAD'
																					  and uservalidasi = '".$user_multi['iduser']."'
																					 order by tglentry desc",$conns);
											$dataevoval = mssql_fetch_array($stdevoval);
											$cekevoval = mssql_num_rows($stdevoval);
											
											$ketvalidasi_sect = $dataevoval['ketvalidasi']; 
											$tglvalidasi_sect = $dataevoval['tglvalidasi']; 
											$validasi_sect = $dataevoval['validasi']; 
											$uservalidasi_sect = $dataevoval['uservalidasi']; 
											
											if ($cekevoval>0) {
												$sql6 = "update DataEvoVal set validasi='".$validasi_sect."',uservalidasi='".$uservalidasi_sect."',
															tglvalidasi= '".$tglvalidasi_sect."', ketvalidasi='".$ketvalidasi_sect."', 
															ipentry = '".$ipaddr."', useragent = '".$_SERVER['HTTP_USER_AGENT']."'
															where nobukti = '".$nobukti."' and level = 'DEPT. HEAD FINANCE / DIV. HEAD FAST'";
												$query6 = mssql_query($sql6,$conns);
												
												$query4 = true;
												$query5 = true;
												$insert=true;
												$query3 =  insertAcc($nobukti);
												
												#---------------- kasir
												if ($query3) {
													$sql7 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait) 
															values ('$nobukti','$KodeDealer','KASIR',getdate(), '')";
													$query7 = mssql_query($sql7,$conns);
												}
											}	 else {
												$query4 = true;
												$query5 = true;
												$query6 = true;
												$query7 = true;
											}							
										
										} else {
											$query4 = true;
											$query5 = true;
											$query6 = true;
											$query7 = true;
										}
									
									} else if (trim($user_aju['department'])=='ACCOUNTING') { //????
									
										if ($level=='DEPT. HEAD') {
											$user_multi = mssql_fetch_array(mssql_query("select tipe, iduser from sys_user_tipe 
																			where tipe = 'DEPT. HEAD FINANCE / DIV. HEAD FAST'" ,$conns));
							
											#-------------- div head + releaser
											$stdevoval = mssql_query("select CONVERT(varchar,tglvalidasi,120) tglvalidasi, validasi, 
																		 uservalidasi, ketvalidasi
																		 from DataEvoVal 
																		 where nobukti='".$nobukti."' and level = 'DIV. HEAD'
																		  and uservalidasi = '".$user_multi['iduser']."'
																		 order by tglentry desc",$conns);
											$dataevoval = mssql_fetch_array($stdevoval);
											$cekevoval = mssql_num_rows($stdevoval);
											
											$ketvalidasi_sect = $dataevoval['ketvalidasi']; 
											$tglvalidasi_sect = $dataevoval['tglvalidasi']; 
											$validasi_sect = $dataevoval['validasi']; 
											$uservalidasi_sect = $dataevoval['uservalidasi']; 
											
											if ($cekevoval>0) {
												$sql6 = "update DataEvoVal set validasi='".$validasi_sect."',uservalidasi='".$uservalidasi_sect."',
															tglvalidasi= '".$tglvalidasi_sect."', ketvalidasi='".$ketvalidasi_sect."', 
															ipentry = '".$ipaddr."', useragent = '".$_SERVER['HTTP_USER_AGENT']."'
															where nobukti = '".$nobukti."' and level = 'DEPT. HEAD FINANCE / DIV. HEAD FAST'";
												$query6 = mssql_query($sql6,$conns);
												
												$query4 = true;
												$query5 = true;
												$insert=true;
												$query3 =  insertAcc($nobukti);
												
												#---------------- kasir
												if ($query3) {
													$sql7 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait) 
															values ('$nobukti','$KodeDealer','KASIR',getdate(), '')";
													$query7 = mssql_query($sql7,$conns);
												}
											}	 else {
												$query4 = true;
												$query5 = true;
												$query6 = true;
												$query7 = true;
											}								
										
										} else {
											$query4 = true;
											$query5 = true;
											$query6 = true;
											$query7 = true;
										}
										
									}  else {
										$query4 = true;
										$query5 = true;
										$query6 = true;
										$query7 = true;
									}
									
								}  else {
									$query4 = true;
									$query5 = true;
									$query6 = true;
									$query7 = true;
								}
								
							} else {
								//$urutan = "2,3,4,5,8,9,11";
								
								/*ADMIN
								TAX
								ACCOUNTING
								SECTION HEAD
								DEPT. HEAD
								DIV. HEAD
								FINANCE
								DEPT. HEAD FINANCE
								DEPT. HEAD FINANCE / DIV. HEAD FAST
								KASIR
								*/
								if (trim($user_aju['divisi'])=='FINANCE and ACCOUNTING' or trim($user_aju['divisi'])=='all') {	
									if (trim($user_aju['department'])=='FINANCE') {
									
										// --- DIV HEAD
										if ($level=='DIV. HEAD') {	
											$user_multi = mssql_fetch_array(mssql_query("select tipe, iduser from sys_user_tipe 
																			where IdUser = '".$user_aju['idatasan']."' and tipe = 'FINANCE'",$conns ));
							
											$stdevoval = mssql_query("select CONVERT(varchar,tglvalidasi,120) tglvalidasi, validasi, 
																		 uservalidasi, ketvalidasi
																		 from DataEvoVal 
																		 where nobukti='".$nobukti."' and level = 'SECTION HEAD'
																		 and uservalidasi = '".$user_multi['iduser']."'
																		 order by tglentry desc",$conns);
											$dataevoval = mssql_fetch_array($stdevoval);
											$cekevoval = mssql_num_rows($stdevoval);
				
											$ketvalidasi_sect = $dataevoval['ketvalidasi']; 
											$tglvalidasi_sect = $dataevoval['tglvalidasi']; 
											$validasi_sect = $dataevoval['validasi']; 
											$uservalidasi_sect = $dataevoval['uservalidasi']; 
											
											if ($cekevoval>0) {
											
												#---------------------- fincek
												$sql4 = "update DataEvoVal set validasi='".$validasi_sect."',uservalidasi='".$uservalidasi_sect."',
															tglvalidasi= '".$tglvalidasi_sect."', ketvalidasi='".$ketvalidasi_sect."', 
															ipentry = '".$ipaddr."', useragent = '".$_SERVER['HTTP_USER_AGENT']."'
															where nobukti = '".$nobukti."' and level = 'FINANCE'";
												$query4 = mssql_query($sql4,$conns);	
												
												
												$user_multi = mssql_fetch_array(mssql_query("select tipe, iduser from sys_user_tipe 
																			where tipe = 'DEPT. HEAD FINANCE'",$conns ));
							
												$stdevoval = mssql_query("select CONVERT(varchar,tglvalidasi,120) tglvalidasi, validasi, 
																		 uservalidasi, ketvalidasi
																		 from DataEvoVal 
																		 where nobukti='".$nobukti."' and level = 'DEPT. HEAD'
																		 and uservalidasi = '".$user_multi['iduser']."'
																		 order by tglentry desc",$conns);
												$dataevoval = mssql_fetch_array($stdevoval);
												$cekevoval = mssql_num_rows($stdevoval);
					
												$ketvalidasi_sect = $dataevoval['ketvalidasi']; 
												$tglvalidasi_sect = $dataevoval['tglvalidasi']; 
												$validasi_sect = $dataevoval['validasi']; 
												$uservalidasi_sect = $dataevoval['uservalidasi']; 
												
												if ($cekevoval>0) {
													#---------------- releaser 1 
													$sql5 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait, 
															validasi, uservalidasi, tglvalidasi, ketvalidasi, ipentry, useragent ) 
															values ('$nobukti','$KodeDealer','DEPT. HEAD FINANCE', getdate(), '',
															'$validasi_sect','$uservalidasi_sect', '$tglvalidasi_sect','$ketvalidasi_sect',
															'".$ipaddr."', '".$_SERVER['HTTP_USER_AGENT']."')";
													$query5 = mssql_query($sql5,$conns);
																										
													#-------------- div head + releaser
													$sql6 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait, 
															validasi, uservalidasi, tglvalidasi, ketvalidasi, ipentry, useragent ) 
															values ('$nobukti','$KodeDealer','DEPT. HEAD FINANCE / DIV. HEAD FAST', getdate(), '',
															'$val','$IdUser', getdate(),'$ketvalidasi',
														'".$ipaddr."', '".$_SERVER['HTTP_USER_AGENT']."')";
													$query6 = mssql_query($sql6,$conns);	
													
													$insert=true;
													$query3 =  insertAcc($nobukti);
													
													#---------------- kasir
													if ($query3) {
														$sql7 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait) 
																values ('$nobukti','$KodeDealer','KASIR',getdate(), '')";
														$query7 = mssql_query($sql7,$conns);
													}
													
												}   else {
													$query3 = true;
													$query4 = true;
													$query5 = true;
													$query6 = true;
													$query7 = true;
												}	
																			
											} else {
												$query4 = true;
												$query5 = true;
												$query6 = true;
												$query7 = true;
											}
										
										// --- FINCEK	
										} else if ($level=='FINANCE') {	
											$user_multi = mssql_fetch_array(mssql_query("select tipe, iduser from sys_user_tipe 
																			where tipe = 'DEPT. HEAD FINANCE'",$conns ));
							
											$stdevoval = mssql_query("select CONVERT(varchar,tglvalidasi,120) tglvalidasi, validasi, 
																		 uservalidasi, ketvalidasi
																		 from DataEvoVal 
																		 where nobukti='".$nobukti."' and level = 'DEPT. HEAD'
																		 and uservalidasi = '".$user_multi['iduser']."'
																		 order by tglentry desc",$conns);
											$dataevoval = mssql_fetch_array($stdevoval);
											$cekevoval = mssql_num_rows($stdevoval);
				
											$ketvalidasi_sect = $dataevoval['ketvalidasi']; 
											$tglvalidasi_sect = $dataevoval['tglvalidasi']; 
											$validasi_sect = $dataevoval['validasi']; 
											$uservalidasi_sect = $dataevoval['uservalidasi']; 
											
											if ($cekevoval>0) {
												$query4 = true;	
												
												if ($cekevoval>0) {
													#---------------- releaser 1 
													$sql5 = "update DataEvoVal set validasi='".$validasi_sect."',uservalidasi='".$uservalidasi_sect."',
															tglvalidasi= '".$tglvalidasi_sect."', ketvalidasi='".$ketvalidasi_sect."', 
															ipentry = '".$ipaddr."', useragent = '".$_SERVER['HTTP_USER_AGENT']."'
															where nobukti = '".$nobukti."' and level = 'DEPT. HEAD FINANCE'";
													$query5 = mssql_query($sql5,$conns);
														
																										
													#-------------- div head + releaser
													$user_multi = mssql_fetch_array(mssql_query("select tipe, iduser from sys_user_tipe 
																			where tipe = 'DEPT. HEAD FINANCE / DIV. HEAD FAST'",$conns ));
												
													$stdevoval = mssql_query("select CONVERT(varchar,tglvalidasi,120) tglvalidasi, validasi, 
																				 uservalidasi, ketvalidasi
																				 from DataEvoVal 
																				 where nobukti='".$nobukti."' and level = 'DIV. HEAD'
																				 and uservalidasi = '".$user_multi['iduser']."'
																				 order by tglentry desc",$conns);
													$dataevoval = mssql_fetch_array($stdevoval);
													$cekevoval = mssql_num_rows($stdevoval);
						
													if ($cekevoval>0) {
														$ketvalidasi_sect = $dataevoval['ketvalidasi']; 
														$tglvalidasi_sect = $dataevoval['tglvalidasi']; 
														$validasi_sect = $dataevoval['validasi']; 
														$uservalidasi_sect = $dataevoval['uservalidasi']; 
														
														$sql6 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait, 
																validasi, uservalidasi, tglvalidasi, ketvalidasi, ipentry, useragent ) 
																values ('$nobukti','$KodeDealer','DEPT. HEAD FINANCE / DIV. HEAD FAST', getdate(), '',
																'$val','$uservalidasi_sect', '$tglvalidasi_sect','$ketvalidasi_sect',
															'".$ipaddr."', '".$_SERVER['HTTP_USER_AGENT']."')";
														$query6 = mssql_query($sql6,$conns);	
														
														$insert=true;
														$query3 =  insertAcc($nobukti);
														
														#---------------- kasir
														if ($query3) {
															$sql7 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait) 
																	values ('$nobukti','$KodeDealer','KASIR',getdate(), '')";
															$query7 = mssql_query($sql7,$conns);
														}
														
													}  else {
														$query3 = true;
														$query4 = true;
														$query5 = true;
														$query6 = true;
														$query7 = true;
													}	
													
													
												} else {
													$query3 = true;
													$query4 = true;
													$query5 = true;
													$query6 = true;
													$query7 = true;
												}	
																			
											} else {
												$query4 = true;
												$query5 = true;
												$query6 = true;
												$query7 = true;
											}
										
										// --- OK	
										} else if ($level=='DEPT. HEAD') {	
											$query4 = true;	
											$query5 = true;
													
											#-------------- div head + releaser
											$user_multi = mssql_fetch_array(mssql_query("select tipe, iduser from sys_user_tipe 
																	where tipe = 'DEPT. HEAD FINANCE / DIV. HEAD FAST'",$conns ));
										
											$stdevoval = mssql_query("select CONVERT(varchar,tglvalidasi,120) tglvalidasi, validasi, 
																		 uservalidasi, ketvalidasi
																		 from DataEvoVal 
																		 where nobukti='".$nobukti."' and level = 'DIV. HEAD'
																		 and uservalidasi = '".$user_multi['iduser']."'
																		 order by tglentry desc",$conns);
											$dataevoval = mssql_fetch_array($stdevoval);
											$cekevoval = mssql_num_rows($stdevoval);
				
											if ($cekevoval>0) {
												$ketvalidasi_sect = $dataevoval['ketvalidasi']; 
												$tglvalidasi_sect = $dataevoval['tglvalidasi']; 
												$validasi_sect = $dataevoval['validasi']; 
												$uservalidasi_sect = $dataevoval['uservalidasi']; 
												
												$sql6 = "update DataEvoVal set validasi='".$validasi_sect."',uservalidasi='".$uservalidasi_sect."',
															tglvalidasi= '".$tglvalidasi_sect."', ketvalidasi='".$ketvalidasi_sect."', 
															ipentry = '".$ipaddr."', useragent = '".$_SERVER['HTTP_USER_AGENT']."'
															where nobukti = '".$nobukti."' and level = 'DEPT. HEAD FINANCE / DIV. HEAD FAST'";
												$query6 = mssql_query($sql6,$conns);	
												
												$insert=true;
												$query3 =  insertAcc($nobukti);
												
												#---------------- kasir
												if ($query3) {
													$sql7 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait) 
															values ('$nobukti','$KodeDealer','KASIR',getdate(), '')";
													$query7 = mssql_query($sql7,$conns);
												}
												
											} else {
												$query4 = true;
												$query5 = true;
												$query6 = true;
												$query7 = true;
											}
										
										// --- OK	
										} else {
											$query4 = true;
											$query5 = true;
											$query6 = true;
											$query7 = true;
										}
										
									} else if (trim($user_aju['department'])=='ACCOUNTING') {
									
										// --- releaser 1
										if ($level=='DEPT. HEAD') {	
											$query4 = true;
											$query5 = true;
											
											$user_multi = mssql_fetch_array(mssql_query("select tipe, iduser from sys_user_tipe 
																			where tipe = 'DEPT. HEAD FINANCE / DIV. HEAD FAST'",$conns ));
												
											$stdevoval = mssql_query("select CONVERT(varchar,tglvalidasi,120) tglvalidasi, validasi, 
																	 uservalidasi, ketvalidasi
																	 from DataEvoVal 
																	 where nobukti='".$nobukti."' and level = 'DIV. HEAD'
																	 and uservalidasi = '".$user_multi['iduser']."'
																	 order by tglentry desc",$conns);
											$dataevoval = mssql_fetch_array($stdevoval);
											$cekevoval = mssql_num_rows($stdevoval);
				
											$ketvalidasi_sect = $dataevoval['ketvalidasi']; 
											$tglvalidasi_sect = $dataevoval['tglvalidasi']; 
											$validasi_sect = $dataevoval['validasi']; 
											$uservalidasi_sect = $dataevoval['uservalidasi']; 
											
											if ($cekevoval>0) {
												#-------------- div head + releaser
												$sql6 = "update DataEvoVal set validasi='".$validasi_sect."',uservalidasi='".$uservalidasi_sect."',
														tglvalidasi= '".$tglvalidasi_sect."', ketvalidasi='".$ketvalidasi_sect."', 
														ipentry = '".$ipaddr."', useragent = '".$_SERVER['HTTP_USER_AGENT']."'
														where nobukti = '".$nobukti."' and level = 'DEPT. HEAD FINANCE / DIV. HEAD FAST'";
												$query6 = mssql_query($sql6,$conns);	
												
												$insert=true;
												$query3 =  insertAcc($nobukti);
												
												#---------------- kasir
												if ($query3) {
													$sql7 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait) 
															values ('$nobukti','$KodeDealer','KASIR',getdate(), '')";
													$query7 = mssql_query($sql7,$conns);
												}
												
											
											} else {
												$query4 = true;
												$query5 = true;
												$query6 = true;
												$query7 = true;
											}
										
										// --- FINCEK	
										} else {
											$query4 = true;
											$query5 = true;
											$query6 = true;
											$query7 = true;
										}
																					
									} else {
										$query4 = true;
										$query5 = true;
										$query6 = true;
										$query7 = true;
									}
								
								} else {
									$query4 = true;
									$query5 = true;
									$query6 = true;
									$query7 = true;
								}
								
							}
						}
																	
					}
					
				} else {
					
				}
				
				
			}
			
			#echo "<pre>".$sql1."</pre>";	
			#echo "<pre>".$sql2."</pre>";	
			
			if ($query1) {
			
				if ($query2) {
				
					if ($query3) {
					
						if ($query4) {
					
							if ($query5) {
					
								if ($query6) {
								
									if ($query7) {
						
										mssql_query("COMMIT TRAN",$conns);
										
										if ($val=='Accept') {
											// $sql = "
											// 	select c.nik,c.email,c.no_tlp,c.namaUser as validator,d.namaUser as pengaju,
											// 	d.department,e.NamaDealer,b.tipe,tgl_pengajuan,metode_bayar,a.nobukti,namaVendor,keterangan,
											// 	case when b.tipe = 'HUTANG' then htg_stl_pajak else biaya_yg_dibyar end as nominal
											// 	from DataEvoVal a
											// 	inner join DataEvo b on a.nobukti=b.nobukti
											// 	inner join sys_user c on c.tipe = a.level and (c.divisi=b.divisi or c.divisi='all') 
											// 	inner join sys_user d on d.IdUser = b.userentry
											// 	inner join SPK00..dodealer e on b.kodedealer = e.kodedealer
											// 	where a.nobukti = '".$nobukti."' and ISNULL(validasi, '')='' 
											// 	and c.IdUser = case when (a.level='SECTION HEAD' or a.level='ADH') then b.IdAtasan else c.IdUser end
											// ";
											$sql = "
												select namaUser as pengaju,department,a.nobukti,NamaDealer,a.tipe,tgl_pengajuan,metode_bayar,namaVendor,keterangan,
												case when a.tipe = 'HUTANG' then htg_stl_pajak else biaya_yg_dibyar end as nominal,a.IdAtasan,a.kodedealer,a.divisi,
												isnull(a.deptterkait,'') deptterkait
												from DataEvo a inner join sys_user b on b.IdUser = a.userentry
												inner join SPK00..dodealer c on a.kodedealer = c.kodedealer
												where nobukti = '".$nobukti."'
											";
											$dt = mssql_fetch_array(mssql_query($sql,$conns));
											
											$sqlterkait = "select top 1 isnull(deptterkait,'') deptterkait, level, kodedealer
															from DataEvoval
															where nobukti = '".$nobukti."'
															order by idval desc";
											$dtterkait = mssql_fetch_array(mssql_query($sqlterkait,$conns));
											$nextlevel_notif = $dtterkait['level'];
											
											if (!empty($dtterkait['deptterkait']) and str_replace(" ","",$dtterkait['deptterkait'])!='') {
												
												if ($nextlevel_notif=='SECTION HEAD') {
													$s_section = "and kodedealer='".$dt['kodedealer']."' and tipe='".$nextlevel_notif."' and department='".$dt['deptterkait']."' 
																	and IdUser in (select IdUser from DeptTerkait)  ";
												
												} else if ($nextlevel_notif=='DEPT. HEAD FINANCE / DIV. HEAD FAST') {
													$s_section = "and kodedealer='".$dt['kodedealer']."' and (tipe='DIV. HEAD' or tipe='DEPT. HEAD') and divisi='FINANCE and ACCOUNTING' 
														and (department='FINANCE' or ISNULL(department, '')='') and IdUser in (select IdUser from DeptTerkait) ";
												
												} else if ($nextlevel_notif=='DEPT. HEAD FINANCE') {
													$s_section = "and kodedealer='".$dt['kodedealer']."' and  tipe='DEPT. HEAD' and divisi='FINANCE and ACCOUNTING' 
														and (department='FINANCE' or ISNULL(department, '')='') and IdUser in (select IdUser from DeptTerkait) ";
												
												} else if ($nextlevel_notif=='OM') {
													$s_section = "and kodedealer='all' and tipe='OM' and IdUser in (select IdUser from DeptTerkait) ";
												
												} else if ($nextlevel_notif=='ACCOUNTING') {
													$s_section = "and kodedealer='".$dt['kodedealer']."' and tipe='".$nextlevel_notif."' ";
													
												} else {
													if ($dtterkait['kodedealer']=='2010') {
														$s_section = "
															and kodedealer='".$dt['kodedealer']."' and tipe='".$nextlevel_notif."' and department='".$dt['deptterkait']."' and IdUser in (select IdUser from DeptTerkait) ";
													} else {
														$s_section = "and kodedealer='".$dt['kodedealer']."' and tipe='".$nextlevel_notif."' and department='".$dt['deptterkait']."'  and IdUser in (select IdUser from DeptTerkait) ";
													}
												}
												
												
											} else {
												if ($nextlevel_notif=='SECTION HEAD') {
													$s_section = "and kodedealer='".$dt['kodedealer']."' and tipe='".$nextlevel_notif."' and idUser = '".$dt['IdAtasan']."'";
												
												} else if ($nextlevel_notif=='DEPT. HEAD FINANCE / DIV. HEAD FAST') {
													$s_section = "and kodedealer='".$dt['kodedealer']."' and (tipe='DIV. HEAD' or tipe='DEPT. HEAD') and divisi='FINANCE and ACCOUNTING' 
														and (department='FINANCE' or ISNULL(department, '')='')";
												
												} else if ($nextlevel_notif=='DEPT. HEAD FINANCE') {
													$s_section = "and kodedealer='".$dt['kodedealer']."' and  tipe='DEPT. HEAD' and divisi='FINANCE and ACCOUNTING' 
														and (department='FINANCE' or ISNULL(department, '')='')";
												
												} else if ($nextlevel_notif=='OM') {
													$s_section = "and kodedealer='all' and tipe='OM'";
												
												} else if ($nextlevel_notif=='ACCOUNTING') {
													$s_section = "and kodedealer='".$dt['kodedealer']."' and tipe='".$nextlevel_notif."' ";
												
												} else if ($nextlevel_notif=='DIREKSI' or $nextlevel_notif =='DIREKSI 2') {
													$s_section = "and kodedealer='".$dt['kodedealer']."' and tipe='".$nextlevel_notif."'";
												
												} else {
													if ($dtterkait['kodedealer']=='2010') {
														if ($nextlevel_notif=='DIV. HEAD') {
															$s_section = "
																and kodedealer='".$dt['kodedealer']."' and tipe='".$nextlevel_notif."' 
																and (divisi='".$dt['divisi']."' or divisi='all') 
																and (department='".$dt['department']."' or ISNULL(department, '')='' or department='all')";
														} else {
															$s_section = "
																and kodedealer='".$dt['kodedealer']."' and tipe='".$nextlevel_notif."' 
																and (divisi='".$dt['divisi']."' or divisi='all') 
																and (department='".$dt['department']."' or ISNULL(department, '')='')";
														}
													} else {
														$s_section = "and kodedealer='".$dt['kodedealer']."' and tipe='".$nextlevel_notif."' and department='".$dt['department']."'";
													}
												}
												
											} 
											
											
											$sql2 = "select nik,email,no_tlp,namaUser as validator from sys_user where ISNULL(isDel,'')='' and idstatus = '1' $s_section";
											$rvld = mssql_query($sql2,$conns);
											$bodyIntra = ""; $nik=""; $email = ""; $no_tlp = ""; $bodyWa = "";
											
											while ($vld = mssql_fetch_array($rvld)) {
												$bodyIntra .= 'Kepada Yth. Bp/Ibu '.$vld['validator'].', ';
												$bodyIntra .= 'Kami informasikan permohonan Validasi Voucher Payment atas: ';
												$bodyIntra .= 'Nama Pengaju:'.$dt['pengaju'].', ';
												$bodyIntra .= 'Department: '.$dt['department'].', ';
												$bodyIntra .= 'Nomor Tagihan: '.$dt['nobukti'].', ';
												$bodyIntra .= 'Terimakasih untuk kerjasamanya.;';
							
												$bodyWa .= 'Mohon Validasi Voucher Payment '.$dt['nobukti'].' tanggal '.date('d/m/Y', strtotime($dt['tgl_pengajuan'])).', ';
												$bodyWa .= 'dari '.strtoupper($dt['pengaju']).' ('.$dt['department'].' Department '.$dt['NamaDealer'].'), ';
												$bodyWa .= 'guna membayar tagihan '.$dt['namaVendor'].', sebesar: '.number_format($dt['nominal'],0,",",".").', untuk '.$dt['keterangan'].'. Untuk melakukan validasi silahkan klik link http://evopay.nasmoco.net . Terima kasih.';
							
												$nik .= $vld['nik'].";";
												$email .= $vld['email'].";";
												$no_tlp .= $vld['no_tlp'].";";
											}
							
											$n_bodyIntra 	= substr($bodyIntra, 0, -1);
											$n_nik 			= substr($nik, 0, -1);
											$n_email 		= substr($email, 0, -1);
											$n_no_tlp 		= substr($no_tlp, 0, -1);
											$n_bodyWa 		= substr($bodyWa, 0, -1);
											$pesan .= "Transaksi telah berhasil di ".$val."!#".$dt['nobukti']."#".$n_bodyIntra."#".$n_nik."#".$n_email."#".$n_no_tlp."#".$n_bodyWa;
											
										} else {
											$pesan .= "Transaksi telah berhasil di ".$val."!";
										}
						
									}else{
										$pesan .= "0#Maaf, gagal melakukan validasi, gagal proses kasir";
										mssql_query("ROLLBACK TRAN",$conns);
									}
								}else{
									$pesan .= "0#Maaf, gagal melakukan validasi 6";
									mssql_query("ROLLBACK TRAN",$conns);
								}

							}else{
								$pesan .= "0#Maaf, gagal melakukan validasi 5";
								mssql_query("ROLLBACK TRAN",$conns);
							}

						}else{
							$pesan .= "0#Maaf, gagal melakukan validasi 4";
							mssql_query("ROLLBACK TRAN",$conns);
						}	
							
					}else{
						$pesan .= "0#Maaf, gagal input ke jurnal!";
						mssql_query("ROLLBACK TRAN",$conns);
					}
					
				} else {
					$pesan .= "0#Maaf, gagal melakukan validasi 2";
					mssql_query("ROLLBACK TRAN",$conns);
				}						
				
				
			} else {
			
				/*if (!$query1) { $pesan .= "0#Failed Query 1!<br/>".$sql1; }
				if (!$query2) { $pesan .= "0#Failed Query 2!<br/>".$sql2; }
				if (!$query3) { $pesan .= "0#Failed Query 3!<br/>".$query3; }				
				//if (!$query3) { $pesan .= "0#Failed Query 3!<br/>".$query3; }
				*/
				$pesan = "Maaf, gagal melakukan validasi 1";
				mssql_query("ROLLBACK TRAN",$conns);
			}
			
		mssql_query("return",$conns);
		echo $pesan;
		
	}  else if ($action=='deleteposbiaya') {
		
		$nobukti = $_POST['nobukti'];
		$idpos = $_POST['idpos'];
		
		$sql = "delete from DataEvoPos where nobukti = '".$nobukti."' and evopos_id = '".$idpos."'";
		$stm = mssql_query($sql,$conns);
		
		if ($stm) {
			echo "1";
		} else {
			echo "0";
		}
		
	} else if ($action=='deleteposhutang') {
		$nobukti = $_POST['nobukti'];
		$idpos = $_POST['idpos'];
		
		$sql = "delete from DataEvoPos where nobukti = '".$nobukti."' and evopos_id = '".$idpos."'";
		$stm = mssql_query($sql,$conns);
		
		if ($stm) {
			echo "1";
		} else {
			echo "0";
		}
	}
	
	// echo insertAcc('VP00/28/07/20/001');
	
	
	function insertAccTemp($nobukti){
	
		include '../inc/conn.php';
		$dtAcc = mssql_fetch_array(mssql_query("select * from DataEvo where nobukti = '".$nobukti."'",$conns));
		$dtAkun = mssql_fetch_array(mssql_query("select * from settingAkun where id=1",$conns));
		if ($dtAcc['kodedealer']=='2010') { $posisi = "HO"; } else { $posisi = "Dealer"; }
		$dtppn = mssql_fetch_array(mssql_query("select jnsPpn from sys_hutang where nama = '".$dtAcc['tipehutang']."' and posisi = '".$posisi."'",$conns));
		$akunppn = $dtppn['jnsPpn'];
		$sgltrn = ""; $saptrn = ""; $sglmst = "";
		$delIndex = "AP".rand(0,99999999);
		$KodeDealer = $dtAcc['kodedealer'];
		include '../inc/koneksi.php';
		$pesan = "";
		
		if ($msg=='0') {
			$pesan = false; // "Gagal Koneksi Cabang!";
		} else if ($msg=='1') {
			$pesan = false; // "Gagal Koneksi HO!";
		} else if (!mssql_select_db("[$table]",$connCab)) {
			$pesan = false; // "Database tidak tersedia!";
			
		} else if ($msg=='3') {
			$getdate = date('Y-m-d');
			
			if ($dtAcc['tipe']=='HUTANG') {
				
				include '../inc/conn.php';
								
				//--------------------------------------- gltrn
				$sgltrn .= "
					insert into [$table]..gltrn (NoBukti,KodeGl,TglTrn,KodeSumber,TypeTrn,Keterangan,JlhDebit,JlhKredit,DelIndex,LastUpdated,KodeUser,KodeLgn,SumberForm,NoFPS) 
					values
				";
				
				if ($dtAcc['tipeppn']=='E' or $dtAcc['tipeppn']=='I') {
					#$sgltrn .= "('$dtAcc[nobukti]','$dtAcc[kodeAkun]','$dtAcc[tgl_pengajuan]','AP','03','VP $dtAcc[keterangan]','$dtAcc[dpp]','0','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";
					#$sgltrn .= "('$dtAcc[nobukti]','$dtAkun[$akunppn]','$dtAcc[tgl_pengajuan]','AP','03','PPN BMHD $dtAcc[keterangan]','$dtAcc[ppn]','0','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";
					
					$sgltrn .= "('$dtAcc[nobukti]','$dtAcc[kodeAkun]','$getdate','AP','03','VP $dtAcc[keterangan]','$dtAcc[dpp]','0','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";
					#$sgltrn .= "('$dtAcc[nobukti]','$dtAkun[akun_ppn]','$getdate','AP','03','PPN BMHD $dtAcc[keterangan]','$dtAcc[ppn]','0','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";
					$sgltrn .= "('$dtAcc[nobukti]','$dtAkun[akun_ppn]','$getdate','AP','03','PPN $dtAcc[keterangan]','$dtAcc[ppn]','0','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";
					
					
					if ($dtAcc['tipematerai']=='E') {
						$sgltrn .= "('$dtAcc[nobukti]','$dtAcc[kodeAkunMaterai]','$getdate','AP','03','By Materai  $dtAcc[keterangan]','$dtAcc[materai]','0','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";
						$jml = ($dtAcc['dpp']+$dtAcc['ppn']+$dtAcc['materai']);
					} else {
						$jml = ($dtAcc['dpp']+$dtAcc['ppn']);
					}
					
					// pos biaya
					$spph = "select akun_pph,jns_pph,tarif_persen,nilai_pph, KeteranganAkun 
							from DataEvoPos 
							where nobukti = '".$dtAcc['nobukti']."' and jns_pph not in ('Non Pph')";
					$rpph = mssql_query($spph,$conns);
					while ($dpph = mssql_fetch_array($rpph)) {
						#$sgltrn .= "('$dtAcc[nobukti]','$dpph[akun_pph]','$dtAcc[tgl_pengajuan]','AP','03','$dpph[jns_pph] ($dpph[tarif_persen]%) $dtAcc[namaVendor] $dtAcc[keterangan]','0','$dpph[nilai_pph]','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";
						$sgltrn .= "('$dtAcc[nobukti]','$dpph[akun_pph]','$getdate','AP','03','$dpph[jns_pph] ($dpph[tarif_persen]%) $dtAcc[namaVendor] $dpph[KeteranganAkun]','0','$dpph[nilai_pph]','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";
					}
					
					#$sgltrn .= "('$dtAcc[nobukti]','$dtAkun[akun_biaya]','$dtAcc[tgl_pengajuan]','AP','03','DPP BMHD $dtAcc[keterangan]','0','$dtAcc[htg_stl_pajak]','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";
					#$sgltrn .= "('$dtAcc[nobukti]','$dtAkun[akun_biaya]','$getdate','AP','03','DPP BMHD $dtAcc[keterangan]','0','$dtAcc[htg_stl_pajak]','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";
					#$sgltrn .= "('$dtAcc[nobukti]','$dtAkun[akun_biaya]','$getdate','AP','03','DPP BMHD $dtAcc[keterangan]','0','$jml','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";
					$sgltrn .= "('$dtAcc[nobukti]','$dtAkun[akun_biaya]','$getdate','AP','03','BMHD $dtAcc[keterangan]','0','$dtAcc[htg_stl_pajak]','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";
					
					
				} else {
				
					if ($dtAcc['tipematerai']=='E') {
						$sgltrn .= "('$dtAcc[nobukti]','$dtAcc[kodeAkunMaterai]','$getdate','AP','03','By Materai $dtAcc[keterangan]','$dtAcc[materai]','0','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";
						$jml = ($dtAcc['dpp']+$dtAcc['ppn']+$dtAcc['materai']);
					
					} else {
						$jml = ($dtAcc['dpp']+$dtAcc['ppn']);
					}					
					
					#$sgltrn .= "('$dtAcc[nobukti]','$dtAcc[kodeAkun]','$dtAcc[tgl_pengajuan]','AP','03','VP $dtAcc[keterangan]','$jml','0','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";
					$sgltrn .= "('$dtAcc[nobukti]','$dtAcc[kodeAkun]','$getdate','AP','03','VP $dtAcc[keterangan]','$jml','0','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";
					
					// pos biaya
					$spph = "select akun_pph,jns_pph,tarif_persen,nilai_pph, KeteranganAkun 
							from DataEvoPos 
							where nobukti = '".$dtAcc['nobukti']."' and jns_pph not in ('Non Pph')";
					$rpph = mssql_query($spph,$conns);
					while ($dpph = mssql_fetch_array($rpph)) {
						#$sgltrn .= "('$dtAcc[nobukti]','$dpph[akun_pph]','$dtAcc[tgl_pengajuan]','AP','03','$dpph[jns_pph] ($dpph[tarif_persen]%) $dtAcc[namaVendor] $dtAcc[keterangan]','0','$dpph[nilai_pph]','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";
						$sgltrn .= "('$dtAcc[nobukti]','$dpph[akun_pph]','$getdate','AP','03','$dpph[jns_pph] ($dpph[tarif_persen]%) $dtAcc[namaVendor] $dpph[KeteranganAkun]','0','$dpph[nilai_pph]','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";
					}
					
					
					#$sgltrn .= "('$dtAcc[nobukti]','$dtAkun[akun_biaya]','$dtAcc[tgl_pengajuan]','AP','03','DPP BMHD $dtAcc[keterangan]','0','$dtAcc[htg_stl_pajak]','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";
					#$sgltrn .= "('$dtAcc[nobukti]','$dtAkun[akun_biaya]','$getdate','AP','03','DPP BMHD $dtAcc[keterangan]','0','$dtAcc[htg_stl_pajak]','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";
					#$sgltrn .= "('$dtAcc[nobukti]','$dtAkun[akun_biaya]','$getdate','AP','03','DPP BMHD $dtAcc[keterangan]','0','$jml','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";
					$sgltrn .= "('$dtAcc[nobukti]','$dtAkun[akun_biaya]','$getdate','AP','03','BMHD $dtAcc[keterangan]','0','$dtAcc[htg_stl_pajak]','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";
					
				}
				
				//echo "<pre>$sgltrn</pre>";
				
				
				//-------------------------------------------------------------------- aptrn
				if ($dtAcc['tipeppn']=='I') {
					if ($dtAcc['tipematerai']=='I' or $dtAcc['tipematerai']=='N') {
						$materai = $dtAcc['materai'];
						$persen = ($dtAcc['ppn'] / ($dtAcc['htg_stl_pajak'] - $materai)) * 100;				
					} else {
						$persen = ($dtAcc['ppn'] / $dtAcc['htg_stl_pajak']) * 100;		
					}
					//$persen = round($persen,2);
					
				} else if ($dtAcc['tipeppn']=='E') {
					if ($dtAcc['tipematerai']=='I' or $dtAcc['tipematerai']=='N') {
						$materai = $dtAcc['materai'];
						$persen = ($dtAcc['ppn'] / ($dtAcc['realisasi_nominal'] - $materai)) * 100;				
					} else {
						$persen = ($dtAcc['ppn'] / $dtAcc['realisasi_nominal']) * 100;		
					}
				}
				
				$saptrn .= "insert into [$table]..aptrn 
						(KodeLgn,NoFaktur,TypeTrn,NoBukti,TglTrn,TglJthTmp,TglTrnFaktur,TglJtpFaktur,
						Statusgiro,Keterangan,JumlahTrn,TglEntry,Kodeuser,Kodesumber,DelIndex,KodeJurnal) values";
				
				$sqlAp = "
					select kode_vendor as KodeLgn,NoFaktur,'O' as TypeTrn,a.nobukti,tgl_pengajuan as TglTrn,tgl_pengajuan as TglJthTmp,tgl_pengajuan as TglJtpFaktur,
					tgl_pengajuan as TglTrnFaktur,'C' as Statusgiro,b.Keterangan,(JumlahTrn*-1) as JumlahTrn,getdate() as TglEntry,userentry as Kodeuser,
					'AP' as Kodesumber,'$delIndex' as DelIndex,kodeAkun  as KodeJurnal, JumlahTrn as JumlahTrnAsli
					from DataEvoTagihan a
					inner join DataEvo b on a.nobukti=b.nobukti 
					where a.nobukti = '".$dtAcc['nobukti']."'";
					
				$rslAp = mssql_query($sqlAp,$conns);
				while ($dtAp = mssql_fetch_array($rslAp)) {
					#$saptrn .= "('$dtAp[KodeLgn]','$dtAp[NoFaktur]','$dtAp[TypeTrn]','$dtAp[nobukti]','$dtAp[TglTrn]','$dtAp[TglJthTmp]','$dtAp[TglJtpFaktur]','$dtAp[TglTrnFaktur]','$dtAp[Statusgiro]','VP $dtAp[Keterangan]','$dtAp[JumlahTrn]','$dtAp[TglEntry]','$dtAp[Kodeuser]','$dtAp[Kodesumber]','$dtAp[DelIndex]','$dtAp[KodeJurnal]'),";
					$jmlppn = 0;
					if ($dtAcc['tipeppn']=='I') {
						$jmlppn = round(($persen / 100) * $dtAp['JumlahTrnAsli']);
						$jmltrn = $dtAp['JumlahTrnAsli'] - $jmlppn;						
						$saptrn .= "('$dtAp[KodeLgn]','$dtAp[NoFaktur]','$dtAp[TypeTrn]','$dtAp[nobukti]','$getdate','$getdate','$getdate','$getdate','$dtAp[Statusgiro]','VP $dtAp[Keterangan]','-$jmltrn','$dtAp[TglEntry]','$dtAp[Kodeuser]','$dtAp[Kodesumber]','$dtAp[DelIndex]','$dtAp[KodeJurnal]'),";
						$saptrn .= "('$dtAcc[kode_vendor]','$dtAp[NoFaktur]','F','$dtAcc[nobukti]','$getdate','$getdate','$getdate','$getdate','C','PPN $dtAcc[keterangan]','-$jmlppn',getdate(),'$dtAcc[userentry]','AP','$delIndex','$dtAkun[akun_biaya]'),";
						
					} else if ($dtAcc['tipeppn']=='E') { 
						$jmlppn = round(($persen / 100) * $dtAp['JumlahTrnAsli']);
						$jmltrn = $dtAp['JumlahTrnAsli'];		
						
						$saptrn .= "('$dtAp[KodeLgn]','$dtAp[NoFaktur]','$dtAp[TypeTrn]','$dtAp[nobukti]','$getdate','$getdate','$getdate','$getdate','$dtAp[Statusgiro]','VP $dtAp[Keterangan]','$dtAp[JumlahTrn]','$dtAp[TglEntry]','$dtAp[Kodeuser]','$dtAp[Kodesumber]','$dtAp[DelIndex]','$dtAp[KodeJurnal]'),";
						$saptrn .= "('$dtAcc[kode_vendor]','$dtAp[NoFaktur]','F','$dtAcc[nobukti]','$getdate','$getdate','$getdate','$getdate','C','PPN $dtAcc[keterangan]','-$jmlppn',getdate(),'$dtAcc[userentry]','AP','$delIndex','$dtAkun[akun_biaya]'),";
					
					} else {
						$saptrn .= "('$dtAp[KodeLgn]','$dtAp[NoFaktur]','$dtAp[TypeTrn]','$dtAp[nobukti]','$dtAp[TglTrn]','$dtAp[TglJthTmp]','$dtAp[TglJtpFaktur]','$dtAp[TglTrnFaktur]','$dtAp[Statusgiro]','VP $dtAp[Keterangan]','$dtAp[JumlahTrn]','$dtAp[TglEntry]','$dtAp[Kodeuser]','$dtAp[Kodesumber]','$dtAp[DelIndex]','$dtAp[KodeJurnal]'),";
					
					}
					
				}
				
				#$saptrn .= "('$dtAcc[kode_vendor]','$dtAcc[nobukti]','C','$dtAcc[nobukti]','$dtAcc[tgl_pengajuan]','$dtAcc[tgl_pengajuan]','$dtAcc[tgl_pengajuan]','$dtAcc[tgl_pengajuan]','C','DPP BMHD $dtAcc[keterangan]','$dtAcc[htg_stl_pajak]',getdate(),'$dtAcc[userentry]','AP','$delIndex','$dtAkun[akun_biaya]'),";
				
				if ($dtAcc['tipematerai']=='E') {
					$jml = $dtAcc['htg_stl_pajak'] + $dtAcc['materai'];
					#$saptrn .= "('$dtAcc[kode_vendor]','$dtAcc[nobukti]','C','$dtAcc[nobukti]','$getdate','$getdate','$getdate','$getdate','C','DPP BMHD $dtAcc[keterangan]','$jml',getdate(),'$dtAcc[userentry]','AP','$delIndex','$dtAkun[akun_biaya]'),";
					$saptrn .= "('$dtAcc[kode_vendor]','$dtAcc[nobukti]','C','$dtAcc[nobukti]','$getdate','$getdate','$getdate','$getdate','C','BMHD $dtAcc[keterangan]','$jml',getdate(),'$dtAcc[userentry]','AP','$delIndex','$dtAkun[akun_biaya]'),";
				} else {
					#$saptrn .= "('$dtAcc[kode_vendor]','$dtAcc[nobukti]','C','$dtAcc[nobukti]','$getdate','$getdate','$getdate','$getdate','C','DPP BMHD $dtAcc[keterangan]','$dtAcc[htg_stl_pajak]',getdate(),'$dtAcc[userentry]','AP','$delIndex','$dtAkun[akun_biaya]'),";
					$saptrn .= "('$dtAcc[kode_vendor]','$dtAcc[nobukti]','C','$dtAcc[nobukti]','$getdate','$getdate','$getdate','$getdate','C','BMHD $dtAcc[keterangan]','$dtAcc[htg_stl_pajak]',getdate(),'$dtAcc[userentry]','AP','$delIndex','$dtAkun[akun_biaya]'),";
				}
				
				
				/*if ($dtAcc['tipeppn']=='E' or $dtAcc['tipeppn']=='I') {
					#$saptrn .= "('$dtAcc[kode_vendor]','$dtAcc[nobukti]','F','$dtAcc[nobukti]','$dtAcc[tgl_pengajuan]','$dtAcc[tgl_pengajuan]','$dtAcc[tgl_pengajuan]','$dtAcc[tgl_pengajuan]','C','PPN BMHD $dtAcc[keterangan]','$dtAcc[ppn]',getdate(),'$dtAcc[userentry]','AP','$delIndex','$dtAkun[akun_biaya]'),";
					#$saptrn .= "('$dtAcc[kode_vendor]','$dtAcc[nobukti]','F','$dtAcc[nobukti]','$getdate','$getdate','$getdate','$getdate','C','PPN BMHD $dtAcc[keterangan]','-$dtAcc[ppn]',getdate(),'$dtAcc[userentry]','AP','$delIndex','$dtAkun[akun_biaya]'),";
					$saptrn .= "('$dtAcc[kode_vendor]','$dtAcc[nobukti]','F','$dtAcc[nobukti]','$getdate','$getdate','$getdate','$getdate','C','PPN $dtAcc[keterangan]','-$dtAcc[ppn]',getdate(),'$dtAcc[userentry]','AP','$delIndex','$dtAkun[akun_biaya]'),";
					
				}*/
				
				$spph = "
						select akun_pph,jns_pph,tarif_persen,nilai_pph, keteranganAkun,
						(select DISTINCT kodelgn from settingPph where jns_pph=a.jns_pph) as kodelgn 
						from DataEvoPos a
						where nobukti = '".$dtAcc['nobukti']."' and jns_pph not in ('Non Pph')";
				$rpph = mssql_query($spph,$conns);
				while ($dpph = mssql_fetch_array($rpph)) {
					#$saptrn .= "('$dpph[kodelgn]','$dtAcc[nobukti]','F','$dtAcc[nobukti]','$dtAcc[tgl_pengajuan]','$dtAcc[tgl_pengajuan]','$dtAcc[tgl_pengajuan]','$dtAcc[tgl_pengajuan]','C','$dpph[jns_pph] ($dpph[tarif_persen]%) $dtAcc[namaVendor] $dtAcc[keterangan]','$dpph[nilai_pph]',getdate(),'$dtAcc[userentry]','AP','$delIndex','$dtAkun[$akunppn]'),";
					$saptrn .= "('$dpph[kodelgn]','$dtAcc[nobukti]','F','$dtAcc[nobukti]','$getdate','$getdate','$getdate','$getdate','C','$dpph[jns_pph] ($dpph[tarif_persen]%) $dtAcc[namaVendor] $dtAcc[keterangan]','$dpph[nilai_pph]',getdate(),'$dtAcc[userentry]','AP','$delIndex','$dtAkun[$akunppn]'),";
				}				
				$sglmst = true;
				
				#echo "<pre>$saptrn</pre>";
				
				
			} else if ($dtAcc['tipe']=='BIAYA') {
				
				include '../inc/conn.php';
		
				// gltrn
				$sgltrn .= "insert into [$table]..gltrn (NoBukti,KodeGl,TglTrn,KodeSumber,TypeTrn,Keterangan,JlhDebit,JlhKredit,DelIndex,LastUpdated,KodeUser,KodeLgn,SumberForm,NoFPS) 
					values";
					
				$spos = "select pos_biaya as kodeGl,nominal,SUBSTRING(ketAkun, 12, len(ketAkun)) as ketAkun, KeteranganAkun 
						 from DataEvoPos where nobukti = '".$nobukti."'";
				$rpos = mssql_query($spos,$conns);
				while ($dpos = mssql_fetch_array($rpos)) {
					$nominal = $dpos['nominal'];
					#$sgltrn .= "('$dtAcc[nobukti]','$dpos[kodeGl]','$dtAcc[tgl_pengajuan]','AP','03','VP $dtAcc[keterangan]','$nominal','0','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";
					$sgltrn .= "('$dtAcc[nobukti]','$dpos[kodeGl]','$getdate','AP','03','VP $dpos[KeteranganAkun]','$nominal','0','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";
				}

				if ($dtAcc['is_ppn']=='1') {
					#$sgltrn .= "('$dtAcc[nobukti]','$dtAkun[akun_ppn]','$dtAcc[tgl_pengajuan]','AP','03','PPN $dtAcc[keterangan]','$dtAcc[ppn]','0','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";	
					$sgltrn .= "('$dtAcc[nobukti]','$dtAkun[akun_ppn]','$getdate','AP','03','PPN $dtAcc[keterangan]','$dtAcc[ppn]','0','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";	
				}

				// pos biaya
				$spph = "select akun_pph,jns_pph,tarif_persen,nilai_pph,KeteranganAkun
						from DataEvoPos 
						where nobukti = '".$nobukti."' and jns_pph not in ('Non Pph')";
				$rpph = mssql_query($spph,$conns);
				while ($dpph = mssql_fetch_array($rpph)) {
					#$sgltrn .= "('$dtAcc[nobukti]','$dpph[akun_pph]','$dtAcc[tgl_pengajuan]','AP','03','$dpph[jns_pph] ($dpph[tarif_persen]%) $dtAcc[namaVendor] $dtAcc[keterangan]','0','$dpph[nilai_pph]','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";
					$sgltrn .= "('$dtAcc[nobukti]','$dpph[akun_pph]','$getdate','AP','03','$dpph[jns_pph] ($dpph[tarif_persen]%) $dtAcc[namaVendor] $dtAcc[keterangan]','0','$dpph[nilai_pph]','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";
				}
				
				#$sgltrn .= "('$dtAcc[nobukti]','$dtAkun[akun_biaya]','$dtAcc[tgl_pengajuan]','AP','03','BMHD $dtAcc[keterangan]','0','$dtAcc[biaya_yg_dibyar]','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";
				$sgltrn .= "('$dtAcc[nobukti]','$dtAkun[akun_biaya]','$getdate','AP','03','BMHD $dtAcc[keterangan]','0','$dtAcc[biaya_yg_dibyar]','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";



				// aptrn
				$saptrn .= "insert into [$table]..aptrn 
					(KodeLgn,NoFaktur,TypeTrn,NoBukti,TglTrn,TglJthTmp,TglTrnFaktur,TglJtpFaktur,
					Statusgiro,Keterangan,JumlahTrn,TglEntry,Kodeuser,Kodesumber,DelIndex,KodeJurnal) values";
				
				#$saptrn .= "('$dtAcc[kode_vendor]','$dtAcc[nobukti]','C','$dtAcc[nobukti]','$dtAcc[tgl_pengajuan]','$dtAcc[tgl_pengajuan]','$dtAcc[tgl_pengajuan]','$dtAcc[tgl_pengajuan]','C','DPP BMHD $dtAcc[keterangan]',$dtAcc[biaya_yg_dibyar]-$dtAcc[ppn],getdate(),'$dtAcc[userentry]','AP','$delIndex','$dtAkun[akun_biaya]'),";
				$saptrn .= "('$dtAcc[kode_vendor]','$dtAcc[nobukti]','C','$dtAcc[nobukti]','$getdate','$getdate','$getdate','$getdate','C','DPP BMHD $dtAcc[keterangan]',$dtAcc[biaya_yg_dibyar]-$dtAcc[ppn],getdate(),'$dtAcc[userentry]','AP','$delIndex','$dtAkun[akun_biaya]'),";
				
				
				if ($dtAcc['is_ppn']=='1') {
					#$saptrn .= "('$dtAcc[kode_vendor]','$dtAcc[nobukti]','F','$dtAcc[nobukti]','$dtAcc[tgl_pengajuan]','$dtAcc[tgl_pengajuan]','$dtAcc[tgl_pengajuan]','$dtAcc[tgl_pengajuan]','C','PPN BMHD $dtAcc[keterangan]','$dtAcc[ppn]',getdate(),'$dtAcc[userentry]','AP','$delIndex','$dtAkun[akun_biaya]'),";	
					$saptrn .= "('$dtAcc[kode_vendor]','$dtAcc[nobukti]','F','$dtAcc[nobukti]','$getdate','$getdate','$getdate','$getdate','C','PPN $dtAcc[keterangan]','$dtAcc[ppn]',getdate(),'$dtAcc[userentry]','AP','$delIndex','$dtAkun[akun_biaya]'),";	
				}

				$spph = "
					select akun_pph,jns_pph,tarif_persen,nilai_pph,keteranganAkun, 
					(select DISTINCT kodelgn from settingPph where jns_pph=a.jns_pph) as kodelgn 
					from DataEvoPos a
					where nobukti = '".$dtAcc['nobukti']."' and jns_pph not in ('Non Pph')";
				$rpph = mssql_query($spph,$conns);
				while ($dpph = mssql_fetch_array($rpph)) {
					#$saptrn .= "('$dpph[kodelgn]','$dtAcc[nobukti]','F','$dtAcc[nobukti]','$dtAcc[tgl_pengajuan]','$dtAcc[tgl_pengajuan]','$dtAcc[tgl_pengajuan]','$dtAcc[tgl_pengajuan]','C','$dpph[jns_pph] ($dpph[tarif_persen]%) $dtAcc[namaVendor] $dtAcc[keterangan]','$dpph[nilai_pph]',getdate(),'$dtAcc[userentry]','AP','$delIndex','$dpph[akun_pph]'),";
					$saptrn .= "('$dpph[kodelgn]','$dtAcc[nobukti]','F','$dtAcc[nobukti]','$getdate','$getdate','$getdate','$getdate','C','$dpph[jns_pph] ($dpph[tarif_persen]%) $dtAcc[namaVendor] $dtAcc[keterangan]','$dpph[nilai_pph]',getdate(),'$dtAcc[userentry]','AP','$delIndex','$dpph[akun_pph]'),";
					
				}
			}
			
			$sgltrn = substr($sgltrn, 0,strlen($sgltrn)-1);
			$saptrn = substr($saptrn, 0,strlen($saptrn)-1);
			
			echo "<pre>".$sgltrn."</pre>";
			echo "<pre>".$saptrn."</pre>";
			
			mssql_query("BEGIN TRAN",$connCab);
				
				$qry1 = mssql_query($sgltrn,$connCab);
				$qry2 = mssql_query($saptrn,$connCab);
				
				if ($dtAcc['tipe']=='BIAYA') {
					$sgl = "select KodeGl,(JlhDebit-JlhKredit) as jumlah from [$table]..Gltrn where NoBukti='$dtAcc[nobukti]'";
					$rgl = mssql_query($sgl,$connCab);
					while ($dgl=mssql_fetch_array($rgl)) {
						if ($dgl['jumlah']>0) {
							$sglmst .= "
								update [$table]..Glmst set JBulanini = JBulanini + $dgl[jumlah], JDebitBi = JDebitBi + $dgl[jumlah] 
								where KodeGl='$dgl[KodeGl]'
							";
						} else {
							$sglmst .= "
								update [$table]..Glmst set JBulanini = JBulanini + $dgl[jumlah], JKreditBi = JKreditBi + Abs($dgl[jumlah]) 
								where KodeGl='$dgl[KodeGl]'
							";
						}
					}
					$qry3 = mssql_query($sglmst,$connCab);
				} else {
					$qry3 = true;
				}
			
				if ($qry1 and $qry2) {
					if ($dtAcc['tipe']=='BIAYA') {
						$cekBalancing = mssql_fetch_array(mssql_query("
							select sum(JlhDebit) as Debit,sum(JlhKredit) as Kredit from [$table]..Gltrn where NoBukti='".$nobukti."'
						",$connCab));
						if ($cekBalancing['Debit']==$cekBalancing['Kredit']) {
							mssql_query("COMMIT TRAN",$connCab);
							$pesan = true; // "Transaksi telah berhasil di Accept!";
						} else {
							//$pesan .= $sgltrn;
							mssql_query("ROLLBACK TRAN",$connCab);
							$pesan = false; // "Failed!! Not Balance!";
						}
					} else {
					
						
						//mssql_query("COMMIT TRAN");
						//$pesan .= true; // "Transaksi telah berhasil di Accept!";
						$cekBalancing = mssql_fetch_array(mssql_query("
							select sum(JlhDebit) as Debit,sum(JlhKredit) as Kredit from [$table]..Gltrn where NoBukti='".$nobukti."'
						",$connCab));
						if ($cekBalancing['Debit']==$cekBalancing['Kredit']) {
							mssql_query("COMMIT TRAN",$connCab);
							$pesan = true; // "Transaksi telah berhasil di Accept!";
						} else {
							$pesan .= $sgltrn;
							mssql_query("ROLLBACK TRAN",$connCab);
							$pesan = false; // "Failed!! Not Balance!";
						}
					}
				} else {
					#if (!$qry1) { $pesan .= "Failed Query 1 ACC!".$sgltrn; }
					#if (!$qry2) { $pesan .= "Failed Query 2 ACC!".$saptrn; }
					#if (!$qry3) { $pesan .= "Failed Query 3 ACC!".$sglmst; }
					$pesan = false; 
					mssql_query("ROLLBACK TRAN",$connCab);
				}
			mssql_query("return",$connCab);
			// echo $sgltrn;
			// echo "<br>";
			// echo "<br>";
			// echo $saptrn;
		}
		include '../inc/conn.php';
		return $pesan;
	}
	
	function insertAcc($nobukti){
	
		include '../inc/conn.php';
		$dtAcc = mssql_fetch_array(mssql_query("select * from DataEvo where nobukti = '".$nobukti."'",$conns));
		$dtAkun = mssql_fetch_array(mssql_query("select * from settingAkun where id=1",$conns));
		if ($dtAcc['kodedealer']=='2010') { $posisi = "HO"; } else { $posisi = "Dealer"; }
		$dtppn = mssql_fetch_array(mssql_query("select jnsPpn from sys_hutang where nama = '".$dtAcc['tipehutang']."' and posisi = '".$posisi."'",$conns));
		$akunppn = $dtppn['jnsPpn'];
		$sgltrn = ""; $saptrn = ""; $sglmst = "";
		$delIndex = "AP".rand(0,99999999);
		$KodeDealer = $dtAcc['kodedealer'];
		include '../inc/koneksi.php';
		$pesan = "";
		
		if ($msg=='0') {
			$pesan = false; // "Gagal Koneksi Cabang!";
		} else if ($msg=='1') {
			$pesan = false; // "Gagal Koneksi HO!";
		} else if (!mssql_select_db("[$table]",$connCab)) {
			$pesan = false; // "Database tidak tersedia!";
			
		} else if ($msg=='3') {
			$getdate = date('Y-m-d');
			
			if ($dtAcc['tipe']=='HUTANG') {
				
				include '../inc/conn.php';
								
				//--------------------------------------- gltrn
				$sgltrn .= "
					insert into [$table]..gltrn (NoBukti,KodeGl,TglTrn,KodeSumber,TypeTrn,Keterangan,JlhDebit,JlhKredit,DelIndex,LastUpdated,KodeUser,KodeLgn,SumberForm,NoFPS) 
					values
				";
				
				if ($dtAcc['tipeppn']=='E' or $dtAcc['tipeppn']=='I') {
					#$sgltrn .= "('$dtAcc[nobukti]','$dtAcc[kodeAkun]','$dtAcc[tgl_pengajuan]','AP','03','VP $dtAcc[keterangan]','$dtAcc[dpp]','0','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";
					#$sgltrn .= "('$dtAcc[nobukti]','$dtAkun[$akunppn]','$dtAcc[tgl_pengajuan]','AP','03','PPN BMHD $dtAcc[keterangan]','$dtAcc[ppn]','0','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";
					
					$sgltrn .= "('$dtAcc[nobukti]','$dtAcc[kodeAkun]','$getdate','AP','03','VP $dtAcc[keterangan]','$dtAcc[dpp]','0','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";
					#$sgltrn .= "('$dtAcc[nobukti]','$dtAkun[akun_ppn]','$getdate','AP','03','PPN BMHD $dtAcc[keterangan]','$dtAcc[ppn]','0','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";
					$sgltrn .= "('$dtAcc[nobukti]','$dtAkun[akun_ppn]','$getdate','AP','03','PPN $dtAcc[keterangan]','$dtAcc[ppn]','0','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";
					
					
					if ($dtAcc['tipematerai']=='E') {
						$sgltrn .= "('$dtAcc[nobukti]','$dtAcc[kodeAkunMaterai]','$getdate','AP','03','By Materai  $dtAcc[keterangan]','$dtAcc[materai]','0','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";
						$jml = ($dtAcc['dpp']+$dtAcc['ppn']+$dtAcc['materai']);
					} else {
						$jml = ($dtAcc['dpp']+$dtAcc['ppn']);
					}
					
					// pos biaya
					$spph = "select akun_pph,jns_pph,tarif_persen,nilai_pph, KeteranganAkun 
							from DataEvoPos 
							where nobukti = '".$dtAcc['nobukti']."' and jns_pph not in ('Non Pph')";
					$rpph = mssql_query($spph,$conns);
					while ($dpph = mssql_fetch_array($rpph)) {
						#$sgltrn .= "('$dtAcc[nobukti]','$dpph[akun_pph]','$dtAcc[tgl_pengajuan]','AP','03','$dpph[jns_pph] ($dpph[tarif_persen]%) $dtAcc[namaVendor] $dtAcc[keterangan]','0','$dpph[nilai_pph]','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";
						$sgltrn .= "('$dtAcc[nobukti]','$dpph[akun_pph]','$getdate','AP','03','$dpph[jns_pph] ($dpph[tarif_persen]%) $dtAcc[namaVendor] $dpph[KeteranganAkun]','0','$dpph[nilai_pph]','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";
					}
					
					#$sgltrn .= "('$dtAcc[nobukti]','$dtAkun[akun_biaya]','$dtAcc[tgl_pengajuan]','AP','03','DPP BMHD $dtAcc[keterangan]','0','$dtAcc[htg_stl_pajak]','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";
					#$sgltrn .= "('$dtAcc[nobukti]','$dtAkun[akun_biaya]','$getdate','AP','03','DPP BMHD $dtAcc[keterangan]','0','$dtAcc[htg_stl_pajak]','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";
					#$sgltrn .= "('$dtAcc[nobukti]','$dtAkun[akun_biaya]','$getdate','AP','03','DPP BMHD $dtAcc[keterangan]','0','$jml','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";
					$sgltrn .= "('$dtAcc[nobukti]','$dtAkun[akun_biaya]','$getdate','AP','03','BMHD $dtAcc[keterangan]','0','$dtAcc[htg_stl_pajak]','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";
					
					
				} else {
				
					if ($dtAcc['tipematerai']=='E') {
						$sgltrn .= "('$dtAcc[nobukti]','$dtAcc[kodeAkunMaterai]','$getdate','AP','03','By Materai $dtAcc[keterangan]','$dtAcc[materai]','0','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";
						$jml = ($dtAcc['dpp']+$dtAcc['ppn']+$dtAcc['materai']);
					
					} else {
						$jml = ($dtAcc['dpp']+$dtAcc['ppn']);
					}					
					
					#$sgltrn .= "('$dtAcc[nobukti]','$dtAcc[kodeAkun]','$dtAcc[tgl_pengajuan]','AP','03','VP $dtAcc[keterangan]','$jml','0','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";
					$sgltrn .= "('$dtAcc[nobukti]','$dtAcc[kodeAkun]','$getdate','AP','03','VP $dtAcc[keterangan]','$jml','0','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";
					
					// pos biaya
					$spph = "select akun_pph,jns_pph,tarif_persen,nilai_pph, KeteranganAkun 
							from DataEvoPos 
							where nobukti = '".$dtAcc['nobukti']."' and jns_pph not in ('Non Pph')";
					$rpph = mssql_query($spph,$conns);
					while ($dpph = mssql_fetch_array($rpph)) {
						#$sgltrn .= "('$dtAcc[nobukti]','$dpph[akun_pph]','$dtAcc[tgl_pengajuan]','AP','03','$dpph[jns_pph] ($dpph[tarif_persen]%) $dtAcc[namaVendor] $dtAcc[keterangan]','0','$dpph[nilai_pph]','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";
						$sgltrn .= "('$dtAcc[nobukti]','$dpph[akun_pph]','$getdate','AP','03','$dpph[jns_pph] ($dpph[tarif_persen]%) $dtAcc[namaVendor] $dpph[KeteranganAkun]','0','$dpph[nilai_pph]','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";
					}
					
					
					#$sgltrn .= "('$dtAcc[nobukti]','$dtAkun[akun_biaya]','$dtAcc[tgl_pengajuan]','AP','03','DPP BMHD $dtAcc[keterangan]','0','$dtAcc[htg_stl_pajak]','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";
					#$sgltrn .= "('$dtAcc[nobukti]','$dtAkun[akun_biaya]','$getdate','AP','03','DPP BMHD $dtAcc[keterangan]','0','$dtAcc[htg_stl_pajak]','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";
					#$sgltrn .= "('$dtAcc[nobukti]','$dtAkun[akun_biaya]','$getdate','AP','03','DPP BMHD $dtAcc[keterangan]','0','$jml','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";
					$sgltrn .= "('$dtAcc[nobukti]','$dtAkun[akun_biaya]','$getdate','AP','03','BMHD $dtAcc[keterangan]','0','$dtAcc[htg_stl_pajak]','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";
					
				}
				
				//echo "<pre>$sgltrn</pre>";
				
				
				//-------------------------------------------------------------------- aptrn
				if ($dtAcc['tipeppn']=='I') {
					if ($dtAcc['tipematerai']=='I' or $dtAcc['tipematerai']=='N') {
						$materai = $dtAcc['materai'];
						$persen = ($dtAcc['ppn'] / ($dtAcc['htg_stl_pajak'] - $materai)) * 100;				
					} else {
						$persen = ($dtAcc['ppn'] / $dtAcc['htg_stl_pajak']) * 100;		
					}
					//$persen = round($persen,2);
					
				} else if ($dtAcc['tipeppn']=='E') {
					if ($dtAcc['tipematerai']=='I' or $dtAcc['tipematerai']=='N') {
						$materai = $dtAcc['materai'];
						$persen = ($dtAcc['ppn'] / ($dtAcc['realisasi_nominal'] - $materai)) * 100;				
					} else {
						$persen = ($dtAcc['ppn'] / $dtAcc['realisasi_nominal']) * 100;		
					}
				}
				
			
				$sqlAp = "
					select kode_vendor as KodeLgn,NoFaktur,'O' as TypeTrn,a.nobukti,tgl_pengajuan as TglTrn,tgl_pengajuan as TglJthTmp,tgl_pengajuan as TglJtpFaktur,
					tgl_pengajuan as TglTrnFaktur,'C' as Statusgiro,b.Keterangan,(JumlahTrn*-1) as JumlahTrn,getdate() as TglEntry,userentry as Kodeuser,
					'AP' as Kodesumber,'$delIndex' as DelIndex,kodeAkun  as KodeJurnal, JumlahTrn as JumlahTrnAsli
					from DataEvoTagihan a
					inner join DataEvo b on a.nobukti=b.nobukti 
					where a.nobukti = '".$dtAcc['nobukti']."'";
					
				$rslAp = mssql_query($sqlAp,$conns);
				while ($dtAp = mssql_fetch_array($rslAp)) {
					
					#$saptrn .= "('$dtAp[KodeLgn]','$dtAp[NoFaktur]','$dtAp[TypeTrn]','$dtAp[nobukti]','$dtAp[TglTrn]','$dtAp[TglJthTmp]','$dtAp[TglJtpFaktur]','$dtAp[TglTrnFaktur]','$dtAp[Statusgiro]','VP $dtAp[Keterangan]','$dtAp[JumlahTrn]','$dtAp[TglEntry]','$dtAp[Kodeuser]','$dtAp[Kodesumber]','$dtAp[DelIndex]','$dtAp[KodeJurnal]'),";
					$jmlppn = 0;
					if ($dtAcc['tipeppn']=='I') {
						$jmlppn = round(($persen / 100) * $dtAp['JumlahTrnAsli']);
						$jmltrn = $dtAp['JumlahTrnAsli'] - $jmlppn;	

						$saptrn .= "insert into [$table]..aptrn 
									(KodeLgn,NoFaktur,TypeTrn,NoBukti,TglTrn,TglJthTmp,TglTrnFaktur,TglJtpFaktur,
									Statusgiro,Keterangan,JumlahTrn,TglEntry,Kodeuser,Kodesumber,DelIndex,KodeJurnal) values
									('$dtAp[KodeLgn]','$dtAp[NoFaktur]','$dtAp[TypeTrn]','$dtAp[nobukti]','$getdate','$getdate','$getdate','$getdate','$dtAp[Statusgiro]','VP $dtAp[Keterangan]','-$jmltrn','$dtAp[TglEntry]','$dtAp[Kodeuser]','$dtAp[Kodesumber]','$dtAp[DelIndex]','$dtAp[KodeJurnal]');";

						$saptrn .= "insert into [$table]..aptrn 
									(KodeLgn,NoFaktur,TypeTrn,NoBukti,TglTrn,TglJthTmp,TglTrnFaktur,TglJtpFaktur,
									Statusgiro,Keterangan,JumlahTrn,TglEntry,Kodeuser,Kodesumber,DelIndex,KodeJurnal) values
									('$dtAcc[kode_vendor]','$dtAp[NoFaktur]','F','$dtAcc[nobukti]','$getdate','$getdate','$getdate','$getdate','C','PPN $dtAcc[keterangan]','-$jmlppn',getdate(),'$dtAcc[userentry]','AP','$delIndex','$dtAkun[akun_biaya]');";
						
					} else if ($dtAcc['tipeppn']=='E') { 
						$jmlppn = round(($persen / 100) * $dtAp['JumlahTrnAsli']);
						$jmltrn = $dtAp['JumlahTrnAsli'];		
						
						$saptrn .= "insert into [$table]..aptrn 
									(KodeLgn,NoFaktur,TypeTrn,NoBukti,TglTrn,TglJthTmp,TglTrnFaktur,TglJtpFaktur,
									Statusgiro,Keterangan,JumlahTrn,TglEntry,Kodeuser,Kodesumber,DelIndex,KodeJurnal) values
									('$dtAp[KodeLgn]','$dtAp[NoFaktur]','$dtAp[TypeTrn]','$dtAp[nobukti]','$getdate','$getdate','$getdate','$getdate','$dtAp[Statusgiro]','VP $dtAp[Keterangan]','$dtAp[JumlahTrn]','$dtAp[TglEntry]','$dtAp[Kodeuser]','$dtAp[Kodesumber]','$dtAp[DelIndex]','$dtAp[KodeJurnal]');";
						
						$saptrn .= "insert into [$table]..aptrn 
								(KodeLgn,NoFaktur,TypeTrn,NoBukti,TglTrn,TglJthTmp,TglTrnFaktur,TglJtpFaktur,
								Statusgiro,Keterangan,JumlahTrn,TglEntry,Kodeuser,Kodesumber,DelIndex,KodeJurnal) values
								('$dtAcc[kode_vendor]','$dtAp[NoFaktur]','F','$dtAcc[nobukti]','$getdate','$getdate','$getdate','$getdate','C','PPN $dtAcc[keterangan]','-$jmlppn',getdate(),'$dtAcc[userentry]','AP','$delIndex','$dtAkun[akun_biaya]');";
					
					} else {
						$saptrn .= "insert into [$table]..aptrn 
									(KodeLgn,NoFaktur,TypeTrn,NoBukti,TglTrn,TglJthTmp,TglTrnFaktur,TglJtpFaktur,
									Statusgiro,Keterangan,JumlahTrn,TglEntry,Kodeuser,Kodesumber,DelIndex,KodeJurnal) values
									('$dtAp[KodeLgn]','$dtAp[NoFaktur]','$dtAp[TypeTrn]','$dtAp[nobukti]','$dtAp[TglTrn]','$dtAp[TglJthTmp]','$dtAp[TglJtpFaktur]','$dtAp[TglTrnFaktur]','$dtAp[Statusgiro]','VP $dtAp[Keterangan]','$dtAp[JumlahTrn]','$dtAp[TglEntry]','$dtAp[Kodeuser]','$dtAp[Kodesumber]','$dtAp[DelIndex]','$dtAp[KodeJurnal]');";
					
					}
					
				}
				
				#$saptrn .= "('$dtAcc[kode_vendor]','$dtAcc[nobukti]','C','$dtAcc[nobukti]','$dtAcc[tgl_pengajuan]','$dtAcc[tgl_pengajuan]','$dtAcc[tgl_pengajuan]','$dtAcc[tgl_pengajuan]','C','DPP BMHD $dtAcc[keterangan]','$dtAcc[htg_stl_pajak]',getdate(),'$dtAcc[userentry]','AP','$delIndex','$dtAkun[akun_biaya]'),";
				
				if ($dtAcc['tipematerai']=='E') {
					$jml = $dtAcc['htg_stl_pajak'] + $dtAcc['materai'];
					#$saptrn .= "('$dtAcc[kode_vendor]','$dtAcc[nobukti]','C','$dtAcc[nobukti]','$getdate','$getdate','$getdate','$getdate','C','DPP BMHD $dtAcc[keterangan]','$jml',getdate(),'$dtAcc[userentry]','AP','$delIndex','$dtAkun[akun_biaya]'),";
					$saptrn .= "insert into [$table]..aptrn 
						(KodeLgn,NoFaktur,TypeTrn,NoBukti,TglTrn,TglJthTmp,TglTrnFaktur,TglJtpFaktur,
						Statusgiro,Keterangan,JumlahTrn,TglEntry,Kodeuser,Kodesumber,DelIndex,KodeJurnal) values
						('$dtAcc[kode_vendor]','$dtAcc[nobukti]','C','$dtAcc[nobukti]','$getdate','$getdate','$getdate','$getdate','C','BMHD $dtAcc[keterangan]','$jml',getdate(),'$dtAcc[userentry]','AP','$delIndex','$dtAkun[akun_biaya]');";
				} else {
					#$saptrn .= "('$dtAcc[kode_vendor]','$dtAcc[nobukti]','C','$dtAcc[nobukti]','$getdate','$getdate','$getdate','$getdate','C','DPP BMHD $dtAcc[keterangan]','$dtAcc[htg_stl_pajak]',getdate(),'$dtAcc[userentry]','AP','$delIndex','$dtAkun[akun_biaya]'),";
					$saptrn .= "insert into [$table]..aptrn 
						(KodeLgn,NoFaktur,TypeTrn,NoBukti,TglTrn,TglJthTmp,TglTrnFaktur,TglJtpFaktur,
						Statusgiro,Keterangan,JumlahTrn,TglEntry,Kodeuser,Kodesumber,DelIndex,KodeJurnal) values
						('$dtAcc[kode_vendor]','$dtAcc[nobukti]','C','$dtAcc[nobukti]','$getdate','$getdate','$getdate','$getdate','C','BMHD $dtAcc[keterangan]','$dtAcc[htg_stl_pajak]',getdate(),'$dtAcc[userentry]','AP','$delIndex','$dtAkun[akun_biaya]');";
				}
				
				
				/*if ($dtAcc['tipeppn']=='E' or $dtAcc['tipeppn']=='I') {
					#$saptrn .= "('$dtAcc[kode_vendor]','$dtAcc[nobukti]','F','$dtAcc[nobukti]','$dtAcc[tgl_pengajuan]','$dtAcc[tgl_pengajuan]','$dtAcc[tgl_pengajuan]','$dtAcc[tgl_pengajuan]','C','PPN BMHD $dtAcc[keterangan]','$dtAcc[ppn]',getdate(),'$dtAcc[userentry]','AP','$delIndex','$dtAkun[akun_biaya]'),";
					#$saptrn .= "('$dtAcc[kode_vendor]','$dtAcc[nobukti]','F','$dtAcc[nobukti]','$getdate','$getdate','$getdate','$getdate','C','PPN BMHD $dtAcc[keterangan]','-$dtAcc[ppn]',getdate(),'$dtAcc[userentry]','AP','$delIndex','$dtAkun[akun_biaya]'),";
					$saptrn .= "('$dtAcc[kode_vendor]','$dtAcc[nobukti]','F','$dtAcc[nobukti]','$getdate','$getdate','$getdate','$getdate','C','PPN $dtAcc[keterangan]','-$dtAcc[ppn]',getdate(),'$dtAcc[userentry]','AP','$delIndex','$dtAkun[akun_biaya]'),";
					
				}*/
				
				$spph = "
						select akun_pph,jns_pph,tarif_persen,nilai_pph, keteranganAkun,
						(select DISTINCT kodelgn from settingPph where jns_pph=a.jns_pph) as kodelgn 
						from DataEvoPos a
						where nobukti = '".$dtAcc['nobukti']."' and jns_pph not in ('Non Pph')";
				$rpph = mssql_query($spph,$conns);
				while ($dpph = mssql_fetch_array($rpph)) {
					#$saptrn .= "('$dpph[kodelgn]','$dtAcc[nobukti]','F','$dtAcc[nobukti]','$dtAcc[tgl_pengajuan]','$dtAcc[tgl_pengajuan]','$dtAcc[tgl_pengajuan]','$dtAcc[tgl_pengajuan]','C','$dpph[jns_pph] ($dpph[tarif_persen]%) $dtAcc[namaVendor] $dtAcc[keterangan]','$dpph[nilai_pph]',getdate(),'$dtAcc[userentry]','AP','$delIndex','$dtAkun[$akunppn]'),";
					$saptrn .= "insert into [$table]..aptrn 
						(KodeLgn,NoFaktur,TypeTrn,NoBukti,TglTrn,TglJthTmp,TglTrnFaktur,TglJtpFaktur,
						Statusgiro,Keterangan,JumlahTrn,TglEntry,Kodeuser,Kodesumber,DelIndex,KodeJurnal) values
						('$dpph[kodelgn]','$dtAcc[nobukti]','F','$dtAcc[nobukti]','$getdate','$getdate','$getdate','$getdate','C','$dpph[jns_pph] ($dpph[tarif_persen]%) $dtAcc[namaVendor] $dtAcc[keterangan]','$dpph[nilai_pph]',getdate(),'$dtAcc[userentry]','AP','$delIndex','$dtAkun[$akunppn]');";
				}				
				$sglmst = true;
				
				#echo "<pre>$saptrn</pre>";
				
				
			} else if ($dtAcc['tipe']=='BIAYA') {
				
				include '../inc/conn.php';
		
				// gltrn
				$sgltrn .= "insert into [$table]..gltrn (NoBukti,KodeGl,TglTrn,KodeSumber,TypeTrn,Keterangan,JlhDebit,JlhKredit,DelIndex,LastUpdated,KodeUser,KodeLgn,SumberForm,NoFPS) 
					values";
					
				$spos = "select pos_biaya as kodeGl,nominal,SUBSTRING(ketAkun, 12, len(ketAkun)) as ketAkun, KeteranganAkun 
						 from DataEvoPos where nobukti = '".$nobukti."'";
				$rpos = mssql_query($spos,$conns);
				while ($dpos = mssql_fetch_array($rpos)) {
					$nominal = $dpos['nominal'];
					#$sgltrn .= "('$dtAcc[nobukti]','$dpos[kodeGl]','$dtAcc[tgl_pengajuan]','AP','03','VP $dtAcc[keterangan]','$nominal','0','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";
					$sgltrn .= "('$dtAcc[nobukti]','$dpos[kodeGl]','$getdate','AP','03','VP $dpos[KeteranganAkun]','$nominal','0','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";
				}

				if ($dtAcc['is_ppn']=='1') {
					#$sgltrn .= "('$dtAcc[nobukti]','$dtAkun[akun_ppn]','$dtAcc[tgl_pengajuan]','AP','03','PPN $dtAcc[keterangan]','$dtAcc[ppn]','0','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";	
					$sgltrn .= "('$dtAcc[nobukti]','$dtAkun[akun_ppn]','$getdate','AP','03','PPN $dtAcc[keterangan]','$dtAcc[ppn]','0','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";	
				}

				// pos biaya
				$spph = "select akun_pph,jns_pph,tarif_persen,nilai_pph,KeteranganAkun
						from DataEvoPos 
						where nobukti = '".$nobukti."' and jns_pph not in ('Non Pph')";
				$rpph = mssql_query($spph,$conns);
				while ($dpph = mssql_fetch_array($rpph)) {
					#$sgltrn .= "('$dtAcc[nobukti]','$dpph[akun_pph]','$dtAcc[tgl_pengajuan]','AP','03','$dpph[jns_pph] ($dpph[tarif_persen]%) $dtAcc[namaVendor] $dtAcc[keterangan]','0','$dpph[nilai_pph]','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";
					$sgltrn .= "('$dtAcc[nobukti]','$dpph[akun_pph]','$getdate','AP','03','$dpph[jns_pph] ($dpph[tarif_persen]%) $dtAcc[namaVendor] $dtAcc[keterangan]','0','$dpph[nilai_pph]','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";
				}
				
				#$sgltrn .= "('$dtAcc[nobukti]','$dtAkun[akun_biaya]','$dtAcc[tgl_pengajuan]','AP','03','BMHD $dtAcc[keterangan]','0','$dtAcc[biaya_yg_dibyar]','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";
				$sgltrn .= "('$dtAcc[nobukti]','$dtAkun[akun_biaya]','$getdate','AP','03','BMHD $dtAcc[keterangan]','0','$dtAcc[biaya_yg_dibyar]','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','EvoPay','$dtAcc[no_fj]'),";



				// aptrn
				$saptrn .= "insert into [$table]..aptrn 
					(KodeLgn,NoFaktur,TypeTrn,NoBukti,TglTrn,TglJthTmp,TglTrnFaktur,TglJtpFaktur,
					Statusgiro,Keterangan,JumlahTrn,TglEntry,Kodeuser,Kodesumber,DelIndex,KodeJurnal) values";
				
				#$saptrn .= "('$dtAcc[kode_vendor]','$dtAcc[nobukti]','C','$dtAcc[nobukti]','$dtAcc[tgl_pengajuan]','$dtAcc[tgl_pengajuan]','$dtAcc[tgl_pengajuan]','$dtAcc[tgl_pengajuan]','C','DPP BMHD $dtAcc[keterangan]',$dtAcc[biaya_yg_dibyar]-$dtAcc[ppn],getdate(),'$dtAcc[userentry]','AP','$delIndex','$dtAkun[akun_biaya]'),";
				$saptrn .= "('$dtAcc[kode_vendor]','$dtAcc[nobukti]','C','$dtAcc[nobukti]','$getdate','$getdate','$getdate','$getdate','C','DPP BMHD $dtAcc[keterangan]',$dtAcc[biaya_yg_dibyar]-$dtAcc[ppn],getdate(),'$dtAcc[userentry]','AP','$delIndex','$dtAkun[akun_biaya]'),";
				
				
				if ($dtAcc['is_ppn']=='1') {
					#$saptrn .= "('$dtAcc[kode_vendor]','$dtAcc[nobukti]','F','$dtAcc[nobukti]','$dtAcc[tgl_pengajuan]','$dtAcc[tgl_pengajuan]','$dtAcc[tgl_pengajuan]','$dtAcc[tgl_pengajuan]','C','PPN BMHD $dtAcc[keterangan]','$dtAcc[ppn]',getdate(),'$dtAcc[userentry]','AP','$delIndex','$dtAkun[akun_biaya]'),";	
					$saptrn .= "('$dtAcc[kode_vendor]','$dtAcc[nobukti]','F','$dtAcc[nobukti]','$getdate','$getdate','$getdate','$getdate','C','PPN $dtAcc[keterangan]','$dtAcc[ppn]',getdate(),'$dtAcc[userentry]','AP','$delIndex','$dtAkun[akun_biaya]'),";	
				}

				$spph = "
					select akun_pph,jns_pph,tarif_persen,nilai_pph,keteranganAkun, 
					(select DISTINCT kodelgn from settingPph where jns_pph=a.jns_pph) as kodelgn 
					from DataEvoPos a
					where nobukti = '".$dtAcc['nobukti']."' and jns_pph not in ('Non Pph')";
				$rpph = mssql_query($spph,$conns);
				while ($dpph = mssql_fetch_array($rpph)) {
					#$saptrn .= "('$dpph[kodelgn]','$dtAcc[nobukti]','F','$dtAcc[nobukti]','$dtAcc[tgl_pengajuan]','$dtAcc[tgl_pengajuan]','$dtAcc[tgl_pengajuan]','$dtAcc[tgl_pengajuan]','C','$dpph[jns_pph] ($dpph[tarif_persen]%) $dtAcc[namaVendor] $dtAcc[keterangan]','$dpph[nilai_pph]',getdate(),'$dtAcc[userentry]','AP','$delIndex','$dpph[akun_pph]'),";
					$saptrn .= "('$dpph[kodelgn]','$dtAcc[nobukti]','F','$dtAcc[nobukti]','$getdate','$getdate','$getdate','$getdate','C','$dpph[jns_pph] ($dpph[tarif_persen]%) $dtAcc[namaVendor] $dtAcc[keterangan]','$dpph[nilai_pph]',getdate(),'$dtAcc[userentry]','AP','$delIndex','$dpph[akun_pph]'),";
					
				}
			}
			
			$sgltrn = substr($sgltrn, 0,strlen($sgltrn)-1);
			$saptrn = substr($saptrn, 0,strlen($saptrn)-1);
			
			#echo "<pre>".$sgltrn."</pre>";
			#echo "<pre>".$saptrn."</pre>";
			
			mssql_query("BEGIN TRAN",$connCab);
				
				$qry1 = mssql_query($sgltrn,$connCab);
				$qry2 = mssql_query($saptrn,$connCab);
				
				if ($dtAcc['tipe']=='BIAYA') {
					$sgl = "select KodeGl,(JlhDebit-JlhKredit) as jumlah from [$table]..Gltrn where NoBukti='$dtAcc[nobukti]'";
					$rgl = mssql_query($sgl,$connCab);
					while ($dgl=mssql_fetch_array($rgl)) {
						if ($dgl['jumlah']>0) {
							$sglmst .= "
								update [$table]..Glmst set JBulanini = JBulanini + $dgl[jumlah], JDebitBi = JDebitBi + $dgl[jumlah] 
								where KodeGl='$dgl[KodeGl]'
							";
						} else {
							$sglmst .= "
								update [$table]..Glmst set JBulanini = JBulanini + $dgl[jumlah], JKreditBi = JKreditBi + Abs($dgl[jumlah]) 
								where KodeGl='$dgl[KodeGl]'
							";
						}
					}
					$qry3 = mssql_query($sglmst,$connCab);
				} else {
					$qry3 = true;
				}
			
				if ($qry1 and $qry2) {
					if ($dtAcc['tipe']=='BIAYA') {
						$cekBalancing = mssql_fetch_array(mssql_query("
							select sum(JlhDebit) as Debit,sum(JlhKredit) as Kredit from [$table]..Gltrn where NoBukti='".$nobukti."'
						",$connCab));
						if ($cekBalancing['Debit']==$cekBalancing['Kredit']) {
							mssql_query("COMMIT TRAN",$connCab);
							$pesan = true; // "Transaksi telah berhasil di Accept!";
						} else {
							//$pesan .= $sgltrn;
							mssql_query("ROLLBACK TRAN",$connCab);
							$pesan = false; // "Failed!! Not Balance!";
						}
					} else {
					
						
						//mssql_query("COMMIT TRAN");
						//$pesan .= true; // "Transaksi telah berhasil di Accept!";
						$cekBalancing = mssql_fetch_array(mssql_query("
							select sum(JlhDebit) as Debit,sum(JlhKredit) as Kredit from [$table]..Gltrn where NoBukti='".$nobukti."'
						",$connCab));
						if ($cekBalancing['Debit']==$cekBalancing['Kredit']) {
							mssql_query("COMMIT TRAN",$connCab);
							$pesan = true; // "Transaksi telah berhasil di Accept!";
						} else {
							$pesan .= $sgltrn;
							mssql_query("ROLLBACK TRAN",$connCab);
							$pesan = false; // "Failed!! Not Balance!";
						}
					}
				} else {
					#if (!$qry1) { $pesan .= "Failed Query 1 ACC!".$sgltrn; }
					#if (!$qry2) { $pesan .= "Failed Query 2 ACC!".$saptrn; }
					#if (!$qry3) { $pesan .= "Failed Query 3 ACC!".$sglmst; }
					$pesan = false; 
					mssql_query("ROLLBACK TRAN",$connCab);
				}
			mssql_query("return",$connCab);
			// echo $sgltrn;
			// echo "<br>";
			// echo "<br>";
			// echo $saptrn;
		}
		include '../inc/conn.php';
		return $pesan;
	}
	// function insertAcc($nobukti){
	// 	$pesan = false;
	// 	echo $pesan;
	// }
	// $nextlvl = getLevel($div['divisi'],$KodeDealer,$nobukti,$urutan,$is_dealer);
	function getLevel($divisi,$kodedealer,$nobukti,$urutan,$is_dealer, $level_aju){
		include '../inc/conn.php';
		$sql = "
			select top 1 nama_lvl from (
				select urutan,nama_lvl, case when urutan='8' then '1' when urutan='6' and is_dealer='1' then '1' else
				(select top 1 IdUser from sys_user where tipe = a.nama_lvl and (divisi = '".$divisi."' or divisi = 'all') and KodeDealer = '".$kodedealer."' order by IdUser) end as IdAtasan 
				from sys_level a 
				left join DataEvoVal b on a.nama_lvl=b.level and nobukti = '".$nobukti."'
				where is_dealer = '".$is_dealer."' and urutan in (".$urutan.") and ISNULL(nobukti, '')=''  and isnull(deptterkait,'')= ''
				and nama_lvl != '".$level_aju."'
			) x order by urutan
		";
		//echo "<pre>$sql</pre>";
		// where ISNULL(IdAtasan, '')<>''
		$rsl = mssql_query($sql);
		$dta = mssql_fetch_array($rsl);
		return $dta['nama_lvl'];
	}
	
	function getLevelDeptTerkait($deptterkait, $nobukti, $level, $urutan, $divisi, $kodedealer, $is_dealer){
		include '../inc/conn.php';
		
		/*if ($level!='TAX') {
			$where = " and a.level = '".$level."'";
		}*/
		$qry_val = mssql_query("select top 1 tipe
								from DeptTerkait a
								inner join sys_user b on a.IdUser = b.IdUser										
								inner join sys_level c on b.tipe = c.nama_lvl and c.is_dealer = 0
								where b.department = '".$deptterkait."' 
								and a.IdUser not in (
									select b.IdUser
									from DataEvoVal a
									inner join sys_user b on a.uservalidasi = b.IdUser
									where a.nobukti = '".$nobukti."' and isnull(a.validasi,'') != '')
								order by levelvalidator ");
								
		/*		
		$qry_val = mssql_query("select a.userentry, b.levelvalidator, 
								(select COUNT(*) from DeptTerkait a
								inner join sys_user b on a.IdUser = b.IdUser										
								inner join sys_level c on b.tipe = c.nama_lvl and c.is_dealer = 0
								where b.department = '".$deptterkait."' 
								) jml
								from DataEvo a
								inner join DeptTerkait b on a.userentry = b.IdUser
								where nobukti = '".$nobukti."'
								order by b.levelvalidator");
		$dt_val = mssql_fetch_array($qry_val);
		$jml_lvl = $dt_val['jml'];
		$levelvalidator_lvl = $dt_val['jlevelvalidatorml'];
		
		if ($jml_lvl==$levelvalidator_lvl) {
			$tipe = getLeveldept($deptterkait,$divisi,$kodedealer,$nobukti,$urutan,$is_dealer);	
			$hasil = array("jml"=>0, "tipe"=>$tipe);	
				
		} else {
			$qry_val = mssql_query("select top 1 tipe
								from DeptTerkait a
								inner join sys_user b on a.IdUser = b.IdUser										
								inner join sys_level c on b.tipe = c.nama_lvl and c.is_dealer = 0
								where b.department = '".$deptterkait."' 
								and a.IdUser not in (
									select b.IdUser
									from DataEvoVal a
									inner join sys_user b on a.uservalidasi = b.IdUser
									where a.nobukti = '".$nobukti."' and isnull(a.validasi,'') != '')
								order by levelvalidator ");
								
			$jml = mssql_num_rows($qry_val);
			$hasil = array();
				
			if ($jml==0) {
				$tipe = getLeveldept($deptterkait,$divisi,$kodedealer,$nobukti,$urutan,$is_dealer);	
				$hasil = array("jml"=>0, "tipe"=>$tipe);		
			} else {
				$tipe = $dt_val['tipe'];
				$hasil = array("jml"=>1, "tipe"=>$tipe);	
			}
		}*/
		/*$qry_val = mssql_query("select tipe
								from DeptTerkait a
								inner join sys_user b on a.IdUser = b.IdUser										
								inner join sys_level c on b.tipe = c.nama_lvl and c.is_dealer = 0
								where b.department = '".$deptterkait."' 
								and b.tipe not in (
									select a.level
									from DataEvoVal a
									where a.nobukti = '".$nobukti."' and a.deptterkait =  '".$deptterkait."' )
								order by levelvalidator ");*/
														
		$dt_val = mssql_fetch_array($qry_val);
		$jml = mssql_num_rows($qry_val);
		$hasil = array();
			
		if ($jml==0) {
			$tipe = getLeveldept($deptterkait,$divisi,$kodedealer,$nobukti,$urutan,$is_dealer);	
			$hasil = array("jml"=>0, "tipe"=>$tipe);		
		} else {
			$tipe = $dt_val['tipe'];
			$hasil = array("jml"=>1, "tipe"=>$tipe);	
		}
		return $hasil;
	}
	
	function getLeveldept($deptterkait,$divisi,$kodedealer,$nobukti,$urutan,$is_dealer){
		include '../inc/conn.php';
		$sql = "
			select top 1 nama_lvl from (
				select urutan,nama_lvl, case when urutan='8' then '1' when urutan='6' and is_dealer='1' then '1' else
				(select top 1 IdUser from sys_user where tipe = a.nama_lvl and (divisi = '".$divisi."' or divisi = 'all') and KodeDealer = '".$kodedealer."' order by IdUser) end as IdAtasan 
				from sys_level a 
				left join DataEvoVal b on a.nama_lvl=b.level and nobukti = '".$nobukti."' and isnull(b.deptterkait,'') != '".$deptterkait."'
				where is_dealer = '".$is_dealer."' and urutan in (".$urutan.") and ISNULL(nobukti, '')=''  
			) x order by urutan
		";
		//echo "<pre>$sql</pre>";
		// where ISNULL(IdAtasan, '')<>''
		$rsl = mssql_query($sql);
		$dta = mssql_fetch_array($rsl);
		return $dta['nama_lvl'];
	}
	
	// $urutan = getUrutan($level,$is_dealer, $batas_direksi1, $batas_direksi2);
	/*
	$user_aju = mssql_fetch_array(mssql_query("select divisi,department, tipe, idatasan, idstatus from sys_user where IdUser = '".$div['userentry']."'"));
	$level_aju = $user_aju['tipe'];
	$user_entry = $div['userentry'];
	*/		
	function getUrutan($level,$is_dealer, $batas_direksi1, $batas_direksi2, $IdUser, $deptterkait, $level_aju, $nobukti){
		include '../inc/conn.php';
		
		/*$qry = mssql_query("select urutan from sys_level where nama_lvl = '".$level."' and is_aktif = 1 and is_dealer = '".$is_dealer."' ");
		$dt = mssql_fetch_array($qry);
		$urut = $dt['urutan'];
		*/
		
		$where_direksi1 = "";
		if ($batas_direksi1==0) {
			$where_direksi1 = "and nama_lvl != 'DIREKSI'";
		}
		
		$where_direksi2 = "";
		if ($batas_direksi2==0) {
			$where_direksi2 = "and nama_lvl != 'DIREKSI 2'";
		}
		
		//$urutan = "2,3,";
		/*if (!empty($deptterkait) and $deptterkait!='') {
			if ($level == "ACCOUNTING") {
				$stm = "select urutan, nama_lvl from sys_level 
					where urutan > isnull((select urutan from sys_level where nama_lvl = 'ACCOUNTING' and is_aktif = 1 and is_dealer = '".$is_dealer."'),0)
					and is_aktif = 1 and is_dealer = 0 $where_direksi1 $where_direksi2 
					order by urutan";
			} else {
				$stm = "select urutan, nama_lvl from sys_level 
					where urutan > isnull((select urutan from sys_level where nama_lvl = '".$level."' and is_aktif = 1 and is_dealer = '".$is_dealer."'),0)
					and is_aktif = 1 and is_dealer = 0 $where_direksi1 $where_direksi2 
					order by urutan";
			}
		
		} else {
			$stm = "select urutan, nama_lvl from sys_level 
					where urutan > isnull((select urutan from sys_level where nama_lvl = '".$level."' and is_aktif = 1 and is_dealer = '".$is_dealer."'),0)
					and is_aktif = 1 and is_dealer = 0 $where_direksi1 $where_direksi2 
					order by urutan";
		}*/
		
		if ($deptterkait!='') {
			//echo $level_aju."".$level;
			if ($level_aju=="KASIR") {
				$level_aju = "ADMIN";
			}
			
			if ($level_aju!="ADMIN" and $level=="ACCOUNTING") {
				$stm = "select urutan, nama_lvl from sys_level 
					where urutan > isnull((select urutan from sys_level where nama_lvl = '".$level_aju."' and is_aktif = 1 and is_dealer = '".$is_dealer."'),0)
					and is_aktif = 1 and is_dealer = 0 $where_direksi1 $where_direksi2 
					order by urutan";
					
			} else {
				$sql_val = mssql_query("select top 1 level from DataEvoVal where nobukti = '".$nobukti."' and  isnull(deptterkait,'') = '' 
										order by tglentry desc");
				$dt_val = mssql_fetch_array($sql_val);
				
				$level_val = $dt_val['level'];	
				
				if ($level_val=='KASIR') {
					if ($level=="DIREKSI") {
						$stm = "select urutan, nama_lvl from sys_level 
								where urutan > isnull((select urutan from sys_level where nama_lvl = '".$level."' and is_aktif = 1 and is_dealer = '".$is_dealer."'),0)
								and is_aktif = 1 and is_dealer = 0 $where_direksi1 $where_direksi2 
								order by urutan";
					}	
				} else {
					$stm = "select urutan, nama_lvl from sys_level 
						where urutan > isnull((select urutan from sys_level where nama_lvl = '".$level_val."' and is_aktif = 1 and is_dealer = '".$is_dealer."'),0)
						and is_aktif = 1 and is_dealer = 0 $where_direksi1 $where_direksi2 
						order by urutan";
				}
			}
			//echo "<pre>$stm</pre>";	
		} else {
			/*$stm = "select urutan, nama_lvl from sys_level 
				where urutan > isnull((select urutan from sys_level where nama_lvl = '".$level."' and is_aktif = 1 and is_dealer = '".$is_dealer."'),0)
				and is_aktif = 1 and is_dealer = 0 $where_direksi1 $where_direksi2 
				order by urutan";		
			*/
			if ($level_aju!="ADMIN" and $level=="ACCOUNTING") {
				$stm = "select urutan, nama_lvl from sys_level 
					where urutan > isnull((select urutan from sys_level where nama_lvl = '".$level_aju."' and is_aktif = 1 and is_dealer = '".$is_dealer."'),0)
					and is_aktif = 1 and is_dealer = 0 $where_direksi1 $where_direksi2 
					order by urutan";
					
			} else {
				$sql_val = mssql_query("select top 1 level from DataEvoVal where nobukti = '".$nobukti."' and  isnull(deptterkait,'') = '' 
										order by tglentry desc");
				$dt_val = mssql_fetch_array($sql_val);
				
				$level_val = $dt_val['level'];	
				
				if ($level_val=='KASIR') {
					if ($level=="DIREKSI") {
						$stm = "select urutan, nama_lvl from sys_level 
								where urutan > isnull((select urutan from sys_level where nama_lvl = '".$level."' and is_aktif = 1 and is_dealer = '".$is_dealer."'),0)
								and is_aktif = 1 and is_dealer = 0 $where_direksi1 $where_direksi2 
								order by urutan";
					}	
				} else {
					$stm = "select urutan, nama_lvl from sys_level 
						where urutan > isnull((select urutan from sys_level where nama_lvl = '".$level_val."' and is_aktif = 1 and is_dealer = '".$is_dealer."'),0)
						and is_aktif = 1 and is_dealer = 0 $where_direksi1 $where_direksi2 
						order by urutan";
				}
			}
			//echo "<pre>$stm</pre>";
		}
		
		//echo "<pre>$stm</pre>";
		
		$qry = mssql_query($stm);
		while ($dt = mssql_fetch_array($qry)) {
			/*
			1	Aktif
			2	Non Aktif
			3	ByPass
			4	Concurrent
			*/
			/*$nama_lvl = $dt['nama_lvl'];
			
			$sqlatasan = "
						select  a.idstatus, a.tipe, (select b.idstatus from sys_user b where b.IdUser = a.IdAtasan) statusatasan, 
						a.IdAtasan, (select b.tipe from sys_user b where b.IdUser = a.IdAtasan) tipeatasan, 
						(select c.IdAtasan from sys_user c where c.IdUser = a.IdAtasan) idatasan2, 
						(select d.tipe from sys_user d where d.IdUser in (select c.IdAtasan from sys_user c where c.IdUser = a.IdAtasan)) tipeatasan2,
						 
						(select c.IdAtasan from sys_user c where c.IdUser in (select c.IdAtasan from sys_user c where c.IdUser = a.IdAtasan)) idatasan3,
						(select d.tipe from sys_user d 
							where d.IdUser in (select c.IdAtasan from sys_user c where c.IdUser 
								in (select c.IdAtasan from sys_user c where c.IdUser = a.IdAtasan))) tipeatasan3
						
						from sys_user a where a.IdUser = '".$IdUser."'";
			$user_aju = mssql_fetch_array(mssql_query($sqlatasan));
			//echo "<pre>$sqlatasan</pre>";
						
			$status_atasan = $user_aju['statusatasan'];
			$tipe_atasan = $user_aju['tipeatasan'];
			$atasan = $user_aju['IdAtasan'];
			
			if ($nama_lvl==$tipe_atasan) {
				if ($tipe_atasan=='SECTION HEAD' or $tipe_atasan=='DEPT. HEAD'  or $tipe_atasan=='DIV. HEAD') {
					if ($status_atasan==2 or $status_atasan==3 or $status_atasan==4) {
						//echo "_msk_".$dt['urutan'];
					} else {
						$urutan .= $dt['urutan'].",";
					}				
				} else {
					$urutan .= $dt['urutan'].",";
				}
			} else {
				$urutan .= $dt['urutan'].",";
			}*/
			//$user_entry = $atasan_user;	
			$urutan .= $dt['urutan'].",";
		}
		$urutan = substr($urutan,0,strlen($urutan)-1);
		return $urutan;
	}
	
?>