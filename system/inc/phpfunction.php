<?
error_reporting(0);

define("CHIPER","nAsMoCoNRM");

/*
koneksi_serverlain('ACC', &$host_acc, &$conn_acc);
global $conn_acc;
global $host_acc;



koneksi_serverlain('HRD', &$host_hrd, &$conn_hrd);
global $conn_hrd;
global $host_hrd;



koneksi_serverlain('GA', &$host_ga, &$conn_ga);
global $conn_ga;
global $host_ga;



koneksi_serverlain('PART', &$host_part, &$conn_part);
global $conn_part;
global $host_part;



koneksi_serverlain('SVC', &$host_svc, &$conn_svc);
global $conn_svc;
global $host_svc;



koneksi_serverlain('GRADING', &$host_grading, &$conn_grading);
global $conn_grading;
global $host_grading;

*/


function chiper($enkrip, $chiper, $kondisi, &$dekrip) {
	$val_chiper = 0;
	
	for ($x=0;$x<strlen($chiper);$x++) {
		$val_chiper = $val_chiper + ord(substr($chiper,$x,1)) + 13;
	}
	
	$dekrip = "";
	for ($x=0;$x<strlen($enkrip);$x++) {
		$j = $x + 1;
		if ($kondisi) {
			$val_enkrip = ord(substr($enkrip,$x,1)) + $val_chiper + ($j * $j);
			while ($val_enkrip>255) {
				$val_enkrip = $val_enkrip - 255;
			} 
		} else {
			$val_enkrip = ord(substr($enkrip,$x,1)) - $val_chiper - ($j * $j);
			while ($val_enkrip<=0) {
				$val_enkrip = $val_enkrip + 255;
			}
		}
		$dekrip .= chr($val_enkrip);
	}
	
	return $dekrip;
}





function getSaturdays($tgl_retail_1, $tgl_retail_2, &$saturdays, &$jml){
    $tgl_retail_1 = str_replace("-","/",$tgl_retail_1);
	$tgl_retail_1 = date("Y-m-d", strtotime($tgl_retail_1.'-1 day'));
	$tgl_retail_2 = str_replace("-","/",$tgl_retail_2);
	$tgl_retail_2 = date("Y-m-d", strtotime($tgl_retail_2.'+1 day'));
	
	$ts  = strtotime("first saturday $tgl_retail_1");
    $end = strtotime("last saturday $tgl_retail_2");
   	$saturdays = array();
    $jml = 0;
	while($ts <= $end) {
		$jml++;
        $saturdays[] = $ts;
        $ts = strtotime('next saturday', $ts);
    }
	return $saturdays;
	return $jml;
}


function getSundays($tgl_retail_1, $tgl_retail_2, &$sundays, &$jml){
	$tgl_retail_1 = str_replace("-","/",$tgl_retail_1);
	$tgl_retail_1 = date("Y-m-d", strtotime($tgl_retail_1.'-1 day'));
	$tgl_retail_2 = str_replace("-","/",$tgl_retail_2);
	$tgl_retail_2 = date("Y-m-d", strtotime($tgl_retail_2.'+1 day'));
	
    $ts  = strtotime("first sunday $tgl_retail_1");
    $end = strtotime("last sunday $tgl_retail_2");
    $sundays = array();
	$jml = 0;
    while($ts <= $end) {
		$jml++;
		$sundays[] = $ts;
        $ts = strtotime('next sunday', $ts);
    }
    return $sundays;
	return $jml;
}



