<?php
	error_reporting(0);
	$page = isset($_POST['page']) ? $_POST['page'] : 1;
	$rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
	$sortname = isset($_POST['sortname']) ? $_POST['sortname'] : 'evotf_id';
	$sortorder = isset($_POST['sortorder']) ? $_POST['sortorder'] : 'asc';
	$query = isset($_POST['query']) ? $_POST['query'] : false;
	$qtype = isset($_POST['qtype']) ? $_POST['qtype'] : false;

	require_once ('../inc/conn.php');

	$page = $_POST['page'];
	$rp = $_POST['rp'];
	$sortname = $_POST['sortname'];
	$sortorder = $_POST['sortorder'];

	if (!$sortname) $sortname = 'evotf_id';
	if (!$sortorder) $sortorder = 'asc';
	$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;
	$jns = isset($_REQUEST['jns']) ? $_REQUEST['jns'] : null;
	
	$sort = "ORDER BY $sortname $sortorder";
	
	if (!$page) $page = 1;
	if (!$rp) $rp = 10;

	$start = (($page-1) * $rp);

	header("Content-type: text/xml");
	$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
	$xml .= "<rows>";
	$xml .= "<page>$page</page>";
	if ($jns == 'bizc') {
		$sql = "
			select top $rp evotf_id,a.nobukti,tf_from_account,benificary_account,nama_pemilik,
			case when tipe='HUTANG' then htg_stl_pajak else biaya_yg_dibyar end Amount,
			Keterangan,bayar_via,nama_bank,email_penerima,konfirm_email,jenis_penerima,payment_detail,
			kota_bank_penerima,kode_dukcapil
			from DataEvo a inner join DataEvoTransfer b on a.nobukti=b.nobukti
			where evotf_id not in (
				select top $start evotf_id from DataEvo a 
				inner join DataEvoTransfer b on a.nobukti=b.nobukti 
				where noCsv = '".$id."' and isnull(is_del,'')='' $sort
			) and noCsv = '".$id."' and isnull(is_del,'')='' $sort
		";
		$rsl = mssql_query($sql,$conns);
		$rows = array();
		while ($row = mssql_fetch_array($rsl)) {
			$rows[] = $row;
		}
		
		$totals = mssql_num_rows(mssql_query("select evotf_id from DataEvo a inner join DataEvoTransfer b on a.nobukti=b.nobukti 
			where noCsv = '".$id."'  and isnull(is_del,'')=''",$conns));
		$no=1;
		foreach($rows as $dt) {
			if ($no %2 == 0) { $bg = "background:#eaeaea;"; $inp = "background-color: #eaeaea;"; } else { $bg = ""; $inp = ""; }
			$xml .= "<row id='".$no."'>";
			$xml .= "<cell><![CDATA[<div style='padding:2px;$bg'>";
			$xml .= "<input type='checkbox' id='chk_".$no."' value='".$dt['evotf_id']."' name='id[]' />";
			$xml .= "</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>".nbsp($dt['tf_from_account'])."</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>".nbsp($dt['benificary_account'])."</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>".nbsp($dt['nama_pemilik'])."</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:5px;text-align:right;$bg'>".number_format($dt['Amount'],0,",",".")."</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:0px;$bg'>";
			$xml .= "<input type='text' id='keterangan_".$no."' class='form-control' style='$inp' value='".nbsp($dt['Keterangan'])."'/>";
			$xml .= "</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:1px;$bg'>";
			$xml .= "<select type='text' id='bayar_via_".$no."' class='form-control' style='$inp'>";
			$xml .= "<option value=''>- Pilih -</option>";
			$opt = array('RTGS','CIMB');
			for ($a=0; $a < count($opt); $a++) { 
				$popt = ($opt[$a]==$dt['bayar_via'])?"selected" : ""; 
				$xml .= "<option value='".$opt[$a]."' $popt>".$opt[$a]."</option>";
			}
			$xml .= "</select>";
			$xml .= "</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>".nbsp($dt['nama_bank'])."</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:0px;$bg'>";
			$xml .= "<input type='text' id='email_penerima_".$no."' class='form-control' style='$inp' value='".nbsp($dt['email_penerima'])."'/>";
			$xml .= "</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:1px;$bg'>";
			$xml .= "<select type='text' id='konfirm_email_".$no."' class='form-control' style='$inp'>";
			$xml .= "<option value=''>- Pilih -</option>";
			$opt1 = array('Y'=>'YES','N'=>'NO');
			foreach ($opt1 as $value => $key) {
				$popt1 = ($value==$dt['konfirm_email'])?"selected" : ""; 
				$xml .= "<option value='".$value."' $popt1>".$key."</option>";
			}
			$xml .= "</select>";
			$xml .= "</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:1px;$bg'>";
			$xml .= "<select type='text' id='jenis_penerima_".$no."' class='form-control' style='$inp'>";
			$xml .= "<option value=''>- Pilih -</option>";
			$opt2 = array('1'=>'Company','2'=>'Perorangan','3'=>'Government');
			foreach ($opt2 as $value => $key) {
				$popt2 = ($value==$dt['jenis_penerima'])?"selected" : ""; 
				$xml .= "<option value='".$value."' $popt2>".$key."</option>";
			}
			$xml .= "</select>";
			$xml .= "</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>".nbsp($dt['payment_detail'])."</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:1px;$bg'>";
			$xml .= "<select type='text' id='kota_penerima_".$no."' onchange='dukcapil(".$no.");' class='form-control' style='$inp'>";
			$xml .= "<option value=''>- Pilih -</option>";
			$qry = mssql_query("select DISTINCT nama_propinsi from sys_bank",$conns);
			while($row = mssql_fetch_array($qry)){
				$xml .="<optgroup label='".$row['nama_propinsi']."'>";
				$qry1 = mssql_query("select sandi_kota,nama_kota from sys_bank where nama_propinsi = '".$row['nama_propinsi']."'",$conns);
				while ($row2 = mssql_fetch_array($qry1)) {
					$popt3 = ($row2['nama_kota']."#".$row2['sandi_kota']==$dt['kota_bank_penerima']."#".$dt['kode_dukcapil'])?"selected" : ""; 
					$xml .= "<option value='".$row2['nama_kota']."#".$row2['sandi_kota']."' $popt3>".$row2['nama_kota']."</option>";
				}
				$xml .= "</optgroup>";
			}
			$xml .= "</select>";
			$xml .= "<input type='hidden' id='kota_bank_penerima_".$no."' value='".nbsp($dt['kota_bank_penerima'])."'/>";
			$xml .= "</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>";
			$xml .= "<input type='hidden' id='kode_dukcapil_".$no."' class='form-control' style='$inp' value='".nbsp($dt['kode_dukcapil'])."'/>";
			$xml .= "<span id='kodedukcapil_".$no."'>".nbsp($dt['kode_dukcapil'])."</span>";
			$xml .= "</div>]]></cell>";
			$xml .= "</row>";
			$no++;
		}
		$xml .= "<total>$totals</total>";
		$xml .= "</rows>";
	} else if ($jns == 'mcm') {
		$sql = "
			select top $rp evotf_id,a.nobukti,benificary_account,nama_pemilik,Alamat,'IDR' as matauang, 
			case when tipe='HUTANG' then htg_stl_pajak else biaya_yg_dibyar end nominal,Keterangan,
			layanan_transfer,kode_rtgs_kliring,nama_bank,kota_cbg_buka,konfirm_email,email_penerima,charger_inst
			from DataEvo a
			inner join DataEvoTransfer b on a.nobukti=b.nobukti
			where evotf_id not in (
				select top $start evotf_id from DataEvo a 
				inner join DataEvoTransfer b on a.nobukti=b.nobukti 
				where noCsv = '".$id."' and isnull(is_del,'')='' $sort
			) and noCsv = '".$id."' and isnull(is_del,'')='' $sort
		";
		$rsl = mssql_query($sql,$conns);
		$rows = array();
		while ($row = mssql_fetch_array($rsl)) {
			$rows[] = $row;
		}
		
		$totals = mssql_num_rows(mssql_query("select evotf_id from DataEvo a inner join DataEvoTransfer b on a.nobukti=b.nobukti 
			where noCsv = '".$id."'  and isnull(is_del,'')=''",$conns));
		$no=1;
		foreach($rows as $dt) {
			if ($no %2 == 0) { $bg = "background:#eaeaea;"; $inp = "background-color: #eaeaea;"; } else { $bg = ""; $inp = ""; }
			$xml .= "<row id='".$no."'>";
			$xml .= "<cell><![CDATA[<div style='padding:2px;$bg'>";
			$xml .= "<input type='checkbox' id='chk_".$no."' value='".$dt['evotf_id']."' name='id[]' />";
			$xml .= "</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>".nbsp($dt['benificary_account'])."</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>".nbsp($dt['nama_pemilik'])."</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:0px;$bg'>";
			$xml .= "<input type='text' id='Alamat_".$no."' class='form-control' style='$inp' value='".nbsp($dt['Alamat'])."'/>";
			$xml .= "</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>".nbsp($dt['matauang'])."</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:5px;text-align:right;$bg'>".number_format($dt['nominal'],0,",",".")."</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>".nbsp($dt['Keterangan'])."</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:1px;$bg'>";
			$xml .= "<select type='text' id='layanan_transfer_".$no."' class='form-control' style='$inp' onchange='layanen_tf(".$no.");'>";
			$xml .= "<option value=''>- Pilih -</option>";
			$opt = array('IBU','LBU','RBU','INU');
			for ($a=0; $a < count($opt); $a++) { 
				$popt = ($opt[$a]==$dt['layanan_transfer'])?"selected" : ""; 
				$xml .= "<option value='".$opt[$a]."' $popt>".$opt[$a]."</option>";
			}
			$xml .= "</select>";
			$xml .= "</div>]]></cell>";
			if ($dt['layanan_transfer']=='LBU' or $dt['layanan_transfer']=='RBU') {
				if ($dt['layanan_transfer']=='LBU') { $val = '1'; } else if ($dt['layanan_transfer']=='RBU') { $val = '2'; }
				$xml .= "<cell><![CDATA[<div style='padding:0px;$bg' id='layanan_tf_".$no."'>";
				$xml .= "<div class='input-group'> <input class='form-control readonly' style='$inp' id='kode_rtgs_kliring_".$no."' readonly type='text'  value='".nbsp($dt['kode_rtgs_kliring'])."'> <span class='input-group-btn'> <button type='button' id='rtgs_".$no."' onclick='getsysMcm(".$no.");' value='".$val."' class='btn btn-sm btn-more'> <i class='fa fa-th'></i> </button> </span> </div>";
				$xml .= "</div>]]></cell>";
			} else {
				$xml .= "<cell><![CDATA[<div style='padding:5px;$bg' id='layanan_tf_".$no."'>";
				$xml .= "&nbsp;<input type='hidden' id='kode_rtgs_kliring_".$no."'>";
				$xml .= "</div>]]></cell>";
			}
			$xml .= "<cell><![CDATA[<div style='padding:5px;$bg'>".nbsp($dt['nama_bank'])."</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:0px;$bg'>";
			$xml .= "<input type='text' id='kota_cbg_buka_".$no."' class='form-control' style='$inp' value='".nbsp($dt['kota_cbg_buka'])."'/>";
			$xml .= "</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:1px;$bg'>";
			$xml .= "<select type='text' id='konfirm_email_".$no."' class='form-control' style='$inp'>";
			$xml .= "<option value=''>- Pilih -</option>";
			$opt1 = array('Y'=>'YES','N'=>'NO');
			foreach ($opt1 as $value => $key) {
				$popt1 = ($value==$dt['konfirm_email'])?"selected" : ""; 
				$xml .= "<option value='".$value."' $popt1>".$key."</option>";
			}
			$xml .= "</select>";
			$xml .= "</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:0px;$bg'>";
			$xml .= "<input type='text' id='email_penerima_".$no."' class='form-control' style='$inp' value='".nbsp($dt['email_penerima'])."'/>";
			$xml .= "</div>]]></cell>";
			$xml .= "<cell><![CDATA[<div style='padding:1px;$bg'>";
			$xml .= "<select type='text' id='charger_inst_".$no."' class='form-control' style='$inp'>";
			$xml .= "<option value=''>- Pilih -</option>";
			$opt2 = array('OUR'=>'OUR','BEN'=>'BEN','SHA'=>'SHA');
			foreach ($opt2 as $value => $key) {
				$popt2 = ($value==$dt['charger_inst'])?"selected" : ""; 
				$xml .= "<option value='".$value."' $popt2>".$key."</option>";
			}
			$xml .= "</select>";
			$xml .= "</div>]]></cell>";
			$xml .= "</row>";
			$no++;
		}
		$xml .= "<total>$totals</total>";
		$xml .= "</rows>";
	}
	echo $xml;

	function nbsp($val){
		if ($val==' ' || $val=='') {
			$data = "&nbsp;";
		} else {
			$data = $val;
		}
		return $data;
	}
?>