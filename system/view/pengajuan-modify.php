<?php  
	require_once ('system/inc/permission.php'); 
	$akun = mssql_fetch_array(mssql_query("select pety_cash from settingAkun where id=1",$conns));
	$div = mssql_fetch_array(mssql_query("select divisi from sys_user where IdUser='".$_SESSION['UserID']."'",$conns));
	$modify = isset($_REQUEST['modify']) ? $_REQUEST['modify'] : null;
	if ($modify=='edit') {
		$vw = mssql_fetch_array(mssql_query("
			select a.*,namaUser 
			from DataEvo a left join sys_user b on b.IdUser=a.IdAtasan where evo_id = '".$_POST['id'][0]."'
		",$conns));
	}
	
	$qry_materai = mssql_query("select top 1 nominal_materai from sys_materai where aktif = '1'",$conns);
	$dt_materai = mssql_fetch_array($qry_materai);
	$nominal_materai = $dt_materai['nominal_materai'];
				
?>
<!-- <script type="text/javascript" src="system/myJs/pengajuan.js"></script> -->
<link rel="stylesheet" type="text/css" href="assets/plugins/flexii/style.css" />
<script type="text/javascript" src="assets/plugins/flexii/js/flexigrid.pack.js"></script>
<link rel="stylesheet" type="text/css" href="assets/plugins/flexii/css/flexigrid.pack.css" />
<link rel="stylesheet" type="text/css" href="assets/css/jquery.fileupload.css" />
<script type="text/javascript" src="assets/js/jquery.ui.widget.js"></script>
<script type="text/javascript" src="assets/js/jquery.fileupload.js"></script>
<script type="text/javascript" src="assets/js/jquery.fileupload-process.js"></script>
<script type="text/javascript" src="assets/js/jquery.fileupload-validate.js"></script>

<script src="assets/js/excel/xlsx.core.min.js"></script>  
<script src="assets/js/excel/xls.core.min.js"></script>

<style type="text/css">
	#datarapb th { font-weight: bold;text-align: center; }
	#datarapb td { text-align: right;padding: 0 5px; }
	.input-group-addon {padding: 0;min-width: 0;padding: 2px 10px; }
</style>
<script type="text/javascript">
	jQuery(document).ready(function($) {
		
		$('#div_materai').hide();
		
		$('#cancel').click(function(event){
			document.location.href="transaksi-pengajuan";
		});
		$('#new').click(function(){
			var IdUser = $("#IdUser").val();
			var KodeDealer = $("#KodeDealer").val();
			var Tipe = $("#Tipe").val();
			var divisi = $("#divisi").val();
			var IdAtasan = $("#IdAtasan").val();
			var nobukti = $("#nobukti").val();
			var tgl_pengajuan = $("#tgl_pengajuan").val();
			var upload_file = $("#upload_file").val();
			var upload_fp = $("#upload_fp").val();
			var kode_vendor = $("#kode_vendor").val();
			var namaVendor = $("#namaVendor").val();
			var metode_bayar = $("#metode_bayar").val();
			var benificary_account = $("#benificary_account").val();
			var tgl_bayar = $("#tgl_bayar").val();
			var nama_bank = $("#nama_bank").val();
			var nama_pemilik = $("#nama_pemilik").val();
			var email_penerima = $("#email_penerima").val();
			var nama_alias = $("#nama_alias").val();
			var kode_bank_pengirim = $("#kode_bank_pengirim").val();
			var nama_bank_pengirim = $("#nama_bank_pengirim").val();
			var tf_from_account = $("#tf_from_account").val();
			var realisasi_nominal = $("#realisasi_nominal").val();
			
			var ppn_persen = $("#ppn_persen").val();
			
			if (KodeDealer=='') {
				onload = needValue('Pengajuan Voucher Payment','Required Kode Dealer!');
			} else if (Tipe=='') {
				onload = needValue('Pengajuan Voucher Payment','Required Tipe!');
				// $("#Tipe").css({'border' : '1px solid #f31313','background' : '#f9b1b1'});
			 //    $("#Tipe").focus();
			} else if (divisi=='') {
				onload = needValue('Pengajuan Voucher Payment','Required Divisi!');
				// $("#divisi").css({'border' : '1px solid #f31313','background' : '#f9b1b1'});
			 //    $("#divisi").focus();
			} else if (IdAtasan=='') {
				onload = needValue('Pengajuan Voucher Payment','Required Nama Atasan!');
				// $("#IdAtasan").css({'border' : '1px solid #f31313','background' : '#f9b1b1'});
			 //    $("#IdAtasan").focus();
			} else if (metode_bayar=='Transfer' && (nama_bank_pengirim=='' || nama_bank_pengirim==' ')) {
				onload = needValue('Pengajuan Voucher Payment','Required Nama Bank Pengirim!');
			} else if (metode_bayar=='Transfer' && (tf_from_account=='' || tf_from_account==' ')) {
				onload = needValue('Pengajuan Voucher Payment','Required Transfer From Account!');
			} else {
				if (Tipe=='HUTANG') {
					var tipehutang = $("#tipehutang").val();
					var kodeAkun = $("#kodeAkun").val();
					var namaAkun = $("#namaAkun").val();
					var tipeppn = $("#tipeppn").val();
					var dpp = $("#dpp").val();
					var ppn = $("#ppn").val();
					var npwp = $("#npwp").val();
					var no_fj = $("#no_fj").val();
					var tagih = $("input[name='byr[]']").length;
					var tagihan = "";
					for (var i = 1; i <= tagih; i++) {
						if ($("#byr-"+i).is(":checked")) {
							var NoFaktur = $("#NoFaktur-"+i).val();
							var TglTrnFaktur = $("#TglTrnFaktur-"+i).val();
							var TglJthTmp = $("#TglJthTmp-"+i).val();
							var Keterangan = $("#Keterangan-"+i).val();
							var JumlahTrn = $("#JumlahTrn-"+i).val();
							tagihan += NoFaktur+"#"+TglTrnFaktur+"#"+TglJthTmp+"#"+Keterangan+"#"+JumlahTrn+"_cn_";
						}
					}
					var tagihan_ = tagihan.slice(0,-4);
					var trfPajak = $("select[name='trfPajak[]']").length;
					var trf_pajak = "";
					var nominal_pph_ = 0;
					
					for (var i = 1; i <= trfPajak; i++) {
						var nominal = $("#nominal_"+i).val();
						var jns_pph = $("#jns_pph_"+i).val();
						var tarif_persen = $("#tarif_persen_"+i).val();
						var akun_pph = $("#akun_pph_"+i).val();
						var nilaiPph = $("#nilaiPph_"+i).val();
						var keteranganAkun = $("#keteranganAkun_"+i).val();
						trf_pajak += nominal+"#"+jns_pph+"#"+tarif_persen+"#"+nilaiPph+"#"+akun_pph+"#"+keteranganAkun+"_cn_";
						
						var nilai_Pph = document.getElementById('nilaiPph_'+i).value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
						nominal_pph_ = parseInt(nominal_pph_) + parseInt(nilai_Pph);
					
					}
					var trf_pajak_ = trf_pajak.slice(0,-4);
					var htg_stl_pajak = $("#htg_stl_pajak").val();
					var keterangan = $("#keterangan").val();
					
					var tipe_materai = $("#tipe_materai").val();
					var nominal_materai = $("#nominal_materai").val();
					var deptterkait = $("#deptterkait").val();
					
					var kodeAkunMaterai = $("#kodeAkunMaterai").val();
					var namaAkunMaterai = $("#namaAkunMaterai").val();
					
					var totDppHtg_ = document.getElementById('totDppHtg').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
					
					// perhitungan ppn
					var tipeppn = $("#tipeppn").val();
					var ppn_ = document.getElementById('ppn').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
					var dpp_ = document.getElementById('dpp').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
					//var totDppHtg_ = document.getElementById('totDppHtg').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
					var nominal_materai_ = document.getElementById('nominal_materai').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
					var realisasi_nominal_ = document.getElementById('realisasi_nominal').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
					var htg_stl_pajak_ = document.getElementById('htg_stl_pajak').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
					var nom = 0;
					
					if (tipeppn=='N') {
						if (tipe_materai=="I") { // include
							var total_ = parseInt(dpp_) - parseInt(nominal_pph_) + parseInt(nominal_materai_); //+ parseInt(nominal_materai_)
						} else if (tipe_materai=="E") { // exclude
							var total_ = parseInt(dpp_) - parseInt(nominal_pph_);
						} else { // non materai
							var total_ = parseInt(dpp_) - parseInt(nominal_pph_); 
						}
												
					} else if (tipeppn=='E') {
						if (tipe_materai=="I") { // include
							var total_ = parseInt(dpp_)  - parseInt(nominal_pph_) + parseInt(nominal_materai_); // 
						} else if (tipe_materai=="E") { // exclude
							var total_ = parseInt(dpp_) - parseInt(nominal_pph_);
						} else { // non materai
							var total_ = parseInt(dpp_) - parseInt(nominal_pph_);
						}
													
					} else if (tipeppn=='I') {
						
						if (tipe_materai=="I") { // include
							var total_ = parseInt(dpp_) + parseInt(ppn_)  - parseInt(nominal_pph_) + parseInt(nominal_materai_); //+ parseInt(nominal_materai_)
						} else if (tipe_materai=="E") { // exclude
							var total_ = parseInt(dpp_) + parseInt(ppn_) - parseInt(nominal_pph_);
						} else { // non materai
							var total_ = parseInt(dpp_) + parseInt(ppn_) - parseInt(nominal_pph_);
						}						
					}
					
					
					if (tipehutang=='') {
						onload = needValue('Pengajuan Voucher Payment','Required Tipe Hutang!');
					} else if (upload_file=='' || upload_file==' ') {
						onload = needValue('Pengajuan Voucher Payment','Required Upload File!');
					} else if ((upload_fp=='' || upload_fp==' ') && (no_fj!='' || no_fj!=' ') && tipeppn!='N') {
						onload = needValue('Pengajuan Voucher Payment','Required Upload FP!');
					} else if (kode_vendor=='' || kode_vendor==' ') {
						onload = needValue('Pengajuan Voucher Payment','Required Kode Vendor!');
					} else if(metode_bayar=='') {
						onload = needValue('Pengajuan Voucher Payment','Required Metode Bayar!');
					} else if (benificary_account=='' || benificary_account==' ') {
						onload = needValue('Pengajuan Voucher Payment','Required Beneficary Account! <br/> Harap Isi di Nis Accounting Kode Supplier');
					} else if (tgl_bayar=='' || tgl_bayar==' ') {
						onload = needValue('Pengajuan Voucher Payment','Required Tanggal Bayar!');
					} else if (nama_bank=='' || nama_bank==' ') {
						onload = needValue('Pengajuan Voucher Payment','Required Nama Bank Penerima! <br/> Harap Isi di Nis Accounting Kode Supplier');
					} else if (nama_pemilik=='' || nama_pemilik==' ') {
						onload = needValue('Pengajuan Voucher Payment','Required Nama Pemilik! <br/> Harap Isi di Nis Accounting Kode Supplier');
					} else if (email_penerima=='' || email_penerima==' ') {
						onload = needValue('Pengajuan Voucher Payment','Required Email Penerima! <br/> Harap Isi di Nis Accounting Kode Supplier');
					} else if (nama_alias=='' || nama_alias=='') {
						onload = needValue('Pengajuan Voucher Payment','Required Nama Alias! <br/> Harap Isi di Nis Accounting Kode Supplier');
					} else if (kodeAkun=='' || kodeAkun==' ') {
						onload = needValue('Pengajuan Voucher Payment','Required Kode Akun!');
					} else if ((npwp=='' || npwp==' ') && tipeppn!='N') {
						onload = needValue('Pengajuan Voucher Payment','Required NPWP! <br/> Harap Isi di Nis Accounting Kode Supplier');
					} else if ((no_fj=='' || no_fj==' ') && tipeppn!='N') {
						onload = needValue('Pengajuan Voucher Payment','Required No Faktur Pajak!');
					} else if (parseInt(dpp_)!=parseInt(totDppHtg_)) {
						onload = needValue('Pengajuan Voucher Payment','Nominal Dpp tidak Balance!');
					} else if (keterangan=='' || keterangan==' ') {
						onload = needValue('Pengajuan Voucher Payment','Required Keterangan!');
					} else if (parseInt(htg_stl_pajak_)!=parseInt(total_)) {
						onload = needValue('Pengajuan Voucher Payment','Nominal Dpp & PPN tidak Balance!');	
					} else {
						$.ajax({ 
						    url: 'system/control/pengajuan.php',
						    data: {
						    	action:'new-hutang', 
						    	'IdUser': IdUser, 
						    	'KodeDealer': KodeDealer, 
						    	'Tipe': Tipe, 
						    	'divisi': divisi,
						    	'IdAtasan': IdAtasan,
						    	'nobukti': nobukti, 
						    	'tgl_pengajuan': tgl_pengajuan, 
						    	'upload_file': upload_file, 
						    	'upload_fp': upload_fp, 
						    	'kode_vendor': kode_vendor, 
						    	'namaVendor': namaVendor, 
						    	'metode_bayar': metode_bayar, 
						    	'benificary_account': benificary_account, 
						    	'tgl_bayar': tgl_bayar, 
						    	'nama_bank': nama_bank,
						    	'nama_pemilik': nama_pemilik, 
						    	'email_penerima': email_penerima, 
						    	'nama_alias': nama_alias, 
						    	'kode_bank_pengirim': kode_bank_pengirim, 
						    	'nama_bank_pengirim': nama_bank_pengirim, 
						    	'tf_from_account': tf_from_account,
						    	'realisasi_nominal': realisasi_nominal,
						    	'kodeAkun': kodeAkun,
						    	'namaAkun': namaAkun,
						    	'tipeppn': tipeppn,
						    	'dpp': dpp,
						    	'ppn': ppn,
						    	'npwp': npwp,
						    	'no_fj': no_fj,
						    	'tagihan': tagihan_,
						    	'trf_pajak': trf_pajak_,
						    	'htg_stl_pajak': htg_stl_pajak,
						    	'keterangan': keterangan,
						    	'tipehutang':tipehutang,
						    	'tipe_materai':tipe_materai,
						    	'nominal_materai':nominal_materai,
						    	'deptterkait':deptterkait,
								'kodeAkunMaterai': kodeAkunMaterai,
						    	'namaAkunMaterai': namaAkunMaterai,
								'ppn_persen': ppn_persen
						    },
						    type: 'post',
						    beforeSend: function(){
						    	onload = showLoading();
						    },
						    success: function(output) {
						    	var msg = output.split("#");
						    	if (msg[0]=='1') {
						    		var ajax1 = $.ajax({ 
										url: 'email.php',
										data: {'id': msg[2]},
										success: function(result) {}
									});
									var nik = msg[4];
									var pesan = msg[3];
									
									/*var nik = msg[4].split(";");
									var pesan = msg[3].split(";");
									for (var i = 0; i < nik.length; i++) {
										if (nik[i]!='') {
											var url = "https://nasmoco.net/notifevopay/pushnotif_new.php?nik="+nik[i]+"&pesan="+pesan[i]+"&sumber=EVOPAY&nobukti="+msg[2]+"&sender=evopay&judul=Validasi Voucher Payment";
											$("#sendIntra").append('<iframe id="myFrame_'+i+'" src="" style="display: none"></iframe>');
											$("#myFrame_"+i).attr('src', url);
											document.getElementById('myFrame_'+i).src = document.getElementById('myFrame_'+i).src
										}
									}*/
									if (nik!='') {
										var url = "https://nasmoco.net/notifevopay/pushnotif_new.php?nik="+nik+"&pesan="+pesan+"&sumber=EVOPAY&nobukti="+msg[2]+"&sender=evopay&judul=Validasi Voucher Payment";
										$("#sendIntra").append('<iframe id="myFrame_0" src="" style="display: none"></iframe>');
										$("#myFrame_0").attr('src', url);
										document.getElementById('myFrame_0').src = document.getElementById('myFrame_0').src
						    		}
								}
						    	//$.when(ajax1).done(function(a1) {
								//	if (a1) {
										onload = hideLoading();
										// document.location.href="transaksi-pengajuan";
										bootbox.dialog({
										    closeButton : false,
											className : "resize",
										    message: msg[1],
										    title: "Pengajuan Voucher Payment",
										    buttons: {
										        main: {
										            label: "Ok",
										            className: "btn-sm btn-primary",
										            callback: function() {
														if (msg[0]=='1') {
															bootbox.dialog({
															    closeButton : false,
																className : "resize",
															    message: "Kirim notifikasi?",
															    title: "Pengajuan Voucher Payment",
															    buttons: {
																    confirm: {
																        label: 'Yes',
																        className: "btn-sm btn-primary",
																        callback: function () {
																        	onload = showLoading();
																			var phone = msg[6];
																			var text = msg[7];
																			
																	    	/*var phone = msg[6].split(";");
																			var text = msg[7].split(";");
																			for (var i = 0; i < nik.length; i++) {
																				if (phone[i]!='') {
																					window.open('https://api.whatsapp.com/send?phone='+phone[i]+'&text='+text[i]);
																				}
																			}*/
																			if (phone!='') {
																				window.open('https://api.whatsapp.com/send?phone='+phone+'&text='+text);
																			}
																			onload = hideLoading();
																			document.location.href="transaksi-pengajuan";
																			// $.when(ajax1).done(function(a1) {
																			// 	if (a1) {
																			// 		onload = hideLoading();
																			// 		document.location.href="transaksi-pengajuan";
																			// 	}
																			// });
																        }
																    },
																    cancel: {
																        label: 'No',
																        className: 'btn-sm btn-danger',
																        callback: function () {
																		    document.location.href="transaksi-pengajuan";
																		}
																    }
																}
															});
														}
										            }
										        }
										    }
										});
									//}
								//});
						    	// onload = hideLoading();
							}
						});
					}
					
				} else if (Tipe=='BIAYA') {
					var status = $("#status").val();
					var is_ppn = $("#is_ppn").val();
					var dpp = $("#dpp").val();
					var ppn = $("#ppn").val();
					var npwp = $("#npwp").val();
					var no_fj = $("#no_fj").val();
					var posbiaya = $("input[name='posbiaya[]']").length;
					var pos_biaya = "";
					var nominal_pph_ = 0;
					
					for (var i = 1; i <= posbiaya; i++) {
						var kodeAkun = $("#kodeAkun_"+i).val();
						var nominal = $("#nominal_"+i).val();
						var jns_pph = $("#jns_pph_"+i).val();
						var tarif_persen = $("#tarif_persen_"+i).val();
						var nilaiPph = $("#nilaiPph_"+i).val();
						var ketAkun = $("#ketAkun_"+i).val();
						var akun_pph = $("#akun_pph_"+i).val();
						var keteranganAkun = $("#keteranganAkun_"+i).val();
						if (kodeAkun == '') { pos_biaya=""; break;  }
						pos_biaya += kodeAkun+"#"+nominal+"#"+jns_pph+"#"+tarif_persen+"#"+nilaiPph+"#"+ketAkun+"#"+akun_pph+"#"+keteranganAkun+"_cn_";
					
						var nilai_Pph = document.getElementById('nilaiPph_'+i).value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
						nominal_pph_ = parseInt(nominal_pph_) + parseInt(nilai_Pph);
					}
					var posbiaya_ = pos_biaya.slice(0,-4);
					var total_dpp = $("#total_dpp").val();
					var biaya_yg_dibyar = $("#biaya_yg_dibyar").val();
					var keterangan = $("#keterangan").val();
					var deptterkait = $("#deptterkait").val();
					
					var real_nom_ = document.getElementById('realisasi_nominal').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
					
					// perhitungan cek ppn
					var nom = document.getElementById('realisasi_nominal').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
					var ppn_ = document.getElementById('ppn').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
					var dpp_ = document.getElementById('dpp').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
					var total_dpp_ = document.getElementById('total_dpp').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
					var biaya_yg_dibyar_ = document.getElementById('biaya_yg_dibyar').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
					
					if ($('#is_ppn').prop("checked")) {
						var tipeppn = $("#tipeppn").val();						
						var total_ = parseInt(dpp_) + parseInt(ppn_) - parseInt(nominal_pph_);					
					} else {		
						var total_ = parseInt(dpp_) - parseInt(nominal_pph_);
					}
					
					if (upload_file=='' || upload_file==' ') {
						onload = needValue('Pengajuan Voucher Payment','Required Upload File!');
					} else if ((upload_fp=='' || upload_file==' ') && (no_fj!='' || no_fj!=' ') && is_ppn!='0') {
						onload = needValue('Pengajuan Voucher Payment','Required Upload FP!');
					} else if (kode_vendor=='' || kode_vendor==' ') {
						onload = needValue('Pengajuan Voucher Payment','Required Kode Vendor!');
					} else if(metode_bayar=='') {
						onload = needValue('Pengajuan Voucher Payment','Required Metode Bayar!');
					} else if (benificary_account=='' || benificary_account==' ') {
						onload = needValue('Pengajuan Voucher Payment','Required Beneficary Account! <br/> Harap Isi di Nis Accounting Kode Supplier');
					} else if (tgl_bayar=='' || tgl_bayar==' ') {
						onload = needValue('Pengajuan Voucher Payment','Required Tanggal Bayar!');
					} else if (nama_bank=='' || nama_bank==' ') {
						onload = needValue('Pengajuan Voucher Payment','Required Nama Bank Penerima! <br/> Harap Isi di Nis Accounting Kode Supplier');
					} else if (nama_pemilik=='' || nama_pemilik==' ') {
						onload = needValue('Pengajuan Voucher Payment','Required Nama Pemilik! <br/> Harap Isi di Nis Accounting Kode Supplier');
					} else if (email_penerima=='' || email_penerima==' ') {
						onload = needValue('Pengajuan Voucher Payment','Required Email Penerima! <br/> Harap Isi di Nis Accounting Kode Supplier');
					} else if (nama_alias=='' || nama_alias==' ') {
						onload = needValue('Pengajuan Voucher Payment','Required Nama Alias! <br/> Harap Isi di Nis Accounting Kode Supplier');
					} else if ((npwp=='' || npwp==' ') && is_ppn!='0') { 
						onload = needValue('Pengajuan Voucher Payment','Required NPWP! <br/> Harap Isi di Nis Accounting Kode Supplier');
					} else if ((no_fj=='' || nama_alias==' ') && is_ppn!='0') {
						onload = needValue('Pengajuan Voucher Payment','Required No Faktur Pajak!');
					} else if (keterangan=='' || keterangan==' ') {
						onload = needValue('Pengajuan Voucher Payment','Required Keterangan!');
					} else if (is_ppn=='0' && parseInt(total_dpp_)!=parseInt(real_nom_)) {
						onload = needValue('Pengajuan Voucher Payment','Nominal Belom Balance!');
					} else if (is_ppn=='1' && parseInt(total_dpp_)!=parseInt(dpp_)) {
						onload = needValue('Pengajuan Voucher Payment','Nominal Belom Balance!');
					} else if (posbiaya_=='') {
						onload = needValue('Pengajuan Voucher Payment','Required Pos Biaya!');
					} else if (parseInt(biaya_yg_dibyar_)!=parseInt(total_)) {
						onload = needValue('Pengajuan Voucher Payment','Nominal Dpp & PPN tidak Balance!');	
						
					} else {
						$.ajax({ 
						    url: 'system/control/pengajuan.php',
						    data: {
						    	action:'new-biaya', 
						    	'IdUser': IdUser, 
						    	'KodeDealer': KodeDealer, 
						    	'Tipe': Tipe, 
						    	'divisi': divisi,
						    	'IdAtasan': IdAtasan,
						    	'status': status, 
						    	'nobukti': nobukti, 
						    	'tgl_pengajuan': tgl_pengajuan, 
						    	'upload_file': upload_file, 
						    	'upload_fp': upload_fp, 
						    	'kode_vendor': kode_vendor, 
						    	'namaVendor': namaVendor, 
						    	'metode_bayar': metode_bayar, 
						    	'benificary_account': benificary_account, 
						    	'tgl_bayar': tgl_bayar,
						    	'nama_bank': nama_bank, 
						    	'nama_pemilik': nama_pemilik, 
						    	'email_penerima': email_penerima, 
						    	'nama_alias': nama_alias, 
						    	'kode_bank_pengirim': kode_bank_pengirim,
						    	'nama_bank_pengirim': nama_bank_pengirim,
						    	'tf_from_account': tf_from_account,
						    	'realisasi_nominal': realisasi_nominal,
						    	'is_ppn': is_ppn,
						    	'dpp': dpp,
						    	'ppn': ppn,
						    	'npwp': npwp,
						    	'no_fj': no_fj,
						    	'posbiaya': posbiaya_,
						    	'total_dpp': total_dpp,
						    	'biaya_yg_dibyar': biaya_yg_dibyar,
						    	'keterangan': keterangan,
								'deptterkait':deptterkait,
								'ppn_persen':ppn_persen
						    },
						    type: 'post',
						    beforeSend: function(){
						    	onload = showLoading();
						    },
						    success: function(output) {
								var msg = output.split("#"); 
								
								// $pesan = "1#Data Save!!#".$dt['nobukti']."#".$n_bodyIntra."#".$n_nik."#".$n_email."#".$n_no_tlp."#".$n_bodyWa;
								if (msg[0]=='1') {
									var ajax1 = $.ajax({ 
										url: 'email.php',
										data: {'id': msg[2]},
										success: function(result) {}
									});
									var nik = msg[4];
									var pesan = msg[3];
									
									/*var nik = msg[4].split(";");
									var pesan = msg[3].split(";");
									for (var i = 0; i < nik.length; i++) {
										if (nik[i]!='') {
											var url = "https://nasmoco.net/notifevopay/pushnotif_new.php?nik="+nik[i]+"&pesan="+pesan[i]+"&sumber=EVOPAY&nobukti="+msg[2]+"&sender=evopay&judul=Validasi Voucher Payment";
											$("#sendIntra").append('<iframe id="myFrame_'+i+'" src="" style="display: none"></iframe>');
												$("#myFrame_"+i).attr('src', url);
												document.getElementById('myFrame_'+i).src = document.getElementById('myFrame_'+i).src
										}
									}*/
									if (nik!='') {
										var url = "https://nasmoco.net/notifevopay/pushnotif_new.php?nik="+nik+"&pesan="+pesan+"&sumber=EVOPAY&nobukti="+msg[2]+"&sender=evopay&judul=Validasi Voucher Payment";
										//$("#sendIntra").append('<iframe id="myFrame_0" src="" style="display: none"></iframe>');
										//$("#myFrame_0").attr('src', url);
										//document.getElementById('myFrame_'+i).src = document.getElementById('myFrame_0').src
									}
									
									bootbox.dialog({
										closeButton : false,
										className : "resize",
										message: "Pengajuan Voucher Payment " + msg[2] + " telah tersimpan. Kirim notifikasi WhatsApp?",
										title: "Pengajuan Voucher Payment",
										buttons: {
											confirm: {
												label: 'Yes',
												className: "btn-sm btn-primary",
												callback: function () {
													onload = showLoading();
													var phone = msg[6];
													var text = msg[7];
													//var phone = msg[6].split(";");
													//var text = msg[7].split(";");
													//for (var i = 0; i < nik.length; i++) {
													//	if (phone[i]!='') {
													//		window.open('https://api.whatsapp.com/send?phone='+phone[i]+'&text='+text[i]);
													//	}
													//}
													if (phone!='') {
														window.open('https://api.whatsapp.com/send?phone='+phone+'&text='+text);
													}
													
													onload = hideLoading();
													document.location.href="transaksi-pengajuan";
												}
											},
											cancel: {
												label: 'No',
												className: 'btn-sm btn-danger',
												callback: function () {
													document.location.href="transaksi-pengajuan";
												}
											}
										}
									});
									//document.location.href="transaksi-pengajuan";
								}

								//$.when(ajax1).done(function(a1) {
									//if (a1) {
										//onload = hideLoading();
										// document.location.href="transaksi-pengajuan";
										
										
									//}
								//});
						    	// onload = hideLoading();
							}
						});
					}
				}
			}
		});

		$('#edit').click(function(){
			var IdUser = $("#IdUser").val();
			var KodeDealer = $("#KodeDealer").val();
			var Tipe = $("#Tipe").val();
			var divisi = $("#divisi").val();
			var IdAtasan = $("#IdAtasan").val();
			var nobukti = $("#nobukti").val();
			var tgl_pengajuan = $("#tgl_pengajuan").val();
			var upload_file = $("#upload_file").val();
			var upload_fp = $("#upload_fp").val();
			var kode_vendor = $("#kode_vendor").val();
			var namaVendor = $("#namaVendor").val();
			var metode_bayar = $("#metode_bayar").val();
			var benificary_account = $("#benificary_account").val();
			var tgl_bayar = $("#tgl_bayar").val();
			var nama_bank = $("#nama_bank").val();
			var nama_pemilik = $("#nama_pemilik").val();
			var email_penerima = $("#email_penerima").val();
			var nama_alias = $("#nama_alias").val();
			var kode_bank_pengirim = $("#kode_bank_pengirim").val();
			var nama_bank_pengirim = $("#nama_bank_pengirim").val();
			var tf_from_account = $("#tf_from_account").val();
			var realisasi_nominal = $("#realisasi_nominal").val();
			
			var ppn_persen = $("#ppn_persen").val();
			
			if (KodeDealer=='') {
				onload = needValue('Pengajuan Voucher Payment','Required Kode Dealer!');
			} else if (Tipe=='') {
				onload = needValue('Pengajuan Voucher Payment','Required Tipe!');
			} else if (divisi=='') {
				onload = needValue('Pengajuan Voucher Payment','Required Divisi!');
			} else if (IdAtasan=='') {
				onload = needValue('Pengajuan Voucher Payment','Required Nama Atasan!');
			} else if (metode_bayar=='Transfer' && (nama_bank_pengirim=='' || nama_bank_pengirim==' ')) {
				onload = needValue('Pengajuan Voucher Payment','Required Nama Bank Pengirim!');
			} else if (metode_bayar=='Transfer' && (tf_from_account=='' || tf_from_account==' ')) {
				onload = needValue('Pengajuan Voucher Payment','Required Transfer From Account!');
			} else {
				if (Tipe=='HUTANG') {
					var tipehutang = $("#tipehutang").val();
					var kodeAkun = $("#kodeAkun").val();
					var namaAkun = $("#namaAkun").val();
					var tipeppn = $("#tipeppn").val();
					var dpp = $("#dpp").val();
					var ppn = $("#ppn").val();
					var npwp = $("#npwp").val();
					var no_fj = $("#no_fj").val();
					var tagih = $("input[name='byr[]']").length;
					var tagihan = "";
					for (var i = 1; i <= tagih; i++) {
						if ($("#byr-"+i).is(":checked")) {
							var NoFaktur = $("#NoFaktur-"+i).val();
							var TglTrnFaktur = $("#TglTrnFaktur-"+i).val();
							var TglJthTmp = $("#TglJthTmp-"+i).val();
							var Keterangan = $("#Keterangan-"+i).val();
							var JumlahTrn = $("#JumlahTrn-"+i).val();
							tagihan += NoFaktur+"#"+TglTrnFaktur+"#"+TglJthTmp+"#"+Keterangan+"#"+JumlahTrn+"_cn_";
						}
					}
					var tagihan_ = tagihan.slice(0,-4);
					var trfPajak = $("select[name='trfPajak[]']").length;
					var trf_pajak = "";
					for (var i = 1; i <= trfPajak; i++) {
						var nominal = $("#nominal_"+i).val();
						var jns_pph = $("#jns_pph_"+i).val();
						var tarif_persen = $("#tarif_persen_"+i).val();
						var akun_pph = $("#akun_pph_"+i).val();
						var nilaiPph = $("#nilaiPph_"+i).val();
						var keteranganAkun = $("#keteranganAkun_"+i).val();
						trf_pajak += nominal+"#"+jns_pph+"#"+tarif_persen+"#"+nilaiPph+"#"+akun_pph+"#"+keteranganAkun+"_cn_";
					}
					var trf_pajak_ = trf_pajak.slice(0,-4);
					var htg_stl_pajak = $("#htg_stl_pajak").val();
					var keterangan = $("#keterangan").val();
					
					var tipe_materai = $("#tipe_materai").val();
					var nominal_materai = $("#nominal_materai").val();
					var deptterkait = $("#deptterkait").val();
					
					var kodeAkunMaterai = $("#kodeAkunMaterai").val();
					var namaAkunMaterai = $("#namaAkunMaterai").val();
					
					if (tipehutang=='') {
						onload = needValue('Pengajuan Voucher Payment','Required Tipe Hutang!');
					} else if (upload_file=='' || upload_file==' ') {
						onload = needValue('Pengajuan Voucher Payment','Required Upload File!');
					} else if ((upload_fp=='' || upload_fp==' ') && (no_fj!='' || no_fj!=' ') && tipeppn!='N') {
						onload = needValue('Pengajuan Voucher Payment','Required Upload FP!');
					} else if (kode_vendor=='' || kode_vendor==' ') {
						onload = needValue('Pengajuan Voucher Payment','Required Kode Vendor!');
					} else if(metode_bayar=='') {
						onload = needValue('Pengajuan Voucher Payment','Required Metode Bayar!');
					} else if (benificary_account=='' || benificary_account==' ') {
						onload = needValue('Pengajuan Voucher Payment','Required Beneficary Account! <br/> Harap Isi di Nis Accounting Kode Supplier');
					} else if (tgl_bayar=='' || tgl_bayar==' ') {
						onload = needValue('Pengajuan Voucher Payment','Required Tanggal Bayar!');
					} else if (nama_bank=='' || nama_bank==' ') {
						onload = needValue('Pengajuan Voucher Payment','Required Nama Bank Penerima! <br/> Harap Isi di Nis Accounting Kode Supplier');
					} else if (nama_pemilik=='' || nama_pemilik==' ') {
						onload = needValue('Pengajuan Voucher Payment','Required Nama Pemilik! <br/> Harap Isi di Nis Accounting Kode Supplier');
					} else if (email_penerima=='' || email_penerima==' ') {
						onload = needValue('Pengajuan Voucher Payment','Required Email Penerima! <br/> Harap Isi di Nis Accounting Kode Supplier');
					} else if (nama_alias=='' || nama_alias=='') {
						onload = needValue('Pengajuan Voucher Payment','Required Nama Alias! <br/> Harap Isi di Nis Accounting Kode Supplier');
					} else if (kodeAkun=='' || kodeAkun==' ') {
						onload = needValue('Pengajuan Voucher Payment','Required Kode Akun!');
					} else if ((npwp=='' || npwp==' ') && tipeppn!='N') {
						onload = needValue('Pengajuan Voucher Payment','Required NPWP! <br/> Harap Isi di Nis Accounting Kode Supplier');
					} else if ((no_fj=='' || no_fj==' ') && tipeppn!='N') {
						onload = needValue('Pengajuan Voucher Payment','Required No Faktur Pajak!');
					} else if (keterangan=='' || keterangan==' ') {
						onload = needValue('Pengajuan Voucher Payment','Required Keterangan!');
					} else {
						$.ajax({ 
						    url: 'system/control/pengajuan.php',
						    data: {
						    	action:'edit-hutang', 
						    	'IdUser': IdUser, 
						    	'KodeDealer': KodeDealer, 
						    	'Tipe': Tipe, 
						    	'divisi': divisi,
						    	'IdAtasan': IdAtasan,
						    	'nobukti': nobukti, 
						    	'tgl_pengajuan': tgl_pengajuan, 
						    	'upload_file': upload_file, 
						    	'upload_fp': upload_fp, 
						    	'kode_vendor': kode_vendor, 
						    	'namaVendor': namaVendor, 
						    	'metode_bayar': metode_bayar, 
						    	'benificary_account': benificary_account, 
						    	'tgl_bayar': tgl_bayar, 
						    	'nama_bank': nama_bank,
						    	'nama_pemilik': nama_pemilik, 
						    	'email_penerima': email_penerima, 
						    	'nama_alias': nama_alias, 
						    	'kode_bank_pengirim': kode_bank_pengirim,
						    	'nama_bank_pengirim': nama_bank_pengirim, 
						    	'tf_from_account': tf_from_account,
						    	'realisasi_nominal': realisasi_nominal,
						    	'kodeAkun': kodeAkun,
						    	'namaAkun': namaAkun,
						    	'tipeppn': tipeppn,
						    	'dpp': dpp,
						    	'ppn': ppn,
						    	'npwp': npwp,
						    	'no_fj': no_fj,
						    	'tagihan': tagihan_,
						    	'trf_pajak': trf_pajak_,
						    	'htg_stl_pajak': htg_stl_pajak,
						    	'keterangan': keterangan,
						    	'tipehutang':tipehutang,
						    	'tipe_materai':tipe_materai,
						    	'nominal_materai':nominal_materai,
						    	'deptterkait':deptterkait,
								'kodeAkunMaterai': kodeAkunMaterai,
						    	'namaAkunMaterai': namaAkunMaterai,
								'ppn_persen': ppn_persen
						    },
						    type: 'post',
						    beforeSend: function(){
						    	onload = showLoading();
						    },
						    success: function(output) {
						    	var msg = output.split("#");
						    	onload = hideLoading();
								bootbox.dialog({
								    closeButton : false,
									className : "resize",
								    message: msg[1],
								    title: "Pengajuan Voucher Payment",
								    buttons: {
								        main: {
								            label: "Ok",
								            className: "btn-sm btn-primary",
								            callback: function() {
												bootbox.dialog({
												    closeButton : false,
													className : "resize",
												    message: "Kirim notifikasi?",
												    title: "Pengajuan Voucher Payment",
												    buttons: {
													    confirm: {
													        label: 'Yes',
													        className: "btn-sm btn-primary",
													        callback: function () {
													        	onload = showLoading();
														    	var ajax1 = $.ajax({ 
																	url: 'email.php',
																	data: {'id': msg[2]},
																	success: function(result) {}
																});
																var nik = msg[4].split(";");
																var pesan = msg[3].split(";");
																for (var i = 0; i < nik.length; i++) {
																	if (nik[i]!='') {
																		var ajax2 = $.ajax({ 
																			url: "https://nasmoco.net/notifevopay/pushnotif_new.php?nik="+nik[i]+"&pesan="+pesan[i]+"&sumber=EVOPAY&nobukti="+msg[2]+"&sender=evopay&judul=Validasi Voucher Payment",
																			success: function(result2) {}  
																		});
																	}
																}

																var phone = msg[6].split(";");
																var text = msg[7].split(";");
																for (var i = 0; i < nik.length; i++) {
																	if (phone[i]!='') {
																		window.open('https://api.whatsapp.com/send?phone='+phone[i]+'&text='+text[i]);
																	}
																}
																$.when(ajax1).done(function(a1) {
																	if (a1) {
																		onload = hideLoading();
																		document.location.href="transaksi-pengajuan";
																	}
																});
													        }
													    },
													    cancel: {
													        label: 'No',
													        className: 'btn-sm btn-danger',
													        callback: function () {
															    document.location.href="transaksi-pengajuan";
															}
													    }
													}
												});
								            }
								        }
								    }
								});
							}
						});
					}
				} else if (Tipe=='BIAYA') {
					var status = $("#status").val();
					var is_ppn = $("#is_ppn").val();
					var dpp = $("#dpp").val();
					var ppn = $("#ppn").val();
					var npwp = $("#npwp").val();
					var no_fj = $("#no_fj").val();
					var posbiaya = $("input[name='posbiaya[]']").length;
					var pos_biaya = "";
					for (var i = 1; i <= posbiaya; i++) {
						var kodeAkun = $("#kodeAkun_"+i).val();
						var nominal = $("#nominal_"+i).val();
						var jns_pph = $("#jns_pph_"+i).val();
						var tarif_persen = $("#tarif_persen_"+i).val();
						var nilaiPph = $("#nilaiPph_"+i).val();
						var ketAkun = $("#ketAkun_"+i).val();
						var akun_pph = $("#akun_pph_"+i).val();
						var keteranganAkun = $("#keteranganAkun_"+i).val();
						pos_biaya += kodeAkun+"#"+nominal+"#"+jns_pph+"#"+tarif_persen+"#"+nilaiPph+"#"+ketAkun+"#"+akun_pph+"#"+keteranganAkun+"_cn_";
					}
					var posbiaya_ = pos_biaya.slice(0,-4);
					var total_dpp = $("#total_dpp").val();
					var biaya_yg_dibyar = $("#biaya_yg_dibyar").val();
					var keterangan = $("#keterangan").val();

					var total_dpp_ = document.getElementById('total_dpp').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
					var real_nom_ = document.getElementById('realisasi_nominal').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
					var dpp_ = document.getElementById('dpp').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
					
					if (upload_file=='' || upload_file==' ') {
						onload = needValue('Pengajuan Voucher Payment','Required Upload File!');
					} else if ((upload_fp=='' || upload_file==' ') && (no_fj!='' || no_fj!=' ') && is_ppn!='0') {
						onload = needValue('Pengajuan Voucher Payment','Required Upload FP!');
					} else if (kode_vendor=='' || kode_vendor==' ') {
						onload = needValue('Pengajuan Voucher Payment','Required Kode Vendor!');
					} else if(metode_bayar=='') {
						onload = needValue('Pengajuan Voucher Payment','Required Metode Bayar!');
					} else if (benificary_account=='' || benificary_account==' ') {
						onload = needValue('Pengajuan Voucher Payment','Required Beneficary Account! <br/> Harap Isi di Nis Accounting Kode Supplier');
					} else if (tgl_bayar=='' || tgl_bayar==' ') {
						onload = needValue('Pengajuan Voucher Payment','Required Tanggal Bayar!');
					} else if (nama_bank=='' || nama_bank==' ') {
						onload = needValue('Pengajuan Voucher Payment','Required Nama Bank Penerima! <br/> Harap Isi di Nis Accounting Kode Supplier');
					} else if (nama_pemilik=='' || nama_pemilik==' ') {
						onload = needValue('Pengajuan Voucher Payment','Required Nama Pemilik! <br/> Harap Isi di Nis Accounting Kode Supplier');
					} else if (email_penerima=='' || email_penerima==' ') {
						onload = needValue('Pengajuan Voucher Payment','Required Email Penerima! <br/> Harap Isi di Nis Accounting Kode Supplier');
					} else if (nama_alias=='' || nama_alias==' ') {
						onload = needValue('Pengajuan Voucher Payment','Required Nama Alias! <br/> Harap Isi di Nis Accounting Kode Supplier');
					} else if ((npwp=='' || npwp==' ') && is_ppn!='0') { 
						onload = needValue('Pengajuan Voucher Payment','Required NPWP! <br/> Harap Isi di Nis Accounting Kode Supplier');
					} else if ((no_fj=='' || no_fj==' ') && is_ppn!='0') {
						onload = needValue('Pengajuan Voucher Payment','Required No Faktur Pajak!');
					} else if (keterangan=='' || keterangan==' ') {
						onload = needValue('Pengajuan Voucher Payment','Required Keterangan!');
					} else if (is_ppn=='0' && parseInt(total_dpp_)!=parseInt(real_nom_)) {
						onload = needValue('Pengajuan Voucher Payment','Nominal Belom Balance!');
					} else if (is_ppn=='1' && parseInt(total_dpp_)!=parseInt(dpp_)) {
						onload = needValue('Pengajuan Voucher Payment','Nominal Belom Balance!');
					} else if (posbiaya_=='') {
						onload = needValue('Pengajuan Voucher Payment','Required Pos Biaya!');
					} else {
						$.ajax({ 
						    url: 'system/control/pengajuan.php',
						    data: {
						    	action:'edit-biaya', 
						    	'IdUser': IdUser, 
						    	'KodeDealer': KodeDealer, 
						    	'Tipe': Tipe, 
						    	'divisi': divisi,
						    	'IdAtasan': IdAtasan,
						    	'status': status, 
						    	'nobukti': nobukti, 
						    	'tgl_pengajuan': tgl_pengajuan, 
						    	'upload_file': upload_file, 
						    	'upload_fp': upload_fp, 
						    	'kode_vendor': kode_vendor, 
						    	'namaVendor': namaVendor, 
						    	'metode_bayar': metode_bayar, 
						    	'benificary_account': benificary_account, 
						    	'tgl_bayar': tgl_bayar,
						    	'nama_bank': nama_bank, 
						    	'nama_pemilik': nama_pemilik, 
						    	'email_penerima': email_penerima, 
						    	'nama_alias': nama_alias, 
						    	'kode_bank_pengirim': kode_bank_pengirim,
						    	'nama_bank_pengirim': nama_bank_pengirim,
						    	'tf_from_account': tf_from_account,
						    	'realisasi_nominal': realisasi_nominal,
						    	'is_ppn': is_ppn,
						    	'dpp': dpp,
						    	'ppn': ppn,
						    	'npwp': npwp,
						    	'no_fj': no_fj,
						    	'posbiaya': posbiaya_,
						    	'total_dpp': total_dpp,
						    	'biaya_yg_dibyar': biaya_yg_dibyar,
						    	'keterangan': keterangan,
								'ppn_persen': ppn_persen
						    },
						    type: 'post',
						    beforeSend: function(){
						    	onload = showLoading();
						    },
						    success: function(output) {
								var msg = output.split("#");
						    	onload = hideLoading();
								bootbox.dialog({
								    closeButton : false,
									className : "resize",
								    message: msg[1],
								    title: "Pengajuan Voucher Payment",
								    buttons: {
								        main: {
								            label: "Ok",
								            className: "btn-sm btn-primary",
								            callback: function() {
												bootbox.dialog({
												    closeButton : false,
													className : "resize",
												    message: "Kirim notifikasi?",
												    title: "Pengajuan Voucher Payment",
												    buttons: {
													    confirm: {
													        label: 'Yes',
													        className: "btn-sm btn-primary",
													        callback: function () {
													        	onload = showLoading();
														    	var ajax1 = $.ajax({ 
																	url: 'email.php',
																	data: {'id': msg[2]},
																	success: function(result) {}
																});
																var nik = msg[4].split(";");
																var pesan = msg[3].split(";");
																for (var i = 0; i < nik.length; i++) {
																	if (nik[i]!='') {
																		var ajax2 = $.ajax({ 
																			url: "https://nasmoco.net/notifevopay/pushnotif_new.php?nik="+nik[i]+"&pesan="+pesan[i]+"&sumber=EVOPAY&nobukti="+msg[2]+"&sender=evopay&judul=Validasi Voucher Payment",
																			success: function(result2) {}  
																		});
																	}
																}

																var phone = msg[6].split(";");
																var text = msg[7].split(";");
																for (var i = 0; i < nik.length; i++) {
																	if (phone[i]!='') {
																		window.open('https://api.whatsapp.com/send?phone='+phone[i]+'&text='+text[i]);
																	}
																}
																$.when(ajax1).done(function(a1) {
																	if (a1) {
																		onload = hideLoading();
																		document.location.href="transaksi-pengajuan";
																	}
																});
													        }
													    },
													    cancel: {
													        label: 'No',
													        className: 'btn-sm btn-danger',
													        callback: function () {
															    document.location.href="transaksi-pengajuan";
															}
													    }
													}
												});
								            }
								        }
								    }
								});
							}
						});
					}
				}
			}
		});

		$('#Tipe, #KodeDealer').change(function(event){
			
			var tipe = $('#Tipe').val();
			var KodeDealer = $('#KodeDealer').val();
			
			onload = getNumber();
			if (tipe=='HUTANG') {
				$("#getForm").html('<div class="form-group"> <label class="col-sm-2 control-label">Divisi</label> <div class="col-sm-4"><select id="divisi" class="form-control"></select></div><label class="col-sm-2 control-label">Nama Atasan</label> <div class="col-sm-4"> <select id="IdAtasan" class="form-control" disabled> <option value="">- Pilih -</option> </select> </div></div><hr style="margin: 10px 0;"/><div class="form-group"> <label class="col-sm-2 control-label">Tipe Hutang</label> <div class="col-sm-4"><select id="tipehutang" class="form-control"></select></div></div><div class="form-group"> <label class="col-sm-2 control-label">No. Bukti</label> <div class="col-sm-4" id="f_NoBuktiPengajuan"> <div class="input-group"><span class="input-group-addon">VP</span> <input type="text" id="nobukti" class="form-control" value="XX/DD/MM/YY/999" readonly/></div></div><label class="col-sm-2 control-label">Tanggal Pengajuan</label> <div class="col-sm-4"><input type="date" id="tgl_pengajuan" class="form-control" value="'+nowdate()+'" readonly/></div></div><div class="form-group"> <label class="col-sm-2 control-label">Upload File</label> <div class="col-sm-4"> <div class="input-group"> <input type="text" id="upload_file" class="form-control" disabled=""/> <span class="input-group-addon" style="padding: 0; min-width: 0;"> <button type="button" style="padding: 2px 10px; border: 0;" onclick="getFile(1);"><i class="fa fa-eye"></i></button> </span> <span class="input-group-addon" style="padding: 0; min-width: 0;"> <span class="fileinput-button"> <span style="padding: 2px 10px; border: 0;"><i class="fa fa-camera"></i></span> <input id="ups-upload_file" type="file" name="files[]" accept=".xlsx,.xls,.jpg,.png,.pdf,.doc,.docx,.zip,.rar"/> </span> </span> </div></div><label class="col-sm-2 control-label">Upload Faktur Pajak</label> <div class="col-sm-4"> <div class="input-group"> <input type="text" id="upload_fp" class="form-control" disabled=""/> <span class="input-group-addon" style="padding: 0; min-width: 0;"> <button type="button" style="padding: 2px 10px; border: 0;" onclick="getFile(2);"><i class="fa fa-eye"></i></button> </span> <span class="input-group-addon" style="padding: 0; min-width: 0;"> <span class="fileinput-button"> <span style="padding: 2px 10px; border: 0;"><i class="fa fa-camera"></i></span> <input id="ups-upload_fp" type="file" name="files[]"  accept=".xlsx,.xls,.jpg,.png,.pdf,.doc,.docx,.zip,.rar"/> </span> </span> </div></div></div><hr style="margin: 10px 0;"/><div class="form-group"> <label class="col-sm-2 control-label">Kode Vendor</label> <div class="col-sm-4"> <div class="input-group"> <input type="text" name="kode_vendor" id="kode_vendor" class="form-control" disabled=""/> <span class="input-group-addon" style="padding: 0; min-width: 0;"> <button type="button" style="padding: 2px 10px; border: 0;" onclick="getVendor();"><i class="fa fa-search"></i></button> </span> </div></div><label class="col-sm-2 control-label">Nama Vendor</label> <div class="col-sm-4"><input type="text" name="namaVendor" id="namaVendor" class="form-control" autocomplete="off" readonly=""/></div></div><div class="form-group"><label class="col-sm-2 control-label">Metode Pembayaran</label> <div class="col-sm-4"> <select type="text" class="form-control" id="metode_bayar"> <option value="">- Pilih -</option> <option value="Transfer" selected>Transfer</option> <option value="Cash">Cash</option> </select> </div><label class="col-sm-2 control-label">Departement Terkait</label> <div class="col-sm-4"> <select type="text" class="form-control" id="deptterkait"> <option value="">- Pilih -</option> </select> </div></div><div class="form-group"> <label class="col-sm-2 control-label">Beneficary Account</label> <div class="col-sm-4"><input type="text" class="form-control" id="benificary_account" readonly=""/></div><label class="col-sm-2 control-label">Tanggal Bayar</label> <div class="col-sm-4"><input type="date" class="form-control" id="tgl_bayar"/></div></div><div class="form-group"> <label class="col-sm-2 control-label">Nama Bank Penerima</label> <div class="col-sm-4"><input type="text" class="form-control" id="nama_bank" readonly=""/></div><label class="col-sm-2 control-label">Nama Pemilik</label> <div class="col-sm-4"><input type="text" class="form-control" id="nama_pemilik" readonly=""/></div></div><div class="form-group"> <label class="col-sm-2 control-label">Email Penerima</label> <div class="col-sm-4"><input type="text" class="form-control" id="email_penerima" readonly=""/></div><label class="col-sm-2 control-label">Nama Alias</label> <div class="col-sm-4"><input type="text" class="form-control" id="nama_alias" readonly=""/></div></div><div class="form-group"> <label class="col-sm-2 control-label">Nama Bank Pengirim</label> <div class="col-sm-4"> <div class="input-group"> <input type="hidden" name="kode_bank_pengirim" id="kode_bank_pengirim" class="form-control" disabled="" value="11121625"/><input type="text" name="nama_bank_pengirim" id="nama_bank_pengirim" class="form-control" disabled="" value="CIMB Niaga New Ratna Motor 3"/> <span class="input-group-addon" style="padding: 0; min-width: 0;"> <button type="button" style="padding: 2px 10px; border: 0;" onclick="getBank();"><i class="fa fa-search"></i></button> </span> </div></div><label class="col-sm-2 control-label">Transfer From Account</label> <div class="col-sm-4"><input type="text" class="form-control" id="tf_from_account" readonly="" value="815388888500"/></div></div><hr style="margin: 10px 0;"/><div class="form-group"><label class="col-sm-6 control-label">No Tagihan</label> <div class="col-sm-6" align="right"><button type="button"  class="btn-success btn" onclick="formatfileImportHutang();">Format Excel</button><button type="button" id="importhutang" class="btn-info btn" onclick="formImportHutang();">Import Excel</button></div></div><div class="form-group"> <div class="col-sm-12"><table class="gethutang" style="display: none;"></table></div></div><div class="form-group"> <label class="col-sm-2 control-label">Nominal</label><div class="col-sm-4"><input type="text" class="form-control" id="realisasi_nominal" value="0" readonly/></div></div><div class="form-group"><label class="col-sm-2 control-label">Tipe Materai</label><div class="col-sm-4"><select name="tipe_materai" id="tipe_materai" class="form-control" onchange="getDppHtg();"><option value="I">Include</option><option value="E">Exclude</option><option value="N">Non Materai</option></select></div><label class="col-sm-2 control-label">Nominal Materai</label><div class="col-sm-4"><input type="text" class="form-control number" id="nominal_materai" readonly /></div></div><div class="form-group" id="div_materai"><label class="col-sm-2 control-label">Kode Akun Materai</label><div class="col-sm-4"><div class="input-group"> <input type="text" class="form-control" id="kodeAkunMaterai" readonly/> <span class="input-group-addon" style="padding: 0; min-width: 0;"> <button type="button" style="padding: 2px 10px; border: 0;" onclick="getAkunMaterai();"><i class="fa fa-search"></i></button> </span></div></div><label class="col-sm-2 control-label">Nama Akun Materai</label> <div class="col-sm-4"><input type="text" class="form-control" id="namaAkunMaterai" readonly/></div></div><div class="form-group"> <label class="col-sm-2 control-label">Kode Akun</label> <div class="col-sm-4"> <div class="input-group"> <input type="text" class="form-control" id="kodeAkun" readonly/> <span class="input-group-addon" style="padding: 0; min-width: 0;"> <button type="button" style="padding: 2px 10px; border: 0;" onclick="getAkun();"><i class="fa fa-search"></i></button> </span> </div></div><label class="col-sm-2 control-label">Nama Akun</label> <div class="col-sm-4"><input type="text" class="form-control" id="namaAkun" readonly/></div></div><div class="form-group"> <label class="col-sm-2 control-label">Type Ppn</label> <div class="col-sm-4"> <select type="text" class="form-control" id="tipeppn" onchange="getDppHtg();"> <option value="N">Non Ppn</option> <option value="I">Include</option> <option value="E">Exclude</option> </select> </div></div><div class="form-group"> <label class="col-sm-2 control-label">Dpp</label> <div class="col-sm-4"><input type="text" class="form-control number" id="dpp" value="0" /></div><label class="col-sm-2 control-label">Ppn<span class="ppn_persen" style="display:none"></span></label> <div class="col-sm-4"><input type="text" class="form-control number" id="ppn" value="0" /></div></div><div class="form-group"> <label class="col-sm-2 control-label">NPWP</label> <div class="col-sm-4"><input type="text" class="form-control" id="npwp" readonly/></div><label class="col-sm-2 control-label">No. Faktur Pajak</label> <div class="col-sm-4"><input type="text" class="form-control" id="no_fj" readonly/></div></div><span id="f_poshutang"></span><hr style="margin: 10px 0;"/><div class="form-group"> <label class="col-sm-2 control-label">Hutang setelah pajak</label> <div class="col-sm-4"><input type="text" class="form-control" id="htg_stl_pajak" value="0" readonly/></div><div class="col-sm-2"><button type="button" class="btn-default btn get_number" style="padding: 3px 10px;"><i class="fa fa-plus"></i> Add Pos Hutang</button></div><div class="col-sm-4"><input type="hidden" class="form-control" id="totDppHtg" value="0" readonly/></div></div><div class="form-group"> <label class="col-sm-2 control-label">Keterangan</label> <div class="col-sm-10"><input type="text" class="form-control" id="keterangan"/></div></div>'
				);
				
				$('#div_materai').hide();
				$('#kodeAkunMaterai').val('');
				$('#namaAkunMaterai').val('');
				
				
				$("#getForm2").html('');
				onload = addposhutang();
				$(".gethutang").flexigrid({
				    dataType : 'xml',
				    colModel : [ 
				        {
				            display : 'No Faktur',
				            name : 'TglPengajuan',
				            width : 120,
				            sortable : false,
				            align : 'left'
				        }, {
				            display : 'Tgl Faktur',
				            name : 'NoBuktiPengajuan',
				            width : 80,
				            sortable : false,
				            align : 'left'
				        }, {
				            display : 'Tgl Jth Tmpo',
				            name : '',
				            width : 80,
				            sortable : false,
				            align : 'left'
				        }, {
				            display : 'Keterangan',
				            name : 'KodeAkunBank',
				            width : 530,
				            sortable : false,
				            align : 'left'
				        }, {
				            display : 'Jumlah',
				            name : 'namaBank',
				            width : 100,
				            sortable : false,
				            align : 'left'
				        }, {
				            display : 'Byr',
				            name : 'NoRekening',
				            width : 30,
				            sortable : false,
				            align : 'center'
				        }, {
				            display : 'Jml Yg dibayar',
				            name : 'NamaRekening',
				            width : 100,
				            sortable : false,
				            align : 'left'
				        }
				    ],
				    showToggleBtn : false,
				    width : 'auto',
				    height : '150'
				});
				$.ajax({ 
					url: 'system/control/pengajuan.php',
					data: { action:'getTipehutang', 'KodeDealer': KodeDealer},
					type: 'post',
					beforeSend: function(){
						onload = showLoading();
					},
					success: function(output) {
						onload = hideLoading();
						$("#tipehutang").html(output);
					}
				});

				var div = $("#div").val();
				$.ajax({ 
					url: 'system/control/pengajuan.php',
					data: { action:'getDivisi', 'kodedealer' : KodeDealer, 'div' : div },
					type: 'post',
					beforeSend: function(){
						onload = showLoading();
					},
					success: function(output) {
						onload = hideLoading();
						$("#divisi").html(output);
					}
				});
				
				//$('#divisi').on('change',function(){
					var divisi = $('#divisi').val();
					var IdUser = $("#IdUser").val();
					$.ajax({ 
						url: 'system/control/pengajuan.php',
						data: { action:'getAtasan', 'kodedealer' : KodeDealer, 'divisi' : divisi, 'IdUser' : IdUser },
						type: 'post',
						beforeSend: function(){
							onload = showLoading();
						},
						success: function(output) {
							onload = hideLoading();
							$('#IdAtasan').html(output);
							$('#IdAtasan').removeAttr('disabled');
						}
					});
				//});

				var deptterkait = $("#deptterkait").val();
				$.ajax({ 
					url: 'system/control/pengajuan.php',
					data: { action:'getDepartementTerkait', 'kodedealer' : KodeDealer, 'div' : div },
					type: 'post',
					beforeSend: function(){
						onload = showLoading();
					},
					success: function(output) {
						onload = hideLoading();
						$("#deptterkait").html(output);
					}
				});
				
				
				$.ajax({ 
					url: 'system/control/pengajuan.php',
					data: { action:'getMaterai' },
					type: 'post',
					beforeSend: function(){
						onload = showLoading();
					},
					success: function(output) {
						onload = hideLoading();
						$("#nominal_materai").val(output);
					}
				});
				
				$('#tipe_materai').on('change',function(){
					var tipe_materai = $('#tipe_materai').val();
					var nominal_materai = $('#nominal_materai').val();
					//alert(tipe_materai);
					$.ajax({ 
						url: 'system/control/pengajuan.php',
						data: { action:'getMaterai' },
						type: 'post',
						beforeSend: function(){
							onload = showLoading();
						},
						success: function(output) {
							onload = hideLoading();
							$("#nominal_materai").val(output);
							
							$('#div_materai').hide();
							$('#kodeAkunMaterai').val('');
							$('#namaAkunMaterai').val('');
							
							
							if (tipe_materai=="I") { // include
								$('#nominal_materai').val(addCommas(output));
								htg_yg_dibyar();
								
							} else if (tipe_materai=="E") { // exclude
								$('#div_materai').show();
								$('#nominal_materai').val(addCommas(output));
								htg_yg_dibyar();
							
							} else { // non materai
								$('#nominal_materai').val("0");
								htg_yg_dibyar();
							}
							
						}
					});
					
				});
				
				/*$('#tipehutang').on('change',function(){
					var tipehutang = $("#tipehutang").val();
					var kode_vendor = $("#kode_vendor").val();
					$("#kode_vendor").val('');
					$("#namaVendor").val('');
					$("#metode_bayar").val('');
					$("#benificary_account").val('');
					$("#nama_bank").val('');
					$("#nama_pemilik").val('');
					$("#email_penerima").val('');
					$("#nama_alias").val('');
					$("#kode_bank_pengirim").val('');
					$("#nama_bank_pengirim").val('');
					$("#tf_from_account").val('');
					$('.gethutang').flexOptions({
						url:'system/data/gethutang.php', 
						newp: 1,
						params:[
							{ name:'KodeLgn', value: kode_vendor },
							{ name:'KodeDealer', value: KodeDealer },
							{ name:'tipehutang', value: tipehutang }
						]
					}).flexReload();
				});*/
				
				$('#tipehutang').on('change',function(){
					var tipehutang = $("#tipehutang").val();
					var kode_vendor = $("#kode_vendor").val();
					var KodeDealer = $("#KodeDealer").val();
					$("#kode_vendor").val('');
					$("#namaVendor").val('');
					$("#metode_bayar").val('');
					$("#benificary_account").val('');
					$("#nama_bank").val('');
					$("#nama_pemilik").val('');
					$("#email_penerima").val('');
					$("#nama_alias").val('');
					$("#kode_bank_pengirim").val('');
					$("#nama_bank_pengirim").val('');
					$("#tf_from_account").val('');
					
					
					/*$('.gethutang').flexOptions({
						url:'system/data/gethutang.php', 
						newp: 1,
						params:[
							{ name:'KodeLgn', value: '' },
							{ name:'KodeDealer', value: KodeDealer },
							{ name:'tipehutang', value: '' }
						]
					}).flexReload();
					*/
					$('.gethutang').flexOptions({
						url:'system/data/gethutang.php', 
						newp: 1,
						params:[
							{ name:'KodeLgn', value: kode_vendor },
							{ name:'KodeDealer', value: KodeDealer },
							{ name:'tipehutang', value: tipehutang }
						]
					}).flexReload();
					
					
					$("#getForm2").html('');
					
					
					var count = $("input[name='posbiaya[]']").length;
					for (var y=0;y<=count;y++) {
						delpos(y);
						delposHtg(y);
					}
					$("#deptterkait").val('');
					$("#realisasi_nominal").val('');
					$("#kodeAkun").val('');
					$("#namaAkun").val('');
					$("#dpp").val('');
					$("#npwp").val('');
					$("#ppn").val('');
					$("#no_fj").val('');
					$("#htg_stl_pajak").val('0');
					$("#keterangan").val('');
					
					$.ajax({ 
						url: 'system/control/pengajuan.php',
						data: { action:'getTipehutangDept', 'KodeDealer': KodeDealer, 'nama': $('#tipehutang').val()},
						type: 'post',
						beforeSend: function(){
							//onload = showLoading();
							//$("#deptterkait").children().remove().end();
							
							$("#deptterkait").prop( "disabled", false );
						},
						success: function(output) {
							//onload = hideLoading();
							//alert(output);
							if (output!='') {
								$("#deptterkait").val(output).change();
								$("#deptterkait").prop( "disabled", true );
							}
						}
					});
					
					
				});
		
				$('.get_number').on('click',function(){
					onload = addposhutang();
					$('.number').number( true, 0 );
				});
				$('.number').number( true, 0 );
				
				getPpn();
				
				
				$("#dpp11, #ppn11").change(function(){
						
					var htg_stl_pajak = document.getElementById('htg_stl_pajak').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
					var ppn_ = document.getElementById('ppn').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
					var dpp_ = document.getElementById('dpp').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
					
					var total = parseInt(ppn_) + parseInt(dpp_);
					if (total!=htg_stl_pajak) {
						onload = needValue('Pengajuan Voucher Payment','Biaya bayar tidak sm dengan total DPP + PPN!');
						
						var nominal = document.getElementById('realisasi_nominal').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');						
						var nominal_materai = document.getElementById('nominal_materai').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
						var tipe_materai = $('#tipe_materai').val();
						var nom = 0;
						var tipeppn = $("#tipeppn").val();
						
						if (tipeppn=='N') {
							if (tipe_materai=="I") { // include
								nom = parseInt(nominal) - parseInt(nominal_materai);
							} else if (tipe_materai=="E") { // exclude
								nom = nominal;
							} else { // non materai
								nom = nominal;
								//htg_yg_dibyar = parseInt(nom) - parseInt(totPhh);
							}
							
							var dpp = nom;
							var ppn = 0;
							
						} else if (tipeppn=='E') {
							if (tipe_materai=="I") { // include
								nom = parseInt(nominal) - parseInt(nominal_materai);
							} else if (tipe_materai=="E") { // exclude
								nom = nominal;
							} else { // non materai
								nom = nominal;
								//htg_yg_dibyar = parseInt(nom) - parseInt(totPhh);
							}
							
							var dpp  = nom;
							//var ppn = Math.round((10/100)*nom);			
							var persen_ppn = $("#ppn_persen").val();			
							var ppn = Math.round((parseInt(persen_ppn)/100)*nom);
																
						} else if (tipeppn=='I') {
							
							if (tipe_materai=="I") { // include
								nom = parseInt(nominal) - parseInt(nominal_materai);
							} else if (tipe_materai=="E") { // exclude
								nom = nominal;
							} else { // non materai
								nom = nominal;
								//htg_yg_dibyar = parseInt(nom) - parseInt(totPhh);
							}
							
							//var dpp  = Math.round((10/11)*nom);
							//var ppn = Math.round((1/11)*nom);
							
							var persen_ppn = $("#ppn_persen").val();
							var dpp = Math.round(parseInt(nom) * (100 / (100 + parseInt(persen_ppn))));
							var ppn = Math.round(parseInt(nom) * (parseInt(persen_ppn) / (100 + parseInt(persen_ppn))));
							
						}
						$("#dpp").val(Math.round(dpp));
						$("#ppn").val(Math.round(ppn));
						//$("#dpp").val(addCommas(Math.round(dpp)));
						//$("#ppn").val(addCommas(Math.round(ppn)));
						
					}
				});
					
				
			} else if (tipe=='BIAYA') {
				var getForm = '<div class="form-group" style="padding-top: 3px;"> <label class="col-sm-2 control-label">Status</label> <div class="col-sm-4"> <select type="text" class="form-control" id="status" > <option value="">- Pilih -</option> <option value="New">New</option> <option value="Reject">Reject</option> </select> </div><label class="col-sm-2 control-label">No. Bukti</label> <div class="col-sm-4" id="f_NoBuktiPengajuan"> <div class="input-group"><span class="input-group-addon">VP</span> <input type="text" id="nobukti" class="form-control" value="XX/DD/MM/YY/999" readonly/></div></div><div class="col-sm-4" id="f_nobuktiReject" style="display: none;"><select type="text" class="form-control" id="nobukti_reject"></select></div></div><div class="form-group" id="f_alasanreject" style="display: none;"> <label class="col-sm-2 control-label">Alasan Reject</label> <div class="col-sm-10"><input type="text" class="form-control" id="alasanreject" readonly/></div></div><div class="form-group"> <label class="col-sm-2 control-label">Divisi</label> <div class="col-sm-4"><select id="divisi" class="form-control"></select></div><label class="col-sm-2 control-label">Nama Atasan</label> <div class="col-sm-4"> <select id="IdAtasan" class="form-control" disabled> <option value="">- Pilih -</option> </select> </div></div><hr style="margin: 10px 0;"/>';

				var getForm2 = '<div class="form-group"> <label class="col-sm-2 control-label">Tanggal Pengajuan</label> <div class="col-sm-4"><input type="date" id="tgl_pengajuan" class="form-control" value="'+nowdate()+'" readonly/></div><label class="col-sm-2 control-label">Tanggal Bayar</label> <div class="col-sm-4"><input type="date" class="form-control" id="tgl_bayar"/></div></div><div class="form-group"> <label class="col-sm-2 control-label">Upload File</label> <div class="col-sm-4"> <div class="input-group"> <input type="text" id="upload_file" class="form-control" disabled/> <span class="input-group-addon" style="padding: 0;min-width: 0;"><button type="button" style="padding: 2px 10px;border: 0;" onclick="getFile(1);"><i class="fa fa-eye"></i></button></span><span class="input-group-addon" style="padding: 0; min-width: 0;"> <span class="fileinput-button"> <span style="padding: 2px 10px; border: 0;"><i class="fa fa-camera"></i></span> <input id="ups-upload_file" type="file" name="files[]"  accept=".xlsx,.xls,.jpg,.png,.pdf,.doc,.docx,.zip,.rar"/> </span> </span> </div></div><label class="col-sm-2 control-label">Upload Faktur Pajak</label> <div class="col-sm-4"> <div class="input-group"> <input type="text" id="upload_fp" class="form-control" disabled/><span class="input-group-addon" style="padding: 0;min-width: 0;"><button type="button" style="padding: 2px 10px;border: 0;" onclick="getFile(2);"><i class="fa fa-eye"></i></button></span> <span class="input-group-addon" style="padding: 0; min-width: 0;"> <span class="fileinput-button"> <span style="padding: 2px 10px; border: 0;"><i class="fa fa-camera"></i></span> <input id="ups-upload_fp" type="file" name="files[]"  accept=".xlsx,.xls,.jpg,.png,.pdf,.doc,.docx,.zip,.rar"/> </span> </span> </div></div></div><hr style="margin: 10px 0;"/><div class="form-group"> <label class="col-sm-2 control-label">Kode Vendor</label> <div class="col-sm-4"> <div class="input-group"> <input type="text" name="kode_vendor" id="kode_vendor" class="form-control" disabled/> \ <span class="input-group-addon" style="padding: 0; min-width: 0;"> <button type="button" style="padding: 2px 10px; border: 0;" onclick="getVendor();"><i class="fa fa-search"></i></button> </span> </div></div><label class="col-sm-2 control-label">Nama Vendor</label> <div class="col-sm-4"><input type="text" name="namaVendor" id="namaVendor" class="form-control" autocomplete="off" readonly/></div></div><div class="form-group"> <label class="col-sm-2 control-label">Metode Pembayaran</label> <div class="col-sm-4"> <select type="text" class="form-control" id="metode_bayar"> <option value="">- Pilih -</option> <option value="Transfer" selected>Transfer</option> <option value="Cash">Cash</option> <option value="Pety Cash">Pety Cash</option> </select> </div><label class="col-sm-2 control-label">Beneficary Account</label> <div class="col-sm-4"><input type="text" class="form-control" id="benificary_account" readonly/></div></div><div class="form-group"> <label class="col-sm-2 control-label">Nama Bank Penerima</label> <div class="col-sm-4"><input type="text" class="form-control" id="nama_bank" readonly/></div><label class="col-sm-2 control-label">Nama Pemilik</label> <div class="col-sm-4"><input type="text" class="form-control" id="nama_pemilik" readonly/></div></div><div class="form-group"> <label class="col-sm-2 control-label">Email Penerima</label> <div class="col-sm-4"><input type="text" class="form-control" id="email_penerima" readonly/></div><label class="col-sm-2 control-label">Nama Alias</label> <div class="col-sm-4"><input type="text" class="form-control" id="nama_alias" readonly/></div></div><div class="form-group"> <label class="col-sm-2 control-label">Nama Bank Pengirim</label> <div class="col-sm-4"> <div class="input-group"> <input type="hidden" name="kode_bank_pengirim" id="kode_bank_pengirim" class="form-control" disabled="" value="11121625"><input type="text" name="nama_bank_pengirim" id="nama_bank_pengirim" class="form-control" disabled value="CIMB Niaga New Ratna Motor 3"/> \ <span class="input-group-addon" style="padding: 0; min-width: 0;"> <button type="button" style="padding: 2px 10px; border: 0;" onclick="getBank();"><i class="fa fa-search"></i></button> </span> </div></div><label class="col-sm-2 control-label">Transfer From Account</label> <div class="col-sm-4"><input type="text" class="form-control" id="tf_from_account" readonly value="815388888500"/></div></div><div class="form-group"> <label class="col-sm-2 control-label">Realisasi Nominal</label> <div class="col-sm-4"><input type="text" class="form-control number" id="realisasi_nominal" value="0"/></div><label class="col-sm-2 control-label">Departement Terkait</label> <div class="col-sm-4"> <select type="text" class="form-control" id="deptterkait"> <option value="">- Pilih -</option> </select> </div></div><hr style="margin: 10px 0;"/><div class="form-group"> <label class="col-sm-2 control-label"> <input type="checkbox" id="is_ppn" value="0" disabled/> PPN <span class="ppn_persen" style="display:none"></span><input type="hidden" id="tipeppn" value=""/></label> <div class="col-sm-4"> <div class="input-group"> <span class="input-group-addon">Dpp</span> <input type="text" class="form-control number" id="dpp" value="0" /> <span class="input-group-addon">Ppn</span> <input type="text" class="form-control number" id="ppn" value="0" /> </div></div></div><div class="form-group"> <label class="col-sm-2 control-label">NPWP</label> <div class="col-sm-4"><input type="text" class="form-control" id="npwp" readonly/></div><label class="col-sm-2 control-label">No. Faktur Pajak</label> <div class="col-sm-4"><input type="text" class="form-control" id="no_fj" readonly/></div></div><span id="f_posbiaya"></span><div class="form-group"> <label class="col-sm-3 control-label">Total Dpp</label> <div class="col-sm-3"><input type="text" class="form-control" id="total_dpp" value="0" readonly/></div><div class="col-sm-3"><button type="button" class="btn-default btn get_number" style="padding: 3px 10px;"><i class="fa fa-plus"></i> Add Pos Biaya</button></div></div><hr style="margin: 10px 0;"/><div class="form-group"> <label class="col-sm-2 control-label">Biaya yg harus dibayar</label> <div class="col-sm-4"><input type="text" class="form-control" id="biaya_yg_dibyar" value="0" readonly/></div></div><div class="form-group"> <label class="col-sm-2 control-label">Keterangan</label> <div class="col-sm-10"><input type="text" class="form-control" id="keterangan"/></div></div>';
				$("#getForm").html(getForm);
				// onload = addpos();
				$("#status").change(function(){
					var status = $('#status').val();
					if (status=='Reject') {
						$("#f_NoBuktiPengajuan").css('display','none');
						$("#f_nobuktiReject").css('display','block');
						$("#f_alasanreject").css('display','block');
						onload = getNumberR();
						$("#getForm2").html(getForm2);
					} else if (status=='New') {
						$("#f_NoBuktiPengajuan").css('display','block');
						$("#f_nobuktiReject").css('display','none');
						$("#f_alasanreject").css('display','none');
						$("#getForm2").html(getForm2);
						onload = addpos();
					} else {
						$("#getForm2").html('');
					}

					getPpn();
					
					$('#is_ppn').click(function(event){
						var nom = document.getElementById('realisasi_nominal').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
						var tipeppn = $("#tipeppn").val();
						
						
						if ($('#is_ppn').prop("checked")) {
							$('#is_ppn').val('1');
							/*if(tipeppn=='I'){
								dpp  = (10/11)*parseInt(nom);
			    				ppn = (1/11)*parseInt(nom);
							} else if(tipeppn=='E') {
								dpp  = parseInt(nom);
			    				ppn = (10/100)*parseInt(nom);
							}*/
							//dpp  = (10/11)*parseInt(nom);
			    			//ppn = (1/11)*parseInt(nom);
							
							//nominal  *(100/(100+ppn))
							//nominal *(ppn/(100+ppn))
							var persen_ppn = $("#ppn_persen").val();
							dpp = Math.round(parseInt(nom) * (100 / (100 + parseInt(persen_ppn))));
							ppn = Math.round(parseInt(nom) * (parseInt(persen_ppn) / (100 + parseInt(persen_ppn))));
							
			    			$("#no_fj").removeAttr('readonly');
							var npwp = $("#npwp").val();
							if (npwp=="") {
								onload = needValue('Pengajuan Voucher Payment','Harap Isi NPWP Di Master Supplier!');
								$("#no_fj").prop('readonly','readonly');
								$('#is_ppn').val('0');
								return false;
							}
						} else {
							$('#is_ppn').val('0');
							dpp = parseInt(nom);
							ppn = 0;
							$("#no_fj").prop('readonly','readonly');
						}
						//alert("1");
						//$("#dpp").val(addCommas(Math.round(dpp)));
						//$("#ppn").val(addCommas(Math.round(ppn)));
						$("#dpp").val(Math.round(dpp));
						$("#ppn").val(Math.round(ppn));
												
						var con = $("input[name='posbiaya[]']").length;
						for (var i = 1; i <= con; i++) {
							onload = getPph(i);
						}
						onload = biaya_yg_dibyar();
					});
					
					$("#realisasi_nominal").change(function(){
						var nom = document.getElementById('realisasi_nominal').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
						if (nom=='') { nom = 0; $("#realisasi_nominal").val('0') }
						if ($('#is_ppn').prop("checked")) {
							var tipeppn = $("#tipeppn").val();
							var persen_ppn = $("#ppn_persen").val();
							
							/*if(tipeppn=='I'){
								var dpp = Math.round(parseInt(nom) * (100 / (100 + parseInt(persen_ppn))));
								var ppn = Math.round(parseInt(nom) * (parseInt(persen_ppn) / (100 + parseInt(persen_ppn))));
							} else if(tipeppn=='E') {
								var dpp  = parseInt(nom);
			    				var ppn = Math.round((parseInt(persen_ppn)/100)*parseInt(nom));
							}*/
							var dpp = Math.round(parseInt(nom) * (100 / (100 + parseInt(persen_ppn))));
							var ppn = Math.round(parseInt(nom) * (parseInt(persen_ppn) / (100 + parseInt(persen_ppn))));
					
							
							/*if(tipeppn=='I'){
								var dpp  = (10/11)*parseInt(nom);
			    				var ppn = (1/11)*parseInt(nom);
							} else if(tipeppn=='E') {
								var dpp  = parseInt(nom);
			    				var ppn = (10/100)*parseInt(nom);
							}*/
							$("#ppn").removeAttr('readonly');
						} else {
							$("#ppn").attr('readonly','readonly');
							var dpp = parseInt(nom);
							var ppn = 0;
						}
						//alert("2");
						$("#dpp").val(Math.round(dpp));
						$("#ppn").val(Math.round(ppn));
						//$("#dpp").val(addCommas(Math.round(dpp)));
						//$("#ppn").val(addCommas(Math.round(ppn)));
						onload = biaya_yg_dibyar();
					});
					
					$("#dpp11, #ppn11").change(function(){
						
						var biaya_yg_dibyar = document.getElementById('biaya_yg_dibyar').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
						var ppn = document.getElementById('ppn').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
						var dpp = document.getElementById('dpp').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
						
						var total = parseInt(ppn) + parseInt(dpp);
						if (total!=biaya_yg_dibyar) {
							onload = needValue('Pengajuan Voucher Payment','Biaya bayar tidak sm dengan total DPP + PPN!');
							
							var nom = document.getElementById('realisasi_nominal').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
							if (nom=='') { nom = 0; $("#realisasi_nominal").val('0') }
							if ($('#is_ppn').prop("checked")) {
								var tipeppn = $("#tipeppn").val();
								
								//nominal  *(100/(100+ppn))
								//nominal *(ppn/(100+ppn))
								var persen_ppn = $("#ppn_persen").val();
								
								if(tipeppn=='I'){
									var dpp = Math.round(parseInt(nom) * (100 / (100 + parseInt(persen_ppn))));
									var ppn = Math.round(parseInt(nom) * (parseInt(persen_ppn) / (100 + parseInt(persen_ppn))));
								} else if(tipeppn=='E') {
									var dpp  = parseInt(nom);
									var ppn = Math.round((parseInt(persen_ppn)/100)*parseInt(nom));
								}
								
								/*if(tipeppn=='I'){
									var dpp  = (10/11)*parseInt(nom);
									var ppn = (1/11)*parseInt(nom);
								} else if(tipeppn=='E') {
									var dpp  = parseInt(nom);
									var ppn = (10/100)*parseInt(nom);
								}*/
							} else {
								var dpp = parseInt(nom);
								var ppn = 0;
							}
							//alert("2");
							$("#dpp").val(Math.round(dpp));
							$("#ppn").val(Math.round(ppn));
							//$("#dpp").val(addCommas(Math.round(dpp)));
							//$("#ppn").val(addCommas(Math.round(ppn)));
							onload = biaya_yg_dibyar();
							
						}
					});
					
					
					$("#nobukti_reject").change(function(){
						var nobukti = $("#nobukti_reject").val();
						var IdUser = $("#IdUser").val();
						$.ajax({ 
							url: 'system/control/pengajuan.php',
							data: { action:'getReject', 'nobukti': nobukti, 'kodedealer' : KodeDealer, 'IdUser' : IdUser },
							type: 'post',
							beforeSend: function(){
								onload = showLoading();
							},
							success: function(output) {
								onload = hideLoading();
								var r = output.split("_cn_");
								$("#tgl_pengajuan").val(r[0]);
								$("#upload_file").val(r[1]);
								$("#upload_fp").val(r[2]);
								$("#kode_vendor").val(r[3]);
								$("#namaVendor").val(r[4]);
								$("#metode_bayar").val(r[5]);
								$("#benificary_account").val(r[6]);
								$("#tgl_bayar").val(r[7]);
								$("#nama_bank").val(r[8]);
								$("#nama_pemilik").val(r[9]);
								$("#email_penerima").val(r[10]);
								$("#nama_alias").val(r[11]);
								$("#nama_bank_pengirim").val(r[12]);
								$("#tf_from_account").val(r[13]);
								$("#realisasi_nominal").val(r[14]);
								$("#is_ppn").val(r[15]); 
								if (r[15]=='1') { $('#is_ppn').prop("checked", true); } else { $('#is_ppn').prop("checked", false) }
								$("#dpp").val(r[16]);
								$("#ppn").val(r[17]);
								$("#npwp").val(r[18]);
								$("#no_fj").val(r[19]);
								$("#total_dpp").val(r[20]);
								$("#biaya_yg_dibyar").val(r[21]);
								$("#keterangan").val(r[22]);
								$("#alasanreject").val(r[23]);
								$("#divisi").val(r[24]);
								$('#IdAtasan').html(r[26]);
								$('#IdAtasan').removeAttr('disabled');
								$("#IdAtasan").val(r[25]);
								$("#f_posbiaya").html(r[27]);
								$("#kode_bank_pengirim").val(r[28]);
								$('.number').number( true, 0 );
							}
						});
					});

					var deptterkait = $("#deptterkait").val();
					$.ajax({ 
						url: 'system/control/pengajuan.php',
						data: { action:'getDepartementTerkait', 'kodedealer' : KodeDealer, 'div' : div },
						type: 'post',
						beforeSend: function(){
							onload = showLoading();
						},
						success: function(output) {
							onload = hideLoading();
							$("#deptterkait").html(output);
						}
					});
					
					//$('#divisi').on('change',function(){
						var divisi = $('#divisi').val();
						var IdUser = $("#IdUser").val();
						$.ajax({ 
							url: 'system/control/pengajuan.php',
							data: { action:'getAtasan', 'kodedealer' : KodeDealer, 'divisi' : divisi, 'IdUser' : IdUser },
							type: 'post',
							beforeSend: function(){
								onload = showLoading();
							},
							success: function(output) {
								onload = hideLoading();
								$('#IdAtasan').html(output);
								$('#IdAtasan').removeAttr('disabled');
							}
						});
					//});

					$("input[type='file']").change(function(){
						var test = this.id; var id = test.split("-");
						var fd = new FormData();
					    var files = $('#'+test)[0].files[0];
						var nobukti = $('#nobukti').val();
					    fd.append('file',files);
					    fd.append('nobukti',nobukti);
						//alert(nobukti);
					    $.ajax({
					        url:'system/control/upload.php',
					        type:'post',
					        data:fd,
					        contentType: false,
					        processData: false,
					        beforeSend: function(){
								onload = showLoading();
							},
					        success:function(response){
					        	onload = hideLoading();
					            if(response != 0){
					            	$("#"+id[1]).val(response);
					                onload = needValue('Pengajuan Voucher Payment','File uploaded!');
					            } else{
					                onload = needValue('Pengajuan Voucher Payment','File not uploaded!');
					            }
					        }
					    });
					});
					$('.get_number').on('click',function(){
						onload = addpos();
						$('.number').number( true, 0 );
					});
					$('.number').number( true, 0 );
				});
				var div = $("#div").val();
				$.ajax({ 
					url: 'system/control/pengajuan.php',
					data: { action:'getDivisi', 'kodedealer' : KodeDealer, 'div' : div },
					type: 'post',
					beforeSend: function(){
						onload = showLoading();
					},
					success: function(output) {
						onload = hideLoading();
						$("#divisi").html(output);
					}
				});
				
				var divisi = $('#divisi').val();
				var IdUser = $("#IdUser").val();
				$.ajax({ 
					url: 'system/control/pengajuan.php',
					data: { action:'getAtasan', 'kodedealer' : KodeDealer, 'divisi' : divisi, 'IdUser' : IdUser },
					type: 'post',
					beforeSend: function(){
						onload = showLoading();
					},
					success: function(output) {
						onload = hideLoading();
						$('#IdAtasan').html(output);
						$('#IdAtasan').removeAttr('disabled');
					}
				});
				
			} else {
				$("#getForm").html('');
			}
			$("input[type='file']").change(function(){
				var test = this.id; var id = test.split("-");
				var fd = new FormData();
			    var files = $('#'+test)[0].files[0];
			    //fd.append('file',files);
				var nobukti = $('#nobukti').val();
				fd.append('file',files);
			    fd.append('nobukti',nobukti);
				//alert(nobukti);
						
			    $.ajax({
			        url:'system/control/upload.php',
			        type:'post',
			        data:fd,
			        contentType: false,
			        processData: false,
			        beforeSend: function(){
						onload = showLoading();
					},
					success:function(response){
						onload = hideLoading();
			            if(response != 0){
			            	$("#"+id[1]).val(response);
			                onload = needValue('Pengajuan Voucher Payment','File uploaded!');
			            } else{
			                onload = needValue('Pengajuan Voucher Payment','File not uploaded!');
			            }
			        }
			    });
			});
		});
		
		<?php if ($modify=='edit') { 			
			if ($vw['tipe']=="HUTANG") { ?>
				$('.get_number').on('click',function(){
					onload = addposhutang();
					$('.number').number( true, 0 );
				});
				$('.number').number( true, 0 );
				
				getPpn();
				$("#dpp11, #ppn11").change(function(){
						
					var htg_stl_pajak = document.getElementById('htg_stl_pajak').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
					var ppn_ = document.getElementById('ppn').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
					var dpp_ = document.getElementById('dpp').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
					
					var total = parseInt(ppn_) + parseInt(dpp_);
					if (total!=htg_stl_pajak) {
						onload = needValue('Pengajuan Voucher Payment','Biaya bayar tidak sm dengan total DPP + PPN!');
						
						var nominal = document.getElementById('realisasi_nominal').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');						
						var nominal_materai = document.getElementById('nominal_materai').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
						var tipe_materai = $('#tipe_materai').val();
						var nom = 0;
						var tipeppn = $("#tipeppn").val();
						
						if (tipeppn=='N') {
							if (tipe_materai=="I") { // include
								nom = parseInt(nominal) - parseInt(nominal_materai);
							} else if (tipe_materai=="E") { // exclude
								nom = nominal;
							} else { // non materai
								nom = nominal;
								//htg_yg_dibyar = parseInt(nom) - parseInt(totPhh);
							}
							
							var dpp = nom;
							var ppn = 0;
							
						} else if (tipeppn=='E') {
							if (tipe_materai=="I") { // include
								nom = parseInt(nominal) - parseInt(nominal_materai);
							} else if (tipe_materai=="E") { // exclude
								nom = nominal;
							} else { // non materai
								nom = nominal;
								//htg_yg_dibyar = parseInt(nom) - parseInt(totPhh);
							}
							
							var dpp  = nom;
							//var ppn = Math.round((10/100)*nom);			
							var persen_ppn = $("#ppn_persen").val();			
							var ppn = Math.round((parseInt(persen_ppn)/100)*nom);
																
						} else if (tipeppn=='I') {
							
							if (tipe_materai=="I") { // include
								nom = parseInt(nominal) - parseInt(nominal_materai);
							} else if (tipe_materai=="E") { // exclude
								nom = nominal;
							} else { // non materai
								nom = nominal;
								//htg_yg_dibyar = parseInt(nom) - parseInt(totPhh);
							}
							
							//var dpp  = Math.round((10/11)*nom);
							//var ppn = Math.round((1/11)*nom);
							
							var persen_ppn = $("#ppn_persen").val();
							var dpp = Math.round(parseInt(nom) * (100 / (100 + parseInt(persen_ppn))));
							var ppn = Math.round(parseInt(nom) * (parseInt(persen_ppn) / (100 + parseInt(persen_ppn))));
							
						}
						$("#dpp").val(Math.round(dpp));
						$("#ppn").val(Math.round(ppn));
						//$("#dpp").val(addCommas(Math.round(dpp)));
						//$("#ppn").val(addCommas(Math.round(ppn)));
						
					}
				});
					
		<?php } else if ($vw['tipe']=="BIAYA") {  ?>
				/*$('.get_number').on('click',function(){
					onload = addpos();
					$('.number').number( true, 0 );
				});
				$('.number').number( true, 0 );
				*/
		<?php } ?>
					
		$("#realisasi_nominal").change(function(){
			var nom = document.getElementById('realisasi_nominal').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
			if (nom=='') { nom = 0; $("#realisasi_nominal").val('0') }
			if ($('#is_ppn').prop("checked")) {
				var tipeppn = $("#tipeppn").val();							
				var persen_ppn = $("#ppn_persen").val();
				
				$("#ppn").removeAttr('readonly');
				var dpp = Math.round(parseInt(nom) * (100 / (100 + parseInt(persen_ppn))));
				var ppn = Math.round(parseInt(nom) * (parseInt(persen_ppn) / (100 + parseInt(persen_ppn))));
		
			} else {
				$("#ppn").attr('readonly','readonly');
				var dpp = parseInt(nom);
				var ppn = 0;
			}
			//alert("3");
			$("#dpp").val(Math.round(dpp));
			$("#ppn").val(Math.round(ppn));
			//$("#dpp").val(addCommas(Math.round(dpp)));
			//$("#ppn").val(addCommas(Math.round(ppn)));
			onload = biaya_yg_dibyar();
		});
		<?php } ?>		
	});
	
	function addpos(){	
		var keterangan = $("#keterangan").val();
		var count = $("input[name='posbiaya[]']").length;
		var y = count + 1;
		if (count>0) {
			$("#f_posbiaya").append('<div class="form-group" id="formPos_'+y+'"> <div class="col-sm-3"> <label class="control-label">Pos Biaya</label> <div class="input-group"> <input type="hidden" name="posbiaya[]" id="kodeAkun_'+y+'"/> <input type="text" class="form-control" id="ketAkun_'+y+'" readonly/> <span class="input-group-addon" style="padding: 0; min-width: 0;"> <button type="button" style="padding: 2px 10px; border: 0;" onclick="getAkun('+y+');"><i class="fa fa-search"></i></button> </span> </div></div><div class="col-sm-3"><label class="control-label">Nominal</label> <input type="text" class="form-control number" id="nominal_'+y+'" value="0" /></div><div class="col-sm-2"> <label class="control-label">Tarif Pajak</label> <select type="text" class="form-control" id="trfPajak_'+y+'" disabled></select> <input type="hidden" id="jns_pph_'+y+'" value="Non Pph"/> <input type="hidden" id="tarif_persen_'+y+'" value="0"/><input type="hidden" id="akun_pph_'+y+'"/> </div><div class="col-sm-2"> <label class="control-label">Nilai Pph</label> <div class="input-group"> <input type="text" class="form-control" id="nilaiPph_'+y+'" value="0" readonly/> <span class="input-group-addon" style="padding: 0; min-width: 0;"> <button type="button" style="padding: 2px 10px; border: 0;" onclick="delpos('+y+');"> <i class="fa fa-minus"></i> </button> </span> </div></div><div class="col-sm-2"> <label class="control-label">Keterangan Biaya</label><input type="text" class="form-control" id="keteranganAkun_'+y+'" maxlength="200" value="'+keterangan+'"/></div></div>');
			
		} else {
			$("#f_posbiaya").append('<div class="form-group" id="formPos_'+y+'"> <div class="col-sm-3"> <label class="control-label">Pos Biaya</label> <div class="input-group"> <input type="hidden" name="posbiaya[]" id="kodeAkun_'+y+'"/> <input type="text" class="form-control" id="ketAkun_'+y+'" readonly/> <span class="input-group-addon" style="padding: 0; min-width: 0;"> <button type="button" style="padding: 2px 10px; border: 0;" onclick="getAkun('+y+');"><i class="fa fa-search"></i></button> </span> </div></div><div class="col-sm-3"><label class="control-label">Nominal</label> <input type="text" class="form-control number" id="nominal_'+y+'" value="0" /></div><div class="col-sm-2"> <label class="control-label">Tarif Pajak</label> <select type="text" class="form-control" id="trfPajak_'+y+'" disabled></select> <input type="hidden" id="jns_pph_'+y+'" value="Non Pph"/> <input type="hidden" id="tarif_persen_'+y+'" value="0"/><input type="hidden" id="akun_pph_'+y+'" disabled /> </div><div class="col-sm-2"> <label class="control-label">Nilai Pph</label> <input type="text" class="form-control" id="nilaiPph_'+y+'" value="0" readonly/> </div><div class="col-sm-2"> <label class="control-label">Keterangan Biaya</label><input type="text" class="form-control" id="keteranganAkun_'+y+'" maxlength="200"  value="'+keterangan+'"/></div></div>');
		}
		
		onload = getPph(y);
		$("#trfPajak_"+y).change(function(){
			var trfPajak = $("#trfPajak_"+y).val();
			var data = trfPajak.split("#");
			$("#jns_pph_"+y).val(data[0]);
			$("#tarif_persen_"+y).val(data[1]);
			$("#akun_pph_"+y).val(data[2]);
			var nom = document.getElementById('nominal_'+y).value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
			var trf = $("#tarif_persen_"+y).val();
			var nilai = Math.floor((parseInt(nom) * parseFloat(trf))/100);
			$("#nilaiPph_"+y).val(addCommas(nilai));
			onload = biaya_yg_dibyar();
		});
		$("#nominal_"+y).change(function(){
			var nom = document.getElementById('nominal_'+y).value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
			if (nom=='') { nom = '0'; $('#nominal_'+y).val('0');}
			var trf = $("#tarif_persen_"+y).val();
			var nilai = Math.floor((parseInt(nom) * parseFloat(trf))/100);
			$("#nilaiPph_"+y).val(addCommas(nilai));
			onload = biaya_yg_dibyar();
		});
		onload = biaya_yg_dibyar();
	}
	function delpos(id){
		$("#formPos_"+id).remove();
		onload = biaya_yg_dibyar();
	}
	
	function trfPajak_Rej(y){
		var trfPajak = $("#trfPajak_"+y).val();
		var data = trfPajak.split("#");
		$("#jns_pph_"+y).val(data[0]);
		$("#tarif_persen_"+y).val(data[1]);
		$("#akun_pph_"+y).val(data[2]);
		var nom = document.getElementById('nominal_'+y).value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
		var trf = $("#tarif_persen_"+y).val();
		var nilai = Math.floor((parseInt(nom) * parseFloat(trf))/100);
		$("#nilaiPph_"+y).val(addCommas(nilai));
		onload = biaya_yg_dibyar();
	}
	function nominal_Rej(y){
		var nom = document.getElementById('nominal_'+y).value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
		if (nom=='') { nom = '0'; $('#nominal_'+y).val('0');}
		var trf = $("#tarif_persen_"+y).val();
		var nilai = Math.floor((parseInt(nom) * parseFloat(trf))/100);
		$("#nilaiPph_"+y).val(addCommas(nilai));
		onload = biaya_yg_dibyar();
	}
	function getVendor(){
		var Tipe = $("#Tipe").val();
		var tipehutang = $("#tipehutang").val();
		if (Tipe!='') {
			if (Tipe=='BIAYA') {
				onload = dataVendor();
				onLoad = getMaterai();
			} else {
				if (tipehutang!='') {
					onload = dataVendor();
				onLoad = getMaterai();
				} else {
					onload = needValue('Pengajuan Voucher Payment','Tipe Hutang masih kosong!');
				}
			}
		} else {
			onload = needValue('Pengajuan Voucher Payment','Tipe Pengajuan masih kosong!');
		}
	}
	function getAkun(id){
		var KodeDealer = $("#KodeDealer").val();
		var IdUser = $("#IdUser").val();
		var Tipe = $("#Tipe").val();
		$.ajax({ 
			url: 'system/view/getAkun.php',
			data: { action:'getAkun', 'KodeDealer': KodeDealer, 'id': id, 'IdUser': IdUser, 'Tipe': Tipe },
			type: 'post',
			beforeSend: function(){
				onload = showLoading();
			},
			success: function(output) {
				onload = hideLoading();
				if (output=='0') {
					onload = needValue('Pengajuan Voucher Payment','Gagal Koneksi Cabang!');
					$("#KodeDealer").val('');
					$("#isGetDealer").css("display","none");
				} else if (output=='1') {
					onload = needValue('Pengajuan Voucher Payment','Gagal Koneksi HO!');
					$("#KodeDealer").val('');
					$("#isGetDealer").css("display","none");
				} else if (output=='2') {
					onload = needValue('Pengajuan Voucher Payment','Database tidak tersedia!');
					$("#KodeDealer").val('');
					$("#isGetDealer").css("display","none");
				} else {
					$("#dataAkunAja").html(output);
					$("#isGetDealer").css("display","block");
					$('#getAkun').modal('show'); 
				}
			}
		});
	}
	function dataVendor(){
		var KodeDealer = $("#KodeDealer").val();
		var Tipe = $("#Tipe").val();
		var tipehutang = $("#tipehutang").val();
		$.ajax({ 
		    url: 'system/view/getVendor.php',
		    data: {'KodeDealer': KodeDealer, 'Tipe': Tipe, 'tipehutang': tipehutang},
		    type: 'post',
		    beforeSend: function(){
		    	onload = showLoading();
		    },
		    success: function(output) {
		    	if (output=='0') {
		    		onload = needValue('Pengajuan Voucher Payment','Gagal Koneksi Cabang!');
		    		$("#KodeDealer").val('');
		    		$("#isGetDealer").css("display","none");
		    	} else if (output=='1') {
		    		onload = needValue('Pengajuan Voucher Payment','Gagal Koneksi HO!');
		    		$("#KodeDealer").val('');
		    		$("#isGetDealer").css("display","none");
		    	} else if (output=='2') {
		    		onload = needValue('Pengajuan Voucher Payment','Database tidak tersedia!');
		    		$("#KodeDealer").val('');
		    		$("#isGetDealer").css("display","none");
		    	} else {
		    		$("#dataVendor").html(output);
		    		$('#getVendor').modal('show'); 
		    	}
		    	onload = hideLoading();
			}
		});
	}
	
	function addposhutang(){
		var keterangan = $("#keterangan").val();
		var count = $("select[name='trfPajak[]']").length;
		var y = count + 1;
		var dpp = $("#dpp").val();
		$("#f_poshutang").append('<div class="form-group" id="formPos_'+y+'"> <div class="col-sm-2"> <label class="control-label">Tarif Pajak</label> <select type="text" name="trfPajak[]" class="form-control" id="trfPajak_'+y+'" disabled ></select> <input type="hidden" id="jns_pph_'+y+'"> <input type="hidden" id="tarif_persen_'+y+'"><input type="hidden" id="akun_pph_'+y+'"></div><div class="col-sm-3"> <label class="control-label">Nominal Dpp</label> <input type="text" class="form-control number" id="nominal_'+y+'" value="'+dpp+'"> </div><div class="col-sm-3"> <label class="control-label">Nilai Pph</label> <div class="input-group"> <input type="text" class="form-control" id="nilaiPph_'+y+'" value="0" readonly><span class="input-group-addon" style="padding: 0;min-width: 0;"><button type="button" style="padding: 2px 10px;border: 0;" onclick="delposHtg('+y+');"><i class="fa fa-minus"></i></button></span></div></div><div class="col-sm-4"> <label class="control-label">Keterangan Biaya</label><input type="text" class="form-control" id="keteranganAkun_'+y+'" maxlength="200"  value="'+keterangan+'"/></div></div>'
		);
		onload = getPph(y);
		$("#trfPajak_"+y).change(function(){
			var trfPajak = $("#trfPajak_"+y).val();
			var data = trfPajak.split("#");
			$("#jns_pph_"+y).val(data[0]);
			$("#tarif_persen_"+y).val(data[1]);
			$("#akun_pph_"+y).val(data[2]);
			var nom = document.getElementById("nominal_"+y).value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
			var trf = $("#tarif_persen_"+y).val();
			var nilai = (parseInt(nom) * parseInt(trf))/100;
			$("#nilaiPph_"+y).val(addCommas(Math.floor(nilai)));
			onload = htg_yg_dibyar();
		});
		$("#nominal_"+y).change(function(){
			var nom = document.getElementById("nominal_"+y).value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
			var trf = $("#tarif_persen_"+y).val();
			var nilai = (parseInt(nom) * parseInt(trf))/100;
			$("#nilaiPph_"+y).val(addCommas(Math.floor(nilai)));
			onload = htg_yg_dibyar();
		});
		onload = htg_yg_dibyar();
	}
	function delposHtg(id){
		$("#formPos_"+id).remove();
		onload = htg_yg_dibyar();
	}
	
	function getBank(id){
		var KodeDealer = $("#KodeDealer").val();
		$.ajax({ 
			url: 'system/view/getBank.php',
			data: { action:'getBank', 'KodeDealer' : KodeDealer },
			type: 'post',
			beforeSend: function(){
				onload = showLoading();
			},
			success: function(output) {
				onload = hideLoading();
				$("#dataBank").html(output);
				$('#getBank').modal('show'); 
			}
		});
	}
	function getNumber(){
		var KodeDealer = $("#KodeDealer").val();
		$.ajax({ 
			url: 'system/data/getNumber.php',
			data: { action:'getNumber', 'KodeDealer': KodeDealer},
			type: 'post',
			beforeSend: function(){
				onload = showLoading();
			},
			success: function(output) {
				onload = hideLoading();
				$("#nobukti").val(output);
			}
		});
	}
	function getNumberR(){
		var KodeDealer = $("#KodeDealer").val();
		var IdUser = $("#IdUser").val();
		$.ajax({ 
			url: 'system/data/getNumber.php',
			data: { action:'getNumberR', 'KodeDealer': KodeDealer, 'IdUser': IdUser},
			type: 'post',
			beforeSend: function(){
				onload = showLoading();
			},
			success: function(output) {
				onload = hideLoading();
				$("#nobukti_reject").html(output);
			}
		});
	}
	function getPph(id){
		var tipeppn = $("#tipeppn").val();
		if (tipeppn=='I' || tipeppn=='E') {
			var is_ppn = '1';
		} else if (tipeppn=='N') {
			var is_ppn = '0';
		} else {
			var is_ppn = $("#is_ppn").val();
		}
		var npwp = $("#npwp").val();
		
		$.ajax({ 
			url: 'system/control/akun.php',
			data: { action:'getPphNew', 'npwp': npwp},
			type: 'post',
			beforeSend: function(){
				onload = showLoading();
			},
			success: function(output) {
				onload = hideLoading();
				$("#trfPajak_"+id).html(output);
				$("#jns_pph_"+id).val('Non Pph');
				$("#tarif_persen_"+id).val('0');
				$("#akun_pph_"+id).val('00000000');
				$("#nilaiPph_"+id).val('0');
			}
		});
	}
	function getPpn(){
		var KodeDealer = $("#KodeDealer").val();
		$.ajax({ 
			url: 'system/control/pengajuan.php',
			data: { action:'getPpn', kodedealer:KodeDealer },
			type: 'post',
			beforeSend: function(){
				onload = showLoading();
			},
			success: function(output) {
				onload = hideLoading();
				$(".ppn_persen").html(" (" + output + "%)");
				$("#ppn_persen").val(output);
			}
		});
	}
	
	function biaya_yg_dibyar(){
		var nom = document.getElementById('realisasi_nominal').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
		var dpp = document.getElementById('dpp').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
		var count = $("input[name='posbiaya[]']").length;
		var totPhh = 0; var totNom = 0;
		for (var i = 1; i <= count; i++) {
			var nilaiPph = document.getElementById('nilaiPph_'+i).value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
			var nominal = document.getElementById('nominal_'+i).value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
			totPhh += parseInt(nilaiPph);
			totNom += parseInt(nominal);
		}
		var biaya_yg_dibyar = parseInt(nom) - parseInt(totPhh);
		// if (parseInt(totNom)!=parseInt(nom)) {
		// 	onload = needValue('Pengajuan Voucher Payment','Nominal Belom Balance!');
		// }
		var pety_cash = $("#pety_cash").val();
		if (parseInt(biaya_yg_dibyar)<=parseInt(pety_cash)) {
			$("#metode_bayar").val('Pety Cash');
		} else {
			var metode_bayar = $("#metode_bayar").val();
			if (metode_bayar=='Pety Cash') {
				$("#metode_bayar").val('');
			} else {
				$("#metode_bayar").val(metode_bayar);
			}
			$("#metode_bayar option[value='Pety Cash']").attr('disabled','disabled');
		}
		$("#total_dpp").val(addCommas(totNom));
		$("#biaya_yg_dibyar").val(addCommas(biaya_yg_dibyar));
	}
	function pickHtg(id,val){
		var ket = $("#namaVendor").val();
		if ($("#byr-"+id).is(":checked")) {
			$("#txtnom_"+id).html(addCommas(val));
		} else {
			$("#txtnom_"+id).html('0');
		}
		
		var count = $("input[name='byr[]']").length;
		var totNom = 0; var newKet = ",INV: "; $("#keterangan").val('');
		for (var i = 1; i <= count; i++) {
			if ($("#byr-"+i).is(":checked")) {
				totNom += parseFloat($("#byr-"+i).val());
				newKet += $("#NoFaktur-"+i).val()+";";
			}
		}
		$("#keterangan").val(ket.toUpperCase()+newKet);
		$("#realisasi_nominal").val(addCommas(totNom));
		// tambah keterangan pada pos biaya 
		/*var posbiaya = $("input[name='posbiaya[]']").length;
		for (var i = 1; i <= posbiaya; i++) {
			$("#keteranganAkun_"+i).val(ket.toUpperCase()+newKet);
		}
		
		var trfPajak = $("select[name='trfPajak[]']").length;
		for (var i = 1; i <= trfPajak; i++) {
			$("#keteranganAkun_"+i).val(ket.toUpperCase()+newKet);
		}
		*/
		
		onload = getDppHtg();
		var dpp = $("#dpp").val();
		var con = $("select[name='trfPajak[]']").length;
		if (con==0){
			$("#keteranganAkun_0").val(ket.toUpperCase()+newKet);
		} else {		
			for (var i = 1; i <= con; i++) {
				$("#nominal_"+i).val(dpp);			
				$("#keteranganAkun_"+i).val(ket.toUpperCase()+newKet);
			}
		}
		
		onload = htg_yg_dibyar();
	}
	function htg_yg_dibyar(){
		var nom = document.getElementById('realisasi_nominal').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
		var con = $("select[name='trfPajak[]']").length;
		var totPhh = 0; var totDppHtg = 0;
		for (var i = 1; i <= con; i++) {
			var nilaiPph = document.getElementById('nilaiPph_'+i).value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
			totPhh += parseInt(nilaiPph);
			var nominal = document.getElementById('nominal_'+i).value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
			totDppHtg += parseInt(nominal);
		}
		var nominal_materai = document.getElementById('nominal_materai').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
		var tipe_materai = $('#tipe_materai').val();
		
		var tipeppn = $("#tipeppn").val();
		
		if(tipeppn=='I'){			
			var htg_yg_dibyar = parseInt(nom) - parseInt(totPhh);
		} else if(tipeppn=='E') {
			var dpp = document.getElementById('dpp').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
			var ppn = document.getElementById('ppn').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
			var htg_yg_dibyar = parseInt(dpp) + parseInt(ppn) - parseInt(totPhh);
		} else {
			var htg_yg_dibyar = parseInt(nom) - parseInt(totPhh);
		}
		
		//var nominal_materai = $('#nominal_materai').val();
		if (tipe_materai=="I") { // include
			//htg_yg_dibyar = parseInt(nom) - parseInt(totPhh);
			
		} else if (tipe_materai=="E") { // exclude
			htg_yg_dibyar = parseInt(htg_yg_dibyar) + parseInt(nominal_materai);
		
		} else { // non materai
			//htg_yg_dibyar = parseInt(nom) - parseInt(totPhh);
		}
		
		//var htg_yg_dibyar = parseInt(nom) - parseInt(totPhh);
		$("#htg_stl_pajak").val(addCommas(htg_yg_dibyar));
		$("#totDppHtg").val(addCommas(totDppHtg));
	}
	function getDppHtg(){
		//alert('iki');
		var tipeppn = $("#tipeppn").val();
		var nominal = document.getElementById('realisasi_nominal').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
		
		var nominal_materai = document.getElementById('nominal_materai').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
		var tipe_materai = $('#tipe_materai').val();
		var nom = 0;
		
		if (tipeppn=='N') {
			if (tipe_materai=="I") { // include
				nom = parseInt(nominal) - parseInt(nominal_materai);
			} else if (tipe_materai=="E") { // exclude
				nom = nominal;
			} else { // non materai
				nom = nominal;
				//htg_yg_dibyar = parseInt(nom) - parseInt(totPhh);
			}
			
		    var dpp = nom;
		    var ppn = 0;
		    $("#no_fj").prop('readonly','readonly');
			
		} else if (tipeppn=='E') {
			if (tipe_materai=="I") { // include
				nom = parseInt(nominal) - parseInt(nominal_materai);
			} else if (tipe_materai=="E") { // exclude
				nom = nominal;
			} else { // non materai
				nom = nominal;
				//htg_yg_dibyar = parseInt(nom) - parseInt(totPhh);
			}
			
		    var dpp  = nom;
		    //var ppn = Math.round((10/100)*nom);			
			var persen_ppn = $("#ppn_persen").val();			
			var ppn = Math.round((parseInt(persen_ppn)/100)*nom);
					
		    var npwp = $("#npwp").val();
		    $("#no_fj").removeAttr('readonly');
			if (npwp=="") {
				onload = needValue('Pengajuan Voucher Payment','Harap Isi NPWP Di Master Supplier!');
				$("#tipeppn").val('N');
				var dpp = nom;
		    	var ppn = 0;
				$("#no_fj").prop('readonly','readonly');
				//alert("4");
				//$("#dpp").val(addCommas(dpp));
				//$("#ppn").val(addCommas(ppn));
				$("#dpp").val(dpp);
				$("#ppn").val(ppn);
				return false;
			}
			
		} else if (tipeppn=='I') {
			
			if (tipe_materai=="I") { // include
				nom = parseInt(nominal) - parseInt(nominal_materai);
			} else if (tipe_materai=="E") { // exclude
				nom = nominal;
			} else { // non materai
				nom = nominal;
				//htg_yg_dibyar = parseInt(nom) - parseInt(totPhh);
			}
			
		    //var dpp  = Math.round((10/11)*nom);
		    //var ppn = Math.round((1/11)*nom);
			
			var persen_ppn = $("#ppn_persen").val();
			var dpp = Math.round(parseInt(nom) * (100 / (100 + parseInt(persen_ppn))));
			var ppn = Math.round(parseInt(nom) * (parseInt(persen_ppn) / (100 + parseInt(persen_ppn))));
			
			
		    var npwp = $("#npwp").val();
		    $("#no_fj").removeAttr('readonly');
			if (npwp=="") {
				onload = needValue('Pengajuan Voucher Payment','Harap Isi NPWP Di Master Supplier!');
				$("#tipeppn").val('N');
				var dpp = nom;
		    	var ppn = 0;
				$("#no_fj").prop('readonly','readonly');
				//$("#dpp").val(addCommas(dpp));
				//$("#ppn").val(addCommas(ppn));
				
				$("#dpp").val(dpp);
				$("#ppn").val(ppn);
						
				return false;
			}
		}
		$("#dpp").val(addCommas(dpp));
		$("#ppn").val(addCommas(ppn));

		var con = $("select[name='trfPajak[]']").length;
		for (var i = 1; i <= con; i++) {
			onload = getPph(i);
		}
		$("#f_poshutang").html('');
		onload = addposhutang();
		onload = getMaterai();
	}
	function nowdate(){
		var d = new Date();
		var month = d.getMonth()+1;
		var day = d.getDate();
		var year = d.getFullYear();
		var month = ((''+month).length<2 ? '0' : '') + month;
		var day = ((''+day).length<2 ? '0' : '') + day;
		var nowdate = year +'-'+ month +'-'+ day;
		return nowdate;
	}
	function getFile(id){
		if (id==1) {
			var data = $("#upload_file").val();
		} else {
			var data = $("#upload_fp").val();
		}
		
		if (data!='') {
			window.open('system/files/'+data);
		} else {
			onload = needValue('Pengajuan Voucher Payment','File belom di upload!');
		}
	}	
	function getMaterai(){
		$('#tipe_materai').on('change',function(){
			var tipe_materai = $('#tipe_materai').val();
			var nominal_materai = $('#nominal_materai').val();
			//alert(tipe_materai);
			$.ajax({ 
				url: 'system/control/pengajuan.php',
				data: { action:'getMaterai' },
				type: 'post',
				beforeSend: function(){
					onload = showLoading();
				},
				success: function(output) {
					onload = hideLoading();
					$("#nominal_materai").val(output);
					
					$('#div_materai').hide();
					$('#kodeAkunMaterai').val('');
					$('#namaAkunMaterai').val('');
					
					
					if (tipe_materai=="I") { // include
						$('#nominal_materai').val(addCommas(output));
						htg_yg_dibyar();
						
					} else if (tipe_materai=="E") { // exclude
						$('#div_materai').show();
						$('#nominal_materai').val(addCommas(output));
						htg_yg_dibyar();
					
					} else { // non materai
						$('#nominal_materai').val("0");
						htg_yg_dibyar();
					}
					
				}
			});
			
		});
	}
	function getAkunMaterai(id){
		var KodeDealer = $("#KodeDealer").val();
		var IdUser = $("#IdUser").val();
		//var Tipe = $("#Tipe").val();
		$.ajax({ 
			url: 'system/view/getAkunMaterai.php',
			data: { action:'getAkun', 'KodeDealer': KodeDealer, 'id': id, 'IdUser': IdUser },
			type: 'post',
			beforeSend: function(){
				onload = showLoading();
			},
			success: function(output) {
				onload = hideLoading();
				if (output=='0') {
					onload = needValue('Pengajuan Voucher Payment','Gagal Koneksi Cabang!');
					$("#KodeDealer").val('');
					//$("#isGetDealer").css("display","none");
				} else if (output=='1') {
					onload = needValue('Pengajuan Voucher Payment','Gagal Koneksi HO!');
					$("#KodeDealer").val('');
					//$("#isGetDealer").css("display","none");
				} else if (output=='2') {
					onload = needValue('Pengajuan Voucher Payment','Database tidak tersedia!');
					$("#KodeDealer").val('');
					//$("#isGetDealer").css("display","none");
				} else {
					$("#dataAkunAjaMaterai").html(output);
					//$("#isGetDealer").css("display","block");
					$('#getAkunMaterai').modal('show'); 
				}
			}
		});
	}
	
	function formatfileImportHutang(){
		
		if ($('#kode_vendor').val()!='') {
			//$('#kode_vendorx').val($('#kode_vendor').val());
			//$('#KodeDealerx').val($('#KodeDealer').val());
			//$('#tipehutangx').val($('#tipehutang').val());
			//$('#nobuktix').val($('#nobukti').val());
			var kodedealer = $('#KodeDealer').val();
			var kodelgn = $('#kode_vendor').val();
			var tipehutang = $('#tipehutang').val();
			
			kodelgn = kodelgn.trim();
			tipehutang = tipehutang.trim();
			kodedealer = kodedealer.trim();
			
			kodelgn = kodelgn.replace(" ","__");
			tipehutang = tipehutang.replace(" ","__");
			
			window.location.href='system/control/importhutang.php?action=formatexcel&KodeDealer='+kodedealer+'&KodeLgn='+kodelgn+'&tipehutang='+tipehutang;
			/*$.ajax({
			   url: 'system/control/importhutang.php',
			   type : 'POST',
			   cache : true,
			   data : { 'KodeDealer':kodedealer, 'KodeLgn' : kodelgn, 'tipehutang': tipehutang,'action':'formatexcel' },
			   success : function(data) {
				   console.log(data);
				   //alert(data);
			   }
			});
			*/
		} else {
			onload = needValue('Pengajuan Voucher Payment','Vendor belum dipilih');
		}
		
		//window.location.href='system/control/importhutang.php?action=formatexcel';
	}
	
	function formImportHutang(){
		//alert('test');
		if ($('#kode_vendor').val()!='') {
			$('#kode_vendorx').val($('#kode_vendor').val());
			$('#KodeDealerx').val($('#KodeDealer').val());
			$('#tipehutangx').val($('#tipehutang').val());
			$('#nobuktix').val($('#nobukti').val());
			$('#formImportHutang').modal('show'); 
		} else {
			onload = needValue('Pengajuan Voucher Payment','Vendor belum dipilih');
		}
	}
						
</script>


<div class="row">
	<div class="col-md-12">
		<div class="panel panel-gray">
			<div class="panel-heading">
				<h4>Pengajuan Voucher Payment</h4>
			</div>
			<form id="validate-form" action="#" method="POST" class="form-horizontal row-border" enctype="multipart/form-data">
            	<input type="hidden" name="ppn_persen" id="ppn_persen" value="0" />
				<?php if ($modify=='new') { ?>
					<div class="panel-body collapse in">
						<input type="hidden" id="IdUser" value="<?php echo $_SESSION['UserID'] ?>">
						<input type="hidden" id="pety_cash" value="<?php echo $akun['pety_cash'] ?>">
						<input type="hidden" id="div" value="<?php echo $div['divisi'] ?>">
						<input type="hidden" id="kodedealer" value="<?php echo $_SESSION['kodedealer'] ?>">
						<div class="form-group">
							<label class="col-sm-2 control-label">Kode Dealer / NRM</label>
							<div class="col-sm-4">
							    <select type="text" name="KodeDealer" id="KodeDealer" class="form-control">
							    	<?php
							    		$qry = mssql_query("select a.KodeDealer,NamaDealer from SPK00..dodealer a 
											INNER JOIN sys_user b ON a.KodeDealer=b.KodeDealer
											where IdUser='".$IdUser."'",$conns);	
										$count = mssql_num_rows($qry);
										if ($count>1) {
											echo "<option value=''>- Pilih -</option>";
										}
										while($row = mssql_fetch_array($qry)){
											$plh = ($upd['kodedealer']==$row['KodeDealer'])?"selected" : ""; 
											echo "<option value='$row[KodeDealer]' $plh>$row[NamaDealer]</option>";
										}
							    	?>
							    </select>
							</div>
							<label class="col-sm-2 control-label">Tipe Pengajuan</label>
							<div class="col-sm-4">
							    <select name="Tipe" id="Tipe" class="form-control">
							    	<?php
							    		$tp = mssql_fetch_array(mssql_query("select tipeAju from sys_user where IdUser = '".$IdUser."'"));
							    		if ($tp['tipeAju']=='all' or $tp['tipeAju']=='') {
							    			$where = "";
							    		} else {
							    			$where = "where Tipe in ('".$tp['tipeAju']."')";
							    		}

							    		$qry = mssql_query("select Tipe from TipePengajuan $where order by idTipe asc",$conns);	
										echo "<option value=''>- Pilih -</option>";
										while($row = mssql_fetch_array($qry)){
											$plh2 = ($upd['tipe']==$row['Tipe'])?"selected" : ""; 
											echo "<option value='$row[Tipe]' $plh2>$row[Tipe]</option>";
										}
							    	?>
							    </select>
							</div>
						</div>
						<section id="getForm"></section>
						<section id="getForm2"></section>
					</div>
					<div class="panel-footer">
						<div class="row">
						    <div class="col-sm-10 col-sm-offset-2">
						        <div class="btn-toolbar">
						        	<?php if (isset($_POST['id'][0])) { ?>
										<button type="button" id="edit" class="btn-primary btn">Save</button>
									<?php } else { ?>
										<button type="button" id="new" class="btn-primary btn">Save</button>
									<?php } ?>
						            <button type="button" id="cancel" class="btn-default btn">Cancel</button>
						        </div>
						    </div>
						</div>
					</div>
				<?php } else if ($modify=='edit') { ?>
					<script type="text/javascript">
						jQuery(document).ready(function($) {
							$("input[type='file']").change(function(){
								var test = this.id; var id = test.split("-");
								var fd = new FormData();
							    var files = $('#'+test)[0].files[0];
							    fd.append('file',files);
							    $.ajax({
							        url:'system/control/upload.php',
							        type:'post',
							        data:fd,
							        contentType: false,
							        processData: false,
							        beforeSend: function(){
										onload = showLoading();
									},
							        success:function(response){
							        	onload = hideLoading();
							            if(response != 0){
							            	$("#"+id[1]).val(response);
							                onload = needValue('Pengajuan Voucher Payment','File uploaded!');
							            } else{
							                onload = needValue('Pengajuan Voucher Payment','File not uploaded!');
							            }
							        }
							    });
							});
							$('#is_ppn').click(function(event){
								var nom = document.getElementById('realisasi_nominal').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
								if ($('#is_ppn').prop("checked")) {
									$('#is_ppn').val('1');
									var tipeppn = $("#tipeppn").val();
									
									var persen_ppn = $("#ppn_persen").val();									
									
									if(tipeppn=='I'){
										//var dpp  = (10/11)*parseInt(nom);
					    				//var ppn = (1/11)*parseInt(nom);
										var dpp = Math.round(parseInt(nom) * (100 / (100 + parseInt(persen_ppn))));
										var ppn = Math.round(parseInt(nom) * (parseInt(persen_ppn) / (100 + parseInt(persen_ppn))));
									
									} else if(tipeppn=='E') {
										var dpp  = parseInt(nom);
					    				//var ppn = (10/100)*parseInt(nom);
										var ppn = Math.round(parseInt(nom) * (parseInt(persen_ppn) / (100 + parseInt(persen_ppn))));
									}
					    			
					    			$("#no_fj").removeAttr('readonly');
									var npwp = $("#npwp").val();
									if (npwp=="") {
										onload = needValue('Pengajuan Voucher Payment','Harap Isi NPWP Di Master Supplier!');
										$("#no_fj").prop('readonly','readonly');
										$('#is_ppn').val('0');
										return false;
									}
								} else {
									$('#is_ppn').val('0');
									var dpp = parseInt(nom);
									var ppn = 0;
									$("#no_fj").prop('readonly','readonly');
								}
								//alert("5");
								//$("#dpp").val(addCommas(Math.round(dpp)));
								//$("#ppn").val(addCommas(Math.round(ppn)));
								$("#dpp").val(Math.round(dpp));
								$("#ppn").val(Math.round(ppn));
						
								
								var con = $("input[name='posbiaya[]']").length;
								for (var i = 1; i <= con; i++) {
									onload = getPph(i);
								}
								onload = biaya_yg_dibyar();
							});
							$('.get_number').on('click',function(){
								onload = addpos();
								$('.number').number( true, 0 );
							});
							$('.number').number( true, 0 );
						});
						function trfPajak(y){
							var trfPajak = $("#trfPajak_"+y).val();
							var data = trfPajak.split("#");
							$("#jns_pph_"+y).val(data[0]);
							$("#tarif_persen_"+y).val(data[1]);
							$("#akun_pph_"+y).val(data[2]);
							var nom = document.getElementById('nominal_'+y).value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
							var trf = $("#tarif_persen_"+y).val();
							var nilai = Math.floor((parseInt(nom) * parseFloat(trf))/100);
							$("#nilaiPph_"+y).val(addCommas(nilai));
							onload = biaya_yg_dibyar();
						}
						function nominal(y){
							var nom = document.getElementById('nominal_'+y).value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
							var trf = $("#tarif_persen_"+y).val();
							var nilai = Math.floor((parseInt(nom) * parseFloat(trf))/100);
							$("#nilaiPph_"+y).val(addCommas(nilai));
							onload = biaya_yg_dibyar();
						}
						function cekRab(kodedealer,kodeakun,nom){
							$.ajax({ 
								url: 'system/control/validasi.php',
								data: { action:'cekRab', 'kodedealer': kodedealer, 'kodeakun': kodeakun, 'nom': nom },
								type: 'post',
								beforeSend: function(){
									onload = showLoading();
								},
								success: function(output) {
									onload = hideLoading();
									$("#over").val(output);
								}
							});
						}
					</script>
					<div class="panel-body collapse in">
						<input type="hidden" id="IdUser" value="<?php echo $_SESSION['UserID'] ?>">
						<input type="hidden" id="pety_cash" value="<?php echo $akun['pety_cash'] ?>">
						<input type="hidden" id="div" value="<?php echo $div['divisi'] ?>">
						<input type="hidden" id="kodedealer" value="<?php echo $_SESSION['kodedealer'] ?>">
						<div class="form-group">
							<label class="col-sm-2 control-label">Kode Dealer / NRM</label>
							<div class="col-sm-4">
							    <select type="text" name="KodeDealer" id="KodeDealer" class="form-control" disabled>
							    	<?php
							    		$qry = mssql_query("select a.KodeDealer,NamaDealer from SPK00..dodealer a 
											INNER JOIN sys_user b ON a.KodeDealer=b.KodeDealer
											where IdUser='".$IdUser."'",$conns);	
										$count = mssql_num_rows($qry);
										if ($count>1) {
											echo "<option value=''>- Pilih -</option>";
										}
										while($row = mssql_fetch_array($qry)){
											$plh = ($vw['kodedealer']==$row['KodeDealer'])?"selected" : ""; 
											echo "<option value='$row[KodeDealer]' $plh>$row[NamaDealer]</option>";
										}
							    	?>
							    </select>
							</div>
							<label class="col-sm-2 control-label">Tipe Pengajuan</label>
							<div class="col-sm-4">
							    <select name="Tipe" id="Tipe" class="form-control" disabled>
							    	<?php
							    		$tp = mssql_fetch_array(mssql_query("select tipeAju from sys_user where IdUser = '".$IdUser."'"));
							    		if ($tp['tipeAju']=='all' or $tp['tipeAju']=='') {
							    			$where = "";
							    		} else {
							    			$where = "where Tipe in ('".$tp['tipeAju']."')";
							    		}

							    		$qry = mssql_query("select Tipe from TipePengajuan $where order by idTipe asc",$conns);	
										echo "<option value=''>- Pilih -</option>";
										while($row = mssql_fetch_array($qry)){
											$plh2 = ($vw['tipe']==$row['Tipe'])?"selected" : ""; 
											echo "<option value='$row[Tipe]' $plh2>$row[Tipe]</option>";
										}
							    	?>
							    </select>
							</div>
						</div>
                        
						<?php if ($vw['tipe']=='HUTANG') { ?>
							<section id="getForm">
								<div class="form-group">
								    <label class="col-sm-2 control-label">Divisi</label>
								    <div class="col-sm-4">
								    	<select id="divisi" class="form-control" disabled="disabled">
								    		<?php
								    			$KodeDealer = $vw['kodedealer'];
												$div = $vw['divisi'];
												if ($KodeDealer=='2010') { $is_dealer = "0"; } else { $is_dealer = "1"; }
												if ($div=='all') { $divisi = ""; } else { $divisi = "and nama_div = '".$div."'"; }
												
												$sql = "select * from sys_divisi where is_dealer = '".$is_dealer."' $divisi";
												$rsl = mssql_query($sql);
												//echo "<option value=''>- Pilih -</option>";
												while ($dt = mssql_fetch_array($rsl)) {
													$plh = ($vw['divisi']==$dt['nama_div'])?"selected" : ""; 
													echo "<option value='".$dt['nama_div']."' $plh>".$dt['nama_div']."</option>";
												}
								    		?>
								    	</select>
								    </div>
								    <label class="col-sm-2 control-label">Nama Atasan</label>
								    <div class="col-sm-4">
								        <select id="IdAtasan" class="form-control" disabled="disabled">
                                          <?php
												$KodeDealer = $vw['kodedealer'];
												$divisi = $vw['divisi'];
								            	$IdUser = $vw['userentry'];
												$atasan = mssql_fetch_array(mssql_query("select IdAtasan from sys_user where IdUser = '".$IdUser."'"));
												if ($atasan['IdAtasan']=='all') { $boss = ""; } else { $boss = "and IdUser = '".$atasan['IdAtasan']."'"; }
												$sql = "select * from sys_user 
														where (tipe = 'SECTION HEAD' or tipe = 'ADH') and divisi in ('".$divisi."','all') 
														and KodeDealer = '".$KodeDealer."' $boss";
												$rsl = mssql_query($sql);
												//echo "<option value=''>- Pilih -</option>";
												while ($dt = mssql_fetch_array($rsl)) {
													$plh = ($vw['IdAtasan']==$dt['IdUser'])?"selected" : ""; 
													echo "<option value='".$dt['IdUser']."' $plh>".$dt['namaUser']."</option>";
												}
								            ?>
								        </select>
								    </div>
								</div>
								<hr style="margin: 10px 0;" />
								<div class="form-group">
								    <label class="col-sm-2 control-label">Tipe Hutang</label>
								    <div class="col-sm-4">
								    	<select id="tipehutang" class="form-control">
								    		<?php
								    			$KodeDealer = $vw['kodedealer'];
												if ($KodeDealer=='2010') {
													$sql = "select idHtg,nama from sys_hutang where posisi = 'HO'";
												} else {
													$sql = "select idHtg,nama from sys_hutang where posisi = 'Dealer'";
												}
												$rsl = mssql_query($sql);
												echo "<option value=''>- Pilih -</option>";
												while ($dt = mssql_fetch_array($rsl)) {
													$plh = ($vw['tipehutang']==$dt['nama'])?"selected" : ""; 
													echo "<option value='$dt[nama]' $plh>$dt[nama]</option>";
												}
								    		?>
								    	</select>
								    </div>
								</div>
								<div class="form-group">
								    <label class="col-sm-2 control-label">No. Bukti</label>
								    <div class="col-sm-4" id="f_NoBuktiPengajuan">
								        <div class="input-group">
								        	<span class="input-group-addon">VP</span> 
								        	<input type="text" id="nobukti" class="form-control" value="<?php echo substr($vw[nobukti], 2,15); ?>" readonly />
								        </div>
								    </div>
								    <label class="col-sm-2 control-label">Tanggal Pengajuan</label>
								    <div class="col-sm-4">
								    	<input type="date" id="tgl_pengajuan" class="form-control" value="<?php echo $vw['tgl_pengajuan']; ?>" readonly />
								    </div>
								</div>
								<div class="form-group">
								    <label class="col-sm-2 control-label">Upload File</label>
								    <div class="col-sm-4">
								        <div class="input-group">
								            <input type="text" id="upload_file" class="form-control" value="<?php echo $vw['upload_file']; ?>"  disabled="" />
								            <span class="input-group-addon" style="padding: 0; min-width: 0;">
								                <button type="button" style="padding: 2px 10px; border: 0;" onclick="getFile(1);">
								                	<i class="fa fa-eye"></i>
								                </button>
								            </span>
								            <span class="input-group-addon" style="padding: 0; min-width: 0;">
								                <span class="fileinput-button">
								                    <span style="padding: 2px 10px; border: 0;">
								                    	<i class="fa fa-camera"></i>
								                    </span> 
								                    <input id="ups-upload_file" type="file" name="files[]" accept=".xlsx,.xls,.jpg,.png,.pdf,.doc,.docx,.zip,.rar"/>
								                </span>
								            </span>
								        </div>
								    </div>
								    <label class="col-sm-2 control-label">Upload Faktur Pajak</label>
								    <div class="col-sm-4">
								        <div class="input-group">
								            <input type="text" id="upload_fp" class="form-control" value="<?php echo $vw['upload_fp']; ?>" disabled="" />
								            <span class="input-group-addon" style="padding: 0; min-width: 0;">
								                <button type="button" style="padding: 2px 10px; border: 0;" onclick="getFile(2);">
								                	<i class="fa fa-eye"></i>
								                </button>
								            </span>
								            <span class="input-group-addon" style="padding: 0; min-width: 0;">
								                <span class="fileinput-button">
								                    <span style="padding: 2px 10px; border: 0;">
								                    	<i class="fa fa-camera"></i>
								                    </span> 
								                    <input id="ups-upload_fp" type="file" name="files[]"  accept=".xlsx,.xls,.jpg,.png,.pdf,.doc,.docx,.zip,.rar" />
								                </span>
								            </span>
								        </div>
								    </div>
								</div>
								<hr style="margin: 10px 0;" />
								<div class="form-group">
								    <label class="col-sm-2 control-label">Kode Vendor</label>
								    <div class="col-sm-4">
								        <div class="input-group">
								            <input type="text" name="kode_vendor" id="kode_vendor" class="form-control" value="<?php echo $vw['kode_vendor']; ?>" readonly/>
								            <span class="input-group-addon" style="padding: 0; min-width: 0;">
								                <button type="button" style="padding: 2px 10px; border: 0;" onclick="getVendor();"><i class="fa fa-search"></i></button>
								            </span>
								        </div>
								    </div>
								    <label class="col-sm-2 control-label">Nama Vendor</label>
								    <div class="col-sm-4">
								    	<input type="text" name="namaVendor" id="namaVendor" class="form-control" value="<?php echo $vw['namaVendor']; ?>" readonly/>
								    </div>
								</div>
								<div class="form-group">
								    <label class="col-sm-2 control-label">Metode Pembayaran</label>
								    <div class="col-sm-4">
								        <select type="text" class="form-control" id="metode_bayar">
								            <?php
								            	$opt = array('' => '- Pilih -', 'Transfer' => 'Transfer', 'Cash' => 'Cash');
								            	foreach ($opt as $key => $value) {
								            		$plh = ($vw['metode_bayar']==$key)?"selected" : ""; 
								            		echo "<option value='$key' $plh>$value</option>";
								            	}
								            ?>
								        </select>
								    </div>
                                    <label class="col-sm-2 control-label">Departement Terkait</label>
								    <div class="col-sm-4">
                                    		 <select type="text" class="form-control" id="deptterkait">
								           <?php
											
												if ($vw['kodedealer']=='2010') { $is_dealer = "0"; } else { $is_dealer = "1"; }
												$sql = "select id_sys_dept, nama_dept 
														from sys_department a
														left join sys_divisi b on a.id_sys_div = b.id_sys_div
														where b.is_dealer = '".$is_dealer."' and a.is_aktif = '1' 
														and nama_dept in (
															select b.department from DeptTerkait a
															inner join sys_user b on a.iduser = b.iduser) and nama_dept != '".$_SESSION['evo_dept']."'";
												$rsl = mssql_query($sql);
												echo "<option value=''>- Pilih -</option>";
												while ($dt = mssql_fetch_array($rsl)) {
													if ($vw['deptterkait'] == $dt['deptterkait']) {
														$select = "selected";
													} else {
														$select = "";
													}
													echo "<option value='".$dt['nama_dept']."' $select>".$dt['nama_dept']."</option>";
												}
								            ?>
                                        
								            
								        </select>
								    </div>
								</div>
                                <div class="form-group">
								    <label class="col-sm-2 control-label">Beneficary Account</label>
								    <div class="col-sm-4">
								    	<input type="text" class="form-control" id="benificary_account" value="<?php echo $vw['benificary_account']; ?>" readonly />
								    </div>
								    <label class="col-sm-2 control-label">Tanggal Bayar</label>
								    <div class="col-sm-4">
								    	<input type="date" class="form-control" id="tgl_bayar" value="<?php echo $vw['tgl_bayar']; ?>" />
								    </div>
								</div>
								<div class="form-group">
								    <label class="col-sm-2 control-label">Nama Bank Penerima</label>
								    <div class="col-sm-4">
								    	<input type="text" class="form-control" id="nama_bank" value="<?php echo $vw['nama_bank']; ?>" readonly />
								    </div>
								    <label class="col-sm-2 control-label">Nama Pemilik</label>
								    <div class="col-sm-4">
								    	<input type="text" class="form-control" id="nama_pemilik" value="<?php echo $vw['nama_pemilik']; ?>" readonly />
								    </div>
								</div>
								<div class="form-group">
								    <label class="col-sm-2 control-label">Email Penerima</label>
								    <div class="col-sm-4">
								    	<input type="text" class="form-control" id="email_penerima" value="<?php echo $vw['email_penerima']; ?>" readonly />
								    </div>
								    <label class="col-sm-2 control-label">Nama Alias</label>
								    <div class="col-sm-4">
								    	<input type="text" class="form-control" id="nama_alias" value="<?php echo $vw['nama_alias']; ?>" readonly />
								    </div>
								</div>
								<div class="form-group">
								    <label class="col-sm-2 control-label">Nama Bank Pengirim</label>
								    <div class="col-sm-4">
								        <div class="input-group">
								            <input type="hidden" name="kode_bank_pengirim" id="kode_bank_pengirim" value="<?php echo $vw['kode_bank_pengirim']; ?>" class="form-control" disabled="" />
								            <input type="text" name="nama_bank_pengirim" id="nama_bank_pengirim" value="<?php echo $vw['nama_bank_pengirim']; ?>" class="form-control" disabled="" />
								            <span class="input-group-addon" style="padding: 0; min-width: 0;">
								                <button type="button" style="padding: 2px 10px; border: 0;" onclick="getBank();">
								                	<i class="fa fa-search"></i>
								                </button>
								            </span>
								        </div>
								    </div>
								    <label class="col-sm-2 control-label">Transfer From Account</label>
								    <div class="col-sm-4">
								    	<input type="text" class="form-control" id="tf_from_account" value="<?php echo $vw['tf_from_account']; ?>" readonly="" />
								    </div>
								</div>
								<hr style="margin: 10px 0;" />
								<div class="form-group"><label class="col-sm-2 control-label">No Tagihan</label></div>
								<div class="form-group">
								    <div class="col-sm-12">
								    	<table class="gethutang" style="display: none;"></table>
								    	<script type="text/javascript">
								        	jQuery(document).ready(function($) {
								        		$(".gethutang").flexigrid({
												    dataType : 'xml',
												    colModel : [ 
												        {
												            display : 'No Faktur',
												            name : 'TglPengajuan',
												            width : 120,
												            sortable : false,
												            align : 'left'
												        }, {
												            display : 'Tgl Faktur',
												            name : 'NoBuktiPengajuan',
												            width : 80,
												            sortable : false,
												            align : 'left'
												        }, {
												            display : 'Tgl Jth Tmpo',
												            name : '',
												            width : 80,
												            sortable : false,
												            align : 'left'
												        }, {
												            display : 'Keterangan',
												            name : 'KodeAkunBank',
												            width : 530,
												            sortable : false,
												            align : 'left'
												        }, {
												            display : 'Jumlah',
												            name : 'namaBank',
												            width : 100,
												            sortable : false,
												            align : 'left'
												        }, {
												            display : 'Byr',
												            name : 'NoRekening',
												            width : 30,
												            sortable : false,
												            align : 'center'
												        }, {
												            display : 'Jml Yg dibayar',
												            name : 'NamaRekening',
												            width : 100,
												            sortable : false,
												            align : 'left'
												        }
												    ],
												    showToggleBtn : false,
												    width : 'auto',
												    height : '150'
												});
												$('.gethutang').flexOptions({
													url:'system/data/gethutang.php', 
													newp: 1,
													params:[
														{ name:'KodeLgn', value: $("#kode_vendor").val() },
														{ name:'KodeDealer', value: $("#KodeDealer").val() },
														{ name:'tipehutang', value: $("#tipehutang").val() },
														{ name:'modif', value: "1" },
														{ name:'nobukti', value: $("#nobukti").val() }
													]
												}).flexReload();
								        	});
								        </script>
								    </div>
								</div>
								<div class="form-group">
								    <label class="col-sm-2 control-label">Nominal</label>
								    <div class="col-sm-4">
								    	<input type="text" class="form-control number" id="realisasi_nominal" value="<?php echo $vw['realisasi_nominal']; ?>" readonly />
								   </div>
								</div>
                                
                                <div class="form-group">
								    <label class="col-sm-2 control-label">Tipe Materai</label>
								    <div class="col-sm-4">
								    	 <select name="tipe_materai" id="tipe_materai" class="form-control" onchange="getDppHtg();">
                                         	<option value="I">Include</option>
                                         	<option value="E">Exclude</option>
                                         	<option value="N">Non Materai</option>
							    		</select>
								   </div>
								    <label class="col-sm-2 control-label">Nominal Materai</label>
								    <div class="col-sm-4">
								    	<input type="text" class="form-control number" id="nominal_materai" value="<?php echo $nominal_materai; ?>" readonly />
								   </div>
								</div>
                                
                                <div class="form-group">
								    <label class="col-sm-2 control-label">Kode Akun Materai</label>
								    <div class="col-sm-4">
								        <div class="input-group">
								            <input type="text" class="form-control" id="kodeAkunMaterai" value="<?php echo $vw['kodeAkunMaterai']; ?>" readonly />
								            <span class="input-group-addon" style="padding: 0; min-width: 0;">
								                <button type="button" style="padding: 2px 10px; border: 0;" onclick="getAkunMaterai();">
								                	<i class="fa fa-search"></i>
								                </button>
								            </span>
								        </div>
								    </div>
								    <label class="col-sm-2 control-label">Nama Akun Materai</label>
								    <div class="col-sm-4">
								    	<input type="text" class="form-control" id="namaAkunMaterai" value="<?php echo $vw['namaAkunMaterai']; ?>" readonly />
								    </div>
								</div>
                                
                                
                                
                                
                                
								<div class="form-group">
								    <label class="col-sm-2 control-label">Kode Akun</label>
								    <div class="col-sm-4">
								        <div class="input-group">
								            <input type="text" class="form-control" id="kodeAkun" value="<?php echo $vw['kodeAkun']; ?>" readonly />
								            <span class="input-group-addon" style="padding: 0; min-width: 0;">
								                <button type="button" style="padding: 2px 10px; border: 0;" onclick="getAkun();">
								                	<i class="fa fa-search"></i>
								                </button>
								            </span>
								        </div>
								    </div>
								    <label class="col-sm-2 control-label">Nama Akun</label>
								    <div class="col-sm-4">
								    	<input type="text" class="form-control" id="namaAkun" value="<?php echo $vw['namaAkun']; ?>" readonly />
								    </div>
								</div>
								<div class="form-group">
								    <label class="col-sm-2 control-label">Type Ppn</label>
								    <div class="col-sm-4">
								        <select type="text" class="form-control" id="tipeppn" onchange="getDppHtg();">
								            <?php
								            	$opt = array('N' => 'Non Ppn', 'I' => 'Include', 'E' => 'Exclude');
								            	foreach ($opt as $key => $value) {
								            		$plh = ($vw['tipeppn']==$key)?"selected" : ""; 
								            		echo "<option value='$key' $plh>$value</option>";
								            	}
								            ?>
								        </select>
								    </div>
								</div>
								<div class="form-group">
								    <label class="col-sm-2 control-label">Dpp</label>
								    <div class="col-sm-4">
								    	<input type="text" class="form-control number" id="dpp" value="<?php echo $vw['dpp']; ?>" readonly />
								    </div>
								    <label class="col-sm-2 control-label">Ppn
                                     <span class="ppn_persen"></span>
                                    </label>
								    <div class="col-sm-4">
								    	<input type="text" class="form-control number" id="ppn" value="<?php echo $vw['ppn']; ?>" readonly />
								    </div>
								</div>
								<div class="form-group">
								    <label class="col-sm-2 control-label">NPWP</label>
								    <div class="col-sm-4">
								    	<input type="text" class="form-control" id="npwp" value="<?php echo $vw['npwp']; ?>" readonly />
								    </div>
								    <label class="col-sm-2 control-label">No. Faktur Pajak</label>
								    <div class="col-sm-4">
								    	<input type="text" class="form-control" id="no_fj" value="<?php echo $vw['no_fj']; ?>" readonly />
								    </div>
								</div>
								<span id="f_poshutang">
                                
                                	<?php
										$spos = "select *,(jns_pph+'#'+convert(varchar,tarif_persen)) val from DataEvoPos where nobukti = '".$vw['nobukti']."'";
										$rpos = mssql_query($spos);
										$jmlpos = mssql_num_rows($rpos);
										$y = 1;
										$over = "0";
										if ($jmlpos>0) {
											while ($dpos = mssql_fetch_array($rpos)) {											
												echo '<div class="form-group" id="formPos_'.$y.'"> 
														<div class="col-sm-2"> <label class="control-label">Tarif Pajak</label> 
															 <label class="control-label">Tarif Pajak</label>
															<select type="text" class="form-control" id="trfPajak_'.$y.'" name="trfPajak[]" onchange="trfPajak('.$y.');" disabled>';
															$sql = "select jns_pph,tarif_persen,(jns_pph+'#'+convert(varchar,tarif_persen)) val 
																from settingPph where npwp = '".$vw['is_ppn']."' order by idpph asc";
															$rsl = mssql_query($sql);
															echo "<option value='Non Pph#0#00000000'>Non Pph</option>";
															while ($dt = mssql_fetch_array($rsl)) {
																if ($vw['is_ppn']=='0') {
																	if ($dt['jns_pph']=='Pph 4 Ayat 2') {
																		$jns = "non_pph_4";
																	} else {
																		$jns = "non_".str_replace(' ', '_', strtolower($dt['jns_pph']));
																	}
																} else if ($vw['is_ppn']=='1') {
																	if ($dt['jns_pph']=='Pph 4 Ayat 2') {
																		$jns = "pph_4";
																	} else {
																		$jns = str_replace(' ', '_', strtolower($dt['jns_pph']));
																	}
																}
																$akun = mssql_fetch_array(mssql_query("select ".$jns." as akun from settingAkun where id=1"));
																$pilih = ($dpos['val']==$dt['val']) ? "selected" : "";
																echo "<option value='$dt[val]#$akun[akun]' $pilih>$dt[jns_pph] ($dt[tarif_persen]%)</option>";
															}
															echo '
															</select>
															<input type="hidden" id="jns_pph_'.$y.'" value="'.$dpos['jns_pph'].'">
															<input type="hidden" id="tarif_persen_'.$y.'" value="'.$dpos['tarif_persen'].'">
															<input type="hidden" id="akun_pph_'.$y.'" value="'.$dpos['akun_pph'].'">
														</div>
														<div class="col-sm-3"> <label class="control-label">Nominal Dpp</label> 
															<input type="text" class="form-control number" id="nominal_'.$y.'" value="'.$dpos['nominal'].'" readonly> 
														</div>
														<div class="col-sm-3"> <label class="control-label">Nilai Pph</label> 
															<div class="input-group"> <input type="text" class="form-control" id="nilaiPph_'.$y.'" value="0" readonly>
																<span class="input-group-addon" style="padding: 0;min-width: 0;">
																<button type="button" style="padding: 2px 10px;border: 0;" onclick="delposHtg('.$y.');">
																	<i class="fa fa-minus"></i></button>
																</span>
															</div>
														</div>
														<div class="col-sm-4"> <label class="control-label">Keterangan Biaya</label>
															<input type="text" class="form-control" id="keteranganAkun_'.$y.'" maxlength="200"  value="'.$dpos['KeteranganAkun'].'"/>
														</div>
													</div>';
													
													
												echo '
													<script type="text/javascript">
														jQuery(document).ready(function($) {
															onload = cekRab(\''.$vw['kodedealer'].'\',\''.$dpos['pos_biaya'].'\',\''.$dpos['nominal'].'\');
														});
													</script>
												';
												
												$y++;
											}
										} else {
																						
											echo '<div class="form-group" id="formPos_'.$y.'"> 
													<div class="col-sm-2"> <label class="control-label">Tarif Pajak</label> 
														 <label class="control-label">Tarif Pajak</label>
														<select type="text" class="form-control" id="trfPajak_'.$y.'" name="trfPajak[]" onchange="trfPajak('.$y.');" disabled >';
														$sql = "select jns_pph,tarif_persen,(jns_pph+'#'+convert(varchar,tarif_persen)) val 
															from settingPph where npwp = '".$vw['is_ppn']."' order by idpph asc";
														$rsl = mssql_query($sql);
														echo "<option value='Non Pph#0#00000000'>Non Pph</option>";
														while ($dt = mssql_fetch_array($rsl)) {
															if ($vw['is_ppn']=='0') {
																if ($dt['jns_pph']=='Pph 4 Ayat 2') {
																	$jns = "non_pph_4";
																} else {
																	$jns = "non_".str_replace(' ', '_', strtolower($dt['jns_pph']));
																}
															} else if ($vw['is_ppn']=='1') {
																if ($dt['jns_pph']=='Pph 4 Ayat 2') {
																	$jns = "pph_4";
																} else {
																	$jns = str_replace(' ', '_', strtolower($dt['jns_pph']));
																}
															}
															$akun = mssql_fetch_array(mssql_query("select ".$jns." as akun from settingAkun where id=1"));
															$pilih = ($dpos['val']==$dt['val']) ? "selected" : "";
															echo "<option value='$dt[val]#$akun[akun]' $pilih>$dt[jns_pph] ($dt[tarif_persen]%)</option>";
														}
														echo '
														</select>
														<input type="hidden" id="jns_pph_'.$y.'" value="'.$dpos['jns_pph'].'">
														<input type="hidden" id="tarif_persen_'.$y.'" value="'.$dpos['tarif_persen'].'">
														<input type="hidden" id="akun_pph_'.$y.'" value="'.$dpos['akun_pph'].'">
													</div>
													<div class="col-sm-3"> <label class="control-label">Nominal Dpp</label> 
														<input type="text" class="form-control number" id="nominal_'.$y.'" value="'.$vw['dpp'].'" readonly> 
													</div>
													<div class="col-sm-3"> <label class="control-label">Nilai Pph</label> 
														<div class="input-group"> 
															<input type="text" class="form-control" id="nilaiPph_'.$y.'" value="0" readonly>
															<span class="input-group-addon" style="padding: 0;min-width: 0;">
															<button type="button" style="padding: 2px 10px;border: 0;" onclick="delposHtg('.$y.');">
																<i class="fa fa-minus"></i></button>
															</span>
														</div>
													</div>
													<div class="col-sm-4"> <label class="control-label">Keterangan Biaya</label>
														<input type="text" class="form-control" id="keteranganAkun_'.$y.'" maxlength="200"  value="'.$vw['keterangan'].'"/>
													</div>
												</div>';
											$y++;
											
										}
									?>
                                </span>
                                
                                
								<hr style="margin: 10px 0;" />
								<div class="form-group">
								    <label class="col-sm-2 control-label">Hutang setelah pajak</label>
								    <div class="col-sm-4">
								    	<input type="text" class="form-control number" id="htg_stl_pajak" value="<?php echo $vw['htg_stl_pajak']; ?>" readonly />
								    </div>
                                    <div class="col-sm-2">
                                    	<button type="button" class="btn-default btn get_number" style="padding: 3px 10px;">
                                        	<i class="fa fa-plus"></i> Add Pos Hutang</button>
                                     </div>
                                     <div class="col-sm-4">
                                            <input type="hidden" class="form-control" id="totDppHtg" value="0" readonly/>
                                     </div>
								</div>
								<div class="form-group">
								    <label class="col-sm-2 control-label">Keterangan</label>
								    <div class="col-sm-10">
								    	<input type="text" class="form-control" id="keterangan" value="<?php echo $vw['keterangan']; ?>" />
								    </div>
								</div>
							</section>
						<?php } else if ($vw['tipe']=='BIAYA') { ?>
							<section id="getForm">
								<div class="form-group" style="padding-top: 3px;">
								    <label class="col-sm-2 control-label">Status</label>
								    <div class="col-sm-4">
								        <select type="text" class="form-control" id="status" disabled>
								            <?php
								            	$opt = array('' => '- Pilih -', 'New' => 'New', 'Reject' => 'Reject');
								            	foreach ($opt as $key => $value) {
								            		$plh = ($vw['status']==$key)?"selected" : ""; 
								            		echo "<option value='$key' $plh>$value</option>";
								            	}
								            ?>
								        </select>
								    </div>
								    <label class="col-sm-2 control-label">No. Bukti</label>
								    <div class="col-sm-4" id="f_NoBuktiPengajuan">
								        <div class="input-group">
								        	<span class="input-group-addon">VP</span> 
								        	<input type="text" id="nobukti" class="form-control" value="<?php echo substr($vw[nobukti], 2,15); ?>" readonly />
								        </div>
								    </div>
								    <div class="col-sm-4" id="f_nobuktiReject" style="display: none;">
								    	<select type="text" class="form-control" id="nobukti_reject"></select>
								    </div>
								</div>
								<div class="form-group" id="f_alasanreject" style="display: none;">
								    <label class="col-sm-2 control-label">Alasan Reject</label>
								    <div class="col-sm-10"><input type="text" class="form-control" id="alasanreject" readonly /></div>
								</div>
								<div class="form-group">
								    <label class="col-sm-2 control-label">Divisi</label>
								    <div class="col-sm-4">
								    	<select id="divisi" class="form-control" disabled>
								    		<?php
								    			$KodeDealer = $vw['kodedealer'];
												$div = $vw['divisi'];
												if ($KodeDealer=='2010') { $is_dealer = "0"; } else { $is_dealer = "1"; }
												if ($div=='all') { $divisi = ""; } else { $divisi = "and nama_div = '".$div."'"; }
												$sql = "select * from sys_divisi where is_dealer = '".$is_dealer."' $divisi";
												$rsl = mssql_query($sql);
												echo "<option value=''>- Pilih -</option>";
												while ($dt = mssql_fetch_array($rsl)) {
													$plh = ($vw['divisi']==$dt['nama_div'])?"selected" : ""; 
													echo "<option value='".$dt['nama_div']."' $plh>".$dt['nama_div']."</option>";
												}
								    		?>
								    	</select>
								    </div>
								    <label class="col-sm-2 control-label">Nama Atasan</label>
								    <div class="col-sm-4">
								        <select id="IdAtasan" class="form-control" disabled="disabled">
								            <?php
												$KodeDealer = $vw['kodedealer'];
												$divisi = $vw['divisi'];
								            	$IdUser = $vw['userentry'];
												$atasan = mssql_fetch_array(mssql_query("select IdAtasan from sys_user where IdUser = '".$IdUser."'"));
												if ($atasan['IdAtasan']=='all') { $boss = ""; } else { $boss = "and IdUser = '".$atasan['IdAtasan']."'"; }
												#$sql = "select * from sys_user where (tipe = 'SECTION HEAD' or tipe = 'ADH') and divisi in ('".$divisi."','all') and KodeDealer = '".$KodeDealer."' $boss";
												$sql = "select * from sys_user where divisi in ('".$divisi."','all') and KodeDealer = '".$KodeDealer."' $boss";
												$rsl = mssql_query($sql);
												echo "<option value=''>- Pilih -</option>";
												while ($dt = mssql_fetch_array($rsl)) {
													$plhx = ($vw['IdAtasan']==$dt['IdUser'])?"selected" : ""; 
													echo "<option value='".$dt['IdUser']."' $plhx>".$dt['namaUser']."</option>";
												}
								            ?>
								        </select>
								    </div>
								</div>
								<hr style="margin: 10px 0;" />
							</section>
							<section id="getForm2">
								<div class="form-group">
								    <label class="col-sm-2 control-label">Tanggal Pengajuan</label>
								    <div class="col-sm-4">
								    	<input type="date" id="tgl_pengajuan" value="<?php echo $vw['tgl_pengajuan']; ?>" class="form-control" readonly />
								    </div>
								    <label class="col-sm-2 control-label">Tanggal Bayar</label>
								    <div class="col-sm-4">
								    	<input type="date" class="form-control" id="tgl_bayar" value="<?php echo $vw['tgl_bayar']; ?>" />
								    </div>
								</div>
								<div class="form-group">
								    <label class="col-sm-2 control-label">Upload File</label>
								    <div class="col-sm-4">
								        <div class="input-group">
								            <input type="text" id="upload_file" class="form-control" value="<?php echo $vw['upload_file']; ?>" disabled />
								            <span class="input-group-addon" style="padding: 0; min-width: 0;">
								                <button type="button" style="padding: 2px 10px; border: 0;" onclick="getFile(1);">
								                	<i class="fa fa-eye"></i>
								                </button>
								            </span>
								            <span class="input-group-addon" style="padding: 0; min-width: 0;">
								                <span class="fileinput-button">
								                    <span style="padding: 2px 10px; border: 0;">
								                    	<i class="fa fa-camera"></i>
								                    </span> 
								                    <input id="ups-upload_file" type="file" name="files[]"  accept=".xlsx,.xls,.jpg,.png,.pdf,.doc,.docx,.zip,.rar"/>
								                </span>
								            </span>
								        </div>
								    </div>
								    <label class="col-sm-2 control-label">Upload Faktur Pajak</label>
								    <div class="col-sm-4">
								        <div class="input-group">
								            <input type="text" id="upload_fp" class="form-control" value="<?php echo $vw['upload_fp']; ?>" disabled />
								            <span class="input-group-addon" style="padding: 0; min-width: 0;">
								                <button type="button" style="padding: 2px 10px; border: 0;" onclick="getFile(2);">
								                	<i class="fa fa-eye"></i>
								                </button>
								            </span>
								            <span class="input-group-addon" style="padding: 0; min-width: 0;">
								                <span class="fileinput-button">
								                    <span style="padding: 2px 10px; border: 0;">
								                    	<i class="fa fa-camera"></i>
								                    </span> 
								                    <input id="ups-upload_fp" type="file" name="files[]"  accept=".xlsx,.xls,.jpg,.png,.pdf,.doc,.docx,.zip,.rar"/>
								                </span>
								            </span>
								        </div>
								    </div>
								</div>
								<hr style="margin: 10px 0;" />
								<div class="form-group">
								    <label class="col-sm-2 control-label">Kode Vendor</label>
								    <div class="col-sm-4">
								        <div class="input-group">
								            <input type="text" name="kode_vendor" id="kode_vendor" value="<?php echo $vw['kode_vendor']; ?>" class="form-control" disabled />
								            <span class="input-group-addon" style="padding: 0; min-width: 0;">
								                <button type="button" style="padding: 2px 10px; border: 0;" onclick="getVendor();"><i class="fa fa-search"></i></button>
								            </span>
								        </div>
								    </div>
								    <label class="col-sm-2 control-label">Nama Vendor</label>
								    <div class="col-sm-4">
								    	<input type="text" name="namaVendor" id="namaVendor" value="<?php echo $vw['namaVendor']; ?>" class="form-control">
								    </div>
								</div>
								<div class="form-group">
								    <label class="col-sm-2 control-label">Metode Pembayaran</label>
								    <div class="col-sm-4">
								        <select type="text" class="form-control" id="metode_bayar">
								            <?php
								            	$opt = array('' => '- Pilih -','Transfer' => 'Transfer','Cash' => 'Cash','Pety Cash' => 'Pety Cash');
								            	foreach ($opt as $key => $value) {
								            		$plh = ($vw['metode_bayar']==$key)?"selected" : ""; 
								            		echo "<option value='$key' $plh>$value</option>";
								            	}
								            ?>
								        </select>
								    </div>
								    <label class="col-sm-2 control-label">Beneficary Account</label>
								    <div class="col-sm-4">
								    	<input type="text" class="form-control" id="benificary_account" value="<?php echo $vw['benificary_account']; ?>" readonly />
								    </div>
								</div>
								<div class="form-group">
								    <label class="col-sm-2 control-label">Nama Bank Penerima</label>
								    <div class="col-sm-4">
								    	<input type="text" class="form-control" id="nama_bank" value="<?php echo $vw['nama_bank']; ?>" readonly />
								    </div>
								    <label class="col-sm-2 control-label">Nama Pemilik</label>
								    <div class="col-sm-4">
								    	<input type="text" class="form-control" id="nama_pemilik" value="<?php echo $vw['nama_pemilik']; ?>" readonly />
								    </div>
								</div>
								<div class="form-group">
								    <label class="col-sm-2 control-label">Email Penerima</label>
								    <div class="col-sm-4">
								    	<input type="text" class="form-control" id="email_penerima" value="<?php echo $vw['email_penerima']; ?>" readonly />
								    </div>
								    <label class="col-sm-2 control-label">Nama Alias</label>
								    <div class="col-sm-4">
								    	<input type="text" class="form-control" id="nama_alias" value="<?php echo $vw['nama_alias']; ?>" readonly />
								    </div>
								</div>
								<div class="form-group">
								    <label class="col-sm-2 control-label">Nama Bank Pengirim</label>
								    <div class="col-sm-4">
								        <div class="input-group">
								            <input type="hidden" name="kode_bank_pengirim" id="kode_bank_pengirim" value="<?php echo $vw['kode_bank_pengirim']; ?>" class="form-control" disabled />
								            <input type="text" name="nama_bank_pengirim" id="nama_bank_pengirim" value="<?php echo $vw['nama_bank_pengirim']; ?>" class="form-control" disabled />
								            <span class="input-group-addon" style="padding: 0; min-width: 0;">
								                <button type="button" style="padding: 2px 10px; border: 0;" onclick="getBank();"><i class="fa fa-search"></i></button>
								            </span>
								        </div>
								    </div>
								    <label class="col-sm-2 control-label">Transfer From Account</label>
								    <div class="col-sm-4">
								    	<input type="text" class="form-control" id="tf_from_account" value="<?php echo $vw['tf_from_account']; ?>" readonly />
								    </div>
								</div>
								<div class="form-group">
								    <label class="col-sm-2 control-label">Realisasi Nominal</label>
								    <div class="col-sm-4">
								    	<input type="text" class="form-control number" id="realisasi_nominal" value="<?php echo $vw['realisasi_nominal']; ?>" />
								    </div>
								</div>
								<hr style="margin: 10px 0;" />
								<div class="form-group">
								    <label class="col-sm-2 control-label">
								    	<?php 
								    		$plh = ($vw['is_ppn']==1) ? "checked" : ""; 
								    		if ($vw['is_ppn']=='0') {
								    			$disabled = "disabled";
								    		} else {
								    			$disabled = "";
								    		}
								    	?> 
								    	<input type="checkbox" id="is_ppn" value="<?php echo $vw['is_ppn']; ?>" <?php echo $plh; echo $disabled; ?> /> PPN 
								    	<input type="hidden" id="tipeppn" value="<?php echo $vw['tipeppn']; ?>" />
								    </label>
								    <div class="col-sm-4">
								        <div class="input-group">
								            <span class="input-group-addon">Dpp</span> 
								            <input type="text" class="form-control number" id="dpp" value="<?php echo $vw['dpp']; ?>" readonly /> 
								            <span class="input-group-addon">Ppn  <span class="ppn_persen"></span></span> 
								            <input type="text" class="form-control number" id="ppn" value="<?php echo $vw['ppn']; ?>" readonly />
								        </div>
								    </div>
								</div>
								<div class="form-group">
								    <label class="col-sm-2 control-label">NPWP</label>
								    <div class="col-sm-4"><input type="text" class="form-control" id="npwp" value="<?php echo $vw['npwp']; ?>" readonly /></div>
								    <label class="col-sm-2 control-label">No. Faktur Pajak</label>
								    <div class="col-sm-4"><input type="text" class="form-control" id="no_fj" value="<?php echo $vw['no_fj']; ?>" readonly /></div>
								</div>
								<span id="f_posbiaya">
									<?php
										$spos = "select *,(jns_pph+'#'+convert(varchar,tarif_persen)) val from DataEvoPos where nobukti = '".$vw['nobukti']."'";
										$rpos = mssql_query($spos);
										$jmlpos = mssql_num_rows($rpos);
										$y = 1;
										$over = "0";
										if ($jmlpos>0) {
											while ($dpos = mssql_fetch_array($rpos)) {
											echo '
												<script type="text/javascript">
													jQuery(document).ready(function($) {
														onload = cekRab(\''.$vw['kodedealer'].'\',\''.$dpos['pos_biaya'].'\',\''.$dpos['nominal'].'\');
													});
												</script>
											';
											echo '
												<div class="form-group">
												    <div class="col-sm-3">
												        <label class="control-label">Pos Biaya</label>
												        <div class="input-group">
												            <input type="hidden" name="posbiaya[]" id="kodeAkun_'.$y.'" value="'.$dpos['pos_biaya'].'">
												            <input type="text" class="form-control" id="ketAkun_'.$y.'" value="'.$dpos['ketAkun'].'" readonly>
												          	<span class="input-group-addon" style="padding: 0;min-width: 0;">
												              <button type="button" style="padding: 2px 10px;border: 0;" onclick="getAkun('.$y.');">
												                <i class="fa fa-search"></i>
												              </button>
												          	</span>
												      	</div>
												    </div>
												    <div class="col-sm-3">
												        <label class="control-label">Nominal</label>
												        <input type="text" class="form-control number" id="nominal_'.$y.'" value="'.$dpos['nominal'].'" onchange="nominal('.$y.');">
												  	</div>
												    <div class="col-sm-2">
												        <label class="control-label">Tarif Pajak</label>
												        <select type="text" class="form-control" id="trfPajak_'.$y.'" onchange="trfPajak('.$y.');" disabled>';
												        $sql = "select jns_pph,tarif_persen,(jns_pph+'#'+convert(varchar,tarif_persen)) val 
											        		from settingPph where npwp = '".$vw['is_ppn']."' order by idpph asc";
														$rsl = mssql_query($sql);
														echo "<option value='Non Pph#0#00000000'>Non Pph</option>";
														while ($dt = mssql_fetch_array($rsl)) {
															if ($vw['is_ppn']=='0') {
																if ($dt['jns_pph']=='Pph 4 Ayat 2') {
																	$jns = "non_pph_4";
																} else {
																	$jns = "non_".str_replace(' ', '_', strtolower($dt['jns_pph']));
																}
															} else if ($vw['is_ppn']=='1') {
																if ($dt['jns_pph']=='Pph 4 Ayat 2') {
																	$jns = "pph_4";
																} else {
																	$jns = str_replace(' ', '_', strtolower($dt['jns_pph']));
																}
															}
															$akun = mssql_fetch_array(mssql_query("select ".$jns." as akun from settingAkun where id=1"));
															$pilih = ($dpos['val']==$dt['val']) ? "selected" : "";
															echo "<option value='$dt[val]#$akun[akun]' $pilih>$dt[jns_pph] ($dt[tarif_persen]%)</option>";
														}
												        echo '
												        </select>
												        <input type="hidden" id="jns_pph_'.$y.'" value="'.$dpos['jns_pph'].'">
												        <input type="hidden" id="tarif_persen_'.$y.'" value="'.$dpos['tarif_persen'].'">
												        <input type="hidden" id="akun_pph_'.$y.'" value="'.$dpos['akun_pph'].'">
												  	</div>
												    <div class="col-sm-2">
												        <label class="control-label">Nilai Pph</label>
														<input type="text" class="form-control number" id="nilaiPph_'.$y.'" value="'.$dpos['nilai_pph'].'" readonly>
												    </div>
													 <div class="col-sm-2"> 
													 	<label class="control-label">Keterangan Biaya</label> 
   														 <input type="text" class="form-control" id="keteranganAkun_'.$y.'" value="'.$dpos['KeteranganAkun'].'" maxlength="200"/>
													</div>
												</div>
											';
											$y++;
										}
										} else {
											
											/*echo '
												<script type="text/javascript">
													jQuery(document).ready(function($) {
														onload = cekRab(\''.$vw['kodedealer'].'\',\''.$dpos['pos_biaya'].'\',\''.$dpos['nominal'].'\');
													});
												</script>
											';*/
											echo '
												<div class="form-group">
												    <div class="col-sm-3">
												        <label class="control-label">Pos Biaya</label>
												        <div class="input-group">
												            <input type="hidden" name="posbiaya[]" id="kodeAkun_'.$y.'" value="'.$dpos['pos_biaya'].'">
												            <input type="text" class="form-control" id="ketAkun_'.$y.'" value="'.$dpos['ketAkun'].'" readonly>
												          	<span class="input-group-addon" style="padding: 0;min-width: 0;">
												              <button type="button" style="padding: 2px 10px;border: 0;" onclick="getAkun('.$y.');">
												                <i class="fa fa-search"></i>
												              </button>
												          	</span>
												      	</div>
												    </div>
												    <div class="col-sm-3">
												        <label class="control-label">Nominal</label>
												        <input type="text" class="form-control number" id="nominal_'.$y.'" value="'.$dpos['nominal'].'" onchange="nominal('.$y.');">
												  	</div>
												    <div class="col-sm-2">
												        <label class="control-label">Tarif Pajak</label>
												        <select type="text" class="form-control" id="trfPajak_'.$y.'" onchange="trfPajak('.$y.');" disabled>';
												        $sql = "select jns_pph,tarif_persen,(jns_pph+'#'+convert(varchar,tarif_persen)) val 
											        		from settingPph where npwp = '".$vw['is_ppn']."' order by idpph asc";
														$rsl = mssql_query($sql);
														echo "<option value='Non Pph#0#00000000'>Non Pph</option>";
														while ($dt = mssql_fetch_array($rsl)) {
															if ($vw['is_ppn']=='0') {
																if ($dt['jns_pph']=='Pph 4 Ayat 2') {
																	$jns = "non_pph_4";
																} else {
																	$jns = "non_".str_replace(' ', '_', strtolower($dt['jns_pph']));
																}
															} else if ($vw['is_ppn']=='1') {
																if ($dt['jns_pph']=='Pph 4 Ayat 2') {
																	$jns = "pph_4";
																} else {
																	$jns = str_replace(' ', '_', strtolower($dt['jns_pph']));
																}
															}
															$akun = mssql_fetch_array(mssql_query("select ".$jns." as akun from settingAkun where id=1"));
															$pilih = ($dpos['val']==$dt['val']) ? "selected" : "";
															echo "<option value='$dt[val]#$akun[akun]' $pilih>$dt[jns_pph] ($dt[tarif_persen]%)</option>";
														}
												        echo '
												        </select>
												        <input type="hidden" id="jns_pph_'.$y.'" value="'.$dpos['jns_pph'].'">
												        <input type="hidden" id="tarif_persen_'.$y.'" value="'.$dpos['tarif_persen'].'">
												        <input type="hidden" id="akun_pph_'.$y.'" value="'.$dpos['akun_pph'].'">
												  	</div>
												    <div class="col-sm-2">
												        <label class="control-label">Nilai Pph</label>
														<input type="text" class="form-control number" id="nilaiPph_'.$y.'" value="'.$dpos['nilai_pph'].'" readonly>
												    </div>
													 <div class="col-sm-2"> 
													 	<label class="control-label">Keterangan Biaya</label> 
   														 <input type="text" class="form-control" id="keteranganAkun_'.$y.'" value="'.$dpos['KeteranganAkun'].'" maxlength="200"/>
													</div>
												</div>
											';
											$y++;
										
										}
									?>
								</span>
								<div class="form-group">
								    <label class="col-sm-3 control-label">Total Dpp</label>
								    <div class="col-sm-3">
								    	<input type="text" class="form-control number" id="total_dpp" value="<?php echo $vw['total_dpp']; ?>" readonly />
								    </div>
								    <div class="col-sm-3">
								        <button type="button" class="btn-default btn get_number" style="padding: 3px 10px;">
								        	<i class="fa fa-plus"></i> Add Pos Biaya
								        </button>
								    </div>
								</div>
								<hr style="margin: 10px 0;" />
								<div class="form-group">
								    <label class="col-sm-2 control-label">Biaya yg harus dibayar</label>
								    <div class="col-sm-4">
								    	<input type="text" class="form-control number" id="biaya_yg_dibyar" value="<?php echo $vw['biaya_yg_dibyar']; ?>" readonly />
								    </div>
								</div>
								<div class="form-group">
								    <label class="col-sm-2 control-label">Keterangan</label>
								    <div class="col-sm-10">
								    	<input type="text" class="form-control" id="keterangan" value="<?php echo $vw['keterangan']; ?>" />
								    </div>
								</div>
							</section>
						<?php } ?>
					</div>
					<div class="panel-footer">
						<div class="row">
						    <div class="col-sm-10 col-sm-offset-2">
						        <div class="btn-toolbar">
						        	<?php if (isset($_POST['id'][0])) { ?>
										<button type="button" id="edit" class="btn-primary btn">Save</button>
									<?php } else { ?>
										<button type="button" id="new" class="btn-primary btn">Save</button>
									<?php } ?>
						            <button type="button" id="cancel" class="btn-default btn">Cancel</button>
						        </div>
						    </div>
						</div>
					</div>
				<?php } ?>
			</form>
		</div>
	</div>
</div>

<div class="modal fade modals" id="getVendor" tabindex="-1" role="dialog" aria-labelledby="getVendor" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h2 class="modal-title">Data Vendor</h2>
			</div>
			<div class="modal-body" style="padding: 0;" id="dataVendor"></div>
		</div>
	</div>
</div>

<div class="modal fade modals" id="getAkun" tabindex="-1" role="dialog" aria-labelledby="getAkun" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h2 class="modal-title">Data GL</h2>
			</div>
			<input type="hidden" id="tipeAkun">
			<input type="hidden" id="jenisAkun">
			<div class="modal-body" style="padding: 0;" id="dataAkunAja"></div>
		</div>
	</div>
</div>


<div class="modal fade modals" id="getAkunMaterai" tabindex="-1" role="dialog" aria-labelledby="getAkunMaterai" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h2 class="modal-title">Data GL Materai</h2>
			</div>
			<input type="hidden" id="tipeAkunMaterai">
			<input type="hidden" id="jenisAkunMaterai">
			<div class="modal-body" style="padding: 0;" id="dataAkunAjaMaterai"></div>
		</div>
	</div>
</div>


<div class="modal fade modals" id="getBank" tabindex="-1" role="dialog" aria-labelledby="getBank" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h2 class="modal-title">Data Bank</h2>
			</div>
			<div class="modal-body" style="padding: 0;" id="dataBank"></div>
		</div>
	</div>
</div>

<div class="modal fade modals" id="formImportHutang" tabindex="-1" role="dialog" aria-labelledby="formImportHutang" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
            <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h2 class="modal-title">Import Excel Tagihan Hutang</h2>
			</div>
			<div class="modal-body" style="padding: 0; height:200px;" >
            	<form method="post" enctype="multipart/form-data" id="formimport">
                <input type="hidden" id="kode_vendorx" name="kode_vendorx" />
                <input type="hidden" id="nobuktix" name="nobuktix" />
                <input type="hidden" id="KodeDealerx" name="KodeDealerx" />
                <input type="hidden" id="tipehutangx" name="tipehutangx" />
                  <div class="form-group">
                        <label class="col-sm-2 control-label">File upload</label>
                        <div class="col-sm-4">
                            <input type="file" name="filex" id="filex" accept='.xlsx,.xls' class="form-control">
                        </div>
                    </div>
                     <div class="form-group">
                        <div class="col-md-12 form-group">
                            <button type="button" id="btnImport" class="btn btn-primary mb-2" onclick="ExportToTable();">Import</button>
                        </div>
                    </div>
                </form>
            </div>
            
		</div>
	</div>
</div>

<span id="sendIntra"></span>


<script type="text/javascript">
$(document).ready(function(){
	

  	$("#btnImport2").click(function(){
	//$("form#formElement").submit(function(){
		var formData = new FormData();
		formData.append('file', $('#filex')[0].files[0]);
		
		$.ajax({
			   url: 'system/control/importhutang.php',
			   type : 'POST',
			   data : formData,
			   processData: false,  // tell jQuery not to process the data
			   contentType: false,  // tell jQuery not to set contentType
			   success : function(data) {
				   console.log(data);
				   alert(data);
			   }
		});
	/*	$.ajax({ 
			url: 'system/control/importhutang.php',
			data: $('#formimport').serialize(),
			type: 'post',
			beforeSend: function(){
				onload = showLoading();
				$("#filename").attr('disabled','disabled');
				$("#btnImport").attr('disabled','disabled');
				$("#btnImport").html('Loading...');
			},
			success: function(output) {
				onload = hideLoading();
				//$('#kode_vendorx').val('');
				$("#filex").removeAttr('disabled','disabled');
				$("#btnImport").removeAttr('disabled','disabled');
				$('#formImportHutang').modal('hide'); 
				$('.gethutang').flexOptions({
					url:'system/data/gethutang.php', 
					newp: 1,
					params:[
						{ name:'action', value: 'import' },
						{ name:'KodeLgn', value: $('#kode_vendorx').val() },
						{ name:'KodeDealer', value: $('#KodeDealerx').val() },
						{ name:'tipehutang', value: $('#tipehutangx').val() }, 
						{ name:'nobukti', value: $('#nobuktix').val() }
						
					]
				}).flexReload();
				
			}
		});*/
		return false;
	});
});		
		
	function ExportToTable() {  
		var regex = /^([a-zA-Z0-9\s_\\.\-:])+(.xlsx|.xls)$/;  
		/*Checks whether the file is a valid excel file*/  
		if (regex.test($("#filex").val().toLowerCase())) {  
			var xlsxflag = false; /*Flag for checking whether excel is .xls format or .xlsx format*/  
			if ($("#filex").val().toLowerCase().indexOf(".xlsx") > 0) {  
				xlsxflag = true;
			}
			/*Checks whether the browser supports HTML5*/  
			if (typeof (FileReader) != "undefined") {  
				var reader = new FileReader();  
				reader.onload = function (e) {  
					var data = e.target.result;  
					/*Converts the excel data in to object*/  
					if (xlsxflag) {  
						var workbook = XLSX.read(data, { type: 'binary',cellText:false,cellDates:true} );  
					} else {  
						var workbook = XLS.read(data, { type: 'binary',cellText:false,cellDates:true} );  
					}
					/*Gets all the sheetnames of excel in to a variable*/  
					var sheet_name_list = workbook.SheetNames;  
					var cnt = 0; /*This is used for restricting the script to consider only first sheet of excel*/  
					sheet_name_list.forEach(function (y) { /*Iterate through all sheets*/  
						/*Convert the cell value to Json*/  
						if (xlsxflag) {  
							var exceljson = XLSX.utils.sheet_to_json(workbook.Sheets[y] , {header:2,raw:true, dateNF:"DD/MM/YYYY"});  
						} else {  
							var exceljson = XLS.utils.sheet_to_row_object_array(workbook.Sheets[y] , {header:2,raw:true, dateNF:"DD/MM/YYYY"});  
						}
						if (exceljson.length > 0 && cnt == 0) {  
							BindTable(exceljson);  
							cnt++;  
						}
					});  
					// $('#exceltable').show();  
				}
				if (xlsxflag) {/*If excel file is .xlsx extension than creates a Array Buffer from excel*/  
					reader.readAsArrayBuffer($("#filex")[0].files[0]);  
				} else {  
					reader.readAsBinaryString($("#filex")[0].files[0]);  
				}
			} else {  
				alert("Sorry! Your browser does not support HTML5!");  
			}
		} else {  
			alert("Please upload a valid Excel file!");  
		}
	}

	function BindTable(jsondata) {/*Function used to convert the JSON array to Html Table*/  
		//var IdUser = $('#IdUser').val();
		var columns = BindTableHeader(jsondata); /*Gets all the column headings of Excel*/  
		var hihi = "";
		for (var i = 0; i < jsondata.length; i++) {  
			var hehe = "";
			for (var colIndex = 0; colIndex < columns.length; colIndex++) {  
				var cellValue = jsondata[i][columns[colIndex]];  
				if (cellValue == null)  
					cellValue = "";  
					hehe += cellValue+";";
			}  
			hihi += hehe.slice(0,-1)+",";
		}
		
		onload = showLoading();
		$("#filename").attr('disabled','disabled');
		$("#btnImport").attr('disabled','disabled');
		$("#btnImport").html('Loading...');
		
		$('.gethutang').flexOptions({
			url:'system/data/gethutang.php', 
			newp: 1,
			params:[
				{ name:'action', value: 'import' },
				{ name:'KodeLgn', value: $('#kode_vendorx').val() },
				{ name:'KodeDealer', value: $('#KodeDealerx').val() },
				{ name:'tipehutang', value: $('#tipehutangx').val() }, 
				{ name:'nobukti', value: $('#nobuktix').val() },
				{ name:'nobukti', value: $('#nobuktix').val() }, 
				{ name:'data', value: hihi }
				
			],
			onSuccess:function(){				
				onload = hideLoading();
				var ket = $("#namaVendor").val();
				/*if ($("#byr-"+id).is(":checked")) {
					$("#txtnom_"+id).html(addCommas(val));
				} else {
					$("#txtnom_"+id).html('0');
				}*/
				
				var count = $("input[name='byr[]']").length;
				var totNom = 0; var newKet = ",INV: "; $("#keterangan").val('');
				for (var i = 1; i <= count; i++) {
					if ($("#byr-"+i).is(":checked")) {
						totNom += parseInt($("#byr-"+i).val());
						newKet += $("#NoFaktur-"+i).val()+";";
					}
				}
				$("#keterangan").val(ket.toUpperCase()+newKet);
				$("#realisasi_nominal").val(addCommas(totNom));
				onload = getDppHtg();
				var dpp = $("#dpp").val();
				var con = $("select[name='trfPajak[]']").length;
				for (var i = 1; i <= con; i++) {
					$("#nominal_"+i).val(dpp);
				}
				onload = htg_yg_dibyar();		
				
				$("#filex").removeAttr('disabled','disabled');
				$("#btnImport").removeAttr('disabled','disabled');				
				$("#btnImport").attr('enabled','enabled');
				$("#btnImport").html('Import');
				$('#formImportHutang').modal('hide'); 
				
				alert('Sukses upload hutang !!');				
			}
		}).flexReload();
		
		
		/*$.ajax({ 
			url: 'system/control/importhutang.php',
			data: {
				'kodelgn': $('#kode_vendorx').val(), 
				'nobukti': $('#nobuktix').val(), 
				'KodeDealer': $('#KodeDealerx').val(), 
				'tipehutang': $('#tipehutangx').val(), 
				'data': hihi
			},
			type: 'post',
			beforeSend: function(){
				// onload = showLoading();
				
				
			},
			success: function(output) {
				// onload = hideLoading();
				//$('#kode_vendorx').val('');
				$("#filex").removeAttr('disabled','disabled');
				$("#btnImport").removeAttr('disabled','disabled');				
				$("#btnImport").attr('enabled','enabled');
				$("#btnImport").html('Import');
				$('#formImportHutang').modal('hide'); 
				
				alert('Sukses upload hutang !!');
				
			}
		});*/
	}
 
	function BindTableHeader(jsondata) {/*Function used to get all column names from JSON and bind the html table header*/  
		var columnSet = [];  
		for (var i = 0; i < jsondata.length; i++) {  
			var rowHash = jsondata[i];  
			for (var key in rowHash) {  
				if (rowHash.hasOwnProperty(key)) {  
					if ($.inArray(key, columnSet) == -1) {/*Adding each unique column names to a variable array*/  
						columnSet.push(key);  
					}  
				}
			}  
		}
		return (columnSet);  
	}
</script>
