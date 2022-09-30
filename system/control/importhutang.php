<?php
session_start();
//error_reporting(0);
/** Include path **/
ini_set('include_path', ini_get('include_path').';assets/phpexcel');

/** PHPExcel */
require_once './../../assets/phpexcel/PHPExcel.php';
require_once ('./../../assets/phpexcel/PHPExcel/IOFactory.php');

/** PHPExcel_Writer_Excel2007 */
include './../../assets/phpexcel/PHPExcel/Writer/Excel2007.php';

//set_time_limit(0);
ini_set('memory_limit', '512M');
ini_set('max_execution_time',7200);


switch ($_REQUEST['action']) {
	case 'formatexcel':
		//$KodeLgn = addslashes($_REQUEST['KodeLgn']); 
		//$filename = "hutang_".$KodeLgn;
		//header("Content-type: application/vnd.ms-excel");
		//header("Content-Disposition: attachment; filename=formatImportHutang.xls");
		
		//header('Content-Type: application/vnd.ms-excel');
		//header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
		//header('Cache-Control: max-age=0');
		
		formatexcel();
		break;
	default:
		import2();
		break;
}


function import(){
	include '../inc/conn.php';
	$IdUser = $_SESSION['UserID'];
	$KodeLgn = addslashes($_REQUEST['kode_vendorx']);
	$nobukti = addslashes($_REQUEST['nobuktix']);
	$KodeDealer = addslashes($_REQUEST['KodeDealerx']);
	$tipehutang = addslashes($_REQUEST['tipehutangx']);
	$file = $_FILES['filex']['name'];	
	
	if ($_FILES['filex']['type'] == "application/csv" or $_FILES['filex']['type'] == "application/vnd.ms-excel" or $_FILES['filex']['type'] == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet") {	
				
		$maxsize = 10000000;
		if ($_FILES['filex']['size'] > $maxsize) {
			$msg .= "File : ".$_FILES['filex']['size']." kb. Your file over from 10 Mb. Please try again !!";					
		} else {		
			$file = $_FILES['filex']['name'];		
			move_uploaded_file($_FILES['filex']['tmp_name'],$file);
			
			if ($_FILES['filex']['type'] == "application/csv" or $_FILES['filex']['type'] == "application/vnd.ms-excel") {
				$jns_excel = "application/vnd.ms-excel"; 
				$type_excel = 'Excel5';
			}else {
				$jns_excel = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"; 
				$type_excel = 'Excel2007';
			}
			
			$del = mssql_query("delete from DataEvoTagihanImport where userentry = '".$IdUser."' and nobukti = '".trim($nobukti)."' and kodelgn = '".trim($KodeLgn)."'");	
			
			$objReader = PHPExcel_IOFactory::createReader($type_excel);
			$objReader->setReadDataOnly(true);
			$objPHPExcel = $objReader->load($file);
			$objWorksheet = $objPHPExcel->getActiveSheet();
			$cell = array();
			
			$filename = $_FILES['filex']['name'];	
			$baris = 0;
			
			foreach ($objWorksheet->getRowIterator() as $row) {	
				$a=0;
				$i=0;
				$baris++;
				
				$cellIterator = $row->getCellIterator();
				$cellIterator->setIterateOnlyExistingCells(false);
				foreach ($cellIterator as $cell_raw) {
					//$cell[$i] = PHPExcel_Style_NumberFormat::toFormattedString($cell_raw->getValue(), 'MM/DD/YYYY');
					/*if ($i==1 or $i==2) {
						$cell[$i] = PHPExcel_Style_NumberFormat::toFormattedString($cell_raw->getValue(), 'DD/MM/YYYY');
					} else {
						$cell[$i] = $cell_raw->getValue();
					}*/
					$cell[$i] = $cell_raw->getValue();
					$i++;
					
				}
				
				if	($baris>=2) {					
					/*$nofaktur	= str_replace('"','',str_replace("'","",rtrim(ltrim($cell[0]))));
					$nofaktur 	= str_replace('&lrm;','',html_entity_decode(mb_convert_encoding(stripslashes($nofaktur), "HTML-ENTITIES", 'UTF-8')));
					$tglfaktur	= str_replace('"','',str_replace("'","",rtrim(ltrim($cell[1]))));
					$tglfaktur 	= str_replace('&lrm;','',html_entity_decode(mb_convert_encoding(stripslashes($tglfaktur), "HTML-ENTITIES", 'UTF-8')));
					$tgljth		= str_replace('"','',str_replace("'","",rtrim(ltrim($cell[2]))));
					$tgljth 	= str_replace('&lrm;','',html_entity_decode(mb_convert_encoding(stripslashes($tgljth), "HTML-ENTITIES", 'UTF-8')));
					$jmltrn		= str_replace('"','',str_replace("'","",rtrim(ltrim($cell[3]))));
					$jmltrn 	= str_replace('&lrm;','',html_entity_decode(mb_convert_encoding(stripslashes($jmltrn), "HTML-ENTITIES", 'UTF-8')));
					$jmlbyr		= str_replace('"','',str_replace("'","",rtrim(ltrim($cell[4]))));
					$jmlbyr 	= str_replace('&lrm;','',html_entity_decode(mb_convert_encoding(stripslashes($jmlbyr), "HTML-ENTITIES", 'UTF-8')));
					*/
					
					$nofaktur	= rtrim(ltrim($cell[0]));
					$tglfakturx	= rtrim(ltrim($cell[1]));
					$tgljthx	= rtrim(ltrim($cell[2]));
					$jmltrn		= str_replace(",",".",str_replace(".","",trim($cell[3])));
					$jmlbyr		= str_replace(",",".",str_replace(".","",trim($cell[4])));
					
					$tglfaktur_arr = explode("/",$tglfakturx);
					$tglfaktur = $tglfaktur_arr[2]."-".$tglfaktur_arr[1]."-".$tglfaktur_arr[0]; 
					
					$tgljth_arr = explode("/",$tgljthx);
					$tgljth = $tgljth_arr[2]."-".$tgljth_arr[1]."-".$tgljth_arr[0]; 
					
					if (!empty($nofaktur)) {		
						$prc = mssql_query("
							insert into DataEvoTagihanImport (NoFaktur, TglTrnFaktur, TglJthTmp, JumlahTrn, JumlahBayar, KodeLgn,
							nobukti, kodedealer, userentry, tglentry)
							values ('".$tgljthx."__".$tgljth."','".$tglfaktur."','".$tgljth."','".$jmltrn."', '".$jmlbyr."', '".$KodeLgn."',
							'".$nobukti."', '".$KodeDealer."', '".$IdUser."',getdate())");
						if ($prc) {
							$status = "Sukses";
							//$msg .= $r[0].";".$r[1].";".$r[2].";".$r[3].";<font style='color: #22af47'>Success!</font>,";
						}
					}
					
				}
			}
		
		}
	} else {
			$status = "Gagal";
	}
	unlink($file);
	$hasil = array(
        'status' => $status,
    );
    echo json_encode($hasil);
}

	

function import2(){
	include '../inc/conn.php';
	$IdUser = $_SESSION['UserID'];
	$KodeLgn = addslashes($_REQUEST['kodelgn']); 
	$nobukti = addslashes("VP".$_REQUEST['nobukti']); 
	$KodeDealer = addslashes($_REQUEST['KodeDealer']); 
	$tipehutang = addslashes($_REQUEST['tipehutangx']); 
	
	$data = addslashes($_REQUEST['data']);
	$datas = explode(",", $data);
	$msg = "";
	
	$qry_del = mssql_query("delete from DataEvoTagihanImport where userentry =  '".$IdUser."'");
	
	for ($i=0; $i < count($datas); $i++) { 
		//echo $datas[$i]."<br>";
		
		$r = explode(";", $datas[$i]);
		if ($r[0]!='') {
			// No faktur	tgl faktur	tgl jth tempo	jml tagihan		jml bayar
			//		0			1			2			3				4
			$tglfaktur_arr = explode("/",$r[1]);
			$tglfaktur = $tglfaktur_arr[2]."-".$tglfaktur_arr[1]."-".$tglfaktur_arr[0];
			$tgljth_arr = explode("/",$r[2]);
			$tgljth = $tgljth_arr[2]."-".$tgljth_arr[1]."-".$tgljth_arr[0];
			//echo $r[0];
				
			$prc = mssql_query("
				insert into DataEvoTagihanImport (NoFaktur, TglTrnFaktur, TglJthTmp, JumlahTrn, JumlahBayar, KodeLgn, nobukti, kodedealer, userentry, tglentry)
				values ('".trim($r[0])."','".$tglfaktur."','".$tgljth."',
				".str_replace(",",".",trim($r[3])).", ".str_replace(",",".",trim($r[4])).", '".trim($KodeLgn)."',
				'".$nobukti."', '".$KodeDealer."', '".$IdUser."',getdate())");
			if ($prc) {
				//$msg .= $r[0].";".$r[1].";".$r[2].";".$r[3].";<font style='color: #22af47'>Success!</font>,";
			}
	
		}
	}
	// echo substr($msg, 0, -1);
}


function formatexcel(){
	include '../inc/conn.php';
	$KodeDealer = addslashes($_REQUEST['KodeDealer']);
	$KodeLgn = addslashes(str_replace("__"," ",$_REQUEST['KodeLgn']));
	$tipehutang = addslashes(str_replace("__"," ",$_REQUEST['tipehutang']));
	$action = addslashes($_REQUEST['action']);
	
	if ($KodeDealer=='2010') { $is_dealer = "HO"; } else { $is_dealer = "Dealer"; }
	
	//$getdata = " and convert(varchar(20),NoFaktur) + '_' + convert(varchar(20),JumlahTrn) not in (".getdata($KodeDealer,$KodeLgn).") ";
	$getdata = " and NoFaktur not in (".getdata($KodeDealer,$KodeLgn).") ";
	include '../inc/koneksi.php';
	
	if ($tipehutang=="Hutang Sales") {
		$wherehutang  = " and NoFaktur like 'MV%' ";
		
	} else if ($tipehutang=="Hutang Part") {
		$wherehutang  = " and kodelgn = 'PRTTAM' ";
		
	} else if ($tipehutang=="Hutang Aksesoris") {
		$wherehutang  = " and NoFaktur like 'WOAC/%' ";
		
	} else if ($tipehutang=="Hutang TWC") {
		$wherehutang  = " and NoFaktur like 'PTC%' ";
		
	} else if ($tipehutang=="Hutang Free Service") {
		$wherehutang  = " and NoFaktur not like 'MV%' and NoFaktur not like 'WOAC/%' and NoFaktur not like 'PTC%' and NoFaktur not like 'MM%'
							and kodelgn not in ('PRTTAM','MBLT-0001') ";
		
	} else if ($tipehutang=="Hutang Lain-lain") {
		$wherehutang  = " and NoFaktur like 'MM%' ";
	}
	
	
	$wsql = "
			with getHutang (TglTrnFaktur2, KodeLgn,NoFaktur,TglTrnFaktur,TglJthTmp,Keterangan,JumlahTrn,DelIndex,TypeTrn,Sumber,Kodejurnal) as (
				
				select TglTrnFaktur2, KodeLgn,NoFaktur,TglTrnFaktur,TglJthTmp,Keterangan,JumlahTrn,DelIndex,TypeTrn,Sumber,Kodejurnal
				from (				
			
					select convert(varchar(10),TglTrnFaktur,111) TglTrnFaktur2, KodeLgn,NoFaktur,TglTrnFaktur,TglJthTmp,    
					(SELECT TOP 1 cast(Keterangan as varchar(1000))  FROM [".$table."]..Aptrn  
						WHERE a.KodeLgn=kodelgn AND a.KodeLgn=kodelgn AND a.Nofaktur=nofaktur) AS Keterangan,   
					ROUND(JumlahTrn,0) JumlahTrn,   
					(SELECT TOP 1 DelIndex FROM [".$table."]..Aptrn WHERE a.KodeLgn = kodelgn AND nofaktur = a.nofaktur ORDER BY Kounter) AS DelIndex,   
					(SELECT TOP 1 Typetrn3 FROM [".$table."]..Aptypesu  WHERE  typetrn = 
						(SELECT TOP 1 TypeTrn FROM [".$table."]..Aptrn  WHERE a.KodeLgn=kodelgn))  AS TypeTrn,   
						(SELECT TOP 1 Left(Delindex,2) FROM [".$table."]..Aptrn WHERE kodelgn = a.KodeLgn AND nofaktur = a.nofaktur 
						ORDER BY Kounter) AS Sumber,   
					(SELECT TOP 1 Kodejurnal FROM [".$table."]..Aptrn  WHERE a.KodeLgn=kodelgn AND a.KodeLgn = kodelgn AND a.Nofaktur=nofaktur) AS Kodejurnal  
					from (   
						
						select max(KodeLgn) as KodeLgn,NoFaktur,min(TglTrnFaktur) as TglTrnFaktur,min(TglJthTmp) as TglJthTmp,   
						sum(JumlahTrn) as JumlahTrn 
						from [".$table."]..Aptrn where KodeLgn='".$KodeLgn."'  ".$wherehutang." GROUP BY NoFaktur, TglTrnFaktur HAVING sum(jumlahtrn)>0  
						
					) a					
				
				) x
			)";
		
	$sql = " $wsql select * from getHutang where KodeLgn='".$KodeLgn."' $getdata order by TglTrnFaktur, NoFaktur";
	$stm = mssql_query("$wsql select KodeLgn from getHutang where KodeLgn='".$KodeLgn."' $getdata order by TglTrnFaktur, NoFaktur",$connCab);
	
	//echo "<pre>$sql</pre>";
		
	$total = mssql_num_rows($stm);
	$result = mssql_query($sql,$connCab);
	$rows = array();
	while ($row = mssql_fetch_array($result)) {
		$rows[] = $row;
	}

	$xml = "";
	
	$objPHPExcel = new PHPExcel(); 
	
	$objPHPExcel->getProperties()->setCreator("Andex Teddy / exnome@gmail.com");
	$objPHPExcel->getProperties()->setLastModifiedBy("Andex Teddy / exnome@gmail.com");
	$objPHPExcel->getProperties()->setTitle("Evopay");
	$objPHPExcel->getProperties()->setSubject("Evopay");
	$objPHPExcel->getProperties()->setDescription("Evopay");
	
	$active_sheet = 0;				
	$rowscounter = 1;
	
	$objPHPExcel->setActiveSheetIndex($active_sheet);
	$objPHPExcel->getActiveSheet()->setTitle('Hutang');
	
	$i=0;
	$j=1;	
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'No Faktur');
	$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;	
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Tgl Faktur');
	$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Tgl Jth Tempo');
	$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Jumlah Tagihan');
	$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Jumlah Bayar');
	$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
	
	
	if (is_array($rows) || is_object($rows)) {
		$no=1;
		$cekada = 0;
		foreach($rows as $dt) {
		
			$nofaktur = $dt['NoFaktur'];
			$tgltrn = $dt['TglTrnFaktur2'];
		//	$cekada = cekdata($KodeDealer, $KodeLgn, $nofaktur, $tgltrn);
			
			if ($cekada==0) {
			
				$xml .= "<tr>";
				$xml .= "<td>".utf8_encode($dt['NoFaktur'])."</td>";
				$xml .= "<td>".datenull($dt['TglTrnFaktur'])."</td>";
				$xml .= "<td>".datenull($dt['TglJthTmp'])."</td>";
				//$xml .= "<td>".number_format($dt['JumlahTrn'],0,"","")."</td>";			
				//$xml .= "<td>".number_format($dt['JumlahTrn'],0,"","")."</td>";
				$xml .= "<td>".$dt['JumlahTrn']."</td>";			
				$xml .= "<td>".$dt['JumlahTrn']."</td>";
				$xml .= "</tr>";
				$no++;
				
				$i=0; 
				$j++;	
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, utf8_encode($dt['NoFaktur']));
				$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;	
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, datenull($dt['TglTrnFaktur']));
				$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;	
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, datenull($dt['TglJthTmp']));
				$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;	
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, number_format($dt['JumlahTrn'],2,".",""));
				$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;	
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, number_format($dt['JumlahTrn'],2,".",""));
				$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;	
				
				
			}
		}
	}

	$html = '
		<table border="1" width="100%" cellspacing="0" cellpadding="0">
			<thead>
				<tr>
					<td>No Faktur</td>
					<td>Tgl Faktur</td>
					<td>Tgl Jth Tempo</td>
					<td>Jumlah Tagihan</td>
					<td>Jumlah Bayar</td>
				</tr>
			</thead>
			<tbody>';
			$html .= $xml;
			$html .= '
			</tbody>
		</table>';
		//echo $html;
		//header("Cache-Control: public");
		
		//header('Content-Transfer-Encoding: binary');
		//header('Expires: 0');
		//header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		//header('Pragma: public');
		//header('Content-Length: ' . filesize($file_url)); //Absolute URL
		//   ob_clean();
		
	$filename = "hutang_".$KodeLgn;
		
	
	$styleArray = array( 'borders' => array( 'allborders' => array( 
							'style' => PHPExcel_Style_Border::BORDER_THIN,
							'color' => array('argb' => '00000000'), ), ), );
	
	$objPHPExcel->getActiveSheet()->getStyle('A1:E'.$j)->applyFromArray($styleArray);
									
		
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
	header('Cache-Control: max-age=0');
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007'); application/vnd.openxmlformats-officedocument.spreadsheetml.sheet
	$objWriter->save('php://output');
	
	
	exit;
	
}


	function getdata($KodeDealer, $kodelgn){
		include '../inc/conn.php';
		//$sql = "select DISTINCT NoFaktur from DataEvoTagihan where isreject=0 and kodedealer = '".$KodeDealer."'";
		
		$sql = "select DISTINCT a.NoFaktur  nofak
				from DataEvoTagihan a
				inner join DataEvo b on a.nobukti = b.nobukti
				where a.isreject=0 and a.kodedealer = '".$KodeDealer."' and b.kode_vendor = '".$kodelgn."'";
		/*$sql = "select DISTINCT convert(varchar(20),a.NoFaktur) + '_' + convert(varchar(20),a.JumlahTrn) nofak
				from DataEvoTagihan a
				inner join DataEvo b on a.nobukti = b.nobukti
				where a.isreject=0 and a.kodedealer = '".$KodeDealer."' and b.kode_vendor = '".$kodelgn."'";*/		
		$rsl = mssql_query($sql);
		$nf = "'',";
		while ($dt = mssql_fetch_array($rsl)) {
			$nf .= "'".$dt['nofak']."',";
		}
		$nf_ = substr($nf, 0,strlen($nf)-1);
		return $nf_;
	}
	
	function cekdata($KodeDealer, $kodelgn, $nofaktur, $tgltrn){
		include '../inc/conn.php';
		
		$sql = "select a.NoFaktur, b.kode_vendor, a.TglTrnFaktur
				from DataEvoTagihan a
				inner join DataEvo b on a.nobukti = b.nobukti
				where isnull(a.isreject,0) = 0 and a.kodedealer = '".$KodeDealer."' and a.NoFaktur = '".$nofaktur."' and a.TglTrnFaktur = '".$tgltrn."'
				and b.kode_vendor = '".$kodelgn."'";	
			
		$query = mssql_query($sql);		
		$jml = mssql_num_rows($query);
		
		return $jml;
	}
	
?>