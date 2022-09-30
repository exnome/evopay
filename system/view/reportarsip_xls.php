<?php
	require_once ('../inc/conn.php');
		
	/** Include path **/
	ini_set('include_path', ini_get('include_path').';phpexcel/');
	
	/** PHPExcel */
	require_once '../../assets/phpexcel/PHPExcel.php';
	require_once('../../assets/phpexcel/PHPExcel/IOFactory.php');
	
	/** PHPExcel_Writer_Excel2007 */
	include '../../assets/phpexcel/PHPExcel/Writer/Excel2007.php';	
	require_once '../../assets/phpexcel/PHPExcel/Cell/AdvancedValueBinder.php';
	
	set_time_limit(0);
	ini_set('memory_limit', '512M');
	
	

	$IdUser = isset($_REQUEST['IdUser']) ? $_REQUEST['IdUser'] : null;
	$nobukti = isset($_REQUEST['nobukti']) ? $_REQUEST['nobukti'] : null;
	$startDate = isset($_REQUEST['startDate']) ? $_REQUEST['startDate'] : null;
	$endDate = isset($_REQUEST['endDate']) ? $_REQUEST['endDate'] : null;
	$validasi = isset($_REQUEST['validasi']) ? $_REQUEST['validasi'] : null;
	$statCsv = isset($_REQUEST['statCsv']) ? $_REQUEST['statCsv'] : null;
	$statBayar = isset($_REQUEST['statBayar']) ? $_REQUEST['statBayar'] : null;
	$noCsv = isset($_REQUEST['noCsv']) ? $_REQUEST['noCsv'] : null;
	$txtSearch = isset($_REQUEST['txtSearch']) ? $_REQUEST['txtSearch'] : null;
	$divisi = isset($_REQUEST['divisi']) ? $_REQUEST['divisi'] : null;
	$department = isset($_REQUEST['department']) ? $_REQUEST['department'] : null;
	$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;

	if ($id!='') {
		$s_id = "and evo_id in (".$id.")";
	} else {
		$s_id = "";
	}
	
	if ($nobukti!='') {
		$s_nobukti = "and nobukti like '%".$nobukti."%'";
	} else {
		$s_nobukti = "";
	}

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


	if ($txtSearch!='') {
		$s_txtSearch = "and (noCsv like '%".$txtSearch."%' or kodedealer like '%".$txtSearch."%' or userAju like '%".$txtSearch."%' or tipe like '%".$txtSearch."%' or tgl_pengajuan like '%".$txtSearch."%' or nobukti like '%".$txtSearch."%' or metode_bayar like '%".$txtSearch."%' or kode_vendor like '%".$txtSearch."%' or namaVendor like '%".$txtSearch."%' or benificary_account like '%".$txtSearch."%' or nama_bank like '%".$txtSearch."%' or nama_pemilik like '%".$txtSearch."%' or nama_alias like '%".$txtSearch."%' or tgl_bayar like '%".$txtSearch."%' or email_penerima like '%".$txtSearch."%' or nama_bank_pengirim like '%".$txtSearch."%' or tf_from_account like '%".$txtSearch."%')";
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
	
	/*$user = mssql_fetch_array(mssql_query("select * from sys_user where IdUser = '".$IdUser."'"));
	if ($user['tipe']=='ADMIN') { 
		$s_admin = "and pengaju = '".$IdUser."'"; 
	} else { 
		$s_admin = ""; 
	}
	
	if ($user['tipe']=='SECTION HEAD' or $user['tipe']=='ADH') { 
		$s_section = "and sect = '".$IdUser."'"; 
	} else { 
		$s_section = ""; 
	}
	
	if ($user['tipe']=='DEPT. HEAD' or $user['tipe']=='KEPALA CABANG') { 
		if ($user['divisi']=='FINANCE and ACCOUNTING' and $user['department']=='FINANCE') {
			$s_dept = "and (ISNULL(level, '')='DEPT. HEAD FINANCE / DIV. HEAD FAST' or dept = '".$IdUser."')"; 
		} else {
			$s_dept = "and dept = '".$IdUser."'"; 
		}
	} else { 
		$s_dept = ""; 
	}
	if ($user['tipe']=='DIV. HEAD') { 
		if ($user['divisi']=='FINANCE and ACCOUNTING' and ($user['department']=='FINANCE' or $user['department']=='')) {
			$s_div = "and (ISNULL(level, '')='DEPT. HEAD FINANCE / DIV. HEAD FAST' or div = '".$IdUser."')"; 
		} else {
			$s_div = "and div = '".$IdUser."'"; 
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
	
	/* No Evopay	
		User Aju	
		Status	
		Tgl Aju	
		Tgl Validasi	
		Lunas/Blm	
		No Pembayaran	
		Tgl Bayar	
		Metode Byr	
		Tipe Aju	
		No Tagihan	
		Kode Vendor	Nama Vendor	Beneficary Account	
		Nama Bank Penerima	Nama Pemilik	Nama Alias	Email Penerima	Nama Bank Pengirim	Transfer From Account	Real. Nominal	
		NPWP	No Faktur	Dpp	Ppn	Pos Biaya	Nominal	Pph	Nilai Pph	Total Bayar	
		Keterangan	Stat Validator	Accept/Reject	
		Note Section Head	Note Dept. Head	Note Div. Head	Note Direksi	Note Dept. Head Finance	Note Div. Head FAST	
		Status CSV	Department	Dealer
		*/
		
	$objPHPExcel = new PHPExcel();
	
	$objPHPExcel->getProperties()->setCreator("Andex Teddy / exnome@gmail.com");
	$objPHPExcel->getProperties()->setLastModifiedBy("Andex Teddy / exnome@gmail.com");
	$objPHPExcel->getProperties()->setTitle("Report Arsip Evopay");
	$objPHPExcel->getProperties()->setSubject("Report Arsip Evopay");
	$objPHPExcel->getProperties()->setDescription("Report Arsip Evopay");
	
	$active_sheet = 0;				
	$rowscounter = 1;
	$a=0;
	$b=0;
	
	$objPHPExcel->setActiveSheetIndex($active_sheet);
	$objPHPExcel->getActiveSheet()->setTitle('Report Arsip Evopay');
	
	$i=0;
	$j=1;
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'No');
	$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'No Evo Pay');
	$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'User Aju');
	$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Stat. Aju');
	$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
	
		
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Tgl Aju');
	$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Tgl Tagihan');
	$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Lunas/Blm');
	$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Voucher Lunas');
	$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Tgl Bayar');
	$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Metode Byr');
	$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Tipe Aju');
	$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'No Tagihan');
	$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
					
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Kode Vendor');
	$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Nama Vendor');
	$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Beneficary Account');
	$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Nama Bank Penerima');
	$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Nama Rekening');
	$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Nama Alias');
	$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Email Penerima');
	$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Nama Bank Pengirim');
	$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Transfer From Account');
	$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Real. Nominal');
	$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
					
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'NPWP');
	$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'No Faktur');
	$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Dpp');
	$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Ppn');
	$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Pos Biaya');
	$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Nominal');
	$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Pph');
	$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Nilai Pph');
	$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Total Bayar');
	$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
					
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Keterangan');
	$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Stat Validator');
	$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Accept/Reject');
	$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
	
	if ($user['KodeDealer']=='2010') {
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Note Section Head');
		$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Note Dept. Head');
		$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Note Div. Head');
		$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Note Direksi');
		$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Note Dept. Head Finance');
		$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Note Div. Head FAST');
		$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
		
	} else {
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Note Sect Head');
		$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Note Adh');
		$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Note BM');
		$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Note OM');
		$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;

	}
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Status CSV');
	$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Department');
	$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Dealer');
	$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
	
	$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);	
	$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
	$objPHPExcel->getActiveSheet()->duplicateStyle( $objPHPExcel->getActiveSheet()->getStyle('A1'), 'B1:AQ1' );	
	
					
	$sql = "
		select * from (
			select evo_id,a.kodedealer,a.divisi,department,a.IdAtasan,NamaDealer,userentry as userAju,a.tipe,tgl_pengajuan,a.nobukti,metode_bayar,kode_vendor,
			namaVendor,benificary_account,nama_bank,nama_pemilik,nama_alias,tgl_bayar,email_penerima,no_fj,
			nama_bank_pengirim,tf_from_account,realisasi_nominal,a.npwp,dpp,ppn,pos_biaya,nominal,
			case when ISNULL(jns_pph, '')!='' then jns_pph+' ('+convert(varchar,tarif_persen)+'%)' else '' end as pph, nilai_pph as nilaiPph,
			case when a.tipe='HUTANG' then htg_stl_pajak else biaya_yg_dibyar end as totBayar,keterangan,
			(select top 1 case when ISNULL(validasi, '')='' then '0' when ISNULL(validasi, '')='Accept' then '1' else '2' end as validasi
			from DataEvoVal where nobukti=a.nobukti order by tglentry desc) as validasi,noCsv,metode_transfer,isnull(kode_voucher,' ') as kode_voucher
			from DataEvo a
			left join DataEvoPos b on a.nobukti=b.nobukti
			inner join spk00..dodealer c on a.kodedealer=c.kodedealer
			left join DataEvoTransfer d on a.nobukti=d.nobukti
			inner join sys_user e on a.userentry=e.IdUser
			where isnull(d.is_del,'')='' and a.nobukti in (select DISTINCT nobukti from(select a.nobukti,userentry as pengaju,IdAtasan as sect,
			(select IdAtasan from sys_user where IdUser=a.IdAtasan) as dept,
			(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.IdAtasan)) as div,level 
			from DataEvo a inner join DataEvoVal b on a.nobukti=b.nobukti) x 
			where nobukti=nobukti $s_admin $s_section $s_dept $s_div $s_kasir)
		) x where evo_id=evo_id $s_id $s_nobukti $s_tglAju $s_validasi $s_statBayar $s_divisi $s_department $s_txtSearch
	";
	
	// echo "<pre>".$sql."</pre>";
	$rsl = mssql_query($sql,$conns);
	$no=1; $data = "";
	
	$x=0;								
	while ($dt = mssql_fetch_array($rsl)) {
		$i=0;
		$j++;
		$x++;
		
		$val = mssql_fetch_array(mssql_query("
			select top 1 case when ISNULL(validasi, '')='' then 'PROSES ' + level else validasi+' '+level end as stataju,ketvalidasi,
			tglValidasi,level as statVal,case when ISNULL(validasi, '')='' then 'BELOM PROSES ' + level else validasi end as validasi
			from DataEvoVal where nobukti='".$dt['nobukti']."' and isnull(level,'')!='' order by tglentry desc
		",$conns));

		$sTagihan = "select NoFaktur,TglTrnFaktur from DataEvoTagihan where nobukti = '".$dt['nobukti']."'";
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
		
		/* No Evopay	
			User Aju	
			Status	
			Tgl Aju	
			Tgl Validasi	
			Lunas/Blm	
			No Pembayaran	
			Tgl Bayar	
			Metode Byr	
			Tipe Aju	
			No Tagihan	
			Kode Vendor	Nama Vendor	Beneficary Account	
			Nama Bank Penerima	Nama Pemilik	Nama Alias	Email Penerima	Nama Bank Pengirim	Transfer From Account	Real. Nominal	
			NPWP	No Faktur	Dpp	Ppn	Pos Biaya	Nominal	Pph	Nilai Pph	Total Bayar	
			Keterangan	Stat Validator	Accept/Reject	
			Note Section Head	Note Dept. Head	Note Div. Head	Note Direksi	Note Dept. Head Finance	Note Div. Head FAST	
			Status CSV	Department	Dealer
			*/			
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j,$no);
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true); 	$i++;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j,nbsp($dt['nobukti']));
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true); 	$i++;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j,nbsp($dt['userAju']));
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true); 	$i++;							
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j,nbsp(strtoupper($stataju)));
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true); 	$i++;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j,datenull($dt['tgl_pengajuan']));
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true); 	$i++;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j,nbsp($tglfaktur));
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true); 	$i++;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j,nbsp($ketLunas));
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true); 	$i++;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j,nbsp($dt['kode_voucher']));
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true); 	$i++;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j,datenull($dt['tgl_bayar']));
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true); 	$i++;							
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j,nbsp($dt['metode_bayar']));
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true); 	$i++;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j,nbsp($dt['tipe']));
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true); 	$i++;							
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j,nbsp($tagihan));
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true); 	$i++;
					
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j,nbsp($dt['kode_vendor']));
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true); 	$i++;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j,nbsp($dt['namaVendor']));
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true); 	$i++;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j,$dt['benificary_account']);
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true); 	$i++;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j,nbsp($dt['nama_bank']));
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true); 	$i++;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j,nbsp($dt['nama_pemilik']));
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true); 	$i++;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j,nbsp($dt['nama_alias']));
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true); 	$i++;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j,nbsp($dt['email_penerima']));
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true); 	$i++;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j,nbsp($dt['nama_bank_pengirim']));
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true); 	$i++;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j,nbsp($dt['tf_from_account']));
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true); 	$i++;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j,$dt['realisasi_nominal']);
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true); 	$i++;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j,nbsp($dt['npwp']));
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true); 	$i++;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j,nbsp($dt['no_fj']));
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true); 	$i++;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j,$dt['dpp']);
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true); 	$i++;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j,$dt['ppn']);
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true); 	$i++;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j,nbsp($dt['pos_biaya']));
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true); 	$i++;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j,$dt['nominal']);
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true); 	$i++;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j,nbsp($dt['pph']));
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true); 	$i++;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j,$dt['nilaiPph']);
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true); 	$i++;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j,$dt['totBayar']);
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true); 	$i++;
					
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j,nbsp($dt['keterangan']));
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true); 	$i++;							
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j,nbsp($val['statVal']));
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true); 	$i++;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j,nbsp($val['validasi']));
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true); 	$i++;
					
			if ($dt['kodedealer']=='2010') {
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j,nbsp(note($dt['nobukti'],'SECTION HEAD','','')));
				$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true); 	$i++;
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j,nbsp(note($dt['nobukti'],'DEPT. HEAD','','')));
				$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true); 	$i++;
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j,nbsp(note($dt['nobukti'],'DIV. HEAD','','')));
				$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true); 	$i++;
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j,nbsp(note($dt['nobukti'],'DIREKSI','','')));
				$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true); 	$i++;
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j,nbsp(note($dt['nobukti'],'DEPT. HEAD','FINANCE and ACCOUNTING','FINANCE')));
				$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true); 	$i++;
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j,nbsp(note($dt['nobukti'],'DIV. HEAD','FINANCE and ACCOUNTING','all')));
				$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true); 	$i++;
			} else {
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j,nbsp(note($dt['nobukti'],'SECTION HEAD')));
				$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true); 	$i++;
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j,nbsp(note($dt['nobukti'],'ADH')));
				$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true); 	$i++;
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j,nbsp(note($dt['nobukti'],'KEPALA CABANG')));
				$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true); 	$i++;
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j,nbsp(note($dt['nobukti'],'OM')));
				$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true); 	$i++;
			}
					
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j,nbsp($csv['noCsv']));
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true); 	$i++;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j,nbsp($dt['department']));
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true); 	$i++;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j,nbsp($dt['kodedealer']).' | '.$dt['NamaDealer']);
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true); 	$i++;
		
			$objPHPExcel->getActiveSheet()->getStyle('V'.$j)->getNumberFormat()->setFormatCode('#,##0.00');
			$objPHPExcel->getActiveSheet()->getStyle('Y'.$j)->getNumberFormat()->setFormatCode('#,##0.00');
			$objPHPExcel->getActiveSheet()->getStyle('Z'.$j)->getNumberFormat()->setFormatCode('#,##0.00');
			$objPHPExcel->getActiveSheet()->getStyle('AB'.$j)->getNumberFormat()->setFormatCode('#,##0.00');
			$objPHPExcel->getActiveSheet()->getStyle('AD'.$j)->getNumberFormat()->setFormatCode('#,##0.00');
			$objPHPExcel->getActiveSheet()->getStyle('AE'.$j)->getNumberFormat()->setFormatCode('#,##0.00');
			
		$no++;
	}
	
	
	
	$styleArray = array( 'borders' => array( 'allborders' => array( 
							'style' => PHPExcel_Style_Border::BORDER_THIN,
							'color' => array('argb' => '00000000'), ), ), );
	
	$objPHPExcel->getActiveSheet()->getStyle('A1:AQ'.$j)->applyFromArray($styleArray);
	

	$tgl = date("Ymd_His");
	$filename = "report_arsip_".$tgl;
		
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
	header('Cache-Control: max-age=0');
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007'); application/vnd.openxmlformats-officedocument.spreadsheetml.sheet
	$objWriter->save('php://output');
	
	
	exit;
	
	

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
		} else if ($lastVal['level']=='DEPT. HEAD FINANCE / DIV. HEAD FAST' and $_SESSION['level']=='DEPT. HEAD' and $div=='FINANCE and ACCOUNTING' and $dept=='FINANCE') {
			$readonly  = ""; 
		} else if ($lastVal['level']=='DEPT. HEAD FINANCE / DIV. HEAD FAST' and $_SESSION['level']=='DIV. HEAD' and $div=='FINANCE and ACCOUNTING' and $dept=='all') {
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
				select ketvalidasi2 as note from DataEvoVal where nobukti = '".$nobukti."' and level = 'DEPT. HEAD FINANCE / DIV. HEAD FAST'
			"));
		}
		return $note['note'];
	}

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