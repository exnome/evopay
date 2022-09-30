<?php
	//error_reporting(0);
	session_start();
	
	include '../inc/conn.php';
	$KodeDealer = addslashes($_REQUEST['KodeDealer']);
	$KodeLgn = addslashes($_REQUEST['KodeLgn']);
	$tipehutang = addslashes($_REQUEST['tipehutang']);
	$action = addslashes($_REQUEST['action']);
	$modif = addslashes($_REQUEST['modif']);
	
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
	
	if ($KodeDealer=='2010') { $is_dealer = "HO"; } else { $is_dealer = "Dealer"; }
	//$dQuery = mssql_fetch_array(mssql_query("select query2 from sys_hutang where nama='".$tipehutang."' and posisi = '".$is_dealer."'"));
	$nobukti = addslashes($_REQUEST['nobukti']);
	
	$nofakturIn = array();
	$tgltrnfakturIn = array();
	$jumlahtrnIn = array();
	$dataIn = array();
	$getdata = "";
			
	/*if (!empty($KodeLgn)) {
		//$getdata_arr = getdata($KodeDealer,$KodeLgn,$wherehutang);
		// and JumlahTrn2 not in (".$hasil['jml'].") and TglTrnFaktur2 not in (".$hasil['tgl'].") 
		//$getdata = $hasil;
		
		$tableacc = "ACC00-".date("Ym");
		$sql = "select DISTINCT a.NoFaktur, a.JumlahTrn,  convert(varchar(10),a.TglTrnFaktur,111) TglTrnFaktur
				from DataEvoTagihan a
				inner join DataEvo b on a.nobukti = b.nobukti
				where isnull(a.isreject,0)=0 and a.kodedealer = '".$KodeDealer."' and b.kode_vendor = '".$KodeLgn."'
				and a.NoFaktur in (	
					select NoFaktur from [10.10.27.171].[".$tableacc."].dbo.Aptrn 
					where KodeLgn='".$KodeLgn."'  ".$wherehutang."
				)";	
		
		mssql_query("SET ANSI_NULLS ON;");
		mssql_query("SET ANSI_WARNINGS ON;"); 		
		$rsl = mssql_query($sql);		
		
		
		while ($dt = mssql_fetch_array($rsl)) {
			//$nofakturIn[] = trim($dt['NoFaktur']);
			//$tgltrnfakturIn[] = trim($dt['TglTrnFaktur']);
			//$jumlahtrnIn[] = number_format($dt['JumlahTrn'],2,".","");
			$dataIn[] = trim($dt['NoFaktur'])."_".trim($dt['TglTrnFaktur'])."_".number_format($dt['JumlahTrn'],2,".","");
			
			//echo $dt['NoFaktur']."__".$dt['TglTrnFaktur']."__".number_format($dt['JumlahTrn'],2,".","").'<br>';
		}
		
	}*/
	
	
	if ($nobukti!='') {
		if ($action=='import') {
			$hutang = array();
			$jumlahTrn = array();
			$jumlahByr = array();
			
			$IdUser = $_SESSION['UserID'];
			//$KodeLgn = addslashes($_REQUEST['kodelgn']); 
			$nobukti = addslashes("VP".$_REQUEST['nobukti']); 
			//$KodeDealer = addslashes($_REQUEST['KodeDealer']); 
			//$tipehutang = addslashes($_REQUEST['tipehutangx']); 
			
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
						$hutang[] = trim($r[0]);
						$jumlahTrn[] = str_replace(",",".",trim($r[3]));
						$jumlahByr[] = str_replace(",",".",trim($r[4]));
						
					}
			
				}
			}
			
			
			/*$sqlHtg = "select NoFaktur, JumlahTrn,JumlahBayar
						 from DataEvoTagihanImport where kodelgn = '".$KodeLgn."' and userentry = '".$_SESSION['UserID']."'";
			$rslHtg = mssql_query($sqlHtg);			
			while ($row = mssql_fetch_array($rslHtg)) {
				$hutang[] = $row['NoFaktur'];
				$jumlahTrn[] = $row['JumlahTrn'];
				$jumlahByr[] = $row['JumlahBayar'];
			}*/
			//$getdata = "";
			
			
		} else {
			$sqlHtg = "select * from DataEvoTagihan where nobukti = 'VP".$nobukti."'";
			$rslHtg = mssql_query($sqlHtg);
			$hutang = array();
			while ($row = mssql_fetch_array($rslHtg)) {
				$hutang[] = $row['NoFaktur'];
			}
			//$getdata = "";
		}
	} else {
		//$getdata = "and NoFaktur not in (".getdata($KodeDealer,$KodeLgn).")";
		//$getdata = " and convert(varchar(20),NoFaktur) + '_' + convert(varchar(20),JumlahTrn) + '_' + convert(varchar(10),TglTrnFaktur,104) not in (".getdata($KodeDealer,$KodeLgn).") ";
		//$getdata = " and kd not in (".getdata($KodeDealer,$KodeLgn).") ";
		
	}

	include '../inc/koneksi.php';
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

	/*$wsql = "
		with getHutang (KodeLgn,NoFaktur,TglTrnFaktur,TglJthTmp,Keterangan,JumlahTrn,DelIndex,TypeTrn,Sumber,Kodejurnal) as (
			".str_replace("[bengkel]", "[".$bengkel."]", str_replace("[bp]", "[".$bp."]", str_replace("[table]", "[".$table."]", $dQuery['query2'])))."
		)
	";
	*/
	
	$wsql = "
			with getHutang (TglTrnFaktur2, KodeLgn,NoFaktur,TglTrnFaktur,TglJthTmp,Keterangan,JumlahTrn,DelIndex,TypeTrn,Sumber,Kodejurnal) as (
				
				select TglTrnFaktur2, KodeLgn,NoFaktur,TglTrnFaktur,TglJthTmp,Keterangan,JumlahTrn,DelIndex,TypeTrn,Sumber,Kodejurnal
				from (				
			
					select convert(varchar(10),TglTrnFaktur,111) TglTrnFaktur2, KodeLgn,NoFaktur,TglTrnFaktur,TglJthTmp,    
					(SELECT TOP 1 cast(Keterangan as varchar(1000))  FROM [".$table."]..Aptrn  
						WHERE a.KodeLgn=kodelgn AND a.KodeLgn=kodelgn AND a.Nofaktur=nofaktur and a.TglTrnFaktur = TglTrnFaktur) AS Keterangan,   
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
	
	/*		
	$wsql = "
			with getHutang (TglTrnFaktur2, KodeLgn,NoFaktur,TglTrnFaktur,TglJthTmp,Keterangan,JumlahTrn,DelIndex,TypeTrn,Sumber,Kodejurnal, nobukti) as (
				
				select TglTrnFaktur2, KodeLgn,NoFaktur,TglTrnFaktur,TglJthTmp,Keterangan,JumlahTrn,DelIndex,TypeTrn,Sumber,Kodejurnal, nobukti
				from (				
					
					select 	TglTrnFaktur2, KodeLgn,NoFaktur,TglTrnFaktur,TglJthTmp,keterangan, 
					sum(JumlahTrn) JumlahTrn, DelIndex, TypeTrn, Left(Delindex,2) Sumber, Kodejurnal, nobukti
					from (
						select convert(varchar(10),TglTrnFaktur,111) TglTrnFaktur2, KodeLgn,NoFaktur,TglTrnFaktur,TglJthTmp,	
						cast(Keterangan as varchar(1000)) keterangan, JumlahTrn , DelIndex, TypeTrn,
						Left(Delindex,2) Sumber, Kodejurnal, nobukti
						from [".$table."]..Aptrn 
						where KodeLgn='".$KodeLgn."' ".$wherehutang."
					) x  
					GROUP BY TglTrnFaktur2, KodeLgn,NoFaktur,TglTrnFaktur,TglJthTmp,keterangan, DelIndex, TypeTrn,  kodejurnal, nobukti
					 HAVING sum(jumlahtrn)>0 			
				
				) x
			)"; */
				
	$sql = " $wsql select * from getHutang where KodeLgn='".$KodeLgn."' $getdata order by TglTrnFaktur, NoFaktur";
	$stm = mssql_query("$wsql select KodeLgn from getHutang where KodeLgn='".$KodeLgn."' $getdata order by TglTrnFaktur, NoFaktur",$connCab);
	
	//echo "<pre>$sql</pre>";
		
	$total = mssql_num_rows($stm);
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
		$totNom = 0;
		foreach($rows as $dt) {
			$bayar = 0;	
			$bayar2 = 0;
			$cekada = 0;
			//echo $no.'.'.$dt['NoFaktur']."__".$dt['TglTrnFaktur2']."__".number_format($dt['JumlahTrn'],2,".","")."###";
			
			/*if (in_array(trim($dt['NoFaktur'])."_".trim($dt['TglTrnFaktur2'])."_".number_format($dt['JumlahTrn'],2,".",""), $dataIn)) {
				$cekada = 1;
			} else {
				$cekada = 0;
			}
			*/ 
			$nofaktur = $dt['NoFaktur'];
			$tgltrn = $dt['TglTrnFaktur2'];
			//$nobuktihutang = $dt['nobukti'];
			$nobuktihutang = "";
			$cekada = cekdata($KodeDealer, $KodeLgn, $nofaktur, $nobuktihutang, $tgltrn, $modif);
			
			if ($modif==1) {
				if ($nobukti!='') {
						if ($action=='import') {
							if (in_array($dt['NoFaktur'], $hutang)) {
								// (in_array(round($dt['JumlahTrn'],0), $jumlahTrn))
									// (in_array(round($dt['JumlahTrn'],0), $jumlahByr))
								if (in_array(number_format($dt['JumlahTrn'],2,".",""), $jumlahTrn)) {
									if (in_array(number_format($dt['JumlahTrn'],2,".",""), $jumlahByr)) {
										$chk = "checked";
										//$bayar = number_format($dt['JumlahTrn'],0,",",".");
										$bayar = number_format($dt['JumlahTrn'],2,",",".");
										//$bayar2 = round($dt['JumlahTrn'],0);
										$bayar2 = $dt['JumlahTrn'];
									} else {
										$chk = "";
									}
								} else {
									$chk = "";
								}
							} else {
								$chk = "";
							}
							
						} else {
							if (in_array($dt['NoFaktur'], $hutang)) {
								$chk = "checked";
							} else {
								$chk = "";
							}
						}
					} else {
						$chk = "";
					}
					$totNom = $totNom + $bayar2;
					// <input type='checkbox' name='byr[]' id='byr-".$no."' value='".round($dt['JumlahTrn'],0)."' onclick='pickHtg(".$no.",this.value);' $chk>
					$xml .= "<row id='".$no."'>";
					$xml .= "<cell><![CDATA[".utf8_encode($dt['NoFaktur'])."]]></cell>";
					$xml .= "<cell><![CDATA[".datenull($dt['TglTrnFaktur'])."]]></cell>";
					$xml .= "<cell><![CDATA[".datenull($dt['TglJthTmp'])."]]></cell>";
					$xml .= "<cell><![CDATA[".utf8_encode($dt['Keterangan'])."]]></cell>";
					$xml .= "<cell><![CDATA[<div style='text-align: right;padding:0'>".number_format($dt['JumlahTrn'],2,",",".")."</div>]]></cell>";
					#$xml .= "<cell><![CDATA[<div style='text-align: right;padding:0'>".$dt['JumlahTrn']."</div>]]></cell>";
					$xml .= "<cell><![CDATA[<div style='text-align: center;padding:0'>
							<input type='checkbox' name='byr[]' id='byr-".$no."' value='".$dt['JumlahTrn']."' onclick='pickHtg(".$no.",this.value);' $chk>
							<input type='hidden' id='NoFaktur-".$no."' value='".$dt['NoFaktur']."'>
							<input type='hidden' id='TglTrnFaktur-".$no."' value='".$dt['TglTrnFaktur']."'>
							<input type='hidden' id='TglJthTmp-".$no."' value='".$dt['TglJthTmp']."'>
							<input type='hidden' id='Keterangan-".$no."' value='".$dt['Keterangan']."'>
							<input type='hidden' id='JumlahTrn-".$no."' value='".$dt['JumlahTrn']."'>
							</div>]]></cell>";
					$xml .= "<cell><![CDATA[<div style='text-align: right;padding:0' id='txtnom_".$no."'>$bayar2</div>]]></cell>";
					$xml .= "</row>";
					$no++;
					
			} else {
				if ($cekada==0) {
					
					if ($nobukti!='') {
						if ($action=='import') {
							if (in_array($dt['NoFaktur'], $hutang)) {
								// (in_array(round($dt['JumlahTrn'],0), $jumlahTrn))
									// (in_array(round($dt['JumlahTrn'],0), $jumlahByr))
								if (in_array(number_format($dt['JumlahTrn'],2,".",""), $jumlahTrn)) {
									if (in_array(number_format($dt['JumlahTrn'],2,".",""), $jumlahByr)) {
										$chk = "checked";
										//$bayar = number_format($dt['JumlahTrn'],0,",",".");
										$bayar = number_format($dt['JumlahTrn'],2,",",".");
										//$bayar2 = round($dt['JumlahTrn'],0);
										$bayar2 = $dt['JumlahTrn'];
									} else {
										$chk = "";
									}
								} else {
									$chk = "";
								}
							} else {
								$chk = "";
							}
							
						} else {
							if (in_array($dt['NoFaktur'], $hutang)) {
								$chk = "checked";
							} else {
								$chk = "";
							}
						}
					} else {
						$chk = "";
					}
					$totNom = $totNom + $bayar2;
					// <input type='checkbox' name='byr[]' id='byr-".$no."' value='".round($dt['JumlahTrn'],0)."' onclick='pickHtg(".$no.",this.value);' $chk>
					$xml .= "<row id='".$no."'>";
					$xml .= "<cell><![CDATA[".utf8_encode($dt['NoFaktur'])."]]></cell>";
					$xml .= "<cell><![CDATA[".datenull($dt['TglTrnFaktur'])."]]></cell>";
					$xml .= "<cell><![CDATA[".datenull($dt['TglJthTmp'])."]]></cell>";
					$xml .= "<cell><![CDATA[".utf8_encode($dt['Keterangan'])."]]></cell>";
					$xml .= "<cell><![CDATA[<div style='text-align: right;padding:0'>".number_format($dt['JumlahTrn'],2,",",".")."</div>]]></cell>";
					#$xml .= "<cell><![CDATA[<div style='text-align: right;padding:0'>".$dt['JumlahTrn']."</div>]]></cell>";
					$xml .= "<cell><![CDATA[<div style='text-align: center;padding:0'>
							<input type='checkbox' name='byr[]' id='byr-".$no."' value='".$dt['JumlahTrn']."' onclick='pickHtg(".$no.",this.value);' $chk>
							<input type='hidden' id='NoFaktur-".$no."' value='".$dt['NoFaktur']."'>
							<input type='hidden' id='TglTrnFaktur-".$no."' value='".$dt['TglTrnFaktur']."'>
							<input type='hidden' id='TglJthTmp-".$no."' value='".$dt['TglJthTmp']."'>
							<input type='hidden' id='Keterangan-".$no."' value='".$dt['Keterangan']."'>
							<input type='hidden' id='JumlahTrn-".$no."' value='".$dt['JumlahTrn']."'>
							</div>]]></cell>";
					$xml .= "<cell><![CDATA[<div style='text-align: right;padding:0' id='txtnom_".$no."'>$bayar2</div>]]></cell>";
					$xml .= "</row>";
					$no++;
				}
				
			}
		}
	}
	$xml .= "</rows>";
	echo $xml;
	

	function getdata($KodeDealer, $kodelgn, $wherehutang){
		include '../inc/conn.php';
		
		//$sql = "select DISTINCT NoFaktur from DataEvoTagihan where isreject=0 and kodedealer = '".$KodeDealer."'";
		
		/*$sql = "select DISTINCT a.NoFaktur  nofak
				from DataEvoTagihan a
				inner join DataEvo b on a.nobukti = b.nobukti
				where isnull(a.isreject,0) = 0 and a.kodedealer = '".$KodeDealer."' and b.kode_vendor = '".$kodelgn."'";
		*/		
		/*$sql = "select DISTINCT a.NoFaktur + '_' + convert(varchar(20),a.JumlahTrn,1) + '_' + convert(varchar(10),a.TglTrnFaktur,104)  nofak
				from DataEvoTagihan a
				inner join DataEvo b on a.nobukti = b.nobukti
				where a.isreject=0 and a.kodedealer = '".$KodeDealer."' and b.kode_vendor = '".$kodelgn."'";	
		*/		
		/*$sql = "select DISTINCT a.NoFaktur, a.JumlahTrn,  convert(varchar(10),a.TglTrnFaktur,111) TglTrnFaktur
				from DataEvoTagihan a
				inner join DataEvo b on a.nobukti = b.nobukti
				where isnull(a.isreject,0)=0 and a.kodedealer = '".$KodeDealer."' and b.kode_vendor = '".$kodelgn."'";	
		*/
		$tableacc = "ACC00-".date("Ym");
		$sql = "select DISTINCT a.NoFaktur, a.JumlahTrn,  convert(varchar(10),a.TglTrnFaktur,111) TglTrnFaktur
				from DataEvoTagihan a
				inner join DataEvo b on a.nobukti = b.nobukti
				where isnull(a.isreject,0)=0 and a.kodedealer = '".$KodeDealer."' and b.kode_vendor = '".$kodelgn."'
				and a.NoFaktur in (
	
					select NoFaktur from [10.10.27.171].[".$tableacc."].dbo.Aptrn 
					where KodeLgn='".$kodelgn."'  ".$wherehutang."
					
				)
				";	
		
		mssql_query("SET ANSI_NULLS ON;");
		mssql_query("SET ANSI_WARNINGS ON;"); 		
		$rsl = mssql_query($sql);		
	
		$nf = "'',";
		//$nofaktur = "";
		//$jml = "";
		//$tgl = "";
		$where = "";
		// and JumlahTrn != '".$dt['JumlahTrn']."'
		$tagihan = array();
		while ($dt = mssql_fetch_array($rsl)) {
			//$nf .= "'".$dt['nofak']."',";
			//$nofaktur .= "'".$dt['NoFaktur']."',";
			//$jml .= "'".$dt['JumlahTrn']."',";
			//$tgl .= "'".$dt['TglTrnFaktur']."',";
			//$where .= " and (NoFaktur != '".$dt['NoFaktur']."'  and TglTrnFaktur != '".$dt['TglTrnFaktur']."') ";
			$tagihan = array("nofaktur"=>$dt['NoFaktur'], "tgltrnfaktur"=> $dt['TglTrnFaktur']);
		}
		
		
		//$nf_ = substr($nf, 0,strlen($nf)-1);
		//$nofaktur = substr($nofaktur, 0,strlen($nofaktur)-1);
		//$jml = substr($jml, 0,strlen($jml)-1);
		//$tgl = substr($tgl, 0,strlen($tgl)-1);
		
		#$hasil = array("nofaktur"=>$nofaktur, "jml" =>$jml, "tgl"=>$tgl);
		
		return $tagihan;
	}
	
	
	function cekdata($KodeDealer, $kodelgn, $nofaktur, $nobuktihutang, $tgltrn, $modif){
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