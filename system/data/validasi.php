<?php
	error_reporting(0);
	$page = isset($_POST['page']) ? $_POST['page'] : 1;
	$rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
	$sortname = isset($_POST['sortname']) ? $_POST['sortname'] : 'tgl_pengajuan';
	$sortorder = isset($_POST['sortorder']) ? $_POST['sortorder'] : 'desc';
	$query = isset($_POST['query']) ? $_POST['query'] : false;

	require_once ('../inc/conn.php');

	$qtype = isset($_POST['qtype']) ? $_POST['qtype'] : false;
	$page = $_POST['page'];
	$rp = $_POST['rp'];
	$sortname = $_POST['sortname'];
	$sortorder = $_POST['sortorder'];
	if (!$sortname) $sortname = 'tgl_pengajuan';
	if (!$sortorder) $sortorder = 'desc';
	$IdUser = isset($_REQUEST['IdUser']) ? $_REQUEST['IdUser'] : null;
	// $NoBuktiPengajuan = isset($_REQUEST['NoBuktiPengajuan']) ? $_REQUEST['NoBuktiPengajuan'] : null;
	// $namaVendor = isset($_REQUEST['namaVendor']) ? $_REQUEST['namaVendor'] : null;
	// $FP = isset($_REQUEST['FP']) ? $_REQUEST['FP'] : null;
	// $Status = isset($_REQUEST['Status']) ? $_REQUEST['Status'] : null;
	// $startDate = isset($_REQUEST['startDate']) ? $_REQUEST['startDate'] : null;
	// $endDate = isset($_REQUEST['endDate']) ? $_REQUEST['endDate'] : null;
	$sort = "ORDER BY $sortname $sortorder";

	
	
	if (!$page) $page = 1;
	if (!$rp) $rp = 10;

	$start = (($page-1) * $rp);
	
	/*
	$user = mssql_fetch_array(mssql_query("select * from sys_user where IdUser = '".$IdUser."'"));
	if ($user['tipe']=='ADMIN') { 
		$level = "and ISNULL(lvl, '')='".$user['tipe']."'"; $s_validator = "and pengaju = '".$IdUser."'"; 
	} else if ($user['tipe']=='SECTION HEAD' or $user['tipe']=='ADH') { 
		$level = "and ISNULL(lvl, '')='".$user['tipe']."'"; $s_validator = "and sect = '".$IdUser."'"; 
	} else if ($user['tipe']=='DEPT. HEAD' or $user['tipe']=='KEPALA CABANG') { 
		if ($user['divisi']=='FINANCE and ACCOUNTING' and $user['department']=='FINANCE') {
			$level = "and ISNULL(lvl, '') in ('DEPT. HEAD FINANCE / DIV. HEAD FAST','DEPT. HEAD')"; 
			$s_validator = "and dept = '".$IdUser."'"; 
			// $s_validator = "";
		} else {
			$level = "and ISNULL(lvl, '')='".$user['tipe']."'"; $s_validator = "and dept = '".$IdUser."'"; 
		}
	} else if ($user['tipe']=='DIV. HEAD') { 
		if ($user['divisi']=='FINANCE and ACCOUNTING' and ($user['department']=='FINANCE' or $user['department']=='all')) {
			$level = "and ISNULL(lvl, '') in ('DEPT. HEAD FINANCE / DIV. HEAD FAST','DIV. HEAD')"; 
			$s_validator = "and div = '".$IdUser."'"; 
			// $s_validator = "";
		} else {
			$level = "and ISNULL(lvl, '')='".$user['tipe']."'"; $s_validator = "and div = '".$IdUser."'"; 
		}
	} else { 
		$level = "and ISNULL(lvl, '')='".$user['tipe']."'"; $s_validator = ""; 
	}
	*/
	
	$user = mssql_fetch_array(mssql_query("select * from sys_user where IdUser = '".$IdUser."'"));
		
	if ($user['tipe']=='ADMIN') { $s_admin = "and pengaju = '".$IdUser."'"; } else { $s_admin = ""; }
	if ($user['tipe']=='SECTION HEAD' or $user['tipe']=='ADH') { $s_section = "and sect = '".$IdUser."'"; } else { $s_section = ""; }
	if ($user['tipe']=='DEPT. HEAD' or $user['tipe']=='KEPALA CABANG') { $s_dept = "and dept = '".$IdUser."'"; } else { $s_dept = ""; }
	if ($user['tipe']=='DIV. HEAD') { $s_div = "and (div = '".$IdUser."' or div = 'all')";  } else { $s_div = ""; }
			
	if ($user['tipe']=='DIREKSI 2') { 
		//$level = "DIREKSI";
		if (ltrim(rtrim(strtoupper($level))) == "DIREKSI") {
			$level = "DIREKSI 2";
		}
	}
	
	$level = $user['tipe'];
	
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
	
		$where_deptterkait_val = "";
		if ($user['tipe']=='ADMIN') { $where_deptterkait_val = "and admindeptterkait = '".$IdUser."'"; }
		if ($user['tipe']=='SECTION HEAD') { $where_deptterkait_val = "and sectdeptterkait = '".$IdUser."'"; }
		if ($user['tipe']=='DEPT. HEAD') { $where_deptterkait_val = "and deptdeptterkait = '".$IdUser."'"; }
			
		$table_sys = "";	
		$where_deptlain = "   ";			
		$where_deptlain2 = "   ";
		$where = " $s_admin  $s_section $s_dept $s_div";
		
		if ($_SESSION['level']=="DIREKSI" or  $_SESSION['level']=="DIREKSI 2") {
			$where_lvl = " and lvl = '".$level."' ";
		} else {
			if ($level=="DIREKSI" or  $level=="DIREKSI 2" ) {
				$where_lvl = " and lvl in ('DIREKSI','DIREKSI 2')  ";
			} else {
				$where_lvl = " and lvl = '".$level."' ";
			}
		}
	}
	
	if ($user['KodeDealer']=='2010') {
		$dealer = " and y.is_dealer = '0' ";
	} else {
		$dealer = " and y.is_dealer = '1' ";
	}

	$month = array('01' => 'Januari','02' => 'Februari','03' => 'Maret','04' => 'April','05' => 'Mei','06' => 'Juni','07' => 'Juli','08' => 'Agustus','09' => 'September','10' => 'Oktober','11' => 'Nopember','12' => 'Desember');
	$month2 = array('01' => 'Jan','02' => 'Feb','03' => 'Mar','04' => 'Apr','05' => 'Mei','06' => 'Jun','07' => 'Jul','08' => 'Ags','09' => 'Sep','10' => 'Okt','11' => 'Nov','12' => 'Dsm');
	
	header("Content-type: text/xml");
	$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
	$xml .= "<rows>";
	$xml .= "<page>$page</page>";
			
	if ($level=="DIREKSI" or  $level=="DIREKSI 2" ) {
		
		$sql = "
			select evo_id,a.nobukti,tgl_pengajuan,namaUser as nama_aju,NamaDealer,namaVendor,b.department,
			case when a.tipe='HUTANG' then htg_stl_pajak else biaya_yg_dibyar end as amount,keterangan ,
			tgl_bayar, realisasi_nominal, userentry, x.kodedealer, status
			from (
			
				select x.nobukti, x.kodedealer, x.idAtasan, x.pengaju, x.tipe, x.sect, x.dept, x.div, x.lvl, x.deptterkait,
				'' admindeptterkait, '' sectdeptterkait, '' deptdeptterkait
				
				from (
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
						left join sys_level y on x.level = y.nama_lvl $dealer 
						where nobukti=a.nobukti and ISNULL(level, '')!=''
						and ISNULL(validasi, '')='' 
						order by y.urutan asc) as deptterkait
						
					from (
						select nobukti,x.kodedealer, x.idAtasan, userentry as pengaju, y.tipe,
						case when y.tipe = 'ADMIN' then x.IdAtasan 
						when y.tipe = 'SECTION HEAD' then userentry
						when y.tipe = 'DEPT. HEAD' then '' 
						when y.tipe = 'DIV. HEAD' then '' else userentry end sect	
						from DataEvo x
						inner join sys_user y on x.userentry = y.IdUser
					) a
					
				) x where ISNULL(lvl, '')!='' and kodedealer = '".$user['KodeDealer']."' $where
										
			) x 
			inner join DataEvo a on x.nobukti=a.nobukti
			inner join sys_user b on a.userentry=b.IdUser
			inner join SPK00..dodealer c on a.kodedealer=c.kodedealer
			where a.nobukti is not null  and a.kodedealer = '".$user['KodeDealer']."'
			$where_lvl 
			
		";
			
	} else if ($level=="FINANCE") {
	
		$sql = "
			select evo_id,a.nobukti,tgl_pengajuan,namaUser as nama_aju,NamaDealer,namaVendor,b.department,
			case when a.tipe='HUTANG' then htg_stl_pajak else biaya_yg_dibyar end as amount,keterangan ,
			tgl_bayar, realisasi_nominal, userentry, x.kodedealer, status
			from (
			
				select x.nobukti, x.kodedealer, x.idAtasan, x.pengaju, x.tipe, x.sect, x.dept, x.div, x.lvl, x.deptterkait,
				'' admindeptterkait, '' sectdeptterkait, '' deptdeptterkait
				
				from (
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
						$where_deptlain2
						order by y.urutan asc) as lvl,										
					(SELECT top 1 deptterkait FROM DataEvoVal x
						left join sys_level y on x.level = y.nama_lvl $dealer
						where nobukti=a.nobukti and ISNULL(level, '')!=''
						and ISNULL(validasi, '')='' 
						order by y.urutan asc) as deptterkait
						
					from (
						select nobukti,x.kodedealer, x.idAtasan, userentry as pengaju, y.tipe,
						case when y.tipe = 'ADMIN' then x.IdAtasan 
						when y.tipe = 'SECTION HEAD' then userentry
						when y.tipe = 'DEPT. HEAD' then '' 
						when y.tipe = 'DIV. HEAD' then '' else userentry end sect	
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
			where a.nobukti is not null and a.kodedealer = '".$user['KodeDealer']."'
			$where_lvl ";
		
	
	} else {
		
		$sql = "
			select evo_id,a.nobukti,tgl_pengajuan,namaUser as nama_aju,NamaDealer,namaVendor,b.department,
			case when a.tipe='HUTANG' then htg_stl_pajak else biaya_yg_dibyar end as amount,keterangan,
			tgl_bayar, realisasi_nominal, userentry, x.kodedealer, status
			from (
			
				select x.nobukti, x.kodedealer, x.idAtasan, x.pengaju, x.tipe, x.sect, x.dept, x.div, x.lvl, x.deptterkait,
				'' admindeptterkait, '' sectdeptterkait, '' deptdeptterkait
				
				from (
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
						$where_deptlain2
						order by y.urutan asc) as lvl,										
					(SELECT top 1 deptterkait FROM DataEvoVal x
						left join sys_level y on x.level = y.nama_lvl $dealer
						where nobukti=a.nobukti and ISNULL(level, '')!=''
						and ISNULL(validasi, '')='' 
						order by y.urutan asc) as deptterkait
						
					from (
						select nobukti,x.kodedealer, x.idAtasan, userentry as pengaju, y.tipe,
						case when y.tipe = 'ADMIN' then x.IdAtasan 
						when y.tipe = 'SECTION HEAD' then userentry
						when y.tipe = 'DEPT. HEAD' then '' 
						when y.tipe = 'DIV. HEAD' then '' else userentry end sect	
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
						$where_deptlain2
						order by y.urutan asc) as lvl,										
					(SELECT top 1 deptterkait FROM DataEvoVal x
						left join sys_level y on x.level = y.nama_lvl $dealer
						where nobukti=a.nobukti and ISNULL(level, '')!=''
						and ISNULL(validasi, '')='' 
						order by y.urutan asc) as deptterkait
						
					from (
						select nobukti,x.kodedealer, x.idAtasan, userentry as pengaju, y.tipe,
						case when y.tipe = 'ADMIN' then x.IdAtasan 
						when y.tipe = 'SECTION HEAD' then userentry
						when y.tipe = 'DEPT. HEAD' then '' 
						when y.tipe = 'DIV. HEAD' then '' else userentry end sect	
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
				x.admindeptterkait, x.sectdeptterkait, x.deptdeptterkait
				
				from (
				
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
					left join sys_level y on x.level = y.nama_lvl $dealer
					where nobukti=a.nobukti and ISNULL(level, '')!=''
					and ISNULL(validasi, '')='' 
					order by y.urutan asc) as deptterkait,
					
					a.admindeptterkait, a.sectdeptterkait, a.deptdeptterkait
					
					from (
						select nobukti,x.kodedealer, x.idAtasan, userentry as pengaju, y.tipe,
						case when y.tipe = 'ADMIN' then x.IdAtasan 
						when y.tipe = 'SECTION HEAD' then userentry
						when y.tipe = 'DEPT. HEAD' then '' 
						when y.tipe = 'DIV. HEAD' then '' else userentry end sect,
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
			where a.nobukti is not null and a.kodedealer = '".$user['KodeDealer']."'
			$where_lvl ";
		
			}
						
	//echo "<pre>$sql</pre>";	
		
	/*$sql2 = "
		select top $rp evo_id,'' as overbudget,(select top 1 case when ISNULL(validasi, '')='' then 'PROSES ' + level else validasi+' '+level end as stataju
		from DataEvoVal where nobukti=a.nobukti order by tglentry desc) as stataju,
		tgl_pengajuan,tgl_bayar,a.nobukti,'' as notagihan,namavendor,realisasi_nominal as totNom,
		keterangan,userentry as pembuat, pos_biaya as noAkun, SUBSTRING(ketAkun, 12, LEN(ketAkun)) as namaAkun, nominal, 
		'' as rapbBln,'' as realBln,'' as rapbOg,'' as realOg,'' as rapbThun,'' as realThun,a.kodedealer,status 
		from DataEvo a
		LEFT join DataEvoPos b on a.nobukti=b.nobukti
		where a.nobukti in (
			select nobukti from(select nobukti,kodedealer,userentry as pengaju,IdAtasan as sect,
			(select IdAtasan from sys_user where IdUser=a.IdAtasan) as dept,
			(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.IdAtasan)) as div,
			(SELECT top 1 level FROM DataEvoVal where nobukti=a.nobukti and ISNULL(level, '')!='' and ISNULL(validasi, '')='' order by idVal desc) as lvl
			from DataEvo a) x where ISNULL(lvl, '')!='' and kodedealer = '".$user['KodeDealer']."' $level $s_validator
		) and evo_id not in (
			select top $start evo_id from DataEvo a LEFT join DataEvoPos b on a.nobukti=b.nobukti
			where a.nobukti in (
				select nobukti from(select nobukti,kodedealer,userentry as pengaju,IdAtasan as sect,
				(select IdAtasan from sys_user where IdUser=a.IdAtasan) as dept,
				(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.IdAtasan)) as div,
				(SELECT top 1 level FROM DataEvoVal where nobukti=a.nobukti and ISNULL(level, '')!='' and ISNULL(validasi, '')='' order by idVal desc) as lvl
				from DataEvo a) x where ISNULL(lvl, '')!='' and kodedealer = '".$user['KodeDealer']."' $level $s_validator
			) $sort
		) $sort
	";*/
	
	$sql2 = "
		select top $rp evo_id,'' as overbudget,(select top 1 case when ISNULL(validasi, '')='' then 'PROSES ' + level else validasi+' '+level end as stataju
		from DataEvoVal where nobukti=xx.nobukti order by tglentry desc) as stataju,
		tgl_pengajuan,tgl_bayar,xx.nobukti,'' as notagihan,namavendor,realisasi_nominal as totNom,
		keterangan,userentry as pembuat,
		-- pos_biaya as noAkun, SUBSTRING(ketAkun, 12, LEN(ketAkun)) as namaAkun, nominal, 
		'' noAkun, '' namaAkun, '' nominal,
		'' as rapbBln,'' as realBln,'' as rapbOg,'' as realOg,'' as rapbThun,'' as realThun,xx.kodedealer,status 
		from (
			$sql
		) XX 
		where evo_id not in (
			select top $start evo_id from ($sql) X
		)
			$sort";
		

	//echo "<pre>$sql2</pre>";	
	$rsl2 = mssql_query($sql2,$conns);
	$rows = array();
	while ($row = mssql_fetch_array($rsl2)) {
		$rows[] = $row;
	}
	
	/*$totalRow = mssql_num_rows(mssql_query("
		select evo_id from DataEvo a
		where a.nobukti in (
			select nobukti from(select nobukti,kodedealer,userentry as pengaju,IdAtasan as sect,
			(select IdAtasan from sys_user where IdUser=a.IdAtasan) as dept,
			(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.IdAtasan)) as div,
			(SELECT top 1 level FROM DataEvoVal where nobukti=a.nobukti and ISNULL(level, '')!='' and ISNULL(validasi, '')='' order by idVal desc) as lvl
			from DataEvo a) x where ISNULL(lvl, '')!='' and kodedealer = '".$user['KodeDealer']."' $level $s_validator
		)
	",$conns));*/
	$totalRow = mssql_num_rows(mssql_query("
		select evo_id from ($sql) XX
	",$conns));
	$no=1;
	$cek = 0;
	$data = "";
	foreach($rows as $dt) {
		$sTagihan = "select NoFaktur,TglTrnFaktur 
						from CreditNote..DataEvoTagihan 
						where nobukti = '".$dt['nobukti']."'";
		$rTagihan = mssql_query($sTagihan,$conns);
		$notagihan = ""; $tglfaktur = "";
		while ($dTagihan = mssql_fetch_array($rTagihan)) {
			$notagihan .= $dTagihan['NoFaktur'].",";
			$tglfaktur .= datenull($dTagihan['TglTrnFaktur']).",";
		}
		$notagihan = substr($notagihan, 0,strlen($notagihan)-1);
		$tglfaktur = substr($tglfaktur, 0,strlen($tglfaktur)-1);
		
		$pos_biaya = ""; $ketakun = "";  $nominal = "";
		$sPos = "select pos_biaya, nominal, SUBSTRING(ketAkun, 12, LEN(ketAkun)) ketakun
				from CreditNote..DataEvoPos
				where nobukti = '".$dt['nobukti']."'";
		$rPos = mssql_query($sPos,$conns);
		while ($dPos = mssql_fetch_array($rPos)) {
			$pos_biaya .= $dPos['pos_biaya'].",";
			$ketakun .= $dPos['ketakun'].",";
			$nominal .= number_format($dPos['nominal'],0,",",".").",";
		}
		$pos_biaya = substr($pos_biaya, 0,strlen($pos_biaya)-1);
		$ketakun = substr($ketakun, 0,strlen($ketakun)-1);
		$nominal = substr($nominal, 0,strlen($nominal)-1);
		
		$pos_biaya2 = str_replace(",","','",$pos_biaya);
		
		
		$KodeDealer = $dt['kodedealer'];
		include '../inc/koneksi.php';
		if ($dt['WithPPN']=='1') { $ppn = "Yes"; } else { $ppn = "No"; }
		
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
				where a.Kodegl in ('".$pos_biaya2."') and Tahun='".$tahun."'";
				
			//$sql3 .="
			//	NUll as selesai
			//	from [$table]..glmst a
			//	inner join [$ra]..ra b on a.KodeGl=b.KodeGl
			//	where a.Kodegl = '".$dt['noAkun']."' and Tahun='".$tahun."'";
			//
			//echo "<pre>".$sql3."</pre>";
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

			// $nama = mssql_fetch_array(mssql_query("select NamaLgn from [$acc-".$tahun."".$hm."]..apmst 
			// 	where kodelgn='".$dt['KodeAP']."'",$connCab));
			
		
		}
		//
		// (($rapbBln-$realBln)<=$dt['nominal']) 
		if (($rapbBln-$realBln)<=$dt['totNom']) {
			$over = "Y";
		} else {
			$over = "T";
		}
		
				
		if ($data!=$dt['evo_id']) {
			if ($no %2 == 0) {$bg="background:#eaeaea;";} else {$bg="";}
			if ($dt['stataju']=='PROSES DEPT. HEAD FINANCE / DIV. HEAD FAST') {
				$stataju = "PROSES RELEASE BIAYA";
			} else {
				$stataju = nbsp($dt['stataju']);
			}
			$xml .= "<row id='".$no."'>";
			$xml .= "<cell><![CDATA[<div style='padding:2px;$bg'>
				<input type='checkbox' id='chk_".$no."' value='".$dt['evo_id']."' name='id[]' /></div>
			]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>".nbsp($over)."</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>".$stataju."</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>".nbsp($dt['pembuat'])."</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>".datenull($dt['tgl_pengajuan'])."</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>".datenull($dt['tgl_bayar'])."</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>".nbsp($dt['nobukti'])."</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>".nbsp($notagihan)."</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>".nbsp($dt['namavendor'])."</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>".number_format($dt['totNom'],0,",",".")."</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>".nbsp($dt['keterangan'])."</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>".$pos_biaya."</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>".$ketakun."</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>".$nominal."</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>".number_format($rapbBln,0,",",".")."</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>".number_format($realBln,0,",",".")."</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>".number_format($rapbOg,0,",",".")."</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>".number_format($realOg,0,",",".")."</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>".number_format($rapbThun,0,",",".")."</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>".number_format($realThun,0,",",".")."</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>".nbsp($dt['status'])."</div>]]></cell>";
			$xml .= "</row>";
		} 
		/*else {
			$no = $no-1;
			$xml .= "<row id='".$no."'>";
			$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>&nbsp;</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>&nbsp;</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>&nbsp;</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>&nbsp;</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>&nbsp;</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>&nbsp;</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>&nbsp;</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>&nbsp;</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>&nbsp;</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>&nbsp;</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>&nbsp;</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>".nbsp($dt['noAkun'])."</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>".nbsp($dt['namaAkun'])."</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>".number_format($dt['nominal'],0,",",".")."</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>".number_format($rapbBln,0,",",".")."</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>".number_format($realBln,0,",",".")."</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>".number_format($rapbOg,0,",",".")."</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>".number_format($realOg,0,",",".")."</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>".number_format($rapbThun,0,",",".")."</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>".number_format($realThun,0,",",".")."</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>&nbsp;</div>]]></cell>";
			$xml .= "</row>";
		}*/
		
		$data=$dt['evo_id'];
		$no++;
	}
	$xml .= "<total>$totalRow</total>";
	$xml .= "</rows>";
	echo $xml;
	
	function nbsp($val){
		if ($val==' ' || $val=='') {
			$data = "&nbsp;";
		} else {
			$data = $val;
		}
		return $data;
	}
?>