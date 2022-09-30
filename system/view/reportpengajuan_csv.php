<?php
	require_once ('../inc/conn.php');
	$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;
	$dt = mssql_fetch_array(mssql_query("select metode_transfer from DataEvoTransfer where noCsv='".$id."'"));

	$html = "";
	if ($dt['metode_transfer']=='CIMB Biz C') {
		$sql = "
			select evotf_id,a.nobukti,tf_from_account,benificary_account,nama_pemilik,
			case when tipe='HUTANG' then htg_stl_pajak else biaya_yg_dibyar end Amount,
			Keterangan,bayar_via,nama_bank,email_penerima,konfirm_email,jenis_penerima,payment_detail,
			kota_bank_penerima,kode_dukcapil
			from DataEvo a inner join DataEvoTransfer b on a.nobukti=b.nobukti
			where noCsv = '".$id."' and isnull(is_del,'')=''
		";
		$rsl = mssql_query($sql);
		while ($row = mssql_fetch_array($rsl)) {
			$html .= $row['tf_from_account'].',';
			$html .= $row['benificary_account'].',';
			$html .= $row['nama_pemilik'].',';
			$html .= $row['Amount'].',';
			$html .= $row['Keterangan'].',';
			$html .= $row['bayar_via'].',';
			$html .= $row['nama_bank'].',';
			$html .= $row['email_penerima'].',';
			$html .= $row['konfirm_email'].',';
			$html .= $row['jenis_penerima'].',';
			$html .= $row['payment_detail'].',';
			$html .= $row['kota_bank_penerima'].',';
			$html .= $row['kode_dukcapil'];
			$html .="\r";
		}
	} else if ($dt['metode_transfer']=='Mandiri MCM') {
		$mcm = mssql_fetch_array(mssql_query("
			select 'P' as defaultP,noCsv,ISNULL(tgl_mcm, convert(varchar, getdate(), 112)) as tgl_mcm,tf_from_account,sum(Amount) totnom,
			(select count(evotf_id) from DataEvoTransfer where noCsv=x.noCsv) totBaris from (
				select noCsv,tgl_mcm,tf_from_account,case when tipe='HUTANG' then htg_stl_pajak else biaya_yg_dibyar end Amount
				from DataEvo a inner join DataEvoTransfer b on a.nobukti=b.nobukti
				where noCsv = '".$id."'
			) x GROUP BY noCsv,tf_from_account,tgl_mcm
		"));
		$html .= $mcm['defaultP'].','.$mcm['tgl_mcm'].','.$mcm['totBaris'].','.$mcm['tf_from_account'].','.$mcm['totnom'].',,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,';
		$html .="\r";
		$sql = "
			select evotf_id,a.nobukti,benificary_account,nama_pemilik,Alamat,'IDR' as matauang, 
			case when tipe='HUTANG' then htg_stl_pajak else biaya_yg_dibyar end nominal,Keterangan,
			layanan_transfer,kode_rtgs_kliring,nama_bank,kota_cbg_buka,konfirm_email,email_penerima,charger_inst
			from DataEvo a
			inner join DataEvoTransfer b on a.nobukti=b.nobukti
			where noCsv = '".$id."' and isnull(is_del,'')=''
		";
		$rsl = mssql_query($sql);
		while ($row = mssql_fetch_array($rsl)) {
			$html .= $row['benificary_account'].',';
			$html .= $row['nama_pemilik'].',';
			$html .= $row['Alamat'].',,,';
			$html .= $row['matauang'].',';
			$html .= $row['nominal'].',';
			$html .= $row['Keterangan'].',,';
			$html .= $row['layanan_transfer'].',';
			$html .= $row['kode_rtgs_kliring'].',';
			$html .= $row['nama_bank'].',';
			$html .= $row['kota_cbg_buka'].',,,,';
			$html .= $row['konfirm_email'].',';
			$html .= $row['email_penerima'].',,,,,,,,,,,,,,,,,,,,,';
			$html .= $row['charger_inst'].',';
			$html .= 'E';
			$html .="\r";
		}
	}
	$file= str_replace(" ", "", $dt['metode_transfer'])."_".str_replace("/", "", $id).".csv";
	header("Content-type: text/csv");
	header("Content-Disposition: attachment; filename=".$file."");
	echo $html;
?>