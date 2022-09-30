jQuery(document).ready(function($) {
	$(".acc-menu #1").css('display','block');
	$('#cancel').click(function(event){
		event.preventDefault();
		history.back(1);
	});

	$('#new').click(function(){
		var IdUser = $('#kodeUser').val();
		var namaUser = $('#namaUser').val();
		var Email = $('#Email').val();
		var nik = $('#nik').val()
		var no_tlp = $('#no_tlp').val()
		var KodeDealer = $('#KodeDealer').val();
		var tipe = $('#tipe').val();
		var divisi = $('#divisi').val();
		var department = $('#department').val();
		var tipeAju = $('#tipeAju').val();
		var IdAtasan = $('#IdAtasan').val();
		var posAkunStart = $('#posAkunStart').val();
		var posAkunEnd = $('#posAkunEnd').val();
		var posAkunHtgStart = $('#posAkunHtgStart').val();
		var posAkunHtgEnd = $('#posAkunHtgEnd').val();
		var statususer = $('#statususer').val();

		var akses = [];
		$("input[name='IdMenu[]']:checked").each(function(){
		    var dt = $(this).val();
		    akses += dt+"#";
		});
		var akses = akses.slice(0,-1);
		if (IdUser=='') {
			onload = needValue('Pengajuan Voucher Payment','Required Kode User!');
		} else if (namaUser=='') {
			onload = needValue('Pengajuan Voucher Payment','Required Nama User!');
		} else if (Email=='') {
			onload = needValue('Pengajuan Voucher Payment','Required Email!');
		} else if (nik=='') {
			onload = needValue('Pengajuan Voucher Payment','Required NIK!');
		} else if (no_tlp=='') {
			onload = needValue('Pengajuan Voucher Payment','Required No Telpon!');
		} else if (KodeDealer=='') {
			onload = needValue('Pengajuan Voucher Payment','Required Kode Dealer!');
		} else if (tipe=='') {
			onload = needValue('Pengajuan Voucher Payment','Required Level!');
		} else if (divisi=='') {
			onload = needValue('Pengajuan Voucher Payment','Required Divisi!');
		} else if (department=='' && KodeDealer=='2010' && tipe!='TAX' && tipe!='ACCOUNTING' && tipe!='FINANCE' && tipe!='DIREKSI' && tipe!='DIREKSI 2' && tipe!='KASIR') {
			onload = needValue('Pengajuan Voucher Payment','Required Departmen!');
		} else if (IdAtasan=='' && tipe!='TAX' && tipe!='ACCOUNTING' && tipe!='FINANCE' && tipe!='DIREKSI'  && tipe!='DIREKSI 2' && tipe!='KASIR') {
			onload = needValue('Pengajuan Voucher Payment','Required Nama Atasan!');
		} else if (tipeAju=='') {
			onload = needValue('Pengajuan Voucher Payment','Required Tipe Pengajuan!');
		} else {
			$.ajax({ 
			    url: 'system/control/user.php',
			    data: {	
			    	action:'new', 
			    	'IdUser': IdUser, 'namaUser': namaUser, 'Email': Email, 'nik': nik, 'no_tlp': no_tlp, 'KodeDealer': KodeDealer, 
			    	'tipe': tipe, 'divisi': divisi, 'department': department, 'tipeAju': tipeAju, 'IdAtasan': IdAtasan, 
			    	'posAkunStart': posAkunStart, 'posAkunEnd': posAkunEnd, 'posAkunHtgStart': posAkunHtgStart, 'posAkunHtgEnd': posAkunHtgEnd, 
			    	'akses': akses, 'statususer': statususer
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
					    title: "User",
					    buttons: {
					        main: {
					            label: "Ok",
					            className: "btn-sm btn-primary",
					            callback: function() {
					                document.location.href="master-user";
					            }
					        }
					    }
					});
				}
			});
		}
	});

	$('#edit').click(function(){
		var IdUser = $('#kodeUser').val();
		var namaUser = $('#namaUser').val();
		var Email = $('#Email').val();
		var nik = $('#nik').val()
		var no_tlp = $('#no_tlp').val()
		var KodeDealer = $('#KodeDealer').val();
		var tipe = $('#tipe').val();
		var divisi = $('#divisi').val();
		var department = $('#department').val();
		var tipeAju = $('#tipeAju').val();
		var IdAtasan = $('#IdAtasan').val();
		var posAkunStart = $('#posAkunStart').val();
		var posAkunEnd = $('#posAkunEnd').val();
		var posAkunHtgStart = $('#posAkunHtgStart').val();
		var posAkunHtgEnd = $('#posAkunHtgEnd').val();
		var statususer = $('#statususer').val();
		
		var akses = [];
		$("input[name='IdMenu[]']:checked").each(function(){
		    var dt = $(this).val();
		    akses += dt+"#";
		});
		var akses = akses.slice(0,-1);
		if (IdUser=='') {
			onload = needValue('Pengajuan Voucher Payment','Required Kode User!');
		} else if (namaUser=='') {
			onload = needValue('Pengajuan Voucher Payment','Required Nama User!');
		} else if (Email=='') {
			onload = needValue('Pengajuan Voucher Payment','Required Email!');
		} else if (nik=='') {
			onload = needValue('Pengajuan Voucher Payment','Required NIK!');
		} else if (no_tlp=='') {
			onload = needValue('Pengajuan Voucher Payment','Required No Telpon!');
		} else if (KodeDealer=='') {
			onload = needValue('Pengajuan Voucher Payment','Required Kode Dealer!');
		} else if (tipe=='') {
			onload = needValue('Pengajuan Voucher Payment','Required Level!');
		} else if (divisi=='') {
			onload = needValue('Pengajuan Voucher Payment','Required Divisi!');
		} else if (department=='' && KodeDealer=='2010' && tipe!='TAX' && tipe!='ACCOUNTING' && tipe!='FINANCE' && tipe!='DIREKSI' && tipe!='DIREKSI 2' && tipe!='KASIR') {
			onload = needValue('Pengajuan Voucher Payment','Required Departmen!');
		} else if (IdAtasan=='' && tipe!='TAX' && tipe!='ACCOUNTING' && tipe!='FINANCE' && tipe!='DIREKSI' && tipe!='DIREKSI 2' && tipe!='KASIR') {
			onload = needValue('Pengajuan Voucher Payment','Required Nama Atasan!');
		} else if (tipeAju=='') {
			onload = needValue('Pengajuan Voucher Payment','Required Tipe Pengajuan!');
		} else {
			$.ajax({ 
			    url: 'system/control/user.php',
			    data: {	
			    	action:'edit', 
			    	'IdUser': IdUser, 'namaUser': namaUser, 'Email': Email, 'nik': nik, 'no_tlp': no_tlp, 'KodeDealer': KodeDealer, 
			    	'tipe': tipe, 'divisi': divisi, 'department': department, 'tipeAju': tipeAju, 'IdAtasan': IdAtasan, 
			    	'posAkunStart': posAkunStart, 'posAkunEnd': posAkunEnd, 'posAkunHtgStart': posAkunHtgStart, 'posAkunHtgEnd': posAkunHtgEnd, 
			    	'akses': akses, 'statususer': statususer
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
					    title: "User",
					    buttons: {
					        main: {
					            label: "Ok",
					            className: "btn-sm btn-primary",
					            callback: function() {
					                document.location.href="master-user";
					            }
					        }
					    }
					});
				}
			});
		}
	});

	$('.flexme1').flexigrid({
        height : 200,
        width : 'auto',
        showToggleBtn : false
    });
});