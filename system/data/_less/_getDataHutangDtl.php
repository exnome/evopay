<?php
	$KodeDealer = addslashes($_REQUEST['KodeDealer']);
	$jnsHutang = addslashes($_REQUEST['jnsHutang']);
	$KodeAP = addslashes($_REQUEST['KodeAP']);
	$noTagihan = addslashes($_REQUEST['noTagihan']);
	include '../inc/koneksi.php';
	$page = isset($_POST['page']) ? $_POST['page'] : 1;
	$rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
	$sortname = isset($_POST['sortname']) ? $_POST['sortname'] : 'NoInv';
	$sortorder = isset($_POST['sortorder']) ? $_POST['sortorder'] : 'asc';
	$query = isset($_POST['query']) ? $_POST['query'] : false;
	$qtype = isset($_POST['qtype']) ? $_POST['qtype'] : false;

	$page = $_POST['page'];
	$rp = $_POST['rp'];
	$sortname = $_POST['sortname'];
	$sortorder = $_POST['sortorder'];

	if (!$sortname) $sortname = 'NoInv';
	if (!$sortorder) $sortorder = 'asc';
	
	if ($query && $qtype) {
		$search = "and ".$_REQUEST['qtype']." like '%".$_REQUEST['query']."%'";
	} else {
		$search = "";
	}

	if ($KodeAP!="" && $jnsHutang=='0') {
		$kode = "and KODESUPP like '%".$KodeAP."%'";
	} else if ($KodeAP!="" && $jnsHutang=='1') {
		$kode = "and KodeAR like '%".$KodeAP."%'";
	} else {
		$kode = "";
	}

	$sort = "ORDER BY $sortname $sortorder";
	
	if (!$page) $page = 1;
	if (!$rp) $rp = 10;

	$start = (($page-1) * $rp);
	
	if ($jnsHutang=='0') {
		$sql = "
			select top $rp * from (select row_number() OVER (order by NoInv) noUrut, NoInv,a.NOWO as no_wo,NamaBarang,
			KodeSatuan,Qty,Harga,(Qty*Harga) total
			from [$bengkel]..KONFIRM_SUBLET a 
			inner join [$bengkel]..JOBSUBLET b on a.NOWO = b.NO_WO and a.NOINV = b.NO_INV and a.SUBLET = b.NAMABARANG 
			where NO_TAGIHAN = '$noTagihan' and KODESUPP like '%$KodeAP%') x where noUrut not in (
				select top $start * from (select row_number() OVER (order by NoInv) noUrut
				from [$bengkel]..KONFIRM_SUBLET a 
				inner join [$bengkel]..JOBSUBLET b on a.NOWO = b.NO_WO and a.NOINV = b.NO_INV and a.SUBLET = b.NAMABARANG 
				where NO_TAGIHAN = '$noTagihan' and KODESUPP like '%$KodeAP%') x
			)
		";
		$total = mssql_num_rows(mssql_query("select row_number() OVER (order by NoInv) noUrut from [$bengkel]..KONFIRM_SUBLET a 
			inner join [$bengkel]..JOBSUBLET b on a.NOWO = b.NO_WO and a.NOINV = b.NO_INV and a.SUBLET = b.NAMABARANG 
			where NO_TAGIHAN = '$noTagihan' and KODESUPP like '%$KodeAP%'",$connCab));
	} else if ($jnsHutang=='1') {
		$wSql = "
			WITH BP (NoInv,no_wo,NamaBarang,KodeSatuan,Qty,Harga,KodeAR,no_tagihan) as (
				Select A.NoInv,A.NoWO as no_wo,Pekerjaan as NamaBarang,KodeSatuan,Qty,A.HargaBeli as Harga,KodeAR,a.KodeTagih as no_tagihan
				FROM [$bp]..JobSublet A 
				Inner Join [$bp]..JobDTA B On A.NoWO = B.NOWO 
				inner join [$bp]..M_SupplierSublet C on C.Kode = A.Supplier 
				Where A.tagih = 1 And A.NoPO <> '' and Right(A.nopo,1) <> 'R' 
				And NoNota <> ''
				UNION 
				Select s.NoInv,s.Nowo as no_wo,Job as NamaBarang,'' as KodeSatuan,'1' as Qty,b.Total as Harga,Alamat3,s.Bayar as no_tagihan
				FROM [$bp]..AP_Profit s 
				INNER JOIN [$bp]..JobDta a On s.Nowo = a.Nowo 
				INNER JOIN [$bp]..JobOrder b On s.Nowo = b.Nowo 
				LEFT JOIN [$bp]..M_GroupMekanik g On s.[Group] = g.KodeGroup 
				Where g.Ext=1 And s.Bayar <> '' 
			)
		";
		
		$sql = "
			$wSql
			select top $rp noUrut,no_wo,NoInv,NamaBarang,KodeSatuan,Qty,Harga,(Qty*Harga) as total from (select row_number() OVER (order by NoInv) noUrut,no_wo,NoInv,NamaBarang,KodeSatuan,Qty,Harga,KodeAR,no_tagihan from BP) x where noUrut not in (
				select top $start noUrut from (select row_number() OVER (order by NoInv) noUrut from BP) x 
				where KodeAR = '$KodeAP' and no_tagihan='$noTagihan'
			) and KodeAR = '$KodeAP' and no_tagihan='$noTagihan'
		";
		$total = mssql_num_rows(mssql_query("$wSql select noUrut from (select row_number() OVER (order by NoInv) noUrut,KodeAR,no_tagihan from BP) x 
				where KodeAR = '$KodeAP' and no_tagihan='$noTagihan'",$connCab));
	} else if ($jnsHutang=='2') {
		$sql = "
			select top $rp * from (select row_number() OVER (order by DaNo) noUrut,DaNo as NoInv,NamaBarang,KodeSatuan,QtyTerimaBln as Qty,
				HargaBeli as Harga,(QtyTerimaBln*HargaBeli) as total From [SPK00]..inmst) x where noUrut not in (
					select top $start noUrut from (select row_number() OVER (order by DaNo) noUrut,DaNo as NoInv,NamaBarang,KodeSatuan,
					QtyTerimaBln as Qty,HargaBeli as Harga,(QtyTerimaBln*HargaBeli) as total From [SPK00]..inmst) x where NoInv = '$noTagihan'
			) and NoInv  = '$noTagihan'
		";
		$total = mssql_num_rows(mssql_query("select noUrut from (select row_number() OVER (order by DaNo) noUrut,DaNo as NoInv,NamaBarang,KodeSatuan,QtyTerimaBln as Qty,HargaBeli as Harga,(QtyTerimaBln*HargaBeli) as total From [SPK00]..inmst) x where NoInv = '$noTagihan'",$connCab));
	} else if ($jnsHutang=='3') {
		$wsql = "
			with part (noTagihan,tglTagihan,KodeAR,totalTagihan) as (
				select NoBukti as noTagihan,TglTrnFaktur as tglTagihan,a.kodelgn as KodeAR,sum(JumlahTrn) as totalTagihan 
				from [$table]..Aptrn a inner join [$table]..apmst b on a.kodelgn=b.kodelgn
				where a.kodelgn = 'PRTTAM'
				GROUP BY a.kodelgn,NoBukti,TglTrnFaktur HAVING sum(jumlahtrn)>0
			)
		";
		$sql = "
			$wsql select top $rp noTagihan,tglTagihan,KodeAR,totalTagihan,year(tglTagihan) as Tahun,month(tglTagihan) as Bulan from part 
				where noTagihan not in (
				select top $start noTagihan from part where ISNULL(tglTagihan,'')<>'' $tagihan $search $kode
			) and ISNULL(tglTagihan,'')<>'' $tagihan $search $kode
		";
		$total = mssql_num_rows(mssql_query("$wsql select noTagihan from part where ISNULL(tglTagihan,'')<>'' $tagihan $search $kode",$connCab));
	}
	// echo $sql;
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
	if (is_array($rows) || is_object($rows)) {
		$no=1;
		foreach($rows as $row_jenis) {
			$xml .= "<row id='".$row_jenis['noUrut']."'>";
			$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['NoInv'])."]]></cell>";
			$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['no_wo'])."]]></cell>";
			$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['NamaBarang'])."]]></cell>";
			$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['KodeSatuan'])."]]></cell>";
			$xml .= "<cell><![CDATA[<div style='text-align: right;padding:0'>".number_format($row_jenis['Qty'],0,",",".")."</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='text-align: right;padding:0'>".number_format($row_jenis['Harga'],0,",",".")."</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='text-align: right;padding:0'>".number_format($row_jenis['total'],0,",",".")."</div>]]></cell>";
			$xml .= "</row>";
			$no++;
		}
	}
	$xml .= "</rows>";
	echo $xml;

	function bln($a){
		if (strlen($a)==1) {
			$data = "0".$a;
		} else {
			$data = $a;
		}
		return $data;
	}
?>