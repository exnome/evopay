<?php
	include '../inc/config.php';
	require_once ('../inc/getNumber.php');
	$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : null;
	if ($action=='new') {
		$IdUser = addslashes($_REQUEST['IdUser']);
		$NoInv = numberInv();
		$tglInv = addslashes($_REQUEST['tglInv']);
		$NopoCust = addslashes($_REQUEST['NopoCust']);
		$noFP = addslashes($_REQUEST['noFP']);
		$nomor_sj = $_REQUEST['nomor_sj'];
		$hpp = addslashes($_REQUEST['hpp']);
		$total = addslashes($_REQUEST['total']);
		$diskon = addslashes($_REQUEST['diskon']);
		$net = addslashes($_REQUEST['net']);
		$ppn = addslashes($_REQUEST['ppn']);
		$grandTotal = addslashes($_REQUEST['grandTotal']);
		$withDN = addslashes($_REQUEST['withDN']);
		$nomor_fsj = addslashes($_REQUEST['nomor_fsj']);
		$msg = "";
		mysqli_query($conn,"START TRANSACTION");
			$delIndex = delIndexSI();
			$kodeGl3 = mysqli_fetch_array(mysqli_query($conn,"SELECT kodeGl FROM settingakun WHERE idSetAkun='3'"));
			$kodeGl4 = mysqli_fetch_array(mysqli_query($conn,"SELECT kodeGl FROM settingakun WHERE idSetAkun='4'"));
			$kodeGl5 = mysqli_fetch_array(mysqli_query($conn,"SELECT kodeGl FROM settingakun WHERE idSetAkun='5'"));
			$kodeGl6 = mysqli_fetch_array(mysqli_query($conn,"SELECT kodeGl FROM settingakun WHERE idSetAkun='6'"));
			$kodeGl7 = mysqli_fetch_array(mysqli_query($conn,"SELECT kodeGl FROM settingakun WHERE idSetAkun='7'"));
			$cek = mysqli_fetch_array(mysqli_query($conn,"select is_fp from sys_profile where sys_id=1"));
			
			$KodeLgn = mysqli_fetch_array(mysqli_query($conn,"
				select DISTINCT id_pelanggan from trn_suratjalan a 
				inner join trn_sohdr b on a.nomor_so=b.nomor_so 
				where (no_sj_gabung in (".$nomor_sj.") OR nomor_sj in (".$nomor_sj.") )
				UNION
				select DISTINCT id_pelanggan from trn_suratjalan_directdtl a 
				inner join trn_suratjalan_direct b on a.nomor_dsj=b.nomor_dsj
				where a.nomor_dsj in (".$nomor_sj.")
			"));
			$syrt = mysqli_fetch_array(mysqli_query($conn,"
				select id_syarat,b.syaratBayar,expr,unit,npwp,nama_perusahaan as namacustomer,typeppn,
				CONCAT(alamatPenagihan,' No.',alamat_no,' RT.',alamat_rt,' RW.',alamat_rw,' ',alamat_kelurahan,' ',alamat_kecamatan,' ',alamat_kota,' ',alamat_kodepos) as alamat,
				alamat_kota,id_pelanggan from mst_pelanggan a 
				inner join syaratbayar b on a.syaratBayar=b.id_syarat where id_pelanggan='".$KodeLgn['id_pelanggan']."'
			"));

				/*
				if ($withDN==1) {
					$nofjs = $nomor_fsj;
				} else {
					$nofjs = "";
				}*/

			$nofjs = $nomor_fsj;

			$prc1=mysqli_query($conn,"insert into trn_salesinvoice (noInv,tglInv,tglJthTempo,hpp,total,diskon,net,ppn,grandTotal,tglEntry,userEntry,DelIndex,NopoCust,nomor_fsj,noFP) 
				values ('$NoInv','$tglInv',DATE_ADD('$tglInv',INTERVAL ".$syrt['expr']." ".$syrt['unit']."),'$hpp','$total','$diskon','$net','$ppn','$grandTotal',CURRENT_TIMESTAMP,'$IdUser','$delIndex','$NopoCust','$nofjs','$noFP')");

			$sql1 = "insert into trn_salesinvoice (noInv,tglInv,tglJthTempo,hpp,total,diskon,net,ppn,grandTotal,tglEntry,userEntry,DelIndex,NopoCust,nomor_fsj) 
				values ('$NoInv','$tglInv',DATE_ADD('$tglInv',INTERVAL ".$syrt['expr']." ".$syrt['unit']."),'$hpp','$total','$diskon','$net','$ppn','$grandTotal',CURRENT_TIMESTAMP,'$IdUser','$delIndex','$NopoCust','$nofjs')";

			// $prc2 = mysqli_query($conn,"insert into trn_salesinvoicedtl (noInv,kodeProduk,hpp,hrgjualSatuan,QtyOrder,QtySuply,diskon,total,Keterangan,nomor_sj)
			// 	select '".$NoInv."' as NoInv,a.kodeProduk,sum(hrgPokok*a.Qty) as hpp,b.hrgJualSatuan,d.Qty as QtyOrder,sum(a.Qty) as QtySuply,sum(diskon) as diskon,sum(((b.hrgJualSatuan*a.Qty)-diskon)) as total,ketKirim as Keterangan,
			// 		case when ifnull(no_sj_gabung,'') ='' then a.nomor_sj else no_sj_gabung end  no_sj_gabung from trn_suratjalandtl a
			// 	inner join mst_produk b on a.kodeProduk=b.kodeProduk
			// 	inner join trn_suratjalan c on a.nomor_sj=c.nomor_sj
			// 	inner join trn_soord d on c.nomor_so=d.nomor_so and a.kodeProduk = d.kodeProduk
			// 	where a.Qty>0 and (c.no_sj_gabung in (".$nomor_sj.") OR c.nomor_sj in (".$nomor_sj.") )
   			// GROUP BY a.kodeproduk,namaProduk,deskripsi,ketKirim,d.Qty
			// ");

			// $prc2 = mysqli_query($conn,"insert into trn_salesinvoicedtl (noInv,kodeProduk,hpp,hrgjualSatuan,QtyOrder,QtySuply,diskon,total,Keterangan,nomor_sj)
			// 	select '".$NoInv."' as NoInv,a.kodeProduk,hrgPokok as hpp,b.hrgJualSatuan,d.Qty as QtyOrder,sum(a.Qty) as QtySuply,sum(diskon) as diskon,sum(((b.hrgJualSatuan*a.Qty)-diskon)) as total,ketKirim as Keterangan,
			// 		case when ifnull(no_sj_gabung,'') ='' then a.nomor_sj else no_sj_gabung end  no_sj_gabung from trn_suratjalandtl a
			// 	inner join mst_produk b on a.kodeProduk=b.kodeProduk
			// 	inner join trn_suratjalan c on a.nomor_sj=c.nomor_sj
			// 	inner join trn_soord d on c.nomor_so=d.nomor_so and a.kodeProduk = d.kodeProduk
			// 	where a.Qty>0 and (c.no_sj_gabung in (".$nomor_sj.") OR c.nomor_sj in (".$nomor_sj.") )
   			// GROUP BY a.kodeproduk,namaProduk,deskripsi,ketKirim,d.Qty
			// ");

			$prc2 = mysqli_query($conn,"insert into trn_salesinvoicedtl (noInv,kodeProduk,hpp,hrgjualSatuan,QtyOrder,QtySuply,diskon,total,Keterangan,nomor_sj)
				select '".$NoInv."' as NoInv,a.kodeProduk,hrgPokok as hpp,b.hrgJualSatuan,d.Qty as QtyOrder,(a.Qty) as QtySuply,(diskon) as diskon,
				(((b.hrgJualSatuan*a.Qty)-diskon)) as total,ketKirim as Keterangan,case when ifnull(no_sj_gabung,'') ='' then a.nomor_sj else no_sj_gabung end no_sj_gabung 
				from trn_suratjalandtl a
				inner join mst_produk b on a.kodeProduk=b.kodeProduk
				inner join trn_suratjalan c on a.nomor_sj=c.nomor_sj
				inner join trn_soord d on c.nomor_so=d.nomor_so  and d.idPoord = a.idPoord
				where a.Qty>0 and (c.no_sj_gabung in (".$nomor_sj.") OR c.nomor_sj in (".$nomor_sj.") )
				UNION
				select '".$NoInv."' as NoInv,a.kodeProduk,hrgPokok as hpp,b.hrgJualSatuan,a.Qty as QtyOrder,(d.Qty) as QtySuply,(d.diskon) as diskon,
				(((b.hrgJualSatuan*d.Qty)-d.diskon)) as total,ketDsj as Keterangan,a.nomor_dsj from trn_suratjalan_directdtl a
				inner join mst_produk b on a.kodeProduk=b.kodeProduk
				inner join trn_suratjalan_direct c on a.nomor_dsj=c.nomor_dsj
				inner join trn_soord_direct d on c.nomor_dsj=d.nomor_dsj and a.idDsjDetail = d.idDsjDetail
				where a.Qty>0 and a.nomor_dsj in (".$nomor_sj.")
			");

			$prc3 = mysqli_query($conn,"insert into acc_artrn (KodeLgn,NoBukti,TglTrn,TglJthTmp,Keterangan,JumlahTrn,TglEntry,userEntry,DelIndex,jnsTrn) values 
				('$KodeLgn[id_pelanggan]','$NoInv','$tglInv',DATE_ADD('$tglInv',INTERVAL ".$syrt['expr']." ".$syrt['unit']."),'Invoice Penjualan','$net',CURRENT_TIMESTAMP,'$IdUser','$delIndex','SI'),
				('$KodeLgn[id_pelanggan]','$NoInv','$tglInv',DATE_ADD('$tglInv',INTERVAL ".$syrt['expr']." ".$syrt['unit']."),'PPN','$ppn',CURRENT_TIMESTAMP,'$IdUser','$delIndex','SI')
			");

			$prc4 = mysqli_query($conn,"insert into acc_gltrn (NoBukti,KodeGl,TglTrn,Keterangan,JlhDebit,JlhKredit,DelIndex,KodeUser,KodeLgn,NoInvoice,tglEntry) values
				('$NoInv','$kodeGl3[kodeGl]','$tglInv','Piutang Penjualan','$grandTotal','0','$delIndex','$IdUser','$KodeLgn[id_pelanggan]','$NoInv',CURRENT_TIMESTAMP),
				('$NoInv','$kodeGl5[kodeGl]','$tglInv','Penjualan','0','$net','$delIndex','$IdUser','$KodeLgn[id_pelanggan]','$NoInv',CURRENT_TIMESTAMP),
				('$NoInv','$kodeGl4[kodeGl]','$tglInv','Ppn Penjualan','0','$ppn','$delIndex','$IdUser','$KodeLgn[id_pelanggan]','$NoInv',CURRENT_TIMESTAMP),
				('$NoInv','$kodeGl6[kodeGl]','$tglInv','BPP Penjualan','$hpp','0','$delIndex','$IdUser','$KodeLgn[id_pelanggan]','$NoInv',CURRENT_TIMESTAMP),
				('$NoInv','$kodeGl7[kodeGl]','$tglInv','BPP dimuka','0','$hpp','$delIndex','$IdUser','$KodeLgn[id_pelanggan]','$NoInv',CURRENT_TIMESTAMP)
			");

			$prc5 = mysqli_query($conn,"update acc_gltrn set NoInvoice='$NoInv' where NoBukti in ($nomor_sj)");

			if ($cek['is_fp']=='1' and $syrt['typeppn']!='T') {
				$sql6 = "
					INSERT INTO fp_sifpstandar (nofps,tglfps,npwp,nppkp,keterangan,hargajual,ppn,ppn_bm,kodeuser,tglentry,nama_fp,alamat_fp1,alamat_fp2,modul,kodelgn) VALUES 
					('".$noFP."','$tglInv','" .$syrt['npwp']. "','','Sales Invoice :".$NoInv."','$net','$ppn','0','".$IdUser."','$tglInv','" .$syrt['namacustomer']. "','".$syrt['alamat']. "','" .$syrt['alamat_kota']. "','SI','" .$syrt['id_pelanggan']. "')
				";
				$prc6=mysqli_query($conn,$sql6);
				$prc7=mysqli_query($conn,"UPDATE fp_konterdtl set noInv = '".$NoInv."' where noFp = '".$noFP."'");
			} else {
				$prc6 = true; $prc7 = true;
			}

			if ($prc1 and $prc2  and $prc3 and $prc4 and $prc5 and $prc6 and $prc7) {
				mysqli_query($conn,"COMMIT");
				$msg .= "Data Save!!";
			} else {
				mysqli_query($conn,"ROLLBACK");
				if (!$prc1) { $msg .= "Failed Query1!".$sql1; }
				if (!$prc2) { $msg .= "Failed Query2!"; }
				if (!$prc3) { $msg .= "Failed Query3!"; }
				if (!$prc4) { $msg .= "Failed Query4!"; }
				if (!$prc5) { $msg .= "Failed Query5!"; }
				if (!$prc6) { $msg .= "Failed Query6!".$sql6; }
				if (!$prc7) { $msg .= "Failed Query7!"; }
				// $msg .= "Failed!!";
			}
		mysqli_query($conn,"return");
		echo $msg;
	} else if ($action=='dataDetail') {
		$id = addslashes($_REQUEST['id']);
		$prc = mysqli_fetch_array(mysqli_query($conn,"
			select a.NoInv,tglInv,c.id_pelanggan,
			nama_perusahaan as namaCustomer,
			case when isdn = 1 then 'Yes' else 'No' end as tipeDN,
			a.nomor_fsj,namaPanggilan,
			CONCAT(alamatPenagihan,if(ifnull(alamat_no,'')='','',concat(' No. ',alamat_no)),if(ifnull(alamat_rt,'')='','',concat(' RT. ',alamat_rt)),if(ifnull(alamat_rw,'')='','',concat(' RW. ',alamat_rw)),if(ifnull(alamat_kelurahan,'')='','',concat(' ',alamat_kelurahan)),if(ifnull(alamat_kecamatan,'')='','',concat(' ',alamat_kecamatan)),if(ifnull(alamat_kota,'')='','',concat(' ',alamat_kota)),if(ifnull(alamat_kodepos,'')='','',concat(' (',alamat_kodepos,')')),if(ifnull(alamat_propinsi,'')='','',concat(' ',alamat_propinsi))) as alamat,
			sum(e.hrgjualSatuan * e.QtySuply) as total,sum(e.diskon) diskon, (a.net+sum(e.diskon)) as totalNew,
	        a.net,a.ppn, a.grandTotal,typePpn
	        from trn_salesinvoice a 
				inner join trn_salesinvoicedtl e on a.noInv=e.noInv
				inner join trn_suratjalan b on (e.nomor_sj=b.no_sj_gabung OR e.nomor_sj=b.nomor_sj)
				inner join trn_sohdr c on b.nomor_so=c.nomor_so
				inner join mst_pelanggan d on c.id_pelanggan=d.id_pelanggan
	     	where idSI = '".$id."'
	   		Group by a.NoInv,tglInv,c.id_pelanggan,
			nama_perusahaan ,
			case when isdn = 1 then 'Yes' else 'No' end ,
			a.nomor_fsj,namaPanggilan,
			CONCAT(alamatPenagihan,' No.',alamat_no,' RT.',alamat_rt,' RW.',alamat_rw,' ',alamat_kelurahan,' ',alamat_kecamatan,' ',alamat_kota,' ',alamat_kodepos) 
			UNION
			select a.NoInv,tglInv,b.id_pelanggan,
			nama_perusahaan as namaCustomer,
			case when isdn = 1 then 'Yes' else 'No' end as tipeDN,
			a.nomor_fsj,namaPanggilan,
			CONCAT(alamatPenagihan,if(ifnull(alamat_no,'')='','',concat(' No. ',alamat_no)),if(ifnull(alamat_rt,'')='','',concat(' RT. ',alamat_rt)),if(ifnull(alamat_rw,'')='','',concat(' RW. ',alamat_rw)),if(ifnull(alamat_kelurahan,'')='','',concat(' ',alamat_kelurahan)),if(ifnull(alamat_kecamatan,'')='','',concat(' ',alamat_kecamatan)),if(ifnull(alamat_kota,'')='','',concat(' ',alamat_kota)),if(ifnull(alamat_kodepos,'')='','',concat(' (',alamat_kodepos,')')),if(ifnull(alamat_propinsi,'')='','',concat(' ',alamat_propinsi))) as alamat,
			sum(e.hrgjualSatuan * e.QtySuply) as total,sum(e.diskon) diskon, (a.net+sum(e.diskon)) as totalNew,
	        a.net,a.ppn, a.grandTotal,typePpn
	        from trn_salesinvoice a 
				inner join trn_salesinvoicedtl e on a.noInv=e.noInv
				inner join trn_suratjalan_direct b on (e.nomor_sj=b.nomor_dsj)
	            inner join trn_suratjalan_directdtl f on b.nomor_dsj=f.nomor_dsj and f.kodeproduk=e.kodeproduk
	            inner join trn_soord_direct g on f.nomor_dsj=g.nomor_dsj and f.idDsjDetail=g.idDsjDetail
				inner join trn_sohdr_direct c on g.nomor_dso=c.nomor_dso
				inner join mst_pelanggan d on b.id_pelanggan=d.id_pelanggan
	     	where idSI = '".$id."'
	   		Group by a.NoInv,tglInv,b.id_pelanggan,
			nama_perusahaan ,
			case when isdn = 1 then 'Yes' else 'No' end ,
			a.nomor_fsj,namaPanggilan,
			CONCAT(alamatPenagihan,' No.',alamat_no,' RT.',alamat_rt,' RW.',alamat_rw,' ',alamat_kelurahan,' ',alamat_kecamatan,' ',alamat_kota,' ',alamat_kodepos)
		"));
		if ($prc['typePpn']=='T') {
		    $dpp = $prc['grandTotal'];
		    $ppn = 0;
		} else if ($prc['typePpn']=='E') {
		    $dpp = $prc['net'];
		    $ppn = $prc['ppn'];
		} else if ($prc['typePpn']=='I') {
		    $dpp  = $prc['grandTotal']-$prc['ppn'];
		    $ppn = $prc['ppn'];
		} else {
		    $dpp = $prc['grandTotal'];
		    $ppn = 0;
		}
		echo $prc['NoInv']."#".datenull($prc['tglInv'])."#".$prc['id_pelanggan']."#".$prc['namaCustomer']."#".$prc['tipeDN']."#".$prc['nomor_fsj']."#".$prc['alamat']."#".$prc['totalNew']."#".$prc['diskon']."#".$prc['net']."#".$ppn."#".$prc['grandTotal']."#".$prc['namaPanggilan']."#".$dpp;
	} else if ($action=='dataDetail2') {
		$id = addslashes($_REQUEST['id']);
		$sql = "
			select a.kodeProduk,namaProduk,deskripsi,group_concat(a.nomor_sj) as nomor_sj,a.hrgjualSatuan,QtyOrder,sum(QtySuply) as QtySuply,sum(a.diskon) as diskon,
			sum(a.total) as total,Keterangan from trn_salesinvoicedtl a
			inner join mst_produk b on a.kodeProduk=b.kodeProduk
			inner join trn_salesinvoice c on a.noInv=c.noInv where idSI= '".$id."'
			group by a.kodeProduk,namaProduk,a.hrgjualSatuan having sum(QtySuply)>0
			-- Group by a.kodeProduk,namaProduk,deskripsi,a.hrgjualSatuan,QtyOrder, Keterangan
			-- Having sum(QtySuply)>0

			
		";
		$rsl = mysqli_query($conn,$sql);
		$no = 1;
		while ($dt = mysqli_fetch_array($rsl)) {
			echo "
				<tr>
					<td><center>".$no."</center></td>
					<td style='min-width:150px'>".$dt['kodeProduk']."</td>
					<td>".$dt['namaProduk']."</td>
					<td>".$dt['deskripsi']."</td>
					<td style='white-space: nowrap;'>".str_replace(",","<br/>",$dt['nomor_sj'])."</td>
					<td align='right'>".number_format($dt['hrgjualSatuan'],0,",",".")."</td>
					<td align='right'>".number_format($dt['QtyOrder'],0,",",".")."</td>
					<td align='right'>".number_format($dt['QtySuply'],0,",",".")."</td>
					<td align='right'>".number_format($dt['diskon'],0,",",".")."</td>
					<td align='right'>".number_format($dt['total'],0,",",".")."</td>
					<td>".$dt['Keterangan']."</td>
				</tr>
			";
			$no++;
		}
	} else if ($action=='loadProduk') {
		$id = $_REQUEST['id'];
		$jnsSJ = $_REQUEST['jnsSJ'];
		// $sql = "select a.kodeProduk,namaProduk,deskripsi,(hrgPokok*a.Qty) as hpp,b.hrgJualSatuan,d.Qty as QtyOrder,a.Qty as QtySuply,diskon,((b.hrgJualSatuan*a.Qty)-diskon) as totals,(b.hrgJualSatuan*a.Qty) as total,ketKirim from trn_suratjalandtl a
		// 	inner join mst_produk b on a.kodeProduk=b.kodeProduk
		// 	inner join trn_suratjalan c on a.nomor_sj=c.nomor_sj
		// 	inner join trn_soord d on c.nomor_so=d.nomor_so and d.kodeProduk = a.kodeProduk 
		// 	where a.Qty>0 and a.nomor_sj in (".$id.")";

		// $sql = "
		// 	select a.kodeProduk,namaProduk,deskripsi,sum(hrgPokok*a.Qty) as hpp,b.hrgJualSatuan hrgjualSatuan,sum(d.Qty) as QtyOrder,
		// 	sum(a.Qty) as QtySuply,sum(diskon) diskon,sum((b.hrgJualSatuan*a.Qty)-diskon) as totals,sum(b.hrgJualSatuan*a.Qty) as total,ketKirim 
		// 	from trn_suratjalandtl a
		// 	inner join mst_produk b on a.kodeProduk=b.kodeProduk
		// 	inner join trn_suratjalan c on a.nomor_sj=c.nomor_sj
		// 	inner join trn_soord d on c.nomor_so=d.nomor_so and d.kodeProduk = a.kodeProduk 
		// 	where a.Qty>0 and (c.no_sj_gabung in (".$id.") OR c.nomor_sj in (".$id."))
		// 	 GROUP BY a.kodeproduk,namaProduk,deskripsi,ketKirim,d.Qty
		// ";
		if ($jnsSJ=='SJ') {
			$sql = "
				select a.kodeProduk,namaProduk,deskripsi,(hrgPokok*sum(a.Qty)) as hpp,b.hrgJualSatuan hrgjualSatuan,sum(d.Qty) as QtyOrder,
				sum(a.Qty) as QtySuply,diskon,((b.hrgJualSatuan*sum(a.Qty))-diskon) as totals,(b.hrgJualSatuan*sum(a.Qty)) as total,ketKirim 
				from trn_suratjalandtl a
				inner join mst_produk b on a.kodeProduk=b.kodeProduk
				inner join trn_suratjalan c on a.nomor_sj=c.nomor_sj
				inner join trn_soord d on c.nomor_so=d.nomor_so and d.idPoord = a.idPoord
				where a.Qty>0 and (c.no_sj_gabung in (".$id.") OR c.nomor_sj in (".$id."))
				GROUP BY a.kodeproduk, namaProduk, deskripsi,ketKirim,d.Qty
			";
		} else if ($jnsSJ=='DSJ') {
			$sql = "
				select a.kodeProduk,namaProduk,deskripsi,(hrgPokok*sum(d.Qty)) as hpp,b.hrgJualSatuan hrgjualSatuan,sum(a.Qty) as QtyOrder,
				sum(d.Qty) as QtySuply,d.diskon,((b.hrgJualSatuan*sum(d.Qty))-d.diskon) as totals,(b.hrgJualSatuan*sum(d.Qty)) as total,
				ketDsj as ketKirim 
				from trn_suratjalan_directdtl a
				inner join mst_produk b on a.kodeProduk=b.kodeProduk
				inner join trn_suratjalan_direct c on a.nomor_dsj=c.nomor_dsj
				inner join trn_soord_direct d on c.nomor_dsj=d.nomor_dsj and d.kodeProduk = a.kodeProduk 
				where a.nomor_dsj in (".$id.")
				GROUP BY a.kodeproduk, namaProduk, deskripsi
			";
		}
		
		// echo $sql;
		$rsl = mysqli_query($conn,$sql);
		$no = 1;
		$count = mysqli_num_rows(mysqli_query($conn,$sql));
		$html = '';
		while ($dt=mysqli_fetch_array($rsl)) {
		    $html.= '
			    <tr id="form_'.$no.'">
				    <td><center>'.$no.'</center></td>
				    <td>
				        <input class="form-control" id="kodeProduk_'.$no.'" value="'.$dt['kodeProduk'].'" readonly="" type="text">
				    </td>
				    <td>
				    	<input class="form-control" id="namaProduk_'.$no.'" value="'.$dt['namaProduk'].'" readonly="" type="text">
				    </td>
				    <td>
				    	<input class="form-control" id="deskripsi_'.$no.'" value="'.$dt['deskripsi'].'"  readonly="" type="text">
				    </td>
				    <td>
				    	<input class="form-control" id="hpp_'.$no.'" value="'.round($dt['hpp'],0).'" name="hpp[]" readonly="" type="hidden">
				        <input class="form-control" id="hrgjualSatuan_'.$no.'" value="'.round($dt['hrgjualSatuan'],2).'" readonly="" type="text">
				    </td>
				    <td>
				        <input class="form-control" id="QtyOrder_'.$no.'" value="'.$dt['QtyOrder'].'" readonly="" type="text">
				    </td>
				    <td>
				        <input class="form-control" id="QtySuply_'.$no.'" value="'.$dt['QtySuply'].'" readonly="" type="text">
				    </td>
				    <td>
				        <input class="form-control id="diskon_'.$no.'" value="'.$dt['diskon'].'" name="diskon[]" readonly="" type="text">
				    </td>
				    <td>
				        <input class="form-control" value="'.round($dt['totals'],0).'" readonly="" type="text">
				        <input class="form-control" id="total_'.$no.'" value="'.round($dt['total'],0).'" name="total[]" readonly="" type="hidden">
				    </td>
				    <td>
				        <input class="form-control" id="ketKirim_'.$no.'" value="'.$dt['ketKirim'].'" readonly="" type="text">
				    </td>
				</tr>';
			$no++;
		}
		echo $count."#".$html;
	} else if ($action=='cekFp') {
		$cek = mysqli_fetch_array(mysqli_query($conn,"select is_fp from sys_profile where sys_id=1"));
		if ($cek['is_fp']=='1') {
			$s_cekFp = "select count(idFp) sisa from fp_konterdtl where ifnull(noInv,'')='' and is_active = 1";
			$cekFp = mysqli_fetch_array(mysqli_query($conn,$s_cekFp));
			$sisaFP = $cekFp['sisa'];
			echo $sisaFP;
		} else {
			echo "N";
		}
	}
?>