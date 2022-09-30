<?php
	require_once ('../inc/myConn.php');
	// require_once ('../inc/phpfunction.php');
	require_once ('../inc/connCab.php');
	// $periode = date('Ym');
	$tahun = date('Y');
	// $tahun = "2020";
	// $bln = "08";
	$bln = date('m');
	$periode = $tahun."".$bln;
	
	//$bp = isset($_REQUEST['jnsHutang']) ? $_REQUEST['jnsHutang'] : 0;
	$bp=0;
	if (!empty($_REQUEST['jnsHutang'])){
		$bp=$_REQUEST['jnsHutang'];
	}
	// echo "Jenis Hutang :".$bp;
	connCab($KodeDealer,$periode,$bp,&$kodecabang,&$connCab,&$msg,&$noIso,&$hostdb_cabang);
	$table = "ACC".$kodecabang."-".$periode;
	$bengkel = "BENGKEL".$kodecabang;
	$bp = "BP".$kodecabang;
	$acc = "ACC".$kodecabang;

	if(!function_exists('datenull')){
	    function datenull($tanggal) {
			if (is_null($tanggal) || $tanggal=='Jan 1 1900 12:00AM' || $tanggal=='Jan 1 1970 12:00AM') { $tanggal=""; } else { $tanggal=date('d/m/Y', strtotime($tanggal)); }
			return $tanggal;
		}
	}

	if(!function_exists('TglNul')){
		function TglNul($tgl) {
			if ($tgl == "" or $tgl == " ") {
				$fix = "Null";
			} else {
				$fix = date('Y-m-d', strtotime($tgl));
			}
			return $fix;
		}
	}

	if(!function_exists('Terbilang')){
		function Terbilang($x) {
			$abil = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
			if ($x < 12)
			return " " . $abil[$x];
			elseif ($x < 20)
			return Terbilang($x - 10) . "belas";
			elseif ($x < 100)
			return Terbilang($x / 10) . " puluh" . Terbilang($x % 10);
			elseif ($x < 200)
			return " Seratus" . Terbilang($x - 100);
			elseif ($x < 1000)
			return Terbilang($x / 100) . " ratus" . Terbilang($x % 100);
			elseif ($x < 2000)
			return " Seribu" . Terbilang($x - 1000);
			elseif ($x < 1000000)
			return Terbilang($x / 1000) . " ribu" . Terbilang($x % 1000);
			elseif ($x < 1000000000)
			return Terbilang($x / 1000000) . " juta" . Terbilang($x % 1000000);
			elseif ($x < 9999999999)
			return Terbilang($x / 1000000000) . " milyar" . Terbilang($x % 1000000000);
		}
	}
?>