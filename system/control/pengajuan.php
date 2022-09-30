<?php
	session_start();
	require_once ('../inc/conn.php');
	
	$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : null;
	if ($_SERVER['HTTP_X_FORWARDED_FOR']){
		$ipaddr = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else{ 
		$ipaddr = $_SERVER['REMOTE_ADDR'];
	}
	
	
	if ($action=='new-hutang') {
		$IdUser = addslashes($_REQUEST['IdUser']);
		$KodeDealer = addslashes($_REQUEST['KodeDealer']);
		$Tipe = addslashes($_REQUEST['Tipe']);
		$divisi = addslashes($_REQUEST['divisi']);
		$IdAtasan = addslashes($_REQUEST['IdAtasan']);
		// $nobukti = addslashes("VP".$_REQUEST['nobukti']);
		$nobukti = getNoBukti($KodeDealer);
		$tgl_pengajuan = addslashes($_REQUEST['tgl_pengajuan']);
		$upload_file = addslashes($_REQUEST['upload_file']);
		$upload_fp = addslashes($_REQUEST['upload_fp']);
		$kode_vendor = addslashes($_REQUEST['kode_vendor']);
		$namaVendor = addslashes($_REQUEST['namaVendor']);
		$metode_bayar = addslashes($_REQUEST['metode_bayar']);
		$benificary_account = addslashes($_REQUEST['benificary_account']);
		$tgl_bayar = addslashes($_REQUEST['tgl_bayar']);
		$nama_bank = addslashes($_REQUEST['nama_bank']);
		$nama_pemilik = addslashes($_REQUEST['nama_pemilik']);
		$email_penerima = addslashes($_REQUEST['email_penerima']);
		$nama_alias = addslashes($_REQUEST['nama_alias']);
		$kode_bank_pengirim = addslashes($_REQUEST['kode_bank_pengirim']);
		$nama_bank_pengirim = addslashes($_REQUEST['nama_bank_pengirim']);
		$tf_from_account = addslashes($_REQUEST['tf_from_account']);
		$realisasi_nominal = str_replace(".", "", $_REQUEST['realisasi_nominal']);
		$kodeAkun = addslashes($_REQUEST['kodeAkun']);
		$namaAkun = addslashes($_REQUEST['namaAkun']);
		$tipeppn = addslashes($_REQUEST['tipeppn']);
		$dpp = str_replace(".", "", $_REQUEST['dpp']);
		$ppn = str_replace(".", "", $_REQUEST['ppn']);
		$npwp = addslashes($_REQUEST['npwp']);
		$no_fj = addslashes($_REQUEST['no_fj']);
		$tagihan = addslashes($_REQUEST['tagihan']);
		$trf_pajak = addslashes($_REQUEST['trf_pajak']);
		$htg_stl_pajak = str_replace(".", "", $_REQUEST['htg_stl_pajak']);
		$keterangan = addslashes($_REQUEST['keterangan']);
		$tipehutang = addslashes($_REQUEST['tipehutang']);
		$tipematerai = addslashes($_REQUEST['tipe_materai']);
		$nominal_materai = addslashes($_REQUEST['nominal_materai']);
		
		$kodeAkunMaterai = addslashes($_REQUEST['kodeAkunMaterai']);
		$namaAkunMaterai = addslashes($_REQUEST['namaAkunMaterai']);
		
		$ppn_persen = addslashes($_REQUEST['ppn_persen']);
		
								
		if (empty($tipematerai)) {
			$tipematerai = "N";
			$nominal_materai = 0;
		}
		$deptterkait = addslashes($_REQUEST['deptterkait']);
		
		
		mssql_query("BEGIN TRAN");
			$sql1 = "insert into DataEvo (kodedealer,tipe,nobukti,tgl_pengajuan,upload_file,upload_fp,
					kode_vendor,namaVendor,metode_bayar,benificary_account,tgl_bayar,			
					nama_bank,nama_pemilik,email_penerima,nama_alias,nama_bank_pengirim,tf_from_account,realisasi_nominal,
					kodeAkun,namaAkun,tipeppn,dpp,ppn,npwp,no_fj,htg_stl_pajak,keterangan,
					userentry,tglentry,status,is_ppn,tipehutang,divisi,IdAtasan,kode_bank_pengirim, 
					materai, tipematerai, deptterkait, kodeAkunMaterai,namaAkunMaterai, ipentry, useragent, nilaiPPn) 
					values 
				('$KodeDealer','$Tipe','$nobukti','$tgl_pengajuan','$upload_file','$upload_fp',
				'$kode_vendor','$namaVendor','$metode_bayar','$benificary_account','$tgl_bayar',
				'$nama_bank','$nama_pemilik','$email_penerima','$nama_alias','$nama_bank_pengirim','$tf_from_account','$realisasi_nominal',
				'$kodeAkun','$namaAkun','$tipeppn','$dpp','$ppn','$npwp','$no_fj','$htg_stl_pajak','$keterangan',
				'$IdUser',getdate(),'New',NULL,'$tipehutang','$divisi','$IdAtasan','$kode_bank_pengirim',
				'$nominal_materai','$tipematerai', '$deptterkait', '$kodeAkunMaterai','$namaAkunMaterai', 
				'".$ipaddr."', '".$_SERVER['HTTP_USER_AGENT']."', '".$ppn_persen."')";
			$query1 = mssql_query($sql1,$conns);

			$r = explode("_cn_", $tagihan);
			for ($i=0; $i < count($r); $i++) { 
				$s = explode("#", $r[$i]);
				$NoFaktur = $s[0];
				$TglTrnFaktur = $s[1];
				$TglJthTmp = $s[2];
				$Keterangan = $s[3];
				//$JumlahTrn = str_replace(".", "", $s[4]);
				$JumlahTrn = $s[4];

				if ($nobukti!='' and $NoFaktur!='' and $JumlahTrn!='') {
					$sql2 = "insert into DataEvoTagihan (nobukti,NoFaktur,TglTrnFaktur,TglJthTmp,Keterangan,JumlahTrn,kodedealer, tglentry, ipentry, useragent) 
						values ('$nobukti','$NoFaktur','$TglTrnFaktur','$TglJthTmp','$Keterangan','$JumlahTrn','$KodeDealer', getdate(),
						'".$ipaddr."', '".$_SERVER['HTTP_USER_AGENT']."')";
					$query2 = mssql_query($sql2,$conns);
				}
			}

			$v = explode("_cn_", $trf_pajak);
			for ($i=0; $i < count($v); $i++) { 
				$s = explode("#", $v[$i]);
				if ($s[1]=='Non Pph') {
					$query3 = true;
				} else {
					$nominal = str_replace(".", "", $s[0]);
					$jns_pph = $s[1];
					$tarif_persen = $s[2];
					$nilai_pph = str_replace(".", "", $s[3]);
					$akun_pph = $s[4];
					$keteranganAKun = $s[5];
					
					if (empty($keteranganAKun)) {
						$keteranganAKun = $keterangan;
					}
					
					if ($nobukti!='' and $nominal!='') {
						$sql3 = "insert into DataEvoPos (nobukti,nominal,jns_pph,tarif_persen,nilai_pph,akun_pph, keteranganAkun, tglentry, ipentry, useragent) 
						values ('$nobukti','$nominal','$jns_pph','$tarif_persen','$nilai_pph','$akun_pph', '$keteranganAKun',  
								getdate(), '".$ipaddr."', '".$_SERVER['HTTP_USER_AGENT']."')";
						$query3 = mssql_query($sql3,$conns);
					}
				}
			}

			/*if ($_SESSION['level']=="ADMIN") {
				if ($KodeDealer=='2010') {
					$sql4 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry) 
					values ('$nobukti','$KodeDealer','TAX',getdate())";
					$nextlvl = "TAX";
				} else {
					$sql4 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry) 
					values ('$nobukti','$KodeDealer','ACCOUNTING',getdate())";
					$nextlvl = "ACCOUNTING";
				}
			} else {
				$lvlnext = getNextLevel($KodeDealer);
				$sql4 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry) 
					values ('$nobukti','$KodeDealer','$lvlnext',getdate())";
				$nextlvl = $lvlnext;
			}*/
			if ($KodeDealer=='2010') {
				$sql4 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry) 
				values ('$nobukti','$KodeDealer','TAX',getdate())";
				$nextlvl = "TAX";
			} else {
				$sql4 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry) 
				values ('$nobukti','$KodeDealer','ACCOUNTING',getdate())";
				$nextlvl = "ACCOUNTING";
			}
			$query4 = mssql_query($sql4,$conns);

			// 
			if ($query1 && $query2 && $query3 && $query4) {
				mssql_query("COMMIT TRAN");
				
				$sql = "
					select namaUser as pengaju,department,a.nobukti,NamaDealer,a.tipe,tgl_pengajuan,metode_bayar,namaVendor,keterangan,
					case when a.tipe = 'HUTANG' then htg_stl_pajak else biaya_yg_dibyar end as nominal,a.IdAtasan 
					from DataEvo a inner join sys_user b on b.IdUser = a.userentry
					inner join SPK00..dodealer c on a.kodedealer = c.kodedealer
					where nobukti = '".$nobukti."'
				";
				$dt = mssql_fetch_array(mssql_query($sql));
				$svld = "
					select nik,email,no_tlp,namaUser as validator from sys_user 
					where kodedealer='".$KodeDealer."' and tipe='".$nextlvl."' and ISNULL(isDel,'')=''
				";
				$rvld = mssql_query($svld);
				$bodyIntra = ""; $nik=""; $email = ""; $no_tlp = ""; $bodyWa = "";
				while ($vld = mssql_fetch_array($rvld)) {
					$bodyIntra .= 'Kepada Yth. Bp/Ibu '.$vld['validator'].', ';
					$bodyIntra .= 'Kami informasikan permohonan Validasi Voucher Payment atas: ';
					$bodyIntra .= 'Nama Pengaju:'.$dt['pengaju'].', ';
					$bodyIntra .= 'Department: '.$dt['department'].', ';
					$bodyIntra .= 'Nomor Tagihan: '.$dt['nobukti'].', ';
					$bodyIntra .= 'Terimakasih untuk kerjasamanya.;';

					$bodyWa .= 'Mohon Validasi Voucher Payment '.$dt['nobukti'].' tanggal '.date('d/m/Y', strtotime($dt['tgl_pengajuan'])).', ';
					$bodyWa .= 'dari '.strtoupper($dt['pengaju']).' ('.$dt['department'].' Department '.$dt['NamaDealer'].'), ';
					//$bodyWa .= 'guna membayar tagihan '.$dt['namaVendor'].', sebesar: '.number_format($dt['nominal'],0,",",".").', untuk '.$dt['keterangan'].'. Terima kasih.;';
					$bodyWa .= 'guna membayar tagihan '.$dt['namaVendor'].', sebesar: '.number_format($dt['nominal'],0,",",".").', untuk '.$dt['keterangan'].'. Untuk melakukan validasi silahkan klik link http://evopay.nasmoco.net . Terima kasih.'; 
	

					$nik .= $vld['nik'].";";
					$email .= $vld['email'].";";
					$no_tlp .= $vld['no_tlp'].";";
				}

				$n_bodyIntra 	= substr($bodyIntra, 0, -1);
				$n_nik 			= substr($nik, 0, -1);
				$n_email 		= substr($email, 0, -1);
				$n_no_tlp 		= substr($no_tlp, 0, -1);
				$n_bodyWa 		= substr($bodyWa, 0, -1);

				$pesan = "1#Data berhasil disimpan !!#".$dt['nobukti']."#".$n_bodyIntra."#".$n_nik."#".$n_email."#".$n_no_tlp."#".$n_bodyWa;
			} else {
				if (!$query1) { $pesan = "0#Failed Query 1!<br/>".$sql1; }
				if (!$query2) { $pesan = "0#Failed Query 2!<br/>".$sql2; }
				if (!$query3) { $pesan = "0#Failed Query 3!<br/>".$sql3; }
				if (!$query4) { $pesan = "0#Failed Query 4!<br/>".$sql4; }
				mssql_query("ROLLBACK TRAN");
			}
		mssql_query("return");
		echo $pesan;
		
	} else if ($action=='edit-hutang') {
		$IdUser = addslashes($_REQUEST['IdUser']);
		$KodeDealer = addslashes($_REQUEST['KodeDealer']);
		$Tipe = addslashes($_REQUEST['Tipe']);
		$divisi = addslashes($_REQUEST['divisi']);
		$IdAtasan = addslashes($_REQUEST['IdAtasan']);
		$nobukti = addslashes("VP".$_REQUEST['nobukti']);
		$tgl_pengajuan = addslashes($_REQUEST['tgl_pengajuan']);
		$upload_file = addslashes($_REQUEST['upload_file']);
		$upload_fp = addslashes($_REQUEST['upload_fp']);
		$kode_vendor = addslashes($_REQUEST['kode_vendor']);
		$namaVendor = addslashes($_REQUEST['namaVendor']);
		$metode_bayar = addslashes($_REQUEST['metode_bayar']);
		$benificary_account = addslashes($_REQUEST['benificary_account']);
		$tgl_bayar = addslashes($_REQUEST['tgl_bayar']);
		$nama_bank = addslashes($_REQUEST['nama_bank']);
		$nama_pemilik = addslashes($_REQUEST['nama_pemilik']);
		$email_penerima = addslashes($_REQUEST['email_penerima']);
		$nama_alias = addslashes($_REQUEST['nama_alias']);
		$kode_bank_pengirim = addslashes($_REQUEST['kode_bank_pengirim']);
		$nama_bank_pengirim = addslashes($_REQUEST['nama_bank_pengirim']);
		$tf_from_account = addslashes($_REQUEST['tf_from_account']);
		$realisasi_nominal = str_replace(".", "", $_REQUEST['realisasi_nominal']);
		$kodeAkun = addslashes($_REQUEST['kodeAkun']);
		$namaAkun = addslashes($_REQUEST['namaAkun']);
		$tipeppn = addslashes($_REQUEST['tipeppn']);
		$dpp = str_replace(".", "", $_REQUEST['dpp']);
		$ppn = str_replace(".", "", $_REQUEST['ppn']);
		$npwp = addslashes($_REQUEST['npwp']);
		$no_fj = addslashes($_REQUEST['no_fj']);
		$tagihan = addslashes($_REQUEST['tagihan']);
		$trf_pajak = addslashes($_REQUEST['trf_pajak']);
		$htg_stl_pajak = str_replace(".", "", $_REQUEST['htg_stl_pajak']);
		$keterangan = addslashes($_REQUEST['keterangan']);
		$tipehutang = addslashes($_REQUEST['tipehutang']);
		$tipematerai = addslashes($_REQUEST['tipe_materai']);
		$nominal_materai = addslashes($_REQUEST['nominal_materai']);
		
		$kodeAkunMaterai = addslashes($_REQUEST['kodeAkunMaterai']);
		$namaAkunMaterai = addslashes($_REQUEST['namaAkunMaterai']);
		
		$ppn_persen = addslashes($_REQUEST['ppn_persen']);
								
		if (empty($tipematerai)) {
			$tipematerai = "N";
			$nominal_materai = 0;
		}
		$deptterkait = addslashes($_REQUEST['deptterkait']);
		
		mssql_query("BEGIN TRAN");
			mssql_query("delete from DataEvo where nobukti = '".$nobukti."'");
			/*$sql1 = "insert into DataEvo (kodedealer,tipe,nobukti,tgl_pengajuan,upload_file,upload_fp,
					kode_vendor,namaVendor,metode_bayar,benificary_account,tgl_bayar,
					nama_bank,nama_pemilik,email_penerima,nama_alias,nama_bank_pengirim,tf_from_account,realisasi_nominal,
					kodeAkun,namaAkun,tipeppn,dpp,ppn,npwp,no_fj,htg_stl_pajak,keterangan,
					userentry,tglentry,status,is_ppn,tipehutang,divisi,IdAtasan,kode_bank_pengirim,  
					materai, tipematerai, deptterkait, kodeAkunMaterai,namaAkunMaterai) ) 
					values 
					('$KodeDealer','$Tipe','$nobukti','$tgl_pengajuan','$upload_file','$upload_fp',
					'$kode_vendor','$namaVendor','$metode_bayar','$benificary_account','$tgl_bayar',
					'$nama_bank','$nama_pemilik','$email_penerima','$nama_alias','$nama_bank_pengirim','$tf_from_account','$realisasi_nominal',
					'$kodeAkun','$namaAkun','$tipeppn','$dpp','$ppn','$npwp','$no_fj','$htg_stl_pajak','$keterangan',
					'$IdUser',getdate(),'New',NULL,'$tipehutang','$divisi','$IdAtasan','$kode_bank_pengirim',
					'$nominal_materai', '$tipematerai', '$deptterkait', '$kodeAkunMaterai','$namaAkunMaterai')";
			*/
			$sql1 = "insert into DataEvo (kodedealer,tipe,nobukti,tgl_pengajuan,upload_file,upload_fp,
					kode_vendor,namaVendor,metode_bayar,benificary_account,tgl_bayar,			
					nama_bank,nama_pemilik,email_penerima,nama_alias,nama_bank_pengirim,tf_from_account,realisasi_nominal,
					kodeAkun,namaAkun,tipeppn,dpp,ppn,npwp,no_fj,htg_stl_pajak,keterangan,
					userentry,tglentry,status,is_ppn,tipehutang,divisi,IdAtasan,kode_bank_pengirim, 
					materai, tipematerai, deptterkait, kodeAkunMaterai,namaAkunMaterai, ipentry, useragent, nilaiPPn) 
					values 
				('$KodeDealer','$Tipe','$nobukti','$tgl_pengajuan','$upload_file','$upload_fp',
				'$kode_vendor','$namaVendor','$metode_bayar','$benificary_account','$tgl_bayar',
				'$nama_bank','$nama_pemilik','$email_penerima','$nama_alias','$nama_bank_pengirim','$tf_from_account','$realisasi_nominal',
				'$kodeAkun','$namaAkun','$tipeppn','$dpp','$ppn','$npwp','$no_fj','$htg_stl_pajak','$keterangan',
				'$IdUser',getdate(),'New',NULL,'$tipehutang','$divisi','$IdAtasan','$kode_bank_pengirim',
				'$nominal_materai','$tipematerai', '$deptterkait', '$kodeAkunMaterai','$namaAkunMaterai',
				'".$ipaddr."', '".$_SERVER['HTTP_USER_AGENT']."', '".$ppn_persen."')";
			$query1 = mssql_query($sql1,$conns);

			mssql_query("delete from DataEvoTagihan where nobukti = '".$nobukti."'");
			$r = explode("_cn_", $tagihan);
			for ($i=0; $i < count($r); $i++) { 
				$s = explode("#", $r[$i]);
				$NoFaktur = $s[0];
				$TglTrnFaktur = $s[1];
				$TglJthTmp = $s[2];
				$Keterangan = $s[3];
				//$JumlahTrn = str_replace(".", "", $s[4]);
				$JumlahTrn = $s[4];

				if ($nobukti!='' and $NoFaktur!='' and $JumlahTrn!='') {
					$sql2 = "insert into DataEvoTagihan (nobukti,NoFaktur,TglTrnFaktur,TglJthTmp,Keterangan,JumlahTrn,kodedealer, tglentry, ipentry, useragent) 
						values ('$nobukti','$NoFaktur','$TglTrnFaktur','$TglJthTmp','$Keterangan','$JumlahTrn','$KodeDealer',
						getdate(), '".$ipaddr."', '".$_SERVER['HTTP_USER_AGENT']."')";
					$query2 = mssql_query($sql2,$conns);
				}
			}

			mssql_query("delete from DataEvoPos where nobukti = '".$nobukti."'");
			$v = explode("_cn_", $trf_pajak);
			for ($i=0; $i < count($v); $i++) { 
				
				$s = explode("#", $v[$i]);
				if ($s[1]=='Non Pph') {
					$query3 = true;
				} else {
					$nominal = str_replace(".", "", $s[0]);
					$jns_pph = $s[1];
					$tarif_persen = $s[2];
					$nilai_pph = str_replace(".", "", $s[3]);
					$akun_pph = $s[4];
					$keteranganAKun = $s[5];
					
					if (empty($keteranganAKun)) {
						$keteranganAKun = $keterangan;
					}
					
					if ($nobukti!='' and $nominal!='') {
						$sql3 = "insert into DataEvoPos (nobukti,nominal,jns_pph,tarif_persen,nilai_pph,akun_pph, keteranganAkun, tglentry, ipentry, useragent) 
								values ('$nobukti','$nominal','$jns_pph','$tarif_persen','$nilai_pph','$akun_pph', '$keteranganAKun', 
								getdate(), '".$ipaddr."', '".$_SERVER['HTTP_USER_AGENT']."')";
						$query3 = mssql_query($sql3,$conns);
					}
				}
			}

			mssql_query("delete from DataEvoVal where nobukti = '".$nobukti."'");
			if ($KodeDealer=='2010') {
				$sql4 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry) 
				values ('$nobukti','$KodeDealer','TAX',getdate())";
			} else {
				$sql4 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry) 
				values ('$nobukti','$KodeDealer','ACCOUNTING',getdate())";
			}
			$query4 = mssql_query($sql4,$conns);

			// && $query3
			if ($query1 && $query2 && $query3 && $query4) {
				mssql_query("COMMIT TRAN");
				$sql = "
					select c.nik,c.email,c.no_tlp,c.namaUser as validator,d.namaUser as pengaju,
					d.department,e.NamaDealer,b.tipe,tgl_pengajuan,metode_bayar,a.nobukti,namaVendor,keterangan,
					case when b.tipe = 'HUTANG' then htg_stl_pajak else biaya_yg_dibyar end as nominal
					from DataEvoVal a
					inner join DataEvo b on a.nobukti=b.nobukti
					inner join sys_user c on c.tipe = a.level and (c.divisi=b.divisi or c.divisi='all') and ISNULL(c.isDel, '')=''
					inner join sys_user d on d.IdUser = b.userentry
					inner join SPK00..dodealer e on b.kodedealer = e.kodedealer
					where a.nobukti = '".$nobukti."' and ISNULL(validasi, '')='' and ISNULL(c.isDel,'')='' and ISNULL(d.isDel,'')=''
					and c.IdUser = case when a.level='SECTION HEAD' then b.IdAtasan else c.IdUser end
				";
				$dt = mssql_fetch_array(mssql_query($sql));
				$bodyIntra .= 'Kepada Yth. Bp/Ibu '.$dt['validator'].', ';
				$bodyIntra .= 'Kami informasikan permohonan Validasi Voucher Payment atas: ';
				$bodyIntra .= 'Nama Pengaju:'.$dt['pengaju'].', ';
				$bodyIntra .= 'Department: '.$dt['department'].', ';
				$bodyIntra .= 'Nomor Tagihan: '.$dt['nobukti'].', ';
				$bodyIntra .= 'Terimakasih untuk kerjasamanya.';

				$bodyWa .= 'Mohon Validasi Voucher Payment '.$dt['nobukti'].' tanggal '.date('d/m/Y', strtotime($dt['tgl_pengajuan'])).', ';
				$bodyWa .= 'dari '.strtoupper($dt['pengaju']).' ('.$dt['department'].' Department '.$dt['NamaDealer'].'), ';
				//$bodyWa .= 'guna membayar tagihan '.$dt['namaVendor'].', sebesar: '.number_format($dt['nominal'],0,",",".").', untuk '.$dt['keterangan'].'. Terima kasih.;';
				$bodyWa .= 'guna membayar tagihan '.$dt['namaVendor'].', sebesar: '.number_format($dt['nominal'],0,",",".").', untuk '.$dt['keterangan'].'. Untuk melakukan validasi silahkan klik link http://evopay.nasmoco.net . Terima kasih.';
	
				$pesan = "1#Data Save!!#".$dt['nobukti']."#".$bodyIntra."#".$dt['nik']."#".$dt['email']."#".$dt['no_tlp']."#".$bodyWa;
			} else {
				if (!$query1) { $pesan = "0#Failed Query 1!<br/>".$sql1; }
				if (!$query2) { $pesan = "0#Failed Query 2!<br/>".$sql2; }
				if (!$query3) { $pesan = "0#Failed Query 3!<br/>".$sql3; }
				if (!$query4) { $pesan = "0#Failed Query 4!<br/>".$sql4; }
				mssql_query("ROLLBACK TRAN");
			}
		mssql_query("return");
		echo $pesan;
		
	#----------------------------------------------------- BIAYA 	
	} else if ($action=='new-biaya') {
		$IdUser = addslashes($_REQUEST['IdUser']);
		$KodeDealer = addslashes($_REQUEST['KodeDealer']);
		$Tipe = addslashes($_REQUEST['Tipe']);
		$divisi = addslashes($_REQUEST['divisi']);
		$IdAtasan = addslashes($_REQUEST['IdAtasan']);
		$status = addslashes($_REQUEST['status']);
		// $nobukti = addslashes("VP".$_REQUEST['nobukti']);
		$nobukti = getNoBukti($KodeDealer);
		$tgl_pengajuan = addslashes($_REQUEST['tgl_pengajuan']);
		$upload_file = addslashes($_REQUEST['upload_file']);
		$upload_fp = addslashes($_REQUEST['upload_fp']);
		$kode_vendor = addslashes($_REQUEST['kode_vendor']);
		$namaVendor = addslashes($_REQUEST['namaVendor']);
		$metode_bayar = addslashes($_REQUEST['metode_bayar']);
		$benificary_account = addslashes($_REQUEST['benificary_account']);
		$tgl_bayar = addslashes($_REQUEST['tgl_bayar']);
		$nama_bank = addslashes($_REQUEST['nama_bank']);
		$nama_pemilik = addslashes($_REQUEST['nama_pemilik']);
		$email_penerima = addslashes($_REQUEST['email_penerima']);
		$nama_alias = addslashes($_REQUEST['nama_alias']);
		$kode_bank_pengirim = addslashes($_REQUEST['kode_bank_pengirim']);
		$nama_bank_pengirim = addslashes($_REQUEST['nama_bank_pengirim']);
		$tf_from_account = addslashes($_REQUEST['tf_from_account']);
		$realisasi_nominal = str_replace(".", "", $_REQUEST['realisasi_nominal']);
		$is_ppn = addslashes($_REQUEST['is_ppn']);
		$dpp = str_replace(".", "", $_REQUEST['dpp']);
		$ppn = str_replace(".", "", $_REQUEST['ppn']);
		$npwp = addslashes($_REQUEST['npwp']);
		$no_fj = addslashes($_REQUEST['no_fj']);
		$posbiaya = addslashes($_REQUEST['posbiaya']);
		$total_dpp = str_replace(".", "", $_REQUEST['total_dpp']);
		$biaya_yg_dibyar = str_replace(".", "", $_REQUEST['biaya_yg_dibyar']);
		$keterangan = addslashes($_REQUEST['keterangan']);
		
		$deptterkait = addslashes($_REQUEST['deptterkait']);
		$ppn_persen = addslashes($_REQUEST['ppn_persen']);
		
		mssql_query("BEGIN TRAN");
			/*$sql1 = "insert into DataEvo (nobukti,kodedealer,tipe,status,tgl_pengajuan,upload_file,upload_fp,kode_vendor,metode_bayar,benificary_account,tgl_bayar,nama_bank,nama_pemilik,email_penerima,nama_alias,nama_bank_pengirim,tf_from_account,realisasi_nominal,is_ppn,dpp,ppn,npwp,no_fj,total_dpp,biaya_yg_dibyar,keterangan,userentry,tglentry,namaVendor,divisi,IdAtasan,kode_bank_pengirim) 
			values 
			('$nobukti','$KodeDealer','$Tipe','$status','$tgl_pengajuan','$upload_file','$upload_fp','$kode_vendor','$metode_bayar','$benificary_account','$tgl_bayar','$nama_bank','$nama_pemilik','$email_penerima','$nama_alias','$nama_bank_pengirim','$tf_from_account','$realisasi_nominal','$is_ppn','$dpp','$ppn','$npwp','$no_fj','$total_dpp','$biaya_yg_dibyar','$keterangan','$IdUser',getdate(),'$namaVendor','$divisi','$IdAtasan','$kode_bank_pengirim')";
			*/
			$sql1 = "insert into DataEvo 
					(nobukti,kodedealer,tipe,status,tgl_pengajuan,upload_file,upload_fp,
					kode_vendor,metode_bayar,benificary_account,tgl_bayar,nama_bank,
					nama_pemilik,email_penerima,nama_alias,nama_bank_pengirim,
					tf_from_account,realisasi_nominal,is_ppn,dpp,ppn,npwp,no_fj,total_dpp,biaya_yg_dibyar,
					keterangan,userentry,tglentry,namaVendor,divisi,IdAtasan,kode_bank_pengirim, deptterkait, ipentry, useragent, nilaiPPn) 
					values 			
					('$nobukti','$KodeDealer','$Tipe','$status','$tgl_pengajuan','$upload_file','$upload_fp',
					'$kode_vendor','$metode_bayar','$benificary_account','$tgl_bayar','$nama_bank',
					'$nama_pemilik','$email_penerima','$nama_alias','$nama_bank_pengirim',
					'$tf_from_account','$realisasi_nominal','$is_ppn','$dpp','$ppn','$npwp','$no_fj','$total_dpp','$biaya_yg_dibyar',
					'$keterangan','$IdUser',getdate(),'$namaVendor','$divisi','$IdAtasan','$kode_bank_pengirim', '$deptterkait',
					'".$ipaddr."', '".$_SERVER['HTTP_USER_AGENT']."', '".$ppn_persen."')";
						
			$query1 = mssql_query($sql1,$conns);
			$r = explode("_cn_", $posbiaya);
			for ($i=0; $i < count($r); $i++) { 
				$s = explode("#", $r[$i]);
				$pos_biaya = $s[0];
				$nominal = str_replace(".", "", $s[1]);
				$jns_pph = $s[2];
				$tarif_persen = $s[3];
				$nilai_pph = str_replace(".", "", $s[4]);
				$ketAkun = $s[5];
				$akun_pph = $s[6];
				$keteranganAKun = $s[7];

				if (empty($keteranganAKun)) {
					$keteranganAKun = $keterangan;
				}
				
				if ($nobukti!='' and $nominal!='') {
					$sql2 = "insert into DataEvoPos (nobukti,pos_biaya,nominal,jns_pph,tarif_persen,nilai_pph,ketAkun,akun_pph, 
						tglentry, ipentry, useragent, keteranganAkun) 
						values ('$nobukti','$pos_biaya','$nominal','$jns_pph','$tarif_persen','$nilai_pph','$ketAkun','$akun_pph',
						getdate(), '".$ipaddr."', '".$_SERVER['HTTP_USER_AGENT']."', '$keteranganAKun')";
					$query2 = mssql_query($sql2,$conns);
				}
			}
			
			
			/*if ($_SESSION['level']=="ADMIN") {
				if ($KodeDealer=='2010') {
					$sql3 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry) 
					values ('$nobukti','$KodeDealer','TAX',getdate())";
					$lvl = "TAX";
				} else {
					$sql3 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry) 
					values ('$nobukti','$KodeDealer','ACCOUNTING',getdate())";
					$lvl = "ACCOUNTING";
				}
			} else {
				$lvlnext = getNextLevel($KodeDealer);
				$sql3 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry) 
					values ('$nobukti','$KodeDealer','$lvlnext',getdate())";
				$lvl = $lvlnext;
			}*/
			if ($KodeDealer=='2010') {
				$sql3 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry) 
				values ('$nobukti','$KodeDealer','TAX',getdate())";
				$lvl = "TAX";
			} else {
				$sql3 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry) 
				values ('$nobukti','$KodeDealer','ACCOUNTING',getdate())";
				$lvl = "ACCOUNTING";
			}
			$query3 = mssql_query($sql3,$conns);
			
			// 			
			if ($query1 && $query2 && $query3) {
				mssql_query("COMMIT TRAN");
				$sql = "
					select namaUser as pengaju,department,a.nobukti,NamaDealer,a.tipe,tgl_pengajuan,metode_bayar,namaVendor,keterangan,
					case when a.tipe = 'HUTANG' then htg_stl_pajak else biaya_yg_dibyar end as nominal,a.IdAtasan,a.kodedealer,a.divisi 
					from DataEvo a inner join sys_user b on b.IdUser = a.userentry
					inner join SPK00..dodealer c on a.kodedealer = c.kodedealer
					where nobukti = '".$nobukti."'
				";
				$dt = mssql_fetch_array(mssql_query($sql));

				$sql2 = "
					select nik,email,no_tlp,namaUser as validator from sys_user 
					where kodedealer='".$dt['kodedealer']."' and tipe='".$lvl."'  and ISNULL(isDel, '')=''
				";
				$rvld = mssql_query($sql2);
				$bodyIntra = ""; $nik=""; $email = ""; $no_tlp = ""; $bodyWa = "";
				while ($vld = mssql_fetch_array($rvld)) {
					$bodyIntra .= 'Kepada Yth. Bp/Ibu '.$vld['validator'].', ';
					$bodyIntra .= 'Kami informasikan permohonan Validasi Voucher Payment atas: ';
					$bodyIntra .= 'Nama Pengaju:'.$dt['pengaju'].', ';
					$bodyIntra .= 'Department: '.$dt['department'].', ';
					$bodyIntra .= 'Nomor Tagihan: '.$dt['nobukti'].', ';
					$bodyIntra .= 'Terimakasih untuk kerjasamanya.;';

					$bodyWa .= 'Mohon Validasi Voucher Payment '.$dt['nobukti'].' tanggal '.date('d/m/Y', strtotime($dt['tgl_pengajuan'])).', ';
					$bodyWa .= 'dari '.strtoupper($dt['pengaju']).' ('.$dt['department'].' Department '.$dt['NamaDealer'].'), ';
					//$bodyWa .= 'guna membayar tagihan '.$dt['namaVendor'].', sebesar: '.number_format($dt['nominal'],0,",",".").', untuk '.$dt['keterangan'].'. Terima kasih.;';
					$bodyWa .= 'guna membayar tagihan '.$dt['namaVendor'].', sebesar: '.number_format($dt['nominal'],0,",",".").', untuk '.$dt['keterangan'].'. Untuk melakukan validasi silahkan klik link http://evopay.nasmoco.net . Terima kasih.';
	

					$nik .= $vld['nik'].";";
					$email .= $vld['email'].";";
					$no_tlp .= $vld['no_tlp'].";";
				}

				$n_bodyIntra 	= substr($bodyIntra, 0, -1);
				$n_nik 			= substr($nik, 0, -1);
				$n_email 		= substr($email, 0, -1);
				$n_no_tlp 		= substr($no_tlp, 0, -1);
				$n_bodyWa 		= substr($bodyWa, 0, -1);

				$pesan .= "1#Data Save!!#".$dt['nobukti']."#".$n_bodyIntra."#".$n_nik."#".$n_email."#".$n_no_tlp."#".$n_bodyWa;
			} else {
				if (!$query1) { $pesan = "0#Failed Query 1!<br/>".$sql1; }
				if (!$query2) { $pesan = "0#Failed Query 2!<br/>".$sql2; }
				if (!$query3) { $pesan = "0#Failed Query 3!<br/>".$sql3; }
				mssql_query("ROLLBACK TRAN");
			}
		mssql_query("return");
		echo $pesan;
		
	} else if ($action=='edit-biaya') {
		$IdUser = addslashes($_REQUEST['IdUser']);
		$KodeDealer = addslashes($_REQUEST['KodeDealer']);
		$Tipe = addslashes($_REQUEST['Tipe']);
		$divisi = addslashes($_REQUEST['divisi']);
		$IdAtasan = addslashes($_REQUEST['IdAtasan']);
		$status = addslashes($_REQUEST['status']);
		$nobukti = addslashes("VP".$_REQUEST['nobukti']);
		$tgl_pengajuan = addslashes($_REQUEST['tgl_pengajuan']);
		$upload_file = addslashes($_REQUEST['upload_file']);
		$upload_fp = addslashes($_REQUEST['upload_fp']);
		$kode_vendor = addslashes($_REQUEST['kode_vendor']);
		$namaVendor = addslashes($_REQUEST['namaVendor']);
		$metode_bayar = addslashes($_REQUEST['metode_bayar']);
		$benificary_account = addslashes($_REQUEST['benificary_account']);
		$tgl_bayar = addslashes($_REQUEST['tgl_bayar']);
		$nama_bank = addslashes($_REQUEST['nama_bank']);
		$nama_pemilik = addslashes($_REQUEST['nama_pemilik']);
		$email_penerima = addslashes($_REQUEST['email_penerima']);
		$nama_alias = addslashes($_REQUEST['nama_alias']);
		$kode_bank_pengirim = addslashes($_REQUEST['kode_bank_pengirim']);
		$nama_bank_pengirim = addslashes($_REQUEST['nama_bank_pengirim']);
		$tf_from_account = addslashes($_REQUEST['tf_from_account']);
		$realisasi_nominal = str_replace(".", "", $_REQUEST['realisasi_nominal']);
		$is_ppn = addslashes($_REQUEST['is_ppn']);
		$dpp = str_replace(".", "", $_REQUEST['dpp']);
		$ppn = str_replace(".", "", $_REQUEST['ppn']);
		$npwp = addslashes($_REQUEST['npwp']);
		$no_fj = addslashes($_REQUEST['no_fj']);
		$posbiaya = addslashes($_REQUEST['posbiaya']);
		$total_dpp = str_replace(".", "", $_REQUEST['total_dpp']);
		$biaya_yg_dibyar = str_replace(".", "", $_REQUEST['biaya_yg_dibyar']);
		$keterangan = addslashes($_REQUEST['keterangan']);
		
		$deptterkait = addslashes($_REQUEST['deptterkait']);
		$ppn_persen = addslashes($_REQUEST['ppn_persen']);
		
		mssql_query("BEGIN TRAN");
			mssql_query("delete from DataEvo where nobukti = '".$nobukti."'");
			
			/*$sql1 = "insert into DataEvo (nobukti,kodedealer,tipe,status,tgl_pengajuan,upload_file,upload_fp,kode_vendor,metode_bayar,benificary_account,tgl_bayar,nama_bank,nama_pemilik,email_penerima,nama_alias,nama_bank_pengirim,tf_from_account,realisasi_nominal,is_ppn,dpp,ppn,npwp,no_fj,total_dpp,biaya_yg_dibyar,keterangan,userentry,tglentry,namaVendor,divisi,IdAtasan,kode_bank_pengirim) 
			values 
			('$nobukti','$KodeDealer','$Tipe','$status','$tgl_pengajuan','$upload_file','$upload_fp','$kode_vendor','$metode_bayar','$benificary_account','$tgl_bayar','$nama_bank','$nama_pemilik','$email_penerima','$nama_alias','$nama_bank_pengirim','$tf_from_account','$realisasi_nominal','$is_ppn','$dpp','$ppn','$npwp','$no_fj','$total_dpp','$biaya_yg_dibyar','$keterangan','$IdUser',getdate(),'$namaVendor','$divisi','$IdAtasan','$kode_bank_pengirim')";*/
			
			$sql1 = "insert into DataEvo (nobukti,kodedealer,tipe,status,tgl_pengajuan,upload_file,upload_fp,kode_vendor,metode_bayar,benificary_account,tgl_bayar,nama_bank,nama_pemilik,email_penerima,nama_alias,nama_bank_pengirim,tf_from_account,realisasi_nominal,is_ppn,dpp,ppn,npwp,no_fj,total_dpp,biaya_yg_dibyar,keterangan,userentry,tglentry,namaVendor,divisi,IdAtasan,kode_bank_pengirim, deptterkait, ipentry, useragent, nilaiPPn) 
			values 
			('$nobukti','$KodeDealer','$Tipe','$status','$tgl_pengajuan','$upload_file','$upload_fp','$kode_vendor','$metode_bayar','$benificary_account','$tgl_bayar','$nama_bank','$nama_pemilik','$email_penerima','$nama_alias','$nama_bank_pengirim','$tf_from_account','$realisasi_nominal','$is_ppn','$dpp','$ppn','$npwp','$no_fj','$total_dpp','$biaya_yg_dibyar','$keterangan','$IdUser',getdate(),'$namaVendor','$divisi','$IdAtasan','$kode_bank_pengirim', '$deptterkait',
			'".$ipaddr."', '".$_SERVER['HTTP_USER_AGENT']."', '".$ppn_persen."')";
			
			$query1 = mssql_query($sql1,$conns);
			
			mssql_query("delete from DataEvoPos where nobukti = '".$nobukti."'");
			$r = explode("_cn_", $posbiaya);
			for ($i=0; $i < count($r); $i++) { 
				$s = explode("#", $r[$i]);
				$pos_biaya = $s[0];
				$nominal = str_replace(".", "", $s[1]);
				$jns_pph = $s[2];
				$tarif_persen = $s[3];
				$nilai_pph = str_replace(".", "", $s[4]);
				$ketAkun = $s[5];
				$akun_pph = $s[6];
				$keteranganAKun = $s[7];
				
				if (empty($keteranganAKun)) {
					$keteranganAKun = $keterangan;
				}
				
				if ($nobukti!='' and $nominal!='') {					
					$sql2 = "insert into DataEvoPos (nobukti,pos_biaya,nominal,jns_pph,tarif_persen,nilai_pph,ketAkun,akun_pph, 
						tglentry, ipentry, useragent, keteranganAkun) 
						values ('$nobukti','$pos_biaya','$nominal','$jns_pph','$tarif_persen','$nilai_pph','$ketAkun','$akun_pph',
						getdate(), '".$ipaddr."', '".$_SERVER['HTTP_USER_AGENT']."', '$keteranganAKun')";
					$query2 = mssql_query($sql2,$conns);
				}
			}
			
			mssql_query("delete from DataEvoVal where nobukti = '".$nobukti."'");
			if ($KodeDealer=='2010') {
				$sql3 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry) 
				values ('$nobukti','$KodeDealer','TAX',getdate())";
			} else {
				$sql3 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry) 
				values ('$nobukti','$KodeDealer','ACCOUNTING',getdate())";
			}
			$query3 = mssql_query($sql3,$conns);
			
			//  
			if ($query1 && $query2 && $query3) {
				mssql_query("COMMIT TRAN");
				$sql = "
					select c.nik,c.email,c.no_tlp,c.namaUser as validator,d.namaUser as pengaju,
					d.department,e.NamaDealer,b.tipe,tgl_pengajuan,metode_bayar,a.nobukti,namaVendor,keterangan,
					case when b.tipe = 'HUTANG' then htg_stl_pajak else biaya_yg_dibyar end as nominal
					from DataEvoVal a
					inner join DataEvo b on a.nobukti=b.nobukti
					inner join sys_user c on c.tipe = a.level and (c.divisi=b.divisi or c.divisi='all') and ISNULL(c.isDel, '')=''
					inner join sys_user d on d.IdUser = b.userentry
					inner join SPK00..dodealer e on b.kodedealer = e.kodedealer
					where a.nobukti = '".$nobukti."' and ISNULL(validasi, '')='' and ISNULL(c.isDel,'')='' and ISNULL(d.isDel,'')=''
					and c.IdUser = case when a.level='SECTION HEAD' then b.IdAtasan else c.IdUser end
				";
				$dt = mssql_fetch_array(mssql_query($sql));
				$body .= 'Kepada Yth. Bp/Ibu '.$dt['validator'].', ';
				$body .= 'Kami informasikan permohonan Validasi Voucher Payment atas: ';
				$body .= 'Nama Pengaju:'.$dt['pengaju'].', ';
				$body .= 'Department: '.$dt['department'].', ';
				$body .= 'Nomor Tagihan: '.$dt['nobukti'].', ';
				$body .= 'Terimakasih untuk kerjasamanya.';
				$pesan = "1#Data Save!!#".$dt['nobukti']."#".$body."#".$dt['nik']."#".$dt['email']."#".$dt['no_tlp'];
			} else {
				if (!$query1) { $pesan = "0#Failed Query 1!<br/>".$sql1; }
				if (!$query2) { $pesan = "0#Failed Query 2!<br/>".$sql2; }
				if (!$query3) { $pesan = "0#Failed Query 3!<br/>".$sql3; }
				mssql_query("ROLLBACK TRAN");
			}
		mssql_query("return");
		echo $pesan;
		
	} else if ($action == 'transfer') {
		$evo_id = addslashes($_REQUEST['evo_id']);
		$noCsv = getNumberCvs();
		$metode = addslashes($_REQUEST['metode']);
		mssql_query("BEGIN TRAN");
			$sql = "select nobukti from DataEvo where evo_id in (".$evo_id.")";
			$rsl = mssql_query($sql);
			while ($dt = mssql_fetch_array($rsl)) {
				$que = "insert into DataEvoTransfer (noCsv,nobukti,tglentry,metode_transfer, ipentry, useragent) 
					values ('".$noCsv."','".$dt['nobukti']."',getdate(),'".$metode."', '".$ipaddr."', '".$_SERVER['HTTP_USER_AGENT']."')";
				$prc = mssql_query($que);
			}
			if ($prc) {
				mssql_query("COMMIT TRAN");
				$pesan = $noCsv;
			} else {
				if (!$prc) { $pesan = "0#Failed Query 1!<br/>".$que; }
				mssql_query("ROLLBACK TRAN");
			}
		mssql_query("return");
		echo $pesan;
	} else if ($action == 'edit-transfer') {
		$data = addslashes($_REQUEST['data']);
		$r = explode("_cn_", $data);
		$pesan = "";
		mssql_query("BEGIN TRAN");
		for ($i=0; $i < count($r); $i++) { 
			$s = explode("#", $r[$i]);
			$upd = "update DataEvoTransfer set bayar_via = '".$s[2]."', konfirm_email = '".$s[4]."', 
					jenis_penerima = '".$s[5]."', kota_bank_penerima = '".$s[6]."', kode_dukcapil = '".$s[7]."' where evotf_id = '".$s[0]."'";
			$prc = mssql_query($upd);

			$upd2 = "update DataEvo set keterangan = '".$s[1]."', email_penerima = '".$s[3]."' 
					where nobukti in (select nobukti from DataEvoTransfer where evotf_id='".$s[0]."')";
			$prc2 = mssql_query($upd2);
		}
		if ($prc and $prc2) {
			mssql_query("COMMIT TRAN");
			$pesan = "Data Save!";
		} else {
			if (!$prc) { $pesan .= "0#Failed Query 1!<br/>".$upd; }
			if (!$prc2) { $pesan .= "0#Failed Query 2!<br/>".$upd2; }
			mssql_query("ROLLBACK TRAN");
		}
		mssql_query("return");
		echo $pesan;
	} else if ($action == 'delete-transfer') {
		$evotf_id = addslashes($_REQUEST['data']);
		mssql_query("BEGIN TRAN");
			$upd = "update DataEvoTransfer set is_del=1, tgldelete=getdate() where evotf_id in (".$evotf_id.")";
			$prc = mssql_query($upd);
			if ($prc) {
				mssql_query("COMMIT TRAN");
				$pesan = "Data Save!";
			} else {
				if (!$prc) { $pesan .= "0#Failed Query 1!<br/>".$upd; }
				mssql_query("ROLLBACK TRAN");
			}
		mssql_query("return");
		echo $pesan;
	} else if ($action == 'edit-transfer-mcm') {
		$data = addslashes($_REQUEST['data']);
		$tgl_mcm = addslashes($_REQUEST['tgl_mcm']);
		$r = explode("_cn_", $data);
		$pesan = "";
		mssql_query("BEGIN TRAN");
		for ($i=0; $i < count($r); $i++) { 
			$s = explode("#", $r[$i]);
			$upd = "update DataEvoTransfer set Alamat='".$s[1]."', layanan_transfer='".$s[2]."', kode_rtgs_kliring='".$s[3]."', 
					kota_cbg_buka='".$s[4]."', konfirm_email='".$s[5]."', charger_inst='".$s[7]."', tgl_mcm = '".$tgl_mcm."' 
					where evotf_id = '".$s[0]."'";
			$prc = mssql_query($upd);

			$upd2 = "update DataEvo set email_penerima = '".$s[6]."' 
					where nobukti in (select nobukti from DataEvoTransfer where evotf_id='".$s[0]."')";
			$prc2 = mssql_query($upd2);
		}
		echo $upd;
		if ($prc and $prc2) {
			mssql_query("COMMIT TRAN");
			$pesan = "Data Save!";
		} else {
			if (!$prc) { $pesan .= "0#Failed Query 1!<br/>".$upd; }
			if (!$prc2) { $pesan .= "0#Failed Query 2!<br/>".$upd2; }
			mssql_query("ROLLBACK TRAN");
		}
		mssql_query("return");
		echo $pesan;
	} else if ($action == 'delete-transfer') {
		$evotf_id = addslashes($_REQUEST['data']);
		mssql_query("BEGIN TRAN");
			$upd = "update DataEvoTransfer set is_del=1, tgldelete=getdate() where evotf_id in (".$evotf_id.")";
			$prc = mssql_query($upd);
			if ($prc) {
				mssql_query("COMMIT TRAN");
				$pesan = "Data Save!";
			} else {
				if (!$prc) { $pesan .= "0#Failed Query 1!<br/>".$upd; }
				mssql_query("ROLLBACK TRAN");
			}
		mssql_query("return");
		echo $pesan;
	} else if ($action == 'getReject') {
		$nobukti = isset($_REQUEST['nobukti']) ? $_REQUEST['nobukti'] : null;
		$sql = "
			select evo_id,nobukti,a.kodedealer,NamaDealer,tipe,status,tgl_pengajuan,upload_file,upload_fp,kode_vendor,namaVendor,metode_bayar,
			benificary_account,tgl_bayar,nama_bank,nama_pemilik,email_penerima,nama_alias,nama_bank_pengirim,tf_from_account,realisasi_nominal,
			is_ppn,dpp,ppn,a.npwp,no_fj,total_dpp,biaya_yg_dibyar,keterangan,divisi,idAtasan,
			(select ketreject from DataEvoVal where nobukti = a.nobukti and validasi='Reject') as alasanreject,kode_bank_pengirim
			from DataEvo a inner join SPK00..dodealer b on a.kodedealer=b.kodedealer
			where nobukti = '".$nobukti."'
		";
		$dt = mssql_fetch_array(mssql_query($sql));

		$kodedealer = isset($_REQUEST['kodedealer']) ? $_REQUEST['kodedealer'] : null;
		$IdUser = isset($_REQUEST['IdUser']) ? $_REQUEST['IdUser'] : null;
		$atasan = mssql_fetch_array(mssql_query("select IdAtasan from sys_user where IdUser = '".$IdUser."'"));
		if ($atasan['IdAtasan']=='all') { $boss = ""; } else { $boss = "and IdUser = '".$atasan['IdAtasan']."'"; }
		// $sql2 = "select * from sys_user where tipe = 'SECTION HEAD' and divisi = '".$dt['divisi']."' and KodeDealer = '".$kodedealer."' $boss";
		//$sql2 = "select * from sys_user where (tipe = 'SECTION HEAD' or tipe = 'ADH') and (divisi = '".$dt['divisi']."' or divisi = 'all') and KodeDealer = '".$kodedealer."'$boss";
		$sql2 = "select * from sys_user where KodeDealer = '".$kodedealer."' $boss";
		
		$rsl2 = mssql_query($sql2);
		$atasan = "<option value=''>- Pilih -</option>";
		while ($dt2 = mssql_fetch_array($rsl2)) {
			$atasan.= "<option value='".$dt2['IdUser']."'>".$dt2['namaUser']."</option>";
		}

		$sql3 = "select * from DataEvoPos where nobukti = '".$nobukti."' order by evopos_id asc";
		$rsl3 = mssql_query($sql3);
		$no = 1;
		$posbiaya = "";
		while ($dt3 = mssql_fetch_array($rsl3)) {
			$posbiaya .='
				<div class="form-group">
				    <div class="col-sm-3">
				        <label class="control-label">Pos Biaya</label>
				        <div class="input-group">
				            <input type="hidden" name="posbiaya[]" id="kodeAkun_'.$no.'" value="'.$dt3['pos_biaya'].'"/>
				            <input type="text" class="form-control" id="ketAkun_'.$no.'" value="'.$dt3['ketAkun'].'" readonly />
				            <span class="input-group-addon" style="padding: 0; min-width: 0;">
				                <button type="button" style="padding: 2px 10px; border: 0;" onclick="getAkun('.$no.');">
				                	<i class="fa fa-search"></i>
				                </button>
				            </span>
				        </div>
				    </div>
				    <div class="col-sm-3">
				    	<label class="control-label">Nominal</label>
				    	<input type="text" class="form-control number" id="nominal_'.$no.'" value="'.number_format($dt3['nominal'],0,",",".").'" onchange="nominal_Rej('.$no.');"/>
				    </div>
				    <div class="col-sm-3">
				        <label class="control-label">Tarif Pajak</label>
				        <select class="form-control" id="trfPajak_'.$no.'" onchange="trfPajak_Rej('.$no.');">';
				        $sql4 = "select jns_pph,tarif_persen from settingPph where npwp = '".$dt['is_ppn']."' order by jns_pph,tarif_persen asc";
						$rsl4 = mssql_query($sql4);
						$posbiaya .='<option value="Non Pph#0#00000000">Non Pph</option>';
						while ($dt4 = mssql_fetch_array($rsl4)) {
							if ($dt['is_ppn']=='0') {
								if ($dt4['jns_pph']=='Pph 4 Ayat 2') {
									$jns = "non_pph_4";
								} else {
									$jns = "non_".str_replace(' ', '_', strtolower($dt4['jns_pph']));
								}
							} else if ($dt['is_ppn']=='1') {
								if ($dt4['jns_pph']=='Pph 4 Ayat 2') {
									$jns = "pph_4";
								} else {
									$jns = str_replace(' ', '_', strtolower($dt4['jns_pph']));
								}
							}
							$akun = mssql_fetch_array(mssql_query("select ".$jns." as akun from settingAkun where id=1"));
							$oldpos = $dt3['jns_pph']."#".$dt3['tarif_persen']."#".$dt3['akun_pph'];
							$newpos = $dt4['jns_pph']."#".$dt4['tarif_persen']."#".$akun['akun'];
							$plh = ($oldpos==$newpos)?"selected" : "";
							$posbiaya .='<option value="'.$dt4['jns_pph'].'#'.$dt4['tarif_persen'].'#'.$akun['akun'].'" '.$plh.' >'.$dt4['jns_pph'].' ('.$dt4['tarif_persen'].'%)</option>';
						}
				        $posbiaya .='
				        </select>
				        <input type="hidden" id="jns_pph_'.$no.'" value="'.$dt3['jns_pph'].'" /> 
				        <input type="hidden" id="tarif_persen_'.$no.'" value="'.$dt3['tarif_persen'].'" />
				        <input type="hidden" id="akun_pph_'.$no.'" value="'.$dt3['akun_pph'].'"/>
				    </div>
				    <div class="col-sm-3">
				        <label class="control-label">Nilai Pph</label>
				        <input type="text" class="form-control" id="nilaiPph_'.$no.'" value="'.number_format($dt3['nilaiPph'],0,",",".").'" readonly />
				    </div>
				</div>
			';
			$no++;
		}

		//$data .= date('m / d / Y', strtotime($dt['tgl_pengajuan']))."_cn_";
		$data .= $dt['tgl_pengajuan']."_cn_";
		$data .= $dt['upload_file']."_cn_";
		$data .= $dt['upload_fp']."_cn_";
		$data .= $dt['kode_vendor']."_cn_";
		$data .= $dt['namaVendor']."_cn_";
		$data .= $dt['metode_bayar']."_cn_";
		$data .= $dt['benificary_account']."_cn_";
		$data .= $dt['tgl_bayar']."_cn_";
		$data .= $dt['nama_bank']."_cn_";
		$data .= $dt['nama_pemilik']."_cn_";
		$data .= $dt['email_penerima']."_cn_";
		$data .= $dt['nama_alias']."_cn_";
		$data .= $dt['nama_bank_pengirim']."_cn_";
		$data .= $dt['tf_from_account']."_cn_";
		$data .= $dt['realisasi_nominal']."_cn_";
		$data .= $dt['is_ppn']."_cn_";
		$data .= number_format($dt['dpp'],0,",",".")."_cn_";
		$data .= number_format($dt['ppn'],0,",",".")."_cn_";
		$data .= $dt['npwp']."_cn_";
		$data .= $dt['no_fj']."_cn_";
		$data .= number_format($dt['total_dpp'],0,",",".")."_cn_";
		$data .= number_format($dt['biaya_yg_dibyar'],0,",",".")."_cn_";
		$data .= $dt['keterangan']."_cn_";
		$data .= $dt['alasanreject']."_cn_";
		$data .= $dt['divisi']."_cn_";
		$data .= $dt['idAtasan']."_cn_";
		$data .= $atasan."_cn_";
		$data .= $posbiaya."_cn_";
		$data .= $dt['kode_bank_pengirim'];
		echo $data;
	} else if ($action == 'getTipehutang') {
		$KodeDealer = isset($_REQUEST['KodeDealer']) ? $_REQUEST['KodeDealer'] : null;
		if ($KodeDealer=='2010') {
			$sql = "select idHtg,nama from sys_hutang where posisi = 'HO'";
		} else {
			$sql = "select idHtg,nama from sys_hutang where posisi = 'Dealer'";
		}
		$rsl = mssql_query($sql);
		echo "<option value=''>- Pilih -</option>";
		while ($dt = mssql_fetch_array($rsl)) {
			echo "<option value='$dt[nama]'>$dt[nama]</option>";
		}
		
	} else if ($action == 'getTipehutangDept') {
		$KodeDealer = isset($_REQUEST['KodeDealer']) ? $_REQUEST['KodeDealer'] : null;
		$nama = isset($_REQUEST['nama']) ? $_REQUEST['nama'] : null;
		if ($KodeDealer=='2010') {
			$sql = "select idHtg,nama,dept from sys_hutang where posisi = 'HO' and nama = '".$nama."'";
		} else {
			$sql = "select idHtg,nama,dept from sys_hutang where posisi = 'Dealer' and nama = '".$nama."'";
		}
		$rsl = mssql_query($sql);
		while ($dt = mssql_fetch_array($rsl)) {
			echo $dt['dept'];
		}
		
	} else if ($action == 'getDivisi') {
		//$KodeDealer = isset($_REQUEST['KodeDealer']) ? $_REQUEST['KodeDealer'] : null;
		$kodedealer = isset($_REQUEST['kodedealer']) ? $_REQUEST['kodedealer'] : null;
		$div = isset($_REQUEST['div']) ? $_REQUEST['div'] : null;
		if ($kodedealer=='2010') { $is_dealer = "0"; } else { $is_dealer = "1"; }
		if ($div=='all') { $divisi = ""; } else { $divisi = "and nama_div = '".$div."'"; }
		$sql = "select * from sys_divisi where is_dealer = '".$is_dealer."' $divisi";
		$rsl = mssql_query($sql);
		//echo "<option value=''>- Pilih -</option>";
		while ($dt = mssql_fetch_array($rsl)) {
			echo "<option value='".$dt['nama_div']."'>".$dt['nama_div']."</option>";
		}
	
	} else if ($action == 'getDepartement') {
		//$KodeDealer = isset($_REQUEST['KodeDealer']) ? $_REQUEST['KodeDealer'] : null;
		$kodedealer = isset($_REQUEST['kodedealer']) ? $_REQUEST['kodedealer'] : null;
		$div = isset($_REQUEST['div']) ? $_REQUEST['div'] : null;
		if ($kodedealer=='2010') { $is_dealer = "0"; } else { $is_dealer = "1"; }
		//if ($div=='all') { $divisi = ""; } else { $divisi = "and nama_div = '".$div."'"; }
		/*$sql = "select id_sys_dept, nama_dept from sys_department a
				left join sys_divisi b on a.id_sys_div = b.id_sys_div
				where b.is_dealer = '".$is_dealer."' and nama_dept in (
															select b.department from DeptTerkait a
															inner join sys_user b on a.iduser = b.iduser) ";*/
															
		$sql = "select id_sys_dept, nama_dept 
				from sys_department a
				left join sys_divisi b on a.id_sys_div = b.id_sys_div
				where b.is_dealer = '".$is_dealer."' and nama_dept in (
															select b.department from DeptTerkait a
															inner join sys_user b on a.iduser = b.iduser) 
				and a.is_aktif = '1'";
				
		$rsl = mssql_query($sql);
		echo "<option value=''>- Pilih -</option>";
		while ($dt = mssql_fetch_array($rsl)) {
			echo "<option value='".$dt['nama_dept']."'>".$dt['nama_dept']."</option>";
		}
		
	} else if ($action == 'getDepartementTerkait') {
		//$KodeDealer = isset($_REQUEST['KodeDealer']) ? $_REQUEST['KodeDealer'] : null;
		$kodedealer = isset($_REQUEST['kodedealer']) ? $_REQUEST['kodedealer'] : null;
		$div = isset($_REQUEST['div']) ? $_REQUEST['div'] : null;
		if ($kodedealer=='2010') { $is_dealer = "0"; } else { $is_dealer = "1"; }
		//if ($div=='all') { $divisi = ""; } else { $divisi = "and nama_div = '".$div."'"; }
		/*
		$sql = "select id_sys_dept, nama_dept from sys_department a
				left join sys_divisi b on a.id_sys_div = b.id_sys_div
				where b.is_dealer = '".$is_dealer."' and nama_dept in (
															select b.department from DeptTerkait a
															inner join sys_user b on a.iduser = b.iduser) and nama_dept != '".$_SESSION['evo_dept']."'";
		*/
		$sql = "select id_sys_dept, nama_dept 
				from sys_department a
				left join sys_divisi b on a.id_sys_div = b.id_sys_div
				where b.is_dealer = '".$is_dealer."' and nama_dept in (
															select b.department from DeptTerkait a
															inner join sys_user b on a.iduser = b.iduser) 
				and nama_dept != '".$_SESSION['evo_dept']."' and a.is_aktif = '1' ";
				
		$rsl = mssql_query($sql);
		echo "<option value=''>- Pilih -</option>";
		while ($dt = mssql_fetch_array($rsl)) {
			echo "<option value='".$dt['nama_dept']."'>".$dt['nama_dept']."</option>";
		}
				
	} else if ($action == 'getMaterai') {
		$sql = "select top 1 nominal_materai from sys_materai where aktif = '1' ";
		$rsl = mssql_query($sql);
		$dt = mssql_fetch_array($rsl);
		echo $dt['nominal_materai'];
	
					
	} else if ($action == 'getPpn') {
		$KodeDealer = isset($_REQUEST['kodedealer']) ? $_REQUEST['kodedealer'] : null;
		//include '../inc/conn.php';
		include '../inc/koneksi.php';
		
		$qry_ppn = mssql_query("select nilaiPPn from profilacc..settingPPn 
								where aktif = '1' and tglAwal <= GETDATE() and tglAkhir >= GETDATE()",$connCab);
		$dt_ppn = mssql_fetch_array($qry_ppn);
		echo $ppn_persen = $dt_ppn['nilaiPPn'];
			
	} else if ($action == 'getAtasan') {
		require_once ('../inc/conn.php');
		$kodedealer = isset($_REQUEST['kodedealer']) ? $_REQUEST['kodedealer'] : null;
		$divisi = isset($_REQUEST['divisi']) ? $_REQUEST['divisi'] : null;
		$IdUser = isset($_REQUEST['IdUser']) ? $_REQUEST['IdUser'] : null;
		$atasan = mssql_fetch_array(mssql_query("select IdAtasan from sys_user where IdUser = '".$IdUser."'"));
		if ($atasan['IdAtasan']=='all') { $boss = ""; } else { $boss = "and IdUser = '".$atasan['IdAtasan']."'"; }

		$user = mssql_fetch_array(mssql_query("select * from sys_user where IdUser = '".$IdUser."'"));
		/*$sql = "
			select * from sys_user where (tipe = 'SECTION HEAD' or tipe = 'ADH') 
			and divisi in ('".$divisi."','all') and KodeDealer = '".$kodedealer."' and department='".$user['department']."' $boss";
		*/
		$sql = "
			select * from sys_user where KodeDealer = '".$kodedealer."' $boss";
		
		$rsl = mssql_query($sql);
		//echo "<option value=''>- Pilih -</option>";
		while ($dt = mssql_fetch_array($rsl)) {
			echo "<option value='".$dt['IdUser']."'>".$dt['namaUser']."</option>";
		}
	} else if ($action == 'dataEdit') {
		$id = addslashes($_REQUEST['id']);
		$sql = "select kodedealer,tipe,divisi,IdAtasan,tipehutang,nobukti,tgl_pengajuan,upload_file,upload_fp from DataEvo where evo_id = '".$id."'";
		$dt = mssql_fetch_array(mssql_query($sql));
		$data = ""; $prefix = "_vp_";
		$data .= $dt['divisi'].$prefix;
		$data .= $dt['IdAtasan'].$prefix;
		$data .= $dt['tipehutang'].$prefix;
		$data .= substr($dt['nobukti'], 2,15).$prefix;
		$data .= $dt['tgl_pengajuan'].$prefix;
		$data .= $dt['upload_file'].$prefix;
		$data .= $dt['upload_fp'].$prefix;
		echo $data;
	}
	
										
											

	function getNumberCvs(){
		$jenis = "CSV/".date('d')."/".date('m')."/".date('y')."/";
		$query = "SELECT max(noCsv) as maxID FROM DataEvoTransfer WHERE noCsv LIKE '%$jenis%'";
		$hasil = mssql_query($query);
		$data = mssql_fetch_array($hasil);
		$idMax = $data['maxID'];
		$noUrut = (int) substr($idMax, -3, 3);
		$noUrut++;
		$number = $jenis.sprintf("%03s", $noUrut);
		return $number;
	}

	function getNoBukti($KodeDealer){
		#$kodecbg = mssql_fetch_array(mssql_query("select kodecabang from profilCR..dealer where kodedealer='".$KodeDealer."'"));
		$kodecbg = mssql_fetch_array(mssql_query("select RIGHT(DBName,2) kodecabang from SPK00..DoDealer where kodedealer='".$KodeDealer."'"));
		if ($KodeDealer=='2010') { $kode = "00"; } else { $kode = $kodecbg['kodecabang']; }
		$jenis = "VP".$kode."/".date('d')."/".date('m')."/".date('y')."/";
		$query = "SELECT max(nobukti) as maxID FROM DataEvo WHERE nobukti LIKE '%$jenis%'";
		$hasil = mssql_query($query);
		$data = mssql_fetch_array($hasil);
		$idMax = $data['maxID'];
		$noUrut = (int) substr($idMax, -3, 3);
		$noUrut++;
		$number = $jenis.sprintf("%03s", $noUrut);
		return $number;
	}
	
	function getNextLevel($KodeDealer) {
		if ($KodeDealer=='2010') {
			$where = " and is_dealer = '0' ";
		} else {
			$where = " and is_dealer = '1' ";
		}
		$data = mssql_fetch_array(mssql_query("select top 1 nama_lvl from sys_level 
											where nama_lvl not in ('TAX','ADMIN') and is_aktif = '1' $where
											and urutan > (select top 1 urutan from sys_level where nama_lvl = '".$_SESSION['level']."' 
											and is_aktif = '1' $where
											order by urutan)
											order by urutan"));
		$lvlnext = $data['nama_lvl'];
		return $lvlnext;
	}
	
?>