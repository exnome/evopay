jQuery(document).ready(function($) {
	onload = prodash();
	onload = profund();
});
function prodash(){
	var IdUser = $("#IdUser").val();
	$.ajax({ 
		url: 'system/data/home.php',
		data: { action:'prodash', 'IdUser' : IdUser },
		type: 'post',
		beforeSend: function(){
			$("#tot_wait_1").html('<i class="fa fa-spin fa-spinner"></i>');
			$("#tot_wait_2").html('<i class="fa fa-spin fa-spinner"></i>');
			$("#tot_wait_3").html('<i class="fa fa-spin fa-spinner"></i>');
			$("#tot_wait_4").html('<i class="fa fa-spin fa-spinner"></i>');
			$("#tot_wait_5").html('<i class="fa fa-spin fa-spinner"></i>');
			$("#tot_wait_6").html('<i class="fa fa-spin fa-spinner"></i>');
			$("#tot_wait_7").html('<i class="fa fa-spin fa-spinner"></i>');
			$("#tot_wait_8").html('<i class="fa fa-spin fa-spinner"></i>');
			$("#tot_wait_9").html('<i class="fa fa-spin fa-spinner"></i>');
			$("#tot_wait_10").html('<i class="fa fa-spin fa-spinner"></i>');
			$("#tot_wait_11").html('<i class="fa fa-spin fa-spinner"></i>');
			$("#tot_wait_12").html('<i class="fa fa-spin fa-spinner"></i>');
			$("#tot_wait_13").html('<i class="fa fa-spin fa-spinner"></i>');
			$("#tot_wait_14").html('<i class="fa fa-spin fa-spinner"></i>');
			$("#tot_wait_15").html('<i class="fa fa-spin fa-spinner"></i>');
			$("#tot_wait_16").html('<i class="fa fa-spin fa-spinner"></i>');
			$("#tot_wait_acc").html('<i class="fa fa-spin fa-spinner"></i>');
			$("#tot_wait_direksi2").html('<i class="fa fa-spin fa-spinner"></i>');
		},
		success: function(output) {
			var data = output.split("#");
			$("#tot_wait_1").html(data[0]);
			$("#tot_wait_2").html(data[1]);
			$("#tot_wait_3").html(data[2]);
			$("#tot_wait_4").html(data[3]);
			$("#tot_wait_5").html(data[4]);
			$("#tot_wait_6").html(data[5]);
			$("#tot_wait_7").html(data[6]);
			$("#tot_wait_8").html(data[7]); //tax
			$("#tot_wait_9").html(data[8]);
			$("#tot_wait_10").html(data[9]);
			$("#tot_wait_11").html(data[10]);
			$("#tot_wait_12").html(data[11]);
			$("#tot_wait_13").html(data[12]);
			$("#tot_wait_14").html(data[13]);
			$("#tot_wait_15").html(data[14]);	
			
			
			$("#tot_wait_acc").html(data[15]);
			$("#tot_wait_direksi2").html(data[16]);
			
			$("#tot_wait_16").html(data[17]);
			
		}
	});
}
function profund(){
	var IdUser = $("#IdUser").val();
	$.ajax({ 
		url: 'system/data/home.php',
		data: { action:'profund', 'IdUser' : IdUser },
		type: 'post',
		beforeSend: function(){
			$("#tot_fund_1").html('<i class="fa fa-spin fa-spinner"></i>');
			$("#tot_fund_2").html('<i class="fa fa-spin fa-spinner"></i>');
			$("#tot_fund_3").html('<i class="fa fa-spin fa-spinner"></i>');
		},
		success: function(output) {
			var data = output.split("#");
			$("#tot_fund_1").html(data[0]);
			$("#tot_fund_2").html(data[1]);
			$("#tot_fund_3").html(data[2]);
		}
	});
}