<?php
	require_once '../inc/conn.php';
	$KodeDealer = addslashes($_REQUEST['KodeDealer']);
	$Tipe = addslashes($_REQUEST['Tipe']);
	$tipehutang = addslashes($_REQUEST['tipehutang']);
	if ($Tipe=='HUTANG') {
		if ($KodeDealer=='2010') { $is_dealer = "HO"; } else { $is_dealer = "Dealer"; }
		//$dQuery = mssql_fetch_array(mssql_query("select query from sys_hutang where nama='".$tipehutang."' and posisi = '".$is_dealer."'"));
	}
	
	require_once '../inc/koneksi.php';
	
	$page = isset($_POST['page']) ? $_POST['page'] : 1;
	$rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
	$sortname = isset($_POST['sortname']) ? $_POST['sortname'] : 'KodeLgn';
	$sortorder = isset($_POST['sortorder']) ? $_POST['sortorder'] : 'asc';
	$query = isset($_POST['query']) ? $_POST['query'] : false;
	$qtype = isset($_POST['qtype']) ? $_POST['qtype'] : false;

	$page = $_POST['page'];
	$rp = $_POST['rp'];
	$sortname = $_POST['sortname'];
	$sortorder = $_POST['sortorder'];

	if (!$sortname) $sortname = 'KodeLgn';
	if (!$sortorder) $sortorder = 'asc';
	
	if ($query && $qtype) {
		$search = "and ".$_REQUEST['qtype']." like '%".$_REQUEST['query']."%'";
	}
	$sort = "ORDER BY $sortname $sortorder";
	
	if (!$page) $page = 1;
	if (!$rp) $rp = 10;

	$start = (($page-1) * $rp);
	if ($Tipe=='HUTANG') {
	
		if ($tipehutang=="Hutang Sales") {
			$wherehutang  = " and a.NoFaktur like 'MV%' ";
			
		} else if ($tipehutang=="Hutang Part") {
			$wherehutang  = " and a.kodelgn = 'PRTTAM' ";
			
		} else if ($tipehutang=="Hutang Aksesoris") {
			$wherehutang  = " and a.NoFaktur like 'WOAC/%' ";
			
		} else if ($tipehutang=="Hutang TWC") {
			$wherehutang  = " and a.NoFaktur like 'PTC%' ";
			
		} else if ($tipehutang=="Hutang Free Service") {
			//$wherehutang  = " and a.NoFaktur not like 'MV%' and a.NoFaktur not like 'WOAC/%' and a.NoFaktur not like 'PTC%' and a.NoFaktur not like 'MM%' ";
			$wherehutang  = " and a.NoFaktur not like 'MV%' and a.NoFaktur not like 'WOAC/%' and a.NoFaktur not like 'PTC%' and a.NoFaktur not like 'MM%' 
							and a.kodelgn not in ('PRTTAM','MBLT-0001') ";
			
		} else if ($tipehutang=="Hutang Lain-lain") {
			$wherehutang  = " and a.NoFaktur like 'MM%' ";
		}
		
		/*$wSql = "
			WITH vendor (KodeLgn,NamaLgn,benificary_account,nama_bank,nama_pemilik,email_penerima,nama_alias,TypePPN,Npwp,pkp, JumlahTrn) as (
				
				select a.KodeLgn,b.NamaLgn,b.Norek_Supplier as benificary_account,b.NmBank_Supplier as nama_bank,
				b.Namarek_Supplier as nama_pemilik,b.Email_Supplier as email_penerima,b.NamaAlias as nama_alias,
				b.TypePPN,isnull(b.Npwp,0) as Npwp,b.pkp, sum(JumlahTrn) as JumlahTrn 
				from [".$table."]..aptrn a
				inner join [".$table."]..apmst b on a.KodeLgn=b.kodelgn
				where a.kodelgn is not null $wherehutang
				group by a.KodeLgn,b.NamaLgn,b.Norek_Supplier,b.NmBank_Supplier,b.Namarek_Supplier,b.Email_Supplier,b.NamaAlias,
				b.TypePPN, b.Npwp,b.pkp
				having sum(JumlahTrn) > 0
				
			)
		";*/
		
		$wSql = "
			WITH vendor (KodeLgn,NamaLgn,benificary_account,nama_bank,nama_pemilik,email_penerima,nama_alias,TypePPN,Npwp,pkp) as (
				
				select a.KodeLgn,b.NamaLgn,b.Norek_Supplier as benificary_account,b.NmBank_Supplier as nama_bank,
				b.Namarek_Supplier as nama_pemilik,b.Email_Supplier as email_penerima,b.NamaAlias as nama_alias,
				b.TypePPN,isnull(b.Npwp,0) as Npwp,b.pkp
				from [".$table."]..aptrn a
				inner join [".$table."]..apmst b on a.KodeLgn=b.kodelgn
				where a.kodelgn is not null $wherehutang
				group by a.KodeLgn,b.NamaLgn,b.Norek_Supplier,b.NmBank_Supplier,b.Namarek_Supplier,b.Email_Supplier,b.NamaAlias,
				b.TypePPN, b.Npwp,b.pkp
				
			)
		";
		
		$sql = "
			$wSql select DISTINCT top $rp KodeLgn,NamaLgn,benificary_account,nama_bank,nama_pemilik,email_penerima,nama_alias,TypePPN,Npwp,pkp
			from vendor where KodeLgn not in (
				select DISTINCT top $start KodeLgn from vendor where KodeLgn=KodeLgn $search $sort
			) $search $sort
		";
		//echo "<pre>$sql</pre>";
		
		$total = mssql_num_rows(mssql_query("$wSql select DISTINCT KodeLgn from vendor where KodeLgn=KodeLgn $search $sort",$connCab));
		
	} else if ($Tipe=='BIAYA') {
	
		$sql = "select top $rp KodeLgn,NamaLgn,Norek_Supplier as benificary_account,NmBank_Supplier as nama_bank,Namarek_Supplier as nama_pemilik,Email_Supplier as email_penerima,NamaAlias as nama_alias,TypePPN,isnull(Npwp,0) as Npwp,pkp 
				from [$table]..apmst where KodeLgn = KodeLgn and KodeLgn not in (
			select top $start KodeLgn from [$table]..apmst where KodeLgn = KodeLgn $search $sort
		) $search $sort";
		$total = mssql_num_rows(mssql_query("select KodeLgn from [$table]..apmst where KodeLgn = KodeLgn $search $sort"));
	
	}
	//echo "<pre>$sql</pre>";

	$result = mssql_query($sql,$connCab);
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
		if ($row['TypePPN']=='' || $row['TypePPN']==' ') { $typePpn = "T"; } else { $typePpn = $row['TypePPN']; }
		$id = "".ltrim(rtrim($row['KodeLgn']))."#".ltrim(rtrim($row['NamaLgn']))."#".$row['benificary_account']."#".ltrim(rtrim($row['nama_bank']))."#".$row['nama_pemilik']."#".ltrim(rtrim($row['email_penerima']))."#".$row['nama_alias']."#".ltrim(rtrim($typePpn))."#".ltrim(rtrim($row['Npwp']))."#".ltrim(rtrim($row['pkp']))."";
		$xml .= "<row id='".$row['KodeLgn']."'>";
		$xml .= "<cell><![CDATA[<input type='checkbox' id='chk-1' name='id[]' value='".$id."'>]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row['KodeLgn'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row['NamaLgn'])."]]></cell>";
		$xml .= "<cell><![CDATA[".utf8_encode($row['TypePPN'])."]]></cell>";
		$xml .= "</row>";
	}
	$xml .= "</rows>";
	echo $xml;

	
	function getdatas($KodeDealer){
		include '../inc/conn.php';
		$sql = "select DISTINCT NoFaktur from DataEvoTagihan where isreject=0 and kodedealer = '".$KodeDealer."'";
		$rsl = mssql_query($sql);
		$nf = "'',";
		while ($dt = mssql_fetch_array($rsl)) {
			$nf .= "'".$dt['NoFaktur']."',";
		}
		$nf_ = substr($nf, 0,strlen($nf)-1);
		return $nf_;
	}
?>