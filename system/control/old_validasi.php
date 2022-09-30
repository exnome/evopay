<?php
	require_once ('../inc/conn.php');
	$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : null;
	if ($action=='validasi') {
		$IdUser = addslashes($_REQUEST['IdUser']);
		$level = addslashes($_REQUEST['level']);
		$KodeDealer = addslashes($_REQUEST['KodeDealer']);
		$Tipe = addslashes($_REQUEST['Tipe']);
		$nobukti = addslashes("VP".$_REQUEST['nobukti']);
		$metode_bayar = addslashes($_REQUEST['metode_bayar']);
		$val = addslashes($_REQUEST['val']);
		$ketreject = "";
		$ketvalidasi = "";
		$over = addslashes($_REQUEST['over']);
		
		$pesan = "";
		mssql_query("BEGIN TRAN",$conns);
			if ($val=='Accept') {
				if ($KodeDealer=='2010') {
					if ($metode_bayar=='Pety Cash') {
						if ($level=='TAX' or $level=='SYSADMIN') {
							$ketvalidasi = "";
							$nextlvl = "SECTION HEAD";
							$insert=true;
						} else if ($level=='SECTION HEAD' or $level=='SYSADMIN') {
							$ketvalidasi = addslashes($_REQUEST['note_sectionhead']);
							$nextlvl = "DEPT. HEAD";
							$insert=true;
						} else if ($level=='DEPT. HEAD' or $level=='SYSADMIN') {
							$ketvalidasi = addslashes($_REQUEST['note_dept_head']);
							$nextlvl = "FINANCE";
							$insert=true;
						} else if ($level=='FINANCE' or $level=='SYSADMIN') {
							$ketvalidasi = "";
							$nextlvl = "DEPT. HEAD FINANCE";
							$insert=true;
						} else if ($level=='DEPT. HEAD FINANCE' or $level=='SYSADMIN') {
							$ketvalidasi = addslashes($_REQUEST['note_dept_head_fin']);
							$nextlvl = "FINANCE";
							$insert=true;
							$pesan .= insertAcc($nobukti);
						}
					} else {
						if ($level=='TAX' or $level=='SYSADMIN') {
							$ketvalidasi = "";
							$nextlvl = "SECTION HEAD";
							$insert=true;
						} else if ($level=='SECTION HEAD' or $level=='SYSADMIN') {
							$ketvalidasi = addslashes($_REQUEST['note_sectionhead']);
							$nextlvl = "DEPT. HEAD";
							$insert=true;
						} else if ($level=='DEPT. HEAD' or $level=='SYSADMIN') {
							$ketvalidasi = addslashes($_REQUEST['note_dept_head']);
							$nextlvl = "DIV. HEAD";
							$insert=true;
						} else if ($level=='DIV. HEAD' or $level=='SYSADMIN') {
							$ketvalidasi = addslashes($_REQUEST['note_div_head']);
							$nextlvl = "DIREKSI";
							$insert=true;
						} else if ($level=='DIREKSI' or $level=='SYSADMIN') {
							$ketvalidasi = addslashes($_REQUEST['note_direksi']);
							$nextlvl = "FINANCE";
							$insert=true;
						} else if ($level=='FINANCE' or $level=='SYSADMIN') {
							$ketvalidasi = addslashes($_REQUEST['note_dept_head_fin']);
							$nextlvl = "DEPT. HEAD FINANCE";
							$ketvalidasi2 = addslashes($_REQUEST['note_div_head_fast']);
							$nextlvl2 = "DIV. HEAD FAST";
							$insert=true;
						} else if ($level=='DEPT. HEAD FINANCE' or $level=='DIV. HEAD FAST' or $level=='SYSADMIN') {
							$ketvalidasi = "";
							$nextlvl = "FINANCE";
							$insert=true;
							$pesan .= insertAcc($nobukti);
						}
					}
				} else {
					if ($Tipe=='HUTANG') {
						if ($level=='ACCOUNTING' or $level=='SYSADMIN') {
							$ketvalidasi = "";
							$nextlvl = "SECTION HEAD";
							$insert=true;
						} else if ($level=='SECTION HEAD' or $level=='SYSADMIN') {
							$ketvalidasi = addslashes($_REQUEST['note_sectionhead']);
							$nextlvl = "ADH";
							$insert=true;
						} else if ($level=='ADH' or $level=='SYSADMIN') {
							$ketvalidasi = addslashes($_REQUEST['note_adh']);
							$nextlvl = "BRANCH MANAGER";
							$insert=true;
						} else if ($level=='BRANCH MANAGER' or $level=='SYSADMIN') {
							$ketvalidasi = addslashes($_REQUEST['note_branch_manager']);
							$nextlvl = "FINANCE";
							$insert=true;
							$pesan .= insertAcc($nobukti);
						}
					} else if ($Tipe=='BIAYA') {
						if ($metode_bayar=='Pety Cash') {
							if ($level=='ACCOUNTING' or $level=='SYSADMIN') {
								$ketvalidasi = "";
								$nextlvl = "SECTION HEAD";
								$insert==true;
							} else if ($level=='SECTION HEAD' or $level=='SYSADMIN') {
								$ketvalidasi = addslashes($_REQUEST['note_sectionhead']);
								$nextlvl = "ADH";
								$insert==true;
							} else if ($level=='ADH' or $level=='SYSADMIN') {
								$ketvalidasi = addslashes($_REQUEST['note_adh']);
								$nextlvl = "FINANCE";
								$insert==true;
								$pesan .= insertAcc($nobukti);
							}
						} else {
							if ($level=='ACCOUNTING' or $level=='SYSADMIN') {
								$ketvalidasi = "";
								$nextlvl = "SECTION HEAD";
								$insert==true;
							} else if ($level=='SECTION HEAD' or $level=='SYSADMIN') {
								$ketvalidasi = addslashes($_REQUEST['note_sectionhead']);
								$nextlvl = "ADH";
								$insert==true;
							} else if ($level=='ADH' or $level=='SYSADMIN') {
								$ketvalidasi = addslashes($_REQUEST['note_adh']);
								if ($over=="0") {
									$nextlvl = "FINANCE";
									$pesan .= insertAcc($nobukti);
								} else if ($over=="1") {
									$nextlvl = "BRANCH MANAGER";
								}
								$insert==true;
							} else if ($level=='BRANCH MANAGER' or $level=='SYSADMIN') {
								$ketvalidasi = addslashes($_REQUEST['note_branch_manager']);
								$nextlvl = "OM";
								$insert==true;
							} else if ($level=='OM' or $level=='SYSADMIN') {
								$ketvalidasi = addslashes($_REQUEST['note_om']);
								$nextlvl = "FINANCE";
								$insert==true;
								$pesan .= insertAcc($nobukti);
							}
						}
					}
				}
				
				if ($insert==true) {
					if ($nextlvl2=='DIV. HEAD FAST') {
						$sql1 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry) 
						values ('$nobukti','$KodeDealer','".$nextlvl."',getdate()),
							   ('$nobukti','$KodeDealer','".$nextlvl."',getdate())";
					} else {
						$sql1 = "insert into DataEvoVal (nobukti,kodedealer,level,tglentry) 
						values ('$nobukti','$KodeDealer','".$nextlvl."',getdate())";
					}
					$query1 = mssql_query($sql1,$conns);
				} else {
					$query1 = true;
				}
			} else if ($val=='Reject') {
				$query1 = true;
				$ketreject = addslashes($_REQUEST['ketreject']);
				mssql_query("update DataEvoTagihan set isreject=1 where nobukti = '".$nobukti."'",$conns);
			}

			$sql2 = "update DataEvoVal set validasi='".$val."',uservalidasi='".$IdUser."',tglvalidasi=getdate(),
					ketvalidasi='".$ketvalidasi."', ketreject='".$ketreject."' where nobukti = '".$nobukti."' and level = '".$level."'";
			$query2 = mssql_query($sql2,$conns);
			
			if ($query1 && $query2) {
				mssql_query("COMMIT TRAN",$conns);
				$pesan .= "1#Data Save!!";
			} else {
				if (!$query1) { $pesan .= "0#Failed Query 1!<br/>".$sql1; }
				if (!$query2) { $pesan .= "0#Failed Query 2!<br/>".$sql2; }
				mssql_query("ROLLBACK TRAN",$conns);
			}
		mssql_query("return",$conns);
		echo $pesan;
	} else if ($action=='edit-hutang') {
		$IdUser = addslashes($_REQUEST['IdUser']);
		$level = addslashes($_REQUEST['level']);
		$KodeDealer = addslashes($_REQUEST['KodeDealer']);
		$Tipe = addslashes($_REQUEST['Tipe']);
		$nobukti = addslashes("VP".$_REQUEST['nobukti']);
		$tgl_bayar = addslashes($_REQUEST['tgl_bayar']);
		$trfPajak = addslashes($_REQUEST['trfPajak']);
		$htg_stl_pajak = addslashes($_REQUEST['htg_stl_pajak']);
		$keterangan = addslashes($_REQUEST['keterangan']);

		$pesan = "";
		mssql_query("BEGIN TRAN");
			$r = explode("_cn_", $trfPajak);
			mssql_query("delete from DataEvoPos where nobukti = '".$nobukti."'");
			for ($i=0; $i < count($r); $i++) { 
				$s = explode("#", $r[$i]);
				$sql = "insert into DataEvoPos (nobukti,nominal,jns_pph,tarif_persen,nilai_pph,akun_pph) 
					values ('$nobukti','$s[0]','$s[1]','$s[2]','$s[3]','$s[4]')";
				$query1 = mssql_query($sql,$conns);
			}

			if ($level=='ACCOUNTING' or $level=='TAX') {
				$sql2 = "update DataEvo set htg_stl_pajak='".$htg_stl_pajak."' where nobukti = '".$nobukti."'";
				$query2 = mssql_query($sql2);
			} else if ($level=='ADH') {
				$sql2 = "update DataEvo set tgl_bayar='".$tgl_bayar."', keterangan='".$keterangan."',
					htg_stl_pajak='".$htg_stl_pajak."' where nobukti =".$nobukti." ''";
				$query2 = mssql_query($sql2);
			}
			if ($query1 && $query2) {
				mssql_query("COMMIT TRAN");
				$pesan .= "1#Data Save!!";
			} else {
				if (!$query1) { $pesan .= "0#Failed Query 1!<br/>".$sql1; }
				if (!$query2) { $pesan .= "0#Failed Query 2!<br/>".$sql2; }
				mssql_query("ROLLBACK TRAN");
			}
		mssql_query("return");
		echo $pesan;
	} else if ($action=='edit-biaya') {
		$IdUser = addslashes($_REQUEST['IdUser']);
		$level = addslashes($_REQUEST['level']);
		$KodeDealer = addslashes($_REQUEST['KodeDealer']);
		$Tipe = addslashes($_REQUEST['Tipe']);
		$nobukti = addslashes("VP".$_REQUEST['nobukti']);
		$tgl_bayar = addslashes($_REQUEST['tgl_bayar']);
		$posbiaya = addslashes($_REQUEST['posbiaya']);
		$keterangan = addslashes($_REQUEST['keterangan']);
		$total_dpp = addslashes($_REQUEST['total_dpp']);
		$biaya_yg_dibyar = addslashes($_REQUEST['biaya_yg_dibyar']);

		mssql_query("BEGIN TRAN");
			$r = explode("_cn_", $posbiaya);
			mssql_query("delete from DataEvoPos where nobukti = '".$nobukti."'");
			for ($i=0; $i < count($r); $i++) { 
				$s = explode("#", $r[$i]);
				$sql = "insert into DataEvoPos (nobukti,pos_biaya,nominal,jns_pph,tarif_persen,nilai_pph,ketAkun,akun_pph) 
					values ('$nobukti','$s[0]','$s[1]','$s[2]','$s[3]','$s[4]','$s[5]','$s[6]')";
				$query1 = mssql_query($sql,$conns);
			}

			if ($level=='ACCOUNTING') {
				$sql2 = "update DataEvo set total_dpp='".$total_dpp."',biaya_yg_dibyar='".$biaya_yg_dibyar."' where nobukti = '".$nobukti."'";
				$query2 = mssql_query($sql2);
			} else if ($level=='ADH') {
				$sql2 = "update DataEvo set tgl_bayar='".$tgl_bayar."', keterangan='".$keterangan."',
					total_dpp='".$total_dpp."',biaya_yg_dibyar='".$biaya_yg_dibyar."' where nobukti =".$nobukti." ''";
				$query2 = mssql_query($sql2);
			}
			if ($query1 && $query2) {
				mssql_query("COMMIT TRAN");
				$pesan = "1#Data Save!!";
			} else {
				if (!$query1) { $pesan = "0#Failed Query 1!<br/>".$sql1; }
				if (!$query2) { $pesan = "0#Failed Query 2!<br/>".$sql2; }
				mssql_query("ROLLBACK TRAN");
			}
		mssql_query("return");
		echo $pesan;
	} else if ($action=='cekRab') {
		error_reporting(0);
		$month2 = array('01' => 'Jan','02' => 'Feb','03' => 'Mar','04' => 'Apr','05' => 'Mei','06' => 'Jun','07' => 'Jul','08' => 'Ags','09' => 'Sep','10' => 'Okt','11' => 'Nov','12' => 'Dsm');
		$KodeDealer = addslashes($_REQUEST['kodedealer']);
		$kodeakun = addslashes($_REQUEST['kodeakun']);
		$nom = addslashes($_REQUEST['nom']);
		include '../inc/koneksi.php';
		if ($msg=='0') {
			$total = "0";
		} else if ($msg=='1') {
			$total = "1";
		} else if (!mssql_select_db("[$table]",$connCab)) {
			$total = "2";
		} else if ($msg=='3') {
			$acc = "ACC".$kodecabang;
			$ra = "RA".$kodecabang;
			$sql3 = "
				SELECT Tahun,a.Kodegl, a.Namagl,Jan,Feb,Mar,Apr,Mei,Jun,
				case when ISNULL(julR,0)=0 then Jul else julR end as Jul,
				case when ISNULL(AgsR,0)=0 then Ags else AgsR end as Ags,
				case when ISNULL(SepR,0)=0 then Sep else SepR end as Sep,
				case when ISNULL(OktR,0)=0 then Okt else OktR end as Okt,
				case when ISNULL(NovR,0)=0 then Nov else NovR end as Nov,
				case when ISNULL(DsmR,0)=0 then Dsm else DsmR end as Dsm,
			";
				for ($a=1; $a <= 12; $a++) { 
					if (strlen($a)==1) { $hm = "0".$a; } else { $hm = $a; }
					$test = $month2[$hm];
					if (mssql_select_db("[$acc-".$tahun."".$hm."]",$connCab)) {
						$sql3 .= "
							(
								SELECT Case 
								When (G.Kategori in ('B','C') or (G.Kodegl < '40000000' and G.Typerek = '19')) then (G.JBulanIni)*-1
								When (G.Kategori = 'A') then (G.JBulanIni)
								When (G.Kategori in ('D','G') or (G.Kodegl >= '40000000' and G.Typerek = '19')) then (G.JBulanIni)*-1
								When (G.Kategori in ('E','F','H')) then (G.JBulanIni)
								End as realMei
								from [$acc-".$tahun."".$hm."]..glmst G
								inner join [$ra]..ra F on G.KodeGl=F.KodeGl
								where G.Kodegl = a.Kodegl and Tahun=b.Tahun
							) as real$test,
						";
					} else {
						$sql3 .= "'-' as real$test,";
					}
				}
			$sql3 .="
				NUll as selesai
				from [$table]..glmst a
				inner join [$ra]..ra b on a.KodeGl=b.KodeGl
				where a.Kodegl = '".$kodeakun."' and Tahun='".$tahun."'
			";
			// echo $sql3;
			$result = mssql_query($sql3,$connCab);
			$rowz = mssql_fetch_array($result);
			$rapbThun = 0;
			$realThun = 0;
			for ($a=1; $a <= 12; $a++) { 
				if (strlen($a)==1) { $hm = "0".$a; } else { $hm = $a; }
				$test2 = $month2[$hm];
				$rapbThun += round($rowz[$test2]);
				if ($rowz["real".$test2]=='-') {
					$realThun += 0;
					$real_ = '-';
				} else {
					$realThun += round($rowz["real".$test2]);
					$real_ = number_format($rowz["real".$test2],0,",",".");
				}
			}

			$rapbBln = $rowz[$month2[$bln]];
			$realBln = $rowz["real".$month2[$bln]];
			$rapbOg = 0;
			for ($i=1; $i <= $bln; $i++) { 
				if (strlen($i)==1) { $hm = "0".$i; } else { $hm = $i; }
				$rapbOg += $rowz[$month2[$hm]];
			}
			$realOg = 0;
			for ($i=1; $i <= $bln; $i++) { 
				if (strlen($i)==1) { $hm = "0".$i; } else { $hm = $i; }
				$realOg = $rowz["real".$month2[$hm]];
			}

			if (($rapbBln-$realBln)<=$nom) {
				echo "1";
			} else {
				echo "0";
			}
		}
	} else if ($action=='getdata') {
		$id = addslashes($_REQUEST['id']);
		$sql = "select *,(select count(evopos_id) from DataEvoPos where nobukti=a.nobukti) totPos from DataEvo a where evo_id = '".$id."'";
		$vw = mssql_fetch_array(mssql_query($sql));

		$sqlPos = "select evopos_id from DataEvoPos where nobukti='".$vw['nobukti']."'";
		$rslPos = mssql_query($sqlPos);
		$evopos = "";
		while ($dtPos = mssql_fetch_array($rslPos)) {
			$evopos .= $dtPos['evopos_id'].",";
		}
		$evopos_id = substr($evopos, 0,strlen($evopos)-1);

		echo $vw['status']."#".$vw['nobukti']."#".$vw['tgl_pengajuan']."#".$vw['upload_file']."#".$vw['upload_fp']."#".$vw['kode_vendor']."#".$vw['namaVendor']."#".$vw['metode_bayar']."#".$vw['benificary_account']."#".$vw['tgl_bayar']."#".$vw['nama_bank']."#".$vw['nama_pemilik']."#".$vw['email_penerima']."#".$vw['nama_alias']."#".$vw['nama_bank_pengirim']."#".$vw['tf_from_account']."#".$vw['realisasi_nominal']."#".$vw['is_ppn']."#".$vw['dpp']."#".$vw['ppn']."#".$vw['npwp']."#".$vw['no_fj']."#".$vw['total_dpp']."#".$vw['biaya_yg_dibyar']."#".$vw['keterangan']."#".$vw['totPos']."#".$evopos_id;
	} else if ($action=='getpos') {
		$id = addslashes($_REQUEST['id']);
		$sql = "select pos_biaya as kodeAkun,ketAkun,nominal,(jns_pph+'#'+convert(varchar,tarif_persen)) as trfPajak,jns_pph,tarif_persen,nilai_pph as nilaiPph from DataEvoPos where evopos_id = '".$id."'";
		$rsl = mssql_query($sql);
		$vw = mssql_fetch_array($rsl);
		echo $vw['kodeAkun']."-".$vw['ketAkun']."-".$vw['nominal']."-".$vw['trfPajak']."-".$vw['jns_pph']."-".$vw['tarif_persen']."-".$vw['nilaiPph'];
	}

	function insertAcc($nobukti){
		include '../inc/conn.php';
		$dtAcc = mssql_fetch_array(mssql_query("select * from DataEvo where nobukti = '".$nobukti."'",$conns));
		$dtAkun = mssql_fetch_array(mssql_query("select * from settingAkun where id=1",$conns));
		$sgltrn = ""; $saptrn = ""; $sglmst = "";
		$delIndex = "AP".rand(0,99999999);
		$KodeDealer = $dtAcc['kodedealer'];
		include '../inc/koneksi.php';
		$pesan = "";
		if ($msg=='0') {
			$pesan .= "Gagal Koneksi Cabang!";
		} else if ($msg=='1') {
			$pesan .= "Gagal Koneksi HO!";
		} else if (!mssql_select_db("[$table]",$connCab)) {
			$pesan .= "Database tidak tersedia!";
		} else if ($msg=='3') {
			if ($dtAcc['tipe']=='HUTANG') {
				if ($dtAcc['tipeppn']!='N') {
					$sgltrn .= "insert into [$table]..gltrn (NoBukti,KodeGl,TglTrn,KodeSumber,TypeTrn,Keterangan,JlhDebit,JlhKredit,DelIndex,LastUpdated,KodeUser,KodeLgn,SumberForm,NoFPS) 
					values";
				}
				if ($dtAcc['tipeppn']=='I') {
					$sgltrn .= "('$dtAcc[nobukti]','$dtAkun[akun_ppn]','$dtAcc[tgl_pengajuan]','AP','03','PPN MASUKAN','$dtAcc[ppn]','0','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','CreditNote','$dtAcc[no_fj]'),";
					$sgltrn .= "('$dtAcc[nobukti]','$dtAkun[akun_sublet]','$dtAcc[tgl_pengajuan]','AP','03','SUBLET DALAM PROSES','0','$dtAcc[ppn]','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','CreditNote','$dtAcc[no_fj]'),";
				} else if ($dtAcc['tipeppn']=='E') {
					$sgltrn .= "('$dtAcc[nobukti]','$dtAkun[akun_ppn]','$dtAcc[tgl_pengajuan]','AP','03','PPN MASUKAN','$dtAcc[ppn]','0','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','CreditNote','$dtAcc[no_fj]'),";
					$sgltrn .= "('$dtAcc[nobukti]','$dtAcc[kodeAkun]','$dtAcc[tgl_pengajuan]','AP','03','$dtAcc[namaAkun]','0','$dtAcc[ppn]','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','CreditNote','$dtAcc[no_fj]'),";
				}
				if ($dtAcc['tipeppn']!='N') {
					$spph = "select akun_pph,jns_pph,tarif_persen,nilai_pph from DataEvoPos 
						where nobukti = '".$nobukti."' and jns_pph not in ('Non Pph')";
					$rpph = mssql_query($spph,$conns);
					$cpph = mssql_num_rows($rpph);
					$htgpph = 0;
					if ($cpph>0) {
						while ($dpph = mssql_fetch_array($rpph)) {
							$htgpph += $dpph['nilai_pph'];
							$sgltrn .= "('$dtAcc[nobukti]','$dpph[akun_pph]','$dtAcc[tgl_pengajuan]','AP','03','Hutang $dpph[jns_pph] ($dpph[tarif_persen]%)','0','$dpph[nilai_pph]','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','CreditNote','$dtAcc[no_fj]'),";
						}
						$sgltrn .= "('$dtAcc[nobukti]','$dtAcc[kodeAkun]','$dtAcc[tgl_pengajuan]','AP','03','$dtAcc[namaAkun]','$htgpph','0','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','CreditNote','$dtAcc[no_fj]'),";
					}
				}

				if ($dtAcc['tipeppn']!='N') {
					$saptrn .= "insert into [$table]..aptrn 
						(KodeLgn,NoFaktur,TypeTrn,NoBukti,TglTrn,TglJthTmp,TglTrnFaktur,TglJtpFaktur,Statusgiro,Keterangan,JumlahTrn,TglEntry,Kodeuser,Kodesumber,DelIndex,KodeJurnal) values";
					$saptrn .= "('$dtAcc[kode_vendor]','$dtAcc[nobukti]','C','$dtAcc[nobukti]','$dtAcc[tgl_pengajuan]','$dtAcc[tgl_pengajuan]','$dtAcc[tgl_pengajuan]','$dtAcc[tgl_pengajuan]','C','$dtAcc[namaAkun]','$dtAcc[htg_stl_pajak]',getdate(),'$dtAcc[userentry]','AP','$delIndex','$dtAcc[kodeAkun]'),";
					$saptrn .= "('$dtAcc[kode_vendor]','$dtAcc[nobukti]','F','$dtAcc[nobukti]','$dtAcc[tgl_pengajuan]','$dtAcc[tgl_pengajuan]','$dtAcc[tgl_pengajuan]','$dtAcc[tgl_pengajuan]','C','PPN','$dtAcc[ppn]',getdate(),'$dtAcc[userentry]','AP','$delIndex','$dtAkun[akun_ppn]'),";
				}
				$sglmst = true;
			} else if ($dtAcc['tipe']=='BIAYA') {
				$sgltrn .= "insert into [$table]..gltrn (NoBukti,KodeGl,TglTrn,KodeSumber,TypeTrn,Keterangan,JlhDebit,JlhKredit,DelIndex,LastUpdated,KodeUser,KodeLgn,SumberForm,NoFPS) 
					values";
				$spos = "select pos_biaya as kodeGl,nominal,SUBSTRING(ketAkun, 12, len(ketAkun)) as ketAkun from DataEvoPos 
					where nobukti = '".$vw['nobukti']."'";
				$rpos = mssql_query($spos,$conns);
				while ($dpos = mssql_fetch_array($rpos)) {
					$sgltrn .= "('$dtAcc[nobukti]','$dpos[kodeGl]','$dtAcc[tgl_pengajuan]','AP','03','$dpos[ketAkun]','$dpos[nominal]','0','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','CreditNote','$dtAcc[no_fj]'),";
				}

				if ($dtAcc['is_ppn']=='1') {
					$sgltrn .= "('$dtAcc[nobukti]','$dtAkun[akun_ppn]','$dtAcc[tgl_pengajuan]','AP','03','PPN','$dtAcc[ppn]','0','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','CreditNote','$dtAcc[no_fj]'),";	
				}

				$spph = "select akun_pph,jns_pph,tarif_persen,nilai_pph from DataEvoPos 
						where nobukti = '".$vw['nobukti']."' and jns_pph not in ('Non Pph')";
				$rpph = mssql_query($spph,$conns);
				while ($dpph = mssql_fetch_array($rpph)) {
					$sgltrn .= "('$dtAcc[nobukti]','$dpph[akun_pph]','$dtAcc[tgl_pengajuan]','AP','03','Hutang $dpph[jns_pph] ($dpph[tarif_persen]%)','0','$dpph[nilai_pph]','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','CreditNote','$dtAcc[no_fj]'),";
				}
				
				$sgltrn .= "('$dtAcc[nobukti]','$dtAkun[akun_biaya]','$dtAcc[tgl_pengajuan]','AP','03','Biaya yang harus dibayar','0','$dtAcc[biaya_yg_dibyar]','$delIndex',getdate(),'$dtAcc[userentry]','$dtAcc[kode_vendor]','CreditNote','$dtAcc[no_fj]'),";

				$saptrn .= "insert into [$table]..aptrn 
					(KodeLgn,NoFaktur,TypeTrn,NoBukti,TglTrn,TglJthTmp,TglTrnFaktur,TglJtpFaktur,Statusgiro,Keterangan,JumlahTrn,TglEntry,Kodeuser,Kodesumber,DelIndex,KodeJurnal) values";
				if ($dtAcc['is_ppn']=='1') {
					$saptrn .= "('$dtAcc[kode_vendor]','$dtAcc[nobukti]','C','$dtAcc[nobukti]','$dtAcc[tgl_pengajuan]','$dtAcc[tgl_pengajuan]','$dtAcc[tgl_pengajuan]','$dtAcc[tgl_pengajuan]','C','PPN','$dtAcc[ppn]',getdate(),'$dtAcc[userentry]','AP','$delIndex','$dtAkun[akun_ppn]'),";	
				}
				$saptrn .= "('$dtAcc[kode_vendor]','$dtAcc[nobukti]','C','$dtAcc[nobukti]','$dtAcc[tgl_pengajuan]','$dtAcc[tgl_pengajuan]','$dtAcc[tgl_pengajuan]','$dtAcc[tgl_pengajuan]','C','Biaya yang harus dibayar','$dtAcc[biaya_yg_dibyar]',getdate(),'$dtAcc[userentry]','AP','$delIndex','$dtAkun[akun_biaya]'),";

				$sgl = "select KodeGl,(JlhDebit-JlhKredit) as jumlah from [$table]..Gltrn where NoBukti='$dtAcc[nobukti]'";
				$rgl = mssql_query($sgl,$connCab);
				while ($dgl=mssql_fetch_array($rgl)) {
					if ($dgl['jumlah']>0) {
						$sglmst .= "
							update [$table]..Glmst set JBulanini = JBulanini + $dgl[jumlah], JDebitBi = JDebitBi + $dgl[jumlah] 
							where KodeGl='$dgl[KodeGl]'
						";
					} else {
						$sglmst .= "
							update [$table]..Glmst set JBulanini = JBulanini + $dgl[jumlah], JKreditBi = JKreditBi + Abs($dgl[jumlah]) 
							where KodeGl='$dgl[KodeGl]'
						";
					}
				}
			}
			$sgltrn = substr($sgltrn, 0,strlen($sgltrn)-1);
			$saptrn = substr($saptrn, 0,strlen($saptrn)-1);
			mssql_query("BEGIN TRAN");
				$qry1 = mssql_query($sgltrn,$connCab);
				$qry2 = mssql_query($saptrn,$connCab);
				if ($sglmst!=true) {
					$qry3 = mssql_query($sglmst,$connCab);
				} else {
					$qry3 = true;
				}
				if ($qry1 and $qry2 and $qry3) {
					if ($dtAcc['tipe']=='BIAYA') {
						$cekBalancing = mssql_fetch_array(mssql_query("
							select sum(JlhDebit) as Debit,sum(JlhKredit) as Kredit from [$table]..Gltrn where NoBukti='".$nobukti."'
						",$connCab));
						if ($cekBalancing['Debit']==$cekBalancing['Kredit']) {
							mssql_query("COMMIT TRAN");
							$pesan .= "Transaksi telah berhasil di Accept!";
						} else {
							mssql_query("ROLLBACK TRAN");
							$pesan .= "Failed!! Not Balance!";
						}
					} else {
						mssql_query("COMMIT TRAN");
						$pesan .= "Transaksi telah berhasil di Accept!";
					}
				} else {
					if (!$qry1) { $pesan .= "Failed Query 1!"; }
					if (!$qry2) { $pesan .= "Failed Query 2!"; }
					if (!$qry3) { $pesan .= "Failed Query 3!"; }
					mssql_query("ROLLBACK TRAN");
				}
			mssql_query("return");
		}
		include '../inc/conn.php';
		echo $pesan;
	}
?>