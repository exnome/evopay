<?php
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
	$vendor = isset($_REQUEST['vendor']) ? $_REQUEST['vendor'] : null;
	$nofj = isset($_REQUEST['no_fj']) ? $_REQUEST['no_fj'] : null;
	$startDate = isset($_REQUEST['startDate']) ? $_REQUEST['startDate'] : null;
	$endDate = isset($_REQUEST['endDate']) ? $_REQUEST['endDate'] : null;
	$sort = "ORDER BY $sortname $sortorder";

	if (!$page) $page = 1;
	if (!$rp) $rp = 10;

	$start = (($page-1) * $rp);
	
	$user = mssql_fetch_array(mssql_query("select * from sys_user where IdUser = '".$IdUser."'"));
	if ($user['KodeDealer']=='all') { $area = ""; } else { $area = "and a.kodedealer = '".$user['KodeDealer']."'"; }
	if ($user['divisi']=='all') { $divisi = ""; } else { $divisi = "and divisi = '".$user['divisi']."'"; }

	if ($nobukti!='') { $no_bukti = "and nobukti like '%".$nobukti."%'"; } else { $no_bukti = ""; }
	if ($vendor!='') { $ven_dor = "and (kode_vendor like '%".$vendor."%' or namaVendor like '%".$vendor."%')"; } else { $ven_dor = ""; }
	if ($nofj!='') { $no_fj = "and no_fj like '%".$nofj."%'"; } else { $no_fj = ""; }
	if ($startDate!='' and $endDate!='') { $tgl = "and tgl_pengajuan between '".$startDate."' and '".$endDate."'"; } else { $tgl = ""; }

	$sql = "
		select top $rp evo_id,nobukti,a.kodedealer,(a.kodedealer+' | '+NamaDealer) as dealer,tipe,status,tgl_pengajuan,upload_file,upload_fp,kode_vendor,metode_bayar,benificary_account,tgl_bayar,nama_bank,nama_pemilik,email_penerima,nama_alias,nama_bank_pengirim,tf_from_account,realisasi_nominal,dpp,ppn,
		(select sum(nilai_pph) from DataEvoPos where nobukti = a.nobukti) as totpph,
		case when tipe='HUTANG' then htg_stl_pajak else biaya_yg_dibyar end as total,a.npwp,no_fj,keterangan  
		from DataEvo a 
		inner join SPK00..dodealer b on a.kodedealer=b.kodedealer
		where evo_id not in (
			select top $start evo_id from DataEvo a 
			inner join SPK00..dodealer b on a.kodedealer=b.kodedealer
			where evo_id = evo_id and userentry='".$IdUser."' $area $divisi $no_bukti $ven_dor $no_fj $tgl $sort
		) and userentry='".$IdUser."' $area $divisi $no_bukti $ven_dor $no_fj $tgl $sort
	";
	$result = mssql_query($sql);
	$total = mssql_num_rows(mssql_query("
		select evo_id from DataEvo a inner join SPK00..dodealer b on a.kodedealer=b.kodedealer
		where evo_id = evo_id and userentry='".$IdUser."' $area $divisi $no_bukti $ven_dor $no_fj $tgl
	"));
	$rows = array();
	while ($row = mssql_fetch_array($result)) {
		$rows[] = $row;
	}

	header("Content-type: text/xml");
	$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
	$xml .= "<rows>";
	$xml .= "<page>$page</page>";
	$xml .= "<total>$total</total>";
	foreach($rows as $row) {
		$cek = mssql_num_rows(mssql_query("select idVal from DataEvoVal where nobukti='".$row['nobukti']."' and ISNULL(validasi, '')!=''"));
		if ($cek>0) { $dis = "disabled"; } else { $dis = ""; }
		$xml .= "<row id='".$row['evo_id']."'>";
		$xml .= "<cell><![CDATA[<input type='checkbox' id='chk-1' name='id[]' value='".$row['evo_id']."' ".$dis.">]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row['dealer'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row['tipe'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row['status'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row['nobukti'])."]]></cell>";
		$xml .= "<cell><![CDATA[".datenull($row['tgl_pengajuan'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row['upload_file'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row['upload_fp'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row['kode_vendor'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row['metode_bayar'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row['benificary_account'])."]]></cell>";
		$xml .= "<cell><![CDATA[".datenull($row['tgl_bayar'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row['nama_bank'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row['nama_pemilik'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row['email_penerima'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row['nama_alias'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row['nama_bank_pengirim'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row['tf_from_account'])."]]></cell>";
		$xml .= "<cell><![CDATA[<div style='text-align:right'>".number_format($row['realisasi_nominal'],0,",",".")."</div>]]></cell>";
		$xml .= "<cell><![CDATA[<div style='text-align:right'>".number_format($row['dpp'],0,",",".")."</div>]]></cell>";
		$xml .= "<cell><![CDATA[<div style='text-align:right'>".number_format($row['ppn'],0,",",".")."</div>]]></cell>";
		$xml .= "<cell><![CDATA[<div style='text-align:right'>".number_format($row['totpph'],0,",",".")."</div>]]></cell>";
		$xml .= "<cell><![CDATA[<div style='text-align:right'>".number_format($row['total'],0,",",".")."</div>]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row['npwp'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row['no_fj'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row['keterangan'])."]]></cell>";
		$xml .= "</row>";
	}
	$xml .= "</rows>";
	echo $xml;
?>