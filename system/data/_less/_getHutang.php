<?php
	$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : null;
	if ($action=='jnsHutang') {
		require_once ('../inc/conn.php');
		$KodeDealer = addslashes($_REQUEST['KodeDealer']);
		$cekData = isset($_REQUEST['cekData']) ? $_REQUEST['cekData'] : '';
		if ($KodeDealer=='2010') {
			$opt = array('2' => 'Unit', '3' => 'Spare Part');
			echo "<option value=''>- Pilih -</option>";
			foreach ($opt as $key => $value) {
				$plh1 = ($cekData==$key) ? "selected" : "";
				echo "<option value='".$key."' $plh1>".$value."</option>";
			}
		} else {
			$sql = "
				select KodeDealer, NamaDealer,
				  indicatorname,
				  indicatorvalue
				from ProfilSPK..DealerBD 
				unpivot
				(
				  indicatorvalue
				  for indicatorname in (Sales, BP)
				) unpiv where ISNULL(indicatorvalue,'')!='' and KodeDealer = '".$KodeDealer."'
			";
			$qry = mssql_query($sql,$conns);	
			echo "<option value=''>- Pilih -</option>";
			while($row = mssql_fetch_array($qry)){
				if ($row['indicatorname']=='Sales') {
					$value = 0;
					$name = "GR";
				} else {
					$value = 1;
					$name = $row['indicatorname'];
				}
				$plh2 = ($cekData==$value) ? "selected" : "";
				echo "<option value='".$value."' $plh2>".strtoupper($name)."</option>";
			}
		}
	} else if ($action=='getAkun') {
		$KodeDealer = addslashes($_REQUEST['KodeDealer']);
		include '../inc/koneksi.php';
		$noTagihan = addslashes($_REQUEST['noTagihan']);
		$accFix = addslashes($_REQUEST['accFix']);
		$bengkel = addslashes($_REQUEST['bengkel']);
		$bp = addslashes($_REQUEST['bp']);
		$jnsHutang = addslashes($_REQUEST['jnsHutang']);
		if ($jnsHutang==0) {
			// $cekDb = mssql_fetch_array(mssql_query("SELECT count(SCHEMA_NAME) tot FROM INFORMATION_SCHEMA.SCHEMATA 
			// 	WHERE SCHEMA_NAME = '".$accFix."'",$connCab));
			// echo $cekDb['tot'];
			// if ($cekDb['tot']>0) {
				$akunD = mssql_fetch_array(mssql_query("
					select DISTINCT c.KodeGl,namaGl from [$accFix]..gltrn c
					inner join [$accFix]..glmst d on c.KodeGl=d.KodeGl
					where nobukti in (select 'S-'+NOWO from [$bengkel]..KONFIRM_SUBLET 
					where replace(NO_TAGIHAN,' ','')='".str_replace(' ', '', $noTagihan)."') and JlhKredit = 0
				"));
				$akunK = mssql_fetch_array(mssql_query("
					select DISTINCT c.KodeGl,namaGl from [$accFix]..gltrn c
					inner join [$accFix]..glmst d on c.KodeGl=d.KodeGl
					where nobukti in (select 'S-'+NOWO from [$bengkel]..KONFIRM_SUBLET 
					where replace(NO_TAGIHAN,' ','')='".str_replace(' ', '', $noTagihan)."') and JlhDebit = 0
				"));
				echo $akunD['KodeGl']."_cn_".$akunD['namaGl']."_cn_".$akunK['KodeGl']."_cn_".$akunK['namaGl'];
			// } else {
			// 	echo "0_cn_0_cn_0_cn_0";
			// }
		} else if ($jnsHutang==1) {
			// $cekDb = mssql_fetch_array(mssql_query("SELECT count(SCHEMA_NAME) tot FROM INFORMATION_SCHEMA.SCHEMATA 
			// 	WHERE SCHEMA_NAME = '".$accFix."'",$connCab));
			// if ($cekDb['tot']>0) {
				$akunD = mssql_fetch_array(mssql_query("
					select DISTINCT b.KodeGl,namaGl from [$accFix]..gltrn a
					inner join [$accFix]..glmst b on a.KodeGl=b.KodeGl
					where nobukti in (select NoPo from [$bp]..JobSublet where replace(kodeTagih,' ','') = '".str_replace(' ', '', $noTagihan)."')
					and JlhKredit = 0
					union
					select DISTINCT b.KodeGl,namaGl from [$accFix]..gltrn a
					inner join [$accFix]..glmst b on a.KodeGl=b.KodeGl
					where nobukti in (select NoInv from [$bp]..AP_Profit where replace(Bayar,' ','') = '".str_replace(' ', '', $noTagihan)."')
					and Keterangan like '%HPP Jasa BP%'
				"));
				$akunK = mssql_fetch_array(mssql_query("
					select DISTINCT b.KodeGl,namaGl from [$accFix]..gltrn a
					inner join [$accFix]..glmst b on a.KodeGl=b.KodeGl
					where nobukti in (select NoPo from [$bp]..JobSublet where replace(kodeTagih,' ','') = '".str_replace(' ', '', $noTagihan)."')
					and JlhDebit = 0
					union
					select DISTINCT b.KodeGl,namaGl from [$accFix]..gltrn a
					inner join [$accFix]..glmst b on a.KodeGl=b.KodeGl
					where nobukti in (select NoInv from [$bp]..AP_Profit where replace(Bayar,' ','') = '".str_replace(' ', '', $noTagihan)."')
					and Keterangan like '%Hutang Pemborong%'
				"));
				echo $akunD['KodeGl']."_cn_".$akunD['namaGl']."_cn_".$akunK['KodeGl']."_cn_".$akunK['namaGl'];
			// } else {
			// 	echo "0_cn_0_cn_0_cn_0";
			// }
		} else { 
			// $cekDb = mssql_fetch_array(mssql_query("SELECT count(SCHEMA_NAME) tot FROM INFORMATION_SCHEMA.SCHEMATA 
			// 	WHERE SCHEMA_NAME = '".$accFix."'",$connCab));
			// if ($cekDb['tot']>0) {
				$akunD = mssql_fetch_array(mssql_query("
					select DISTINCT c.KodeGl,namaGl from [$accFix]..gltrn c
					inner join [$accFix]..glmst d on c.KodeGl=d.KodeGl
					where replace(nobukti,' ','') = '".str_replace(' ', '', $noTagihan)."' and JlhKredit = 0 and c.KodeGl <> '11510000'
				"));
				$akunK = mssql_fetch_array(mssql_query("
					select DISTINCT c.KodeGl,namaGl from [$accFix]..gltrn c
					inner join [$accFix]..glmst d on c.KodeGl=d.KodeGl
					where replace(nobukti,' ','') = '".str_replace(' ', '', $noTagihan)."' and JlhDebit = 0
				"));
				echo $akunD['KodeGl']."_cn_".$akunD['namaGl']."_cn_".$akunK['KodeGl']."_cn_".$akunK['namaGl'];
			// } else {
			// 	echo "0_cn_0_cn_0_cn_0";
			// }
		}
	} else if ($action=='getDataTagihan'){
		$KodeDealer = addslashes($_REQUEST['KodeDealer']);
		$jnsHutang = addslashes($_REQUEST['jnsHutang']);
		$KodeAP = addslashes($_REQUEST['KodeAP']);
		$NoBuktiPengajuan = addslashes($_REQUEST['NoBuktiPengajuan']);
		include '../inc/koneksi.php';
		if ($KodeAP!="" && $jnsHutang=='0') {
			$kode = "and KODESUPP like '%".$KodeAP."%'";
		} else if ($KodeAP!="" && $jnsHutang=='1') {
			$kode = "and KodeAR like '%".$KodeAP."%'";
		} else {
			$kode = "";
		}
		// $tagih = cekTagihan();
		// if ($tagih!="0") {
		// 	$tagihan = "and NO_TAGIHAN not in ($tagih)";
		// } else {
			$tagihan = "";
		// }
		if ($jnsHutang=='0') {
			$sql = "
				select (select DBASECABANG from profilbengkel..profil) cbg, (select FS_KD_DEALER from [$bengkel].[dbo].T_PROFIL) kdcbg,
				NO_TAGIHAN as noTagihan,TANGGAL as tglTagihan, KODESUPP, year(tglValid) Tahun, MONTH(tglValid) Bulan, SUM(QTY*harga) as totalTagihan
				from [$bengkel]..KONFIRM_SUBLET a 
				inner join [$bengkel]..JOBSUBLET b on a.NOWO = b.NO_WO and a.NOINV = b.NO_INV and a.SUBLET = b.NAMABARANG 
				where NO_TAGIHAN=NO_TAGIHAN $tagihan $search $kode
				group by NO_TAGIHAN, KODESUPP, year(tglValid), MONTH(tglValid) ,TANGGAL
				order by year(tglValid), MONTH(tglValid) , KODESUPP
			";
			// echo $sql;
			$total = mssql_num_rows(mssql_query("select NO_TAGIHAN
				from [$bengkel]..KONFIRM_SUBLET a 
				inner join [$bengkel]..JOBSUBLET b on a.NOWO = b.NO_WO and a.NOINV = b.NO_INV and a.SUBLET = b.NAMABARANG 
				where NO_TAGIHAN=NO_TAGIHAN $tagihan $search $kode
				group by NO_TAGIHAN, KODESUPP, year(tglValid), MONTH(tglValid) ,TANGGAL
				order by year(tglValid), MONTH(tglValid) , KODESUPP",$connCab));
		} else if ($jnsHutang=='1') {
			$wSql = "
				WITH BP (NO_TAGIHAN,tglTagihan,SupplierSublet,totalTagihan,KodeAR) as (
					Select a.KodeTagih as NO_TAGIHAN, CONVERT(date,A.Tgltagih,105) as tglTagihan,
					A.Supplier as SupplierSublet, Sum(A.HargaBeli) as totalTagihan,KodeAR  
					FROM [$bp]..JobSublet A 
					Inner Join [$bp]..JobDTA B On A.NoWO = B.NOWO 
					inner join [$bp]..M_SupplierSublet C on C.Kode = A.Supplier 
					Where A.tagih = 1 And A.NoPO <> '' and Right(A.nopo,1) <> 'R' And NoNota <> ''
					Group By A.SUPPLIER,a.KodeTagih, CONVERT(date,A.Tgltagih,105),KodeAR 
					UNION
					Select s.Bayar as NO_TAGIHAN, CONVERT(date,s.TglBayar, 105) as tglTagihan,
					S.[Group] as GroupBorong, SUM(s.ProfitAR) as totalTagihan,Alamat3 as KodeAR 
					FROM [$bp]..AP_Profit s 
					INNER JOIN [$bp]..JobDta a On s.Nowo = a.Nowo 
					LEFT JOIN [$bp]..M_GroupMekanik g On s.[Group] = g.KodeGroup
					Where g.Ext=1 And s.Bayar <> ''
					Group By s.[Group], s.Bayar, CONVERT(date,s.TglBayar, 105),Alamat3 
				)
			";
			$sql = "
				$wSql 
				select NO_TAGIHAN as noTagihan,tglTagihan,SupplierSublet,totalTagihan,KodeAR,year(tglTagihan) as Tahun, MONTH(tglTagihan) Bulan from BP where NO_TAGIHAN=NO_TAGIHAN $tagihan $search $kode
			";
			$total = mssql_num_rows(mssql_query("$wSql select NO_TAGIHAN from BP where ISNULL(tglTagihan,'')<>'' $search $kode",$connCab));
		} else if ($jnsHutang=='2') {
			$wsql = "
				with unit (NO_TAGIHAN,tglTagihan,KodeAR,totalTagihan) as (
					select NoFaktur as NO_TAGIHAN,min(TglTrnFaktur) as tglTagihan,max(KodeLgn) as KodeAR,sum(JumlahTrn) as totalTagihan
					from [$table]..Aptrn where NoFaktur like 'MV%' 
					GROUP BY NoFaktur HAVING sum(jumlahtrn)>0
				)
			";
			$sql = "
				$wsql select top $rp NO_TAGIHAN as noTagihan,tglTagihan,KodeAR,totalTagihan,year(tglTagihan) as Tahun,month(tglTagihan) as Bulan from unit where NO_TAGIHAN=NO_TAGIHAN $tagihan $search $kode
			";
			$total = mssql_num_rows(mssql_query("$wsql select NO_TAGIHAN from unit where ISNULL(tglTagihan,'')<>'' $tagihan $search $kode",$connCab));
		} else if ($jnsHutang=='3') {
			$wsql = "
				with part (NO_TAGIHAN,tglTagihan,KodeAR,totalTagihan) as (
					select NoBukti as NO_TAGIHAN,TglTrnFaktur as tglTagihan,a.kodelgn as KodeAR,sum(JumlahTrn) as totalTagihan 
					from [$table]..Aptrn a inner join [$table]..apmst b on a.kodelgn=b.kodelgn
					where a.kodelgn = 'PRTTAM'
					GROUP BY a.kodelgn,NoBukti,TglTrnFaktur HAVING sum(jumlahtrn)>0
				)
			";
			$sql = "
				$wsql select top $rp NO_TAGIHAN as noTagihan,tglTagihan,KodeAR,totalTagihan,year(tglTagihan) as Tahun,month(tglTagihan) as Bulan from part where NO_TAGIHAN = NO_TAGIHAN $tagihan $search $kode
			";
			$total = mssql_num_rows(mssql_query("$wsql select NO_TAGIHAN from part where ISNULL(tglTagihan,'')<>'' $tagihan $search $kode",$connCab));
		}
		echo $sql;
		$result = mssql_query($sql,$connCab);
		$rows = array();
		$no=1;
		while ($row = mssql_fetch_array($result)) {
			$accFix = $acc."-".$row['Tahun']."".bln($row['Bulan']);
			$idTagihan = "".$row['noTagihan']."_cn_".$row['totalTagihan']."_cn_".$accFix."_cn_".$bengkel."_cn_".$jnsHutang."_cn_".$bp."_cn_".date('Y-m-d', strtotime($row['tglTagihan']))."";
			if ($jnsHutang!='3') {
				$xml = "<a href=\"javascript:void(0)\" onclick=\"show5('".$row['noTagihan']."');\">".utf8_encode($row['noTagihan'])."</a>";
			} else {
				$xml = "".utf8_encode($row['noTagihan'])."";
			}
			$plh = checkNoTagihan($NoBuktiPengajuan,$row['noTagihan']);
			echo "
				<tr>
					<td>
						<div style='text-align: center;padding:3px;width:40px'>
							<input type='checkbox' id='idTagihan-".$no."' onclick='coba(".$no.");' name='idTagihan[]' value='".$idTagihan."' ".$plh.">
						</div>
					</td>
					<td><div style='text-align: left;padding:3px;width:150px'>".$xml."</div></td>
					<td><div style='text-align: left;padding:3px;width:100px'>".datenull($row['tglTagihan'])."</div></td>
					<td><div style='text-align: right;padding:3px;width:150px'>".number_format($row['totalTagihan'],0,",",".")."</div></td>
				</tr>
			";
			$no++;
		}
	} else {
		$KodeDealer = addslashes($_REQUEST['KodeDealer']);
		$jnsHutang = addslashes($_REQUEST['jnsHutang']);
		$KodeAP = addslashes($_REQUEST['KodeAP']);
		include '../inc/koneksi.php';
		$page = isset($_POST['page']) ? $_POST['page'] : 1;
		$rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
		$sortname = isset($_POST['sortname']) ? $_POST['sortname'] : 'NO_TAGIHAN';
		$sortorder = isset($_POST['sortorder']) ? $_POST['sortorder'] : 'asc';
		$query = isset($_POST['query']) ? $_POST['query'] : false;
		$qtype = isset($_POST['qtype']) ? $_POST['qtype'] : false;

		$page = $_POST['page'];
		$rp = $_POST['rp'];
		$sortname = $_POST['sortname'];
		$sortorder = $_POST['sortorder'];

		if (!$sortname) $sortname = 'NO_TAGIHAN';
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

		$tagih = cekTagihan();
		if ($tagih!="0") {
			$tagihan = "and NO_TAGIHAN not in ($tagih)";
		} else {
			$tagihan = "";
		}
		
		if ($jnsHutang=='0') {
			$sql = "
				select top $rp (select DBASECABANG from profilbengkel..profil) cbg, (select FS_KD_DEALER from [$bengkel].[dbo].T_PROFIL) kdcbg,
				NO_TAGIHAN as noTagihan,TANGGAL as tglTagihan, KODESUPP, year(tglValid) Tahun, MONTH(tglValid) Bulan, SUM(QTY*harga) as totalTagihan
				from [$bengkel]..KONFIRM_SUBLET a 
				inner join [$bengkel]..JOBSUBLET b on a.NOWO = b.NO_WO and a.NOINV = b.NO_INV and a.SUBLET = b.NAMABARANG 
				where replace(NO_TAGIHAN,' ','') not in (
					select top $start NO_TAGIHAN
					from [$bengkel]..KONFIRM_SUBLET a 
					inner join [$bengkel]..JOBSUBLET b on a.NOWO = b.NO_WO and a.NOINV = b.NO_INV and a.SUBLET = b.NAMABARANG 
					where NO_TAGIHAN=NO_TAGIHAN $search $kode
					group by NO_TAGIHAN, KODESUPP, year(tglValid), MONTH(tglValid) ,TANGGAL
					order by year(tglValid), MONTH(tglValid) , KODESUPP
				) $tagihan $search $kode
				group by NO_TAGIHAN, KODESUPP, year(tglValid), MONTH(tglValid) ,TANGGAL
				order by year(tglValid), MONTH(tglValid) , KODESUPP
			";
			// echo $sql;
			$total = mssql_num_rows(mssql_query("select NO_TAGIHAN
				from [$bengkel]..KONFIRM_SUBLET a 
				inner join [$bengkel]..JOBSUBLET b on a.NOWO = b.NO_WO and a.NOINV = b.NO_INV and a.SUBLET = b.NAMABARANG 
				where NO_TAGIHAN=NO_TAGIHAN $tagihan $search $kode
				group by NO_TAGIHAN, KODESUPP, year(tglValid), MONTH(tglValid) ,TANGGAL
				order by year(tglValid), MONTH(tglValid) , KODESUPP",$connCab));
		} else if ($jnsHutang=='1') {
			$wSql = "
				WITH BP (NO_TAGIHAN,tglTagihan,SupplierSublet,totalTagihan,KodeAR) as (
					Select a.KodeTagih as NO_TAGIHAN, CONVERT(date,A.Tgltagih,105) as tglTagihan,
					A.Supplier as SupplierSublet, Sum(A.HargaBeli) as totalTagihan,KodeAR  
					FROM [$bp]..JobSublet A 
					Inner Join [$bp]..JobDTA B On A.NoWO = B.NOWO 
					inner join [$bp]..M_SupplierSublet C on C.Kode = A.Supplier 
					Where A.tagih = 1 And A.NoPO <> '' and Right(A.nopo,1) <> 'R' And NoNota <> ''
					Group By A.SUPPLIER,a.KodeTagih, CONVERT(date,A.Tgltagih,105),KodeAR 
					UNION
					Select s.Bayar as NO_TAGIHAN, CONVERT(date,s.TglBayar, 105) as tglTagihan,
					S.[Group] as GroupBorong, SUM(s.ProfitAR) as totalTagihan,Alamat3 as KodeAR 
					FROM [$bp]..AP_Profit s 
					INNER JOIN [$bp]..JobDta a On s.Nowo = a.Nowo 
					LEFT JOIN [$bp]..M_GroupMekanik g On s.[Group] = g.KodeGroup
					Where g.Ext=1 And s.Bayar <> ''
					Group By s.[Group], s.Bayar, CONVERT(date,s.TglBayar, 105),Alamat3 
				)
			";
			$sql = "
				$wSql select top $rp NO_TAGIHAN as noTagihan,tglTagihan,SupplierSublet,totalTagihan,KodeAR,year(tglTagihan) as Tahun, MONTH(tglTagihan) Bulan from BP 
					where NO_TAGIHAN not in (
					select top $start NO_TAGIHAN from BP where ISNULL(tglTagihan,'')<>'' $tagihan $search $kode
				) and ISNULL(tglTagihan,'')<>'' $tagihan $search $kode
			";
			$total = mssql_num_rows(mssql_query("$wSql select NO_TAGIHAN from BP where ISNULL(tglTagihan,'')<>'' $search $kode",$connCab));
		} else if ($jnsHutang=='2') {
			$wsql = "
				with unit (NO_TAGIHAN,tglTagihan,KodeAR,totalTagihan) as (
					select NoFaktur as NO_TAGIHAN,min(TglTrnFaktur) as tglTagihan,max(KodeLgn) as KodeAR,sum(JumlahTrn) as totalTagihan
					from [$table]..Aptrn where NoFaktur like 'MV%' 
					GROUP BY NoFaktur HAVING sum(jumlahtrn)>0
				)
			";
			$sql = "
				$wsql select top $rp NO_TAGIHAN as noTagihan,tglTagihan,KodeAR,totalTagihan,year(tglTagihan) as Tahun,month(tglTagihan) as Bulan from unit 
					where NO_TAGIHAN not in (
					select top $start NO_TAGIHAN from unit where ISNULL(tglTagihan,'')<>'' $tagihan $search $kode
				) and ISNULL(tglTagihan,'')<>'' $tagihan $search $kode
			";
			$total = mssql_num_rows(mssql_query("$wsql select NO_TAGIHAN from unit where ISNULL(tglTagihan,'')<>'' $tagihan $search $kode",$connCab));
		} else if ($jnsHutang=='3') {
			$wsql = "
				with part (NO_TAGIHAN,tglTagihan,KodeAR,totalTagihan) as (
					select NoBukti as NO_TAGIHAN,TglTrnFaktur as tglTagihan,a.kodelgn as KodeAR,sum(JumlahTrn) as totalTagihan 
					from [$table]..Aptrn a inner join [$table]..apmst b on a.kodelgn=b.kodelgn
					where a.kodelgn = 'PRTTAM'
					GROUP BY a.kodelgn,NoBukti,TglTrnFaktur HAVING sum(jumlahtrn)>0
				)
			";
			$sql = "
				$wsql select top $rp NO_TAGIHAN as noTagihan,tglTagihan,KodeAR,totalTagihan,year(tglTagihan) as Tahun,month(tglTagihan) as Bulan from part 
					where NO_TAGIHAN not in (
					select top $start NO_TAGIHAN from part where ISNULL(tglTagihan,'')<>'' $tagihan $search $kode
				) and ISNULL(tglTagihan,'')<>'' $tagihan $search $kode
			";
			$total = mssql_num_rows(mssql_query("$wsql select NO_TAGIHAN from part where ISNULL(tglTagihan,'')<>'' $tagihan $search $kode",$connCab));
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

				$accFix = $acc."-".$row_jenis['Tahun']."".bln($row_jenis['Bulan']);
				$idTagihan = "".$row_jenis['noTagihan']."_cn_".$row_jenis['totalTagihan']."_cn_".$accFix."_cn_".$bengkel."_cn_".$jnsHutang."_cn_".$bp."_cn_".date('Y-m-d', strtotime($row_jenis['tglTagihan']))."";
				$xml .= "<row id='".$row_jenis['noTagihan']."'>";
				$xml .= "<cell><![CDATA[<input type='checkbox' id='idTagihan-".$no."' onclick='coba(".$no.");' name='idTagihan[]' value='".$idTagihan."'>]]></cell>";
				if ($jnsHutang!='3') {
					$xml .= "<cell><![CDATA[";
					$xml .= "<a href=\"javascript:void(0)\" onclick=\"show5('".$row_jenis['noTagihan']."');\">".utf8_encode($row_jenis['noTagihan'])."</a>";
					$xml .= "]]></cell>";
				} else {
					$xml .= "<cell><![CDATA[".utf8_encode($row_jenis['noTagihan'])."]]></cell>";
				}
				
				$xml .= "<cell><![CDATA[".datenull($row_jenis['tglTagihan'])."]]></cell>";
				$xml .= "<cell><![CDATA[<div style='text-align: right;padding:0'>".number_format($row_jenis['totalTagihan'],0,",",".")."</div>]]></cell>";
				$xml .= "</row>";
				$no++;
			}
		}
		$xml .= "</rows>";
		echo $xml;
	}

	function bln($a){
		if (strlen($a)==1) {
			$data = "0".$a;
		} else {
			$data = $a;
		}
		return $data;
	}

	function cekTagihan(){
		// $host = '10.10.10.181';
		// $user = "nis";
		// $pass = "N@sm0c0W0r!d";
		$host = '10.10.26.200';
		$user = "sa";
		$pass = "nasmoco~123";
		$db  = 'CreditNote';
		$conns = mssql_connect($host,$user,$pass) or die("Connection Failed");
		mssql_select_db($db,$conns);
		
		$sql = "select b.NoTagihan from DataPengajuan a 
			inner join DataPengajuanDtl b on a.NoBuktiPengajuan=b.NoBuktiPengajuan
			where idStat<>'6'";
		$rsl = mssql_query($sql,$conns);
		$count = mssql_num_rows($rsl);
		if ($count>0) {
			$data = "";
			while ($dt = mssql_fetch_array($rsl)) {
				$data .= "'".$dt['NoTagihan']."',";
			}
			$data_ = substr($data, 0,strlen($data)-1);
		} else {
			$data_ = "0";
		}
		return $data_;
	}

	function checkNoTagihan($NoBuktiPengajuan,$noTagihan){
		$host = '10.10.26.200';
		$user = "sa";
		$pass = "nasmoco~123";
		$db  = 'CreditNote';
		$conns = mssql_connect($host,$user,$pass) or die("Connection Failed");
		mssql_select_db($db,$conns);

		$sql = "select * from DataPengajuanDtl where NoBuktiPengajuan='".$NoBuktiPengajuan."' and noTagihan='".$noTagihan."'";
		$rsl = mssql_query($sql,$conns);
		$dt = mssql_num_rows($rsl);
		if ($dt>0) {
			$cek =  "checked";
		} else {
			$cek = "";
		}
		return $cek;
		// return $sql;
	}
?>