function cekLibur($tgl_retail_1,$tgl_retail_2,&$jml) {
	
	koneksi_serverlain('VLD', &$host_vld, &$conn_vld);
	//global $conn_vld,$host_vld;
	

	$jml = 0;
	
	if (!$conn_vld) {
		echo "<center><b>Connection Failed to ".$host_vld."</b></center><br>";
	} else { 			
		$qry = mssql_query("use [ProfilPDS];select  CONVERT(VARCHAR(10), tgllibur, 102) tgllibur from harilibur
							where tgllibur between cast('".$tgl_retail_1."' as datetime) and cast('".$tgl_retail_2."' as datetime)",$conn_vld);
		while ($dt = mssql_fetch_array($qry)) {
			$tgllibur = $dt['tgllibur'];
			$tgllibur = str_replace(".","-",$tgllibur);			
			getSaturdays($tgllibur, $tgllibur, &$saturdays, &$jml_sabtu);
			if ($jml_sabtu==0) {
				getSundays($tgllibur, $tgllibur, &$sundays, &$jml_minggu);
				#echo $jml_minggu."...";
				if ($jml_minggu==0) {					
					$jml++;
				} else {
					if ($jml==0)
						$jml = 0;
						else
							$jml--;	
				}
			} else {
				$jml--;
			}
		}
	}
	return $jml;
}



function cekharikerja($tgl_retail_1,$tgl_retail_2,&$jml){

	$pecah_tgl1 = explode("-",$tgl_retail_1); // y-m-d
	$pecah_tgl2 = explode("-",$tgl_retail_2);
	
	$bln1 = $pecah_tgl1[1];
	$thn1 = $pecah_tgl1[0];
	$hari1 = $pecah_tgl1[2];
	
	$bln2 = $pecah_tgl2[1];
	$thn2 = $pecah_tgl2[0];
	$hari2 = $pecah_tgl2[2];
	
	cekLibur($tgl_retail_1,$tgl_retail_2,&$jml_libur);
	getSaturdays($tgl_retail_1, $tgl_retail_2, $saturdays, &$jml_sabtu);
	getSundays($tgl_retail_1, $tgl_retail_2, &$sundays, &$jml_minggu);
	
	
	$jml_senin_jmt = ($hari2 - $hari1 + 1) - ($jml_libur + $jml_sabtu + $jml_minggu);
	$jml = $jml_senin_jmt + ($jml_sabtu * 0.5);
	#echo $tgl_retail_1."--".$jml_senin_jmt."--".$jml_sabtu."--".$jml_minggu."--".$jml_libur."<br>";
	
	#echo $tgl_retail_1."__".$hari2."__".$hari1."__".$jml_sabtu."__".$jml_minggu."__".$jml_libur."<br>";
	
	return $jml;
}




function last_login() {
	global $conn;
	
	$qry = mysql_query("select a.id_aktifitas, b.nama_aktifitas, date_format(a.tgl,'%d-%m-%Y %H:%i:%S') tgl, a.ip from user_log a
						left join m_aktifitas b on a.id_aktifitas = b.id_aktifitas
						where a.id_user = '".$_SESSION['dashboard_usr']."' and a.id_aktifitas = 1
						order by a.tgl desc
						limit 2",$conn);
	$x=0;					
	while ($dt = mysql_fetch_array($qry)) {
		$x++;
		if ($x==2) {
			echo "Last login:".$dt['tgl']." # ".$dt['ip'].". ";	
		} 
	}
	 
}



function koneksi_server_cabang($hostdb_koneksi,$userdb_koneksi,$passdb_koneksi,$db_koneksi,$db_cabang, $user_chiper, $pass_chiper, &$conn_cabang){
	global $conn;
	
	koneksi_serverlain('ACC',&$hostdb_server, &$conn_server);

	if (!$conn_server) {
		//echo "<center><b>Connection Failed to ".$hostdb_server."</b></center><br>";
	} else { 
		$qry_cabang = mssql_query("select kodecabang, namacabang,ipaddress, NewEnkrip from Kodecabang 
									where ipaddress = '".$hostdb_koneksi."'",$conn_server); 
		$dt_cabang = mssql_fetch_array($qry_cabang);
		$kode_cabang = $dt_cabang['kodecabang'];
		$namadb_cabang = $dt_cabang['namacabang'];
		$newenkrip = $dt_cabang['NewEnkrip'];
		
		if ($newenkrip==1) {
			$userdb_koneksi	= user_koneksi();
			$passdb_koneksi = password_koneksi();	
			$user_chiper = KataKunci(3);
			$pass_chiper = KataKunci(5);
			
		} 
		$conn_koneksi = mssql_connect($hostdb_koneksi,$userdb_koneksi,$passdb_koneksi);
		if (!$conn_koneksi) {
			//echo "<center><b>Connection Failed to ".$hostdb_koneksi."</b></center><br>";
		} 
		mssql_select_db($db_koneksi);
		//global $conn_koneksi;
		
		$qry_accountdb = mssql_query("select top 1 CONVERT(NVARCHAR(30),UserSQL) UserSQL, CONVERT(NVARCHAR(30),PwdSQL) PwdSQL 
									from [".$db_koneksi."]..koneksi",$conn_koneksi);
		$dt_accountdb = mssql_fetch_array($qry_accountdb);
		
		
		if ($newenkrip==1) {
			chiperdb($dt_accountdb['UserSQL'], $user_chiper, false,&$user_dekrip);
			chiperdb($dt_accountdb['PwdSQL'], $pass_chiper, false,&$pass_dekrip);
		} else {
			chiperdbx($dt_accountdb['UserSQL'], $user_chiper, false,&$user_dekrip);
			chiperdbx($dt_accountdb['PwdSQL'], $pass_chiper, false,&$pass_dekrip);
		}
		
		$hostdb_server = $hostdb_koneksi;
		$userdb_server	= $user_dekrip;
		$passdb_server = $pass_dekrip;
		$db_server = $db_cabang;

		
	}
	
	#-------------------- koneksi baru
			
	$conn_cabang = mssql_connect($hostdb_server,$userdb_server,$passdb_server);
	mssql_select_db($db_server);
	
	if (!$conn_cabang) {
		//echo "<center><b>Connection Failed to ".$hostdb_server."</b></center><br>";
	} 
	
	return $conn_cabang;
}




function koneksi_serverlain($kode_server, &$host, &$conn_server){
	global $conn;
	
	#-------------------- koneksi baru
	$qry_koneksi = mysql_query("select host, userdb, passdb, db, db_koneksi, user_chiper, pass_chiper, newenkrip from m_koneksi
								where is_active = '1' and kode = '".$kode_server."' limit 1",$conn);
	$dt_koneksi = mysql_fetch_array($qry_koneksi);
	$newenkrip = $dt_koneksi['newenkrip'];
	
	
	$hostdb_koneksi = $dt_koneksi['host'];
	$userdb_koneksi	= $dt_koneksi['userdb'];
	$passdb_koneksi = $dt_koneksi['passdb'];	
	$db_koneksi = $dt_koneksi['db_koneksi'];
	$user_chiper = $dt_koneksi['user_chiper'];
	$pass_chiper = $dt_koneksi['pass_chiper'];
	
	if ($newenkrip==1) {
		$userdb_koneksi	= user_koneksi();
		$passdb_koneksi = password_koneksi();	
		$user_chiper = KataKunci(3);
		$pass_chiper = KataKunci(5);
		
	} 
	#read_koneksi(&$userdb_koneksi, &$passdb_koneksi, &$user_chiper, &$pass_chiper);
	
	$conn_koneksi = mssql_connect($hostdb_koneksi,$userdb_koneksi,$passdb_koneksi);
	if (!$conn_koneksi) {
		//echo "<center><b>Connection Failed to ".$hostdb_koneksi."</b></center><br>";
	} 
	mssql_select_db($db_koneksi);
	//global $conn_koneksi;
	
	$qry_accountdb = mssql_query("select top 1 CONVERT(NVARCHAR(30),UserSQL) UserSQL, CONVERT(NVARCHAR(30),PwdSQL) PwdSQL 
								from [".$db_koneksi."]..koneksi",$conn_koneksi);
	$dt_accountdb = mssql_fetch_array($qry_accountdb);
	
	#$user_dekrip = Dekeripsi($dt_accountdb['UserSQL'],KataKunci(3));
	#$pass_dekrip = Dekeripsi($dt_accountdb['PwdSQL'],KataKunci(5));
	
	#$data = getDecrypted($dt_koneksi['host']);
	#$userdb_server = $data[0]; #$user_dekrip;
	#$passdb_server = $data[1]; #$pass_dekrip;
	
	if ($newenkrip==1) {
		chiperdb($dt_accountdb['UserSQL'], $user_chiper, false,&$user_dekrip);
		chiperdb($dt_accountdb['PwdSQL'], $pass_chiper, false,&$pass_dekrip);
	} else {
		chiperdbx($dt_accountdb['UserSQL'], $user_chiper, false,&$user_dekrip);
		chiperdbx($dt_accountdb['PwdSQL'], $pass_chiper, false,&$pass_dekrip);
	}
	
	$hostdb_server = $dt_koneksi['host'];
	$userdb_server = $user_dekrip;
	$passdb_server = $pass_dekrip;
	$db_server = $dt_koneksi['db'];
	
	#-------------------- koneksi baru
	$conn_server = mssql_connect($hostdb_server,$userdb_server,$passdb_server);
	mssql_select_db($db_server);
	
	if (!$conn_server) {
		//echo "<center><b>Connection Failed to ".$hostdb_server."</b></center><br>";
	} 
	
	$host = $hostdb_server;
	return $conn_server;
	return $host;
}

// $output = eval(base64_decode("ZnVuY3Rpb24gS2F0YUt1bmNpKCR2YWwpew0KCXN3aXRjaCgkdmFsKXsNCgkJY2FzZSAzOg0KCQkJJGthdGEgPSAiTTRkNDYyIjsNCgkJCWJyZWFrOw0KCQljYXNlIDU6DQoJCQkka2F0YSA9ICJHNGo0SCI7DQoJCQlicmVhazsNCgl9CQ0KCXJldHVybiAka2F0YTsNCn0NCg0KDQpmdW5jdGlvbiB1c2VyX2tvbmVrc2koKXsNCgkkdXNlciA9ICJrb25la3NpIjsNCglyZXR1cm4gJHVzZXI7DQp9DQoNCmZ1bmN0aW9uIHBhc3N3b3JkX2tvbmVrc2koKXsNCgkkcGFzcyA9ICJHNGo0aE00ZGEiOw0KCXJldHVybiAkcGFzczsNCn0NCg0KDQpmdW5jdGlvbiBjaGlwZXJkYigkZW5rcmlwLCAkY2hpcGVyLCAka29uZGlzaSwgJiRkZWtyaXApIHsNCgkkdmFsX2NoaXBlciA9IDA7DQoJDQoJZm9yICgkeD0wOyR4PHN0cmxlbigkY2hpcGVyKTskeCsrKSB7DQoJCSR2YWxfY2hpcGVyID0gJHZhbF9jaGlwZXIgKyBvcmQoc3Vic3RyKCRjaGlwZXIsJHgsMSkpICsgMTM7DQoJfQ0KCQ0KCSRkZWtyaXAgPSAiIjsNCglmb3IgKCR4PTA7JHg8c3RybGVuKCRlbmtyaXApOyR4KyspIHsNCgkJJGogPSAkeCArIDE7DQoJCWlmICgka29uZGlzaSkgew0KCQkJJHZhbF9lbmtyaXAgPSBvcmQoc3Vic3RyKCRlbmtyaXAsJHgsMSkpICsgJHZhbF9jaGlwZXIgKyAoJGogKiAkaik7DQoJCQl3aGlsZSAoJHZhbF9lbmtyaXA+MjU1KSB7DQoJCQkJJHZhbF9lbmtyaXAgPSAkdmFsX2Vua3JpcCAtIDI1NTsNCgkJCX0gDQoJCX0gZWxzZSB7DQoJCQkkdmFsX2Vua3JpcCA9IG9yZChzdWJzdHIoJGVua3JpcCwkeCwxKSkgLSAkdmFsX2NoaXBlciAtICgkaiAqICRqKTsNCgkJCXdoaWxlICgkdmFsX2Vua3JpcDw9MCkgew0KCQkJCSR2YWxfZW5rcmlwID0gJHZhbF9lbmtyaXAgKyAyNTU7DQoJCQl9DQoJCX0NCgkJJGRla3JpcCAuPSBjaHIoJHZhbF9lbmtyaXApOw0KCX0NCgkNCglyZXR1cm4gJGRla3JpcDsNCn0="));

$output = eval(base64_decode("ZnVuY3Rpb24gS2F0YUt1bmNpKCR2YWwpew0KCXN3aXRjaCgkdmFsKXsNCgkJY2FzZSAzOg0KCQkJJGthdGEgPSAiTTRkNDYyIjsNCgkJCWJyZWFrOw0KCQljYXNlIDU6DQoJCQkka2F0YSA9ICJHNGo0SCI7DQoJCQlicmVhazsNCgl9CQ0KCXJldHVybiAka2F0YTsNCn0NCg0KDQpmdW5jdGlvbiB1c2VyX2tvbmVrc2koKXsNCgkkdXNlciA9ICJrb25la3NpIjsNCglyZXR1cm4gJHVzZXI7DQp9DQoNCmZ1bmN0aW9uIHBhc3N3b3JkX2tvbmVrc2koKXsNCgkkcGFzcyA9ICJHNGo0aE00ZGEiOw0KCXJldHVybiAkcGFzczsNCn0NCg0KDQpmdW5jdGlvbiBjaGlwZXJkYigkZW5rcmlwLCAkY2hpcGVyLCAka29uZGlzaSwgJiRkZWtyaXApIHsNCgkkdmFsX2NoaXBlciA9IDA7DQoJDQoJZm9yICgkeD0wOyR4PHN0cmxlbigkY2hpcGVyKTskeCsrKSB7DQoJCSR2YWxfY2hpcGVyID0gJHZhbF9jaGlwZXIgKyBvcmQoc3Vic3RyKCRjaGlwZXIsJHgsMSkpICsgMTM7DQoJfQ0KCQ0KCSRkZWtyaXAgPSAiIjsNCglmb3IgKCR4PTA7JHg8c3RybGVuKCRlbmtyaXApOyR4KyspIHsNCgkJJGogPSAkeCArIDE7DQoJCWlmICgka29uZGlzaSkgew0KCQkJJHZhbF9lbmtyaXAgPSBvcmQoc3Vic3RyKCRlbmtyaXAsJHgsMSkpICsgJHZhbF9jaGlwZXIgKyAoJGogKiAkaik7DQoJCQl3aGlsZSAoJHZhbF9lbmtyaXA+MjU1KSB7DQoJCQkJJHZhbF9lbmtyaXAgPSAkdmFsX2Vua3JpcCAtIDI1NTsNCgkJCX0gDQoJCX0gZWxzZSB7DQoJCQkkdmFsX2Vua3JpcCA9IG9yZChzdWJzdHIoJGVua3JpcCwkeCwxKSkgLSAkdmFsX2NoaXBlciAtICgkaiAqICRqKTsNCgkJCXdoaWxlICgkdmFsX2Vua3JpcDw9MCkgew0KCQkJCSR2YWxfZW5rcmlwID0gJHZhbF9lbmtyaXAgKyAyNTU7DQoJCQl9DQoJCX0NCgkJJGRla3JpcCAuPSBjaHIoJHZhbF9lbmtyaXApOw0KCX0NCgkNCglyZXR1cm4gJGRla3JpcDsNCn0="));


function chiperdbx($enkrip, $chiper, $kondisi, &$dekrip) {
	$val_chiper = 0;
	
	for ($x=0;$x<strlen($chiper);$x++) {
		$val_chiper = $val_chiper + ord(substr($chiper,$x,1)) + 13;
	}
	
	$dekrip = "";
	for ($x=0;$x<strlen($enkrip);$x++) {
		$j = $x + 1;
		if ($kondisi) {
			$val_enkrip = ord(substr($enkrip,$x,1)) + $val_chiper + ($j * $j);
			while ($val_enkrip>255) {
				$val_enkrip = $val_enkrip - 255;
			} 
		} else {
			$val_enkrip = ord(substr($enkrip,$x,1)) - $val_chiper - ($j * $j);
			while ($val_enkrip<=0) {
				$val_enkrip = $val_enkrip + 255;
			}
		}
		$dekrip .= chr($val_enkrip);
	}
	
	return $dekrip;
}

function tgl_header() {
	$hari = date("l");
	$bulan= date("F");
	$tanggal= date("d");
	$tahun= date("Y");
		
	# ganti nama hari
	switch ($hari) {
		case "Sunday" :
			print ("Minggu, ");
			break;
		case "Monday" :
			print ("Senin, ");
			break;
		case "Tuesday" :
			print ("Selasa, ");
			break;
		case "Wednesday" :
			print ("Rabu, ");
			break;
		case "Thursday" :
			print ("Kamis, ");
			break;
		case "Friday" :
			print ("Jum`at, ");
			break;
		case "Saturday" :
			print ("Sabtu, ");
			break;
	}
	# Print Tanggal			
	echo $tanggal;		
	# Ganti Nama Bulan
	switch ($bulan)  {
		case "January" :
			print (" Januari ");
			break;
		case "February" :
			print (" Februari ");
			break;
		case "March" :
			print (" Maret ");
			break;
		case "April" :
			print (" April ");
			break;
		case "May" :
			print (" Mei ");
			break;
		case "June" :
			print (" Juni ");
			break;
		case "July" :
			print (" Juli ");
			break;
		case "August" :
			print (" Agustus ");
			break;
		case "September" :
			print (" September ");
			break;
		case "October" :
			print (" Oktober ");
			break;
		case "November" :
			print (" November ");
			break;
		case "December" :
			print (" Desember ");
			break;
	}
	 echo "$tahun";
}


function nama_bulan($bulan,&$bln) {
	# Ganti Nama Bulan
	switch ($bulan)  {
		case "01" :
			$bln = "Jan";
			break;
		case "02" :
			$bln = "Feb";
			break;
		case "03" :
			$bln = "Mar";
			break;
		case "04" :
			$bln = "Apr";
			break;
		case "05" :
			$bln = "Mei";
			break;
		case "06" :
			$bln = "Jun";
			break;
		case "07" :
			$bln = "Jul";
			break;
		case "08" :
			$bln = "Ags";
			break;
		case "09" :
			$bln = "Sep";
			break;
		case "10" :
			$bln = "Okt";
			break;
		case "11" :
			$bln = "Nov";
			break;
		case "12" :
			$bln = "Des";
			break;
	}
	
	return $bln;	
}




########################  MENU HORISONTAL  #############################
function block_create_menu($title,$content){
	echo "<script language=\"JavaScript\">";			
	echo $content;
	echo "</script>";
}


function menuBoxMenu($blockId) {
	global $conn,$tab_user_menu;	
	
	$sessGroup = $_SESSION['dashboard_group'];	
	//$sessGroup = "ADMIN";
	$sql = "select id_menu, menu_name, menu_desc, menu_link, is_active, parent_
			 from user_menu
			 where IS_ACTIVE='1' and ".$sessGroup."= '1' and parent_ = ".$blockId." order by order_";
	$stm = mysql_query($sql,$conn);
	while($data = mysql_fetch_array($stm)) {
		$menuId = $data['id_menu'];
		$menuName = $data['menu_name'];
		$menuDesc = $data['menu_desc'];
		$menuLink = $data['menu_link'];
		$menuAccess = $data['is_active'];
		$contents = 'var MENU_'.$menuId.' = ['.menuMenu($menuId).']; '.
			'new menu(MENU_'.$menuId.',MENU_TPL);';
		block_create_menu($menuName, $contents);
	}
}



function menuMenu($blockId) {
	global $conn,$tab_user_menu;
	
	$sessGroup = $_SESSION['dashboard_group'];	
	//$sessGroup = "ADMIN";	
	$sql = "select id_menu, menu_name, menu_desc, 	menu_link, 	is_active, 	parent_
			from user_menu 
			where IS_ACTIVE='1' and ".$sessGroup."= '1' and parent_ = ".$blockId." order by order_";
	$stm = mysql_query($sql,$conn);
	while($data = mysql_fetch_array($stm)) {
		$menuId = $data['id_menu'];
		$menuName = $data['menu_name'];
		$menuDesc = $data['menu_desc'];
		$menuLink = $data['menu_link'];
		$menuAccess = $data['is_active'];
		if(empty($menuLink))
		{
			$menuLinkx="null";
			$null="null";
		}
		else if($menuLink=="#")
		{
			$menuLinkx="null";
			$null="null";
		}
		else
		{
			$menuLinkx="'".$menuLink."'";
			if($menuName=="HOME" || $menuName=="Home" || $menuName=="home")
			{
				$null="null";	
			} else {
				$null="{'tw' :'$menuJenis'}";
			}
		}
		$menuArray .= "['".$menuName."', ".$menuLinkx.", ".$null."],";
		$menuArray = substr($menuArray, 0, strlen($menuArray)-2).",".menuMenu($menuId).substr($menuArray, -2, 2);			
	}
	return $menuArray;
}



function uploadImage($img_name,$img_temp,$vdir_upload){
	//header("Content-type: image/jpeg");
	
	//direktori gambar
	//vdir_upload = "img/";
	$vfile_upload = $vdir_upload . $img_name;
	
	//Simpan gambar dalam ukuran sebenarnya
	move_uploaded_file($img_temp, $vfile_upload);
	
	//identitas file asli
	$im_src = imagecreatefromjpeg($vfile_upload);
	$src_width = imageSX($im_src);
	$src_height = imageSY($im_src);
	
	//Simpan dalam versi small 110 pixel
	//set ukuran gambar hasil perubahan
	$dst_width = 110;
	$dst_height = ($dst_width/$src_width)*$src_height;
	
	//proses perubahan ukuran
	$im = imagecreatetruecolor($dst_width,$dst_height);
	imagecopyresampled($im, $im_src, 0, 0, 0, 0, $dst_width, $dst_height, $src_width, $src_height);
	
	//Simpan gambar
	imagejpeg($im,$vdir_upload . "small_" . $img_name);
	
	//Simpan dalam versi medium 320 pixel
	//set ukuran gambar hasil perubahan
	$dst_width = 320;
	$dst_height = ($dst_width/$src_width)*$src_height;
	
	//proses perubahan ukuran
	$im = imagecreatetruecolor($dst_width,$dst_height);
	imagecopyresampled($im, $im_src, 0, 0, 0, 0, $dst_width, $dst_height, $src_width, $src_height);
	
	//Simpan gambar
	imagejpeg($im,$vdir_upload . "medium_" . $img_name);
	
	imagedestroy($im_src);
	imagedestroy($im);
	unlink($vfile_upload);
}


function uploadImagebadan($img_name,$img_temp,$vdir_upload){
	//header("Content-type: image/jpeg");
	
	//direktori gambar
	//vdir_upload = "img/";
	$vfile_upload = $vdir_upload . $img_name;
	
	//Simpan gambar dalam ukuran sebenarnya
	move_uploaded_file($img_temp, $vfile_upload);
	
	//identitas file asli
	$im_src = imagecreatefromjpeg($vfile_upload);
	$src_width = imageSX($im_src);
	$src_height = imageSY($im_src);
	
	//Simpan dalam versi small 110 pixel
	//set ukuran gambar hasil perubahan
	$dst_width = 150;
	$dst_height = ($dst_width/$src_width)*$src_height;
	
	//proses perubahan ukuran
	$im = imagecreatetruecolor($dst_width,$dst_height);
	imagecopyresampled($im, $im_src, 0, 0, 0, 0, $dst_width, $dst_height, $src_width, $src_height);
	
	//Simpan gambar
	imagejpeg($im,$vdir_upload . "small_" . $img_name);
	
	//Simpan dalam versi medium 320 pixel
	//set ukuran gambar hasil perubahan
	$dst_width = 600;
	$dst_height = ($dst_width/$src_width)*$src_height;
	
	//proses perubahan ukuran
	$im = imagecreatetruecolor($dst_width,$dst_height);
	imagecopyresampled($im, $im_src, 0, 0, 0, 0, $dst_width, $dst_height, $src_width, $src_height);
	
	//Simpan gambar
	imagejpeg($im,$vdir_upload . "medium_" . $img_name);
	
	imagedestroy($im_src);
	imagedestroy($im);
	unlink($vfile_upload);
}


function uploadImageprofil($img_name,$img_temp,$vdir_upload){
	//header("Content-type: image/jpeg");
	
	//direktori gambar
	//vdir_upload = "img/";
	$vfile_upload = $vdir_upload . $img_name;
	
	//Simpan gambar dalam ukuran sebenarnya
	move_uploaded_file($img_temp, $vfile_upload);
	
	//identitas file asli
	$im_src = imagecreatefromjpeg($vfile_upload);
	$src_width = imageSX($im_src);
	$src_height = imageSY($im_src);
	
	//Simpan dalam versi small 110 pixel
	//set ukuran gambar hasil perubahan
	$dst_width = 20;
	$dst_height = ($dst_width/$src_width)*$src_height;
	
	//proses perubahan ukuran
	$im = imagecreatetruecolor($dst_width,$dst_height);
	imagecopyresampled($im, $im_src, 0, 0, 0, 0, $dst_width, $dst_height, $src_width, $src_height);
	
	//Simpan gambar
	imagejpeg($im,$vdir_upload . "small_" . $img_name);
	
	//Simpan dalam versi medium 320 pixel
	//set ukuran gambar hasil perubahan
	$dst_width = 160;
	$dst_height = ($dst_width/$src_width)*$src_height;
	
	//proses perubahan ukuran
	$im = imagecreatetruecolor($dst_width,$dst_height);
	imagecopyresampled($im, $im_src, 0, 0, 0, 0, $dst_width, $dst_height, $src_width, $src_height);
	
	//Simpan gambar
	imagejpeg($im,$vdir_upload . "medium_" . $img_name);
	
	imagedestroy($im_src);
	imagedestroy($im);
	unlink($vfile_upload);
}


function formatTanggal($date=null)
{
	//buat array nama hari dalam bahasa Indonesia dengan urutan 1-7
	$array_hari = array(1=>'Senin','Selasa','Rabu','Kamis','Jumat', 'Sabtu','Minggu');
	//buat array nama bulan dalam bahasa Indonesia dengan urutan 1-12
	$array_bulan = array(1=>'Januari','Februari','Maret', 'April', 'Mei', 'Juni','Juli','Agustus',
	'September','Oktober', 'November','Desember');
	if($date == null) {
	//jika $date kosong, makan tanggal yang diformat adalah tanggal hari ini
	$hari = $array_hari[date('N')];
	$tanggal = date ('j');
	$bulan = $array_bulan[date('n')];
	$tahun = date('Y');
	} else {
	//jika $date diisi, makan tanggal yang diformat adalah tanggal tersebut
	$date = strtotime($date);
	$hari = $array_hari[date('N',$date)];
	$tanggal = date ('j', $date);
	$bulan = $array_bulan[date('n',$date)];
	$tahun = date('Y',$date);
	}
	$formatTanggal = $hari . ", " . $tanggal ." ". $bulan ." ". $tahun;
	return $formatTanggal;
}
	
	
	
function AddDateFormat($date_source="",$date_format="d/m/y",$add_date=0,$add_month=0,$add_year=0) {
	
	if (!$date_source) $date_source = date("Y-m-d");
	
	return date($date_format, mktime(0, 0, 0,
	substr($date_source,5,2)+$add_month, substr($date_source,8,2)+$add_date,
	substr($date_source,0,4)+$add_year));

}	


	function enkripsi($data)
	{
	  $enkrip1=base64_encode($data);
	  $enkrip2=$enkrip1."_d4ShB04Rd";
	  $enkrip3=base64_encode($enkrip2);
		
	  return $enkrip3;
	}
	
	function dekripsi($data)
	{
	  $dekrip1=base64_decode($data);
	  $dekrip2=explode("_d4ShB04Rd",$dekrip1);
	  $dekrip3=base64_decode($dekrip2[0]);
	  
	  return $dekrip3;
	}
	
	//anti sql injeksi/ hack
	function antisqlinjection($value){
		// Stripslashes
		if 	(get_magic_quotes_gpc()){
			$value = stripslashes($value);
		}
		if (!is_numeric($value)){
			$value = mysql_real_escape_string($value);
		}
		return $value;
	}
	
	// anti cross site scripting
	function anti_xss($data){ 
		$xss=stripslashes(strip_tags(htmlspecialchars($data,ENT_QUOTES))); 
		return $xss; 
	} 

	// FUNGSI KONVERSI TGL ENGLISH -> TGL INDONESIA
	function konversi_tgl($tgl) {
		$tgl_ind=substr($tgl,8,2)."-".substr($tgl,5,2)."-".substr($tgl,0,4);
		return $tgl_ind;
	}
	// Konvesi KONVERSI TGL INDONESIA -> TGL ENGLISH
	function konversi_tgl_eng($tgl) {
		$tgl_eng=substr($tgl,6,4)."-".substr($tgl,3,2)."-".substr($tgl,0,2);
		return $tgl_eng;
	}
	// FUNGSI FORMAT ANGKA
	function format_angka($angka) {
		$hasil =  number_format($angka,0, ",",".");
		$hasil = $hasil.",-";
		return $hasil;
	}
	
	

?>