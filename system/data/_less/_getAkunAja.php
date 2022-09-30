<?php
	if(!isset($_SESSION)){
        session_start();
    }
    $UserID = $_SESSION['UserID'];
	$tipe = addslashes($_REQUEST['tipe']);
	$KodeDealer = addslashes($_REQUEST['KodeDealer']);
	$jenisAkun = addslashes($_REQUEST['jenisAkun']);
	include '../inc/conn.php';
	$akun = mssql_fetch_array(mssql_query("select * from settingAkun where id=1",$conns));
	global $conns;
	if ($jenisAkun=='HUTANG') {
		$debitStart = $akun['debitStart'];
		$debitEnd = $akun['debitEnd'];
	} else {
		$akunUser = mssql_fetch_array(mssql_query("select * from sys_user where IdUser='".$UserID."'",$conns));
		$debitStart = $akunUser['posAkunStart'];
		$debitEnd = $akunUser['posAkunEnd'];
	}
	$kreditStart = $akun['kreditStart'];
	$kreditEnd = $akun['kreditEnd'];

	include '../inc/koneksi.php';
	$ra = "RA".$kodecabang;
	$page = isset($_POST['page']) ? $_POST['page'] : 1;
	$rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
	$sortname = isset($_POST['sortname']) ? $_POST['sortname'] : 'G.KodeGl';
	$sortorder = isset($_POST['sortorder']) ? $_POST['sortorder'] : 'asc';
	$query = isset($_POST['query']) ? $_POST['query'] : false;
	$qtype = isset($_POST['qtype']) ? $_POST['qtype'] : false;

	$page = $_POST['page'];
	$rp = $_POST['rp'];
	$sortname = $_POST['sortname'];
	$sortorder = $_POST['sortorder'];

	if (!$sortname) $sortname = 'G.KodeGl';
	if (!$sortorder) $sortorder = 'asc';
	
	if ($query && $qtype) {
		$search = "and ".$_REQUEST['qtype']." like '%".$_REQUEST['query']."%'";
	}
	$sort = "ORDER BY $sortname $sortorder";
	
	if (!$page) $page = 1;
	if (!$rp) $rp = 10;

	$start = (($page-1) * $rp);
	
	if ($tipe=='debit') {
		// $sql = "select top $rp G.KodeGl,G.NamaGl from [$table]..glmst G 
		// 	left join [$ra]..ra b on G.KodeGl=b.KodeGl where G.KodeGl = G.KodeGl and G.KodeGl not in (
		// 	select top $start G.KodeGl from [$table]..glmst G 
		// 	left join [$ra]..ra b on G.KodeGl=b.KodeGl where G.KodeGl = G.KodeGl and Tahun='".$tahun."' and (ISNULL(Jan,0)!=0 or ISNULL(Feb,0)!=0 or ISNULL(Mar,0)!=0 or ISNULL(Apr,0)!=0 or ISNULL(Mei,0)!=0 or ISNULL(Jun,0)!=0 or ISNULL(Jul,0)!=0 or ISNULL(Ags,0)!=0 or ISNULL(Sep,0)!=0 or ISNULL(Okt,0)!=0 or ISNULL(Nov,0)!=0 or ISNULL(Dsm,0)!=0 or ISNULL(JulR,ISNULL(Jul,0))!=0 or ISNULL(AgsR,ISNULL(Ags,0))!=0 or ISNULL(SepR,ISNULL(Sep,0))!=0 or ISNULL(OktR,ISNULL(Okt,0))!=0 or ISNULL(NovR,ISNULL(Nov,0))!=0 or ISNULL(DsmR,ISNULL(Dsm,0))!=0) and KodeMaster not in ('M') and G.KodeGl between '".$debitStart."' and '".$debitEnd."' $search $sort
		// ) and Tahun='".$tahun."' and (ISNULL(Jan,0)!=0 or ISNULL(Feb,0)!=0 or ISNULL(Mar,0)!=0 or ISNULL(Apr,0)!=0 or ISNULL(Mei,0)!=0 or ISNULL(Jun,0)!=0 or ISNULL(Jul,0)!=0 or ISNULL(Ags,0)!=0 or ISNULL(Sep,0)!=0 or ISNULL(Okt,0)!=0 or ISNULL(Nov,0)!=0 or ISNULL(Dsm,0)!=0 or ISNULL(JulR,ISNULL(Jul,0))!=0 or ISNULL(AgsR,ISNULL(Ags,0))!=0 or ISNULL(SepR,ISNULL(Sep,0))!=0 or ISNULL(OktR,ISNULL(Okt,0))!=0 or ISNULL(NovR,ISNULL(Nov,0))!=0 or ISNULL(DsmR,ISNULL(Dsm,0))!=0) and KodeMaster not in ('M') and G.KodeGl between '".$debitStart."' and '".$debitEnd."' $search $sort";
		// $total = mssql_num_rows(mssql_query("select G.KodeGl from [$table]..glmst G left join [$ra]..ra b on G.KodeGl=b.KodeGl 
		// 	where G.KodeGl = G.KodeGl and Tahun='".$tahun."' and (ISNULL(Jan,0)!=0 or ISNULL(Feb,0)!=0 or ISNULL(Mar,0)!=0 or ISNULL(Apr,0)!=0 or ISNULL(Mei,0)!=0 or ISNULL(Jun,0)!=0 or ISNULL(Jul,0)!=0 or ISNULL(Ags,0)!=0 or ISNULL(Sep,0)!=0 or ISNULL(Okt,0)!=0 or ISNULL(Nov,0)!=0 or ISNULL(Dsm,0)!=0 or ISNULL(JulR,ISNULL(Jul,0))!=0 or ISNULL(AgsR,ISNULL(Ags,0))!=0 or ISNULL(SepR,ISNULL(Sep,0))!=0 or ISNULL(OktR,ISNULL(Okt,0))!=0 or ISNULL(NovR,ISNULL(Nov,0))!=0 or ISNULL(DsmR,ISNULL(Dsm,0))!=0) and KodeMaster not in ('M') and G.KodeGl between '".$debitStart."' and '".$debitEnd."' $search $sort"));
		$sql = "select top $rp KodeGl,NamaGl from [$table]..glmst G where KodeGl not in (
			select top $start KodeGl from [$table]..glmst G where KodeGl = KodeGl and KodeMaster not in ('M') and G.KodeGl between '".$debitStart."' and '".$debitEnd."' $search $sort
		) and KodeMaster not in ('M') and G.KodeGl between '".$debitStart."' and '".$debitEnd."' $search $sort";
		$total = mssql_num_rows(mssql_query("select KodeGl from [$table]..glmst G where KodeGl = KodeGl and KodeMaster not in ('M') and G.KodeGl between '".$debitStart."' and '".$debitEnd."' $search $sort"));
	} else {
		$sql = "select top $rp KodeGl,NamaGl from [$table]..glmst G where KodeGl not in (
			select top $start KodeGl from [$table]..glmst G where KodeGl = KodeGl and KodeMaster not in ('M') and G.KodeGl between '".$kreditStart."' and '".$kreditEnd."' $search $sort
		) and KodeMaster not in ('M') and G.KodeGl between '".$kreditStart."' and '".$kreditEnd."' $search $sort";
		$total = mssql_num_rows(mssql_query("select KodeGl from [$table]..glmst G where KodeGl = KodeGl and KodeMaster not in ('M') and G.KodeGl between '".$kreditStart."' and '".$kreditEnd."' $search $sort"));
	}

	$result = mssql_query($sql);
	$rows = array();
	while ($row = mssql_fetch_array($result)) {
		$rows[] = $row;
	}

	header("Content-type: text/xml");
	$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
	$xml .= "<rows>";
	$xml .= "<page>$page</page>";
	$xml .= "<total>$total</total>";
	foreach($rows as $row_jenis) {
		$xml .= "<row id='".$row_jenis['KodeGl']."'>";
		$xml .= "<cell><![CDATA[<input type='checkbox' id='chk-1' name='id[]' value='".$row_jenis['KodeGl']."#".$row_jenis['NamaGl']."'>]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['KodeGl'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['NamaGl'])."]]></cell>";
		$xml .= "</row>";
	}
	$xml .= "</rows>";
	echo $xml;
?>