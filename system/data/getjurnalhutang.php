<?php
	session_start();
	include '../inc/conn.php';
	$KodeDealer = addslashes($_SESSION['kodedealer']);
	$nobukti = addslashes($_REQUEST['nobukti']);
	$kode_vendor = addslashes($_REQUEST['kode_vendor']);
	
	/*$nofaktur = "";
	$periode = "";
	while ($row = mssql_fetch_array($result)) {
		$nofaktur .= "'".$row['NoFaktur']."',";
		$periodefak = $row['tahun'].$row['bulan'];
	}
	$nofaktur = substr($nofaktur,0,strlen($nofaktur)-1);
	*/

	
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

	
	$nobuktitagihan = "";
	$no=0;
	
	/*
	--a.	Ambil No Bukti di APTRN dari hutang yang akan dibayar
SELECT distinct NoBukti FROM Aptrn WHERE NoFaktur = 'AR02635' and TypeTrn = 'C'

--b.	Ambil tanggal transaksi dari hutang tersebut untuk identifikasi database fiskal dimana jurnal tersebut berada
--ambil dr tgl faktur di tabel hutang evopay

--c.	Gunakan variable No Bukti di point a untuk menampilkan jurnal beserta No Faktur atas hutang yang akan dibayar
SELECT '21D0P9FM' as nofaktur, NoBukti, t.KodeGl as kodeakun, m.namagl, t.Keterangan, t.JlhDebit, t.JlhKredit
 FROM [acc00-202103]..GLtrn t inner join [acc00-202103]..glmst m on t.KodeGl=m.kodegl
 WHERE NoBukti in ( 'MM/48/III/2021', 'MM/49/III/2021')
	*/
		
	$sql = "select NoFaktur, SUBSTRING(CONVERT(nvarchar(6),TglTrnFaktur, 112),5,2) bulan, YEAR(TglTrnFaktur) tahun
			from DataEvoTagihan where nobukti = 'VP".$nobukti."'";
	$result = mssql_query($sql,$conns);
		
	while ($row = mssql_fetch_array($result)) {
		$nofaktur = "'".$row['NoFaktur']."'";
		$periodefak = $row['tahun'].$row['bulan'];
		
		include '../inc/koneksi.php';
										
		$sql1 = "SELECT distinct NoBukti 
							FROM [ACC".$kodecabang."-".$periodefak."]..Aptrn 
							WHERE NoFaktur in (".$nofaktur.") and TypeTrn = 'C' and KodeLgn = '".$kode_vendor."'";
							
		$result1 = mssql_query($sql1,$connCab);
		while ($row1 = mssql_fetch_array($result1)) {
		
			$nobuktitagihan = $row1['NoBukti'];
			
			$sql2 = "SELECT '".$nobuktitagihan."' as NoFaktur, NoBukti, t.KodeGl as kodeakun, m.namagl, t.Keterangan, t.JlhDebit, t.JlhKredit
					 FROM [ACC".$kodecabang."-".$periodefak."]..GLtrn t 
					 inner join [ACC".$kodecabang."-".$periodefak."]..glmst m on t.KodeGl=m.kodegl
					 WHERE NoBukti in ('".$nobuktitagihan."') and KodeLgn = '".$kode_vendor."'";
			
			$result2 = mssql_query($sql2,$connCab);
			while ($row2 = mssql_fetch_array($result2)) {
				$no++;
				$xml_body .= "<row id='".$no."'>";
				$xml_body .= "<cell><![CDATA[".utf8_encode($row2['NoFaktur'])."]]></cell>";
				$xml_body .= "<cell><![CDATA[".utf8_encode($row2['NoBukti'])."]]></cell>";
				$xml_body .= "<cell><![CDATA[".utf8_encode($row2['kodeakun'])."]]></cell>";
				$xml_body .= "<cell><![CDATA[".utf8_encode($row2['namagl'])."]]></cell>";
				$xml_body .= "<cell><![CDATA[".utf8_encode($row2['Keterangan'])."]]></cell>";
				$xml_body .= "<cell><![CDATA[<div style='text-align: right;padding:0'>".number_format($row2['JlhDebit'],0,",",".")."</div>]]></cell>";
				$xml_body .= "<cell><![CDATA[<div style='text-align: right;padding:0'>".number_format($row2['JlhKredit'],0,",",".")."</div>]]></cell>";
				$xml_body .= "</row>";
			}
		}
		
	}
	$total = $no;
	header("Content-type: text/xml");
	$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
	$xml .= "<rows>";
	$xml .= "<page>$page</page>";
	$xml .= "<total>$total</total>";
	$xml .= $xml_body;
	$xml .= "</rows>";
	echo $xml;

?>