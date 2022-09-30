<?php
	require_once ('../inc/conn.php');
	$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : null;
	
	if ($action=='new') {
		$akun_start_hutang = addslashes($_REQUEST['akun_start_hutang']);
		$akun_end_hutang = addslashes($_REQUEST['akun_end_hutang']);
		$akun_start_biaya = addslashes($_REQUEST['akun_start_biaya']);
		$akun_end_biaya = addslashes($_REQUEST['akun_end_biaya']);
		$akun_biaya = addslashes($_REQUEST['akun_biaya']);
		$pety_cash = addslashes($_REQUEST['pety_cash']);
		$skip_direksi = addslashes($_REQUEST['skip_direksi']);
		$skip_direksi2 = addslashes($_REQUEST['skip_direksi2']);
		
		$pph_21 = addslashes($_REQUEST['pph_21']);
		$pph_22 = addslashes($_REQUEST['pph_22']);
		$pph_23 = addslashes($_REQUEST['pph_23']);
		$pph_25 = addslashes($_REQUEST['pph_25']);
		$pph_21_pihak_3 = addslashes($_REQUEST['pph_21_pihak_3']);
		$pph_4 = addslashes($_REQUEST['pph_4']);
		$non_pph_21 = addslashes($_REQUEST['non_pph_21']);
		$non_pph_22 = addslashes($_REQUEST['non_pph_22']);
		$non_pph_23 = addslashes($_REQUEST['non_pph_23']);
		$non_pph_25 = addslashes($_REQUEST['non_pph_25']);
		$non_pph_21_pihak_3 = addslashes($_REQUEST['non_pph_21_pihak_3']);
		$non_pph_4 = addslashes($_REQUEST['non_pph_4']);
		$akun_sublet = addslashes($_REQUEST['akun_sublet']);
		$akun_ppn = addslashes($_REQUEST['akun_ppn']);
		$akun_ppn_aksesoris = addslashes($_REQUEST['akun_ppn_aksesoris']);
		$akun_ppn_bengkel = addslashes($_REQUEST['akun_ppn_bengkel']);
		$akun_ppn_mobil = addslashes($_REQUEST['akun_ppn_mobil']);
		$akun_ppn_part = addslashes($_REQUEST['akun_ppn_part']);
		$akun_ppn_sublet = addslashes($_REQUEST['akun_ppn_sublet']);
		$akun_ppn_sewa = addslashes($_REQUEST['akun_ppn_sewa']);

		mssql_query("BEGIN TRAN");
			$prc1 = mssql_query("
				update settingAkun set 
				akun_start_hutang='$akun_start_hutang',akun_end_hutang='$akun_end_hutang',
				akun_start_biaya='$akun_start_biaya',akun_end_biaya='$akun_end_biaya',
				akun_biaya='$akun_biaya',pety_cash='$pety_cash', skip_direksi='$skip_direksi', skip_direksi2='$skip_direksi2',
				pph_21='$pph_21', pph_22='$pph_22', pph_23='$pph_23',
				pph_25='$pph_25', pph_21_pihak_3='$pph_21_pihak_3', pph_4='$pph_4',
				non_pph_21='$non_pph_21', non_pph_22='$non_pph_22', non_pph_23='$non_pph_23',
				non_pph_25='$non_pph_25', non_pph_21_pihak_3='$non_pph_21_pihak_3', non_pph_4='$non_pph_4',
				akun_sublet='$akun_sublet', akun_ppn='$akun_ppn', akun_ppn_aksesoris='$akun_ppn_aksesoris', 
				akun_ppn_bengkel='$akun_ppn_bengkel',akun_ppn_mobil='$akun_ppn_mobil', 
				akun_ppn_part='$akun_ppn_part', akun_ppn_sublet='$akun_ppn_sublet', akun_ppn_sewa='$akun_ppn_sewa'
				where id=1"
			);

			if ($prc1) {
				mssql_query("COMMIT TRAN");
				echo "Data Saved!!";
			} else {
				mssql_query("ROLLBACK TRAN");
				echo "Failed!!";
			}
		mssql_query("return");
		
	} else if($action=='new-tarif') {
		$jns_pph = addslashes($_REQUEST['jns_pph']);
		$npwp = addslashes($_REQUEST['npwp']);
		$kodelgn = addslashes($_REQUEST['kodelgn']);
		$tarif_persen = addslashes($_REQUEST['tarif_persen']);
		mssql_query("BEGIN TRAN");
			$prc1 = mssql_query("
				insert into settingPph (jns_pph,npwp,tarif_persen,kodelgn) values ('$jns_pph','$npwp','$tarif_persen','$kodelgn')
			");
			if ($prc1) {
				mssql_query("COMMIT TRAN");
				echo "Data Saved!!";
			} else {
				mssql_query("ROLLBACK TRAN");
				echo "Failed!!";
			}
		mssql_query("return");
		
	} else if ($action=='getPph') {
		$is_ppn = isset($_REQUEST['is_ppn']) ? $_REQUEST['is_ppn'] : null;
		//$sql = "select jns_pph,tarif_persen from settingPph where npwp = '".$is_ppn."' order by jns_pph,tarif_persen asc";
		$sql = "select jns_pph,tarif_persen from settingPph order by jns_pph,tarif_persen asc";
		$rsl = mssql_query($sql);
		echo "<option value='Non Pph#0#00000000'>Non Pph</option>";
		while ($dt = mssql_fetch_array($rsl)) {
			if ($is_ppn=='0') {
				if ($dt['jns_pph']=='Pph 4 Ayat 2') {
					$jns = "non_pph_4";
				} else {
					$jns = "non_".str_replace(' ', '_', strtolower($dt['jns_pph']));
				}
			} else if ($is_ppn=='1') {
				if ($dt['jns_pph']=='Pph 4 Ayat 2') {
					$jns = "pph_4";
				} else {
					$jns = str_replace(' ', '_', strtolower($dt['jns_pph']));
				}
			}
			$akun = mssql_fetch_array(mssql_query("select ".$jns." as akun from settingAkun where id=1"));
			echo "<option value='$dt[jns_pph]#$dt[tarif_persen]#$akun[akun]'>$dt[jns_pph] ($dt[tarif_persen]%)</option>";
		}
	
	} else if ($action=='getPphNew') {
		if ($_REQUEST['npwp']!='') {
			$npwp = 1;
		} else {
			$npwp = 0;
		}		
		//$sql = "select jns_pph,tarif_persen from settingPph where npwp = '".$npwp."' order by jns_pph,tarif_persen asc";
		$sql = "select jns_pph,tarif_persen from settingPph order by jns_pph,tarif_persen asc";
		$rsl = mssql_query($sql);
		echo "<option value='Non Pph#0#00000000'>Non Pph</option>";
		while ($dt = mssql_fetch_array($rsl)) {
			if ($npwp=='0') {
				if ($dt['jns_pph']=='Pph 4 Ayat 2') {
					$jns = "non_pph_4";
				} else {
					$jns = "non_".str_replace(' ', '_', strtolower($dt['jns_pph']));
				}
			} else if ($npwp=='1') {
				if ($dt['jns_pph']=='Pph 4 Ayat 2') {
					$jns = "pph_4";
				} else {
					$jns = str_replace(' ', '_', strtolower($dt['jns_pph']));
				}
			}
			//echo "select ".$jns." as akun from settingAkun where id=1";
			$akun = mssql_fetch_array(mssql_query("select ".$jns." as akun from settingAkun where id=1"));
			echo "<option value='$dt[jns_pph]#$dt[tarif_persen]#$akun[akun]'>$dt[jns_pph] ($dt[tarif_persen]%)</option>";
		}
			
	} else if ($action=='delete') {
		$idPph = isset($_REQUEST['idPph']) ? $_REQUEST['idPph'] : null;
		mssql_query("BEGIN TRAN");
			$prc1 = mssql_query("delete from settingPph where idPph = '".$idPph."'");
			if ($prc1) {
				mssql_query("COMMIT TRAN");
				echo "Data Saved!!";
			} else {
				mssql_query("ROLLBACK TRAN");
				echo "Failed!!";
			}
		mssql_query("return");
		
	} else {
		$page = isset($_POST['page']) ? $_POST['page'] : 1;
		$rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
		$sortname = isset($_POST['sortname']) ? $_POST['sortname'] : 'idPph';
		$sortorder = isset($_POST['sortorder']) ? $_POST['sortorder'] : 'asc';
	
		require_once ('../inc/conn.php');

		$page = $_POST['page'];
		$rp = $_POST['rp'];

		if (!$sortname) $sortname = 'idPph';
		if (!$sortorder) $sortorder = 'asc';
		$sort = "ORDER BY $sortname $sortorder";

		if (!$page) $page = 1;
		if (!$rp) $rp = 10;

		$start = (($page-1) * $rp);
		
		$sql = "
			select top $rp idpph,case when npwp=1 then 'NPWP' else 'Non' end as npwp,jns_pph,tarif_persen,
			REPLACE(REPLACE(LOWER(case when npwp=1 then jns_pph else 'non_'+jns_pph end), ' ayat 2', ''), ' ', '_') as kode
			from settingPph where idpph not in (
				select top $start idpph from settingPph
			) order by npwp,jns_pph,tarif_persen
		";
		$result = mssql_query($sql);
		$total = mssql_num_rows(mssql_query("select idpph from settingPph"));
		$rows = array();
		while ($row = mssql_fetch_array($result)) {
			$rows[] = $row;
		}

		header("Content-type: text/xml");
		$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		$xml .= "<rows>";
		$xml .= "<page>$page</page>";
		$xml .= "<total>$total</total>";
		foreach($rows as $row) {
			$dt = mssql_fetch_array(mssql_query("select ".$row['kode']." as kodeakun from settingAkun where id=1"));
			$xml .= "<row id='".$row['idpph']."'>";
			$xml .= "<cell><![CDATA[<input type='checkbox' id='chk-1' name='id[]' value='".$row['idpph']."'>]]></cell>";
			$xml .= "<cell><![CDATA[".utf8_encode($row['npwp'])."]]></cell>";
			$xml .= "<cell><![CDATA[".utf8_encode($row['jns_pph'])."]]></cell>";
			$xml .= "<cell><![CDATA[".utf8_encode($dt['kodeakun'])."]]></cell>";
			$xml .= "<cell><![CDATA[<div style='text-align:right'>".number_format($row['tarif_persen'],1,",",".")."%</div>]]></cell>";
			$xml .= "</row>";
		}
		$xml .= "</rows>";
		echo $xml;
	}
?>