<?php
	session_start();
	error_reporting(0);
	$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : null;
	$IdUser = isset($_REQUEST['IdUser']) ? $_REQUEST['IdUser'] : null;
	
	if ($action=='proval') {
		require_once ('../inc/conn.php');
		$level = $_REQUEST['level'];
		
		// Cabang
		/*$txt .= proval('ACCOUNTING',$IdUser)."#";
		$txt .= proval('SECTION HEAD',$IdUser)."#";
		$txt .= proval('ADH',$IdUser)."#";
		$txt .= proval('KEPALA CABANG',$IdUser)."#";
		$txt .= proval('KASIR',$IdUser)."#";
		*/
		// HO
		if (!empty($level)) {
			if ($level =='DEPT. HEAD FINANCE DIV. HEAD FAST') {
				$txt = proval('DEPT. HEAD FINANCE / DIV. HEAD FAST',$IdUser);
			} else {
				$txt = proval($level,$IdUser);
			}
		} else {
			$txt = 0;
		}
		echo $txt;
		
	} else if ($action=='provalold') {
		require_once ('../inc/conn.php');
		// Cabang
		$txt .= proval('ACCOUNTING',$IdUser)."#";
		$txt .= proval('SECTION HEAD',$IdUser)."#";
		$txt .= proval('ADH',$IdUser)."#";
		$txt .= proval('KEPALA CABANG',$IdUser)."#";
		$txt .= proval('KASIR',$IdUser)."#";
		// HO
		$txt .= proval('TAX',$IdUser)."#";
		$txt .= proval('ACCOUNTING',$IdUser)."#";
		$txt .= proval('SECTION HEAD',$IdUser)."#";
		$txt .= proval('DEPT. HEAD',$IdUser)."#";
		$txt .= proval('DIREKSI',$IdUser)."#";
		
		//if ($_SESSION['level']=="DIREKSI 2") {
		$txt .= proval('DIREKSI 2',$IdUser)."#";
		//} else {
		//}
		
		$txt .= proval('FINANCE',$IdUser)."#";
		$txt .= proval('DEPT. HEAD FINANCE',$IdUser)."#";
		$txt .= proval('DEPT. HEAD FINANCE / DIV. HEAD FAST',$IdUser)."#";
		$txt .= proval('DIV. HEAD',$IdUser)."#";
		
		$txt .= proval('WAITDEPTTERKAIT',$IdUser)."#";
		$txt .= proval('REQDEPTTERKAIT',$IdUser)."#";
		echo $txt;
		
		
	} else if ($action=='prodash') {
		require_once ('../inc/conn.php');
		// Cabang
		$txt .= prodash('SECTION HEAD','AFTER SALES',$IdUser)."#";
		$txt .= prodash('SECTION HEAD','SALES',$IdUser)."#";
		$txt .= prodash('ADH','all',$IdUser)."#";
		$txt .= prodash('KEPALA CABANG','all',$IdUser)."#";
		$txt .= prodash('ACCOUNTING','all',$IdUser)."#";
		$txt .= prodash('KASIR','all',$IdUser)."#";
		$txt .= prodash('all','all',$IdUser)."#";
		// HO
		$txt .= prodash('TAX','all',$IdUser)."#"; //7
		$txt .= prodash('SECTION HEAD','all',$IdUser)."#";
		$txt .= prodash('DEPT. HEAD','all',$IdUser)."#";
		$txt .= prodash('DIV. HEAD','all',$IdUser)."#";
		$txt .= prodash('DIREKSI','all',$IdUser)."#";
		$txt .= prodash('FINANCE','all',$IdUser)."#";
		$txt .= prodash('DEPT. HEAD FINANCE','all',$IdUser)."#";
		$txt .= prodash('DEPT. HEAD FINANCE / DIV. HEAD FAST','all',$IdUser)."#";
		$txt .= prodash('KASIR','all',$IdUser)."#";
		$txt .= prodash('ACCOUNTING','all',$IdUser)."#";
		$txt .= prodash('DIREKSI 2','all',$IdUser)."#";
		
		$txt .= prodash('all','all',$IdUser)."#"; // 17
		$txt .= proval('DEPTTERKAIT','all',$IdUser);
		echo $txt;
		
	} else if ($action=='profund') {
		require_once ('../inc/conn.php');
		echo profund(0,$IdUser)."#".profund(1,$IdUser)."#".profund(2,$IdUser);
		
	} else if ($action=='getLevel') {
		require_once ('../inc/conn.php');
		$kodedealer = isset($_REQUEST['kodedealer']) ? $_REQUEST['kodedealer'] : null;
		$value = isset($_REQUEST['value']) ? $_REQUEST['value'] : null;
		if ($kodedealer=='2010') { $is_dealer = "0"; $wom = ""; } else { $is_dealer = "1"; $wom = "and nama_lvl not in ('OM')"; }
		if ($kodedealer!='') {
			$sql = "select * from sys_level where is_dealer = '".$is_dealer."' and is_aktif=1 $wom order by urutan asc";
			$rsl = mssql_query($sql);
			echo "<option value=''>- Pilih -</option>";
			while ($dt = mssql_fetch_array($rsl)) {
				$pilih = ($value==$dt['nama_lvl'])?"selected" : ""; 
				echo "<option value='".$dt['nama_lvl']."' $pilih>".$dt['nama_lvl']."</option>";
			}
		} else {
			echo "";
		}
		
	} else if ($action=='getDivisi') {
		require_once ('../inc/conn.php');
		$kodedealer = isset($_REQUEST['kodedealer']) ? $_REQUEST['kodedealer'] : null;
		$tipe = isset($_REQUEST['tipe']) ? $_REQUEST['tipe'] : null;
		$value = isset($_REQUEST['value']) ? $_REQUEST['value'] : null;
		if ($kodedealer=='2010') { $is_dealer = "0"; } else { $is_dealer = "1"; }
		$cek = mssql_fetch_array(mssql_query("select is_alldiv from sys_level where is_dealer = '".$is_dealer."' and nama_lvl = '".$tipe."'"));
		if ($tipe!='') {
			if ($cek['is_alldiv']=='0') {
				$plh = ($value=='all')?"selected" : ""; 
				$sql = "select * from sys_divisi where is_dealer = '".$is_dealer."' and is_aktif = 1";
				$rsl = mssql_query($sql);
				echo "<option value=''>- Pilih -</option>";
				if ($kodedealer=='2010') { echo ""; } else { echo "<option value='all' $plh>ALL</option>"; }
				while ($dt = mssql_fetch_array($rsl)) {
					$pilih = ($value==$dt['nama_div'])?"selected" : ""; 
					echo "<option value='".$dt['nama_div']."' $pilih>".$dt['nama_div']."</option>";
				}
			} else if ($cek['is_alldiv']=='1') {
				echo "<option value='all'>ALL</option>";
			}
		} else {
			echo "";
		}
	} else if ($action=='getDepartment') {
		require_once ('../inc/conn.php');
		$kodedealer = isset($_REQUEST['kodedealer']) ? $_REQUEST['kodedealer'] : null;
		$tipe = isset($_REQUEST['tipe']) ? $_REQUEST['tipe'] : null;
		$divisi = isset($_REQUEST['divisi']) ? $_REQUEST['divisi'] : null;
		$value = isset($_REQUEST['value']) ? $_REQUEST['value'] : null;

		$sql = "select nama_dept from sys_department a inner join sys_divisi b on a.id_sys_div=b.id_sys_div
				where nama_div = '".$divisi."'";
		$rsl = mssql_query($sql);
		echo "<option value=''>- Pilih -</option>";
		echo "<option value='all' $plh>ALL</option>";
		while ($dt = mssql_fetch_array($rsl)) {
			$pilih = ($value==$dt['nama_dept'])?"selected" : ""; 
			echo "<option value='".$dt['nama_dept']."' $pilih>".$dt['nama_dept']."</option>";
		}
	
	} else if ($action=='getUser') {
		require_once ('../inc/conn.php');
		$kodedealer = isset($_REQUEST['kodedealer']) ? $_REQUEST['kodedealer'] : null;
		$divisi = isset($_REQUEST['divisi']) ? $_REQUEST['divisi'] : null;
		$dept = isset($_REQUEST['department']) ? $_REQUEST['department'] : null;
		$atasan = isset($_REQUEST['atasan']) ? $_REQUEST['atasan'] : NULL;
		$value = isset($_REQUEST['value']) ? $_REQUEST['value'] : null;

		$sql = "select iduser, namauser 
				from sys_user a 
				inner join sys_divisi b on a.divisi=b.nama_div
				where a.divisi = '".$divisi."' and a.department = '".$dept."'";
		if ($atasan!=NULL) {
			$sql .= " and idatasan = '".$atasan."'";
		}		
		$rsl = mssql_query($sql);
		echo "<option value=''>- Pilih -</option>";
		while ($dt = mssql_fetch_array($rsl)) {
			$pilih = ($value==$dt['iduser'])?"selected" : ""; 
			echo "<option value='".$dt['iduser']."' $pilih>".$dt['namauser']."</option>";
		}
	
	
	} else if ($action=='getStatusUser') {
		require_once ('../inc/conn.php');
		$kodedealer = isset($_REQUEST['kodedealer']) ? $_REQUEST['kodedealer'] : null;
		$tipe = isset($_REQUEST['tipe']) ? $_REQUEST['tipe'] : null;
		$value = isset($_REQUEST['value']) ? $_REQUEST['value'] : null;

		$sql = "select distinct a.id,a.NamaStatus from StatusUser a
				inner join StatusUserlevel b on a.id = b.idstatus
				inner join sys_level c on b.id_sys_lvl = c.id_sys_lvl";
		if ($tipe!=NULL) {
			$sql .= " where c.nama_lvl in ('".$tipe."')";
		}	
		$rsl = mssql_query($sql);
		echo "<option value=''>- Pilih -</option>";
		while ($dt = mssql_fetch_array($rsl)) {
			$pilih = "";
			if (strtolower($row['NamaStatus'])=='aktif') {
				$pilih = "selected";
			} 
			if ($value==$dt['id']) {
				$pilih = "selected";
			} else {
				if (strtolower($row['NamaStatus'])=='aktif') {
					$pilih = "selected";
				} 
			}
			
			echo "<option value='".$dt['id']."' $pilih>".$dt['NamaStatus']."</option>";
		}
		
	} else if ($action=='getAtasan') {
		require_once ('../inc/conn.php');
		$kodedealer = isset($_REQUEST['kodedealer']) ? $_REQUEST['kodedealer'] : null;
		$tipe = isset($_REQUEST['tipe']) ? $_REQUEST['tipe'] : null;
		$divisi = isset($_REQUEST['divisi']) ? $_REQUEST['divisi'] : null;
		$department = isset($_REQUEST['department']) ? $_REQUEST['department'] : null;
		$value = isset($_REQUEST['value']) ? $_REQUEST['value'] : null;
		$plh = ($value=='all')?"selected" : ""; 
		if ($department!='') { $dept = "and department in ('".$department."','all') "; } else { $dept = ""; }
		if (($kodedealer=='2010' and $department!='') or ($kodedealer!='2010' and $tipe!='')) {
			$sql = "
				select * from sys_user where KodeDealer = '".$kodedealer."' 
				and divisi in ('".$divisi."','all') and tipe not in ('".$tipe."') 
				and ISNULL(isDel,'')='' $dept
			";
			// $sql = "
			// 	select * from sys_user where KodeDealer = '".$kodedealer."' 
			// 	and divisi in ('".$divisi."','all') and ISNULL(isDel,'')='' 
			// 	and tipe in (select nama_lvl from (
			// 	select ROW_NUMBER() OVER(ORDER BY urutan ASC) AS Row,nama_lvl from sys_level 
			// 	where is_dealer = 0 and is_aktif = 1 
			// 	) x where Row = (select (row+1) id from (select ROW_NUMBER() OVER(ORDER BY urutan ASC) AS Row,nama_lvl from sys_level 
			// 	where is_dealer = 0 and is_aktif = 1 ) x where nama_lvl='".$tipe."'))
			// 	$dept
			// ";
			// echo $sql;
			$rsl = mssql_query($sql);
			echo "<option value=''>- Pilih -</option>";
			echo "<option value='all' $plh>ALL</option>";
			while ($dt = mssql_fetch_array($rsl)) {
				$pilih = ($value==$dt['IdUser'])?"selected" : ""; 
				echo "<option value='".$dt['IdUser']."' $pilih>".$dt['namaUser']."</option>";
			}
		} else {
			echo "";
		}
		
	} else {
		$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
		$rp = isset($_REQUEST['rp']) ? $_REQUEST['rp'] : 20;
		$sortname = isset($_REQUEST['sortname']) ? $_REQUEST['sortname'] : 'tgl_pengajuan';
		$sortorder = isset($_REQUEST['sortorder']) ? $_REQUEST['sortorder'] : 'asc';
		$query = isset($_REQUEST['query']) ? $_REQUEST['query'] : false;
		$qtype = isset($_REQUEST['qtype']) ? $_REQUEST['qtype'] : false;
		
		$IdUser = isset($_REQUEST['IdUser']) ? $_REQUEST['IdUser'] : NULL;
		$level = isset($_REQUEST['level']) ? $_REQUEST['level'] : NULL;
		$sesi_level = isset($_REQUEST['sesi_level']) ? $_REQUEST['sesi_level'] : NULL;		
		$searchText = isset($_REQUEST['searchText']) ? $_REQUEST['searchText'] : NULL;

		require_once ('../inc/conn.php');

		$page = $_REQUEST['page'];
		$rp = $_REQUEST['rp'];
		$sortname = $_REQUEST['sortname'];
		$sortorder = $_REQUEST['sortorder'];
		
		if (!$sortname) $sortname = 'tgl_pengajuan';
		if (!$sortorder) $sortorder = 'asc';
		$sort = "ORDER BY $sortname $sortorder";
		if ($searchText!='') {
			$src = " and (a.nobukti like '%".$searchText."%' or tgl_pengajuan like '%".$searchText."%' or userentry like '%".$searchText."%' or NamaDealer like '%".$searchText."%' or namaVendor like '%".$searchText."%' or keterangan like '%".$searchText."%')";
		} else {
			$src = "";
		}

		if (!$page) $page = 1;
		if (!$rp) $rp = 10;
		$start = (($page-1) * $rp);
		
		$user = mssql_fetch_array(mssql_query("select * from sys_user where IdUser = '".$IdUser."'"));
		
		if ($user['tipe']=='ADMIN') { $s_admin = "and pengaju = '".$IdUser."'"; } else { $s_admin = ""; }
		if ($user['tipe']=='SECTION HEAD' or $user['tipe']=='ADH') { $s_section = "and sect = '".$IdUser."'"; } else { $s_section = ""; }
		
		$s_dept = "";
		$s_div = ""; 
		/*if (($user['tipe']=='DEPT. HEAD' or $user['tipe']=='KEPALA CABANG') and $level!='DEPT. HEAD FINANCE / DIV. HEAD FAST') { 
			$s_dept = "and dept = '".$IdUser."'"; }
			
		if (($user['tipe']=='DEPT. HEAD' or $user['tipe']=='KEPALA CABANG') and $level=='DEPT. HEAD FINANCE / DIV. HEAD FAST') { 
			$s_dept = "and (dept = '".$IdUser."' or dept = 'all') "; } 
		*/
		
		if (($user['tipe']=='DEPT. HEAD' or $user['tipe']=='KEPALA CABANG')) {
			if ($level=='DEPT. HEAD FINANCE / DIV. HEAD FAST' or $level=='DEPT. HEAD FINANCE') {
				$s_dept = "and (dept = '".$IdUser."' or dept = 'all') ";
			} else {
				$s_dept = "and dept = '".$IdUser."'"; 
			} 
		}
			
		/*if ($user['tipe']=='DIV. HEAD' and $level!='DEPT. HEAD FINANCE / DIV. HEAD FAST') { 
			$s_div = "and (div = '".$IdUser."')";  }
			
		if ($user['tipe']=='DIV. HEAD' and $level=='DEPT. HEAD FINANCE / DIV. HEAD FAST') { 
			$s_div = "and (div = '".$IdUser."' or div = 'all')";  }
		*/
		
		if ($user['tipe']=='DIV. HEAD') {
			if ($level=='DEPT. HEAD FINANCE / DIV. HEAD FAST' or $level=='DEPT. HEAD FINANCE') {
				$s_div = "and (div = '".$IdUser."' or div = 'all') ";
			} else {
				$s_div = "and div = '".$IdUser."'"; 
			} 
		}
		
				
		/*if ($user['tipe']=='DIREKSI 2') { 
			//$level = "DIREKSI";
			if (ltrim(rtrim(strtoupper($level))) == "DIREKSI") {
				$level = "DIREKSI 2";
			}
		}*/

		if ($level=='WAITDEPTTERKAIT') {
			$table_sys = "";	
			$where_deptlain = " where isnull(x.deptterkait,'') <> '' ";
			$where_deptlain2 = " and isnull(deptterkait,'') <> '' ";
			$where_deptlain3 = "  ";
			$where = "  $s_admin $s_section $s_dept $s_div ";
			$where_lvl = " ";
			
		} else if ($level=='REQDEPTTERKAIT') {
			$table_sys = " inner join sys_user y on x.lvl = y.tipe and x.deptterkait = y.department ";
			$where_deptlain = " where x.deptterkait = '".$user['department']."' ";
			$where_deptlain2 = "  and isnull(deptterkait,'') = '".$user['department']."' ";
			$where_deptlain3 = "  and y.iduser = '".$IdUser."' "; 
			$where = " ";
			$where_lvl = " and lvl = 'ADMIN' ";
			
		} else {
		
			#if ($user['tipe']=='SECTION HEAD' or $user['tipe']=='ADH') { $s_section = "and (sect = '".$IdUser."') "; } else { $s_section = ""; }
			#if (($user['tipe']=='DEPT. HEAD' or $user['tipe']=='KEPALA CABANG') and $lvl!='DEPT. HEAD FINANCE / DIV. HEAD FAST') { $s_dept = "and (dept = '".$IdUser."' ) "; } else { $s_dept = ""; }
			// ($lvl=="DEPT. HEAD FINANCE / DIV. HEAD FAST" or $lvl=="DEPT. HEAD FINANCE") 
			$where_deptterkait_val = "";
			if ($user['tipe']=='ADMIN') { $where_deptterkait_val = "and admindeptterkait = '".$IdUser."'"; }
			if ($user['tipe']=='SECTION HEAD') { $where_deptterkait_val = "and sectdeptterkait = '".$IdUser."'"; }
			if ($user['tipe']=='DEPT. HEAD'  and $level!='DEPT. HEAD FINANCE') { $where_deptterkait_val = "and deptdeptterkait = '".$IdUser."'"; }
			if ($user['tipe']=='DIV. HEAD' and $level!='DEPT. HEAD FINANCE / DIV. HEAD FAST') { $where_deptterkait_val = "and deptdeptterkait = '".$IdUser."'"; }
			
			$table_sys = "";	
			$where_deptlain = "   ";			
			$where_deptlain2 = "   ";
			$where = " $s_admin  $s_section $s_dept $s_div";
			
			if ($_SESSION['level']=="DIREKSI" or  $_SESSION['level']=="DIREKSI 2") {
				$where_lvl = " and lvl = '".$level."' ";
			} else {
				/*if ($level=="DIREKSI" or  $level=="DIREKSI 2" ) {
					$where_lvl = " and lvl in ('DIREKSI','DIREKSI 2')  ";
				} else {
					$where_lvl = " and lvl = '".$level."' ";
				}*/
				$where_lvl = " and lvl = '".$level."' ";
			}
		}
		
		if ($user['KodeDealer']=='2010') {
			$dealer = " and y.is_dealer = '0' ";
		} else {
			$dealer = " and y.is_dealer = '1' ";
		}
			
		/*$sql = "
			select top $rp evo_id,a.nobukti,tgl_pengajuan,namaUser as nama_aju,NamaDealer,namaVendor,b.department,
			case when a.tipe='HUTANG' then htg_stl_pajak else biaya_yg_dibyar end as amount,keterangan 
			from (
				select * from(
					select nobukti,kodedealer,userentry as pengaju,IdAtasan as sect,
					(select IdAtasan from sys_user where IdUser=a.IdAtasan) as dept,
					(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.IdAtasan)) as div,
					(SELECT top 1 level FROM DataEvoVal where nobukti=a.nobukti and ISNULL(level, '')!='' and ISNULL(validasi, '')='' order by idVal desc) as lvl
					from DataEvo a
					$where_deptlain 
				) x where ISNULL(lvl, '')!='' and kodedealer = '".$user['KodeDealer']."' $where
			) x 
			inner join DataEvo a on x.nobukti=a.nobukti
			inner join sys_user b on a.userentry=b.IdUser
			inner join SPK00..dodealer c on a.kodedealer=c.kodedealer
			where a.nobukti is not null
			$where_lvl and evo_id not in (
				select top $start evo_id from (
					select * from(select nobukti,kodedealer,userentry as pengaju,IdAtasan as sect,
					(select IdAtasan from sys_user where IdUser=a.IdAtasan) as dept,
					(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.IdAtasan)) as div,
					(SELECT top 1 level FROM DataEvoVal where nobukti=a.nobukti and ISNULL(level, '')!='' and ISNULL(validasi, '')='' order by idVal desc) as lvl
					from DataEvo a
					$where_deptlain ) x where ISNULL(lvl, '')!='' and kodedealer = '".$user['KodeDealer']."' 
				) x 
				inner join DataEvo a on x.nobukti=a.nobukti
				inner join sys_user b on a.userentry=b.IdUser
				inner join SPK00..dodealer c on a.kodedealer=c.kodedealer
				where a.nobukti is not null $where_lvl
			) $src
		";*/
		
		
				
		if ($level=='WAITDEPTTERKAIT' or $level=='REQDEPTTERKAIT') {
			$sql = "
				select top $rp evo_id,a.nobukti,tgl_pengajuan, b.namaUser as nama_aju,NamaDealer,namaVendor,b.department,
				case when a.tipe='HUTANG' then htg_stl_pajak else biaya_yg_dibyar end as amount,keterangan 
				from (
					select x.nobukti, x.kodedealer, x.idAtasan, x.pengaju, x.tipe, x.sect, x.dept, x.div, x.lvl, x.deptterkait,
					'' admindeptterkait, '' sectdeptterkait, '' deptdeptterkait
					from (
						
						select a.nobukti, a.kodedealer, a.idAtasan, a.pengaju, a.tipe, a.sect,
									
						case 
							when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
							when a.tipe = 'DEPT. HEAD' then a.pengaju
							when a.tipe = 'DIV. HEAD' then '' else (select IdAtasan from sys_user where IdUser=a.idAtasan) end dept,
								
						case 
							when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user 
									where IdUser=(select IdAtasan from sys_user where IdUser=a.pengaju))
							when a.tipe = 'DEPT. HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
							when a.tipe = 'DIV. HEAD' then '' else
							(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.sect)) end div,
						
						(SELECT top 1 level FROM DataEvoVal x
									left join sys_level y on x.level = y.nama_lvl $dealer
									where nobukti=a.nobukti and ISNULL(level, '')!=''
									and ISNULL(validasi, '')='' 
									$where_deptlain2
									order by y.urutan asc) as lvl,
									
									(SELECT top 1 deptterkait FROM DataEvoVal x
									left join sys_level y on x.level = y.nama_lvl $dealer
									where nobukti=a.nobukti and ISNULL(level, '')!=''
									and ISNULL(validasi, '')='' 
									order by y.urutan asc) as deptterkait
									
						from (
							select nobukti,x.kodedealer, y.idAtasan, userentry as pengaju, y.tipe,
							case 
							when y.tipe = 'SECTION HEAD' then userentry
							when y.tipe = 'DEPT. HEAD' then '' 
							when y.tipe = 'DIV. HEAD' then '' else y.IdAtasan  end sect	
							from DataEvo x
							inner join sys_user y on x.userentry = y.IdUser
							$where_deptlain
						) a
						
					) x 
					$table_sys				
					where ISNULL(lvl, '')!='' and x.kodedealer = '".$user['KodeDealer']."' $where
					$where_deptlain3
				) x 
				inner join DataEvo a on x.nobukti=a.nobukti
				inner join sys_user b on a.userentry=b.IdUser
				inner join SPK00..dodealer c on a.kodedealer=c.kodedealer
				where a.nobukti is not null $where_lvl 
				
				and evo_id not in (
					select top $start evo_id from (
						select x.nobukti, x.kodedealer, x.idAtasan, x.pengaju, x.tipe, x.sect, x.dept, x.div, x.lvl, x.deptterkait,
						'' admindeptterkait, '' sectdeptterkait, '' deptdeptterkait
						
						from (
							
							select a.nobukti, a.kodedealer, a.idAtasan, a.pengaju, a.tipe, a.sect,
									
							case 
								when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
								when a.tipe = 'DEPT. HEAD' then a.pengaju
								when a.tipe = 'DIV. HEAD' then '' else 
								(select IdAtasan from sys_user where IdUser=a.idAtasan) end dept,
									
							case 
								when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user 
										where IdUser=(select IdAtasan from sys_user where IdUser=a.pengaju))
								when a.tipe = 'DEPT. HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
								when a.tipe = 'DIV. HEAD' then '' else 
								(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.sect)) end div,
							
							(SELECT top 1 level FROM DataEvoVal x
									left join sys_level y on x.level = y.nama_lvl $dealer
									where nobukti=a.nobukti and ISNULL(level, '')!=''
									and ISNULL(validasi, '')='' 
									$where_deptlain2
									order by y.urutan asc) as lvl,
									
									(SELECT top 1 deptterkait FROM DataEvoVal x
									left join sys_level y on x.level = y.nama_lvl $dealer
									where nobukti=a.nobukti and ISNULL(level, '')!=''
									and ISNULL(validasi, '')='' 
									order by y.urutan asc) as deptterkait
									
							from (
								select nobukti,x.kodedealer, y.idAtasan, userentry as pengaju, y.tipe,
								case 
								when y.tipe = 'SECTION HEAD' then userentry
								when y.tipe = 'DEPT. HEAD' then '' 
								when y.tipe = 'DIV. HEAD' then '' else y.IdAtasan  end sect	
								from DataEvo x
								inner join sys_user y on x.userentry = y.IdUser
								$where_deptlain
							) a
							
						) x 
						$table_sys				
						where ISNULL(lvl, '')!='' and x.kodedealer = '".$user['KodeDealer']."' $where
						$where_deptlain3
						
					) x 
					inner join DataEvo a on x.nobukti=a.nobukti
					inner join sys_user b on a.userentry=b.IdUser
					inner join SPK00..dodealer c on a.kodedealer=c.kodedealer
					where a.nobukti is not null $where_lvl
				) $src
			";
			//echo "<pre>".$sql."</pre>";
			
		} else {
			
			if ($level=="DIREKSI" or  $level=="DIREKSI 2" ) {
				
				$sql = "
					select top $rp evo_id,a.nobukti,tgl_pengajuan,namaUser as nama_aju,NamaDealer,namaVendor,b.department,
					case when a.tipe='HUTANG' then htg_stl_pajak else biaya_yg_dibyar end as amount,keterangan 
					from (
					
						select x.nobukti, x.kodedealer, x.idAtasan, x.pengaju, x.tipe, x.sect, x.dept, x.div, x.lvl, x.deptterkait,
						'' admindeptterkait, '' sectdeptterkait, '' deptdeptterkait
						
						from (
							select a.nobukti, a.kodedealer, a.idAtasan, a.pengaju, a.tipe, a.sect,	
																
							case 
								when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
								when a.tipe = 'DEPT. HEAD' then a.pengaju
								when a.tipe = 'DIV. HEAD' then '' else  (select IdAtasan from sys_user where IdUser=a.idAtasan) end dept,	
																
							case 
								when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user 
										where IdUser=(select IdAtasan from sys_user where IdUser=a.pengaju))
								when a.tipe = 'DEPT. HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
								when a.tipe = 'DIV. HEAD' then '' else 
								(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.sect)) end div,	
														
							(SELECT top 1 level FROM DataEvoVal x
								left join sys_level y on x.level = y.nama_lvl $dealer
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								order by y.urutan asc) as lvl,										
							(SELECT top 1 deptterkait FROM DataEvoVal x
								left join sys_level y on x.level = y.nama_lvl $dealer 
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								order by y.urutan asc) as deptterkait
								
							from (
								select nobukti,x.kodedealer, y.idAtasan, userentry as pengaju, y.tipe,
								case  
								when y.tipe = 'SECTION HEAD' then userentry
								when y.tipe = 'DEPT. HEAD' then '' 
								when y.tipe = 'DIV. HEAD' then '' else  x.IdAtasan end sect	
								from DataEvo x
								inner join sys_user y on x.userentry = y.IdUser
							) a
							
						) x where ISNULL(lvl, '')!='' and kodedealer = '".$user['KodeDealer']."' $where
												
					) x 
					inner join DataEvo a on x.nobukti=a.nobukti
					inner join sys_user b on a.userentry=b.IdUser
					inner join SPK00..dodealer c on a.kodedealer=c.kodedealer
					where a.nobukti is not null
					$where_lvl 
					
					and evo_id not in (
						select top $start evo_id from (
					
							select x.nobukti, x.kodedealer, x.idAtasan, x.pengaju, x.tipe, x.sect, x.dept, x.div, x.lvl, x.deptterkait,
							'' admindeptterkait, '' sectdeptterkait, '' deptdeptterkait
							
							from (
								select a.nobukti, a.kodedealer, a.idAtasan, a.pengaju, a.tipe, a.sect,
																		
								case 
									when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
									when a.tipe = 'DEPT. HEAD' then a.pengaju
									when a.tipe = 'DIV. HEAD' then '' else (select IdAtasan from sys_user where IdUser=a.idAtasan) end dept,	
																	
								case 
									when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user 
											where IdUser=(select IdAtasan from sys_user where IdUser=a.pengaju))
									when a.tipe = 'DEPT. HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
									when a.tipe = 'DIV. HEAD' then '' else 
									(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.sect)) end div,	
															
								(SELECT top 1 level FROM DataEvoVal x
									left join sys_level y on x.level = y.nama_lvl $dealer
									where nobukti=a.nobukti and ISNULL(level, '')!=''
									and ISNULL(validasi, '')='' 
									$where_deptlain2
									order by y.urutan asc) as lvl,										
								(SELECT top 1 deptterkait FROM DataEvoVal x
									left join sys_level y on x.level = y.nama_lvl $dealer
									where nobukti=a.nobukti and ISNULL(level, '')!=''
									and ISNULL(validasi, '')='' 
									order by y.urutan asc) as deptterkait
									
								from (
									select nobukti,x.kodedealer, y.idAtasan, userentry as pengaju, y.tipe,
									case 
									when y.tipe = 'SECTION HEAD' then userentry
									when y.tipe = 'DEPT. HEAD' then '' 
									when y.tipe = 'DIV. HEAD' then '' else y.IdAtasan  end sect	
									from DataEvo x
									inner join sys_user y on x.userentry = y.IdUser
								) a
								
							) x where ISNULL(lvl, '')!='' and kodedealer = '".$user['KodeDealer']."' $where
							
						) x 
						inner join DataEvo a on x.nobukti=a.nobukti
						inner join sys_user b on a.userentry=b.IdUser
						inner join SPK00..dodealer c on a.kodedealer=c.kodedealer
						where a.nobukti is not null $where_lvl
					) $src
				";
					
			} else if ($level=="FINANCE") {
			
				$sql = "
					select top $rp evo_id,a.nobukti,tgl_pengajuan,namaUser as nama_aju,NamaDealer,namaVendor,b.department,
					case when a.tipe='HUTANG' then htg_stl_pajak else biaya_yg_dibyar end as amount,keterangan 
					from (
					
						select x.nobukti, x.kodedealer, x.idAtasan, x.pengaju, x.tipe, x.sect, x.dept, x.div, x.lvl, x.deptterkait,
						'' admindeptterkait, '' sectdeptterkait, '' deptdeptterkait
						
						from (
							select a.nobukti, a.kodedealer, a.idAtasan, a.pengaju, a.tipe, a.sect,
																	
							case 
								when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
								when a.tipe = 'DEPT. HEAD' then a.pengaju
								when a.tipe = 'DIV. HEAD' then '' else  (select IdAtasan from sys_user where IdUser=a.idAtasan) end dept,
																	
							case 
								when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user 
										where IdUser=(select IdAtasan from sys_user where IdUser=a.pengaju))
								when a.tipe = 'DEPT. HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
								when a.tipe = 'DIV. HEAD' then '' else 
								(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.sect)) end div,	
														
							(SELECT top 1 level FROM DataEvoVal x
								left join sys_level y on x.level = y.nama_lvl $dealer
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								$where_deptlain2
								order by y.urutan asc) as lvl,										
							(SELECT top 1 deptterkait FROM DataEvoVal x
								left join sys_level y on x.level = y.nama_lvl $dealer
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								order by y.urutan asc) as deptterkait
								
							from (
								select nobukti,x.kodedealer, y.idAtasan, userentry as pengaju, y.tipe,
								case  
								when y.tipe = 'SECTION HEAD' then userentry
								when y.tipe = 'DEPT. HEAD' then '' 
								when y.tipe = 'DIV. HEAD' then '' else  x.IdAtasan end sect	
								from DataEvo x
								inner join sys_user y on x.userentry = y.IdUser
								$where_deptlain
							) a
							
						) x where ISNULL(lvl, '')!='' and kodedealer = '".$user['KodeDealer']."' $where
						and isnull(deptterkait,'') = ''
												
					) x 
					inner join DataEvo a on x.nobukti=a.nobukti
					inner join sys_user b on a.userentry=b.IdUser
					inner join SPK00..dodealer c on a.kodedealer=c.kodedealer
					where a.nobukti is not null
					$where_lvl 
					
					and evo_id not in (
						select top $start evo_id from (
					
							select x.nobukti, x.kodedealer, x.idAtasan, x.pengaju, x.tipe, x.sect, x.dept, x.div, x.lvl, x.deptterkait,
							'' admindeptterkait, '' sectdeptterkait, '' deptdeptterkait
							
							from (
								select a.nobukti, a.kodedealer, a.idAtasan, a.pengaju, a.tipe, a.sect,										
								case 
									when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
									when a.tipe = 'DEPT. HEAD' then a.pengaju
									when a.tipe = 'DIV. HEAD' then '' else (select IdAtasan from sys_user where IdUser=a.idAtasan) end dept,									
								
								case 
									when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user 
											where IdUser=(select IdAtasan from sys_user where IdUser=a.pengaju))
									when a.tipe = 'DEPT. HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
									when a.tipe = 'DIV. HEAD' then '' else 
									(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.sect)) end div,	
															
								(SELECT top 1 level FROM DataEvoVal x
									left join sys_level y on x.level = y.nama_lvl $dealer
									where nobukti=a.nobukti and ISNULL(level, '')!=''
									and ISNULL(validasi, '')='' 
									$where_deptlain2
									order by y.urutan asc) as lvl,										
								(SELECT top 1 deptterkait FROM DataEvoVal x
									left join sys_level y on x.level = y.nama_lvl $dealer
									where nobukti=a.nobukti and ISNULL(level, '')!=''
									and ISNULL(validasi, '')='' 
									order by y.urutan asc) as deptterkait
									
								from (
									select nobukti,x.kodedealer, y.idAtasan, userentry as pengaju, y.tipe,
									case 
									when y.tipe = 'SECTION HEAD' then userentry
									when y.tipe = 'DEPT. HEAD' then '' 
									when y.tipe = 'DIV. HEAD' then '' else y.IdAtasan  end sect	
									from DataEvo x
									inner join sys_user y on x.userentry = y.IdUser
									$where_deptlain
								) a
								
							) x where ISNULL(lvl, '')!='' and kodedealer = '".$user['KodeDealer']."' $where
							and isnull(deptterkait,'') = ''
							
						) x 
						inner join DataEvo a on x.nobukti=a.nobukti
						inner join sys_user b on a.userentry=b.IdUser
						inner join SPK00..dodealer c on a.kodedealer=c.kodedealer
						where a.nobukti is not null $where_lvl
					) $src
				";
				
			
			} else if (($level=="DEPT. HEAD FINANCE / DIV. HEAD FAST" or $level=="DEPT. HEAD FINANCE")  and ($user['department']=='FINANCE' or $user['department']=='all')  and $user['divisi']=="FINANCE and ACCOUNTING") {
				
				$sql = "
					select top $rp evo_id,a.nobukti,tgl_pengajuan,namaUser as nama_aju,NamaDealer,namaVendor,b.department,
					case when a.tipe='HUTANG' then htg_stl_pajak else biaya_yg_dibyar end as amount,keterangan 
					from (
					
						select x.nobukti, x.kodedealer, x.idAtasan, x.pengaju, x.tipe, x.sect, x.dept, x.div, x.lvl, x.deptterkait,
						'' admindeptterkait, '' sectdeptterkait, '' deptdeptterkait
						
						from (
							select a.nobukti, a.kodedealer, a.idAtasan, a.pengaju, a.tipe, a.sect,										
							case 
								when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
								when a.tipe = 'DEPT. HEAD' then a.pengaju
								when a.tipe = 'DIV. HEAD' then '' else (select IdAtasan from sys_user where IdUser=a.idAtasan) end dept,	
																
							case
								when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user 
										where IdUser=(select IdAtasan from sys_user where IdUser=a.pengaju))
								when a.tipe = 'DEPT. HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
								when a.tipe = 'DIV. HEAD' then '' else 
								(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.sect)) end div,		
													
							(SELECT top 1 level FROM DataEvoVal x
								left join sys_level y on x.level = y.nama_lvl $dealer
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								$where_deptlain2
								order by y.urutan asc) as lvl,										
							(SELECT top 1 deptterkait FROM DataEvoVal x
								left join sys_level y on x.level = y.nama_lvl $dealer
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								order by y.urutan asc) as deptterkait
								
							from (
								select nobukti,x.kodedealer, y.idAtasan, userentry as pengaju, y.tipe,
								case 
								when y.tipe = 'SECTION HEAD' then userentry
								when y.tipe = 'DEPT. HEAD' then '' 
								when y.tipe = 'DIV. HEAD' then '' else y.IdAtasan  end sect	
								from DataEvo x
								inner join sys_user y on x.userentry = y.IdUser
								$where_deptlain
							) a
							
						) x where ISNULL(lvl, '')!='' and kodedealer = '".$user['KodeDealer']."' $where
						and isnull(deptterkait,'') = ''
						
						union
							
						select x.nobukti, x.kodedealer, x.idAtasan, x.pengaju, x.tipe, x.sect, x.dept, x.div, x.lvl, x.deptterkait	,
						'' admindeptterkait, '' sectdeptterkait, '' deptdeptterkait
											
						from (
							select a.nobukti, a.kodedealer, a.idAtasan, a.pengaju, a.tipe, a.sect,										
							case 
								when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
								when a.tipe = 'DEPT. HEAD' then a.pengaju
								when a.tipe = 'DIV. HEAD' then '' else (select IdAtasan from sys_user where IdUser=a.idAtasan) end dept,									
							case 
								when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user 
										where IdUser=(select IdAtasan from sys_user where IdUser=a.pengaju))
								when a.tipe = 'DEPT. HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
								when a.tipe = 'DIV. HEAD' then '' else 
								(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.sect)) end div,	
														
							(SELECT top 1 level FROM DataEvoVal x
								left join sys_level y on x.level = y.nama_lvl $dealer
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								$where_deptlain2
								order by y.urutan asc) as lvl,										
							(SELECT top 1 deptterkait FROM DataEvoVal x
								left join sys_level y on x.level = y.nama_lvl $dealer
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								order by y.urutan asc) as deptterkait
								
							from (
								select nobukti,x.kodedealer, y.idAtasan, userentry as pengaju, y.tipe,
								case 
								when y.tipe = 'SECTION HEAD' then userentry
								when y.tipe = 'DEPT. HEAD' then '' 
								when y.tipe = 'DIV. HEAD' then '' else y.IdAtasan  end sect	
								from DataEvo x
								inner join sys_user y on x.userentry = y.IdUser
								$where_deptlain
							) a
							
						) x 						
						inner join sys_user y on x.lvl = y.tipe and x.deptterkait = y.department
						where ISNULL(lvl, '')!='' and x.kodedealer = '".$user['KodeDealer']."'
						and y.iduser = '".$IdUser."'
						
						union
								
						select x.nobukti, x.kodedealer, x.idAtasan, x.pengaju, x.tipe, x.sect, x.dept, x.div, x.lvl, x.deptterkait,
						isnull(x.admindeptterkait,'') admindeptterkait, isnull(x.sectdeptterkait,'') sectdeptterkait, 
	isnull(x.deptdeptterkait,'') deptdeptterkait
						
						from (
						
							select a.nobukti, a.kodedealer, a.idAtasan, a.pengaju, a.tipe, a.sect,
							
							case 
								when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
								when a.tipe = 'DEPT. HEAD' then a.pengaju
								when a.tipe = 'DIV. HEAD' then '' else 
								(select IdAtasan from sys_user where IdUser=a.idAtasan) end dept,
									
							case 
								when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user 
										where IdUser=(select IdAtasan from sys_user where IdUser=a.pengaju))
								when a.tipe = 'DEPT. HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
								when a.tipe = 'DIV. HEAD' then '' else 
								(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.sect)) end div,
							
							(SELECT top 1 level FROM DataEvoVal x
							left join sys_level y on x.level = y.nama_lvl $dealer
							where nobukti=a.nobukti and ISNULL(level, '')!=''
							and ISNULL(validasi, '')='' 
							order by y.urutan asc) as lvl,
							
							(SELECT top 1 deptterkait FROM DataEvoVal x
							left join sys_level y on x.level = y.nama_lvl $dealer
							where nobukti=a.nobukti and ISNULL(level, '')!=''
							and ISNULL(validasi, '')='' 
							order by y.urutan asc) as deptterkait,
							
							a.admindeptterkait, a.sectdeptterkait, a.deptdeptterkait
							
							from (
								select nobukti,x.kodedealer, y.idAtasan, userentry as pengaju, y.tipe,
								case
								when y.tipe = 'SECTION HEAD' then userentry
								when y.tipe = 'DEPT. HEAD' then '' 
								when y.tipe = 'DIV. HEAD' then '' else y.IdAtasan  end sect,
								(select top 1 uservalidasi from DataEvoVal z 
								where z.nobukti = x.nobukti
								and isnull(z.deptterkait,'') = '".$user['department']."' and z.level = 'ADMIN') admindeptterkait, 									
								(select top 1  uservalidasi from DataEvoVal z 
								where z.nobukti = x.nobukti
								and isnull(z.deptterkait,'') = '".$user['department']."' and z.level = 'SECTION HEAD') sectdeptterkait, 																
								(select top 1  uservalidasi from DataEvoVal z 
								where z.nobukti = x.nobukti
								and isnull(z.deptterkait,'') = '".$user['department']."' and z.level = 'DEPT. HEAD') deptdeptterkait		
								from DataEvo x
								inner join sys_user y on x.userentry = y.IdUser
							) a
							
						) x 
						inner join sys_user y on x.lvl = y.tipe and x.deptterkait = y.department
						where ISNULL(lvl, '')!='' and x.kodedealer = '".$user['KodeDealer']."'
						$where_deptterkait_val
						
						union
								
						select x.nobukti, x.kodedealer, x.idAtasan, x.pengaju, x.tipe, x.sect, x.dept, x.div, x.lvl, x.deptterkait,
						isnull(x.admindeptterkait,'') admindeptterkait, isnull(x.sectdeptterkait,'') sectdeptterkait, 
	isnull(x.deptdeptterkait,'') deptdeptterkait
						
						from (
						
							select a.nobukti, a.kodedealer, a.idAtasan, a.pengaju, a.tipe, a.sect,
							
							case 
								when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
								when a.tipe = 'DEPT. HEAD' then a.pengaju
								when a.tipe = 'DIV. HEAD' then '' else (select IdAtasan from sys_user where IdUser=a.idAtasan) end dept,
									
							case 
								when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user 
										where IdUser=(select IdAtasan from sys_user where IdUser=a.pengaju))
								when a.tipe = 'DEPT. HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
								when a.tipe = 'DIV. HEAD' then '' else 
								(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.sect)) end div,
							
							(SELECT top 1 level FROM DataEvoVal x
							left join sys_level y on x.level = y.nama_lvl $dealer
							where nobukti=a.nobukti and ISNULL(level, '')!=''
							and ISNULL(validasi, '')='' 
							order by y.urutan asc) as lvl,
							
							(SELECT top 1 deptterkait FROM DataEvoVal x
							left join sys_level y on x.level = y.nama_lvl $dealer
							where nobukti=a.nobukti and ISNULL(level, '')!=''
							and ISNULL(validasi, '')='' 
							order by y.urutan asc) as deptterkait,
							
							'' admindeptterkait, '' sectdeptterkait, '' deptdeptterkait
							
							from (
								select nobukti,x.kodedealer, y.idAtasan, userentry as pengaju, y.tipe,
								case  
								when y.tipe = 'SECTION HEAD' then userentry
								when y.tipe = 'DEPT. HEAD' then '' 
								when y.tipe = 'DIV. HEAD' then '' else  x.IdAtasan end sect,
								(select top 1 uservalidasi from DataEvoVal z 
								where z.nobukti = x.nobukti
								and isnull(z.deptterkait,'') = '".$user['department']."' and z.level = 'ADMIN') admindeptterkait, 									
								(select top 1  uservalidasi from DataEvoVal z 
								where z.nobukti = x.nobukti
								and isnull(z.deptterkait,'') = '".$user['department']."' and z.level = 'SECTION HEAD') sectdeptterkait, 																
								(select top 1  uservalidasi from DataEvoVal z 
								where z.nobukti = x.nobukti
								and isnull(z.deptterkait,'') = '".$user['department']."' and z.level = 'DEPT. HEAD') deptdeptterkait		
								from DataEvo x
								inner join sys_user y on x.userentry = y.IdUser
							) a
							
						) x 
						where ISNULL(lvl, '')!='' and x.kodedealer = '".$user['KodeDealer']."'
						$where_deptterkait_val
												
					) x 
					inner join DataEvo a on x.nobukti=a.nobukti
					inner join sys_user b on a.userentry=b.IdUser
					inner join SPK00..dodealer c on a.kodedealer=c.kodedealer
					where a.nobukti is not null
					$where_lvl 
					
					and evo_id not in (
						select top $start evo_id from (
					
							select x.nobukti, x.kodedealer, x.idAtasan, x.pengaju, x.tipe, x.sect, x.dept, x.div, x.lvl, x.deptterkait,
							'' admindeptterkait, '' sectdeptterkait, '' deptdeptterkait
							
							from (
								select a.nobukti, a.kodedealer, a.idAtasan, a.pengaju, a.tipe, a.sect,										
								case
									when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
									when a.tipe = 'DEPT. HEAD' then a.pengaju
									when a.tipe = 'DIV. HEAD' then '' else 
									(select IdAtasan from sys_user where IdUser=a.idAtasan) end dept,	
																	
								case 
									when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user 
											where IdUser=(select IdAtasan from sys_user where IdUser=a.pengaju))
									when a.tipe = 'DEPT. HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
									when a.tipe = 'DIV. HEAD' then '' else 
									(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.sect)) end div,	
															
								(SELECT top 1 level FROM DataEvoVal x
									left join sys_level y on x.level = y.nama_lvl $dealer
									where nobukti=a.nobukti and ISNULL(level, '')!=''
									and ISNULL(validasi, '')='' 
									$where_deptlain2
									order by y.urutan asc) as lvl,										
								(SELECT top 1 deptterkait FROM DataEvoVal x
									left join sys_level y on x.level = y.nama_lvl $dealer
									where nobukti=a.nobukti and ISNULL(level, '')!=''
									and ISNULL(validasi, '')='' 
									order by y.urutan asc) as deptterkait
									
								from (
									select nobukti,x.kodedealer, y.idAtasan, userentry as pengaju, y.tipe,
									case 
									when y.tipe = 'SECTION HEAD' then userentry
									when y.tipe = 'DEPT. HEAD' then '' 
									when y.tipe = 'DIV. HEAD' then '' else y.IdAtasan  end sect	
									from DataEvo x
									inner join sys_user y on x.userentry = y.IdUser
									$where_deptlain
								) a
								
							) x where ISNULL(lvl, '')!='' and kodedealer = '".$user['KodeDealer']."' $where
							and isnull(deptterkait,'') = ''
							
							union
								
							select x.nobukti, x.kodedealer, x.idAtasan, x.pengaju, x.tipe, x.sect, x.dept, x.div, x.lvl, x.deptterkait	,
							'' admindeptterkait, '' sectdeptterkait, '' deptdeptterkait
												
							from (
								select a.nobukti, a.kodedealer, a.idAtasan, a.pengaju, a.tipe, a.sect,										
								case 
									when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
									when a.tipe = 'DEPT. HEAD' then a.pengaju
									when a.tipe = 'DIV. HEAD' then '' else (select IdAtasan from sys_user where IdUser=a.idAtasan) end dept,	
																	
								case
									when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user 
											where IdUser=(select IdAtasan from sys_user where IdUser=a.pengaju))
									when a.tipe = 'DEPT. HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
									when a.tipe = 'DIV. HEAD' then '' else 
									(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.sect)) end div,
																
								(SELECT top 1 level FROM DataEvoVal x
									left join sys_level y on x.level = y.nama_lvl $dealer
									where nobukti=a.nobukti and ISNULL(level, '')!=''
									and ISNULL(validasi, '')='' 
									$where_deptlain2
									order by y.urutan asc) as lvl,										
								(SELECT top 1 deptterkait FROM DataEvoVal x
									left join sys_level y on x.level = y.nama_lvl $dealer
									where nobukti=a.nobukti and ISNULL(level, '')!=''
									and ISNULL(validasi, '')='' 
									order by y.urutan asc) as deptterkait
									
								from (
									select nobukti,x.kodedealer, y.idAtasan, userentry as pengaju, y.tipe,
									case 
									when y.tipe = 'SECTION HEAD' then userentry
									when y.tipe = 'DEPT. HEAD' then '' 
									when y.tipe = 'DIV. HEAD' then '' else y.IdAtasan  end sect	
									from DataEvo x
									inner join sys_user y on x.userentry = y.IdUser
									$where_deptlain
								) a
								
							) x 						
							inner join sys_user y on x.lvl = y.tipe and x.deptterkait = y.department
							where ISNULL(lvl, '')!='' and x.kodedealer = '".$user['KodeDealer']."'
							and y.iduser = '".$IdUser."'
							
								union
									
							select x.nobukti, x.kodedealer, x.idAtasan, x.pengaju, x.tipe, x.sect, x.dept, x.div, x.lvl, x.deptterkait,
							isnull(x.admindeptterkait,'') admindeptterkait, isnull(x.sectdeptterkait,'') sectdeptterkait, 
	isnull(x.deptdeptterkait,'') deptdeptterkait
							
							from (
							
								select a.nobukti, a.kodedealer, a.idAtasan, a.pengaju, a.tipe, a.sect,
								
								case 
									when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
									when a.tipe = 'DEPT. HEAD' then a.pengaju
									when a.tipe = 'DIV. HEAD' then '' else 
									(select IdAtasan from sys_user where IdUser=a.idAtasan) end dept,
										
								case 
									when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user 
											where IdUser=(select IdAtasan from sys_user where IdUser=a.pengaju))
									when a.tipe = 'DEPT. HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
									when a.tipe = 'DIV. HEAD' then '' else 
									(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.sect)) end div,
								
								(SELECT top 1 level FROM DataEvoVal x
								left join sys_level y on x.level = y.nama_lvl $dealer
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								order by y.urutan asc) as lvl,
								
								(SELECT top 1 deptterkait FROM DataEvoVal x
								left join sys_level y on x.level = y.nama_lvl $dealer
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								order by y.urutan asc) as deptterkait,
								
								a.admindeptterkait, a.sectdeptterkait, a.deptdeptterkait
								
								from (
									select nobukti,x.kodedealer, y.idAtasan, userentry as pengaju, y.tipe,
									case 
									when y.tipe = 'SECTION HEAD' then userentry
									when y.tipe = 'DEPT. HEAD' then '' 
									when y.tipe = 'DIV. HEAD' then '' else y.IdAtasan  end sect,
									(select top 1 uservalidasi from DataEvoVal z 
									where z.nobukti = x.nobukti
									and isnull(z.deptterkait,'') = '".$user['department']."' and z.level = 'ADMIN') admindeptterkait, 									
									(select top 1  uservalidasi from DataEvoVal z 
									where z.nobukti = x.nobukti
									and isnull(z.deptterkait,'') = '".$user['department']."' and z.level = 'SECTION HEAD') sectdeptterkait, 																
									(select top 1  uservalidasi from DataEvoVal z 
									where z.nobukti = x.nobukti
									and isnull(z.deptterkait,'') = '".$user['department']."' and z.level = 'DEPT. HEAD') deptdeptterkait		
									from DataEvo x
									inner join sys_user y on x.userentry = y.IdUser
								) a
								
							) x 
							inner join sys_user y on x.lvl = y.tipe and x.deptterkait = y.department
							where ISNULL(lvl, '')!='' and x.kodedealer = '".$user['KodeDealer']."'
							$where_deptterkait_val
							
									union
									
							select x.nobukti, x.kodedealer, x.idAtasan, x.pengaju, x.tipe, x.sect, x.dept, x.div, x.lvl, x.deptterkait,
							isnull(x.admindeptterkait,'') admindeptterkait, isnull(x.sectdeptterkait,'') sectdeptterkait, 
	isnull(x.deptdeptterkait,'') deptdeptterkait
							
							from (
							
								select a.nobukti, a.kodedealer, a.idAtasan, a.pengaju, a.tipe, a.sect,
								
								case 
									when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
									when a.tipe = 'DEPT. HEAD' then a.pengaju
									when a.tipe = 'DIV. HEAD' then '' else (select IdAtasan from sys_user where IdUser=a.idAtasan) end dept,
										
								case 
									when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user 
											where IdUser=(select IdAtasan from sys_user where IdUser=a.pengaju))
									when a.tipe = 'DEPT. HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
									when a.tipe = 'DIV. HEAD' then '' else 
									(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.sect)) end div,
								
								(SELECT top 1 level FROM DataEvoVal x
								left join sys_level y on x.level = y.nama_lvl $dealer
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								order by y.urutan asc) as lvl,
								
								(SELECT top 1 deptterkait FROM DataEvoVal x
								left join sys_level y on x.level = y.nama_lvl $dealer
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								order by y.urutan asc) as deptterkait,
								
								'' admindeptterkait, '' sectdeptterkait, '' deptdeptterkait
								
								from (
									select nobukti,x.kodedealer, y.idAtasan, userentry as pengaju, y.tipe,
									case  
									when y.tipe = 'SECTION HEAD' then userentry
									when y.tipe = 'DEPT. HEAD' then '' 
									when y.tipe = 'DIV. HEAD' then '' else y.IdAtasan end sect,
									(select top 1 uservalidasi from DataEvoVal z 
									where z.nobukti = x.nobukti
									and isnull(z.deptterkait,'') = '".$user['department']."' and z.level = 'ADMIN') admindeptterkait, 									
									(select top 1  uservalidasi from DataEvoVal z 
									where z.nobukti = x.nobukti
									and isnull(z.deptterkait,'') = '".$user['department']."' and z.level = 'SECTION HEAD') sectdeptterkait, 																
									(select top 1  uservalidasi from DataEvoVal z 
									where z.nobukti = x.nobukti
									and isnull(z.deptterkait,'') = '".$user['department']."' and z.level = 'DEPT. HEAD') deptdeptterkait		
									from DataEvo x
									inner join sys_user y on x.userentry = y.IdUser
								) a
								
							) x 
							where ISNULL(lvl, '')!='' and x.kodedealer = '".$user['KodeDealer']."'
							$where_deptterkait_val
							
					
						) x 
						inner join DataEvo a on x.nobukti=a.nobukti
						inner join sys_user b on a.userentry=b.IdUser
						inner join SPK00..dodealer c on a.kodedealer=c.kodedealer
						where a.nobukti is not null $where_lvl
					) $src
				";
				
			} else {
				
				$sql = "
					select top $rp evo_id,a.nobukti,tgl_pengajuan,namaUser as nama_aju,NamaDealer,namaVendor,b.department,
					case when a.tipe='HUTANG' then htg_stl_pajak else biaya_yg_dibyar end as amount,keterangan 
					from (
					
						select x.nobukti, x.kodedealer, x.idAtasan, x.pengaju, x.tipe, x.sect, x.dept, x.div, x.lvl, x.deptterkait,
						'' admindeptterkait, '' sectdeptterkait, '' deptdeptterkait
						
						from (
							select a.nobukti, a.kodedealer, a.idAtasan, a.pengaju, a.tipe, a.sect,										
							case 
								when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
								when a.tipe = 'DEPT. HEAD' then a.pengaju
								when a.tipe = 'DIV. HEAD' then '' else (select IdAtasan from sys_user where IdUser=a.idAtasan) end dept,									
							case 
								when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user 
										where IdUser=(select IdAtasan from sys_user where IdUser=a.pengaju))
								when a.tipe = 'DEPT. HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
								when a.tipe = 'DIV. HEAD' then '' else 
								(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.sect)) end div,	
														
							(SELECT top 1 level FROM DataEvoVal x
								left join sys_level y on x.level = y.nama_lvl $dealer
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								$where_deptlain2
								order by y.urutan asc) as lvl,										
							(SELECT top 1 deptterkait FROM DataEvoVal x
								left join sys_level y on x.level = y.nama_lvl $dealer
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								order by y.urutan asc) as deptterkait
								
							from (
								select nobukti,x.kodedealer, y.idAtasan, userentry as pengaju, y.tipe,
								case 
								when y.tipe = 'SECTION HEAD' then userentry
								when y.tipe = 'DEPT. HEAD' then '' 
								when y.tipe = 'DIV. HEAD' then '' else y.IdAtasan  end sect	
								from DataEvo x
								inner join sys_user y on x.userentry = y.IdUser
								$where_deptlain
							) a
							
						) x where ISNULL(lvl, '')!='' and kodedealer = '".$user['KodeDealer']."' $where
						and isnull(deptterkait,'') = ''
						
						union
							
						select x.nobukti, x.kodedealer, x.idAtasan, x.pengaju, x.tipe, x.sect, x.dept, x.div, x.lvl, x.deptterkait	,
						'' admindeptterkait, '' sectdeptterkait, '' deptdeptterkait
											
						from (
							select a.nobukti, a.kodedealer, a.idAtasan, a.pengaju, a.tipe, a.sect,										
							case 
								when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
								when a.tipe = 'DEPT. HEAD' then a.pengaju
								when a.tipe = 'DIV. HEAD' then '' else (select IdAtasan from sys_user where IdUser=a.idAtasan) end dept,	
																
							case
								when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user 
										where IdUser=(select IdAtasan from sys_user where IdUser=a.pengaju))
								when a.tipe = 'DEPT. HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
								when a.tipe = 'DIV. HEAD' then '' else 
								 (select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.sect)) end div,		
													
							(SELECT top 1 level FROM DataEvoVal x
								left join sys_level y on x.level = y.nama_lvl $dealer
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								$where_deptlain2
								order by y.urutan asc) as lvl,										
							(SELECT top 1 deptterkait FROM DataEvoVal x
								left join sys_level y on x.level = y.nama_lvl $dealer
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								order by y.urutan asc) as deptterkait
								
							from (
								select nobukti,x.kodedealer, y.idAtasan, userentry as pengaju, y.tipe,
								case 
								when y.tipe = 'SECTION HEAD' then userentry
								when y.tipe = 'DEPT. HEAD' then '' 
								when y.tipe = 'DIV. HEAD' then '' else y.IdAtasan  end sect	
								from DataEvo x
								inner join sys_user y on x.userentry = y.IdUser
								$where_deptlain
							) a
							
						) x 						
						inner join sys_user y on x.lvl = y.tipe and x.deptterkait = y.department
						where ISNULL(lvl, '')!='' and x.kodedealer = '".$user['KodeDealer']."'
						and y.iduser = '".$IdUser."'
						
						union
								
						select x.nobukti, x.kodedealer, x.idAtasan, x.pengaju, x.tipe, x.sect, x.dept, x.div, x.lvl, x.deptterkait,
						isnull(x.admindeptterkait,'') admindeptterkait, isnull(x.sectdeptterkait,'') sectdeptterkait, 
	isnull(x.deptdeptterkait,'') deptdeptterkait
						
						from (
						
							select a.nobukti, a.kodedealer, a.idAtasan, a.pengaju, a.tipe, a.sect,
							
							case 
								when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
								when a.tipe = 'DEPT. HEAD' then a.pengaju
								when a.tipe = 'DIV. HEAD' then '' else (select IdAtasan from sys_user where IdUser=a.idAtasan) end dept,
									
							case 
								when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user 
										where IdUser=(select IdAtasan from sys_user where IdUser=a.pengaju))
								when a.tipe = 'DEPT. HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
								when a.tipe = 'DIV. HEAD' then '' else 
								(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.sect)) end div,
							
							(SELECT top 1 level FROM DataEvoVal x
							left join sys_level y on x.level = y.nama_lvl $dealer
							where nobukti=a.nobukti and ISNULL(level, '')!=''
							and ISNULL(validasi, '')='' 
							order by y.urutan asc) as lvl,
							
							(SELECT top 1 deptterkait FROM DataEvoVal x
							left join sys_level y on x.level = y.nama_lvl $dealer
							where nobukti=a.nobukti and ISNULL(level, '')!=''
							and ISNULL(validasi, '')='' 
							order by y.urutan asc) as deptterkait,
							
							a.admindeptterkait, a.sectdeptterkait, a.deptdeptterkait
							
							from (
								select nobukti,x.kodedealer, y.idAtasan, userentry as pengaju, y.tipe,
								case  
								when y.tipe = 'SECTION HEAD' then userentry
								when y.tipe = 'DEPT. HEAD' then '' 
								when y.tipe = 'DIV. HEAD' then '' else y.IdAtasan end sect,
								(select top 1 uservalidasi from DataEvoVal z 
								where z.nobukti = x.nobukti
								and isnull(z.deptterkait,'') = '".$user['department']."' and z.level = 'ADMIN') admindeptterkait, 									
								(select top 1  uservalidasi from DataEvoVal z 
								where z.nobukti = x.nobukti
								and isnull(z.deptterkait,'') = '".$user['department']."' and z.level = 'SECTION HEAD') sectdeptterkait, 																
								(select top 1  uservalidasi from DataEvoVal z 
								where z.nobukti = x.nobukti
								and isnull(z.deptterkait,'') = '".$user['department']."' and z.level = 'DEPT. HEAD') deptdeptterkait		
								from DataEvo x
								inner join sys_user y on x.userentry = y.IdUser
							) a
							
						) x 
						inner join sys_user y on x.lvl = y.tipe and x.deptterkait = y.department
						where ISNULL(lvl, '')!='' and x.kodedealer = '".$user['KodeDealer']."'
						$where_deptterkait_val
						
												
					) x 
					inner join DataEvo a on x.nobukti=a.nobukti
					inner join sys_user b on a.userentry=b.IdUser
					inner join SPK00..dodealer c on a.kodedealer=c.kodedealer
					where a.nobukti is not null
					$where_lvl 
					
					and evo_id not in (
						select top $start evo_id from (
					
							select x.nobukti, x.kodedealer, x.idAtasan, x.pengaju, x.tipe, x.sect, x.dept, x.div, x.lvl, x.deptterkait,
							'' admindeptterkait, '' sectdeptterkait, '' deptdeptterkait
							
							from (
								select a.nobukti, a.kodedealer, a.idAtasan, a.pengaju, a.tipe, a.sect,										
								case 
									when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
									when a.tipe = 'DEPT. HEAD' then a.pengaju
									when a.tipe = 'DIV. HEAD' then '' else (select IdAtasan from sys_user where IdUser=a.idAtasan) end dept,	
																	
								case 
									when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user 
											where IdUser=(select IdAtasan from sys_user where IdUser=a.pengaju))
									when a.tipe = 'DEPT. HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
									when a.tipe = 'DIV. HEAD' then '' else
									(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.sect)) end div,
																
								(SELECT top 1 level FROM DataEvoVal x
									left join sys_level y on x.level = y.nama_lvl $dealer
									where nobukti=a.nobukti and ISNULL(level, '')!=''
									and ISNULL(validasi, '')='' 
									$where_deptlain2
									order by y.urutan asc) as lvl,										
								(SELECT top 1 deptterkait FROM DataEvoVal x
									left join sys_level y on x.level = y.nama_lvl $dealer
									where nobukti=a.nobukti and ISNULL(level, '')!=''
									and ISNULL(validasi, '')='' 
									order by y.urutan asc) as deptterkait
									
								from (
									select nobukti,x.kodedealer, y.idAtasan, userentry as pengaju, y.tipe,
									case 
									when y.tipe = 'SECTION HEAD' then userentry
									when y.tipe = 'DEPT. HEAD' then '' 
									when y.tipe = 'DIV. HEAD' then '' else y.IdAtasan  end sect	
									from DataEvo x
									inner join sys_user y on x.userentry = y.IdUser
									$where_deptlain
								) a
								
							) x where ISNULL(lvl, '')!='' and kodedealer = '".$user['KodeDealer']."' $where
							and isnull(deptterkait,'') = ''
							
							union
								
							select x.nobukti, x.kodedealer, x.idAtasan, x.pengaju, x.tipe, x.sect, x.dept, x.div, x.lvl, x.deptterkait	,
							'' admindeptterkait, '' sectdeptterkait, '' deptdeptterkait
												
							from (
								select a.nobukti, a.kodedealer, a.idAtasan, a.pengaju, a.tipe, a.sect,										
								case 
									when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
									when a.tipe = 'DEPT. HEAD' then a.pengaju
									when a.tipe = 'DIV. HEAD' then '' else (select IdAtasan from sys_user where IdUser=a.idAtasan) end dept,	
																	
								case 
									when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user 
											where IdUser=(select IdAtasan from sys_user where IdUser=a.pengaju))
									when a.tipe = 'DEPT. HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
									when a.tipe = 'DIV. HEAD' then '' else 
									(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.sect)) end div,	
															
								(SELECT top 1 level FROM DataEvoVal x
									left join sys_level y on x.level = y.nama_lvl $dealer
									where nobukti=a.nobukti and ISNULL(level, '')!=''
									and ISNULL(validasi, '')='' 
									$where_deptlain2
									order by y.urutan asc) as lvl,										
								(SELECT top 1 deptterkait FROM DataEvoVal x
									left join sys_level y on x.level = y.nama_lvl $dealer
									where nobukti=a.nobukti and ISNULL(level, '')!=''
									and ISNULL(validasi, '')='' 
									order by y.urutan asc) as deptterkait
									
								from (
									select nobukti,x.kodedealer, y.idAtasan, userentry as pengaju, y.tipe,
									case 
									when y.tipe = 'SECTION HEAD' then userentry
									when y.tipe = 'DEPT. HEAD' then '' 
									when y.tipe = 'DIV. HEAD' then '' else y.IdAtasan  end sect	
									from DataEvo x
									inner join sys_user y on x.userentry = y.IdUser
									$where_deptlain
								) a
								
							) x 						
							inner join sys_user y on x.lvl = y.tipe and x.deptterkait = y.department
							where ISNULL(lvl, '')!='' and x.kodedealer = '".$user['KodeDealer']."'
							and y.iduser = '".$IdUser."'
							
								union
									
							select x.nobukti, x.kodedealer, x.idAtasan, x.pengaju, x.tipe, x.sect, x.dept, x.div, x.lvl, x.deptterkait,
							isnull(x.admindeptterkait,'') admindeptterkait, isnull(x.sectdeptterkait,'') sectdeptterkait, 
	isnull(x.deptdeptterkait,'') deptdeptterkait
							
							from (
							
								select a.nobukti, a.kodedealer, a.idAtasan, a.pengaju, a.tipe, a.sect,
								
								case 
									when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
									when a.tipe = 'DEPT. HEAD' then a.pengaju
									when a.tipe = 'DIV. HEAD' then '' else (select IdAtasan from sys_user where IdUser=a.idAtasan) end dept,
										
								case 
									when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user 
											where IdUser=(select IdAtasan from sys_user where IdUser=a.pengaju))
									when a.tipe = 'DEPT. HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
									when a.tipe = 'DIV. HEAD' then '' else 
									(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.sect)) end div,
								
								(SELECT top 1 level FROM DataEvoVal x
								left join sys_level y on x.level = y.nama_lvl $dealer
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								order by y.urutan asc) as lvl,
								
								(SELECT top 1 deptterkait FROM DataEvoVal x
								left join sys_level y on x.level = y.nama_lvl $dealer
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								order by y.urutan asc) as deptterkait,
								
								a.admindeptterkait, a.sectdeptterkait, a.deptdeptterkait
								
								from (
									select nobukti,x.kodedealer, y.idAtasan, userentry as pengaju, y.tipe,
									case  
									when y.tipe = 'SECTION HEAD' then userentry
									when y.tipe = 'DEPT. HEAD' then '' 
									when y.tipe = 'DIV. HEAD' then '' else y.IdAtasan end sect,
									(select top 1 uservalidasi from DataEvoVal z 
									where z.nobukti = x.nobukti
									and isnull(z.deptterkait,'') = '".$user['department']."' and z.level = 'ADMIN') admindeptterkait, 									
									(select top 1  uservalidasi from DataEvoVal z 
									where z.nobukti = x.nobukti
									and isnull(z.deptterkait,'') = '".$user['department']."' and z.level = 'SECTION HEAD') sectdeptterkait, 																
									(select top 1  uservalidasi from DataEvoVal z 
									where z.nobukti = x.nobukti
									and isnull(z.deptterkait,'') = '".$user['department']."' and z.level = 'DEPT. HEAD') deptdeptterkait		
									from DataEvo x
									inner join sys_user y on x.userentry = y.IdUser
								) a
								
							) x 
							inner join sys_user y on x.lvl = y.tipe and x.deptterkait = y.department
							where ISNULL(lvl, '')!='' and x.kodedealer = '".$user['KodeDealer']."'
							$where_deptterkait_val
							
					
						) x 
						inner join DataEvo a on x.nobukti=a.nobukti
						inner join sys_user b on a.userentry=b.IdUser
						inner join SPK00..dodealer c on a.kodedealer=c.kodedealer
						where a.nobukti is not null $where_lvl
					) $src
				";
				
			}
						
		}
				
		//echo "<pre>$sql</pre>";
		$result = mssql_query($sql);
		
		/*$total = mssql_num_rows(mssql_query("
			select evo_id from (
				select * from(select nobukti,kodedealer,userentry as pengaju,IdAtasan as sect,
				(select IdAtasan from sys_user where IdUser=a.IdAtasan) as dept,
				(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.IdAtasan)) as div,
				(SELECT top 1 level FROM DataEvoVal where nobukti=a.nobukti and ISNULL(level, '')!='' and ISNULL(validasi, '')='' order by idVal desc) as lvl
				from DataEvo a) x where ISNULL(lvl, '')!='' and kodedealer = '".$user['KodeDealer']."' $s_admin $s_section $s_dept $s_div $src
			) x 
			inner join DataEvo a on x.nobukti=a.nobukti
			inner join sys_user b on a.userentry=b.IdUser
			inner join SPK00..dodealer c on a.kodedealer=c.kodedealer
			where lvl = '".$level."' $src
		"));*/
	
		if ($level=='WAITDEPTTERKAIT' or $level=='REQDEPTTERKAIT') {
				
			$total = mssql_num_rows(mssql_query("
				select a.evo_id
				from (
					select x.nobukti, x.kodedealer, x.idAtasan, x.pengaju, x.tipe, x.sect, x.dept, x.div, x.lvl, x.deptterkait,
					'' admindeptterkait, '' sectdeptterkait, '' deptdeptterkait
					from (
					
						select a.nobukti, a.kodedealer, a.idAtasan, a.pengaju, a.tipe, a.sect,
									
						case 
							when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
							when a.tipe = 'DEPT. HEAD' then a.pengaju
							when a.tipe = 'DIV. HEAD' then '' else (select IdAtasan from sys_user where IdUser=a.idAtasan) end dept,
								
						case 
							when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user 
									where IdUser=(select IdAtasan from sys_user where IdUser=a.pengaju))
							when a.tipe = 'DEPT. HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
							when a.tipe = 'DIV. HEAD' then '' else 
							(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.sect)) end div,
						
						(SELECT top 1 level FROM DataEvoVal x
									left join sys_level y on x.level = y.nama_lvl $dealer
									where nobukti=a.nobukti and ISNULL(level, '')!=''
									and ISNULL(validasi, '')='' 
									$where_deptlain2
									order by y.urutan asc) as lvl,
									
						(SELECT top 1 deptterkait FROM DataEvoVal x
									left join sys_level y on x.level = y.nama_lvl $dealer
									where nobukti=a.nobukti and ISNULL(level, '')!=''
									and ISNULL(validasi, '')='' 
									order by y.urutan asc) as deptterkait
												
						from (
							select nobukti,x.kodedealer, y.idAtasan, userentry as pengaju, y.tipe,
							case
							when y.tipe = 'SECTION HEAD' then userentry
							when y.tipe = 'DEPT. HEAD' then '' 
							when y.tipe = 'DIV. HEAD' then '' else y.IdAtasan  end sect	
							from DataEvo x
							inner join sys_user y on x.userentry = y.IdUser
							$where_deptlain
						) a
						
					) x 
					$table_sys				
					where ISNULL(lvl, '')!='' and x.kodedealer = '".$user['KodeDealer']."' $where
					$where_deptlain3
					
				) x 
				inner join DataEvo a on x.nobukti=a.nobukti
				inner join sys_user b on a.userentry=b.IdUser
				inner join SPK00..dodealer c on a.kodedealer=c.kodedealer
				where a.nobukti is not null $where_lvl
				 $src"));
				
				
		} else {
		
			if ($level=="DIREKSI" or  $level=="DIREKSI 2" ) {
			
				$total = mssql_num_rows(mssql_query("
					select a.evo_id
					from (
					
						select x.nobukti, x.kodedealer, x.idAtasan, x.pengaju, x.tipe, x.sect, x.dept, x.div, x.lvl, x.deptterkait,
						'' admindeptterkait, '' sectdeptterkait, '' deptdeptterkait
						
						from (
							select a.nobukti, a.kodedealer, a.idAtasan, a.pengaju, a.tipe, a.sect,										
							case 
								when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
								when a.tipe = 'DEPT. HEAD' then a.pengaju
								when a.tipe = 'DIV. HEAD' then '' else (select IdAtasan from sys_user where IdUser=a.idAtasan) end dept,		
															
							case 
								when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user 
										where IdUser=(select IdAtasan from sys_user where IdUser=a.pengaju))
								when a.tipe = 'DEPT. HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
								when a.tipe = 'DIV. HEAD' then '' else 
								(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.sect)) end div,	
														
							(SELECT top 1 level FROM DataEvoVal x
								left join sys_level y on x.level = y.nama_lvl $dealer
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								order by y.urutan asc) as lvl,										
							(SELECT top 1 deptterkait FROM DataEvoVal x
								left join sys_level y on x.level = y.nama_lvl $dealer
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								order by y.urutan asc) as deptterkait
								
							from (
								select nobukti,x.kodedealer, y.idAtasan, userentry as pengaju, y.tipe,
								case  
								when y.tipe = 'SECTION HEAD' then userentry
								when y.tipe = 'DEPT. HEAD' then '' 
								when y.tipe = 'DIV. HEAD' then '' else y.IdAtasan end sect	
								from DataEvo x
								inner join sys_user y on x.userentry = y.IdUser
							) a
							
						) x where ISNULL(lvl, '')!='' and kodedealer = '".$user['KodeDealer']."' $where
						
					) x 
					inner join DataEvo a on x.nobukti=a.nobukti
					inner join sys_user b on a.userentry=b.IdUser
					inner join SPK00..dodealer c on a.kodedealer=c.kodedealer
					where a.nobukti is not null
					$where_lvl $src"));
			
			} else if ($level=="FINANCE") {
			
				$total = mssql_num_rows(mssql_query("
					select a.evo_id
					from (
					
						select x.nobukti, x.kodedealer, x.idAtasan, x.pengaju, x.tipe, x.sect, x.dept, x.div, x.lvl, x.deptterkait,
						'' admindeptterkait, '' sectdeptterkait, '' deptdeptterkait
						
						from (
							select a.nobukti, a.kodedealer, a.idAtasan, a.pengaju, a.tipe, a.sect,										
							case
								when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
								when a.tipe = 'DEPT. HEAD' then a.pengaju
								when a.tipe = 'DIV. HEAD' then '' else (select IdAtasan from sys_user where IdUser=a.idAtasan) end dept,	
																
							case 
								when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user 
										where IdUser=(select IdAtasan from sys_user where IdUser=a.pengaju))
								when a.tipe = 'DEPT. HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
								when a.tipe = 'DIV. HEAD' then '' else 
								(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.sect)) end div,	
														
							(SELECT top 1 level FROM DataEvoVal x
								left join sys_level y on x.level = y.nama_lvl $dealer
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								$where_deptlain2
								order by y.urutan asc) as lvl,										
							(SELECT top 1 deptterkait FROM DataEvoVal x
								left join sys_level y on x.level = y.nama_lvl $dealer
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								order by y.urutan asc) as deptterkait
								
							from (
								select nobukti,x.kodedealer, y.idAtasan, userentry as pengaju, y.tipe,
								case 
								when y.tipe = 'SECTION HEAD' then userentry
								when y.tipe = 'DEPT. HEAD' then '' 
								when y.tipe = 'DIV. HEAD' then '' else y.IdAtasan  end sect	
								from DataEvo x
								inner join sys_user y on x.userentry = y.IdUser
								$where_deptlain
							) a
							
						) x where ISNULL(lvl, '')!='' and kodedealer = '".$user['KodeDealer']."' $where
						and isnull(deptterkait,'') = ''
						
					) x 
					inner join DataEvo a on x.nobukti=a.nobukti
					inner join sys_user b on a.userentry=b.IdUser
					inner join SPK00..dodealer c on a.kodedealer=c.kodedealer
					where a.nobukti is not null
					$where_lvl $src"));
					
			
			} else if (($level=="DEPT. HEAD FINANCE / DIV. HEAD FAST" or $level=="DEPT. HEAD FINANCE") and ($user['department']=='FINANCE' or $user['department']=='all')  and $user['divisi']=="FINANCE and ACCOUNTING") {
				
				$total = mssql_num_rows(mssql_query("
					select a.evo_id
					from (
					
						select x.nobukti, x.kodedealer, x.idAtasan, x.pengaju, x.tipe, x.sect, x.dept, x.div, x.lvl, x.deptterkait,
						'' admindeptterkait, '' sectdeptterkait, '' deptdeptterkait
						
						from (
							select a.nobukti, a.kodedealer, a.idAtasan, a.pengaju, a.tipe, a.sect,										
							case 
								when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
								when a.tipe = 'DEPT. HEAD' then a.pengaju
								when a.tipe = 'DIV. HEAD' then '' else (select IdAtasan from sys_user where IdUser=a.idAtasan) end dept,
																	
							case
								when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user 
										where IdUser=(select IdAtasan from sys_user where IdUser=a.pengaju))
								when a.tipe = 'DEPT. HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
								when a.tipe = 'DIV. HEAD' then '' else 
								(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.sect)) end div,	
														
							(SELECT top 1 level FROM DataEvoVal x
								left join sys_level y on x.level = y.nama_lvl $dealer
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								$where_deptlain2
								order by y.urutan asc) as lvl,										
							(SELECT top 1 deptterkait FROM DataEvoVal x
								left join sys_level y on x.level = y.nama_lvl $dealer
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								order by y.urutan asc) as deptterkait
								
							from (
								select nobukti,x.kodedealer, y.idAtasan, userentry as pengaju, y.tipe,
								case 
								when y.tipe = 'SECTION HEAD' then userentry
								when y.tipe = 'DEPT. HEAD' then '' 
								when y.tipe = 'DIV. HEAD' then '' else y.IdAtasan  end sect	
								from DataEvo x
								inner join sys_user y on x.userentry = y.IdUser
								$where_deptlain
							) a
							
						) x where ISNULL(lvl, '')!='' and kodedealer = '".$user['KodeDealer']."' $where
						and isnull(deptterkait,'') = ''
						
						union
							
						select x.nobukti, x.kodedealer, x.idAtasan, x.pengaju, x.tipe, x.sect, x.dept, x.div, x.lvl, x.deptterkait	,
						'' admindeptterkait, '' sectdeptterkait, '' deptdeptterkait
											
						from (
							select a.nobukti, a.kodedealer, a.idAtasan, a.pengaju, a.tipe, a.sect,										
							case 
								when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
								when a.tipe = 'DEPT. HEAD' then a.pengaju
								when a.tipe = 'DIV. HEAD' then '' else (select IdAtasan from sys_user where IdUser=a.idAtasan) end dept,	
																
							case 
								when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user 
										where IdUser=(select IdAtasan from sys_user where IdUser=a.pengaju))
								when a.tipe = 'DEPT. HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
								when a.tipe = 'DIV. HEAD' then '' else 
								(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.sect)) end div,	
														
							(SELECT top 1 level FROM DataEvoVal x
								left join sys_level y on x.level = y.nama_lvl $dealer
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								$where_deptlain2
								order by y.urutan asc) as lvl,										
							(SELECT top 1 deptterkait FROM DataEvoVal x
								left join sys_level y on x.level = y.nama_lvl $dealer 
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								order by y.urutan asc) as deptterkait
								
							from (
								select nobukti,x.kodedealer, y.idAtasan, userentry as pengaju, y.tipe,
								case 
								when y.tipe = 'SECTION HEAD' then userentry
								when y.tipe = 'DEPT. HEAD' then '' 
								when y.tipe = 'DIV. HEAD' then '' else y.IdAtasan  end sect	
								from DataEvo x
								inner join sys_user y on x.userentry = y.IdUser
								$where_deptlain
							) a
							
						) x 						
						inner join sys_user y on x.lvl = y.tipe and x.deptterkait = y.department
						where ISNULL(lvl, '')!='' and x.kodedealer = '".$user['KodeDealer']."'
						and y.iduser = '".$IdUser."'
						
							union
								
						select x.nobukti, x.kodedealer, x.idAtasan, x.pengaju, x.tipe, x.sect, x.dept, x.div, x.lvl, x.deptterkait,
						isnull(x.admindeptterkait,'') admindeptterkait, isnull(x.sectdeptterkait,'') sectdeptterkait, 
	isnull(x.deptdeptterkait,'') deptdeptterkait
						
						from (
						
							select a.nobukti, a.kodedealer, a.idAtasan, a.pengaju, a.tipe, a.sect,
							
							case 
								when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
								when a.tipe = 'DEPT. HEAD' then a.pengaju
								when a.tipe = 'DIV. HEAD' then '' else (select IdAtasan from sys_user where IdUser=a.idAtasan) end dept,
									
							case 
								when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user 
										where IdUser=(select IdAtasan from sys_user where IdUser=a.pengaju))
								when a.tipe = 'DEPT. HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
								when a.tipe = 'DIV. HEAD' then '' else 
								(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.sect)) end div,
							
							(SELECT top 1 level FROM DataEvoVal x
							left join sys_level y on x.level = y.nama_lvl $dealer
							where nobukti=a.nobukti and ISNULL(level, '')!=''
							and ISNULL(validasi, '')='' 
							order by y.urutan asc) as lvl,
							
							(SELECT top 1 deptterkait FROM DataEvoVal x
							left join sys_level y on x.level = y.nama_lvl $dealer
							where nobukti=a.nobukti and ISNULL(level, '')!=''
							and ISNULL(validasi, '')='' 
							order by y.urutan asc) as deptterkait,
							
							a.admindeptterkait, a.sectdeptterkait, a.deptdeptterkait
							
							from (
								select nobukti,x.kodedealer, y.idAtasan, userentry as pengaju, y.tipe,
								case 
								when y.tipe = 'SECTION HEAD' then userentry
								when y.tipe = 'DEPT. HEAD' then '' 
								when y.tipe = 'DIV. HEAD' then '' else y.IdAtasan  end sect,
								(select top 1 uservalidasi from DataEvoVal z 
								where z.nobukti = x.nobukti
								and isnull(z.deptterkait,'') = '".$user['department']."' and z.level = 'ADMIN') admindeptterkait, 									
								(select top 1  uservalidasi from DataEvoVal z 
								where z.nobukti = x.nobukti
								and isnull(z.deptterkait,'') = '".$user['department']."' and z.level = 'SECTION HEAD') sectdeptterkait, 																
								(select top 1  uservalidasi from DataEvoVal z 
								where z.nobukti = x.nobukti
								and isnull(z.deptterkait,'') = '".$user['department']."' and z.level = 'DEPT. HEAD') deptdeptterkait		
								from DataEvo x
								inner join sys_user y on x.userentry = y.IdUser
							) a
							
						) x 
						inner join sys_user y on x.lvl = y.tipe and x.deptterkait = y.department
						where ISNULL(lvl, '')!='' and x.kodedealer = '".$user['KodeDealer']."'
						$where_deptterkait_val
						
						
							union
								
						select x.nobukti, x.kodedealer, x.idAtasan, x.pengaju, x.tipe, x.sect, x.dept, x.div, x.lvl, x.deptterkait,
						isnull(x.admindeptterkait,'') admindeptterkait, isnull(x.sectdeptterkait,'') sectdeptterkait, 
	isnull(x.deptdeptterkait,'') deptdeptterkait
						
						from (
						
							select a.nobukti, a.kodedealer, a.idAtasan, a.pengaju, a.tipe, a.sect,
							
							case 
								when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
								when a.tipe = 'DEPT. HEAD' then a.pengaju
								when a.tipe = 'DIV. HEAD' then '' else (select IdAtasan from sys_user where IdUser=a.idAtasan) end dept,
									
							case 
								when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user 
										where IdUser=(select IdAtasan from sys_user where IdUser=a.pengaju))
								when a.tipe = 'DEPT. HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
								when a.tipe = 'DIV. HEAD' then '' else 
								(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.sect)) end div,
							
							(SELECT top 1 level FROM DataEvoVal x
							left join sys_level y on x.level = y.nama_lvl $dealer
							where nobukti=a.nobukti and ISNULL(level, '')!=''
							and ISNULL(validasi, '')='' 
							order by y.urutan asc) as lvl,
							
							(SELECT top 1 deptterkait FROM DataEvoVal x
							left join sys_level y on x.level = y.nama_lvl $dealer
							where nobukti=a.nobukti and ISNULL(level, '')!=''
							and ISNULL(validasi, '')='' 
							order by y.urutan asc) as deptterkait,
							
							'' admindeptterkait, '' sectdeptterkait, '' deptdeptterkait
							
							from (
								select nobukti,x.kodedealer, y.idAtasan, userentry as pengaju, y.tipe,
								case 
								when y.tipe = 'SECTION HEAD' then userentry
								when y.tipe = 'DEPT. HEAD' then '' 
								when y.tipe = 'DIV. HEAD' then '' else y.IdAtasan  end sect,
								(select top 1 uservalidasi from DataEvoVal z 
								where z.nobukti = x.nobukti
								and isnull(z.deptterkait,'') = '".$user['department']."' and z.level = 'ADMIN') admindeptterkait, 									
								(select top 1  uservalidasi from DataEvoVal z 
								where z.nobukti = x.nobukti
								and isnull(z.deptterkait,'') = '".$user['department']."' and z.level = 'SECTION HEAD') sectdeptterkait, 																
								(select top 1  uservalidasi from DataEvoVal z 
								where z.nobukti = x.nobukti
								and isnull(z.deptterkait,'') = '".$user['department']."' and z.level = 'DEPT. HEAD') deptdeptterkait		
								from DataEvo x
								inner join sys_user y on x.userentry = y.IdUser
							) a
							
						) x 
						where ISNULL(lvl, '')!='' and x.kodedealer = '".$user['KodeDealer']."'
						$where_deptterkait_val
						
						
					) x 
					inner join DataEvo a on x.nobukti=a.nobukti
					inner join sys_user b on a.userentry=b.IdUser
					inner join SPK00..dodealer c on a.kodedealer=c.kodedealer
					where a.nobukti is not null
					$where_lvl $src"));
					
			} else {
				
				$total = mssql_num_rows(mssql_query("
					select a.evo_id
					from (
					
						select x.nobukti, x.kodedealer, x.idAtasan, x.pengaju, x.tipe, x.sect, x.dept, x.div, x.lvl, x.deptterkait,
						'' admindeptterkait, '' sectdeptterkait, '' deptdeptterkait
						
						from (
							select a.nobukti, a.kodedealer, a.idAtasan, a.pengaju, a.tipe, a.sect,										
							case 
								when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
								when a.tipe = 'DEPT. HEAD' then a.pengaju
								when a.tipe = 'DIV. HEAD' then '' else (select IdAtasan from sys_user where IdUser=a.idAtasan) end dept,
																
							case 
								when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user 
										where IdUser=(select IdAtasan from sys_user where IdUser=a.pengaju))
								when a.tipe = 'DEPT. HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
								when a.tipe = 'DIV. HEAD' then '' else 
								(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.sect)) end div,	
														
							(SELECT top 1 level FROM DataEvoVal x
								left join sys_level y on x.level = y.nama_lvl $dealer
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								$where_deptlain2
								order by y.urutan asc) as lvl,										
							(SELECT top 1 deptterkait FROM DataEvoVal x
								left join sys_level y on x.level = y.nama_lvl $dealer
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								order by y.urutan asc) as deptterkait
								
							from (
								select nobukti,x.kodedealer, y.idAtasan, userentry as pengaju, y.tipe,
								case  
								when y.tipe = 'SECTION HEAD' then userentry
								when y.tipe = 'DEPT. HEAD' then '' 
								when y.tipe = 'DIV. HEAD' then '' else y.IdAtasan end sect	
								from DataEvo x
								inner join sys_user y on x.userentry = y.IdUser
								$where_deptlain
							) a
							
						) x where ISNULL(lvl, '')!='' and kodedealer = '".$user['KodeDealer']."' $where
						and isnull(deptterkait,'') = ''
						
						union
							
						select x.nobukti, x.kodedealer, x.idAtasan, x.pengaju, x.tipe, x.sect, x.dept, x.div, x.lvl, x.deptterkait	,
						'' admindeptterkait, '' sectdeptterkait, '' deptdeptterkait
											
						from (
							select a.nobukti, a.kodedealer, a.idAtasan, a.pengaju, a.tipe, a.sect,										
							case 
								when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
								when a.tipe = 'DEPT. HEAD' then a.pengaju
								when a.tipe = 'DIV. HEAD' then '' else (select IdAtasan from sys_user where IdUser=a.idAtasan) end dept,
																	
							case
								when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user 
										where IdUser=(select IdAtasan from sys_user where IdUser=a.pengaju))
								when a.tipe = 'DEPT. HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
								when a.tipe = 'DIV. HEAD' then '' else 
								(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.sect)) end div,	
														
							(SELECT top 1 level FROM DataEvoVal x
								left join sys_level y on x.level = y.nama_lvl $dealer
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								$where_deptlain2
								order by y.urutan asc) as lvl,										
							(SELECT top 1 deptterkait FROM DataEvoVal x
								left join sys_level y on x.level = y.nama_lvl $dealer 
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								order by y.urutan asc) as deptterkait
								
							from (
								select nobukti,x.kodedealer, y.idAtasan, userentry as pengaju, y.tipe,
								case 
								when y.tipe = 'SECTION HEAD' then userentry
								when y.tipe = 'DEPT. HEAD' then '' 
								when y.tipe = 'DIV. HEAD' then '' else y.IdAtasan  end sect	
								from DataEvo x
								inner join sys_user y on x.userentry = y.IdUser
								$where_deptlain
							) a
							
						) x 						
						inner join sys_user y on x.lvl = y.tipe and x.deptterkait = y.department
						where ISNULL(lvl, '')!='' and x.kodedealer = '".$user['KodeDealer']."'
						and y.iduser = '".$IdUser."'
						
							union
								
						select x.nobukti, x.kodedealer, x.idAtasan, x.pengaju, x.tipe, x.sect, x.dept, x.div, x.lvl, x.deptterkait,
						isnull(x.admindeptterkait,'') admindeptterkait, isnull(x.sectdeptterkait,'') sectdeptterkait, 
	isnull(x.deptdeptterkait,'') deptdeptterkait
						
						from (
						
							select a.nobukti, a.kodedealer, a.idAtasan, a.pengaju, a.tipe, a.sect,
							
							case 
								when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
								when a.tipe = 'DEPT. HEAD' then a.pengaju
								when a.tipe = 'DIV. HEAD' then '' else (select IdAtasan from sys_user where IdUser=a.idAtasan) end dept,
									
							case
								when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user 
										where IdUser=(select IdAtasan from sys_user where IdUser=a.pengaju))
								when a.tipe = 'DEPT. HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
								when a.tipe = 'DIV. HEAD' then '' else 
								(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.sect)) end div,
							
							(SELECT top 1 level FROM DataEvoVal x
							left join sys_level y on x.level = y.nama_lvl $dealer
							where nobukti=a.nobukti and ISNULL(level, '')!=''
							and ISNULL(validasi, '')='' 
							order by y.urutan asc) as lvl,
							
							(SELECT top 1 deptterkait FROM DataEvoVal x
							left join sys_level y on x.level = y.nama_lvl $dealer
							where nobukti=a.nobukti and ISNULL(level, '')!=''
							and ISNULL(validasi, '')='' 
							order by y.urutan asc) as deptterkait,
							
							a.admindeptterkait, a.sectdeptterkait, a.deptdeptterkait
							
							from (
								select nobukti,x.kodedealer, y.idAtasan, userentry as pengaju, y.tipe,
								case 
								when y.tipe = 'SECTION HEAD' then userentry
								when y.tipe = 'DEPT. HEAD' then '' 
								when y.tipe = 'DIV. HEAD' then '' else y.IdAtasan  end sect,
								(select top 1 uservalidasi from DataEvoVal z 
								where z.nobukti = x.nobukti
								and isnull(z.deptterkait,'') = '".$user['department']."' and z.level = 'ADMIN') admindeptterkait, 									
								(select top 1  uservalidasi from DataEvoVal z 
								where z.nobukti = x.nobukti
								and isnull(z.deptterkait,'') = '".$user['department']."' and z.level = 'SECTION HEAD') sectdeptterkait, 																
								(select top 1  uservalidasi from DataEvoVal z 
								where z.nobukti = x.nobukti
								and isnull(z.deptterkait,'') = '".$user['department']."' and z.level = 'DEPT. HEAD') deptdeptterkait		
								from DataEvo x
								inner join sys_user y on x.userentry = y.IdUser
							) a
							
						) x 
						inner join sys_user y on x.lvl = y.tipe and x.deptterkait = y.department
						where ISNULL(lvl, '')!='' and x.kodedealer = '".$user['KodeDealer']."'
						$where_deptterkait_val
						
						
					) x 
					inner join DataEvo a on x.nobukti=a.nobukti
					inner join sys_user b on a.userentry=b.IdUser
					inner join SPK00..dodealer c on a.kodedealer=c.kodedealer
					where a.nobukti is not null
					$where_lvl $src"));
					
			}
		
		}
		
		
		$rows = array();
		while ($row = mssql_fetch_array($result)) {
			$rows[] = $row;
		}
		
		header("Content-type: text/xml");
		$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		$xml .= "<rows>";
		$xml .= "<page>$page</page>";
		foreach($rows as $row) {
			$xml .= "<row id='".$row['evo_id']."'>";
			$xml .= "<cell><![CDATA[<input type='checkbox' id='chk-1' name='id[]' value='".$row['evo_id']."'>]]></cell>";
			$xml .= "<cell><![CDATA[".utf8_encode($row['nobukti'])."]]></cell>";
			$xml .= "<cell><![CDATA[".datenull($row['tgl_pengajuan'])."]]></cell>";
			$xml .= "<cell><![CDATA[".utf8_encode($row['nama_aju'])."]]></cell>";
			$xml .= "<cell><![CDATA[".utf8_encode($row['NamaDealer'])." - ".utf8_encode($row['department'])."]]></cell>";
			$xml .= "<cell><![CDATA[".utf8_encode($row['namaVendor'])."]]></cell>";
			$xml .= "<cell><![CDATA[<div style='text-align:right;padding:0'>".number_format($row['amount'],0,",",".")."</div>]]></cell>";
			$xml .= "<cell><![CDATA[".utf8_encode($row['keterangan'])."]]></cell>";
			$xml .= "</row>";
		}
		$xml .= "<total>".$total."</total>";	
		$xml .= "</rows>";
		echo $xml;
	}

	function proval($lvl,$IdUser){
		require_once ('../inc/conn.php');
		$user = mssql_fetch_array(mssql_query("select * from sys_user where IdUser = '".$IdUser."'"));
		if ($user['tipe']=='ADMIN') { $s_admin = "and pengaju = '".$IdUser."'"; } else { $s_admin = ""; }
		//if ($user['tipe']=='ACCOUNTING') { $s_accounting = "and pengaju = '".$IdUser."'"; } else { $s_admin = ""; }
		if ($user['tipe']=='SECTION HEAD' or $user['tipe']=='ADH') { $s_section = "and sect = '".$IdUser."'"; } else { $s_section = ""; }
		
		$s_dept = "";
		$s_div = ""; 
		/*if (($user['tipe']=='DEPT. HEAD' or $user['tipe']=='KEPALA CABANG') and $lvl!='DEPT. HEAD FINANCE / DIV. HEAD FAST') { 
			$s_dept = "and dept = '".$IdUser."'"; }
		if (($user['tipe']=='DEPT. HEAD' or $user['tipe']=='KEPALA CABANG') and $lvl=='DEPT. HEAD FINANCE / DIV. HEAD FAST') { 
			$s_dept = "and (dept = '".$IdUser."' or dept = 'all') "; } 
		
		if ($user['tipe']=='DIV. HEAD' and $lvl!='DEPT. HEAD FINANCE / DIV. HEAD FAST') { 
			$s_div = "and (div = '".$IdUser."')";  }
			
		if ($user['tipe']=='DIV. HEAD' and $lvl=='DEPT. HEAD FINANCE / DIV. HEAD FAST') { 
			$s_div = "and (div = '".$IdUser."' or div = 'all')";  }
		*/
		if (($user['tipe']=='DEPT. HEAD' or $user['tipe']=='KEPALA CABANG')) { 
			if ($lvl=='DEPT. HEAD FINANCE / DIV. HEAD FAST' or $lvl=='DEPT. HEAD FINANCE') {
				$s_dept = "and (dept = '".$IdUser."' or dept = 'all') ";
			} else {
				$s_dept = "and dept = '".$IdUser."'"; 
			}
		}
		
		if ($user['tipe']=='DIV. HEAD') { 
			if ($lvl=='DEPT. HEAD FINANCE / DIV. HEAD FAST' or $lvl=='DEPT. HEAD FINANCE') {
				$s_div = "and (div = '".$IdUser."' or div = 'all')"; 
			} else {
				$s_div = "and (div = '".$IdUser."')";
			}
		}
		
		
		/*if ($_SESSION['level']=="DIREKSI" or  $_SESSION['level']=="DIREKSI 2") {
			//if ($lvl =="DIREKSI") {
				$lvl = $_SESSION['level'];
			//}
		}*/
		
		// $user['departement']=='FINANCE' and $user['divisi']=="FINANCE and ACCOUNTING"
		
		/*if ($lvl=='WAITDEPTTERKAIT') {
			$sql = "
				select count(*) as tot from (
					
					select * from
						(select nobukti,kodedealer,userentry as pengaju,IdAtasan as sect,
						(select IdAtasan from sys_user where IdUser=a.IdAtasan) as dept,
						(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.IdAtasan)) as div,
						(SELECT top 1 level FROM DataEvoVal where nobukti=a.nobukti and ISNULL(level, '')!='' and ISNULL(validasi, '')='' order by idVal desc) as lvl
						from DataEvo a
						where isnull(deptterkait,'') <> ''
					) x where ISNULL(lvl, '')!='' and kodedealer = '".$user['KodeDealer']."' $s_admin $s_section $s_dept $s_div
					
				) x";
			
		} else if ($lvl=='REQDEPTTERKAIT') {
			$sql = "
				select count(*) as tot from (
					
					select * from
						(select nobukti,kodedealer,userentry as pengaju,IdAtasan as sect,
						(select IdAtasan from sys_user where IdUser=a.IdAtasan) as dept,
						(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.IdAtasan)) as div,
						(SELECT top 1 level FROM DataEvoVal where nobukti=a.nobukti and ISNULL(level, '')!='' and ISNULL(validasi, '')='' order by idVal desc) as lvl
						from DataEvo a
						where isnull(deptterkait,'') = '".$user['department']."'
					) x where ISNULL(lvl, '')!='' and kodedealer = '".$user['KodeDealer']."'
					
				) x GROUP BY lvl";
			
		} else {
			if ($_SESSION['level']=="DIREKSI" or  $_SESSION['level']=="DIREKSI 2") {
				$sql = "
					select count(*) as tot from (
						select * from(select nobukti,kodedealer,userentry as pengaju,IdAtasan as sect,
						(select IdAtasan from sys_user where IdUser=a.IdAtasan) as dept,
						(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.IdAtasan)) as div,
						(SELECT top 1 level FROM DataEvoVal where nobukti=a.nobukti and ISNULL(level, '')!='' and ISNULL(validasi, '')='' order by idVal desc) as lvl
						from DataEvo a) x where ISNULL(lvl, '')!='' and kodedealer = '".$user['KodeDealer']."' $s_admin $s_section $s_dept $s_div
					) x where lvl = '".$lvl."' GROUP BY lvl
				";
				
			} else {
				if ($lvl=="DIREKSI" or  $lvl=="DIREKSI 2" ) {
					$sql = "
						select count(*) as tot from (
							select * from(select nobukti,kodedealer,userentry as pengaju,IdAtasan as sect,
							(select IdAtasan from sys_user where IdUser=a.IdAtasan) as dept,
							(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.IdAtasan)) as div,
							(SELECT top 1 level FROM DataEvoVal where nobukti=a.nobukti and ISNULL(level, '')!='' and ISNULL(validasi, '')='' order by idVal desc) as lvl
							from DataEvo a) x where ISNULL(lvl, '')!='' and kodedealer = '".$user['KodeDealer']."' $s_admin $s_section $s_dept $s_div
						) x where lvl in ('DIREKSI','DIREKSI 2') GROUP BY lvl";
						
				} else{
					$sql = "
						select count(*) as tot from (
							select * from(select nobukti,kodedealer,userentry as pengaju,IdAtasan as sect,
							(select IdAtasan from sys_user where IdUser=a.IdAtasan) as dept,
							(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.IdAtasan)) as div,
							(SELECT top 1 level FROM DataEvoVal where nobukti=a.nobukti and ISNULL(level, '')!='' and ISNULL(validasi, '')='' order by idVal desc) as lvl
							from DataEvo a) x where ISNULL(lvl, '')!='' and kodedealer = '".$user['KodeDealer']."' $s_admin $s_section $s_dept $s_div
						) x where lvl = '".$lvl."' GROUP BY lvl";

				}
			}
			
		}*/
		
		if ($user['KodeDealer']=='2010') {
			$dealer = " and y.is_dealer = '0' ";
		} else {
			$dealer = " and y.is_dealer = '1' ";
		}
		
		if ($lvl=='WAITDEPTTERKAIT') {
					
			$sql = "
				select count(*) as tot from (					
					select x.nobukti, x.kodedealer, x.idAtasan, x.pengaju, x.tipe, x.sect, x.dept, x.div, x.lvl, x.deptterkait
					from (
					
						select a.nobukti, a.kodedealer, a.idAtasan, a.pengaju, a.tipe, a.sect,
								
						case
							when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
							when a.tipe = 'DEPT. HEAD' then a.pengaju
							when a.tipe = 'DIV. HEAD' then '' else (select IdAtasan from sys_user where IdUser=a.idAtasan) end dept,
								
						case 
							when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user 
									where IdUser=(select IdAtasan from sys_user where IdUser=a.pengaju))
							when a.tipe = 'DEPT. HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
							when a.tipe = 'DIV. HEAD' then '' else 
							(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.sect)) end div,
						
						(SELECT top 1 level FROM DataEvoVal x
							left join sys_level y on x.level = y.nama_lvl $dealer
							where nobukti=a.nobukti and ISNULL(level, '')!=''
							and ISNULL(validasi, '')='' 
							and isnull(deptterkait,'') <> ''
							order by y.urutan asc) as lvl,
								
						(SELECT top 1 deptterkait FROM DataEvoVal x
							left join sys_level y on x.level = y.nama_lvl  $dealer
							where nobukti=a.nobukti and ISNULL(level, '')!=''
							and ISNULL(validasi, '')='' 
							order by y.urutan asc) as deptterkait								
							
						from (
							select nobukti,x.kodedealer, y.idAtasan, userentry as pengaju, y.tipe,
							case
							when y.tipe = 'SECTION HEAD' then userentry
							when y.tipe = 'DEPT. HEAD' then '' 
							when y.tipe = 'DIV. HEAD' then '' else y.IdAtasan  end sect	
							from DataEvo x
							inner join sys_user y on x.userentry = y.IdUser
							where isnull(x.deptterkait,'') <> ''
						) a
						
					) x 
					where ISNULL(lvl, '')!='' and x.kodedealer = '".$user['KodeDealer']."' $s_admin $s_section $s_dept $s_div
					and isnull(deptterkait,'') <> ''
				) x
				";
			
		} else if ($lvl=='REQDEPTTERKAIT') {
			
			$sql = "
				select count(*) as tot from (	
								
					select x.nobukti, x.kodedealer, x.idAtasan, x.pengaju, x.tipe, x.sect, x.dept, x.div, x.lvl, x.deptterkait 
					from (
						
						select a.nobukti, a.kodedealer, a.idAtasan, a.pengaju, a.tipe, a.sect,
								
						case
							when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
							when a.tipe = 'DEPT. HEAD' then a.pengaju
							when a.tipe = 'DIV. HEAD' then '' else (select IdAtasan from sys_user where IdUser=a.idAtasan) end dept,
								
						case 
							when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user 
									where IdUser=(select IdAtasan from sys_user where IdUser=a.pengaju))
							when a.tipe = 'DEPT. HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
							when a.tipe = 'DIV. HEAD' then '' else 
							(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.sect)) end div,
						
						(SELECT top 1 level FROM DataEvoVal x
							left join sys_level y on x.level = y.nama_lvl $dealer
							where nobukti=a.nobukti and ISNULL(level, '')!=''
							and ISNULL(validasi, '')='' 
							and isnull(deptterkait,'') = '".$user['department']."'
							order by y.urutan asc) as lvl,
								
						(SELECT top 1 deptterkait FROM DataEvoVal x
							left join sys_level y on x.level = y.nama_lvl $dealer
							where nobukti=a.nobukti and ISNULL(level, '')!=''
							and ISNULL(validasi, '')='' 
							order by y.urutan asc) as deptterkait
								
						from (
							select nobukti,x.kodedealer, y.idAtasan, userentry as pengaju, y.tipe,
							case 
							when y.tipe = 'SECTION HEAD' then userentry
							when y.tipe = 'DEPT. HEAD' then '' 
							when y.tipe = 'DIV. HEAD' then '' else y.IdAtasan  end sect	
							from DataEvo x
							inner join sys_user y on x.userentry = y.IdUser
							where isnull(x.deptterkait,'') = '".$user['department']."'
						) a
						
					) x 
					inner join sys_user y on x.lvl = y.tipe and x.deptterkait = y.department
					where ISNULL(lvl, '')!='' and x.kodedealer = '".$user['KodeDealer']."'
					and y.iduser = '".$IdUser."' 
										
				) x
				where lvl = 'ADMIN' GROUP BY lvl";
			
		} else {
		
			// or deptterkait = '".$user['department']."' or deptterkait = '".$user['department']."'
			//if ($user['tipe']=='SECTION HEAD' or $user['tipe']=='ADH') { $s_section = "and (sect = '".$IdUser."') "; } else { $s_section = ""; }
			//if (($user['tipe']=='DEPT. HEAD' or $user['tipe']=='KEPALA CABANG') and $lvl!='DEPT. HEAD FINANCE / DIV. HEAD FAST') { $s_dept = "and (dept = '".$IdUser."' ) "; } else { $s_dept = ""; }
			
			$where_deptterkait_val = "";
			if ($user['tipe']=='ADMIN') { $where_deptterkait_val = "and admindeptterkait = '".$IdUser."'"; }
			if ($user['tipe']=='SECTION HEAD') { $where_deptterkait_val = "and sectdeptterkait = '".$IdUser."'"; }
			if ($user['tipe']=='DEPT. HEAD' and $lvl!='DEPT. HEAD FINANCE') { $where_deptterkait_val = "and deptdeptterkait = '".$IdUser."'"; }
			if ($user['tipe']=='DIV. HEAD' and $lvl!='DEPT. HEAD FINANCE / DIV. HEAD FAST') { $where_deptterkait_val = "and deptdeptterkait = '".$IdUser."'"; }
			
			//if ($user['tipe']=='DIV. HEAD') { $where_deptterkait_val = "and deptdeptterkait = '".$IdUser."'"; }
						
			if ($_SESSION['level']=="DIREKSI" or  $_SESSION['level']=="DIREKSI 2") {
				$sql = "
					select count(*) as tot from (
						select * from (
							
							select a.nobukti, a.kodedealer, a.idAtasan, a.pengaju, a.tipe, a.sect,
								
								case 
									when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user with (nolock) where IdUser=a.pengaju)
									when a.tipe = 'DEPT. HEAD' then a.pengaju
									when a.tipe = 'DIV. HEAD' then '' else (select IdAtasan from sys_user  with (nolock) where IdUser=a.idAtasan) end dept,
										
								case 
									when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user with (nolock)
											where IdUser=(select IdAtasan from sys_user with (nolock) where IdUser=a.pengaju))
									when a.tipe = 'DEPT. HEAD' then (select IdAtasan from sys_user with (nolock) where IdUser=a.pengaju)
									when a.tipe = 'DIV. HEAD' then '' else 
									(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user with (nolock) where IdUser=a.sect)) end div,
								
								(SELECT top 1 level FROM DataEvoVal x with (nolock)
								left join sys_level y with (nolock) on x.level = y.nama_lvl $dealer
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								order by y.urutan asc) as lvl,
								
								(SELECT top 1 deptterkait FROM DataEvoVal x with (nolock)
								left join sys_level y with (nolock) on x.level = y.nama_lvl   $dealer
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								order by y.urutan asc) as deptterkait
								
								
								from (
									select nobukti,x.kodedealer, y.idAtasan, userentry as pengaju, y.tipe,
									case 
									when y.tipe = 'SECTION HEAD' then userentry
									when y.tipe = 'DEPT. HEAD' then '' 
									when y.tipe = 'DIV. HEAD' then '' else y.IdAtasan  end sect	
									from DataEvo x with (nolock)
									inner join sys_user y with (nolock) on x.userentry = y.IdUser
								) a
								
						) x where ISNULL(lvl, '')!='' and kodedealer = '".$user['KodeDealer']."' $s_admin $s_section $s_dept $s_div
					) x where lvl = '".$lvl."' GROUP BY lvl
				";
				  //echo "<pre>$sql</pre>";
				  
			} else {
				if ($lvl=="DIREKSI" or  $lvl=="DIREKSI 2" ) {
					$sql = "
						select count(*) as tot from (
							select * from (
							
								select a.nobukti, a.kodedealer, a.idAtasan, a.pengaju, a.tipe, a.sect,
								
								case
									when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user with (nolock)  where IdUser=a.pengaju)
									when a.tipe = 'DEPT. HEAD' then a.pengaju
									when a.tipe = 'DIV. HEAD' then '' else (select IdAtasan from sys_user with (nolock)  where IdUser=a.idAtasan) end dept,
										
								case 
									when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user with (nolock)  
											where IdUser=(select IdAtasan from sys_user with (nolock)  where IdUser=a.pengaju))
									when a.tipe = 'DEPT. HEAD' then (select IdAtasan from sys_user with (nolock)  where IdUser=a.pengaju)
									when a.tipe = 'DIV. HEAD' then '' else 
									(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user with (nolock)  where IdUser=a.sect)) end div,
								
								(SELECT top 1 level FROM DataEvoVal x with (nolock) 
								left join sys_level y  with (nolock) on x.level = y.nama_lvl $dealer
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								order by y.urutan asc) as lvl,
								
								(SELECT top 1 deptterkait FROM DataEvoVal x with (nolock) 
								left join sys_level y  with (nolock) on x.level = y.nama_lvl   $dealer
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								order by y.urutan asc) as deptterkait
								
								from (
									select nobukti,x.kodedealer, y.idAtasan, userentry as pengaju, y.tipe,
									case 
									when y.tipe = 'SECTION HEAD' then userentry
									when y.tipe = 'DEPT. HEAD' then '' 
									when y.tipe = 'DIV. HEAD' then '' else y.IdAtasan  end sect	
									from DataEvo x with (nolock) 
									inner join sys_user y  with (nolock) on x.userentry = y.IdUser
								) a
								
							) x where ISNULL(lvl, '')!='' and kodedealer = '".$user['KodeDealer']."' $s_admin $s_section $s_dept $s_div
						) x where lvl in ('".$lvl."')";
						 // echo "<pre>$sql</pre>";
						 
				} else if ($lvl=="FINANCE") {
				
					$sql = "
						select count(*) as tot from (
							
							select x.nobukti, x.kodedealer, x.idAtasan, x.pengaju, x.tipe, x.sect, x.dept, x.div, x.lvl, x.deptterkait,
							'' admindeptterkait, '' sectdeptterkait, '' deptdeptterkait
							
							from (
							
								select a.nobukti, a.kodedealer, a.idAtasan, a.pengaju, a.tipe, a.sect,
								
								case 
									when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user  with (nolock) where IdUser=a.pengaju)
									when a.tipe = 'DEPT. HEAD' then a.pengaju
									when a.tipe = 'DIV. HEAD' then '' else 
									(select IdAtasan from sys_user  with (nolock) where IdUser=a.idAtasan) end dept,
										
								case 
									when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user  with (nolock) 
											where IdUser=(select IdAtasan from sys_user  with (nolock) where IdUser=a.pengaju))
									when a.tipe = 'DEPT. HEAD' then (select IdAtasan from sys_user  with (nolock) where IdUser=a.pengaju)
									when a.tipe = 'DIV. HEAD' then '' else 
									(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user with (nolock)  where IdUser=a.sect)) end div,
								
								(SELECT top 1 level FROM DataEvoVal x with (nolock) 
								left join sys_level y with (nolock)  on x.level = y.nama_lvl $dealer
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								order by y.urutan asc) as lvl,
								
								(SELECT top 1 deptterkait FROM DataEvoVal x with (nolock) 
								left join sys_level y with (nolock)  on x.level = y.nama_lvl  $dealer
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								order by y.urutan asc) as deptterkait
								
								
								from (
									select nobukti,x.kodedealer, y.idAtasan, userentry as pengaju, y.tipe,
									case
									when y.tipe = 'SECTION HEAD' then userentry
									when y.tipe = 'DEPT. HEAD' then '' 
									when y.tipe = 'DIV. HEAD' then '' else y.IdAtasan end sect	
									from DataEvo x with (nolock) 
									inner join sys_user y with (nolock)  on x.userentry = y.IdUser
								) a
								
							) x where ISNULL(lvl, '')!='' and kodedealer = '".$user['KodeDealer']."' $s_admin $s_section $s_dept $s_div
							and isnull(deptterkait,'') = ''
														
						) x where lvl = '".$lvl."' GROUP BY lvl";	

				
				} else if (($lvl=="DEPT. HEAD FINANCE / DIV. HEAD FAST" or $lvl=="DEPT. HEAD FINANCE") and ($user['department']=='FINANCE' or $user['department']=='all')  and $user['divisi']=="FINANCE and ACCOUNTING") {
					$sql = "
						select count(*) as tot from (
							
							select x.nobukti, x.kodedealer, x.idAtasan, x.pengaju, x.tipe, x.sect, x.dept, x.div, x.lvl, x.deptterkait,
							'' admindeptterkait, '' sectdeptterkait, '' deptdeptterkait
							
							from (
							
								select a.nobukti, a.kodedealer, a.idAtasan, a.pengaju, a.tipe, a.sect,
								
								case 
									when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user  with (nolock) where IdUser=a.pengaju)
									when a.tipe = 'DEPT. HEAD' then a.pengaju
									when a.tipe = 'DIV. HEAD' then '' else (select IdAtasan from sys_user with (nolock) where IdUser=a.idAtasan) end dept,
										
								case 
									when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user  with (nolock) 
											where IdUser=(select IdAtasan from sys_user  with (nolock) where IdUser=a.pengaju))
									when a.tipe = 'DEPT. HEAD' then (select IdAtasan from sys_user with (nolock)  where IdUser=a.pengaju)
									when a.tipe = 'DIV. HEAD' then '' else 
									(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user with (nolock)  where IdUser=a.sect)) end div,
								
								(SELECT top 1 level FROM DataEvoVal x with (nolock) 
								left join sys_level y with (nolock)  on x.level = y.nama_lvl $dealer
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								order by y.urutan asc) as lvl,
								
								(SELECT top 1 deptterkait FROM DataEvoVal x with (nolock) 
								left join sys_level y with (nolock)  on x.level = y.nama_lvl  $dealer
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								order by y.urutan asc) as deptterkait
								
								
								from (
									select nobukti,x.kodedealer, y.idAtasan, userentry as pengaju, y.tipe,
									case 
									when y.tipe = 'SECTION HEAD' then userentry
									when y.tipe = 'DEPT. HEAD' then '' 
									when y.tipe = 'DIV. HEAD' then '' else  x.IdAtasan  end sect	
									from DataEvo x with (nolock) 
									inner join sys_user y  with (nolock) on x.userentry = y.IdUser
								) a
								
							) x where ISNULL(lvl, '')!='' and kodedealer = '".$user['KodeDealer']."' $s_admin $s_section $s_dept $s_div
							and isnull(deptterkait,'') = ''
							
							union
							
							select x.nobukti, x.kodedealer, x.idAtasan, x.pengaju, x.tipe, x.sect, x.dept, x.div, x.lvl, x.deptterkait,
							'' admindeptterkait, '' sectdeptterkait, '' deptdeptterkait
							
							from (
							
								select a.nobukti, a.kodedealer, a.idAtasan, a.pengaju, a.tipe, a.sect,
								
								case 
									when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user with (nolock)  where IdUser=a.pengaju)
									when a.tipe = 'DEPT. HEAD' then a.pengaju
									when a.tipe = 'DIV. HEAD' then '' else (select IdAtasan from sys_user with (nolock)  where IdUser=a.idAtasan) end dept,
										
								case 
									when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user with (nolock)  
											where IdUser=(select IdAtasan from sys_user with (nolock)  where IdUser=a.pengaju))
									when a.tipe = 'DEPT. HEAD' then (select IdAtasan from sys_user with (nolock)  where IdUser=a.pengaju)
									when a.tipe = 'DIV. HEAD' then '' else 
									(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user  with (nolock) where IdUser=a.sect)) end div,
								
								(SELECT top 1 level FROM DataEvoVal x with (nolock) 
								left join sys_level y  with (nolock) on x.level = y.nama_lvl $dealer
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								order by y.urutan asc) as lvl,
								
								(SELECT top 1 deptterkait FROM DataEvoVal x with (nolock) 
								left join sys_level y with (nolock)  on x.level = y.nama_lvl  $dealer
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								order by y.urutan asc) as deptterkait
								
								
								from (
									select nobukti,x.kodedealer, y.idAtasan, userentry as pengaju, y.tipe,
									case 
									when y.tipe = 'SECTION HEAD' then userentry
									when y.tipe = 'DEPT. HEAD' then '' 
									when y.tipe = 'DIV. HEAD' then '' else y.IdAtasan  end sect	
									from DataEvo x with (nolock) 
									inner join sys_user y  with (nolock) on x.userentry = y.IdUser
								) a
								
							) x 
							inner join sys_user y with (nolock)  on x.lvl = y.tipe and x.deptterkait = y.department
							where ISNULL(lvl, '')!='' and x.kodedealer = '".$user['KodeDealer']."'
							and y.iduser = '".$IdUser."' 
							
							union
							
							select x.nobukti, x.kodedealer, x.idAtasan, x.pengaju, x.tipe, x.sect, x.dept, x.div, x.lvl, x.deptterkait,
							isnull(x.admindeptterkait,'') admindeptterkait, isnull(x.sectdeptterkait,'') sectdeptterkait, 
							isnull(x.deptdeptterkait,'') deptdeptterkait
							
							from (
							
								select a.nobukti, a.kodedealer, a.idAtasan, a.pengaju, a.tipe, a.sect,
								
								case  
									when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user with (nolock)  where IdUser=a.pengaju)
									when a.tipe = 'DEPT. HEAD' then a.pengaju
									when a.tipe = 'DIV. HEAD' then '' else (select IdAtasan from sys_user with (nolock)  where IdUser=a.idAtasan) end dept,
										
								case 
									when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user  with (nolock) 
											where IdUser=(select IdAtasan from sys_user with (nolock)  where IdUser=a.pengaju))
									when a.tipe = 'DEPT. HEAD' then (select IdAtasan from sys_user with (nolock)  where IdUser=a.pengaju)
									when a.tipe = 'DIV. HEAD' then '' else 
									(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user with (nolock)  where IdUser=a.sect)) end div,
								
								(SELECT top 1 level FROM DataEvoVal x with (nolock) 
								left join sys_level y  with (nolock) on x.level = y.nama_lvl $dealer
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								order by y.urutan asc) as lvl,
								
								(SELECT top 1 deptterkait FROM DataEvoVal x with (nolock) 
								left join sys_level y  with (nolock) on x.level = y.nama_lvl  $dealer
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								order by y.urutan asc) as deptterkait,
								
								a.admindeptterkait, a.sectdeptterkait, a.deptdeptterkait
								
								from (
									select nobukti,x.kodedealer, y.idAtasan, userentry as pengaju, y.tipe,
									case
									when y.tipe = 'SECTION HEAD' then userentry
									when y.tipe = 'DEPT. HEAD' then '' 
									when y.tipe = 'DIV. HEAD' then '' else y.IdAtasan  end sect,
									(select top 1 uservalidasi from DataEvoVal z  with (nolock) 
									where z.nobukti = x.nobukti
									and isnull(z.deptterkait,'') = '".$user['department']."' and z.level = 'ADMIN') admindeptterkait, 									
									(select top 1  uservalidasi from DataEvoVal z  with (nolock) 
									where z.nobukti = x.nobukti
									and isnull(z.deptterkait,'') = '".$user['department']."' and z.level = 'SECTION HEAD') sectdeptterkait, 																
									(select top 1  uservalidasi from DataEvoVal z  with (nolock) 
									where z.nobukti = x.nobukti
									and isnull(z.deptterkait,'') = '".$user['department']."' and z.level = 'DEPT. HEAD') deptdeptterkait		
									from DataEvo x with (nolock) 
									inner join sys_user y with (nolock)  on x.userentry = y.IdUser
								) a
								
							) x 
							inner join sys_user y  with (nolock) on x.lvl = y.tipe and x.deptterkait = y.department
							where ISNULL(lvl, '')!='' and x.kodedealer = '".$user['KodeDealer']."'
							$where_deptterkait_val
							
								union
							
							select x.nobukti, x.kodedealer, x.idAtasan, x.pengaju, x.tipe, x.sect, x.dept, x.div, x.lvl, x.deptterkait,
							isnull(x.admindeptterkait,'') admindeptterkait, isnull(x.sectdeptterkait,'') sectdeptterkait, 
							isnull(x.deptdeptterkait,'') deptdeptterkait
							
							from (
							
								select a.nobukti, a.kodedealer, a.idAtasan, a.pengaju, a.tipe, a.sect,
								
								case 
									when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user  with (nolock) where IdUser=a.pengaju)
									when a.tipe = 'DEPT. HEAD' then a.pengaju
									when a.tipe = 'DIV. HEAD' then '' else (select IdAtasan from sys_user  with (nolock) where IdUser=a.idAtasan) end dept,
										
								case 
									when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user  with (nolock) 
											where IdUser=(select IdAtasan from sys_user  with (nolock) where IdUser=a.pengaju))
									when a.tipe = 'DEPT. HEAD' then (select IdAtasan from sys_user  with (nolock) where IdUser=a.pengaju)
									when a.tipe = 'DIV. HEAD' then '' else 
									(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user  with (nolock) where IdUser=a.sect)) end div,
								
								(SELECT top 1 level FROM DataEvoVal x with (nolock) 
								left join sys_level y  with (nolock) on x.level = y.nama_lvl $dealer
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								order by y.urutan asc) as lvl,
								
								(SELECT top 1 deptterkait FROM DataEvoVal x with (nolock) 
								left join sys_level y  with (nolock) on x.level = y.nama_lvl  $dealer
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								order by y.urutan asc) as deptterkait,
								
								'' admindeptterkait, '' sectdeptterkait, '' deptdeptterkait
								
								from (
									select nobukti,x.kodedealer, y.idAtasan, userentry as pengaju, y.tipe,
									case
									when y.tipe = 'SECTION HEAD' then userentry
									when y.tipe = 'DEPT. HEAD' then '' 
									when y.tipe = 'DIV. HEAD' then '' else y.IdAtasan  end sect,
									(select top 1 uservalidasi from DataEvoVal z  with (nolock) 
									where z.nobukti = x.nobukti
									and isnull(z.deptterkait,'') = '".$user['department']."' and z.level = 'ADMIN') admindeptterkait, 									
									(select top 1  uservalidasi from DataEvoVal z  with (nolock) 
									where z.nobukti = x.nobukti
									and isnull(z.deptterkait,'') = '".$user['department']."' and z.level = 'SECTION HEAD') sectdeptterkait, 																
									(select top 1  uservalidasi from DataEvoVal z  with (nolock) 
									where z.nobukti = x.nobukti
									and isnull(z.deptterkait,'') = '".$user['department']."' and z.level = 'DEPT. HEAD') deptdeptterkait		
									from DataEvo x with (nolock) 
									inner join sys_user y with (nolock)  on x.userentry = y.IdUser
								) a
								
							) x 
							where ISNULL(lvl, '')!='' and x.kodedealer = '".$user['KodeDealer']."'
							
							$where_deptterkait_val
														
						) x where lvl = '".$lvl."' GROUP BY lvl";	

					//echo "<pre>$sql</pre>";
					
				} else{
					$sql = "
						select count(*) as tot from (
							
							select x.nobukti, x.kodedealer, x.idAtasan, x.pengaju, x.tipe, x.sect, x.dept, x.div, x.lvl, x.deptterkait,
							'' admindeptterkait, '' sectdeptterkait, '' deptdeptterkait
							
							from (
							
								select a.nobukti, a.kodedealer, a.idAtasan, a.pengaju, a.tipe, a.sect,
								
								case 
									when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user  with (nolock) where IdUser=a.pengaju)
									when a.tipe = 'DEPT. HEAD' then a.pengaju
									when a.tipe = 'DIV. HEAD' then '' else (select IdAtasan from sys_user  with (nolock) where IdUser=a.idAtasan) end dept,
										
								case  
									when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user  with (nolock) 
											where IdUser=(select IdAtasan from sys_user  with (nolock) where IdUser=a.pengaju))
									when a.tipe = 'DEPT. HEAD' then (select IdAtasan from sys_user  with (nolock) where IdUser=a.pengaju)
									when a.tipe = 'DIV. HEAD' then '' else 
									(select IdAtasan from sys_user  with (nolock) where IdUser=(select IdAtasan from sys_user  with (nolock) where IdUser=a.sect)) end div,
								
								(SELECT top 1 level FROM DataEvoVal x with (nolock) 
								left join sys_level y  with (nolock) on x.level = y.nama_lvl $dealer
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								order by y.urutan asc) as lvl,
								
								(SELECT top 1 deptterkait FROM DataEvoVal x with (nolock) 
								left join sys_level y  with (nolock) on x.level = y.nama_lvl  $dealer
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								order by y.urutan asc) as deptterkait
								
								
								from (
									select nobukti,x.kodedealer, y.idAtasan, userentry as pengaju, y.tipe,
									case 
									when y.tipe = 'SECTION HEAD' then userentry
									when y.tipe = 'DEPT. HEAD' then '' 
									when y.tipe = 'DIV. HEAD' then '' else y.IdAtasan  end sect	
									from DataEvo x with (nolock) 
									inner join sys_user y  with (nolock) on x.userentry = y.IdUser
								) a
								
							) x where ISNULL(lvl, '')!='' and kodedealer = '".$user['KodeDealer']."' $s_admin $s_section $s_dept $s_div
							and isnull(deptterkait,'') = ''
							
							union
							
							select x.nobukti, x.kodedealer, x.idAtasan, x.pengaju, x.tipe, x.sect, x.dept, x.div, x.lvl, x.deptterkait,
							'' admindeptterkait, '' sectdeptterkait, '' deptdeptterkait
							
							from (
							
								select a.nobukti, a.kodedealer, a.idAtasan, a.pengaju, a.tipe, a.sect,
								
								case
									when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user  with (nolock) where IdUser=a.pengaju)
									when a.tipe = 'DEPT. HEAD' then a.pengaju
									when a.tipe = 'DIV. HEAD' then '' else 
									(select IdAtasan from sys_user  with (nolock) where IdUser=a.idAtasan) end dept,
										
								case 
									when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user  with (nolock) 
											where IdUser=(select IdAtasan from sys_user  with (nolock) where IdUser=a.pengaju))
									when a.tipe = 'DEPT. HEAD' then (select IdAtasan from sys_user  with (nolock) where IdUser=a.pengaju)
									when a.tipe = 'DIV. HEAD' then '' else 
									(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user  with (nolock) where IdUser=a.sect)) end div,
								
								(SELECT top 1 level FROM DataEvoVal x with (nolock) 
								left join sys_level y  with (nolock) on x.level = y.nama_lvl $dealer
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								order by y.urutan asc) as lvl,
								
								(SELECT top 1 deptterkait FROM DataEvoVal x with (nolock) 
								left join sys_level y  with (nolock) on x.level = y.nama_lvl  $dealer
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								order by y.urutan asc) as deptterkait
								
								
								from (
									select nobukti,x.kodedealer, y.idAtasan, userentry as pengaju, y.tipe,
									case 
									when y.tipe = 'SECTION HEAD' then userentry
									when y.tipe = 'DEPT. HEAD' then '' 
									when y.tipe = 'DIV. HEAD' then '' else y.IdAtasan  end sect	
									from DataEvo x with (nolock) 
									inner join sys_user y with (nolock)  on x.userentry = y.IdUser
								) a
								
							) x 
							inner join sys_user y  with (nolock) on x.lvl = y.tipe and x.deptterkait = y.department
							where ISNULL(lvl, '')!='' and x.kodedealer = '".$user['KodeDealer']."'
							and y.iduser = '".$IdUser."' 
							
								union
							
							select x.nobukti, x.kodedealer, x.idAtasan, x.pengaju, x.tipe, x.sect, x.dept, x.div, x.lvl, x.deptterkait,
							isnull(x.admindeptterkait,'') admindeptterkait, isnull(x.sectdeptterkait,'') sectdeptterkait, 
							isnull(x.deptdeptterkait,'') deptdeptterkait
							
							from (
							
								select a.nobukti, a.kodedealer, a.idAtasan, a.pengaju, a.tipe, a.sect,
								
								case 
									when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user  with (nolock) where IdUser=a.pengaju)
									when a.tipe = 'DEPT. HEAD' then a.pengaju
									when a.tipe = 'DIV. HEAD' then '' else (select IdAtasan from sys_user  with (nolock) where IdUser=a.idAtasan) end dept,
										
								case 
									when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user  with (nolock) 
											where IdUser=(select IdAtasan from sys_user with (nolock)  where IdUser=a.pengaju))
									when a.tipe = 'DEPT. HEAD' then (select IdAtasan from sys_user  with (nolock) where IdUser=a.pengaju)
									when a.tipe = 'DIV. HEAD' then '' else 
									(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user  with (nolock) where IdUser=a.sect)) end div,
								
								(SELECT top 1 level FROM DataEvoVal x with (nolock) 
								left join sys_level y with (nolock) on x.level = y.nama_lvl $dealer
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								order by y.urutan asc) as lvl,
								
								(SELECT top 1 deptterkait FROM DataEvoVal x with (nolock) 
								left join sys_level y  with (nolock) on x.level = y.nama_lvl  $dealer
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								order by y.urutan asc) as deptterkait,
								
								a.admindeptterkait, a.sectdeptterkait, a.deptdeptterkait
								
								from (
									select nobukti,x.kodedealer, y.idAtasan, userentry as pengaju, y.tipe,
									case 
									when y.tipe = 'SECTION HEAD' then userentry
									when y.tipe = 'DEPT. HEAD' then '' 
									when y.tipe = 'DIV. HEAD' then '' else y.IdAtasan  end sect,
									(select top 1 uservalidasi from DataEvoVal z  with (nolock) 
									where z.nobukti = x.nobukti
									and isnull(z.deptterkait,'') = '".$user['department']."' and z.level = 'ADMIN') admindeptterkait, 									
									(select top 1  uservalidasi from DataEvoVal z  with (nolock) 
									where z.nobukti = x.nobukti
									and isnull(z.deptterkait,'') = '".$user['department']."' and z.level = 'SECTION HEAD') sectdeptterkait, 																
									(select top 1  uservalidasi from DataEvoVal z  with (nolock) 
									where z.nobukti = x.nobukti
									and isnull(z.deptterkait,'') = '".$user['department']."' and z.level = 'DEPT. HEAD') deptdeptterkait		
									from DataEvo x with (nolock) 
									inner join sys_user y with (nolock)  on x.userentry = y.IdUser
								) a
								
							) x 
							inner join sys_user y  with (nolock) on x.lvl = y.tipe and x.deptterkait = y.department
							where ISNULL(lvl, '')!='' and x.kodedealer = '".$user['KodeDealer']."'
							
							$where_deptterkait_val
														
						) x where lvl = '".$lvl."' GROUP BY lvl";	

				}
			}
			
		}
		
		//echo "<pre>$sql</pre>";
		// return $sql;
		$rsl = mssql_query($sql);
		$dt = mssql_fetch_array($rsl);
		return isset($dt['tot']) ? $dt['tot'] : 0;
	}

	function prodash($lvl,$divisi,$IdUser){
		require_once ('../inc/conn.php');
		$user = mssql_fetch_array(mssql_query("select * from sys_user where IdUser = '".$IdUser."'"));
		if ($user['tipe']=='ADMIN') { $s_admin = "and pengaju = '".$IdUser."'"; } else { $s_admin = ""; }
		if ($user['tipe']=='ACCOUNTING') { $s_accounting = "and pengaju = '".$IdUser."'"; } else { $s_accounting = ""; }
		//if ($user['tipe']=='SECTION HEAD' or $user['tipe']=='ADH') { $s_section = "and sect = '".$IdUser."'"; } else { $s_section = ""; }
		//if ($user['tipe']=='DEPT. HEAD' or $user['tipe']=='KEPALA CABANG') { $s_dept = "and dept = '".$IdUser."'"; } else { $s_dept = ""; }
		//if ($user['tipe']=='DIV. HEAD') { $s_div = "and div = '".$IdUser."'"; } else { $s_div = ""; }
		
		if ($user['tipe']=='DIV. HEAD') { $s_div = "and (div = '".$IdUser."' or pengaju = '".$IdUser."') "; } else { $s_div = ""; }
		
		if ($_SESSION['level']=="DIREKSI" or  $_SESSION['level']=="DIREKSI 2") {
			if ($lvl=='all') { $s_lvl = ""; } else { $s_lvl = "and lvl = '".$lvl."'"; }
		} else {
			if ($lvl=='all') { $s_lvl = ""; } else { $s_lvl = "and lvl = '".$lvl."'"; }
		}
		
		if ($divisi=='all') { $s_divisi = ""; } else { $s_divisi = "and divisi = '".$divisi."'"; }

		$where = "";
		/*if ($lvl=='DEPTTERKAIT') {
			$sql = "
				select count(*) as tot from (
					select * from(select nobukti,kodedealer,divisi,userentry as pengaju,IdAtasan as sect,
					(select IdAtasan from sys_user where IdUser=a.IdAtasan) as dept,
					(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.IdAtasan)) as div,
					(SELECT top 1 level FROM DataEvoVal where nobukti=a.nobukti and ISNULL(level, '')!='' and ISNULL(validasi, '')='' order by idVal desc) as lvl
					from DataEvo a) x where ISNULL(lvl, '')!='' and kodedealer = '".$user['KodeDealer']."' $s_divisi $s_admin $s_section $s_dept $s_div
					and isnull(deptterkait,'') <> ''
				) x where 1=1 -- GROUP BY lvl
			";

		} else {
			if ($lvl=="KASIR") {
				$where .= " and isnull(a.tglbayar,'') = '' ";
			} else if ($lvl=="all") {
				$where .= " and isnull(a.tglbayar,'') != '' ";
			}
			
			$sql = "
				select count(*) as tot from (
					select * from (
						select nobukti,kodedealer,divisi,userentry as pengaju,IdAtasan as sect,
						(select IdAtasan from sys_user where IdUser=a.IdAtasan) as dept,
						(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.IdAtasan)) as div,
						(SELECT top 1 level FROM DataEvoVal where nobukti=a.nobukti and ISNULL(level, '')!='' and ISNULL(validasi, '')='' order by idVal desc) as lvl
						from DataEvo a
						where a.nobukti is not null $where) x 
					where ISNULL(lvl, '')!='' and kodedealer = '".$user['KodeDealer']."' $s_divisi $s_admin $s_section $s_dept $s_div
				) x where 1=1 $s_lvl -- GROUP BY lvl
			";
		}*/
		
		if ($user['KodeDealer']=='2010') {
			$dealer = " and y.is_dealer = '0' ";
		} else {
			$dealer = " and y.is_dealer = '1' ";
		}
		
		if ($lvl=='DEPTTERKAIT') {
					
			$sql = "
				select count(*) as tot from (
					select * from(
					
						select a.nobukti, a.kodedealer, a.idAtasan, a.pengaju, a.tipe, a.sect,
								
						case when a.tipe = 'ADMIN' then (select IdAtasan from sys_user where IdUser=a.idAtasan)
							when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
							when a.tipe = 'DEPT. HEAD' then a.pengaju
							when a.tipe = 'DIV. HEAD' then '' else a.pengaju end dept,
								
						case when a.tipe = 'ADMIN' then (select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.sect))
							when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user 
									where IdUser=(select IdAtasan from sys_user where IdUser=a.pengaju))
							when a.tipe = 'DEPT. HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
							when a.tipe = 'DIV. HEAD' then '' else a.pengaju end div,
						
						(SELECT top 1 level FROM DataEvoVal x
								left join sys_level y on x.level = y.nama_lvl $dealer
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								and isnull(deptterkait,'')  <> ''
								order by y.urutan asc) as lvl,
								
								(SELECT top 1 deptterkait FROM DataEvoVal x
								left join sys_level y on x.level = y.nama_lvl  and y.is_dealer = '0' 
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								order by y.urutan asc) as deptterkait
								
								
						from (
							select nobukti,x.kodedealer, y.idAtasan, userentry as pengaju, y.tipe,
							case when y.tipe = 'ADMIN' then x.IdAtasan 
							when y.tipe = 'SECTION HEAD' then userentry
							when y.tipe = 'DEPT. HEAD' then '' 
							when y.tipe = 'DIV. HEAD' then '' else userentry end sect	
							from DataEvo x
							inner join sys_user y on x.userentry = y.IdUser
						) a
						
					) x where ISNULL(lvl, '')!='' and kodedealer = '".$user['KodeDealer']."' and (pengaju = '".$IdUser."' or lvl = 'ADMIN')
					and isnull(deptterkait,'') <> ''
				) x where 1=1 -- GROUP BY lvl
			";

		} else {
			if ($user['tipe']=='SECTION HEAD' or $user['tipe']=='ADH') { $s_section = "and (sect = '".$IdUser."' or pengaju = '".$IdUser."' or deptterkait = '".$user['department']."') "; } else { $s_section = ""; }
			if ($user['tipe']=='DEPT. HEAD' or $user['tipe']=='KEPALA CABANG') { $s_dept = "and (dept = '".$IdUser."' or pengaju = '".$IdUser."' or deptterkait = '".$user['department']."') "; } else { $s_dept = ""; }
		
			if ($lvl=="KASIR") {
				$where .= " and isnull(x.tglbayar,'') = '' ";
			} else if ($lvl=="all") {
				$where .= " and isnull(x.tglbayar,'') != '' ";
			}
			
			$sql = "
				select count(*) as tot from (
					select * from (
					
						select a.nobukti, a.kodedealer, a.idAtasan, a.pengaju, a.tipe, a.sect,
									
						case when a.tipe = 'ADMIN' then (select IdAtasan from sys_user where IdUser=a.idAtasan)
							when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
							when a.tipe = 'DEPT. HEAD' then a.pengaju
							when a.tipe = 'DIV. HEAD' then '' else a.pengaju end dept,
								
						case when a.tipe = 'ADMIN' then (select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.sect))
							when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user 
									where IdUser=(select IdAtasan from sys_user where IdUser=a.pengaju))
							when a.tipe = 'DEPT. HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
							when a.tipe = 'DIV. HEAD' then '' else a.pengaju end div,
						
						(SELECT top 1 level FROM DataEvoVal x
								left join sys_level y on x.level = y.nama_lvl $dealer
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								order by y.urutan asc) as lvl,
								
								(SELECT top 1 deptterkait FROM DataEvoVal x
								left join sys_level y on x.level = y.nama_lvl  and y.is_dealer = '0' 
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								order by y.urutan asc) as deptterkait
								
								
						from (
							select nobukti,x.kodedealer, y.idAtasan, userentry as pengaju, y.tipe,
							case when y.tipe = 'ADMIN' then x.IdAtasan 
							when y.tipe = 'SECTION HEAD' then userentry
							when y.tipe = 'DEPT. HEAD' then '' 
							when y.tipe = 'DIV. HEAD' then '' else userentry end sect	
							from DataEvo x
							inner join sys_user y on x.userentry = y.IdUser
							where x.nobukti is not null $where
						) a
					
					) x where ISNULL(lvl, '')!='' and kodedealer = '".$user['KodeDealer']."' $s_divisi $s_admin $s_section $s_dept $s_div
				) x where 1=1 $s_lvl -- GROUP BY lvl
			";
		}
		
		
		
		//echo "<pre>".$lvl."__".$sql."</pre>";
		$rsl = mssql_query($sql);
		$dt = mssql_fetch_array($rsl);
		return isset($dt['tot']) ? $dt['tot'] : 0;
		// return $sql;
	}

	function profund($int,$IdUser){
		require_once ('../inc/conn.php');
		$user = mssql_fetch_array(mssql_query("select * from sys_user where IdUser = '".$IdUser."'"));
		if ($int == 0) {
			$date = " and tgl_bayar = GETDATE()";
		} else if ($int == 1) {
			$date = " and tgl_bayar = DATEADD(DAY, 1, GETDATE())";
		} else if ($int == 2) {
			$date = " and tgl_bayar >= DATEADD(DAY, 1, GETDATE())";
		}
		if ($user['tipe']=='ADMIN') { $s_admin = "and pengaju = '".$IdUser."'"; } else { $s_admin = ""; }
		if ($user['tipe']=='SECTION HEAD' or $user['tipe']=='ADH') { $s_section = "and (sect = '".$IdUser."' or pengaju = '".$IdUser."') "; } else { $s_section = ""; }
		if ($user['tipe']=='DEPT. HEAD' or $user['tipe']=='KEPALA CABANG') { $s_dept = "and (dept = '".$IdUser."'  or pengaju = '".$IdUser."') "; } else { $s_dept = ""; }
		if ($user['tipe']=='DIV. HEAD') { $s_div = "and (div = '".$IdUser."'  or pengaju = '".$IdUser."') "; } else { $s_div = ""; }
		
		/*$sql = "
			select sum(amount) as tot from (
				select * from(select nobukti,kodedealer,divisi,userentry as pengaju,IdAtasan as sect,
				(select IdAtasan from sys_user where IdUser=a.IdAtasan) as dept,
				(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.IdAtasan)) as div,
				(SELECT top 1 level FROM DataEvoVal where nobukti=a.nobukti and ISNULL(level, '')!='' and ISNULL(validasi, '')='' order by idVal desc) as lvl,
				case when tipe='HUTANG' then htg_stl_pajak else biaya_yg_dibyar end as amount,tgl_bayar
				from DataEvo a) x where ISNULL(lvl, '')!='' and kodedealer='".$user['KodeDealer']."' $s_admin $s_section $s_dept $s_div $date
			) x
		";*/
		if ($user['KodeDealer']=='2010') {
			$dealer = " and y.is_dealer = '0' ";
		} else {
			$dealer = " and y.is_dealer = '1' ";
		}
		
		$sql = "
			select sum(amount) as tot from (
				select * from (
					
					select a.nobukti, a.kodedealer, a.idAtasan, a.pengaju, a.tipe, a.sect,
								
					case when a.tipe = 'ADMIN' then (select IdAtasan from sys_user where IdUser=a.idAtasan)
						when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
						when a.tipe = 'DEPT. HEAD' then a.pengaju
						when a.tipe = 'DIV. HEAD' then '' else a.pengaju end dept,
							
					case when a.tipe = 'ADMIN' then (select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.sect))
						when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user 
								where IdUser=(select IdAtasan from sys_user where IdUser=a.pengaju))
						when a.tipe = 'DEPT. HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
						when a.tipe = 'DIV. HEAD' then '' else a.pengaju end div,
					
					(SELECT top 1 level FROM DataEvoVal x
								left join sys_level y on x.level = y.nama_lvl $dealer
								where nobukti=a.nobukti and ISNULL(level, '')!=''
								and ISNULL(validasi, '')='' 
								order by y.urutan asc) as lvl, 
								
								a.amount, a.tgl_bayar
					from (
						select nobukti,x.kodedealer, y.idAtasan, userentry as pengaju, y.tipe,
						case when y.tipe = 'ADMIN' then x.IdAtasan 
						when y.tipe = 'SECTION HEAD' then userentry
						when y.tipe = 'DEPT. HEAD' then '' 
						when y.tipe = 'DIV. HEAD' then '' else userentry end sect,
						 case when x.tipe='HUTANG' then htg_stl_pajak else biaya_yg_dibyar end as amount, tgl_bayar
						from DataEvo x
						inner join sys_user y on x.userentry = y.IdUser
					) a
					  
				) x where ISNULL(lvl, '')!='' and kodedealer='".$user['KodeDealer']."' $s_admin $s_section $s_dept $s_div $date
			) x
		";
		/*
		select a.nobukti, a.kodedealer, a.idAtasan, a.pengaju, a.sect,
				(select IdAtasan from sys_user where IdUser=a.sect) as dept,
				(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.sect)) as div,
				(SELECT top 1 level FROM DataEvoVal where nobukti=a.nobukti and ISNULL(level, '')!='' and ISNULL(validasi, '')='' 
				order by idVal desc) as lvl
			from (
				select nobukti,kodedealer,idAtasan, userentry as pengaju, 
				case when userentry = IdAtasan then idAtasan else userentry end	
				 as sect,
				 case when tipe='HUTANG' then htg_stl_pajak else biaya_yg_dibyar end as amount,tgl_bayar
				from DataEvo
			) a    
		) x where ISNULL(lvl, '')!=''*/
					
		//echo "<pre>$sql</pre>";			
		$rsl = mssql_query($sql);
		$count = mssql_fetch_array($rsl);
		return number_format($count['tot'],0,",",".");
	}
?>