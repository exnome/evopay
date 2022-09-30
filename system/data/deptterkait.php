<?php
	$page = isset($_POST['page']) ? $_POST['page'] : 1;
	$rp = isset($_POST['rp']) ? $_POST['rp'] : 20;
	$sortname = isset($_POST['sortname']) ? $_POST['sortname'] : 'namaUser';
	$sortorder = isset($_POST['sortorder']) ? $_POST['sortorder'] : 'asc';
	$query = isset($_POST['query']) ? $_POST['query'] : false;
	$qtype = isset($_POST['qtype']) ? $_POST['qtype'] : false;

	require_once ('../inc/conn.php');

	$page = $_POST['page'];
	$rp = $_POST['rp'];
	$sortname = $_POST['sortname'];
	$sortorder = $_POST['sortorder'];
	
	if (!$sortname) $sortname = 'namaUser';
	if (!$sortorder) $sortorder = 'asc';
	$NamaLengkap = isset($_REQUEST['NamaLengkap']) ? $_REQUEST['NamaLengkap'] : null;
	$UserName = isset($_REQUEST['UserName']) ? $_REQUEST['UserName'] : null;
	$Departement = isset($_REQUEST['Departement']) ? $_REQUEST['Departement'] : null;
	$sort = "ORDER BY namaUser $sortorder";

	if ($NamaLengkap!='') {
		$namaUser = "and namaUser like '%".$NamaLengkap."%'";
	} else {
		$namaUser="";
	}

	if ($UserName!='') {
		$tipe = "and a.idUser like '%".$UserName."%'";
	} else {
		$tipe="";
	}
	
	if ($Departement!='') {
		$dept = "and a.department like '%".$Departement."%'";
	} else {
		$dept="";
	}
		

	if (!$page) $page = 1;
	if (!$rp) $rp = 10;

	$start = (($page-1) * $rp);

	$sql = "select top $rp c.id, a.IdUser,namaUser,a.tipe,Email,nik,no_tlp,NamaDealer,a.divisi,a.department dept,IdAtasan, c.levelvalidator 
			from deptterkait c
			inner join sys_user a on c.IdUser = a.IdUser
			left join spk00..dodealer b on a.kodedealer=b.kodedealer
			where c.id not in (
				select top $start a.IdUser from deptterkait c
			inner join sys_user a on c.IdUser = a.IdUser
			left join spk00..dodealer b on a.kodedealer=b.kodedealer
				where id=id and isnull(isDel,0)<>1 $namaUser $tipe $sort
			) and isnull(isDel,0)<>1 $namaUser $tipe $dept $sort";
	$result = mssql_query($sql);
	
	$total = mssql_num_rows(mssql_query("select c.id 
			from deptterkait c
			inner join sys_user a on c.IdUser = a.IdUser
			left join spk00..dodealer b on a.kodedealer=b.kodedealer
		where c.id=c.id and isnull(isDel,0)<>1  $namaUser $tipe $dept $sort"));
	$rows = array();
	while ($row = mssql_fetch_array($result)) {
		$rows[] = $row;
	}
	
	header("Content-type: text/xml");
	$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
	$xml .= "<rows>";
	$xml .= "<page>$page</page>";
	foreach($rows as $row) {
		$xml .= "<row id='".$row['id']."'>";
		$xml .= "<cell><![CDATA[<input type='checkbox' id='chk-1' name='id[]' value='".$row['id']."'>]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row['IdUser'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row['namaUser'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row['nik'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row['NamaDealer'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row['tipe'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row['divisi'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row['dept'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row['levelvalidator'])."]]></cell>";
		$xml .= "</row>";
	}
	$xml .= "<total>".$total."</total>";	
	$xml .= "</rows>";
	echo $xml;
?>