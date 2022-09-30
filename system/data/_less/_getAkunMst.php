<?php
	$KodeDealer = addslashes($_REQUEST['KodeDealer']);
	include '../inc/koneksi.php';
	$page = isset($_POST['page']) ? $_POST['page'] : 1;
	$rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
	$sortname = isset($_POST['sortname']) ? $_POST['sortname'] : 'KodeGl';
	$sortorder = isset($_POST['sortorder']) ? $_POST['sortorder'] : 'asc';
	$query = isset($_POST['query']) ? $_POST['query'] : false;
	$qtype = isset($_POST['qtype']) ? $_POST['qtype'] : false;

	$page = $_POST['page'];
	$rp = $_POST['rp'];
	$sortname = $_POST['sortname'];
	$sortorder = $_POST['sortorder'];

	if (!$sortname) $sortname = 'KodeGl';
	if (!$sortorder) $sortorder = 'asc';
	
	if ($query && $qtype) {
		$search = "and ".$_REQUEST['qtype']." like '%".$_REQUEST['query']."%'";
	}
	$sort = "ORDER BY $sortname $sortorder";

	if (!$page) $page = 1;
	if (!$rp) $rp = 10;

	$start = (($page-1) * $rp);
	
	$sql = "select top $rp KodeGl,NamaGl from [$table]..glmst where KodeGl not in (
		select top $start KodeGl from [$table]..glmst where KodeGl = KodeGl $search $sort
	) $search $sort";
	$total = mssql_num_rows(mssql_query("select KodeGl from [$table]..glmst where KodeGl = KodeGl $search $sort"));
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
		$xml .= "<cell><![CDATA[<input type='checkbox' id='chk-1' name='id[]' value='".$row_jenis['KodeGl']."'>]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['KodeGl'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['NamaGl'])."]]></cell>";
		$xml .= "</row>";
	}
	$xml .= "</rows>";
	echo $xml;
?>