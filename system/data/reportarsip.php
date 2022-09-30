<?php
	error_reporting(0);
	$page = isset($_POST['page']) ? $_POST['page'] : 1;
	$rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
	$sortname = isset($_POST['sortname']) ? $_POST['sortname'] : 'tgl_pengajuan';
	$sortorder = isset($_POST['sortorder']) ? $_POST['sortorder'] : 'desc';
	$query = isset($_POST['query']) ? $_POST['query'] : false;
	$qtype = isset($_POST['qtype']) ? $_POST['qtype'] : false;

	require_once ('../inc/conn.php');

	$page = $_POST['page'];
	$rp = $_POST['rp'];
	$sortname = $_POST['sortname'];
	$sortorder = $_POST['sortorder'];

	if (!$sortname) $sortname = 'tgl_pengajuan';
	if (!$sortorder) $sortorder = 'desc';
	$IdUser = isset($_REQUEST['IdUser']) ? $_REQUEST['IdUser'] : null;

	$nobukti = isset($_REQUEST['nobukti']) ? $_REQUEST['nobukti'] : null;
	$notagihan = isset($_REQUEST['notagihan']) ? $_REQUEST['notagihan'] : null;
	$startDate = isset($_REQUEST['startDate']) ? $_REQUEST['startDate'] : null;
	$endDate = isset($_REQUEST['endDate']) ? $_REQUEST['endDate'] : null;
	$validasi = isset($_REQUEST['validasi']) ? $_REQUEST['validasi'] : null;
	//$statCsv = isset($_REQUEST['statCsv']) ? $_REQUEST['statCsv'] : null;
	$statBayar = isset($_REQUEST['statBayar']) ? $_REQUEST['statBayar'] : null;
	$noCsv = isset($_REQUEST['noCsv']) ? $_REQUEST['noCsv'] : null;
	$txtSearch = isset($_REQUEST['txtSearch']) ? $_REQUEST['txtSearch'] : null;
	$divisi = isset($_REQUEST['divisi']) ? $_REQUEST['divisi'] : null;
	$department = isset($_REQUEST['department']) ? $_REQUEST['department'] : null;
	
	$sort = "ORDER BY $sortname $sortorder";

	if ($nobukti!='') {
		$s_nobukti = "and nobukti like '%".$nobukti."%'";
	} else {
		$s_nobukti = "";
	}
	//echo $s_nobukti;

	if ($startDate!='' and $endDate!='') {
		$s_tglAju = "and tgl_pengajuan between '".$startDate."' and '".$endDate."'";
	} else {
		$s_tglAju = "and tgl_pengajuan between '".date('Y-m-01')."' and '".date('Y-m-t')."'";
	}

	if ($validasi!='') {
		$s_validasi = "and validasi = '".$validasi."'";
	} else {
		$s_validasi = "";
	}

	/*if ($statCsv=='') {
		$s_statCsv = "";
	} else if ($statCsv=='0') {
		$s_statCsv = "and isnull(noCsv,'')=''";
	} else {
		$s_statCsv = "and isnull(noCsv,'')!=''";
	}

	if ($noCsv!='') {
		$s_noCsv = "and noCsv like '%".$noCsv."%'";
	} else {
		$s_noCsv = "";
	}*/
	
	if ($statBayar=='') {
		$s_statBayar = "";
	} else if ($statBayar=='0') {
		$s_statBayar = "and isnull(tglbayar,'')=''";
	} else {
		$s_statBayar = "and isnull(tglbayar,'')!=''";
	}

	
	// No Tagihan, NPWP, Keterangan, dan Voucher Lunas (No Bukti Bank / Kas

	if ($txtSearch!='') {
		$s_txtSearch = "and (noCsv like '%".$txtSearch."%' or kodedealer like '%".$txtSearch."%' or userAju like '%".$txtSearch."%' or tipe like '%".$txtSearch."%' or tgl_pengajuan like '%".$txtSearch."%' or nobukti like '%".$txtSearch."%' or metode_bayar like '%".$txtSearch."%' or kode_vendor like '%".$txtSearch."%' or namaVendor like '%".$txtSearch."%' or benificary_account like '%".$txtSearch."%' or nama_bank like '%".$txtSearch."%' or nama_pemilik like '%".$txtSearch."%' or nama_alias like '%".$txtSearch."%' or tgl_bayar like '%".$txtSearch."%' or email_penerima like '%".$txtSearch."%' or nama_bank_pengirim like '%".$txtSearch."%' or tf_from_account like '%".$txtSearch."%' or npwp like '%".$txtSearch."%' or keterangan like '%".$txtSearch."%' or kode_voucher like '%".$txtSearch."%' )";
	} else {
		$s_txtSearch = "";
	}

	if ($divisi!='') {
		$s_divisi = "and divisi like '%".$divisi."%'";
	} else {
		$s_divisi = "";
	}

	if ($department!='') {
		$s_department = "and department like '%".$department."%'";
	} else {
		$s_department = "";
	}
	
	if (!$page) $page = 1;
	if (!$rp) $rp = 10;

	$start = (($page-1) * $rp);
	
	$user = mssql_fetch_array(mssql_query("select * from sys_user where IdUser = '".$IdUser."'"));
	/*if ($user['tipe']=='ADMIN') { 
		$s_admin = "and pengaju = '".$IdUser."'"; 
	} else { 
		$s_admin = ""; 
	}
	
	if ($user['tipe']=='SECTION HEAD' or $user['tipe']=='ADH') { 
		$s_section = "and (sect = '".$IdUser."' or pengaju = '".$IdUser."') "; 
	} else { 
		$s_section = ""; 
	}
	
	if ($user['tipe']=='DEPT. HEAD' or $user['tipe']=='KEPALA CABANG') { 
		if ($user['divisi']=='FINANCE, ADMINISTRATION & SYSTEM TECHNOLOGY' and $user['department']=='FINANCE') {
			$s_dept = "and (ISNULL(level, '')='DEPT. HEAD FINANCE / DIV. HEAD FAST' or dept = '".$IdUser."')"; 
		} else {
			$s_dept = "and (dept = '".$IdUser."'  or pengaju = '".$IdUser."')"; 
		}
	} else { 
		$s_dept = ""; 
	}
	if ($user['tipe']=='DIV. HEAD') { 
		if ($user['divisi']=='FINANCE, ADMINISTRATION & SYSTEM TECHNOLOGY' and ($user['department']=='FINANCE' or $user['department']=='')) {
			$s_div = "and (ISNULL(level, '')='DEPT. HEAD FINANCE / DIV. HEAD FAST' or div = '".$IdUser."')"; 
		} else {
			$s_div = "and (div = '".$IdUser."'  or pengaju = '".$IdUser."') "; 
		}
	} else { 
		$s_div = ""; 
	}

	if ($user['tipe']=='KASIR') { 
		$s_kasir = "and ISNULL(level, '')='KASIR'"; 
	} else { 
		$s_kasir = ""; 
	}
	*/
	//$s_where = " and a.kodedealer = '".$user['KodeDealer']."' and (isnull(a.tglbayar,'')!='' or a.nobukti in (select nobukti from dataevoval where level = 'KASIR') )";
	$s_where = " and a.kodedealer = '".$user['KodeDealer']."' ";
	
	if ($notagihan!='') {
		$s_txtSearchtag = "and NoFaktur like '%".$notagihan."%'";
	} else {
		$s_txtSearchtag = ""; 
	}

	// and a.nobukti in (select nobukti from DataEvoTagihan where nobukti is not null $s_txtSearchtag)

	$sql = "
		select top $rp * from (
			
			select evo_id,a.kodedealer,a.divisi,department,a.IdAtasan,NamaDealer,userentry as userAju,a.tipe,tgl_pengajuan,a.nobukti,metode_bayar,kode_vendor,
			namaVendor,benificary_account,nama_bank,nama_pemilik,nama_alias,tgl_bayar,email_penerima,no_fj,
			nama_bank_pengirim,tf_from_account,realisasi_nominal,a.npwp,dpp,ppn,pos_biaya,nominal,
			case when ISNULL(jns_pph, '')!='' then jns_pph+' ('+convert(varchar,tarif_persen)+'%)' else '' end as pph, nilai_pph as nilaiPph,
			case when a.tipe='HUTANG' then htg_stl_pajak else biaya_yg_dibyar end as totBayar,keterangan,
				(select top 1 case when ISNULL(validasi, '')='' then '0' when ISNULL(validasi, '')='Accept' then '1' else '2' end as validasi
				from DataEvoVal where nobukti=a.nobukti order by tglentry desc) as validasi,
			noCsv,metode_transfer,isnull(kode_voucher,' ') as kode_voucher, tglbayar
			
			from DataEvo a
			left join DataEvoPos b on a.nobukti=b.nobukti
			inner join spk00..dodealer c on a.kodedealer=c.kodedealer
			left join DataEvoTransfer d on a.nobukti=d.nobukti
			inner join sys_user e on a.userentry=e.IdUser
			
			where isnull(d.is_del,'')='' $s_where
						
			
			and a.nobukti in (
				select DISTINCT nobukti from(select a.nobukti,userentry as pengaju,IdAtasan as sect,
				(select IdAtasan from sys_user where IdUser=a.IdAtasan) as dept,
				(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.IdAtasan)) as div,level 
				from DataEvo a 
				inner join DataEvoVal b on a.nobukti=b.nobukti) x 
				where nobukti=nobukti $s_admin $s_section $s_dept $s_div $s_kasir $s_statBayar
			)

		) x where evo_id not in (
			select top $start evo_id from (
				select evo_id,tgl_pengajuan,validasi from (
					
					select evo_id,tgl_pengajuan,a.kodedealer,a.divisi,department,a.IdAtasan,
						(select top 1 case when ISNULL(validasi, '')='' then '0' when ISNULL(validasi, '')='Accept' then '1' else '2' end as validasi
						from DataEvoVal where nobukti=a.nobukti order by tglentry desc) as validasi
					
					from DataEvo a left join DataEvoPos b on a.nobukti=b.nobukti 
					inner join spk00..dodealer c on a.kodedealer=c.kodedealer
					left join DataEvoTransfer d on a.nobukti=d.nobukti
					inner join sys_user e on a.userentry=e.IdUser
					
					where isnull(d.is_del,'')='' $s_where
										
					and a.nobukti in (
						select DISTINCT nobukti from(select a.nobukti,userentry as pengaju,IdAtasan as sect,
						(select IdAtasan from sys_user where IdUser=a.IdAtasan) as dept,
						(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.IdAtasan)) as div,level 
						from DataEvo a inner join DataEvoVal b on a.nobukti=b.nobukti) x 
						where nobukti=nobukti $s_admin $s_section $s_dept $s_div $s_kasir
					)

				) y where evo_id=evo_id $s_nobukti $s_tglAju $s_validasi $s_statCsv $s_noCsv $s_divisi $s_department $s_statBayar $s_txtSearch
			) z $sort
		) $s_nobukti $s_tglAju $s_validasi $s_statCsv $s_noCsv $s_divisi $s_department $s_statBayar $s_txtSearch $sort
	";
	//echo "<pre>".$sql."</pre>";
	$rsl = mssql_query($sql,$conns);
	$rows = array();
	while ($row = mssql_fetch_array($rsl)) {
		$rows[] = $row;
	}
	
	/*$totals = mssql_num_rows(mssql_query("
		select DISTINCT evo_id,tgl_pengajuan,nobukti,validasi,divisi,department 
		from (
			select evo_id,tgl_pengajuan,a.nobukti,a.kodedealer,a.divisi,department,a.IdAtasan,
				(select top 1 case when ISNULL(validasi, '')='' then '0' when ISNULL(validasi, '')='Accept' then '1' else '2' end as validasi
				from DataEvoVal where nobukti=a.nobukti order by tglentry desc) as validasi,
			noCsv,nama_alias,tgl_bayar,email_penerima,nama_bank,
			tf_from_account,kode_vendor,namaVendor,benificary_account,nama_pemilik,nama_bank_pengirim,userentry as userAju,a.tipe,metode_bayar
			from DataEvo a 
			left join DataEvoPos b on a.nobukti=b.nobukti 
			inner join spk00..dodealer c on a.kodedealer=c.kodedealer 
			left join DataEvoTransfer d on a.nobukti=d.nobukti
			inner join sys_user e on a.userentry=e.IdUser
			
			where isnull(d.is_del,'')='' and a.nobukti in (
				select DISTINCT nobukti from(select a.nobukti,userentry as pengaju,IdAtasan as sect,
				(select IdAtasan from sys_user where IdUser=a.IdAtasan) as dept,
				(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.IdAtasan)) as div,level 
				from DataEvo a inner join DataEvoVal b on a.nobukti=b.nobukti) x 
				where nobukti=nobukti $s_admin $s_section $s_dept $s_div $s_kasir
			)
		) y 
		where evo_id=evo_id $s_nobukti $s_tglAju $s_validasi $s_statCsv $s_noCsv $s_divisi $s_department $s_txtSearch
	",$conns));*/
	
	$qry_tot = "
		select * from (
		select evo_id,a.kodedealer,a.divisi,department,a.IdAtasan,NamaDealer,userentry as userAju,a.tipe,tgl_pengajuan,a.nobukti,metode_bayar,kode_vendor,
			namaVendor,benificary_account,nama_bank,nama_pemilik,nama_alias,tgl_bayar,email_penerima,no_fj,
			nama_bank_pengirim,tf_from_account,realisasi_nominal,a.npwp,dpp,ppn,pos_biaya,nominal,
			case when ISNULL(jns_pph, '')!='' then jns_pph+' ('+convert(varchar,tarif_persen)+'%)' else '' end as pph, nilai_pph as nilaiPph,
			case when a.tipe='HUTANG' then htg_stl_pajak else biaya_yg_dibyar end as totBayar,keterangan,
				(select top 1 case when ISNULL(validasi, '')='' then '0' when ISNULL(validasi, '')='Accept' then '1' else '2' end as validasi
				from DataEvoVal where nobukti=a.nobukti order by tglentry desc) as validasi,
			noCsv,metode_transfer,isnull(kode_voucher,' ') as kode_voucher, tglbayar
			
			from DataEvo a
			left join DataEvoPos b on a.nobukti=b.nobukti
			inner join spk00..dodealer c on a.kodedealer=c.kodedealer
			left join DataEvoTransfer d on a.nobukti=d.nobukti
			inner join sys_user e on a.userentry=e.IdUser
			
			where isnull(d.is_del,'')='' $s_where
			
			and a.nobukti in (
				select DISTINCT nobukti from(select a.nobukti,userentry as pengaju,IdAtasan as sect,
				(select IdAtasan from sys_user where IdUser=a.IdAtasan) as dept,
				(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.IdAtasan)) as div,level 
				from DataEvo a inner join DataEvoVal b on a.nobukti=b.nobukti) x 
				where nobukti=nobukti $s_admin $s_section $s_dept $s_div $s_kasir $s_statBayar
			)
		) x
		where evo_id=evo_id $s_nobukti $s_tglAju $s_validasi $s_statCsv $s_noCsv $s_divisi $s_department $s_statBayar $s_txtSearch
	";
	
	//echo "<pre>$qry_tot</pre>";
	$totals = mssql_num_rows(mssql_query($qry_tot,$conns));
	
	$no=1;
	$data = "";
	
	header("Content-type: text/xml");
	$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
	$xml .= "<rows>";
	$xml .= "<page>$page</page>";
	$total_data = 0;
	
	foreach($rows as $dt) {
		//if ($data!=$dt['evo_id']) {
			// if ($no %2 == 0) {$bg="background:#eaeaea;";} else {$bg="";}
			$val = mssql_fetch_array(mssql_query("
				select top 1 case when ISNULL(validasi, '')='' then 'PROSES ' + level else validasi+' '+level end as stataju,ketvalidasi,
				tglValidasi,level as statVal,case when ISNULL(validasi, '')='' then 'BELOM PROSES ' + level else validasi end as validasi
				from DataEvoVal 
				where nobukti='".$dt['nobukti']."' and isnull(level,'')!='' 
				order by tglentry desc
			",$conns));

			$sTagihan = "select NoFaktur,TglTrnFaktur 
							from DataEvoTagihan 
							where nobukti = '".$dt['nobukti']."' ";
			$rTagihan = mssql_query($sTagihan);
			$tagihan = ""; $tglfaktur = "";
			while ($dTagihan = mssql_fetch_array($rTagihan)) {
				$tagihan .= $dTagihan['NoFaktur'].",";
				$tglfaktur .= datenull($dTagihan['TglTrnFaktur']).",";
			}
			$tagihan = substr($tagihan, 0,strlen($tagihan)-1);
			$tglfaktur = substr($tglfaktur, 0,strlen($tglfaktur)-1);
			
			if ($dt['kode_voucher']!=' ') {
				$ketLunas = "LUNAS";
			} else {
				$ketLunas = "BELOM";
				// $ketLunas = 
				// cekLunas($dt['nobukti']);
			}
			if ($val['stataju']=='PROSES DEPT. HEAD FINANCE / DIV. HEAD FAST') {
				$stataju = "PROSES RELEASE BIAYA";
			} else if ($val['stataju']=='PROSES DEPT. HEAD FINANCE') {
				$stataju = "Proses Release Dept. Head Fin";
			} else {
				$stataju = strtoupper(nbsp($val['stataju']));
			}
			if ($val['validasi']=='Reject') { $disable = "disabled"; } else { $disable = ""; }
			
			
			$xml .= "<row id='".$no."'>";
			$xml .= "<cell><![CDATA[";
			$xml .= "<input type='checkbox' id='chk_".$no."' value='".$dt['evo_id']."' name='id[]' ".$disable."/>";
			$xml .= "<input type='hidden' id='stat_".$no."' value='".$dt['noCsv']."' />";
			$xml .= "<input type='hidden' id='metod_".$no."' value='".$dt['metode_transfer']."' />";
			$xml .= "]]></cell>";
			$xml .= "<cell><![CDATA[";
			$xml .= "<a href='javascript:void(0);' style='color: #e73c3c;text-decoration: none;' onclick='getDetail(".$dt['evo_id'].");'>".nbsp($dt['nobukti'])."</a>";
			$xml .= "]]></cell>";			
			$xml .= "<cell><![CDATA[".nbsp($dt['userAju'])."]]></cell>";
			$xml .= "<cell><![CDATA[".$stataju."]]></cell>";
			$xml .= "<cell><![CDATA[".datenull($dt['tgl_pengajuan'])."]]></cell>";
			// tgl tagihan					
			$xml .= "<cell><![CDATA[".nbsp($tglfaktur)."]]></cell>";
			$xml .= "<cell><![CDATA[".$ketLunas."]]></cell>";							
			$xml .= "<cell><![CDATA[";
			$xml .= "<a href='javascript:void(0);' style='color: #e73c3c;text-decoration: none;' onclick='getBuktiKas(\"".$dt['nobukti']."\");'>".nbsp($dt['kode_voucher'])."</a>";
			$xml .= "]]></cell>";
			$xml .= "<cell><![CDATA[".datenull($dt['tgl_bayar'])."]]></cell>";
			$xml .= "<cell><![CDATA[".nbsp($dt['metode_bayar'])."]]></cell>";
			$xml .= "<cell><![CDATA[".nbsp($dt['tipe'])."]]></cell>";	
			$xml .= "<cell><![CDATA[".nbsp($tagihan)."]]></cell>";
			$xml .= "<cell><![CDATA[".nbsp($dt['kode_vendor'])."]]></cell>";
			$xml .= "<cell><![CDATA[".nbsp($dt['namaVendor'])."]]></cell>";
			$xml .= "<cell><![CDATA[".nbsp($dt['benificary_account'])."]]></cell>";
			$xml .= "<cell><![CDATA[".nbsp($dt['nama_bank'])."]]></cell>";
			$xml .= "<cell><![CDATA[".nbsp($dt['nama_pemilik'])."]]></cell>";
			$xml .= "<cell><![CDATA[".nbsp($dt['nama_alias'])."]]></cell>";			
			$xml .= "<cell><![CDATA[".nbsp($dt['email_penerima'])."]]></cell>";
			$xml .= "<cell><![CDATA[".nbsp($dt['nama_bank_pengirim'])."]]></cell>";
			$xml .= "<cell><![CDATA[".nbsp($dt['tf_from_account'])."]]></cell>";
			$xml .= "<cell><![CDATA[<div style='text-align:right;'>".number_format($dt['realisasi_nominal'],0,",",".")."</div>]]></cell>";
			$xml .= "<cell><![CDATA[".nbsp($dt['npwp'])."]]></cell>";
			$xml .= "<cell><![CDATA[".nbsp($dt['no_fj'])."]]></cell>";
			$xml .= "<cell><![CDATA[<div style='text-align:right;'>".number_format($dt['dpp'],0,",",".")."</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='text-align:right;'>".number_format($dt['ppn'],0,",",".")."</div>]]></cell>";
			$xml .= "<cell><![CDATA[".nbsp($dt['pos_biaya'])."]]></cell>";
			$xml .= "<cell><![CDATA[<div style='text-align:right;'>".number_format($dt['nominal'],0,",",".")."</div>]]></cell>";
			$xml .= "<cell><![CDATA[".nbsp($dt['pph'])."]]></cell>";
			$xml .= "<cell><![CDATA[<div style='text-align:right;'>".number_format($dt['nilaiPph'],0,",",".")."</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='text-align:right;'>".number_format($dt['totBayar'],0,",",".")."</div>]]></cell>";
			$xml .= "<cell><![CDATA[".nbsp($dt['keterangan'])."]]></cell>";
			$xml .= "<cell><![CDATA[".nbsp($val['statVal'])."]]></cell>";	
			$xml .= "<cell><![CDATA[".nbsp($val['validasi'])."]]></cell>";
				
			if ($dt['kodedealer']=='2010') {
				$xml .= "<cell><![CDATA[".nbsp(note($dt['nobukti'],'SECTION HEAD','',''))."]]></cell>";
				$xml .= "<cell><![CDATA[".nbsp(note($dt['nobukti'],'DEPT. HEAD','',''))."]]></cell>";
				$xml .= "<cell><![CDATA[".nbsp(note($dt['nobukti'],'DIV. HEAD','',''))."]]></cell>";
				$xml .= "<cell><![CDATA[".nbsp(note($dt['nobukti'],'DIREKSI','',''))."]]></cell>";
				$xml .= "<cell><![CDATA[".nbsp(note($dt['nobukti'],'DIREKSI 2','',''))."]]></cell>";
				$xml .= "<cell><![CDATA[".nbsp(note($dt['nobukti'],'DEPT. HEAD','FINANCE, ADMINISTRATION & SYSTEM TECHNOLOGY','FINANCE'))."]]></cell>";
				$xml .= "<cell><![CDATA[".nbsp(note($dt['nobukti'],'DIV. HEAD','FINANCE, ADMINISTRATION & SYSTEM TECHNOLOGY','all'))."]]></cell>";
			} else {
				$xml .= "<cell><![CDATA[".nbsp(note($dt['nobukti'],'SECTION HEAD','',''))."]]></cell>";
				$xml .= "<cell><![CDATA[".nbsp(note($dt['nobukti'],'ADH','',''))."]]></cell>";
				$xml .= "<cell><![CDATA[".nbsp(note($dt['nobukti'],'KEPALA CABANG','',''))."]]></cell>";
				$xml .= "<cell><![CDATA[".nbsp(note($dt['nobukti'],'OM','',''))."]]></cell>";
			}
			
			$xml .= "<cell><![CDATA[".nbsp($dt['noCsv'])."]]></cell>";		
			$xml .= "<cell><![CDATA[".nbsp($dt['department'])."]]></cell>";
			$xml .= "<cell><![CDATA[".nbsp($dt['kodedealer'])." | ".$dt['NamaDealer']."]]></cell>";
			$xml .= "</row>";
		
			
		//} else {
			//$no = $no-1;
			/*$xml .= "<row id='".$no."'>";
			$xml .= "<cell><![CDATA[&nbsp;]]></cell>";
			$xml .= "<cell><![CDATA[&nbsp;]]></cell>";
			$xml .= "<cell><![CDATA[&nbsp;]]></cell>";
			$xml .= "<cell><![CDATA[&nbsp;]]></cell>";
			$xml .= "<cell><![CDATA[&nbsp;]]></cell>";
			$xml .= "<cell><![CDATA[&nbsp;]]></cell>";
			$xml .= "<cell><![CDATA[&nbsp;]]></cell>";
			$xml .= "<cell><![CDATA[&nbsp;]]></cell>";
			$xml .= "<cell><![CDATA[&nbsp;]]></cell>";
			$xml .= "<cell><![CDATA[&nbsp;]]></cell>";
			$xml .= "<cell><![CDATA[&nbsp;]]></cell>";
			$xml .= "<cell><![CDATA[&nbsp;]]></cell>";
			$xml .= "<cell><![CDATA[&nbsp;]]></cell>";
			$xml .= "<cell><![CDATA[&nbsp;]]></cell>";
			$xml .= "<cell><![CDATA[&nbsp;]]></cell>";
			$xml .= "<cell><![CDATA[&nbsp;]]></cell>";
			$xml .= "<cell><![CDATA[&nbsp;]]></cell>";
			$xml .= "<cell><![CDATA[&nbsp;]]></cell>";
			$xml .= "<cell><![CDATA[&nbsp;]]></cell>";
			$xml .= "<cell><![CDATA[&nbsp;]]></cell>";
			$xml .= "<cell><![CDATA[&nbsp;]]></cell>";
			$xml .= "<cell><![CDATA[&nbsp;]]></cell>";
			$xml .= "<cell><![CDATA[&nbsp;]]></cell>";
			$xml .= "<cell><![CDATA[&nbsp;]]></cell>";
			$xml .= "<cell><![CDATA[&nbsp;]]></cell>";
			$xml .= "<cell><![CDATA[&nbsp;]]></cell>";
			$xml .= "<cell><![CDATA[&nbsp;]]></cell>";
			$xml .= "<cell><![CDATA[&nbsp;]]></cell>";
			$xml .= "<cell><![CDATA[".$dt['pos_biaya']."]]></cell>";
			$xml .= "<cell><![CDATA[<div style='text-align:right;'>".number_format($dt['nominal'],0,",",".")."</div>]]></cell>";
			$xml .= "<cell><![CDATA[".$dt['pph']."]]></cell>";
			$xml .= "<cell><![CDATA[<div style='text-align:right;'>".number_format($dt['nilaiPph'],0,",",".")."</div>]]></cell>";
			$xml .= "<cell><![CDATA[&nbsp;]]></cell>";
			$xml .= "<cell><![CDATA[&nbsp;]]></cell>";
			$xml .= "<cell><![CDATA[&nbsp;]]></cell>";
			$xml .= "<cell><![CDATA[&nbsp;]]></cell>";
			$xml .= "<cell><![CDATA[&nbsp;]]></cell>";
			$xml .= "<cell><![CDATA[&nbsp;]]></cell>";
			$xml .= "<cell><![CDATA[&nbsp;]]></cell>";
			$xml .= "<cell><![CDATA[&nbsp;]]></cell>";
			$xml .= "</row>";*/
		//}
       // $data=$dt['evo_id'];
		$no++;
	}
	$xml .= "<total>$totals</total>";
	$xml .= "</rows>";
	echo $xml;

	function nbsp($val){
		if ($val==' ' || $val=='') {
			$data = "-";
		} else {
			$data = $val;
		}
		return $data;
	}

	function note($nobukti,$level,$div,$dept){
		$lastVal = mssql_fetch_array(mssql_query("select level from DataEvoVal where nobukti = '".$nobukti."' and isnull(validasi,'')='' order by IdVal desc"));
		$user = mssql_fetch_array(mssql_query("select * from sys_user where IdUser = '".$_SESSION['UserID']."'"));
		
		if ($_SESSION['level']==$level and $lastVal['level']==$level and $div=='' and $dept=='') {
			$readonly  = ""; 
		} else if ($lastVal['level']=='DEPT. HEAD FINANCE / DIV. HEAD FAST' and $_SESSION['level']=='DEPT. HEAD' and $div=='FINANCE, ADMINISTRATION & SYSTEM TECHNOLOGY' and $dept=='FINANCE') {
			$readonly  = ""; 
		} else if ($lastVal['level']=='DEPT. HEAD FINANCE / DIV. HEAD FAST' and $_SESSION['level']=='DIV. HEAD' and $div=='FINANCE, ADMINISTRATION & SYSTEM TECHNOLOGY' and $dept=='all') {
			$readonly  = ""; 
		} else {
			$readonly  = "readonly"; 
		}
		
		if ($div=='' and $dept=='') {
			$note = mssql_fetch_array(mssql_query("select ketvalidasi as note from DataEvoVal where nobukti = '".$nobukti."' and level = '".$level."'"));
		} else if ($div!='' and $dept!='all') {
			$note = mssql_fetch_array(mssql_query("
				select ketvalidasi as note from DataEvoVal where nobukti = '".$nobukti."' and level = 'DEPT. HEAD FINANCE / DIV. HEAD FAST'
			"));
		} else {
			$note = mssql_fetch_array(mssql_query("
				select ketvalidasi as note from DataEvoVal where nobukti = '".$nobukti."' and level = 'DEPT. HEAD FINANCE / DIV. HEAD FAST'
			"));
		}
		return $note['note'];
	}

	// function note($nobukti,$level){
	// 	$note = mssql_fetch_array(mssql_query("select ketvalidasi from DataEvoVal where nobukti = '".$nobukti."'and level = '".$level."'"));
	// 	return $note['ketvalidasi'];
	// }

	function cekLunas($nobukti){
		include '../inc/conn.php';
		$dtAcc = mssql_fetch_array(mssql_query("select * from DataEvo where nobukti = '".$nobukti."'",$conns));
		$KodeDealer = $dtAcc['kodedealer'];
		$kodeForm = mssql_fetch_array(mssql_query("select kodedok from sys_kodeIso where kodedealer = '".$KodeDealer."'",$conns));
		include '../inc/koneksi.php';
		$pesan = "";
		if ($msg=='0') {
			$pesan = false; // "Gagal Koneksi Cabang!";
		} else if ($msg=='1') {
			$pesan = false; // "Gagal Koneksi HO!";
		} else if (!mssql_select_db("[$table]",$connCab)) {
			$pesan = false; // "Database tidak tersedia!";
		} else if ($msg=='3') {
			// $cek2 = mssql_num_rows(mssql_query("select kodeLgn from [$table]..aptrn where nofaktur = '".$nobukti."' and KodeLgn='".$dtAcc['kode_vendor']."'",$connCab));
			
			// $sql = "select sum(JumlahTrn) tot from [$table]..aptrn where nofaktur = '".$nobukti."' and KodeLgn='".$dtAcc['kode_vendor']."'";
			// $dt = mssql_fetch_array(mssql_query($sql,$connCab));
			// if ($cek2>0) {
			// 	if ($dt['tot']==0) {
			// 		$pesan = "LUNAS";
			// 	} else {
			// 		$pesan = "BELOM";
			// 	}
			// } else {
			// 	$pesan = "BELOM";
			// }

			$cek = mssql_fetch_array(mssql_query("
				select isnull(NoBukti,'') as NoBukti,delIndex from [$table]..aptrn where nofaktur = '".$nobukti."' and KodeLgn='".$dtAcc['kode_vendor']."' and typetrn = 'K'
			",$connCab));
			include '../inc/conn.php';
			mssql_query("
				update DataEvo set kode_form='".$kodeForm['kodedok']."',kode_voucher='".$cek['NoBukti']."',delIndex='".$cek['delIndex']."' 
				where nobukti = '".$nobukti."'
			",$conns);
			// if ($cek['NoBukti']!=' ' or $cek['NoBukti']!='') {
			// 	$pesan = "LUNAS";
			// } else {
			// 	$pesan = "BELOM";
			// }
		}
		return $pesan;
	}
?>