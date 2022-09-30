jQuery(document).ready(function($) {
	$(".acc-menu #1").css('display','block');
	$('#cancel').click(function(event){
		event.preventDefault();
		history.back(1);
	});

	$('#new').click(function(){
		var akun_start_hutang = $('#akun_start_hutang').val();
		var akun_end_hutang = $('#akun_end_hutang').val();
		var akun_start_biaya = $('#akun_start_biaya').val();
		var akun_end_biaya = $('#akun_end_biaya').val();
		var akun_biaya = $("#akun_biaya").val();
		var pety_cash = $("#pety_cash").val();
		var skip_direksi = $("#skip_direksi").val();
		var skip_direksi2 = $("#skip_direksi2").val();
		var pph_21 = $("#pph_21").val();
		var pph_22 = $("#pph_22").val();
		var pph_23 = $("#pph_23").val();
		var pph_25 = $("#pph_25").val();
		var pph_21_pihak_3 = $("#pph_21_pihak_3").val();
		var pph_4 = $("#pph_4").val();
		var non_pph_21 = $("#non_pph_21").val();
		var non_pph_22 = $("#non_pph_22").val();
		var non_pph_23 = $("#non_pph_23").val();
		var non_pph_25 = $("#non_pph_25").val();
		var non_pph_21_pihak_3 = $("#non_pph_21_pihak_3").val();
		var non_pph_4 = $("#non_pph_4").val();
		var akun_sublet = $("#akun_sublet").val();
		var akun_ppn = $("#akun_ppn").val();
		var akun_ppn_aksesoris = $("#akun_ppn_aksesoris").val();
		var akun_ppn_bengkel = $("#akun_ppn_bengkel").val();
		var akun_ppn_mobil = $("#akun_ppn_mobil").val();
		var akun_ppn_part = $("#akun_ppn_part").val();
		var akun_ppn_sublet = $("#akun_ppn_sublet").val();
		var akun_ppn_sewa = $("#akun_ppn_sewa").val();

		$.ajax({ 
		    url: 'system/control/akun.php',
		    data: {
		    	action:'new', 
		    	'akun_start_hutang': akun_start_hutang, 
		    	'akun_end_hutang': akun_end_hutang, 
		    	'akun_start_biaya': akun_start_biaya, 
		    	'akun_end_biaya': akun_end_biaya,
		    	'akun_biaya': akun_biaya,
				'pety_cash': pety_cash,
				'skip_direksi': skip_direksi,
				'skip_direksi2': skip_direksi2,
				'pph_21': pph_21, 'pph_22': pph_22,
				'pph_23': pph_23, 'pph_25': pph_25,
				'pph_21_pihak_3': pph_21_pihak_3, 'pph_4': pph_4,
				'non_pph_21': non_pph_21, 'non_pph_22': non_pph_22,
				'non_pph_23': non_pph_23, 'non_pph_25': non_pph_25,
				'non_pph_21_pihak_3': non_pph_21_pihak_3, 'non_pph_4': non_pph_4,
				'akun_sublet': akun_sublet, 
				'akun_ppn': akun_ppn,'akun_ppn_aksesoris': akun_ppn_aksesoris,
				'akun_ppn_bengkel': akun_ppn_bengkel,'akun_ppn_mobil': akun_ppn_mobil,
				'akun_ppn_part': akun_ppn_part,'akun_ppn_sublet': akun_ppn_sublet,'akun_ppn_sewa': akun_ppn_sewa
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
				    title: "Master Akun",
				    buttons: {
				        main: {
				            label: "Ok",
				            className: "btn-sm btn-primary",
				            callback: function() {
				                document.location.href="master-akun";
				            }
				        }
				    }
				});
			}
		});
	});
});

function add(id,pph,kodelgn){
	if (id=='1') {
		var judul = "Pajak Ber NPWP";
	} else if (id=='0') {
		var judul = "Pajak Ber Non NPWP";
	}
	bootbox.dialog({
	    message: '<form action="" method="post" class="form-horizontal"> <div class="form-group" style="margin-bottom: 2px;"> <label class="col-md-4 control-label">Trf. Pajak '+pph+'</label> <div class="col-md-8"> <input class="form-control" id="trfpajak" autocomplete="off"> </div></div></form>',
	    title: judul,
	    buttons: {
	        main: {
	            label: "Save",
	            className: "btn-sm btn-primary",
	            callback: function() {
	                $.ajax({ 
					    url: 'system/control/akun.php',
					    data: {
					    	action:'new-tarif', 
					    	'jns_pph': pph, 
					    	'npwp': id,
					    	'kodelgn': kodelgn, 
					    	'tarif_persen': $("#trfpajak").val()
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
							    title: judul,
							    buttons: {
							        main: {
							            label: "Ok",
							            className: "btn-sm btn-primary",
							            callback: function() {
							                document.location.href="master-akun";
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
}