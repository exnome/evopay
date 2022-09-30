<?php
	$page = isset($_POST['page']) ? $_POST['page'] : 1;
	$rp = isset($_POST['rp']) ? $_POST['rp'] : 20;
	$sortname = isset($_POST['sortname']) ? $_POST['sortname'] : 'IdUser';
	$sortorder = isset($_POST['sortorder']) ? $_POST['sortorder'] : 'asc';
	$query = isset($_POST['query']) ? $_POST['query'] : false;
	$qtype = isset($_POST['qtype']) ? $_POST['qtype'] : false;

	require_once ('../inc/conn.php');

	$page = $_POST['page'];
	$rp = $_POST['rp'];
	$sortname = $_POST['sortname'];
	$sortorder = $_POST['sortorder'];
	
	if (!$sortname) $sortname = 'IdUser';
	if (!$sortorder) $sortorder = 'asc';
	$namaUser = isset($_REQUEST['namaUser']) ? $_REQUEST['namaUser'] : null;
	$tipe = isset($_REQUEST['tipe']) ? $_REQUEST['tipe'] : null;
	$sort = "ORDER BY $sortname $sortorder";

	if ($namaUser!='') {
		$namaUser = "and namaUser like '%".$namaUser."%'";
	} else {
		$namaUser="";
	}

	if ($tipe!='') {
		$tipe = "and tipe like '%".$tipe."%'";
	} else {
		$tipe="";
	}
		

	if (!$page) $page = 1;
	if (!$rp) $rp = 10;

	$start = (($page-1) * $rp);

	$sql = "select top $rp IdUser,namaUser,a.tipe,Email,nik,no_tlp,NamaDealer,divisi,department,IdAtasan from sys_user a 
			left join spk00..dodealer b on a.kodedealer=b.kodedealer
			where IdUser not in (
				select top $start IdUser from sys_user a 
				left join spk00..dodealer b on a.kodedealer=b.kodedealer
				where IdUser=IdUser and isnull(isDel,0)<>1 $namaUser $tipe $sort
			) and isnull(isDel,0)<>1 $namaUser $tipe $sort";
	$result = mssql_query($sql);
	$total = mssql_num_rows(mssql_query("select IdUser from sys_user a 
		left join spk00..dodealer b on a.kodedealer=b.kodedealer
		where IdUser=IdUser and isnull(isDel,0)<>1  $namaUser $tipe $sort"));
	$rows = array();
	while ($row = mssql_fetch_array($result)) {
		$rows[] = $row;
	}
	
	header("Content-type: text/xml");
	$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
	$xml .= "<rows>";
	$xml .= "<page>$page</page>";
	foreach($rows as $row) {
		if ($row['IdAtasan']!='all') {
			$nama = mssql_fetch_array(mssql_query("select namaUser from sys_user where IdUser = '".$row['IdAtasan']."'"));
			$atasan = $nama['namaUser'];
		} else {
			$atasan = "ALL";
		}
		$xml .= "<row id='".$row['IdUser']."'>";
		$xml .= "<cell><![CDATA[<input type='checkbox' id='chk-1' name='id[]' value='".$row['IdUser']."'>]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row['IdUser'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row['namaUser'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row['Email'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row['nik'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row['no_tlp'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row['NamaDealer'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row['tipe'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row['divisi'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row['department'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($atasan)."]]></cell>";
		$xml .= "</row>";
	}
	$xml .= "<total>".$total."</total>";	
	$xml .= "</rows>";
	echo $xml;
?>