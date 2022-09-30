<?php
	error_reporting(0);
	require_once('../inc/conn.php');
	//require_once('../inc/koneksi.php');
	include 'QR_BarCode.php';
	
	
	$id = $_REQUEST['id'];
	$sys = mssql_fetch_array(mssql_query("select skip_direksi,skip_direksi2 from settingAkun where id=1",$conns));
	$dt = mssql_fetch_array(mssql_query("
		select evo_id,nobukti,a.tglentry,a.kodedealer,kode_form,kode_voucher,tgl_bayar,delIndex,metode_bayar,
		namaVendor,dpp,ppn,nama_bank,benificary_account,nama_pemilik,keterangan,
		(select sum(nilai_pph) from DataEvoPos where nobukti = a.nobukti) as pph,
		case when a.tipe='HUTANG' then htg_stl_pajak else biaya_yg_dibyar end as totBayar,namaUser,nobukti,tglbayar, isnull(deptterkait,'') deptterkait, b.tipe,
		b.department departement_aju
		from DataEvo a 
		inner join sys_user b on a.userentry = b.IdUser
		where nobukti = '".$id."'
	",$conns));
	
	$depterkait = $dt['deptterkait'];
	$tipe_useraju = $dt['tipe'];
	$nobukti = $dt['nobukti'];
	
	if (!empty($depterkait)) {
		$stm_deptterkait = "select a.tglvalidasi, b.namaUser
							from DataEvoVal a 
							inner join sys_user b on a.uservalidasi = b.IdUser
							where a.nobukti = '".$id."' and deptterkait = '".$dt['deptterkait']."' and isnull(deptterkait,'') <> ''
							order by a.idval";
		$qry_deptterkait = mssql_query($stm_deptterkait,$conns);
		$jml_deptterkait = mssql_num_rows($qry_deptterkait);
	} else {
		$jml_deptterkait = 0;
	}
	
	
	$month = array('01' => 'Januari','02' => 'Februari','03' => 'Maret','04' => 'April','05' => 'Mei','06' => 'Juni','07' => 'Juli','08' => 'Agustus','09' => 'September','10' => 'Oktober','11' => 'Nopember','12' => 'Desember');
	$s=explode("-", date("Y-m-d", strtotime($dt['tglbayar'])));
	$tanggal = $s[2]." ".$month[$s[1]]." ".$s[0];
	$tanggal2 = $s[2]." ".$month[$s[1]]." ".$s[0];
	
	// $text = $dt['kode_voucher'].'#'.$dt['delIndex'];
	$filename = "temp/".str_replace("/", "", $dt['nobukti']);
    if (!file_exists($filename)) { 
    	$text = "https://evopay.nasmoco.net/index.php?nobuk=".$dt['nobukti']."";
	    // QR_BarCode object 
		$qr = new QR_BarCode(); 
		// create text QR code 
		$qr->text($text); 
		// display QR code image
		$qr->qrCode('200',$filename);
    }
	$qrcode = '<img src="'.$filename.'.png" width="60px"/>';
	
	
	$html = '
		<table width="100%" cellspacing="0" cellpadding="0" border="0">
			<body>
				<tr>
					<td rowspan="2" colspan="4">
						<img src="../../assets/img/logo-nasmoco.png" width="150" height="40"><br />
						<b>PT. NEW RATNA MOTOR</b>
					</td>
					<td colspan="3" style="font-size:12pt;border-bottom:1px solid">
						<center><b>'.$dt['kode_form'].'&nbsp;</b></center>
					</td>
				</tr>
				<tr>
					<td colspan="3" style="font-size:11pt;border-bottom:1px solid">
						<center><b>BUKTI PENGELUARAN KAS/BANK</b></center>
					</td>
				</tr>
				<tr>
					<td rowspan="2" colspan="4">
						<span style="font-size:9px;">Gedung MG Setos Lantai 6, Jl. Inspeksi, Gajah Mada, Semarang 50133, Indonesia</span><br/>
						<span style="font-size:9px;">Phone +6224 3516972 Fax.+6224 3513339 Email: newratna@nasmoco.co.id Web: www.nasmoco.co.id</span>
					</td>
					<td width="14%" style="padding: 0px;">No Bukti</td>
					<td width="5px">:</td>
					<td width="13%" style="padding: 0px;"><b>'.$dt['kode_voucher'].'</b></td>
				</tr>
				<tr>
					<td style="padding: 0px;">No Voucher</td>
					<td width="5px">:</td>
					<td style="padding: 0px;">'.$dt['nobukti'].'</td>
				</tr>
				<tr>
					<td width="10%">Dibayar kepada</td>
					<td width="5px">:</td>
					<td colspan="2">'.$dt['namaVendor'].'</td>
					<td>Tanggal</td>
					<td>:</td>
					<td>'.$tanggal.'</td>
				</tr>';
				
	if ($dt['pph']==0 && $dt['ppn']==0) {
		$dpp = 0;
	} else {
		$dpp = number_format($dt['dpp'],0,",",".");
	}		
				
	$html .= '	<tr>
					<td>DPP</td>
					<td>:</td>
					<td align="right" width="20%">'.$dpp.'</td>
					<td width="10%"></td>
					<td style="vertical-align:top">Departemen Pengaju</td>
					<td style="vertical-align:top">:</td>
					<td style="vertical-align:top">';
						$html .= $dt['departement_aju'];
					$html.='
					</td>
				</tr>
				<tr>
					<td>PPN</td>
					<td>:</td>
					<td align="right">'.number_format($dt['ppn'],0,",",".").'</td>
					<td></td>
					<td style="vertical-align:top">Pembayaran Melalui</td>
					<td style="vertical-align:top">:</td>
					<td style="vertical-align:top">';
					if ($dt['metode_bayar']=='Transfer') {
						$html .= tfBank($dt['evo_id']);
					} else {
						$html .= 'Kas';
					}
					$html.='
					</td>
				</tr>
				<tr>
					<td>Pph</td>
					<td>:</td>
					<td align="right">'.number_format($dt['pph'],0,",",".").'</td>
					<td></td>';
					if ($dt['metode_bayar']=='Transfer') {
						$html .= '
							<td>Rekening Tujuan</td>
							<td width="5px">:</td>
							<td>'.$dt['nama_bank'].'</td>
						';
					} else {
						$html .= '
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						';
					}
				$html .='
				</tr>
				<tr>
					<td><b>Bayar</b></td>
					<td>:</td>
					<td align="right">'.number_format($dt['totBayar'],0,",",".").'</td>
					<td></td>';
					if ($dt['metode_bayar']=='Transfer') {
						$html .= '
							<td>&nbsp;</td>
							<td width="5px">&nbsp;</td>
							<td>'.$dt['benificary_account'].'</td>
						';
					} else {
						$html .= '
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						';
					}
				$html .='
				</tr>';
				
				// terbilang
				//$qryterbilang = mssql_query("select ProfilACC.dbo.Terbilang(25373202800) as nama");	
				$dataterbilang = mssql_fetch_array(mssql_query("select ProfilACC.dbo.Terbilang(".$dt['totBayar'].") as nama"));
	
			$html .= '
				<tr>
					<td style="vertical-align:top">Terbilang Bayar</td>
					<td style="vertical-align:top">:</td>
					<td colspan="2" style="vertical-align:top"><i>'.$dataterbilang['nama'].'</i></td>
					<td style="vertical-align:top;text-align:left">'.$qrcode.'</td>
					<td width="5px">&nbsp;</td>';
					if ($dt['metode_bayar']=='Transfer') {
						$html .= '<td rowspan="2" style="vertical-align:top">a/n '.$dt['nama_pemilik'].'</td>';
					} else {
						$html .= '
							<td rowspan="2">&nbsp;</td>
						';
					}
			$html .= '</tr>
				<tr>
					<td style="vertical-align:top">Untuk Pembayaran</td>
					<td style="vertical-align:top">:</td>
					<td style="vertical-align:top" colspan="2">'.str_replace(";","; ",$dt['keterangan']).'</td>
					<td style="vertical-align:top" colspan="3">';
			$html .= cekAkun($dt['nobukti']);
			$html .= '
					</td>
				</tr>';
			
			if ($jml_deptterkait>0) {	
				
			}
			
			
			$html .='
				<tr>
					<td style="vertical-align:top" colspan="7">';
						
				if (strtolower($dt['metode_bayar'])=='cash') {					
						
					if ($dt['kodedealer']=='2010') {
						$width = "14.13%";
						$html .='
							<table width="2460px" cellspacing="0" cellpadding="0" border="0">
								<tr>
									<td colspan="3" style="border:1px solid;">
										<center><b>Checked</b></center>
									</td>
									<td colspan="2" style="border:1px solid;">
										<center><b>Release</b></center>
									</td>';
									
						if ($jml_deptterkait>0) {			
							$html .= 	'<td colspan="'.$jml_deptterkait.'" style="border:1px solid;">
											<center><b>Related Dept</b></center>
										</td>';
						} 
							
						$html .= '
									<td style="border:1px solid;">
										<center><b>Diterima</b></center>
									</td>';
						if ($jml_deptterkait>0) {
						
						} else {
							$html .= '<td>&nbsp;</td>
										<td>&nbsp;</td>';
						}			
									
						$html .= '	
								</tr>
								<tr>
									<td width="'.$width.'" style="border:1px solid;color:red;min-height:50px">';
										$html .= cekVal($dt['evo_id'],'TAX','CHECKED',$nobukti);
									$html .='
									</td>
									<td width="'.$width.'" style="border:1px solid;color:red;min-height:50px">';
										$html .= cekVal($dt['evo_id'],'ACCOUNTING','CHECKED',$nobukti);
									$html .='
									</td>
									<td width="'.$width.'" style="border:1px solid;color:red;min-height:50px">';
										$html .= cekVal($dt['evo_id'],'FINANCE','CHECKED',$nobukti);
									$html .='
									</td>
									<td width="'.$width.'" style="border:1px solid;color:red;min-height:50px">';
										$html .= cekVal($dt['evo_id'],'DEPT. HEAD FINANCE','RELEASE',$nobukti);
									$html .='
									</td>
									<td width="'.$width.'" style="border:1px solid;color:red;min-height:50px">';
										$html .= cekVal($dt['evo_id'],'DEPT. HEAD FINANCE / DIV. HEAD FAST','RELEASE',$nobukti);
									$html .='
									</td>';
							if ($jml_deptterkait>0) {
								$i=0;	
								$qry_deptterkait = mssql_query($stm_deptterkait,$conns);	
								while ($dt_deptterkait = mssql_fetch_array($qry_deptterkait)) {		
									if ($i==0) {
										$txt = "CHECKED";
									} else {
										$txt = "APPROVED";
									}
									$data = "<center><b>".$txt." <br> ".date('d/m/Y H.i', strtotime($dt_deptterkait['tglvalidasi']))."</b></center>";
										
									$html .= '<td width="'.$width.'" style="border:1px solid;color:red;min-height:50px">
													'.$data.'
												</td>';
									$i++;
								}
							}
							
							$html .='
									<td width="'.$width.'" style="border:1px solid;color:red;min-height:50px">&nbsp;</td>';
							
							if ($jml_deptterkait>0) {
							
							} else {
								$html .= '
										<td width="'.$width.'" >&nbsp;</td>
										<td width="'.$width.'" >&nbsp;</td>';
							}			
								
							$html .='
								</tr>
								<tr>
									<td style="border:1px solid;text-align:center;font-size:11px;">
										<center>Tax</center>
									</td>
									<td style="border:1px solid;text-align:center;font-size:11px;">
										<center>CoA</center>
									</td>
									<td style="border:1px solid;text-align:center;font-size:11px;">
										<center>Fin - Inv Ctrl</center>
									</td>
									<td style="border:1px solid;text-align:center;font-size:11px;"><center>';
										$html .= ValUser($dt['evo_id'],'DEPT. HEAD FINANCE');
										$html .='</center>
									</td>
									<td style="border:1px solid;text-align:center;font-size:11px;"><center>';
										$html .= ValUser($dt['evo_id'],'DEPT. HEAD FINANCE / DIV. HEAD FAST');
										$html .='</center>
									</td>';
							
							if ($jml_deptterkait>0) {		
								$qry_deptterkait = mssql_query($stm_deptterkait,$conns);
								while ($dt_deptterkait = mssql_fetch_array($qry_deptterkait)) {			
									$html .= '<td style="border:1px solid;text-align:center">
													'.$dt_deptterkait['namaUser'].'
												</td>';
								}
							}
							
							$html .= '
									<td style="border:1px solid;color:red;min-height:50px">&nbsp;</td>';
									
							
							if ($jml_deptterkait>0) {	
						
							} else { 
								$html .= '	
									<td></td>
									<td></td>';
							}
							
							
							$html .= '
								</tr>
							</table>';
							
					} else {
						$width = "14.13%";
						$html .='
							<table width="100%" cellspacing="0" cellpadding="0" border="0">
								<tr>
									<td style="border:1px solid;">
										<center><b>ACCOUNTING</b></center>
									</td>
									<td style="border:1px solid;">
										<center><b>DITERIMA</b></center>
									</td>
									<td width="'.$width.'">&nbsp;</td>
									<td width="'.$width.'">&nbsp;</td>
									<td width="'.$width.'">&nbsp;</td>
									<td width="'.$width.'">&nbsp;</td>
									<td width="'.$width.'">&nbsp;</td>
								</tr>
								<tr>
									<td width="'.$width.'" style="border:1px solid;text-align:center;color:red;height:75px;font-size:11px;">';
										$html .= cekVal($dt['evo_id'],'ACCOUNTING','CHECKED',$nobukti);
									$html .='
									</td>
									<td width="'.$width.'" style="border:1px solid;color:red;height:75px">&nbsp;</td>
									<td width="'.$width.'">&nbsp;</td>
									<td width="'.$width.'">&nbsp;</td>
									<td width="'.$width.'">&nbsp;</td>
									<td width="'.$width.'">&nbsp;</td>
									<td width="'.$width.'">&nbsp;</td>
								</tr>
							</table>
						';
					}
					
				} else {
				
					if ($dt['kodedealer']=='2010') {
								$width = "14.13%";
								$html .='
									<table width="2460px" cellspacing="0" cellpadding="0" border="0">
										<tr>
											<td colspan="3" style="border:1px solid;">
												<center><b>Checked</b></center>
											</td>
											<td colspan="2" style="border:1px solid;">
												<center><b>Release</b></center>
											</td>';
								if ($jml_deptterkait>0) {	
									$html .= '<td colspan="'.$jml_deptterkait.'" style="border:1px solid;">
												<center><b>Related Dept</b></center>
											</td>';
								}	else {
									$html .= '	
											<td></td>
											<td></td>';
								
								}
									
								$html .= '	
											<td></td>
										</tr>
										<tr>
											<td width="'.$width.'" style="border:1px solid;color:red;">';
												$html .= cekVal($dt['evo_id'],'TAX','CHECKED',$nobukti);
											$html .='
											</td>
											<td width="'.$width.'" style="border:1px solid;color:red;">';
												$html .= cekVal($dt['evo_id'],'ACCOUNTING','CHECKED',$nobukti);
											$html .='
											</td>
											<td width="'.$width.'" style="border:1px solid;color:red;">';
												$html .= cekVal($dt['evo_id'],'FINANCE','CHECKED',$nobukti);
											$html .='
											</td>
											<td width="'.$width.'" style="border:1px solid;color:red;">';
												$html .= cekVal($dt['evo_id'],'DEPT. HEAD FINANCE','RELEASE',$nobukti);
											$html .='
											</td>
											<td width="'.$width.'" style="border:1px solid;color:red;">';
												$html .= cekVal($dt['evo_id'],'DEPT. HEAD FINANCE / DIV. HEAD FAST','RELEASE',$nobukti);
											$html .='
											</td>';
									
								if ($jml_deptterkait>0) {	
									$i=0;
									$qry_deptterkait = mssql_query($stm_deptterkait,$conns);		
									while ($dt_deptterkait = mssql_fetch_array($qry_deptterkait)) {		
										if ($i==0) {
											$txt = "CHECKED";
										} else {
											$txt = "APPROVED";
										}
										$data = "<center><b>".$txt." <br> ".date('d/m/Y H.i', strtotime($dt_deptterkait['tglvalidasi']))."</b></center>";
											
										$html .= '<td width="'.$width.'" style="border:1px solid;color:red;">
														'.$data.'
													</td>';
										$i++;
									}
								} else {
									$html .= '	
											<td width="'.$width.'"></td>
											<td width="'.$width.'"></td>';
								
								}
										
								
									
								$html .= '
											<td width="'.$width.'"></td>
										</tr>
										<tr>
											<td style="border:1px solid;text-align:center;font-size:11px;">
												<center>Tax</center>
											</td>
											<td style="border:1px solid;text-align:center;font-size:11px;">
												<center>CoA</center>
											</td>
											<td style="border:1px solid;text-align:center;font-size:11px;">
												<center>Fin - Inv Ctrl</center>
											</td>	
											<td  style="border:1px solid;text-align:center;font-size:11px;">';
												$html .= ValUser($dt['evo_id'],'DEPT. HEAD FINANCE');
												$html .='
											</td>
											<td  style="border:1px solid;text-align:center;font-size:11px;">';
												$html .= ValUser($dt['evo_id'],'DEPT. HEAD FINANCE / DIV. HEAD FAST');
												$html .='
											</td>';
								
								if ($jml_deptterkait>0) {	
									$qry_deptterkait = mssql_query($stm_deptterkait,$conns);	
									while ($dt_deptterkait = mssql_fetch_array($qry_deptterkait)) {		
										$html .= '<td style="border:1px solid;text-align:center;font-size:11px;"><center>
														'.$dt_deptterkait['namaUser'].'
													</center></td>';
									}
								} else {
									$html .= '	
											<td width="'.$width.'"></td>
											<td width="'.$width.'"></td>';
								
								}								
								
								$html .= '
											<td width="'.$width.'"></td>
										</tr>
									</table>';
									
							} else {
								$width = "14.13%";
								$html .='
									<table cellspacing="0" cellpadding="0" border="0" width="100%">
										<tr>
											<td style="border:1px solid;">
												<center><b>ACCOUNTING</b></center>
											</td>
											<td width="'.$width.'">&nbsp;</td>
											<td width="'.$width.'">&nbsp;</td>
											<td width="'.$width.'">&nbsp;</td>
											<td width="'.$width.'">&nbsp;</td>
											<td width="'.$width.'">&nbsp;</td>
											<td width="'.$width.'">&nbsp;</td>
										</tr>
										<tr>
											<td width="'.$width.'" style="border:1px solid;text-align:center;color:red;height:75px;font-size:11px;">';
												$html .= cekVal($dt['evo_id'],'ACCOUNTING','CHECKED',$nobukti);
											$html .='
											</td>
											<td width="'.$width.'">&nbsp;</td>
											<td width="'.$width.'">&nbsp;</td>
											<td width="'.$width.'">&nbsp;</td>
											<td width="'.$width.'">&nbsp;</td>
											<td width="'.$width.'">&nbsp;</td>
											<td width="'.$width.'">&nbsp;</td>
										</tr>
									</table>
								';
							}
					
				}
				
			$html.='
						</td>
					</tr>
					<tr>
						<td style="vertical-align:top" colspan="7">';
				
			if ($dt['kodedealer']=='2010') {
				$html .='
					<table width="2460px" cellspacing="0" cellpadding="0" border="0">
						<tr>';
					
						if ($dt['totBayar']>$sys['skip_direksi2']) {
				$html .='		<td colspan="2" style="border:1px solid;">
									<center><b>Disetujui</b></center>
								</td>';	
					
						} else if ($dt['totBayar']>$sys['skip_direksi'] and $dt['totBayar']<=$sys['skip_direksi2']) {
				$html .='		<td style="border:1px solid;">
									<center><b>Disetujui</b></center>
								</td>';	
						}
						
				$html .='		
							<td colspan="3" style="border:1px solid;">
								<center><b>Diketahui</b></center>
							</td>
							<td style="border:1px solid;">
								<center><b>PIC</b></center>
							</td>
							<td style="border:1px solid;">
								<center><b>Dibayar</b></center>
							</td>';
							
						if ($dt['totBayar']>$sys['skip_direksi2']) {
					
						} else if ($dt['totBayar']>$sys['skip_direksi'] and $dt['totBayar']<=$sys['skip_direksi2']) {
				$html .='	<td>&nbsp;</td>';
						} else {
				$html .='	<td colspan="2">&nbsp;</td>';			
						}
							
				$html .= '
						</tr>
						<tr>';
								/*$html .='
								<td width="150px" style="border:1px solid;color:red;padding:5px">';
									$html .= cekVal($dt['evo_id'],'DEPT. HEAD FINANCE / DIV. HEAD FAST','RELEASE');
								$html .='
								</td>';*/
							if ($dt['totBayar']>$sys['skip_direksi2']) {
							$html .='
								<td style="border:1px solid;color:red">';
									$html .= cekVal($dt['evo_id'],'DIREKSI 2','APPROVED',$nobukti);
								$html .='
								</td>';
							$html .='
								<td style="border:1px solid;color:red">';
									$html .= cekVal($dt['evo_id'],'DIREKSI','APPROVED',$nobukti);
								$html .='
								</td>';		
						
							} else if ($dt['totBayar']>$sys['skip_direksi'] and $dt['totBayar']<=$sys['skip_direksi2']) {
							$html .='
								<td style="border:1px solid;color:red">';
									$html .= cekVal($dt['evo_id'],'DIREKSI','APPROVED',$nobukti);
								$html .='
								</td>';
							
							} else {
								/*$html .='
								<td colspan="2" width="120px" style="border:1px solid;color:red;padding:5px">';
									$html .= cekVal($dt['evo_id'],'DEPT. HEAD FINANCE / DIV. HEAD FAST','RELEASE',$nobukti);
								$html .='
								</td>';*/
							}
							if ($tipe_useraju=='DIV. HEAD') {
								$html .='
									<td style="border:1px solid;color:red">';
										$html .= '<center><b>APPROVED <br> '.date('d/m/Y H.i', strtotime($dt['tglentry'])).'</b></center>';
							} else {
								$html .='
									<td style="border:1px solid;color:red">';
										$html .= cekVal($dt['evo_id'],'DIV. HEAD','APPROVED',$nobukti);
							} 
															
							if ($tipe_useraju=='DEPT. HEAD') {
								$html .='
									<td style="border:1px solid;color:red">';
										$html .= '<center><b>APPROVED <br> '.date('d/m/Y H.i', strtotime($dt['tglentry'])).'</b></center>';
							} else {
								$html .='
									</td>
									<td style="border:1px solid;color:red">';
										$html .= cekVal($dt['evo_id'],'DEPT. HEAD','APPROVED',$nobukti);
							}
							
							if ($tipe_useraju=='SECTION HEAD') {
								$html .='
									<td style="border:1px solid;color:red">';
										$html .= '<center><b>APPROVED <br> '.date('d/m/Y H.i', strtotime($dt['tglentry'])).'</b></center>';
							} else {
								$html .='
									</td>
									<td style="border:1px solid;color:red">';
										$html .= cekVal($dt['evo_id'],'SECTION HEAD','CHECKED',$nobukti);
							}
							$html .='
							</td>
							<td style="border:1px solid;color:red">
								<center><b>PREPARE <br> '.date('d/m/Y H.i', strtotime($dt['tglentry'])).'</b></center>
							</td>
							<td style="border:1px solid;color:red">';
								$html .= cekVal($dt['evo_id'],'KASIR','PAID',$nobukti);
							$html .='
							</td>';
						
						if ($dt['totBayar']>$sys['skip_direksi2']) {
					
						} else if ($dt['totBayar']>$sys['skip_direksi'] and $dt['totBayar']<=$sys['skip_direksi2']) {
				$html .='	<td>&nbsp;</td>';
						} else {
				$html .='	<td colspan="2">&nbsp;</td>';			
						}
							
				$html .= '
						</tr>
						<tr>';
							$width = "14.13%";
							
							if ($dt['totBayar']>$sys['skip_direksi2']) {
								$html .='
								<td width="'.$width.'" style="border:1px solid;text-align:center;font-size:11px;">';
									$html .= ValUser($dt['evo_id'],'DIREKSI 2');
								$html .='
								</td>';
								$html .='
								<td width="'.$width.'" style="border:1px solid;text-align:center;font-size:11px;">';
									$html .= ValUser($dt['evo_id'],'DIREKSI');
								$html .='
								</td>';
						
							} else if ($dt['totBayar']>$sys['skip_direksi'] and $dt['totBayar']<=$sys['skip_direksi2']) {
								//$width = "16.6%";
								$html .='
								<td width="'.$width.'" style="border:1px solid;text-align:center;font-size:11px;">';
									$html .= ValUser($dt['evo_id'],'DIREKSI');
								$html .='
								</td>';
							} else {
								//$width = "20%";
								/*$html .='
								<td colspan="2" style="border:1px solid;text-align:center">';
									$html .= ValUser($dt['evo_id'],'DEPT. HEAD FINANCE / DIV. HEAD FAST');
								$html .='
								</td>';*/
							}
							if ($tipe_useraju=='DIV. HEAD') {
								$html .='
									<td width="'.$width.'" style="border:1px solid;text-align:center;font-size:11px;">';
								$html .= $dt['namaUser'];
							} else {
								$html .='
								<td width="'.$width.'" style="border:1px solid;text-align:center;font-size:11px;">';
									$html .= ValUser($dt['evo_id'],'DIV. HEAD');
							}
							if ($tipe_useraju=='DEPT. HEAD') {
								$html .='
									<td width="'.$width.'" style="border:1px solid;text-align:center;font-size:11px;">';
								$html .= $dt['namaUser'];
							} else {$html .='
								</td>
								<td width="'.$width.'" style="border:1px solid;text-align:center;font-size:11px;">';
									$html .= ValUser($dt['evo_id'],'DEPT. HEAD');
							}								
							if ($tipe_useraju=='SECTION HEAD') {
								$html .='
									<td width="'.$width.'" style="border:1px solid;text-align:center;font-size:11px;">';
								$html .= $dt['namaUser'];
							} else {
								$html .='
								</td>
								<td width="'.$width.'" style="border:1px solid;text-align:center;font-size:11px;">';
									$html .= ValUser($dt['evo_id'],'SECTION HEAD');
							}
							
							$html .='
							</td>
							<td width="'.$width.'" style="border:1px solid;text-align:center;font-size:11px;">'.$dt['namaUser'].'</td>
							<td width="'.$width.'" style="border:1px solid;text-align:center;font-size:11px;">';
								$html .= ValUser($dt['evo_id'],'KASIR');
							$html .='
							</td>';
							
						if ($dt['totBayar']>$sys['skip_direksi2']) {
					
						} else if ($dt['totBayar']>$sys['skip_direksi'] and $dt['totBayar']<=$sys['skip_direksi2']) {
				$html .='	<td width="'.$width.'">&nbsp;</td>';
						} else {
				$html .='	<td width="'.$width.'">&nbsp;</td>
							<td width="'.$width.'">&nbsp;</td>';
						}
							
				$html .= '
						</tr>
						<tr>';
							if ($dt['totBayar']>$sys['skip_direksi2']) {
								$html .= '<td style="border:1px solid;text-align:center">Direksi</td>';
								$html .= '<td style="border:1px solid;text-align:center">Direksi</td>';
							} else if ($dt['totBayar']>$sys['skip_direksi'] and $dt['totBayar']<=$sys['skip_direksi2']) {
								//$html .='<td style="border:1px solid;text-align:center">Div.Head Fast / Dept.Head Fin</td>';
								$html .= '<td style="border:1px solid;text-align:center">Direksi</td>';
							} else {
								//$html .='<td colspan="2" style="border:1px solid;text-align:center">Div.Head Fast / Dept.Head Fin</td>';
							}
							/*
							$html .='
							<td style="border:1px solid;text-align:center">Div. Head</td>
							<td style="border:1px solid;text-align:center">Dept. Head</td>
							<td style="border:1px solid;text-align:center">Sect. Head</td>';
							*/
							
							$html .='
							<td style="border:1px solid;text-align:center">';
							if ($tipe_useraju=='DIV. HEAD') {
								$html .= 'Div. Head';
							} else {
								$html .= ValJabatanUser($dt['evo_id'],'Div. Head');
							}
							$html .='</td>
								<td style="border:1px solid;text-align:center">';
							if ($tipe_useraju=='DEPT. HEAD') {
								$html .= 'Dept. Head';
							} else {	
								$html .= ValJabatanUser($dt['evo_id'],'Dept. Head');
							}
							$html .='</td>							
							<td style="border:1px solid;text-align:center">';
							if ($tipe_useraju=='SECTION HEAD') {
								$html .= 'Section Head';
							} else {
								$html .= ValJabatanUser($dt['evo_id'],'Section Head');
							}
							$html .='</td>
							<td style="border:1px solid;text-align:center">Pengaju</td>
							<td style="border:1px solid;text-align:center">Fin - Treasury</td>';
							
						if ($dt['totBayar']>$sys['skip_direksi2']) {
					
						} else if ($dt['totBayar']>$sys['skip_direksi'] and $dt['totBayar']<=$sys['skip_direksi2']) {
				$html .='	<td>&nbsp;</td>';
						} else {
				$html .='	<td colspan="2">&nbsp;</td>';			
						}
							
				$html .= '
						</tr>
					</table>
				';
			} else {
				$width = "14.13%";
				$html .='
					<table width="2460px" cellspacing="0" cellpadding="0" border="0">
						<tr>
							<td style="border:1px solid;">
								<center><b>Disetujui</b></center>
							</td>
							<td style="border:1px solid;" colspan="2">
								<center><b>Diketahui</b></center>
							</td>
							<td style="border:1px solid;">
								<center><b>PIC</b></center>
							</td>
							<td style="border:1px solid;">
								<center><b>Dibayar</b></center>
							</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td width="'.$width.'" style="border:1px solid;color:red">';
								$html .= cekVal($dt['evo_id'],'KEPALA CABANG','RELEASE',$nobukti);
							$html .='
							</td>
							<td width="'.$width.'" style="border:1px solid;color:red">';
								$html .= cekVal($dt['evo_id'],'ADH','CHECKED',$nobukti);
							$html .='
							</td>
							<td width="'.$width.'" style="border:1px solid;color:red">';
								$html .= cekVal($dt['evo_id'],'SECTION HEAD','CHECKED',$nobukti);
							$html .='
							</td>
							<td width="'.$width.'" style="border:1px solid;color:red">
								<center><b>PREPARE <br> '.date('d/m/Y H.i', strtotime($dt['tglentry'])).'</b></center>
							</td>
							<td width="'.$width.'" style="border:1px solid;color:red">';
								$html .= cekVal($dt['evo_id'],'KASIR','PAID',$nobukti);
							$html .='
							</td>
							<td width="'.$width.'">&nbsp;</td>
						</tr>
						<tr>
							<td style="border:1px solid;text-align:center;font-size:11px;">';
								$html .= ValUser($dt['evo_id'],'KEPALA CABANG');
							$html .='
							</td>
							<td style="border:1px solid;text-align:center;font-size:11px;">';
								$html .= ValUser($dt['evo_id'],'ADH');
							$html .='
							</td>
							<td style="border:1px solid;text-align:center;font-size:11px;">';
								$html .= ValUser($dt['evo_id'],'SECTION HEAD');
							$html .='
							</td>
							<td style="border:1px solid;text-align:center;font-size:11px;">'.$dt['namaUser'].'</td>
							<td style="border:1px solid;text-align:center;font-size:11px;">';
								$html .= ValUser($dt['evo_id'],'KASIR');
							$html .='
							</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td style="border:1px solid;text-align:center">Dept. Head Fin</td>
							<td style="border:1px solid;text-align:center">ADH</td>
							<td style="border:1px solid;text-align:center">Section Head</td>
							<td style="border:1px solid;text-align:center">User</td>
							<td style="border:1px solid;text-align:center">Fin/Kasir</td>
							<td>&nbsp;</td>
						</tr>
					</table>
				';
			}
							
			$html .= '		
						</td>
					</tr>	
			</tbody>
		</table>';
	
	
	//==============================================================
	//==============================================================
	//==============================================================

	function cekVal($id,$level,$txt,$nobukti){	
		include('../inc/conn.php');
		global $conns;
			
		if($level!='KASIR') {
			$sql = "select a.uservalidasi, a.validasi
					from DataEvoVal a 
					where nobukti in (select nobukti from DataEvo where evo_id = '".$id."') and level = '".$level."' and isnull(deptterkait,'') = '' ";
			
			$dt = mssql_fetch_array(mssql_query($sql,$conns));
			$uservalidasi = $dt['uservalidasi'];
			
			
			if ($uservalidasi!='-' and $uservalidasi!='') {
				
				if ($level=="ACCOUNTING") {
					
					//$stm = mssql_query("select level from DataEvoval where nobukti = '".$nobukti."' and level = 'KASIR'",$conns);
					//$cek = mssql_num_rows($stm);
					$sql = mssql_query("select a.nobukti, a.validasi, a.tglvalidasi, b.idstatus, a.uservalidasi, b.tipe 
							from DataEvoVal a
							left join sys_user b on a.uservalidasi = b.IdUser
							where a.nobukti in (select nobukti from DataEvo 
								where evo_id = '".$id."') and a.level = '".$level."'   and isnull(a.deptterkait,'') = '' ",$conns);
					$dt = mssql_fetch_array($sql);
					$cek = mssql_num_rows($sql);					
					if ($cek>=1) {
						//$dt = mssql_fetch_array(mssql_query("select kode_form,kode_voucher,tglbayar,kodedealer from DataEvo where evo_id = '".$id."'",$conns));
						$data = "<center><b>".$txt." <br>".date('d/m/Y', strtotime($dt['tglvalidasi']))."</b></center>";
					} else {
						$data = "<center><b>#######</b></center>";
					}
					
				} else {	
					$sql = "select a.nobukti, a.validasi, a.tglvalidasi, b.idstatus, a.uservalidasi, b.tipe 
							from DataEvoVal a
							left join sys_user b on a.uservalidasi = b.IdUser
							where a.nobukti in (select nobukti from DataEvo where evo_id = '".$id."') and a.level = '".$level."'   and isnull(a.deptterkait,'') = '' ";
					$dt = mssql_fetch_array(mssql_query($sql,$conns));
					
					if ($dt['validasi']=='Accept') { 
						$tipe = $dt['tipe'];	
						$uservalidasi = trim($dt['uservalidasi']);	
						$tglvalidasi = trim($dt['tglvalidasi']);
						/*
						1	Aktif
						2	Non Aktif
						3	ByPass
						4	Concurrent
						*/
						if ($tglvalidasi!=NULL) {
							if ($level=="ACCOUNTING") {
								$data = "<center><b>".$txt." <br>".date('d/m/Y', strtotime($dt['tglvalidasi']))."</b></center>";
							 } else {
							 	$data = "<center><b>".$txt." <br>".date('d/m/Y H.i', strtotime($dt['tglvalidasi']))."</b></center>";
							 }
						} else {
							$data = "<center><b>".$txt."<br>&nbsp;&nbsp;</b></center>";
						}
						/*if ($tipe==$level) {
							$data = "<center><b>".$txt." <br>".date('d/m/Y H.i', strtotime($dt['tglvalidasi']))."</b></center>";
							
						} else {
							if ($uservalidasi=='-') {
								$data = "<center><b>#######</b></center>";
							} else {
								$data = "<center><b>".$txt." <br>".date('d/m/Y H.i', strtotime($dt['tglvalidasi']))."</b></center>";
							}
						}*/
						
					}else {	
						$data = "<center><b>#######</b></center>";
					}
					
				}
				
			} else {	
				
				if ($level=="ACCOUNTING") {
					$stm = mssql_query("select level from DataEvoval where nobukti = '".$nobukti."' and level = 'KASIR'",$conns);
					$cek = mssql_num_rows($stm);
					
					if ($cek>=1) {
						$dt = mssql_fetch_array(mssql_query("select kode_form,kode_voucher,tglbayar,kodedealer from DataEvo where evo_id = '".$id."'",$conns));
						$data = "<center><b>".$txt." <br>".date('d/m/Y', strtotime($dt['tglbayar']))."</b></center>";
					} else {
						$data = "<center><b>#######</b></center>";
					}
				} else {
					$data = "<center><b>#######</b></center>";
				}
			}
			
			
		} else if($level=='KASIR') {
			$dt = mssql_fetch_array(mssql_query("select kode_form,kode_voucher,tglbayar,kodedealer from DataEvo where evo_id = '".$id."'",$conns));
			if ($dt['kode_voucher']!=' ') {
				$KodeDealer = $dt['kodedealer'];
				include '../inc/koneksi.php';
				$pesan = "";
				if ($msg=='0') {
					$pesan = false; // "Gagal Koneksi Cabang!";
				} else if ($msg=='1') {
					$pesan = false; // "Gagal Koneksi HO!";
				} else if (!mssql_select_db("[$table]",$connCab)) {
					$pesan = false; // "Database tidak tersedia!";
				} else if ($msg=='3') {
					$sql = "select TglEntry from [$table]..cltrn where nobukti = '".$dt['kode_voucher']."'";
					$cl = mssql_fetch_array(mssql_query($sql,$connCab));
					$data = "<center><b>".$txt." <br>".date('d/m/Y', strtotime($dt['tglbayar']))."</b></center>";
				}
			}
		
		} 
		
		//include '../inc/conn.php';
		return $data;
	}

	function ValUser($id,$level){
		include('../inc/conn.php');
		global $conns;
		
		if ($level!='KASIR') {
			
			$sql = "select a.uservalidasi
					from DataEvoVal a 
					where nobukti in (select nobukti from DataEvo where evo_id = '".$id."') and level = '".$level."' and isnull(deptterkait,'') = '' ";
			
			$dt = mssql_fetch_array(mssql_query($sql,$conns));
			$nama = $dt['uservalidasi'];
			
			if ($nama!='########') {
				$sql = "select b.namaUser, b.idstatus, a.uservalidasi, b.tipe 
						from DataEvoVal a 
						inner join sys_user b on a.uservalidasi=b.IdUser 
						where nobukti in (select nobukti from DataEvo where evo_id = '".$id."') and level = '".$level."' and isnull(deptterkait,'') = '' ";
				$dt = mssql_fetch_array(mssql_query($sql,$conns));
				$nama = $dt['namaUser'];
				$idstatus = $dt['idstatus'];
				$tipe = $dt['tipe'];	
				$uservalidasi = trim($dt['uservalidasi']);	
			}
			
		} else {
			//$sql = "select b.namaUser from DataEvo a
			//inner join sys_user b on a.kodedealer=b.kodedealer and b.tipe='KASIR'
			//where evo_id = '".$id."'";
			$sql = "select userbayar as namaUser, '' idstatus, '' uservalidasi, '' tipe from DataEvo where evo_id = '".$id."'";
			$dt = mssql_fetch_array(mssql_query($sql,$conns));
			$nama = $dt['namaUser'];
			$idstatus = $dt['idstatus'];
			$tipe = $dt['tipe'];	
			$uservalidasi = trim($dt['uservalidasi']);	
		}
		
		/*
			1	Aktif
			2	Non Aktif
			3	ByPass
			4	Concurrent
			*/		
		if ($tipe==$level) {
			if (empty($nama)) {
				$nama = "########";
			}			
		}		
		return $nama;
	}
	
	function ValJabatanUser($id,$level) {
		
		include('../inc/conn.php');
		global $conns;
		
		if ($level!='KASIR') {
			
			$sql = "select a.uservalidasi
					from DataEvoVal a 
					where nobukti in (select nobukti from DataEvo where evo_id = '".$id."') and level = '".$level."' and isnull(deptterkait,'') = '' ";
			
			$dt = mssql_fetch_array(mssql_query($sql,$conns));
			$nama = $dt['uservalidasi'];
			
			if ($nama!='########') {
				$sql = "select b.namaUser, b.idstatus, a.uservalidasi, UPPER(LEFT(b.tipe,1))+LOWER(SUBSTRING(b.tipe,2,LEN(b.tipe))) tipe, a.ketvalidasi 
						from DataEvoVal a 
						inner join sys_user b on a.uservalidasi=b.IdUser 
						where nobukti in (select nobukti from DataEvo where evo_id = '".$id."') and level = '".$level."' and isnull(deptterkait,'') = '' ";
				$dt = mssql_fetch_array(mssql_query($sql,$conns));
				$nama = $dt['namaUser'];
				$idstatus = $dt['idstatus'];
				$uservalidasi = trim($dt['uservalidasi']);	
				$note = $dt['ketvalidasi'];
				
				if ($note=='Concurrent') {
					$tipe = $level;
				} else {					
					$tipe = $dt['tipe'];	
				}
			} else {
				$tipe = "########";
			}
		} else {
			//$sql = "select b.namaUser from DataEvo a
			//inner join sys_user b on a.kodedealer=b.kodedealer and b.tipe='KASIR'
			//where evo_id = '".$id."'";
			$sql = "select userbayar as namaUser, '' idstatus, '' uservalidasi, '' tipe from DataEvo where evo_id = '".$id."'";
			$dt = mssql_fetch_array(mssql_query($sql,$conns));
			$nama = $dt['namaUser'];
			$idstatus = $dt['idstatus'];
			$tipe = $dt['tipe'];	
			$uservalidasi = trim($dt['uservalidasi']);	
		}
		
		/*
			1	Aktif
			2	Non Aktif
			3	ByPass
			4	Concurrent
			*/		
		if ($tipe==$level) {
			if (empty($tipe)) {
				$tipe = "#######";
			}			
		}
		
		return $tipe;
	}

	function cekAkun($nobukti){
		include '../inc/conn.php';
		global $conns;
		
		$dtAcc = mssql_fetch_array(mssql_query("
			select *,('ACC'+RIGHT(dbname,2)+'-'+CONVERT(varchar,YEAR(tglbayar))+RIGHT('0' + RTRIM(MONTH(tglbayar)), 2)) as fiskal
			from DataEvo a
			left join spk00..DoDealer b on a.kodedealer = b.KodeDealer where nobukti = '".$nobukti."'
		",$conns));
		$table2 = $dtAcc['fiskal'];
		$KodeDealer = $dtAcc['kodedealer'];
		include '../inc/koneksi.php';
		//echo $msg;
		
		$pesan = "";
		if ($msg=='0') {
			$pesan = false; // "Gagal Koneksi Cabang!";
		} else if ($msg=='1') {
			$pesan = false; // "Gagal Koneksi HO!";
		} else if (!mssql_select_db("[$table]",$connCab)) {
			$pesan = false; // "Database tidak tersedia!";
		} else if ($msg=='3') {
			$pesan = '
				<table width="100%" cellspacing="0" cellpadding="0" border="0">
					<tr>
						<td width="135px" style="border:1px solid;text-align:center">Kode Akun (Db/Cr)</td>
						<td width="135px" style="border:1px solid;text-align:center">Jumlah</td>
					</tr>';
					if ($dtAcc['tipe']=='HUTANG') {
						include '../inc/koneksi.php';
		
						$sql = "
							select KodeGl,sum(JlhDebit) as jml from [$table2]..gltrn 
							where nobukti = '".$dtAcc['kode_voucher']."' and JlhKredit=0
							GROUP BY KodeGl
						";
						$rsl = mssql_query($sql,$connCab);
					} else if ($dtAcc['tipe']=='BIAYA') {
						include '../inc/conn.php';
						$sql = "
							select top 5 pos_biaya as KodeGl,sum(nominal) as jml from DataEvoPos 
							where nobukti = '".$nobukti."'
							GROUP BY pos_biaya
						";
						$rsl = mssql_query($sql,$conns);
					}
					//echo $sql;
					$count = mssql_num_rows($rsl);
					while ($dts = mssql_fetch_array($rsl)) {
						$kodegl = substr($dts['KodeGl'],0,4).".".substr($dts['KodeGl'],4);
						$pesan .='
							<tr>
								<td style="border:1px solid">'.$kodegl.'</td>
								<td style="border:1px solid;text-align:right">'.number_format($dts['jml'],0,",",".").'</td>
							</tr>
						';
					}
					for ($i=1; $i <= (5-$count); $i++) { 
						$pesan .='
							<tr>
								<td style="border:1px solid">&nbsp;</td>
								<td style="border:1px solid">&nbsp;</td>
							</tr>
						';
					}
			$pesan .='
			</table>';
		}
		//include '../inc/conn.php';
		return $pesan;
	}

	function tfBank($id){
		global $conns;
									
		$dt = mssql_fetch_array(mssql_query("select kode_form,kode_voucher, convert(varchar(10),tglbayar,105) tglbayar,kodedealer 
									from DataEvo where evo_id = '".$id."'",$conns));
		$KodeDealer = $dt['kodedealer'];
		include '../inc/koneksi.php';
		
		//echo $kodecabang;
		$tglbayar_arr = explode("-",$dt['tglbayar']);
		$periode_bayar = $tglbayar_arr[2].$tglbayar_arr[1];
		$table_bayar = "ACC".$kodecabang."-".$periode_bayar;
		
		$pesan = "";
		//echo "[$table]";
		//echo $msg;
		
		if ($msg=='0') {
			$pesan = false; // "Gagal Koneksi Cabang!";
		} else if ($msg=='1') {
			$pesan = false; // "Gagal Koneksi HO!";
		/*} else if (!mssql_select_db("[$table]",$connCab)) {
			echo "kosong";
			$pesan = false; // "Database tidak tersedia!";
		*/
		} else if ($msg=='3') {
			$sql = "
				select NamaBank from [$table_bayar]..clmst a
				inner join [$table_bayar]..cltrn b on a.KodeBank=b.KodeBank
				where b.nobukti = '".$dt['kode_voucher']."'";
			$cl = mssql_fetch_array(mssql_query($sql,$connCab));
			$data = $cl['NamaBank'];
		}
		//include '../inc/conn.php';
		return $data;
	}

	include("../../assets/pdf/mpdf.php");

	// $mpdf=new mPDF('c','CF','','',0,0,0,0,0,0);  tanggal 10 Juli 2017 -> kertas beda ukuran
	
	$mpdf=new mPDF('c','A4','','',5,5,5,5,5,5); // jika tampilan ingin landscape di ubah menjadi A4-L 
	// Angka diatas 20,20,40,20,20,10 => kiri,kanan,header,bottom,top, footer
	$mpdf->SetDisplayMode('fullpage');
	$mpdf->list_indent_first_level = 1;	// 1 or 0 - whether to indent the first level of a list
	// LOAD a stylesheet
	$stylesheet = file_get_contents('../../assets/pdf/mpdfstyletables3.css');
	$mpdf->WriteHTML($stylesheet,1);	// The parameter 1 tells that this is css/style only and no body/html/text
	$mpdf->SetHTMLHeader($header);
	$mpdf->WriteHTML($html,2);
	//$mpdf->setFooter('| Page {PAGENO}|') ;
	//$mpdf->AddPage('L','','','','',25,25,55,45,18,12);
	$mpdf->Output('invoice_'.str_replace("/", "_", $noFaktur).'.pdf','I'); // Nama File ketika pdf di download
	exit;
	
	
	//echo $html;
	//==============================================================
	//==============================================================
	//==============================================================
?>