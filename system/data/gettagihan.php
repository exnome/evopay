<?php
	$nobukti = addslashes($_REQUEST['nobukti']);
	
	include '../inc/conn.php';
	$page = isset($_POST['page']) ? $_POST['page'] : 1;
	$rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
	$sortname = isset($_POST['sortname']) ? $_POST['sortname'] : 'NoFaktur';
	$sortorder = isset($_POST['sortorder']) ? $_POST['sortorder'] : 'asc';
	$query = isset($_POST['query']) ? $_POST['query'] : false;
	$qtype = isset($_POST['qtype']) ? $_POST['qtype'] : false;

	$page = $_POST['page'];
	$rp = $_POST['rp'];
	$sortname = $_POST['sortname'];
	$sortorder = $_POST['sortorder'];

	if (!$sortname) $sortname = 'NoFaktur';
	if (!$sortorder) $sortorder = 'asc';
	
	if ($query && $qtype) {
		$search = "and ".$_REQUEST['qtype']." like '%".$_REQUEST['query']."%'";
	} else {
		$search = "";
	}

	$sort = "ORDER BY $sortname $sortorder";
	
	if (!$page) $page = 1;
	if (!$rp) $rp = 10;

	$start = (($page-1) * $rp);

	$sql = "select * from DataEvoTagihan where nobukti = 'VP".$nobukti."'";
	$total = mssql_num_rows(mssql_query($sql));
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
	if (is_array($rows) || is_object($rows)) {
		$no=1;
		foreach($rows as $dt) {
			$xml .= "<row id='".$no."'>";
			$xml .= "<cell><![CDATA[".utf8_encode($dt['NoFaktur'])."]]></cell>";
			$xml .= "<cell><![CDATA[".datenull($dt['TglTrnFaktur'])."]]></cell>";
			$xml .= "<cell><![CDATA[".datenull($dt['TglJthTmp'])."]]></cell>";
			$xml .= "<cell><![CDATA[".utf8_encode($dt['Keterangan'])."]]></cell>";
			$xml .= "<cell><![CDATA[<div style='text-align: right;padding:0'>".number_format($dt['JumlahTrn'],0,",",".")."</div>]]></cell>";
			$xml .= "</row>";
			$no++;
		}
	}
	$xml .= "</rows>";
	echo $xml;
?>