<?php
	$KodeDealer = addslashes($_REQUEST['KodeDealer']);
	include '../inc/koneksi.php';
	$page = isset($_POST['page']) ? $_POST['page'] : 1;
	$rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
	$sortname = isset($_POST['sortname']) ? $_POST['sortname'] : 'KodeBank';
	$sortorder = isset($_POST['sortorder']) ? $_POST['sortorder'] : 'asc';
	$query = isset($_POST['query']) ? $_POST['query'] : false;
	$qtype = isset($_POST['qtype']) ? $_POST['qtype'] : false;

	$page = $_POST['page'];
	$rp = $_POST['rp'];
	$sortname = $_POST['sortname'];
	$sortorder = $_POST['sortorder'];

	if (!$sortname) $sortname = 'KodeBank';
	if (!$sortorder) $sortorder = 'asc';
	
	if ($query && $qtype) {
		$search = "and ".$_REQUEST['qtype']." like '%".$_REQUEST['query']."%'";
	}
	$sort = "ORDER BY $sortname $sortorder";
	
	if (!$page) $page = 1;
	if (!$rp) $rp = 10;

	$start = (($page-1) * $rp);
	
	$sql = "
		select top $rp KodeBank,NamaBank,NoRekening from [$table]..CLmst where KodeBank not in (
			select top $start KodeBank from [$table]..CLmst where KodeBank=KodeBank $search $sort
		) and KodeBank=KodeBank $search $sort
	";
	// echo $sql;
	$result = mssql_query($sql);
	$total = mssql_num_rows(mssql_query("select KodeBank from [$table]..CLmst where KodeBank=KodeBank $search"));
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
		$i = 1;
		foreach($rows as $row_jenis) {
			$id = $row_jenis['KodeBank']."#".$row_jenis['NamaBank']."#".$row_jenis['NoRekening'];
			$xml .= "<row id='".$row_jenis['KodeBank']."'>";
			$xml .= "<cell><![CDATA[<input type='checkbox' id='chk-".$i."' name='id[]' value='".$id."'>]]></cell>";
			$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['KodeBank'])."]]></cell>";
			$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['NamaBank'])."]]></cell>";
			$xml .= "<cell><![CDATA[".utf8_encode(trim($row_jenis['NoRekening']))."]]></cell>";
			$xml .= "</row>";
			$i++;
		}
	}
	$xml .= "</rows>";
	echo $xml;
?>