<?php 
	require_once ('system/inc/permission.php');
	$id = isset($_REQUEST['id']) ? $_REQUEST['id'][0] : NULL;
	$vw = mssql_fetch_array(mssql_query("select a.*,b.namaUser namaUserAtasan, c.namaUser, c.department, c.divisi, c.namauser useraju,
								case when a.tipe='HUTANG' then a.htg_stl_pajak else a.biaya_yg_dibyar end as totBayar
								from DataEvo a 
								left join sys_user b on b.IdUser=a.IdAtasan 
								left join sys_user c on c.IdUser=a.userentry
								where evo_id = '".$id."'"));
	$useraju = $vw['useraju']." (".$vw['divisi']."/".$vw['department'].")";

	$skipdir = mssql_fetch_array(mssql_query('select skip_direksi,skip_direksi2 from settingAkun where id=1'));
	
?>
<link rel="stylesheet" type="text/css" href="assets/plugins/flexii/css/flexigrid.pack.css" />
<link rel="stylesheet" type="text/css" href="assets/css/jquery.fileupload.css" />
<link rel="stylesheet" href="https://www.jqueryscript.net/demo/Multifunction-Customizable-Modal-Plugin-For-jQuery-ssi-modal/ssi-modal/styles/ssi-modal.css"/> 
<style type="text/css">
	#datarapb th { font-weight: bold;text-align: center; }
	#datarapb td { text-align: right;padding: 0 5px; }
	.input-group-addon {padding: 0;min-width: 0;padding: 2px 10px; }
</style>
<script type="text/javascript">
	jQuery(document).ready(function($) {
		$(".acc-menu #2").css('display','block');
		
		$('#hutangjurnal').click(function(event){
			onload = showLoading();
			$('.getjurnalhutang').flexOptions({
				url:'system/data/getjurnalhutang.php', 
				newp: 1,
				params:[{name:'nobukti', value: $("#nobukti").val()} ,{name:'kode_vendor', value: $("#kode_vendor").val()}]
			}).flexReload();
			onload = hideLoading();
		});
		
		var trfPajak = $("select[name='trfPajak[]']").length;
		for (var i = 1; i <= trfPajak; i++) {
			$("#trfPajak_"+i).attr('disabled','disabled');
			$("#btn-poshtg_"+i).attr('disabled','disabled');
			$("#btn-pos_"+i).attr('disabled','disabled');
			$("#nominal_"+i).attr('disabled','disabled');
			$("#keteranganAkun_"+i).attr('disabled','disabled');
		}
		$("#is_ppn").attr('disabled','disabled');
		
		$('#back').click(function(event){
			document.location.href="transaksi-validasi";
		});

		$('#approve').click(function(event){
			bootbox.dialog({
			    closeButton : false,
				className : "resize",
			    message: "Apakah anda yakin akan melakukan proses validasi?",
			    title: "Validasi Pengajuan Evo Pay",
			    buttons: {
			        main: {
			            label: "Yes",
			            className: "btn-sm btn-primary",
			            callback: function() {
			            	var level = $("#level").val();
							var over = $("#over").val();
							if (level=='ADH' && over=='1') {
								bootbox.dialog({
								    closeButton : false,
									className : "resize",
								    message: "Nilai tagihan melebihi budget!<br/>Lanjutkan pengajuan?",
								    title: "Validasi Pengajuan Evo Pay",
								    buttons: {
								        main: {
								            label: "Yes",
								            className: "btn-sm btn-primary",
								            callback: function() {
								            	var note_adh = $("#note_adh").val();
								            	if (note_adh=='') {
								            		onload = needValue('Validasi Pengajuan Evo Pay','Required Note ADH!');
								            	} else {
								            		onload = approval();
								            	}
								            }
								        },
										cancel: {
										    label: 'No',
										    className: 'btn-sm btn-danger'
										}
								    }
								});
							} else {
								onload = approval();
							}
			            }
			        },
			        cancel: {
			            label: 'No',
			            className: 'btn-sm btn-danger'
			        }
			    }
			});
		});

		$('#reject').click(function(event){
			bootbox.dialog({
			    closeButton : false,
				className : "resize",
			    message: "Apakah anda yakin akan melakukan proses reject?",
			    title: "Validasi Pengajuan Evo Pay",
			    buttons: {
			        main: {
			            label: "Yes",
			            className: "btn-sm btn-primary",
			            callback: function() {
			            	bootbox.dialog({
							    closeButton : false,
								className : "resize",
							    message: "<textarea class='form-control' id='ketreject' rows='10' cols='50'></textarea>",
							    title: "Alasan Reject",
							    buttons: {
							        main: {
							            label: "Save",
							            className: "btn-sm btn-primary",
							            callback: function() {
											var IdUser = $("#IdUser").val();
											var level = $("#level").val();
											var KodeDealer = $("#KodeDealer").val();
											var Tipe = $("#Tipe").val();
											var nobukti = $("#nobukti").val();
											var ketreject = $("#ketreject").val();
											var note_sectionhead = $("#note_sectionhead").val();
											var note_adh = $("#note_adh").val();
											var note_branch_manager = $("#note_branch_manager").val();
											var note_om = $("#note_om").val();
											if (ketreject=='') {
												onload = needValue('Validasi Pengajuan Evo Pay','Required Alasan Reject!');
											} else {
												$.ajax({ 
													url: 'system/control/validasi.php',
													data: { 
														action:'validasi', 'IdUser': IdUser, 'level': level, 'KodeDealer': KodeDealer, 
														'Tipe': Tipe, 'nobukti': nobukti, 'val': 'Reject', 'ketreject' : ketreject,
														'note_sectionhead': note_sectionhead, 'note_adh': note_adh, 
														'note_branch_manager': note_branch_manager, 'note_om': note_om
													},
													type: 'post',
													beforeSend: function(){
														onload = showLoading();
													},
													success: function(output) {
														onload = hideLoading();
														bootbox.dialog({
														    closeButton : false,
															className : "resize",
														    message: output,
														    title: "Validasi Pengajuan Evo Pay",
														    buttons: {
														        main: {
														            label: "Ok",
														            className: "btn-sm btn-primary",
														            callback: function() {
														            	document.location.href="transaksi-validasi";
														            }
														        }
														    }
														});
													}
												});
											}
							            }
							        },
							        cancel: {
							            label: 'Cancel',
							            className: 'btn-sm btn-danger',
							            callback: function() {
											$("#ketreject").val('');
							            }
							        }
							    }
							});
			            }
			        },
			        cancel: {
			            label: 'No',
			            className: 'btn-sm btn-danger'
			        }
			    }
			});
		});

		$('#update').click(function(event){
			var IdUser = $("#IdUser").val();
			var level = $("#level").val();
			var KodeDealer = $("#KodeDealer").val();
			var Tipe = $("#Tipe").val();
			var nobukti = $("#nobukti").val();
			var tgl_bayar = $("#tgl_bayar").val();
			if (Tipe=='HUTANG') {
				var trfPajak = $("select[name='trfPajak[]']").length;
				var trf_pajak = "";				
				var nominal_pph_ = 0;
				for (var i = 1; i <= trfPajak; i++) {
					var nominal = $("#nominal_"+i).val();
					var jns_pph = $("#jns_pph_"+i).val();
					var tarif_persen = $("#tarif_persen_"+i).val();
					var nilaiPph = $("#nilaiPph_"+i).val();
					var akun_pph = $("#akun_pph_"+i).val();
					var keteranganAkun = $("#keteranganAkun_"+i).val();
					trf_pajak += nominal+"#"+jns_pph+"#"+tarif_persen+"#"+nilaiPph+"#"+akun_pph+"#"+keteranganAkun+"_cn_";
					
					var nilai_Pph = document.getElementById('nilaiPph_'+i).value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
					nominal_pph_ = parseInt(nominal_pph_) + parseInt(nilai_Pph);
					
				}
				var trf_pajak_ = trf_pajak.slice(0,-4);
				var htg_stl_pajak = $("#htg_stl_pajak").val();
				var keterangan = $("#keterangan").val();
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
				//alert(dpp_ + '__' + ppn_ + '__' + nominal_pph_ + '__' +total_ + '__' + htg_stl_pajak_);
				
				if (parseInt(dpp_)!=parseInt(totDppHtg_)) {
					onload = needValue('Pengajuan Evo Pay','Nominal Dpp tidak Balance!');
				/*} else if (parseInt(htg_stl_pajak_)!=parseInt(total_)) {
					onload = needValue('Pengajuan Voucher Payment','Nominal Dpp & PPN tidak Balance!');	*/
				} else {
					var ppn = $("#ppn").val();
					var dpp = $("#dpp").val();
					var tipe_materai = $("#tipe_materai").val();
					var nominal_materai = $("#nominal_materai").val();
					var tipeppn = $("#tipeppn").val();
					
					$.ajax({ 
						url: 'system/control/validasi.php',
						data: { 
							action:'edit-hutang', 'IdUser': IdUser, 'level': level, 'KodeDealer': KodeDealer, 
							'Tipe': Tipe, 'nobukti': nobukti, 'tgl_bayar': tgl_bayar, 'trfPajak': trf_pajak_, 
							'htg_stl_pajak' : htg_stl_pajak,'keterangan' : keterangan,
							'ppn' : ppn,'dpp' : dpp, 'tipeppn' : tipeppn, 'tipe_materai' : tipe_materai, 'nominal_materai' : nominal_materai
						},
						type: 'post',
						beforeSend: function(){
							onload = showLoading();
						},
						success: function(output) {
							onload = hideLoading();
							bootbox.dialog({
							    closeButton : false,
								className : "resize",
							    message: output,
							    title: "Validasi Pengajuan Evo Pay",
							    buttons: {
							        main: {
							            label: "Ok",
							            className: "btn-sm btn-primary",
							            callback: function() {
							            	$( "#cancel" ).trigger( "click" );
							            }
							        }
							    }
							});
						}
					});
				}
			} else if (Tipe=='BIAYA') {
				var is_ppn = $("#is_ppn").val();
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
				
				var real_nom_ = document.getElementById('realisasi_nominal').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
				
				// perhitungan cek ppn				
				var nom = document.getElementById('realisasi_nominal').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
				var ppn_ = document.getElementById('ppn').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
				var dpp_ = document.getElementById('dpp').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
				var total_dpp_ = document.getElementById('total_dpp').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
				var biaya_yg_dibyar_ = document.getElementById('biaya_yg_dibyar').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
				
				var total_ = 0;
				if ($('#is_ppn').prop("checked")) {
					var tipeppn = $("#tipeppn").val();						
					//var total_ = parseInt(dpp_) + parseInt(ppn_) - parseInt(nominal_pph_);
					is_ppn = 1;
				} else {	
				//	var total_ = parseInt(dpp_) - parseInt(nominal_pph_);
					is_ppn = 0;
				}
				var total_ = parseInt(dpp_) + parseInt(ppn_) - parseInt(nominal_pph_);
				
				//alert(total_dpp_ + '__' + dpp_);
				//alert(is_ppn);	
				if (is_ppn=='0' && parseInt(total_dpp_)!=parseInt(real_nom_)) {
					onload = needValue('Pengajuan Evo Pay','Nominal Belom Balance!');
				} else if (is_ppn=='1' && parseInt(total_dpp_)!=parseInt(dpp_)) {
					onload = needValue('Pengajuan Evo Pay','Nominal Belom Balance!');
				} else if (posbiaya_=='') {
					onload = needValue('Pengajuan Evo Pay','Required Pos Biaya!');
				} else if (parseInt(biaya_yg_dibyar_)!=parseInt(total_)) {
					onload = needValue('Pengajuan Voucher Payment','Nominal Dpp & PPN tidak Balance!');	
				} else {
					$("#is_ppn").attr('disabled','disabled');
					var ppn = $("#ppn").val();
					var dpp = $("#dpp").val();
					
					$.ajax({ 
						url: 'system/control/validasi.php',
						data: { 
							action:'edit-biaya', 'IdUser': IdUser, 'level': level, 'KodeDealer': KodeDealer, 
							'Tipe': Tipe, 'nobukti': nobukti, 'tgl_bayar': tgl_bayar, 'posbiaya': posbiaya_, 
							'keterangan' : keterangan,'total_dpp' : total_dpp,'biaya_yg_dibyar' : biaya_yg_dibyar,
							'ppn' : ppn,'dpp' : dpp, 'is_ppn' : is_ppn
						},
						type: 'post',
						beforeSend: function(){
							onload = showLoading();
						},
						success: function(output) {
							onload = hideLoading();
							bootbox.dialog({
							    closeButton : false,
								className : "resize",
							    message: output,
							    title: "Validasi Pengajuan Evo Pay",
							    buttons: {
							        main: {
							            label: "Ok",
							            className: "btn-sm btn-primary",
							            callback: function() {
							            	$( "#cancel" ).trigger( "click" );
							            }
							        }
							    }
							});
						}
					});
				}
			}
		});

		$('#edit').click(function(event){
			$("#valEdit").val('1');
			$("#approve").css('display','none');
			$("#edit").css('display','none');
			$("#update").css('display','');
			$("#cancel").css('display','');
			$("#tgl_bayar").removeAttr('readonly');
			$("#tipeppn").removeAttr('disabled');
			$("#btn-kodeAkun").removeAttr('disabled');
			$("#btn-kodeAkunMaterai").removeAttr('disabled');
			$("#btn-addpos").css('display','');
			$("#keterangan").removeAttr('disabled');
			$("#tipe_materai").removeAttr('disabled');
			
			$("#dpp").removeAttr('readonly');
						
			var trfPajak = $("select[name='trfPajak[]']").length;
			for (var i = 1; i <= trfPajak; i++) {
				$("#trfPajak_"+i).removeAttr('disabled');
				$("#btn-poshtg_"+i).removeAttr('disabled');
				$("#btn-pos_"+i).removeAttr('disabled');
				$("#nominal_"+i).removeAttr('disabled');
				$("#keteranganAkun_"+i).removeAttr('disabled');
			}
			
			var is_ppn = $("#is_ppn").val();
			if (is_ppn==1) {
				$("#ppn").removeAttr('readonly');
			} else {				
				$("#ppn").attr('readonly','readonly');
			}
				
			function nominal(y){
				var nom = document.getElementById('nominal_'+y).value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
				var trf = $("#tarif_persen_"+y).val();
				var nilai = Math.floor((parseInt(nom) * parseInt(trf))/100);
				$("#nilaiPph_"+y).val(addCommas(nilai));
				onload = biaya_yg_dibyar();
			}
			
			<?php if  ($_SESSION['level']=='TAX' or $_SESSION['level']=='ACCOUNTING') { ?>
				$("#is_ppn").removeAttr('disabled');
			<?php } ?>
			
		});

		$('#cancel').click(function(event){
			$("#valEdit").val('0');
			$("#approve").css('display','');
			$("#edit").css('display','');
			$("#update").css('display','none');
			$("#cancel").css('display','none');
			$("#tgl_bayar").attr('readonly','readonly');
			$("#tipeppn").attr('disabled','disabled');
			$("#tipe_materai").attr('disabled','disabled');
			$("#btn-kodeAkun").attr('disabled','disabled');
			$("#btn-kodeAkunMaterai").attr('disabled','disabled');
			$("#dpp").attr('readonly','readonly');
			$("#ppn").attr('readonly','readonly');
			$("#btn-addpos").css('display','none');
			$("#keterangan").attr('disabled','disabled');
			var trfPajak = $("select[name='trfPajak[]']").length;
			for (var i = 1; i <= trfPajak; i++) {
				$("#trfPajak_"+i).attr('disabled','disabled');
				$("#btn-poshtg_"+i).attr('disabled','disabled');
				$("#btn-pos_"+i).attr('disabled','disabled');
				$("#nominal_"+i).attr('disabled','disabled');
				$("#keteranganAkun_"+i).attr('disabled','disabled');
			}
			$("#is_ppn").attr('disabled','disabled');
		});

		$('#btn-tab1').click(function(event){
			$(".panel-footer").css('display','block');
		});

		$('#btn-tab2').click(function(event){
			$(".panel-footer").css('display','none');
		});

		$('.get_number').on('click',function(){
			onload = addpos();
			$('.number').number( true, 0 );
		});
		$('.get_numberhutang').on('click',function(){
			onload = addposhutang();
			$('.number').number( true, 0 );
		});
		$('.number').number( true, 0 );
		
		
		$('#is_ppn').change(function() {
			 if(this.checked) {
				var tipeppn = $("#tipeppn").val();
				var persen_ppn = $("#ppn_persen").val();
							
											
				var nom = document.getElementById('realisasi_nominal').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
				//var dpp = document.getElementById('dpp').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
				//var ppn = document.getElementById('ppn').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
				
				//var dpp  = Math.round((10/11)*nom);
		    	//var ppn = Math.round((1/11)*nom);
				var dpp = Math.round(parseInt(nom) * (100 / (100 + parseInt(persen_ppn))));
				var ppn = Math.round(parseInt(nom) * (parseInt(persen_ppn) / (100 + parseInt(persen_ppn))));
							
				$("#ppn").removeAttr('readonly');
				$("#dpp").val(dpp);
				$("#ppn").val(ppn);
							
			} else {
				var nom = document.getElementById('realisasi_nominal').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
				var ppn = 0;
				var dpp = nom;
				
				$("#ppn").attr('readonly','readonly');
				$("#dpp").val(dpp);
				$("#ppn").val(ppn);
			}
			
		});
		
		var tipe_materai = $('#tipe_materai').val();
		if (tipe_materai=="I") { // include
			$('#div_materai').hide();			
		} else if (tipe_materai=="E") { // exclude
			$('#div_materai').show();
		}
		
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
		
	
	});
	
	function approval(){
		var IdUser = $("#IdUser").val();
		var level = $("#level").val();
		var KodeDealer = $("#KodeDealer").val();
		var Tipe = $("#Tipe").val();
		var nobukti = $("#nobukti").val();
		var metode_bayar = $("#metode_bayar").val();
		var note_sectionhead = $("#note_sectionhead").val();
		var note_adh = $("#note_adh").val();
		var note_branch_manager = $("#note_branch_manager").val();
		var note_om = $("#note_om").val();
		var note_dept_head = $("#note_dept_head").val();
		var note_div_head = $("#note_div_head").val();
		var note_direksi = $("#note_direksi").val();
		var note_direksi2 = $("#note_direksi2").val();
		var note_dept_head_fin = $("#note_dept_head_fin").val();
		var note_div_head_fast = $("#note_div_head_fast").val();
		var over = $("#over").val();
		var note_deptterkait = $("#note_deptterkait").val();
		
		$.ajax({ 
			url: 'system/control/validasi.php',
			data: { 
				action:'validasi', 'IdUser': IdUser, 'level': level, 'KodeDealer': KodeDealer, 
				'Tipe': Tipe, 'nobukti': nobukti, 'val': 'Accept', 'metode_bayar' : metode_bayar,
				'note_sectionhead': note_sectionhead, 'note_adh': note_adh, 
				'note_branch_manager': note_branch_manager, 'note_om': note_om,
				'note_dept_head': note_dept_head,'note_div_head': note_div_head,'note_direksi': note_direksi,'note_direksi2': note_direksi2,
				'note_dept_head_fin': note_dept_head_fin,'note_div_head_fast': note_div_head_fast, 'over' : over,
				'note_deptterkait': note_deptterkait
			},
			type: 'post',
			beforeSend: function(){
				onload = showLoading();
			},
			success: function(output) {
				var msg = output.split("#"); 
				// $pesan .= "Transaksi telah berhasil di ".$val."!#".$dt['nobukti']."#".$n_bodyIntra."#".$n_nik."#".$n_email."#".$n_no_tlp."#".$n_bodyWa;
		    	var kata1 = msg[0];
				
				if (kata1!="0") {
				
					var ajax1 = $.ajax({ 
						url: 'email.php',
						//data: {'id': msg[2]},
						data: {'id': msg[1]},
						success: function(result) {}
					});
					//var nik = msg[4].split(";");
					//var pesan = msg[3].split(";");
					var nik = msg[3].split(";");
					var pesan = msg[2].split(";");
					
					for (var i = 0; i < nik.length; i++) {
						if (nik[i]!='') {
							var url = "https://nasmoco.net/notifevopay/pushnotif_new.php?nik="+nik[i]+"&pesan="+pesan[i]+"&sumber=EVOPAY&nobukti="+msg[1]+"&sender=evopay&judul=Validasi Voucher Payment";
							//$("#myFrame").attr('src', url);
							//document.getElementById('myFrame').src = document.getElementById('myFrame').src
							//$("#myFrame").attr('src', url);
							//document.getElementById('myFrame').src = document.getElementById('myFrame').src
							var ajax1 = $.ajax({ 
								url: url,
								//data: {'id': msg[2]},
								//data: {'id': msg[1]},
								success: function(result) {}
							});
						}
					}
	
					onload = hideLoading();
					bootbox.dialog({
						closeButton : false,
						className : "resize",
						message: "Validasi Voucher Payment " + msg[1] + " telah tersimpan. Kirim notifikasi WhatsApp?",
						title: "Validasi Voucher Payment",
						buttons: {
							confirm: {
								label: 'Yes',
								className: "btn-sm btn-primary",
								callback: function () {
									onload = showLoading();
									//var phone = msg[6].split(";");
									//var text = msg[7].split(";");
									var phone = msg[5].split(";");
									var text = msg[6].split(";");
									for (var i = 0; i < nik.length; i++) {
										if (phone[i]!='') {
											window.open('https://api.whatsapp.com/send?phone='+phone[i]+'&text='+text[i]);
										}
									}
									onload = hideLoading();
									document.location.href="transaksi-validasi";
								}
							},
							cancel: {
								label: 'No',
								className: 'btn-sm btn-danger',
								callback: function () {
									document.location.href="transaksi-validasi";
								}
							}
						}
					});
					
				} else {
					
					onload = hideLoading();
					bootbox.dialog({
						closeButton : false,
						className : "resize",
						message: "Validasi Voucher Payment " + msg[1],
						title: "Validasi Voucher Payment",
						buttons: {
							ok: {
								label: 'Ok',
								className: 'btn-sm btn-danger',
								callback: function () {
									document.location.href="home";
								}
							}
						}
					});
					
				}
			}
		});
	}
	function addpos(){
		var keterangan = $("#keterangan").val();
		
		var count = $("input[name='posbiaya[]']").length;
		var y = count + 1;
		$("#f_posbiaya").append('<div class="form-group" id="formPos_'+y+'"> <div class="col-sm-3"> <label class="control-label">Pos Biaya</label> <div class="input-group"> <input type="hidden" name="posbiaya[]" id="kodeAkun_'+y+'"/> <input type="text" class="form-control" id="ketAkun_'+y+'" readonly/> <span class="input-group-addon" style="padding: 0; min-width: 0;"> <button type="button" style="padding: 2px 10px; border: 0;" onclick="getAkun('+y+');"><i class="fa fa-search"></i></button> </span> </div></div><div class="col-sm-3"><label class="control-label">Nominal</label> <input type="text" class="form-control number" id="nominal_'+y+'" value="0"/></div><div class="col-sm-2"> <label class="control-label">Tarif Pajak</label> <select type="text" class="form-control" id="trfPajak_'+y+'"></select> <input type="hidden" id="jns_pph_'+y+'" value="Non Pph"/> <input type="hidden" id="tarif_persen_'+y+'" value="0"/><input type="hidden" id="akun_pph_'+y+'"/> </div><div class="col-sm-2"> <label class="control-label">Nilai Pph</label> <div class="input-group"> <input type="text" class="form-control" id="nilaiPph_'+y+'" value="0" readonly/> <span class="input-group-addon" style="padding: 0; min-width: 0;"> <button type="button" style="padding: 2px 10px; border: 0;" onclick="delpos('+y+');"> <i class="fa fa-minus"></i> </button> </span> </div></div><div class="col-sm-2"> <label class="control-label">Keterangan Biaya</label><input type="text" class="form-control" id="keteranganAkun_'+y+'" maxlength="200"  value="'+keterangan+'"/></div></div>'
		);
		onload = getPph(y);
		$("#trfPajak_"+y).change(function(){
			onload = trfPajak(y);
		});
		$("#nominal_"+y).change(function(){
			onload = nominal(y);
		});
	}
	
	function delpos(id){
		$("#formPos_"+id).remove();
		onload = biaya_yg_dibyar();
	}
	
	function nominal(y){
		var nom = document.getElementById('nominal_'+y).value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
		var trf = $("#tarif_persen_"+y).val();
		var nilai = Math.floor((parseInt(nom) * parseInt(trf))/100);
		$("#nilaiPph_"+y).val(addCommas(nilai));
		//alert(y);
		onload = biaya_yg_dibyar();
	}
	
	/*function trfPajak_Rej(y){
		var trfPajak = $("#trfPajak_"+y).val();
		var data = trfPajak.split("#");
		$("#jns_pph_"+y).val(data[0]);
		$("#tarif_persen_"+y).val(data[1]);
		$("#akun_pph_"+y).val(data[2]);
		var nom = document.getElementById('nominal_'+y).value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
		var trf = $("#tarif_persen_"+y).val();
		var nilai = Math.round((parseInt(nom) * parseInt(trf))/100);
		$("#nilaiPph_"+y).val(addCommas(nilai));
		onload = biaya_yg_dibyar();
	}*/
	
	function trfPajak(y){ // 
		var trfPajak = $("#trfPajak_"+y).val();
		var data = trfPajak.split("#");
		$("#jns_pph_"+y).val(data[0]);
		$("#tarif_persen_"+y).val(data[1]);
		$("#akun_pph_"+y).val(data[2]);
		var nom = document.getElementById('nominal_'+y).value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
		var trf = $("#tarif_persen_"+y).val();
		var nilai = Math.floor((parseInt(nom) * parseFloat(trf))/100);
		//nilai = Math.round(nilai);
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
	
	function biaya_yg_dibyar(){
		var nom = document.getElementById('realisasi_nominal').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
		var dpp = document.getElementById('dpp').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
		var ppn = document.getElementById('ppn').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
		var count = $("input[name='posbiaya[]']").length;
		var totPhh = 0; var totNom = 0;
		/*for (var i = 1; i <= count; i++) {
			var nilaiPph = document.getElementById('nilaiPph_'+i).value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
			var nominal = document.getElementById('nominal_'+i).value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
			totPhh += parseInt(nilaiPph);
			totNom += parseInt(nominal);
		}*/
		for (var i = 0; i <= count; i++) {
		//	var nilaiPph = document.getElementById('nilaiPph_'+i).value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
		//	var nominal = document.getElementById('nominal_'+i).value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
			var nilaiPph = document.getElementById('nilaiPph_'+i);
			var nominal = document.getElementById('nominal_'+i);
			
			if (nominal != null && nominal.value != '') {
				nominal = document.getElementById('nominal_'+i).value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');			
				totNom += parseInt(nominal);
			}
			if (nilaiPph != null && nilaiPph.value != '') {
				nilaiPph = document.getElementById('nilaiPph_'+i).value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
				totPhh += parseInt(nilaiPph);
			}
		}
		var biaya_yg_dibyar = parseInt(dpp) + parseInt(ppn) - parseInt(totPhh);
		//var biaya_yg_dibyar = parseInt(nom) - parseInt(totPhh);
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
		    var ppn = Math.round((10/100)*nom);
		    var npwp = $("#npwp").val();
		    $("#no_fj").removeAttr('readonly');
			if (npwp=="") {
				onload = needValue('Pengajuan Voucher Payment','Harap Isi NPWP Di Master Supplier!');
				$("#tipeppn").val('N');
				var dpp = nom;
		    	var ppn = 0;
				$("#no_fj").prop('readonly','readonly');
				$("#dpp").val(addCommas(dpp));
				$("#ppn").val(addCommas(ppn));
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
			
		    var dpp  = Math.round((10/11)*nom);
		    var ppn = Math.round((1/11)*nom);
		    var npwp = $("#npwp").val();
		    $("#no_fj").removeAttr('readonly');
			if (npwp=="") {
				onload = needValue('Pengajuan Voucher Payment','Harap Isi NPWP Di Master Supplier!');
				$("#tipeppn").val('N');
				var dpp = nom;
		    	var ppn = 0;
				$("#no_fj").prop('readonly','readonly');
				$("#dpp").val(addCommas(dpp));
				$("#ppn").val(addCommas(ppn));
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
	
	function addposhutang(){
	
		var count = $("select[name='trfPajak[]']").length;
		if (count==0) { var disable = "disabled"; } else { var disable = ""; }
		
		var keterangan = $("#keterangan").val();
		var y = count + 1;
		var dpp = $("#dpp").val();
		$("#f_poshutang").append('<div class="form-group" id="formPos_'+y+'"> <div class="col-sm-3"> <label class="control-label">Tarif Pajak</label> <select type="text" name="trfPajak[]" class="form-control" id="trfPajak_'+y+'" '+disable+'></select> <input type="hidden" id="jns_pph_'+y+'"> <input type="hidden" id="tarif_persen_'+y+'"><input type="hidden" id="akun_pph_'+y+'"></div><div class="col-sm-3"> <label class="control-label">Nominal Dpp</label> <input type="text" class="form-control number" id="nominal_'+y+'" value="'+dpp+'"  '+disable+'> </div><div class="col-sm-3"> <label class="control-label">Nilai Pph</label> <div class="input-group"> <input type="text" class="form-control" id="nilaiPph_'+y+'" value="0" readonly><span class="input-group-addon" style="padding: 0;min-width: 0;"><button type="button" style="padding: 2px 10px;border: 0;" onclick="delposHtg('+y+');"><i class="fa fa-minus"></i></button></span></div></div><div class="col-sm-3"> <label class="control-label">Keterangan Biaya</label><input type="text" class="form-control" id="keteranganAkun_'+y+'" maxlength="200"  value="'+keterangan+'" '+disable+'/></div></div>'
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
	function getMaterai(){
		//$('#tipe_materai').on('change',function(){
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
			
		//});
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
	
	/*function trfPajak(y){ // 
		var trfPajak = $("#trfPajak_"+y).val();
		var data = trfPajak.split("#");
		$("#jns_pph_"+y).val(data[0]);
		$("#tarif_persen_"+y).val(data[1]);
		$("#akun_pph_"+y).val(data[2]);
		var nom = document.getElementById('nominal_'+y).value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
		var trf = $("#tarif_persen_"+y).val();
		var nilai = ((parseInt(nom) * parseInt(trf))/100);
		nilai = Math.round(nilai);
		$("#nilaiPph_"+y).val(addCommas(nilai));
		onload = biaya_yg_dibyar();
	}
	function nominal(y){
		var nom = document.getElementById('nominal_'+y).value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
		var trf = $("#tarif_persen_"+y).val();
		var nilai = ((parseInt(nom) * parseInt(trf))/100);
		$("#nilaiPph_"+y).val(addCommas(nilai));
		onload = biaya_yg_dibyar();
	}
	function getVendor(){
		var Tipe = $("#Tipe").val();
		if (Tipe!='') {
			if (Tipe=='HUTANG') {
				var jnsHutang = $("#jnsHutang").val();
				if (jnsHutang!='') {
					onload = dataVendor();
				} else {
					onload = needValue('Pengajuan Voucher Payment','Jenis Tagihan masih kosong!');
				}
			} else if (Tipe=='BIAYA') {
				onload = dataVendor();
			}
		} else {
			onload = needValue('Pengajuan Voucher Payment','Tipe Pengajuan masih kosong!');
		}
	}
	function getAkun(id){
		var KodeDealer = $("#KodeDealer").val();
		$.ajax({ 
			url: 'system/view/getAkun.php',
			data: { action:'getAkun', 'KodeDealer': KodeDealer, 'id': id },
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
		var jnsHutang = $("#jnsHutang").val();
		$.ajax({ 
		    url: 'system/view/getVendor.php',
		    data: {'KodeDealer': KodeDealer, 'Tipe': Tipe, 'jnsHutang': jnsHutang},
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
		var count = $("select[name='trfPajak[]']").length;
		var y = count + 1;
		var dpp = $("#dpp").val();
		$("#f_poshutang").append('<div class="form-group"> <div class="col-sm-4"> <label class="control-label">Tarif Pajak</label> <select type="text" name="trfPajak[]" class="form-control" id="trfPajak_'+y+'" disabled></select> <input type="hidden" id="jns_pph_'+y+'"> <input type="hidden" id="tarif_persen_'+y+'"><input type="hidden" id="akun_pph_'+y+'"></div><div class="col-sm-4"> <label class="control-label">Nominal Dpp</label> <input type="text" class="form-control" id="nominal_'+y+'" value="'+dpp+'" readonly> </div><div class="col-sm-4"> <label class="control-label">Nilai Pph</label> <div class="input-group"> <input type="text" class="form-control" id="nilaiPph_'+y+'" readonly><span class="input-group-addon" style="padding: 0;min-width: 0;"><button type="button" style="padding: 2px 10px;border: 0;" onclick="addposhutang();" id="btn-poshtg_'+y+'" disabled><i class="fa fa-plus"></i></button></span></div></div></div>'
		);
		var cek = $("#valEdit").val();
		if (cek=='1') {
			$("#trfPajak_"+y).removeAttr('disabled');
			$("#btn-poshtg_"+y).removeAttr('disabled');
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
			var nilai = Math.round((parseInt(nom) * parseInt(trf))/100);
			$("#nilaiPph_"+y).val(addCommas(nilai));
			onload = htg_yg_dibyar();
		});
		$("#nominal_"+y).change(function(){
			var nom = document.getElementById('nominal_'+y).value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
			var trf = $("#tarif_persen_"+y).val();
			var nilai = Math.round((parseInt(nom) * parseInt(trf))/100);
			$("#nilaiPph_"+y).val(addCommas(nilai));
			onload = htg_yg_dibyar();
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
		$.ajax({ 
			url: 'system/control/akun.php',
			data: { action:'getPph', 'is_ppn': is_ppn},
			type: 'post',
			beforeSend: function(){
				onload = showLoading();
			},
			success: function(output) {
				onload = hideLoading();
				$("#trfPajak_"+id).html(output);
				$("#jns_pph_"+id).val('Non Pph');
				$("#tarif_persen_"+id).val('0');
				$("#nilaiPph_"+id).val('0');
				$("#akun_pph_"+id).val('00000000');
			}
		});
	}
	function biaya_yg_dibyar(){
		var nom = document.getElementById('realisasi_nominal').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
		var count = $("input[name='posbiaya[]']").length;
		var totPhh = 0; var totNom = 0;
		for (var i = 1; i <= count; i++) {
			var nilaiPph = document.getElementById('nilaiPph_'+i).value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
			var nominal = document.getElementById('nominal_'+i).value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
			totPhh += parseInt(nilaiPph);
			totNom += parseInt(nominal);
		}
		var biaya_yg_dibyar = (parseInt(nom) - parseInt(totPhh));
		$("#total_dpp").val(addCommas(totNom));
		$("#biaya_yg_dibyar").val(addCommas(biaya_yg_dibyar));
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
	function getDppHtg(){
		var tipeppn = $("#tipeppn").val();
		var nom = document.getElementById('realisasi_nominal').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
		var nominal = nom.replace(".","");
		if (tipeppn=='N') {
		    var dpp = nominal;
		    var ppn = 0;
		    $("#no_fj").prop('readonly','readonly');
		} else if (tipeppn=='E') {
		    var dpp  = nominal;
		    var ppn = Math.round((10/100)*nominal);
		    var npwp = $("#npwp").val();
		    $("#no_fj").removeAttr('readonly');
			if (npwp=="") {
				onload = needValue('Pengajuan Evo Pay','Harap Isi NPWP Di Master Supplier!');
				$("#tipeppn").val('N');
				var dpp = nominal;
		    	var ppn = 0;
				$("#no_fj").prop('readonly','readonly');
				$("#dpp").val(addCommas(dpp));
				$("#ppn").val(addCommas(ppn));
				return false;
			}
		} else if (tipeppn=='I') {
		    var dpp  = Math.round((10/11)*nominal);
		    var ppn = Math.round((1/11)*nominal);
		    var npwp = $("#npwp").val();
		    $("#no_fj").removeAttr('readonly');
			if (npwp=="") {
				onload = needValue('Pengajuan Evo Pay','Harap Isi NPWP Di Master Supplier!');
				$("#tipeppn").val('N');
				var dpp = nominal;
		    	var ppn = 0;
				$("#no_fj").prop('readonly','readonly');
				$("#dpp").val(addCommas(dpp));
				$("#ppn").val(addCommas(ppn));
				return false;
			}
		}
		$("#dpp").val(addCommas(dpp));
		$("#ppn").val(addCommas(ppn));

		var con = $("select[name='trfPajak[]']").length;
		for (var i = 1; i <= con; i++) {
			onload = getPph(i);
		}
	}
	function htg_yg_dibyar(){
		var nom = document.getElementById('realisasi_nominal').value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
		var count = $("select[name='trfPajak[]']").length;
		var totPhh = 0; var totDppHtg = 0;
		for (var i = 1; i <= count; i++) {
			var nilaiPph = document.getElementById('nilaiPph_'+i).value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
			totPhh += parseInt(nilaiPph);
			var nominal = document.getElementById('nominal_'+i).value.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
			totDppHtg += parseInt(nominal);
		}
		var htg_yg_dibyar = (parseInt(nom) - parseInt(totPhh));
		$("#htg_stl_pajak").val(addCommas(htg_yg_dibyar));
		$("#totDppHtg").val(addCommas(totDppHtg));
	}*/
	
	function getFile(data){
		window.open('system/files/'+data);
	}
</script>
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-gray">
			<div class="panel-heading">
				<h4>Validasi Pengajuan Voucher Payment</h4>
			</div>
			<form id="validate-form" action="test.php" method="POST" class="form-horizontal row-border">
				<div class="panel-body collapse in">
					<input type="hidden" id="IdUser" value="<?php echo $_SESSION['UserID']; ?>" readonly="readonly">
					<input type="hidden" id="level" value="<?php echo $_SESSION['level']; ?>" readonly="readonly">
					<input type="hidden" id="evo_id" value="<?php echo $vw['evo_id']; ?>" readonly="readonly">                    
                    <input type="hidden" name="ppn_persen" id="ppn_persen"  value="<?php echo $vw['nilaiPPn']; ?>" readonly="readonly"/>
					<div class="tab-container tab-primary">
						<ul class="nav nav-tabs">
							<li class="active"><a href="#tab1" id="btn-tab1" data-toggle="tab">Approval</a></li>
							<li><a href="#tab2" id="btn-tab2" data-toggle="tab">View Doc</a></li>
						</ul>
						<div class="tab-content" style="border: 0;padding: 10px 0 0 0;">
							<div class="tab-pane active" id="tab1">
								<div class="form-group">
									<label class="col-sm-2 control-label">Kode Dealer / NRM</label>
									<div class="col-sm-4">
									    <select type="text" name="KodeDealer" id="KodeDealer" class="form-control" disabled>
									    	<?php
									    		if ($IdUser=='om') {
									    			$qry = mssql_query("select KodeDealer,NamaDealer from SPK00..dodealer",$conns);
									    		} else {
									    			$qry = mssql_query("select a.KodeDealer,NamaDealer from SPK00..dodealer a 
													INNER JOIN sys_user b ON a.KodeDealer=b.KodeDealer
													where IdUser='".$IdUser."'",$conns);
									    		}
												$count = mssql_num_rows($qry);
												if ($count>1) {
													echo "<option value='' $pilih>- Pilih -</option>";
												}
												while($row = mssql_fetch_array($qry)){
													$plh = ($row['KodeDealer']==$vw['kodedealer']) ? "selected" : "";
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
													$pilih = ($row['Tipe']==$vw['tipe']) ? "selected" : "";
													echo "<option value='$row[Tipe]' $pilih>$row[Tipe]</option>";
												}
									    	?>
									    </select>
									</div>
								</div>
								<section id="getForm">
									<?php if ($vw['tipe']=='HUTANG') { ?>
										<hr style="margin: 10px 0;">
										<div class="form-group">
										    <label class="col-sm-2 control-label">No. Bukti</label>
										    <div class="col-sm-4" id="f_NoBuktiPengajuan">
										        <div class="input-group"> 
										          	<span class="input-group-addon">VP</span>
										            <input type="text" class="form-control" id="nobukti" value="<?php echo substr($vw[nobukti], 2,15); ?>" readonly> 
										      	</div>
										    </div>
										    <label class="col-sm-2 control-label">Tanggal Pengajuan</label>
										    <div class="col-sm-4">
										        <input type="text" class="form-control" id="tgl_pengajuan" value="<?php echo $vw['tgl_pengajuan']; ?>" readonly>
										  	</div>
										</div>
										<div class="form-group">
										    <label class="col-sm-2 control-label">Upload File</label>
										    <div class="col-sm-4">
										        <input type="text" class="form-control" id="upload_file" value="<?php echo $vw['upload_file']; ?>" readonly>
										    </div>
										    <label class="col-sm-2 control-label">Upload Faktur Pajak</label>
										    <div class="col-sm-4">
										        <input type="text" class="form-control" id="upload_fp" value="<?php echo $vw['upload_fp']; ?>" readonly>
										    </div>
										</div>
										<hr style="margin: 10px 0;">
										<div class="form-group">
										    <label class="col-sm-2 control-label">Kode Vendor</label>
										    <div class="col-sm-4">
										        <input type="text" class="form-control" id="kode_vendor" value="<?php echo $vw['kode_vendor']; ?>" readonly>
										    </div>
										    <label class="col-sm-2 control-label">Nama Vendor</label>
										    <div class="col-sm-4">
										        <input type="text" class="form-control" id="namaVendor" value="<?php echo $vw['namaVendor']; ?>" readonly>
										    </div>
										</div>
										<div class="form-group">
										    <label class="col-sm-2 control-label">Metode Pembayaran</label>
										    <div class="col-sm-4">
										        <input type="text" class="form-control" id="metode_bayar" value="<?php echo $vw['metode_bayar']; ?>" readonly>
										    </div>
										    <label class="col-sm-2 control-label">Departement Terkait</label>
										    <div class="col-sm-4">
										        <input type="text" class="form-control" id="metode_bayar" value="<?php echo $vw['deptterkait']; ?>" readonly>
										    </div>
										</div>
										<div class="form-group">
										    <label class="col-sm-2 control-label">Beneficary Account</label>
										    <div class="col-sm-4">
										        <input type="text" class="form-control" id="benificary_account" value="<?php echo $vw['benificary_account']; ?>" readonly>
										    </div>
										    <label class="col-sm-2 control-label">Tanggal Bayar</label>
										    <div class="col-sm-4">
										        <input type="date" class="form-control" id="tgl_bayar" value="<?php echo $vw[tgl_bayar]; ?>" readonly>
										    </div>
										</div>
										<div class="form-group">
										    <label class="col-sm-2 control-label">Nama Bank Penerima</label>
										    <div class="col-sm-4">
										        <input type="text" class="form-control" id="nama_bank" value="<?php echo $vw['nama_bank']; ?>" readonly> 
										    </div>
										    <label class="col-sm-2 control-label">Nama Pemilik</label>
										    <div class="col-sm-4">
										        <input type="text" class="form-control" id="nama_pemilik" value="<?php echo $vw['nama_pemilik']; ?>" readonly> 
										    </div>
										</div>
										<div class="form-group">
										    <label class="col-sm-2 control-label">Email Penerima</label>
										    <div class="col-sm-4">
										        <input type="text" class="form-control" id="email_penerima" value="<?php echo $vw['email_penerima']; ?>" readonly> 
										    </div>
										    <label class="col-sm-2 control-label">Nama Alias</label>
										    <div class="col-sm-4">
										        <input type="text" class="form-control" id="nama_alias" value="<?php echo $vw['nama_alias']; ?>" readonly> 
										    </div>
										</div>
										<div class="form-group">
										    <label class="col-sm-2 control-label">Nama Bank Pengirim</label>
										    <div class="col-sm-4">
										        <input type="text" class="form-control" id="nama_bank_pengirim" value="<?php echo $vw['nama_bank_pengirim']; ?>" readonly> 
										    </div>
										    <label class="col-sm-2 control-label">Transfer From Account</label>
										    <div class="col-sm-4">
										        <input type="text" class="form-control" id="tf_from_account" value="<?php echo $vw['tf_from_account']; ?>" readonly> 
										    </div>
										</div>
										<hr style="margin: 10px 0;">
										<div class="form-group">
										    <label class="col-sm-2 control-label">No Tagihan</label>
										</div>
										<div class="form-group">
										    <div class="col-sm-12">
										        <table class="gethutang" style="display: none"></table>
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
														            width : 550,
														            sortable : false,
														            align : 'left'
														        }, {
														            display : 'Jumlah',
														            name : 'namaBank',
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
															url:'system/data/gettagihan.php', 
															newp: 1,
															params:[{
																	name:'nobukti', value: $("#nobukti").val()
																}
															]
														}).flexReload();
										        	});
										        </script>
										    </div>
										</div>
                                        
                                        <!--// Jurnal Pengakuan Hutang //--->
                                        <hr style="margin: 10px 0;">
										<div class="form-group">
										    <label class="col-sm-8 control-label">Jurnal Pengakuan Hutang</label>
                                            <div class="col-sm-4" align="right">
                                              <button type="button" id="hutangjurnal" class="btn-success btn">View All</button>
                                            </div>
										</div>
										<div class="form-group">
										    <div class="col-sm-12">
										        <table class="getjurnalhutang" style="display: none"></table>
										        <script type="text/javascript">
										        	jQuery(document).ready(function($) {
										        		$(".getjurnalhutang").flexigrid({
														    dataType : 'xml',
														    colModel : [ 
														        {
														            display : 'No Faktur',
														            name : 'nofaktur',
														            width : 100,
														            sortable : false,
														            align : 'left'
														        }, {
														            display : 'No Bukti',
														            name : 'nobukti',
														            width : 100,
														            sortable : false,
														            align : 'left'
														        }, {
														            display : 'Kode Akun',
														            name : 'kodeakun',
														            width : 100,
														            sortable : false,
														            align : 'left'
														        }, {
														            display : 'Nama Akun',
														            name : 'namaakun',
														            width : 150,
														            sortable : false,
														            align : 'left'
														        }, {
														            display : 'Keterangan',
														            name : 'keterangan',
														            width : 300,
														            sortable : false,
														            align : 'left'
														        }, {
														            display : 'Jumlah Debit',
														            name : 'debit',
														            width : 100,
														            sortable : false,
														            align : 'right'
														        }, {
														            display : 'Jumlah Kredit',
														            name : 'kredit',
														            width : 100,
														            sortable : false,
														            align : 'right'
														        }
														    ],
														    showToggleBtn : false,
														    width : 'auto',
														    height : '150'
														});
										        	});
										        </script>
										    </div>
										</div>
                                        
                                        
										<div class="form-group">
										    <label class="col-sm-2 control-label">Nominal</label>
										    <div class="col-sm-4">
										        <input type="text" class="form-control" id="realisasi_nominal" value="<?php echo number($vw['realisasi_nominal']); ?>" readonly> 
										    </div>
										</div>
                                         <div class="form-group">
                                            <label class="col-sm-2 control-label">Tipe Materai</label>
                                            <div class="col-sm-4">
                                                 <select name="tipe_materai" id="tipe_materai" class="form-control" onchange="getDppHtg();" disabled>
                                                    	<?php
										        		$opt = array('I' => 'Include','E' => 'Exclude','N' => 'Non Materai');
										        		foreach ($opt as $value => $display) {
										        			$plh = ($value==$vw['tipematerai'])?"selected" : ""; 
										        			echo "<option value='$value' $plh>$display</option>";
										        		}
										        	?>
                                                    
                                                </select>
                                           </div>
                                            <label class="col-sm-2 control-label">Nominal Materai</label>
                                            <div class="col-sm-4">
                                                <input type="text" class="form-control number" id="nominal_materai" value="<?php echo number($vw['materai']); ?>" readonly />
                                           </div>
                                        </div>
                                        
                                        <div class="form-group" id="div_materai">
                                            <label class="col-sm-2 control-label">Kode Akun Materai</label>
                                            <div class="col-sm-4">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="kodeAkunMaterai" value="<?php echo $vw['kodeAkunMaterai']; ?>" readonly />
                                                    <span class="input-group-addon" style="padding: 0; min-width: 0;">
                                                        <button type="button" id="btn-kodeAkunMaterai" style="padding: 2px 10px; border: 0;" onclick="getAkunMaterai();" disabled>
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
										            <input type="text" class="form-control" id="kodeAkun" value="<?php echo $vw['kodeAkun']; ?>" readonly>
										            <span class="input-group-addon" style="padding: 0;min-width: 0;"> 
										            	<button type="button" id="btn-kodeAkun" style="padding: 2px 10px;border: 0;" onclick="getAkun();" disabled> 
										            		<i class="fa fa-search"></i>
										            	</button>
										            </span>
										        </div>
										    </div>
										    <label class="col-sm-2 control-label">Nama Akun</label>
										    <div class="col-sm-4">
										        <input type="text" class="form-control" id="namaAkun" value="<?php echo $vw['namaAkun']; ?>" readonly>
										    </div>
										</div>
										<div class="form-group">
										    <label class="col-sm-2 control-label">Type Ppn</label>
										    <div class="col-sm-4">
											    	<select type="text" class="form-control" id="tipeppn" onchange="getDppHtg();" disabled>
										        	<?php
										        		$opt = array('N' => 'Non Ppn','I' => 'Include','E' => 'Exclude');
										        		foreach ($opt as $value => $display) {
										        			$plh = ($value==$vw['tipeppn'])?"selected" : ""; 
										        			echo "<option value='$value' $plh>$display</option>";
										        		}
										        	?>
										        </select>
										    </div>
										</div>
										<div class="form-group">
										    <label class="col-sm-2 control-label">Dpp</label>
										    <div class="col-sm-4">
										        <input type="text" class="form-control" id="dpp" value="<?php echo number($vw['dpp']); ?>" readonly>
										    </div>
										    <label class="col-sm-2 control-label">Ppn</label>
										    <div class="col-sm-4">
										        <input type="text" class="form-control" id="ppn" value="<?php echo number($vw['ppn']); ?>" readonly>
										    </div>
										</div>
										<div class="form-group">
										    <label class="col-sm-2 control-label">NPWP</label>
										    <div class="col-sm-4">
										        <input type="text" class="form-control" id="npwp" value="<?php echo ltrim(rtrim($vw['npwp'])); ?>" readonly>
										    </div>
										    <label class="col-sm-2 control-label">No. Faktur Pajak</label>
										    <div class="col-sm-4">
										        <input type="text" class="form-control" id="no_fj" value="<?php echo $vw['no_fj']; ?>" readonly>
										    </div>
										</div>
										<span id="f_poshutang">
											<?php
												$sqlpos = "select *,(jns_pph+'#'+convert(varchar,tarif_persen)) val from DataEvoPos where nobukti = '".$vw['nobukti']."'";
												$rslpos = mssql_query($sqlpos);
												$no=1;
												$cpos = mssql_num_rows($rslpos);
												if ($cpos==0) {
													echo '
														<script type="text/javascript">
															jQuery(document).ready(function($) {
																onload = addposhutang();
															});
														</script>
													';
													$totDppHtg = $vw['dpp'];
												} else {
													$totDppHtg = 0;
													while ($dtpos = mssql_fetch_array($rslpos)) {
														if ($vw['tipeppn']!='N') { $is_ppn = '1'; } else { $is_ppn = '0'; }
															if (ltrim(rtrim($vw['npwp']))!='') { $is_npwp = '1'; } else { $is_npwp = '0'; }
														echo '
															<div class="form-group">
															    <div class="col-sm-3">
															        <label class="control-label">Tarif Pajak</label>
																	<select type="text" name="trfPajak[]" class="form-control" id="trfPajak_'.$no.'" onchange="trfPajak('.$no.');" disabled>';
															        $sql = "select jns_pph,tarif_persen,(jns_pph+'#'+convert(varchar,tarif_persen)) val 
														        		from settingPph  order by idpph asc"; // where npwp = '".$is_npwp."'
																	$rsl = mssql_query($sql);
																	echo "<option value='Non Pph#0#00000000'>Non Pph</option>";
																	while ($dt = mssql_fetch_array($rsl)) {
																		if ($is_npwp=='0') {
																			if ($dt['jns_pph']=='Pph 4 Ayat 2') {
																				$jns = "non_pph_4";
																			} else {
																				$jns = "non_".str_replace(' ', '_', strtolower($dt['jns_pph']));
																			}
																		} else if ($is_npwp=='1') {
																			if ($dt['jns_pph']=='Pph 4 Ayat 2') {
																				$jns = "pph_4";
																			} else {
																				$jns = str_replace(' ', '_', strtolower($dt['jns_pph']));
																			}
																		}
																		$akun = mssql_fetch_array(mssql_query("select ".$jns." as akun from settingAkun where id=1"));
																		$pilih = ($dtpos['val']==$dt['val']) ? "selected" : "";
																		echo "<option value='$dt[val]#$akun[akun]' $pilih>$dt[jns_pph] ($dt[tarif_persen]%)</option>";
																	}
															        echo '
															        </select>
															        <input type="hidden" id="jns_pph_'.$no.'" value="'.$dtpos['jns_pph'].'">
															        <input type="hidden" id="tarif_persen_'.$no.'" value="'.$dtpos['tarif_persen'].'">
															        <input type="hidden" id="akun_pph_'.$no.'" value="'.$dtpos['akun_pph'].'">
																</div>
															    <div class="col-sm-3">
															        <label class="control-label">Nominal Dpp</label>
															        <input type="text" class="form-control" id="nominal_'.$no.'" value="'.number($dtpos['nominal']).'" disabled />
															  	</div>
															    <div class="col-sm-3">
															        <label class="control-label">Nilai Pph</label>
															        <div class="input-group">
															            <input type="text" class="form-control" id="nilaiPph_'.$no.'" value="'.number($dtpos['nilai_pph']).'" readonly>
															          	<span class="input-group-addon" style="padding: 0;min-width: 0;">
															              <button type="button" style="padding: 2px 10px;border: 0;" onclick="addposhutang();" id="btn-poshtg_'.$no.'" disabled />
															                <i class="fa fa-plus"></i>
															              </button>
															          	</span>
															      	</div>
															    </div>
																<div class="col-sm-3"> 
																	<label class="control-label">Keterangan Biaya</label>
																	<input type="text" class="form-control" id="keteranganAkun_'.$y.'" maxlength="200" value="'.$dpos['KeteranganAkun'].'" disabled />
																	</div>
															</div>
														';
														$totDppHtg += $dtpos['nominal'];
														$no++;
													}
												}
											?>
										</span>
										<hr style="margin: 10px 0;">
										<div class="form-group">
										    <label class="col-sm-2 control-label">Hutang setelah pajak</label>
										    <div class="col-sm-4">
										        <input type="text" class="form-control" id="htg_stl_pajak" value="<?php echo number($vw['htg_stl_pajak']); ?>" readonly>
										    </div>
                                            <div class="col-sm-2">
                                            	<button type="button" class="btn-default btn get_numberhutang" style="padding: 3px 10px; display: none;" id="btn-addpos" >
                                                	<i class="fa fa-plus"></i> Add Pos Hutang</button>
                                            </div>
                                            <div class="col-sm-4">
										        <input type="hidden" class="form-control" id="totDppHtg" value="<?php echo $totDppHtg; ?>" readonly/>
										    </div>
										</div>
										<div class="form-group">
										    <label class="col-sm-2 control-label">Keterangan</label>
										    <div class="col-sm-10">
										        <input type="text" class="form-control" id="keterangan" value="<?php echo $vw['keterangan']; ?>" readonly>
										    </div>
										</div>                            
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">Diajukan oleh</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="useraju" value="<?php echo $useraju; ?>" readonly>
                                            </div>
                                        </div>
									<?php } else if ($vw['tipe']=='BIAYA') { ?>
										<div class="form-group">
										    <label class="col-sm-2 control-label">Divisi</label>
										    <div class="col-sm-4">
										        <input type="text" id="divisi" class="form-control" value="<?php echo $vw[divisi]; ?>" readonly>
										    </div>
										    <label class="col-sm-2 control-label">Nama Atasan</label>
										    <div class="col-sm-4">
										        <input type="text" id="IdAtasan" class="form-control" value="<?php echo $vw[namaUserAtasan]; ?>" readonly>
										  	</div>
										</div>
										<hr style="margin: 10px 0;">
										<div class="form-group">
										    <label class="col-sm-2 control-label">Status</label>
										    <div class="col-sm-4">
										        <input type="text" id="status" class="form-control" value="<?php echo $vw[status]; ?>" readonly>
										    </div>
										</div>
										<div class="form-group">
										    <label class="col-sm-2 control-label">No. Bukti</label>
										    <div class="col-sm-4" id="f_NoBuktiPengajuan">
										        <div class="input-group"> <span class="input-group-addon">VP</span>
										            <input type="text" id="nobukti" class="form-control" value="<?php echo substr($vw[nobukti], 2,15); ?>" readonly>
										      	</div>
										    </div>
										    <label class="col-sm-2 control-label">Tanggal Pengajuan</label>
										    <div class="col-sm-4">
										        <input type="date" id="tgl_pengajuan" class="form-control" value="<?php echo $vw[tgl_pengajuan]; ?>" readonly>
										  	</div>
										</div>
										<div class="form-group">
										    <label class="col-sm-2 control-label">Upload File</label>
										    <div class="col-sm-4">
										        <input type="text" id="upload_file" class="form-control" value="<?php echo $vw[upload_file]; ?>" disabled>
										  	</div>
										    <label class="col-sm-2 control-label">Upload Faktur Pajak</label>
										    <div class="col-sm-4">
										        <input type="text" id="upload_fp" class="form-control" value="<?php echo $vw[upload_fp]; ?>" disabled>
										  	</div>
										</div>
										<hr style="margin: 10px 0;">
										<div class="form-group">
										    <label class="col-sm-2 control-label">Kode Vendor</label>
										    <div class="col-sm-4">
										        <input type="text" id="kode_vendor" class="form-control" value="<?php echo $vw[kode_vendor]; ?>" disabled>
										  	</div>
										    <label class="col-sm-2 control-label">Nama Vendor</label>
										    <div class="col-sm-4">
										        <input type="text" id="namaVendor" class="form-control" value="<?php echo $vw[namaVendor]; ?>" readonly>
										  	</div>
										</div>
										<div class="form-group">
										    <label class="col-sm-2 control-label">Metode Pembayaran</label>
										    <div class="col-sm-4">
										    	<input type="text" class="form-control" id="metode_bayar" value="<?php echo $vw[metode_bayar]; ?>" readonly>
										    </div>
										    <label class="col-sm-2 control-label">Departement Terkait</label>
										    <div class="col-sm-4">
										    	<input type="text" class="form-control" id="metode_bayar" value="<?php echo $vw[deptterkait]; ?>" readonly>
										    </div>
										</div>
										<div class="form-group">
										    <label class="col-sm-2 control-label">Beneficary Account</label>
										    <div class="col-sm-4">
										        <input type="text" class="form-control" id="benificary_account" value="<?php echo $vw[benificary_account]; ?>" readonly>
										  	</div>
										    <label class="col-sm-2 control-label">Tanggal Bayar</label>
										    <div class="col-sm-4">
										        <!-- <?php if ($_SESSION['level']=='ADH') { ?>
													<input type="date" class="form-control" id="tgl_bayar" value="<?php echo $vw[tgl_bayar]; ?>">
												<?php } else { ?> -->
													<input type="date" class="form-control" id="tgl_bayar" value="<?php echo $vw[tgl_bayar]; ?>" readonly>
												<!-- <?php } ?> -->
										  	</div>
										</div>
										<div class="form-group">
										    <label class="col-sm-2 control-label">Nama Bank Penerima</label>
										    <div class="col-sm-4">
										        <input type="text" class="form-control" id="nama_bank" value="<?php echo $vw[nama_bank]; ?>" readonly>
										  	</div>
										    <label class="col-sm-2 control-label">Nama Pemilik</label>
										    <div class="col-sm-4">
										        <input type="text" class="form-control" id="nama_pemilik" value="<?php echo $vw[nama_pemilik]; ?>" readonly>
										  	</div>
										</div>
										<div class="form-group">
										    <label class="col-sm-2 control-label">Email Penerima</label>
										    <div class="col-sm-4">
										        <input type="text" class="form-control" id="email_penerima" value="<?php echo $vw[email_penerima]; ?>" readonly>
										  	</div>
										    <label class="col-sm-2 control-label">Nama Alias</label>
										    <div class="col-sm-4">
										        <input type="text" class="form-control" id="nama_alias" value="<?php echo $vw[nama_alias]; ?>" readonly>
										  	</div>
										</div>
										<div class="form-group">
										    <label class="col-sm-2 control-label">Nama Bank Pengirim</label>
										    <div class="col-sm-4">
										        <input type="text" class="form-control" id="nama_bank_pengirim" value="<?php echo $vw[nama_bank_pengirim]; ?>" readonly>
										  	</div>
										    <label class="col-sm-2 control-label">Transfer From Account</label>
										    <div class="col-sm-4">
										        <input type="text" class="form-control" id="tf_from_account" value="<?php echo $vw[tf_from_account]; ?>" readonly>
										  	</div>
										</div>
										<div class="form-group">
										    <label class="col-sm-2 control-label">Realisasi Nominal</label>
										    <div class="col-sm-4">
										        <input type="text" class="form-control" id="realisasi_nominal" value="<?php echo number($vw[realisasi_nominal]); ?>" readonly>
										  	</div>
										</div>
										<hr style="margin: 10px 0;">
										<div class="form-group">
										    <label class="col-sm-2 control-label">
										    <?php 
											//$pilih = ($vw['is_ppn']==1) ? "checked" : ""; 
											$pilih = ($vw['ppn']>0) ? "checked" : ""; 
											
											?>
										    <input type="checkbox" id="is_ppn" value="<?php echo $vw[is_ppn]; ?>" <?php echo $pilih; ?> disabled> PPN</label>
										    <div class="col-sm-4">
										      <div class="input-group">
										        <span class="input-group-addon">Dpp</span>
										        <input type="text" class="form-control" id="dpp" value="<?php echo number($vw[dpp]); ?>" readonly> 
										        <span class="input-group-addon">Ppn</span>
										        <input type="text" class="form-control" id="ppn" value="<?php echo number($vw[ppn]); ?>" readonly>
										      </div>
										    </div>
										</div>
										<div class="form-group">
										    <label class="col-sm-2 control-label">NPWP</label>
										    <div class="col-sm-4">
										        <input type="text" class="form-control" id="npwp" value="<?php echo ltrim(rtrim($vw['npwp'])); ?>" readonly>
										  	</div>
										    <label class="col-sm-2 control-label">No. Faktur Pajak</label>
										    <div class="col-sm-4">
										        <input type="text" class="form-control" id="no_fj" value="<?php echo $vw[no_fj]; ?>" readonly>
										  	</div>
										</div>
										<span id="f_posbiaya">
											<?php
												$spos = "select *,(jns_pph+'#'+convert(varchar,tarif_persen)) val from DataEvoPos where nobukti = '".$vw['nobukti']."'";
												$rpos = mssql_query($spos);
												$y = 1;
												$over = "0";
												while ($dpos = mssql_fetch_array($rpos)) {
													echo '
														<script type="text/javascript">
															jQuery(document).ready(function($) {
																onload = cekRab(\''.$vw['kodedealer'].'\',\''.$dpos['pos_biaya'].'\',\''.$dpos['nominal'].'\');
															});
														</script>
													';
													echo '
														<div class="form-group" id="formPos_'.$y.'">
														    <div class="col-sm-3">
														        <label class="control-label">Pos Biaya</label>
														        <div class="input-group">
														            <input type="hidden" name="posbiaya[]" id="kodeAkun_'.$y.'" value="'.$dpos['pos_biaya'].'">
														            <input type="text" class="form-control" id="ketAkun_'.$y.'" value="'.$dpos['ketAkun'].'" readonly>
														          	<span class="input-group-addon" style="padding: 0;min-width: 0;">
														              <button type="button" style="padding: 2px 10px;border: 0;" id="btn-pos_'.$y.'" onclick="getAkun('.$y.');" disabled>
														                <i class="fa fa-search"></i>
														              </button>
														          	</span>
														      	</div>
														    </div>
														    <div class="col-sm-3">
														        <label class="control-label">Nominal</label>
														        <input type="text" class="form-control number" id="nominal_'.$y.'" value="'.number($dpos['nominal']).'" onChange="nominal('.$y.');" disabled>
														  	</div>
														    <div class="col-sm-2">
														        <label class="control-label">Tarif Pajak</label>
														        <select type="text" class="form-control" name="trfPajak[]" id="trfPajak_'.$y.'" onchange="trfPajak('.$y.');" disabled>';
																if ($vw['tipeppn']!='N') { $is_ppn = '1'; } else { $is_ppn = '0'; }
																if (ltrim(rtrim($vw['npwp']))!='') { $is_npwp = '1'; } else { $is_npwp = '0'; }
															
														        $sql = "select jns_pph,tarif_persen,(jns_pph+'#'+convert(varchar,tarif_persen)) val 
													        		from settingPph  order by idpph asc"; //where npwp = '".$is_npwp."'
																$rsl = mssql_query($sql);
																echo "<option value='Non Pph#0#00000000'>Non Pph</option>";
																while ($dt = mssql_fetch_array($rsl)) {
																	if ($is_npwp=='0') {
																		if ($dt['jns_pph']=='Pph 4 Ayat 2') {
																			$jns = "non_pph_4";
																		} else {
																			$jns = "non_".str_replace(' ', '_', strtolower($dt['jns_pph']));
																		}
																	} else if ($is_npwp=='1') {
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
																<div class="input-group"> 
																	<input type="text" class="form-control" id="nilaiPph_'.$y.'" value="'.number($dpos['nilai_pph']).'" readonly> 
																	<span class="input-group-addon removepos" style="padding: 0; min-width: 0;"> 
																		<button type="button" style="padding: 2px 10px; border: 0;" onclick="delpos('.$y.', '.$dpos['evopos_id'].');"> 
																	<i class="fa fa-minus"></i> </button> </span> 
																</div>
														    </div>
															<div class="col-sm-2"> 
																<label class="control-label">Keterangan Biaya</label>
																<input type="text" class="form-control" id="keteranganAkun_'.$y.'" maxlength="200" value="'.$dpos['KeteranganAkun'].'" disabled/></div>
														</div>
													';
													$y++;
												}
											?><input type="hidden" id="jmlpos" value="<?php echo $y;?>" readonly="readonly" />
										</span>
										<div class="form-group">
										    <label class="col-sm-3 control-label">Total Dpp</label>
										    <div class="col-sm-3">
										        <input type="text" class="form-control" id="total_dpp" value="<?php echo number($vw[total_dpp]); ?>" readonly>
										    </div>
										    <div class="col-sm-3">
										    	<button type="button" class="btn-default btn get_number" style="padding: 3px 10px;display: none;" id="btn-addpos">
										    		<i class="fa fa-plus"></i> Add Pos Biaya
										    	</button>
										    </div>
										    <div class="col-sm-3">
										        <input type="hidden" class="form-control" id="over" value="<?php echo $over; ?>" readonly>
										    </div>
										</div>
										<hr style="margin: 10px 0;">
										<div class="form-group">
										    <label class="col-sm-2 control-label">Biaya yg harus dibayar</label>
										    <div class="col-sm-4">
										        <input type="text" class="form-control" id="biaya_yg_dibyar" value="<?php echo number($vw[biaya_yg_dibyar]); ?>" readonly>
										    </div>
										</div>
										<div class="form-group">
										    <label class="col-sm-2 control-label">Keterangan</label>
										    <div class="col-sm-10">
												<input type="text" class="form-control" id="keterangan" value="<?php echo $vw[keterangan]; ?>" disabled>
										    </div>
										</div>                                 
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">Diajukan oleh</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="useraju" value="<?php echo $useraju; ?>" readonly>
                                            </div>
                                        </div>
									<?php } ?>
								</section>
								<section>
									<hr style="margin: 10px 0;">
									<div class="form-group">
										<label class="col-sm-12 control-label"><b>Note :</b></label> 
									</div>
									<?php 
									$notedeptterkait = "";
									if ($vw['kodedealer']=='2010') { 
									
									$rowdeptterkait = trim($vw['deptterkait']);
									
									if (!empty($rowdeptterkait)) { 
									
									?>
                                    	<div class="form-group">
											<label class="col-sm-2 control-label">Other Dept</label> 
											 <div class="col-sm-10"></div>
                                        </div>    
									   <?php 
									   
									   
									   $deptterkait = mssql_query("select b.namaUser, a.ketvalidasi note, 
																	CONVERT(varchar,a.tglvalidasi,105) + ' ' +CONVERT(varchar,a.tglvalidasi,108) tgl
																	 from dataEvoVal a
																	 inner join sys_user b on a.uservalidasi = b.IdUser
																	 left join sys_level c on b.tipe = c.nama_lvl
																	 where b.department = '".$vw['deptterkait']."' and c.is_dealer = '0' 
																	 and a.nobukti = '".$vw['nobukti']."' and isnull(a.deptterkait,'') != ''
																	 order by c.urutan");
									   
									   while ($row_deptterkait = mssql_fetch_array($deptterkait)) {
									   
									   ?>     
											<div class="form-group">
												<label class="col-sm-2 control-label">&nbsp;</label> 
												<div class="col-sm-1">User : </div>
												<div class="col-sm-4">
														<?php echo $row_deptterkait["namaUser"]; ?>
												</div>
												<div class="col-sm-1">Waktu :  </div>
												<div class="col-sm-4"><?php	echo $row_deptterkait["tgl"]; ?></div>
											</div>
											<div class="form-group">
												<label class="col-sm-2 control-label"></label> 
												<div class="col-sm-1">Note : </div>
												<div class="col-sm-9">
													<input type="text" class="form-control" value="<?php echo $row_deptterkait['note'];?>" readonly>
												</div>
											</div>
										<?php
											}
										
										//echo $vw['deptterkait']."dd".$_SESSION['evo_dept'];
										
										if ($vw['deptterkait']==$_SESSION['evo_dept']) { ?>
                                        
                                        <div class="form-group">
											<label class="col-sm-2 control-label"></label> 
											<div class="col-sm-1">User : </div>
                                            <div class="col-sm-4"><?php echo $_SESSION['UserName'];?></div>
                                            <div class="col-sm-1">Waktu :  </div>
                                            <div class="col-sm-4"><?php	echo date("d-m-Y H:i:s"); ?></div>
										</div>
                                        <div class="form-group">
											<label class="col-sm-2 control-label"></label> 
                                            <div class="col-sm-1">Note : </div>
											<div class="col-sm-9">
												<input type="text" class="form-control" id="note_deptterkait">
											</div>
										</div>
                                        
                                        
										<?
											$notedeptterkait = 1;
										} else {
											$notedeptterkait = "";
										}
									
										
									 }  ?>    
                                        
										<div class="form-group">
											<label class="col-sm-2 control-label">Section Head</label> 
											<div class="col-sm-1">User : </div>
                                            <div class="col-sm-4">
                                            		<?php $note_arr = usernote($vw['nobukti'],'SECTION HEAD','',''); 
														echo $note_arr["user"]; ?>
											</div>
                                            <div class="col-sm-1">Waktu :  </div>
                                            <div class="col-sm-4"><?php	echo $note_arr["tgl"]; ?></div>
										</div>
                                        <div class="form-group">
											<label class="col-sm-2 control-label"></label> 
                                            <div class="col-sm-1">Note : </div>
											<div class="col-sm-9">
												<input type="text" class="form-control" id="note_sectionhead" <?php note($vw['nobukti'],'SECTION HEAD','','', $notedeptterkait); ?> >
											</div>
										</div>
                                        
										<div class="form-group">
											<label class="col-sm-2 control-label">Dept. Head</label> 
											<div class="col-sm-1">User : </div>
                                            <div class="col-sm-4">
                                            	<?php $note_arr = usernote($vw['nobukti'],'DEPT. HEAD','',''); 
														echo $note_arr["user"]; ?>
											</div>
                                            <div class="col-sm-1">Waktu : </div>
                                            <div class="col-sm-4"><?php	echo $note_arr["tgl"]; ?></div>
										</div>
                                        <div class="form-group">
											<label class="col-sm-2 control-label"></label> 
                                            <div class="col-sm-1">Note : </div>
											<div class="col-sm-9">
												<input type="text" class="form-control" id="note_dept_head" <?php note($vw['nobukti'],'DEPT. HEAD','','',$notedeptterkait); ?> >
											</div>
										</div>
                                        
										<div class="form-group">
											<label class="col-sm-2 control-label">Div. Head</label> 
											<div class="col-sm-1">User : </div>
                                            <div class="col-sm-4">
													<?php $note_arr = usernote($vw['nobukti'],'DIV. HEAD','',''); 
														echo $note_arr["user"]; ?>
											</div>
                                            <div class="col-sm-1">Waktu : </div>
                                            <div class="col-sm-4"><?php	echo $note_arr["tgl"]; ?></div>
										</div>
                                        <div class="form-group">
											<label class="col-sm-2 control-label"></label> 
                                            <div class="col-sm-1">Note : </div>
											<div class="col-sm-9">
												<input type="text" class="form-control" id="note_div_head" <?php note($vw['nobukti'],'DIV. HEAD','','',$notedeptterkait); ?> >
											</div>
										</div>
                                        
										<div class="form-group">
											<label class="col-sm-2 control-label">Direksi 1</label> 
											<div class="col-sm-1">User :  </div>
                                            <div class="col-sm-4">
													<?php $note_arr = usernote($vw['nobukti'],'DIREKSI','',''); 
														echo $note_arr["user"]; ?>
											</div>
                                            <div class="col-sm-1">Waktu : </div>
                                            <div class="col-sm-4"><?php	echo $note_arr["tgl"]; ?></div>
										</div>
                                        <div class="form-group">
											<label class="col-sm-2 control-label"></label> 
                                            <div class="col-sm-1">Note : </div>
											<div class="col-sm-9">
												<input type="text" class="form-control" id="note_direksi" <?php note($vw['nobukti'],'DIREKSI','','',$notedeptterkait); ?> >
											</div>
										</div>
                                        
                                        <?php 
										//echo $vw['htg_stl_pajak']."__".$skipdir['skip_direksi2'];
										if ($vw['totBayar'] > $skipdir['skip_direksi2']) { ?>
                                              <div class="form-group">
                                                <label class="col-sm-2 control-label">Direksi 2</label> 
                                                <div class="col-sm-1">User :  </div>
                                                <div class="col-sm-4">
                                                        <?php $note_arr = usernote($vw['nobukti'],'DIREKSI 2','',''); 
                                                            echo $note_arr["user"]; ?>
                                                </div>
                                                <div class="col-sm-1">Waktu : </div>
                                                <div class="col-sm-4"><?php	echo $note_arr["tgl"]; ?></div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label"></label> 
                                                <div class="col-sm-1">Note : </div>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" id="note_direksi2" <?php note($vw['nobukti'],'DIREKSI 2','','',$notedeptterkait); ?> >
                                                </div>
                                            </div>
                                            <?php } else { ?>
                                           <!-- <input type="text" class="form-control" id="note_direksi2" >-->
                                            <?php } ?>
                                        
										<div class="form-group">
											<label class="col-sm-2 control-label">Dept.Head Finance / Releaser 1</label> 
											<div class="col-sm-1">User : </div>
                                            <div class="col-sm-4">
													<?php $note_arr = usernote($vw['nobukti'],'DEPT. HEAD FINANCE','FINANCE and ACCOUNTING','FINANCE'); 
													echo $note_arr["user"]; ?>
											</div>
                                           <div class="col-sm-1">Waktu :  </div>
                                            <div class="col-sm-4"><?php	echo $note_arr["tgl"]; ?></div>
										</div>
                                        <div class="form-group">
											<label class="col-sm-2 control-label"></label> 
                                            <div class="col-sm-1">Note : </div>
											<div class="col-sm-9">
												<input type="text" class="form-control" id="note_dept_head_fin" <?php note($vw['nobukti'],'DEPT. HEAD FINANCE','FINANCE and ACCOUNTING','FINANCE',$notedeptterkait); ?> >
											</div>
										</div>
                                        
                                        <div class="form-group">
											<label class="col-sm-2 control-label">Div.Head FAST / Releaser 2</label> 
											<div class="col-sm-1">User : </div>
                                            <div class="col-sm-4">
													<?php $note_arr = usernote($vw['nobukti'],'DEPT. HEAD FINANCE / DIV. HEAD FAST','FINANCE and ACCOUNTING','all'); 
													echo $note_arr["user"]; ?>
											</div>
                                           <div class="col-sm-1">Waktu :  </div>
                                            <div class="col-sm-4"><?php	echo $note_arr["tgl"]; ?></div>
										</div>
                                        <div class="form-group">
											<label class="col-sm-2 control-label"></label> 
                                            <div class="col-sm-1">Note : </div>
											<div class="col-sm-9">
												<input type="text" class="form-control" id="note_div_head_fast" <?php note($vw['nobukti'],'DEPT. HEAD FINANCE / DIV. HEAD FAST','FINANCE and ACCOUNTING','all',$notedeptterkait); ?> >
											</div>
										</div>
                                        
                                        
									<?php } else { ?>
                                        
                                        <div class="form-group">
											<label class="col-sm-2 control-label">Section Head</label> 
											<div class="col-sm-1">User : </div>
                                            <div class="col-sm-4">
													<?php $note_arr = usernote($vw['nobukti'],'SECTION HEAD','',''); 
													echo $note_arr["user"]; ?>
											</div>
                                           <div class="col-sm-1">Waktu :  </div>
                                            <div class="col-sm-4"><?php	echo $note_arr["tgl"]; ?></div>
										</div>
                                        <div class="form-group">
											<label class="col-sm-2 control-label"></label> 
                                            <div class="col-sm-1">Note : </div>
											<div class="col-sm-9">
												<input type="text" class="form-control" id="note_sectionhead" <?php note($vw['nobukti'],'SECTION HEAD','','',''); ?> >
											</div>
										</div>
                                        
                                        <div class="form-group">
											<label class="col-sm-2 control-label">ADH</label> 
											<div class="col-sm-1">User : </div>
                                            <div class="col-sm-4">
													<?php $note_arr = usernote($vw['nobukti'],'ADH','',''); 
													echo $note_arr["user"]; ?>
											</div>
                                           <div class="col-sm-1">Waktu :  </div>
                                            <div class="col-sm-4"><?php	echo $note_arr["tgl"]; ?></div>
										</div>
                                        <div class="form-group">
											<label class="col-sm-2 control-label"></label> 
                                            <div class="col-sm-1">Note : </div>
											<div class="col-sm-9">
												<input type="text" class="form-control" id="note_adh" <?php note($vw['nobukti'],'ADH','','',''); ?> >
											</div>
										</div>
                                        
                                        <div class="form-group">
											<label class="col-sm-2 control-label">Kepala Cabang</label> 
											<div class="col-sm-1">User : </div>
                                            <div class="col-sm-4">
													<?php $note_arr = usernote($vw['nobukti'],'KEPALA CABANG','',''); 
													echo $note_arr["user"]; ?>
											</div>
                                           <div class="col-sm-1">Waktu :  </div>
                                            <div class="col-sm-4"><?php	echo $note_arr["tgl"]; ?></div>
										</div>
                                        <div class="form-group">
											<label class="col-sm-2 control-label"></label> 
                                            <div class="col-sm-1">Note : </div>
											<div class="col-sm-9">
												<input type="text" class="form-control" id="note_branch_manager" <?php note($vw['nobukti'],'KEPALA CABANG','','',''); ?> >
											</div>
										</div>
                                        
										<?php if ($vw['tipe']=='BIAYA') { ?>
										<div class="form-group">
											<label class="col-sm-2 control-label">OM</label> 
											<div class="col-sm-1">User : </div>
                                            <div class="col-sm-4">
													<?php $note_arr = usernote($vw['nobukti'],'OM','',''); 
													echo $note_arr["user"]; ?>
											</div>
                                           <div class="col-sm-1">Waktu :  </div>
                                            <div class="col-sm-4"><?php	echo $note_arr["tgl"]; ?></div>
										</div>
                                        <div class="form-group">
											<label class="col-sm-2 control-label"></label> 
                                            <div class="col-sm-1">Note : </div>
											<div class="col-sm-9">
												<input type="text" class="form-control" id="note_om" <?php note($vw['nobukti'],'OM','','',''); ?> >
											</div>
										</div>
										<?php } ?>
									<?php } ?>
								</section>
							</div>
  							<div class="tab-pane" id="tab2">
  								<section>
	  								<div class="form-group">
	  									<div class="col-sm-6">
											<?php $ext1 = substr($vw['upload_file'], strrpos($vw['upload_file'], '.')+1); ?>
											<?php if ($ext1=='jpg' or $ext1=='jpeg') { ?>
												<a data-ssi_imgGroup="group1" href="system/files/<?php echo $vw['upload_file']; ?>"
													title="Metallica" class="ssi-imgBox"> 
													<img src="system/files/<?php echo $vw['upload_file']; ?>" alt="Fountain" class="img-responsive img-thumbnail">
									            </a>
											<?php } else { ?>
												<div class="input-group"> 
													<input type="text" class="form-control" value="<?php echo $vw['upload_file']; ?>" disabled="">
													<span class="input-group-addon" style="padding: 0;min-width: 0;">
														<button type="button" style="padding: 2px 10px;border: 0;" onclick="getFile('<?php echo $vw['upload_file']; ?>');">
															<i class="fa fa-download"></i>
														</button>
													</span>
												</div>
	  										<?php } ?>
	  									</div>
	  									<div class="col-sm-6">
	  										<?php $ext2 = substr($vw['upload_fp'], strrpos($vw['upload_fp'], '.')+1); ?>
	  										<?php if ($ext2=='jpg' or $ext2=='jpeg') { ?>
												<a data-ssi_imgGroup="group1" href="system/files/<?php echo $vw['upload_file']; ?>"
													title="Metallica" class="ssi-imgBox"> 
													<img src="system/files/<?php echo $vw['upload_file']; ?>" alt="Fountain" class="img-responsive img-thumbnail">
									            </a>
											<?php } else { ?>
												<div class="input-group"> 
													<input type="text" class="form-control" value="<?php echo $vw['upload_fp']; ?>" disabled="">
													<span class="input-group-addon" style="padding: 0;min-width: 0;">
														<button type="button" style="padding: 2px 10px;border: 0;" onclick="getFile('<?php echo $vw['upload_fp']; ?>');">
															<i class="fa fa-download"></i>
														</button>
													</span>
												</div>
	  										<?php } ?>
	  									</div>
	  								</div>
	  							</section>
  							</div>
	  					</div>
					</div>
				</div>
				<div class="panel-footer">
					<div class="row">
					    <div class="col-sm-10 col-sm-offset-2">
					        <div class="btn-toolbar">
					        	<button type="button" id="back" class="btn-primary btn">Back</button>
					            <button type="button" id="approve" class="btn-primary btn">Approve</button>
					            <?php if ($_SESSION['level']!='TAX'){ ?>
					            	<button type="button" id="reject" class="btn-primary btn">Reject</button>
					            <?php } ?>
								<?php if ($_SESSION['level']=='ACCOUNTING' or $_SESSION['level']=='ADH' or $_SESSION['level']=='TAX') { ?>
									<button type="button" id="edit" class="btn-primary btn">Edit</button>
									<button type="button" id="update" class="btn-primary btn" style="display: none;">Save</button>
									<button type="button" id="cancel" class="btn-default btn" style="display: none;">Cancel</button>
									<input type="hidden" id="valEdit" value="0">
								<?php } ?>
					        </div>
					    </div>
					</div>
				</div>
			</form>
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

<div class="modal fade modals" id="alasaReject" tabindex="-1" role="dialog" aria-labelledby="alasaReject" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h2 class="modal-title">Alasan Reject</h2>
			</div>
			<div class="modal-body" style="padding: 0;"></div>
		</div>
	</div>
</div>
<iframe id="myFrame" src="" style="display: none"></iframe>
<?php

	function note($nobukti,$level,$div,$dept, $deptterkait){
		$lastVal = mssql_fetch_array(mssql_query("select level from DataEvoVal where nobukti = '".$nobukti."' and isnull(validasi,'')=''
												and isnull(deptterkait,'') = ''  order by IdVal desc"));
		$user = mssql_fetch_array(mssql_query("select * from sys_user where IdUser = '".$_SESSION['UserID']."'"));
		
		if ($_SESSION['level']==$level and $lastVal['level']==$level and $div=='' and $dept=='') {
			$readonly  = ""; 
		} else if ($lastVal['level']==$level and $lastVal['level']=='DEPT. HEAD FINANCE' and $_SESSION['level']=='DEPT. HEAD' and $div=='FINANCE and ACCOUNTING' and $dept=='FINANCE') {
			$readonly  = ""; 
		} else if ($lastVal['level']==$level and $lastVal['level']=='DEPT. HEAD FINANCE / DIV. HEAD FAST' and $_SESSION['level']=='DIV. HEAD' and $div=='FINANCE and ACCOUNTING' and $dept=='all') {
			$readonly  = ""; 
		} else {
			$readonly  = "readonly"; 
		}
		
		$note = mssql_fetch_array(mssql_query("select ketvalidasi as note from DataEvoVal where nobukti = '".$nobukti."' and level = '$level'
					and isnull(deptterkait,'') = '' "));
		echo 'value="'.$note['note'].' " '.$readonly.'';		
	}
	
	function number($num){
		return number_format($num,0,",",".");
	}
	
	function usernote($nobukti,$level,$div,$dept){	
		
		$lastVal = mssql_fetch_array(mssql_query("select level from DataEvoVal where nobukti = '".$nobukti."' and isnull(validasi,'')=''
												and isnull(deptterkait,'') = ''  order by IdVal desc"));
		
		//echo $level.$lastVal['level'].$_SESSION['level'];										
		if ($div=='' and $dept=='') {
			//echo "iki1";
			$qry = "select a.ketvalidasi as note,
													case a.uservalidasi when '########' then a.uservalidasi else b.namauser end namauser,  
													CONVERT(varchar,a.tglvalidasi,105) + ' ' +CONVERT(varchar,a.tglvalidasi,108) tglvalidasi, 
													isnull(deptterkait,'') deptterkait
													from DataEvoVal a
													left join sys_user b on a.uservalidasi = b.IdUser
													where nobukti = '".$nobukti."' and level = '".$level."' 
													and isnull(deptterkait,'') = '' ";
			 // and isnull(validasi,'')=''
													
		/*} else if ($div!='' and $dept!='all') {
			//echo "iki2";
			$qry = "
				select a.ketvalidasi as note, case a.uservalidasi when '########' then a.uservalidasi else b.namauser end namauser,   
				CONVERT(varchar,a.tglvalidasi,105) + ' ' +CONVERT(varchar,a.tglvalidasi,108) tglvalidasi, isnull(deptterkait,'') deptterkait
				from DataEvoVal a
				left join sys_user b on a.uservalidasi = b.IdUser
				where nobukti = '".$nobukti."' and level = 'DEPT. HEAD FINANCE / DIV. HEAD FAST'
				and isnull(deptterkait,'') = '' "; //  and isnull(validasi,'')=''
		*/
		} else if ($level=='DEPT. HEAD FINANCE') {
			//echo "iki2";
			$qry = "
				select a.ketvalidasi as note, case a.uservalidasi when '########' then a.uservalidasi else b.namauser end namauser,   
				CONVERT(varchar,a.tglvalidasi,105) + ' ' +CONVERT(varchar,a.tglvalidasi,108) tglvalidasi, isnull(deptterkait,'') deptterkait
				from DataEvoVal a
				left join sys_user b on a.uservalidasi = b.IdUser
				where nobukti = '".$nobukti."' and level = 'DEPT. HEAD FINANCE'
				and isnull(deptterkait,'') = '' "; //  and isnull(validasi,'')=''
		
		} else if ($level=='DEPT. HEAD FINANCE / DIV. HEAD FAST') {
			//echo "iki2";
			$qry = "
				select a.ketvalidasi as note, case a.uservalidasi when '########' then a.uservalidasi else b.namauser end namauser,   
				CONVERT(varchar,a.tglvalidasi,105) + ' ' +CONVERT(varchar,a.tglvalidasi,108) tglvalidasi, isnull(deptterkait,'') deptterkait
				from DataEvoVal a
				left join sys_user b on a.uservalidasi = b.IdUser
				where nobukti = '".$nobukti."' and level = 'DEPT. HEAD FINANCE / DIV. HEAD FAST'
				and isnull(deptterkait,'') = '' "; //  and isnull(validasi,'')=''
		
		} 
		//echo "<pre>".$qry."</pre>";
		
		$stm = mssql_query($qry);
		$dt = mssql_fetch_array($stm);
		$jml = mssql_num_rows($stm);
		$note = $dt['note'];
		$deptterkait = trim($dt['deptterkait']);
		
		if ($jml>0) {
			if ($deptterkait=='') {
				
				if ($_SESSION['level']==$level and $lastVal['level']==$level) {
					$user = $_SESSION['UserName'];			
					//$user = $dt['namauser'];
					$tgl = date("d-m-Y H:i:s");
					//$tgl = $dt['tglvalidasi'];
			
				} else {
					if ($lastVal['level']==$level and $lastVal['level']=='DEPT. HEAD FINANCE' and $_SESSION['level']=='DEPT. HEAD' and $div=='FINANCE and ACCOUNTING' and $dept=='FINANCE') {
						
						//$user = $_SESSION['UserName'];
						$user = $_SESSION['UserName'];			
						//$user = $dt['namauser'];
						$tgl = date("d-m-Y H:i:s");
						
					} else if ($lastVal['level']==$level and $lastVal['level']=='DEPT. HEAD FINANCE / DIV. HEAD FAST' and $_SESSION['level']=='DIV. HEAD' and $div=='FINANCE and ACCOUNTING') {
						$user = $_SESSION['UserName'];			
						//$user = $dt['namauser'];
						$tgl = date("d-m-Y H:i:s");
					} else {		
						$user = $dt['namauser'];
						$tgl = $dt['tglvalidasi'];
					}
				}
				if ($note=='Concurrent' and $user==$_SESSION['UserName']) {
					$tgl = date("d-m-Y H:i:s");
				}
				
			}
		}
		$return = array("user"=>$user, "tgl"=>$tgl);
		return $return;
	}
?>
<script src="assets/js/jquery-2.2.4.min.js"></script>
<script src="assets/js/ssi-modal.js"></script>