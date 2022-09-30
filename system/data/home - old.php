<?php
	$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : null;
	$IdUser = isset($_REQUEST['IdUser']) ? $_REQUEST['IdUser'] : null;
	if ($action=='proval') {
		require_once ('../inc/conn.php');
		// Cabang
		$txt .= proval('ACCOUNTING','',$IdUser)."#";
		$txt .= proval('SECTION HEAD','',$IdUser)."#";
		$txt .= proval('ADH','',$IdUser)."#";
		$txt .= proval('KEPALA CABANG','',$IdUser)."#";
		$txt .= proval('KASIR','',$IdUser)."#";
		// HO
		$txt .= proval('TAX','',$IdUser)."#";
		$txt .= proval('SECTION HEAD','',$IdUser)."#";
		$txt .= proval('DEPT. HEAD','',$IdUser)."#";
		$txt .= proval('DIREKSI','',$IdUser)."#";
		$txt .= proval('FINANCE','',$IdUser)."#";
		$txt .= proval('DEPT. HEAD FINANCE / DIV. HEAD FAST','',$IdUser)."#";
		$txt .= proval('DIV. HEAD','',$IdUser);
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
		$txt .= prodash('TAX','all',$IdUser)."#";
		$txt .= prodash('SECTION HEAD','',$IdUser)."#";
		$txt .= prodash('DEPT. HEAD','',$IdUser)."#";
		$txt .= prodash('DIV. HEAD','',$IdUser)."#";
		$txt .= prodash('DIREKSI','all',$IdUser)."#";
		$txt .= prodash('FINANCE','all',$IdUser)."#";
		$txt .= prodash('DEPT. HEAD FINANCE / DIV. HEAD FAST','all',$IdUser)."#";
		$txt .= prodash('FINANCE','all',$IdUser)."#";
		$txt .= prodash('all','all',$IdUser);
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
		while ($dt = mssql_fetch_array($rsl)) {
			$pilih = ($value==$dt['nama_dept'])?"selected" : ""; 
			echo "<option value='".$dt['nama_dept']."' $pilih>".$dt['nama_dept']."</option>";
		}
	} else if ($action=='getAtasan') {
		require_once ('../inc/conn.php');
		$kodedealer = isset($_REQUEST['kodedealer']) ? $_REQUEST['kodedealer'] : null;
		$tipe = isset($_REQUEST['tipe']) ? $_REQUEST['tipe'] : null;
		$divisi = isset($_REQUEST['divisi']) ? $_REQUEST['divisi'] : null;
		$department = isset($_REQUEST['department']) ? $_REQUEST['department'] : null;
		$value = isset($_REQUEST['value']) ? $_REQUEST['value'] : null;
		$plh = ($value=='all')?"selected" : ""; 
		if ($department!='') { $dept = "and department = '".$department."'"; } else { $dept = ""; }
		if (($kodedealer=='2010' and $department!='') or ($kodedealer!='2010' and $tipe!='')) {
			$sql = "select * from sys_user where KodeDealer = '".$kodedealer."' and divisi in ('".$divisi."','all') and tipe not in ('".$tipe."') $dept";
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
		$level = isset($_REQUEST['level']) ? $_REQUEST['level'] : false;
		$searchText = isset($_REQUEST['searchText']) ? $_REQUEST['searchText'] : false;

		require_once ('../inc/conn.php');

		$page = $_REQUEST['page'];
		$rp = $_REQUEST['rp'];
		$sortname = $_REQUEST['sortname'];
		$sortorder = $_REQUEST['sortorder'];
		
		if (!$sortname) $sortname = 'tgl_pengajuan';
		if (!$sortorder) $sortorder = 'asc';
		$sort = "ORDER BY $sortname $sortorder";
		if ($searchText!='') {
			$src = " and (nobukti like '%".$searchText."%' or tgl_pengajuan like '%".$searchText."%' or userentry like '%".$searchText."%' or NamaDealer like '%".$searchText."%' or namaVendor like '%".$searchText."%' or keterangan like '%".$searchText."%')";
		} else {
			$src = "";
		}

		if (!$page) $page = 1;
		if (!$rp) $rp = 10;

		$start = (($page-1) * $rp);

		// if ($level=='FINANCE') { $val = "Accept"; } else { $val = ""; }

		if ($level == 'all') { 
			$stat = ""; 
		} else { 
			$stat = "and stataju in ('".$level."')"; 
		}
		$user = mssql_fetch_array(mssql_query("select * from sys_user where IdUser = '".$IdUser."'"));
		if ($user['KodeDealer']=='all') { $area = ""; } else { $area = "and kodedealer = '".$user['KodeDealer']."'"; }
		if ($user['divisi']=='all') { $divisi = ""; } else { $divisi = "and divisi = '".$user['divisi']."'"; }
		if ($user['tipe']=='SECTION HEAD') { 
			$atasan = "and IdAtasan = '".$IdUser."'"; 
		} else if ($user['tipe']=='DEPT. HEAD') { 
			if ($user['department']=='FINANCE') {
				$atasan = ""; 
			} else {
				$atasan = "and (select IdAtasan from sys_user where IdUser=x.IdAtasan)= '".$IdUser."'"; 
			}
		} else { 
			$atasan = ""; 
		}
		// $sql = "
		// 	select top $rp * from (
		// 		select evo_id,nobukti,tgl_pengajuan,userentry as nama_aju,NamaDealer,namaVendor,a.kodedealer,divisi,
		// 		case when tipe='HUTANG' then htg_stl_pajak else biaya_yg_dibyar end as amount,keterangan,IdAtasan,
		// 		(select top 1 case when ISNULL(validasi, '')='".$val."' then level else level end as stataju from DataEvoVal 
		// 		where nobukti=a.nobukti order by tglentry desc) as stataju,
		// 		(select top 1 ISNULL(validasi, '') from DataEvoVal where nobukti=a.nobukti order by tglentry desc) as validasi
		// 		from DataEvo a inner join SPK00..dodealer b on a.kodedealer=b.kodedealer
		// 		where evo_id=evo_id $src
		// 	) x where evo_id not in (
		// 		select top $start evo_id from (
		// 			select evo_id,a.kodedealer,(select top 1 case when ISNULL(validasi, '')='".$val."' then level else level end as stataju from DataEvoVal 
		// 			where nobukti=a.nobukti order by tglentry desc) as stataju,divisi,IdAtasan,
		// 			(select top 1 ISNULL(validasi, '') from DataEvoVal where nobukti=a.nobukti order by tglentry desc) as validasi
		// 			from DataEvo a inner join SPK00..dodealer b on a.kodedealer=b.kodedealer
		// 			where evo_id=evo_id $src
		// 		) x where validasi = '".$val."' $area $divisi $atasan $stat $sort
		// 	) and validasi = '".$val."' $area $divisi $atasan $stat $sort
		// ";

		$sql = "
			select top $rp * from (
				select evo_id,nobukti,tgl_pengajuan,userentry as nama_aju,NamaDealer,namaVendor,a.kodedealer,divisi,
				case when tipe='HUTANG' then htg_stl_pajak else biaya_yg_dibyar end as amount,keterangan,IdAtasan,
				(select top 1 case when ISNULL(validasi, '')='".$val."' then level else level end as stataju from DataEvoVal 
				where nobukti=a.nobukti order by tglentry desc) as stataju,
				(select top 1 ISNULL(validasi, '') from DataEvoVal where nobukti=a.nobukti order by tglentry desc) as validasi
				from DataEvo a inner join SPK00..dodealer b on a.kodedealer=b.kodedealer
				where evo_id=evo_id $src
			) x where evo_id not in (
				select top $start evo_id from (
					select evo_id,a.kodedealer,(select top 1 case when ISNULL(validasi, '')='".$val."' then level else level end as stataju from DataEvoVal 
					where nobukti=a.nobukti order by tglentry desc) as stataju,divisi,IdAtasan,
					(select top 1 ISNULL(validasi, '') from DataEvoVal where nobukti=a.nobukti order by tglentry desc) as validasi
					from DataEvo a inner join SPK00..dodealer b on a.kodedealer=b.kodedealer
					where evo_id=evo_id $src
				) x where evo_id=evo_id $area $divisi $atasan $stat $sort
			) $area $divisi $atasan $stat $sort
		";
		// echo $sql;
		$result = mssql_query($sql);
		$total = mssql_num_rows(mssql_query("
			select evo_id from (
				select evo_id,a.kodedealer,(select top 1 case when ISNULL(validasi, '')='".$val."' then level else level end as stataju from DataEvoVal 
				where nobukti=a.nobukti order by tglentry desc) as stataju,divisi,IdAtasan,
				(select top 1 ISNULL(validasi, '') from DataEvoVal where nobukti=a.nobukti order by tglentry desc) as validasi
				from DataEvo a inner join SPK00..dodealer b on a.kodedealer=b.kodedealer
			) x where validasi = '".$val."' $area $divisi $atasan $stat
		"));
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
			$xml .= "<cell><![CDATA[".utf8_encode($row['NamaDealer'])."]]></cell>";
			$xml .= "<cell><![CDATA[".utf8_encode($row['namaVendor'])."]]></cell>";
			$xml .= "<cell><![CDATA[<div style='text-align:right;padding:0'>".number_format($row['amount'],0,",",".")."</div>]]></cell>";
			$xml .= "<cell><![CDATA[".utf8_encode($row['keterangan'])."]]></cell>";
			$xml .= "</row>";
		}
		$xml .= "<total>".$total."</total>";	
		$xml .= "</rows>";
		echo $xml;
	}

	function proval($lvl,$val,$IdUser){
		require_once ('../inc/conn.php');
		if ($lvl == 'all') { 
			$stat = ""; 
		} else { 
			$stat = "and stataju = '".$lvl."'"; 
		}
		$user = mssql_fetch_array(mssql_query("select * from sys_user where IdUser = '".$IdUser."'"));
		if ($user['KodeDealer']=='all') { $area = ""; } else { $area = "and kodedealer = '".$user['KodeDealer']."'"; }
		if ($user['divisi']=='all') { $divisi = ""; } else { $divisi = "and divisi = '".$user['divisi']."'"; }
		if ($user['tipe']=='SECTION HEAD') { 
			$atasan = "and IdAtasan = '".$IdUser."'"; 
		} else if ($user['tipe']=='DEPT. HEAD') { 
			if ($user['department']=='FINANCE') {
				$atasan = ""; 
			} else {
				$atasan = "and (select IdAtasan from sys_user where IdUser=x.IdAtasan)= '".$IdUser."'"; 
			}
		} else { 
			$atasan = ""; 
		}
		$sql = "
			select evo_id tot from (
				select evo_id,nobukti,tgl_pengajuan,userentry as nama_aju,NamaDealer,namaVendor,a.kodedealer,divisi,
				case when tipe='HUTANG' then htg_stl_pajak else biaya_yg_dibyar end as amount,keterangan,IdAtasan,
				(select top 1 case when ISNULL(validasi, '')='".$val."' then level else level end as stataju from DataEvoVal 
				where nobukti=a.nobukti order by tglentry desc) as stataju,
				(select top 1 ISNULL(validasi, '') from DataEvoVal where nobukti=a.nobukti order by tglentry desc) as validasi
				from DataEvo a inner join SPK00..dodealer b on a.kodedealer=b.kodedealer
			) x where validasi = '".$val."' $area $divisi $atasan $stat
		";
		$rsl = mssql_query($sql);
		$count = mssql_num_rows($rsl);
		return $count;
	}

	function prodash($lvl,$divisi,$IdUser){
		require_once ('../inc/conn.php');
		$user = mssql_fetch_array(mssql_query("select * from sys_user where IdUser = '".$IdUser."'"));
		if ($lvl == 'all') { 
			$stat = ""; 
		} else { 
			$stat = "and stataju = '".$lvl."'"; 
		}
		if ($divisi == 'all') { 
			$divisiarea = ""; 
		} else if ($divisi == '') { 
			$divisiarea = "and divisi = '".$user['divisi']."'"; 
		} else { 
			$divisiarea = "and divisi = '".$divisi."'"; 
		}
		if ($user['KodeDealer']=='all') { $area = ""; } else { $area = "and kodedealer = '".$user['KodeDealer']."'"; }
		if ($user['tipe']=='SECTION HEAD') { 
			$atasan = "and IdAtasan = '".$IdUser."'"; 
		} else if ($user['tipe']=='DEPT. HEAD') { 
			if ($user['department']=='FINANCE') {
				$atasan = ""; 
			} else {
				$atasan = "and (select IdAtasan from sys_user where IdUser=x.IdAtasan)= '".$IdUser."'"; 
			}
		} else { 
			$atasan = ""; 
		}
		$sql = "
			select evo_id tot from (
				select evo_id,nobukti,tgl_pengajuan,userentry as nama_aju,NamaDealer,namaVendor,a.kodedealer,divisi,
				case when tipe='HUTANG' then htg_stl_pajak else biaya_yg_dibyar end as amount,keterangan,IdAtasan,
				(select top 1 case when ISNULL(validasi, '')='' then level else level end as stataju from DataEvoVal 
				where nobukti=a.nobukti order by tglentry desc) as stataju,
				(select top 1 ISNULL(validasi, '') from DataEvoVal where nobukti=a.nobukti order by tglentry desc) as validasi
				from DataEvo a inner join SPK00..dodealer b on a.kodedealer=b.kodedealer
			) x where evo_id = evo_id $area $divisiarea $atasan $stat
		";
		$rsl = mssql_query($sql);
		$count = mssql_num_rows($rsl);
		return $count;
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
		if ($user['divisi'] == 'all') { 
			$divisiarea = ""; 
		} else { 
			$divisiarea = "and divisi = '".$user['divisi']."'"; 
		}
		if ($user['KodeDealer']=='all') { $area = ""; } else { $area = "and kodedealer = '".$user['KodeDealer']."'"; }
		if ($user['tipe']=='SECTION HEAD') { 
			$atasan = "and IdAtasan = '".$IdUser."'"; 
		} else if ($user['tipe']=='DEPT. HEAD') { 
			if ($user['department']=='FINANCE') {
				$atasan = ""; 
			} else {
				$atasan = "and (select IdAtasan from sys_user where IdUser=x.IdAtasan)= '".$IdUser."'"; 
			}
		} else { 
			$atasan = ""; 
		}
		$sql = "
			select sum(amount) tot from (
				select evo_id,nobukti,tgl_pengajuan,userentry as nama_aju,NamaDealer,namaVendor,a.kodedealer,divisi,IdAtasan,
				case when tipe='HUTANG' then htg_stl_pajak else biaya_yg_dibyar end as amount,keterangan,
				(select top 1 case when ISNULL(validasi, '')='' then level else level end as stataju from DataEvoVal 
				where nobukti=a.nobukti order by tglentry desc) as stataju,tgl_bayar,
				(select top 1 ISNULL(validasi, '') from DataEvoVal where nobukti=a.nobukti order by tglentry desc) as validasi
				from DataEvo a inner join SPK00..dodealer b on a.kodedealer=b.kodedealer
			) x where validasi = '' $date $area $divisiarea $atasan
		";
		$rsl = mssql_query($sql);
		$count = mssql_fetch_array($rsl);
		return number_format($count['tot'],0,",",".");
	}
?>