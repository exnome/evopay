jQuery(document).ready(function($) {
	$(".acc-menu #1").css('display','block');
	$('#cancel').click(function(event){
		event.preventDefault();
		history.back(1);
	});

	$('#new').click(function(){
		var KodeDealer = $('#KodeDealer').val();
		var divisi = $('#divisi').val();
		var department = $('#department').val();
		var user = $('#user').val();
		var validator_nid = $('#validator_nid').val();
		

		if (KodeDealer=='') {
			onload = needValue('Pengajuan Voucher Payment','Required Kode Dealer!');
		} else if (divisi=='') {
			onload = needValue('Pengajuan Voucher Payment','Required Divisi!');
		} else if (department=='') {
			onload = needValue('Pengajuan Voucher Payment','Required Departmen!');
		} else if (user=='') {
			onload = needValue('Pengajuan Voucher Payment','Required User!');
		
		} else {
			var user2 = "";
			var user3 = "";
			for (i=0;i<validator_nid;i++) {
				if ($('#user'+i).val()!='') {
					user2 = $('#user'+i).val() + ';';
					user3 = user3.concat(user2);
				}
			}
			
			$.ajax({ 
			    url: 'system/control/deptterkait.php',
			    data: {	
			    	action:'new', 
			    	'KodeDealer': KodeDealer, 'divisi': divisi, 'department': department, 'user': user, 'user3': user3
			    },
			    type: 'post',
			    beforeSend: function(){
			    	onload = showLoading();
			    },
			    success: function(output) {
					//alert(output);
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
					                document.location.href="master-deptterkait";
					            }
					        }
					    }
					});
				}
			});
			
		}
	});

	$('#edit').click(function(){
		var KodeDealer = $('#KodeDealer').val();
		var divisi = $('#divisi').val();
		var department = $('#department').val();
		var user = $('#user').val();
		var validator_nid = $('#validator_nid').val();

		if (KodeDealer=='') {
			onload = needValue('Pengajuan Voucher Payment','Required Kode Dealer!');
		} else if (divisi=='') {
			onload = needValue('Pengajuan Voucher Payment','Required Divisi!');
		} else if (department=='') {
			onload = needValue('Pengajuan Voucher Payment','Required Departmen!');
		} else if (user=='') {
			onload = needValue('Pengajuan Voucher Payment','Required User!');
		
		} else {
			var user2 = "";
			var user3 = "";
			for (i=0;i<validator_nid;i++) {
				if ($('#user'+i).val()!='') {
					user2 = $('#user'+i).val() + ';';
					user3 = user3.concat(user2);
				}
			}
			
			$.ajax({ 
			    url: 'system/control/deptterkait.php',
			    data: {	
			    	action:'edit', 
			    	'KodeDealer': KodeDealer, 'divisi': divisi, 'department': department, 'user': user, 'user3': user3
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
					                document.location.href="master-deptterkait";
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