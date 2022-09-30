<?php
	$KodeDealer = addslashes($_REQUEST['KodeDealer']);
	require_once ('../inc/conn.php');
	$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : null;
	if ($action=='getNumber') {
		##$kodecbg = mssql_fetch_array(mssql_query("select kodecabang from profilCR..dealer where kodedealer='".$KodeDealer."'"));		
		$kodecbg = mssql_fetch_array(mssql_query("select RIGHT(DBName,2) kodecabang from SPK00..DoDealer where kodedealer='".$KodeDealer."'"));
		if ($KodeDealer=='2010') { $kode = "00"; } else { $kode = $kodecbg['kodecabang']; }
		$jenis = $kode."/".date('d')."/".date('m')."/".date('y')."/";
		$query = "SELECT max(nobukti) as maxID FROM DataEvo WHERE nobukti LIKE '%$jenis%'";
		$hasil = mssql_query($query);
		$data = mssql_fetch_array($hasil);
		$idMax = $data['maxID'];
		$noUrut = (int) substr($idMax, -3, 3);
		$noUrut++;
		$number = $jenis.sprintf("%03s", $noUrut);
		echo $number;
	} else if ($action=='getNumberR') {
		$IdUser = addslashes($_REQUEST['IdUser']);
		$sql = "select nobukti from DataEvo where kodedealer='".$KodeDealer."'";
		$sql = "
			select a.nobukti from DataEvoVal a
			inner join DataEvo b on a.nobukti=b.nobukti
			where validasi = 'Reject' and a.kodedealer='".$KodeDealer."' and userentry = '".$IdUser."'
		";
		$rsl = mssql_query($sql);
		echo "<option value=''>- Pilih -</option>";
		while ($dt = mssql_fetch_array($rsl)) {
			echo "<option value='$dt[nobukti]'>$dt[nobukti]</option>";
		}
	} else if ($action=='getNumberCsv') {
		// $kodecbg = mssql_fetch_array(mssql_query("select kodecabang from profilCR..dealer where kodedealer='".$KodeDealer."'"));
		// if ($KodeDealer=='2010') { $kode = "00"; } else { $kode = $kodecbg['kodecabang']; }
		// $jenis = "CSV/".$kode."/".date('d')."/".date('m')."/".date('y')."/";
		$jenis = "CSV/".date('d')."/".date('m')."/".date('y')."/";
		$query = "SELECT max(noCsv) as maxID FROM DataEvoTransfer WHERE noCsv LIKE '%$jenis%'";
		$hasil = mssql_query($query);
		$data = mssql_fetch_array($hasil);
		$idMax = $data['maxID'];
		$noUrut = (int) substr($idMax, -3, 3);
		$noUrut++;
		$number = $jenis.sprintf("%03s", $noUrut);
		echo $number;
	}
?>