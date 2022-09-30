<?php
	$page = isset($_POST['page']) ? $_POST['page'] : 1;
	$rp = isset($_POST['rp']) ? $_POST['rp'] : 20;
	$sortname = isset($_POST['sortname']) ? $_POST['sortname'] : 'kombinasi_nama';
	$sortorder = isset($_POST['sortorder']) ? $_POST['sortorder'] : 'asc';
	$query = isset($_POST['query']) ? $_POST['query'] : false;
	$qtype = isset($_POST['qtype']) ? $_POST['qtype'] : false;

	require_once ('../inc/conn.php');

	$page = $_POST['page'];
	$rp = $_POST['rp'];
	$sortname = $_POST['sortname'];
	$sortorder = $_POST['sortorder'];
	
	if (!$sortname) $sortname = 'kombinasi_nama';
	if (!$sortorder) $sortorder = 'asc';
	$kode = isset($_REQUEST['kode']) ? $_REQUEST['kode'] : null;
	$sort = "ORDER BY $sortname $sortorder";

	if (!$page) $page = 1;
	if (!$rp) $rp = 10;

	$start = (($page-1) * $rp);

	$sql = "select top $rp id_mcm,case when '1'='".$kode."' then kode_kliring else kode_rtgs end as kode,
			kombinasi_nama,nama_bank from sys_mcm a 
			where id_mcm not in (
				select top $start id_mcm from sys_mcm a 
				where id_mcm=id_mcm $sort
			) $sort";
	$result = mssql_query($sql);
	$total = mssql_num_rows(mssql_query("select id_mcm from sys_mcm where id_mcm=id_mcm $sort"));
	$rows = array();
	while ($row = mssql_fetch_array($result)) {
		$rows[] = $row;
	}
	
	header("Content-type: text/xml");
	$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
	$xml .= "<rows>";
	$xml .= "<page>$page</page>";
	$no=1;
	foreach($rows as $row) {
		if ($no %2 == 0) { $bg = "background:#eaeaea;"; } else { $bg = ""; }
		$xml .= "<row id='".$no."'>";
		$xml .= "<cell><![CDATA[<div style='padding:2px;$bg'><input type='checkbox' name='id[]' value='".$row['kode']."'></div>]]></cell>";
		$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>".utf8_encode($row['kode'])."</div>]]></cell>";
		$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>".utf8_encode($row['nama_bank'])."</div>]]></cell>";
		$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>".utf8_encode($row['kombinasi_nama'])."</div>]]></cell>";
		$xml .= "</row>";
		$no++;
	}
	$xml .= "<total>".$total."</total>";	
	$xml .= "</rows>";
	echo $xml;
?>