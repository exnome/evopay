jQuery(document).ready(function($) {
	var IdUser = $("#IdUser").val();
	$('.info-tiles').click(function(event){
		var id = $(this).attr('id');
		var sesi_kodedealer	= $("#sesi_kodedealer").val();
		
		if (sesi_kodedealer!="2010") {
		if (id == "wait_1") {
			$(".tiles-footer").html('&nbsp;');
			$("#capt_wait_1").html('SELECTED');
			var lvl = "ACCOUNTING";
			$("#list_title").html('Request Waiting on Checker');
		} else if (id == "wait_2") {
			$(".tiles-footer").html('&nbsp;');
			$("#capt_wait_2").html('SELECTED');
			var lvl = "SECTION HEAD";
			$("#list_title").html('Request Waiting on Approval Section Head');
		} else if (id == "wait_3") {
			$(".tiles-footer").html('&nbsp;');
			$("#capt_wait_3").html('SELECTED');
			var lvl = "ADH";
			$("#list_title").html('Request Waiting on Approval ADH');
		} else if (id == "wait_4") {
			$(".tiles-footer").html('&nbsp;');
			$("#capt_wait_4").html('SELECTED');
			var lvl = "KEPALA CABANG";
			$("#list_title").html('Request Waiting on Approval Kacab');
		} else if (id == "wait_5") {
			$(".tiles-footer").html('&nbsp;');
			$("#capt_wait_5").html('SELECTED');
			var lvl = "KASIR";
			$("#list_title").html('Ready For Settlement');
		}
		
		} else {
			// HO
			if (id == "wait_tax") {
				$(".tiles-footer").html('&nbsp;');
				$("#capt_wait_tax").html('SELECTED');
				var lvl = "TAX";
				$("#list_title").html('Waiting Checker Tax');
			} else if (id == "wait_accounting") {
				$(".tiles-footer").html('&nbsp;');
				$("#capt_wait_accounting").html('SELECTED');
				var lvl = "ACCOUNTING";
				$("#list_title").html('Waiting Accounting');
			} else if (id == "wait_section") {
				$(".tiles-footer").html('&nbsp;');
				$("#capt_wait_section").html('SELECTED');
				var lvl = "SECTION HEAD";
				$("#list_title").html('Waiting Approval Section Head');
			} else if (id == "wait_dept_head") {
				$(".tiles-footer").html('&nbsp;');
				$("#capt_wait_dept_head").html('SELECTED');
				var lvl = "DEPT. HEAD";
				$("#list_title").html('Waiting Approval Dept. Head');
			} else if (id == "wait_div_head") {
				$(".tiles-footer").html('&nbsp;');
				$("#capt_wait_div_head").html('SELECTED');
				var lvl = "DIV. HEAD";
				$("#list_title").html('Waiting Approval Div. Head');
			} else if (id == "wait_direksi") {
				$(".tiles-footer").html('&nbsp;');
				$("#capt_wait_direksi").html('SELECTED');
				var lvl = "DIREKSI";
				$("#list_title").html('Waiting Approval Direksi 1');
			} else if (id == "wait_direksi2") {
				$(".tiles-footer").html('&nbsp;');
				$("#capt_wait_direksi2").html('SELECTED');
				var lvl = "DIREKSI 2";
				$("#list_title").html('Waiting Approval Direksi 2');
			} else if (id == "wait_fin") {
				$(".tiles-footer").html('&nbsp;');
				$("#capt_wait_fin").html('SELECTED');
				var lvl = "FINANCE";
				$("#list_title").html('Waiting Checker Finance');
			
			} else if (id == "wait_releaser") {
				$(".tiles-footer").html('&nbsp;');
				$("#capt_wait_releaser").html('SELECTED');
				var lvl = "DEPT. HEAD FINANCE";
				$("#list_title").html('Waiting Approval Releaser 1');
			
			
			} else if (id == "wait_div_fast") {
				$(".tiles-footer").html('&nbsp;');
				$("#capt_wait_div_fast").html('SELECTED');
				var lvl = "DEPT. HEAD FINANCE / DIV. HEAD FAST";
				// var lvl = "DIV. HEAD";
				$("#list_title").html('Waiting Approval Releaser 2');
			
		
				
			} else if (id == "wait_dept_lain") {
				$(".tiles-footer").html('&nbsp;');
				$("#capt_wait_dept_lain").html('SELECTED');
				var lvl = "WAITDEPTTERKAIT";
				// var lvl = "DIV. HEAD";
				$("#list_title").html('Wait Related Department');
			
			} else if (id == "req_dept_lain") {
				$(".tiles-footer").html('&nbsp;');
				$("#capt_req_dept_lain").html('SELECTED');
				var lvl = "REQDEPTTERKAIT";
				// var lvl = "DIV. HEAD";
				$("#list_title").html('Request Others Department');
			}
		}
		
		
		$("#srcLevel").val(lvl);
		
		var srcLevel	= $("#srcLevel").val();
		var sesi_level	= $("#sesi_level").val();
		var user_div	= $("#user_div").val();
		var user_dept	= $("#user_dept").val();
		var sesi_dept	= $("#sesi_dept").val();
		var evo_dept	= $("#evo_dept").val();
	
		var dt = [{ name:'level',value: lvl },{ name:'IdUser',value: IdUser },{ name:'sesi_level',value: sesi_level }];
		$(".flexme3").flexOptions({url: "system/data/home.php", params: dt }).flexReload();
		
		
		if (srcLevel==sesi_level) {	
			//alert(srcLevel + "_" + sesi_level);
			
			if (sesi_level=='DIREKSI' || sesi_level=='DIREKSI 2' || sesi_level=='DIV. HEAD') {
				$("#btn-val").html('<button type="button" onclick="btnval(\'val\');" class="btn-primary btn btn-val">Single Approve</button>');
				$("#btn-approve").html('<button type="button" onclick="btnapprove(\'val\');" class="btn-info btn btn-val">Batch Approve</button>');
				$("#btn-reject").html('<button type="button" onclick="btnreject(\'val\');" class="btn-danger btn btn-val">Reject</button>');
			
			} else if (sesi_level=='DEPT. HEAD' && user_div=='FINANCE and ACCOUNTING' && evo_dept=='FINANCE') {
				$("#btn-val").html('<button type="button" onclick="btnval(\'val\');" class="btn-primary btn btn-val">Single Approve</button>');
				$("#btn-approve").html('<button type="button" onclick="btnapprove(\'val\');" class="btn-info btn btn-val">Batch Approve</button>');
				$("#btn-reject").html('<button type="button" onclick="btnreject(\'val\');" class="btn-danger btn btn-val">Reject</button>');
				
			} else {
				$("#btn-val").html('<button type="button" onclick="btnval(\'val\');" class="btn-primary btn btn-val">Validasi</button>');
				$("#btn-approve").html('');
				$("#btn-reject").html('');
			}
			
			
		} else {
			//alert(srcLevel + "_beda_" + sesi_dept);
			$("#btn-approve").html('');
			$("#btn-reject").html('');
			
			if (sesi_level=='DEPT. HEAD' && srcLevel=='DEPT. HEAD FINANCE' && user_div == 'FINANCE and ACCOUNTING' && evo_dept=='FINANCE') {
				$("#btn-val").html('<button type="button" onclick="btnval(\'val\');" class="btn-primary btn btn-val">Single Approve</button>');
				$("#btn-approve").html('<button type="button" onclick="btnapprove(\'val\');" class="btn-info btn btn-val">Batch Approve</button>');
				$("#btn-reject").html('<button type="button" onclick="btnreject(\'val\');" class="btn-danger btn btn-val">Reject</button>');
				
			} else if (sesi_level=='DIV. HEAD' && srcLevel=='DEPT. HEAD FINANCE / DIV. HEAD FAST' && user_div == 'FINANCE and ACCOUNTING' && user_dept=='all') {
				$("#btn-val").html('<button type="button" onclick="btnval(\'val\');" class="btn-primary btn btn-val">Single Approve</button>');
				$("#btn-approve").html('<button type="button" onclick="btnapprove(\'val\');" class="btn-info btn btn-val">Batch Approve</button>');
				$("#btn-reject").html('<button type="button" onclick="btnreject(\'val\');" class="btn-danger btn btn-val">Reject</button>');
			
			} else {
				$("#btn-val").html('<button type="button" onclick="btnval(\'view\');" class="btn-primary btn btn-val">View Progress</button>');
			}
			
			
			if (sesi_level=='DIREKSI 2' && srcLevel=='DIREKSI 2') {
				$("#btn-val").html('<button type="button" onclick="btnval(\'val\');" class="btn-primary btn btn-val">Single Approve</button>');
				$("#btn-approve").html('<button type="button" onclick="btnapprove(\'val\');" class="btn-info btn btn-val">Batch Approve</button>');
				$("#btn-reject").html('<button type="button" onclick="btnreject(\'val\');" class="btn-danger btn btn-val">Reject</button>');
			}
			
			if (sesi_level=='DIREKSI'  && srcLevel=='DIREKSI') {
				$("#btn-val").html('<button type="button" onclick="btnval(\'val\');" class="btn-primary btn btn-val">Single Approve</button>');
				$("#btn-approve").html('<button type="button" onclick="btnapprove(\'val\');" class="btn-info btn btn-val">Batch Approve</button>');
				$("#btn-reject").html('<button type="button" onclick="btnreject(\'val\');" class="btn-danger btn btn-val">Reject</button>');
			}
			
			if (sesi_level=='DEPT. HEAD' && srcLevel=='DEPT. HEAD' && user_div=='FINANCE and ACCOUNTING' && evo_dept=='FINANCE') { 
				$("#btn-val").html('<button type="button" onclick="btnval(\'val\');" class="btn-primary btn btn-val">Single Approve</button>');
				$("#btn-approve").html('<button type="button" onclick="btnapprove(\'val\');" class="btn-info btn btn-val">Batch Approve</button>');
				$("#btn-reject").html('<button type="button" onclick="btnreject(\'val\');" class="btn-danger btn btn-val">Reject</button>');
			}
			
			if (srcLevel=='REQDEPTTERKAIT' && sesi_dept!='') {
				$("#btn-val").html('<button type="button" onclick="btnval(\'val\');" class="btn-primary btn btn-val">Validasi</button>');
			} 
			
		}
						
		//onload = proval();
		//onload = proval_tax();
		if (sesi_kodedealer!="2010") {
			
		} else {
			if (srcLevel=='TAX') {
				onload = proval_tax();
			} else if (srcLevel=='ACCOUNTING') {
				onload = proval_accounting();
			} else if (srcLevel=='SECTION HEAD') {
				onload = proval_section();
			} else if (srcLevel=='DEPT. HEAD') {
				onload = proval_dept();
			} else if (srcLevel=='DIV. HEAD') {
				onload = proval_div();
			} else if (srcLevel=='DIREKSI') {
				onload = proval_direksi();
			} else if (srcLevel=='DIREKSI 2') {
				onload = proval_direksi2();
			} else if (srcLevel=='FINANCE') {
				onload = proval_fin();
				
			} else if (srcLevel=='DEPT. HEAD FINANCE') {
				onload = proval_releaser1();
			} else if (srcLevel=='DEPT. HEAD FINANCE / DIV. HEAD FAST') {
				onload = proval_releaser2();
				
			} else if (srcLevel=='REQDEPTTERKAIT') {
				onload = proval_req(); 				
			} else if (srcLevel=='WAITDEPTTERKAIT') {
				onload = proval_wait();
			}
		}
		
	});
	
	
	var srcLevel	= $("#srcLevel").val();
	var sesi_level	= $("#sesi_level").val();
	var user_div	= $("#user_div").val();
	var user_dept	= $("#user_dept").val();
	var sesi_kodedealer	= $("#sesi_kodedealer").val();
	var sesi_dept	= $("#sesi_dept").val();
	var evo_dept	= $("#evo_dept").val();
	
	if (srcLevel==sesi_level) {	
		if (sesi_level=='DIREKSI' || sesi_level=='DIREKSI 2' || sesi_level=='DIV. HEAD') {
			$("#btn-val").html('<button type="button" onclick="btnval(\'val\');" class="btn-primary btn btn-val">Single Approve</button>');
			$("#btn-approve").html('<button type="button" onclick="btnapprove(\'val\');" class="btn-info btn btn-val">Batch Approve</button>');
			$("#btn-reject").html('<button type="button" onclick="btnreject(\'val\');" class="btn-danger btn btn-val">Reject</button>');
		
		} else if (sesi_level=='DEPT. HEAD' && user_div=='FINANCE and ACCOUNTING' && evo_dept=='FINANCE') {
			$("#btn-val").html('<button type="button" onclick="btnval(\'val\');" class="btn-primary btn btn-val">Single Approve</button>');
			$("#btn-approve").html('<button type="button" onclick="btnapprove(\'val\');" class="btn-info btn btn-val">Batch Approve</button>');
			$("#btn-reject").html('<button type="button" onclick="btnreject(\'val\');" class="btn-danger btn btn-val">Reject</button>');
			
		} else {
			$("#btn-val").html('<button type="button" onclick="btnval(\'val\');" class="btn-primary btn btn-val">Validasi</button>');
			$("#btn-approve").html('');
			$("#btn-reject").html('');
		}
		
	} else {
		
		if (sesi_level=='DEPT. HEAD' && srcLevel=='DEPT. HEAD FINANCE' && user_div == 'FINANCE and ACCOUNTING' && evo_dept=='FINANCE') {
			$("#btn-val").html('<button type="button" onclick="btnval(\'val\');" class="btn-primary btn btn-val">Single Approve</button>');
			$("#btn-approve").html('<button type="button" onclick="btnapprove(\'val\');" class="btn-info btn btn-val">Batch Approve</button>');
			$("#btn-reject").html('<button type="button" onclick="btnreject(\'val\');" class="btn-danger btn btn-val">Reject</button>');
			
		} else if (sesi_level=='DIV. HEAD' && srcLevel=='DEPT. HEAD FINANCE / DIV. HEAD FAST' && user_div == 'FINANCE and ACCOUNTING' && user_dept=='all') {
			$("#btn-val").html('<button type="button" onclick="btnval(\'val\');" class="btn-primary btn btn-val">Single Approve</button>');
			$("#btn-approve").html('<button type="button" onclick="btnapprove(\'val\');" class="btn-info btn btn-val">Batch Approve</button>');
			$("#btn-reject").html('<button type="button" onclick="btnreject(\'val\');" class="btn-danger btn btn-val">Reject</button>');
		
		} else {
			$("#btn-val").html('<button type="button" onclick="btnval(\'view\');" class="btn-primary btn btn-val">View Progress</button>');
		}
		
		
		if (sesi_level=='DIREKSI 2' && srcLevel=='DIREKSI 2') {
			$("#btn-val").html('<button type="button" onclick="btnval(\'val\');" class="btn-primary btn btn-val">Single Approve</button>');
			$("#btn-approve").html('<button type="button" onclick="btnapprove(\'val\');" class="btn-info btn btn-val">Batch Approve</button>');
			$("#btn-reject").html('<button type="button" onclick="btnreject(\'val\');" class="btn-danger btn btn-val">Reject</button>');
		}
		
		if (sesi_level=='DIREKSI'  && srcLevel=='DIREKSI') {
			$("#btn-val").html('<button type="button" onclick="btnval(\'val\');" class="btn-primary btn btn-val">Single Approve</button>');
			$("#btn-approve").html('<button type="button" onclick="btnapprove(\'val\');" class="btn-info btn btn-val">Batch Approve</button>');
			$("#btn-reject").html('<button type="button" onclick="btnreject(\'val\');" class="btn-danger btn btn-val">Reject</button>');
		}
		
		if (sesi_level=='DEPT. HEAD' && srcLevel=='DEPT. HEAD' && user_div=='FINANCE and ACCOUNTING' && evo_dept=='FINANCE') { 
			$("#btn-val").html('<button type="button" onclick="btnval(\'val\');" class="btn-primary btn btn-val">Single Approve</button>');
			$("#btn-approve").html('<button type="button" onclick="btnapprove(\'val\');" class="btn-info btn btn-val">Batch Approve</button>');
			$("#btn-reject").html('<button type="button" onclick="btnreject(\'val\');" class="btn-danger btn btn-val">Reject</button>');
		}
		
		if (srcLevel=='REQDEPTTERKAIT' && sesi_dept!='') {
			$("#btn-val").html('<button type="button" onclick="btnval(\'val\');" class="btn-primary btn btn-val">Validasi</button>');
		} 
	}
	
	if (sesi_kodedealer!="2010") {
		if (srcLevel == "ACCOUNTING") {
			$(".tiles-footer").html('&nbsp;');
			$("#capt_wait_1").html('SELECTED');
			$("#list_title").html('Request Waiting on Checker');
		} else if (srcLevel == "SECTION HEAD") {
			$(".tiles-footer").html('&nbsp;');
			$("#capt_wait_2").html('SELECTED');
			$("#list_title").html('Request Waiting on Approval Section Head');
		} else if (srcLevel == "ADH") {
			$(".tiles-footer").html('&nbsp;');
			$("#capt_wait_3").html('SELECTED');
			$("#list_title").html('Request Waiting on Approval ADH');
		} else if (srcLevel == "KEPALA CABANG") {
			$(".tiles-footer").html('&nbsp;');
			$("#capt_wait_4").html('SELECTED');
			$("#list_title").html('Request Waiting on Approval Kacab');
		} else if (srcLevel == "KASIR") {
			$(".tiles-footer").html('&nbsp;');
			$("#capt_wait_5").html('SELECTED');
			$("#list_title").html('Ready For Settlement');
		} 
		
	} else {
		// HO
		if (srcLevel == "TAX" || srcLevel == "ADMIN") {
			$(".tiles-footer").html('&nbsp;');
			$("#capt_wait_tax").html('SELECTED');
			$("#list_title").html('Waiting Checker Tax');
		} else if (srcLevel == "ACCOUNTING") {
			$(".tiles-footer").html('&nbsp;');
			$("#capt_wait_accounting").html('SELECTED');
			$("#list_title").html('Waiting Checker Accounting');
		} else if (srcLevel == "SECTION HEAD") {
			$(".tiles-footer").html('&nbsp;');
			$("#capt_wait_section").html('SELECTED');
			$("#list_title").html('Waiting Approval Section Head');
		} else if (srcLevel == "DEPT. HEAD") {
			$(".tiles-footer").html('&nbsp;');
			$("#capt_wait_dept_head").html('SELECTED');
			$("#list_title").html('Waiting Approval Dept. Head');
		} else if (srcLevel == "DIV. HEAD") {
			$(".tiles-footer").html('&nbsp;');
			$("#capt_wait_div_head").html('SELECTED');
			$("#list_title").html('Waiting Approval Div. Head');
		} else if (srcLevel == "DIREKSI") {
			$(".tiles-footer").html('&nbsp;');
			$("#capt_wait_direksi").html('SELECTED');
			$("#list_title").html('Waiting Approval Direksi 1');
		}else if (srcLevel == "DIREKSI 2") {
			$(".tiles-footer").html('&nbsp;');
			$("#capt_wait_direksi2").html('SELECTED');
			$("#list_title").html('Waiting Approval Direksi 2');
		} else if (srcLevel == "FINANCE") {
			$(".tiles-footer").html('&nbsp;');
			$("#capt_wait_fin").html('SELECTED');
			$("#list_title").html('Waiting Checker Finance');
			
		} else if (srcLevel == "DEPT. HEAD FINANCE") {
			$(".tiles-footer").html('&nbsp;');
			$("#capt_wait_releaser").html('SELECTED');
			$("#list_title").html('Waiting Approval Releaser 1');
		} else if (srcLevel == "DEPT. HEAD FINANCE / DIV. HEAD FAST") {
			$(".tiles-footer").html('&nbsp;');
			$("#capt_wait_div_fast").html('SELECTED');
			$("#list_title").html('Waiting Approval Releaser 2');
		}
		
		onload = provalnew();
	}
	
	//onload = proval();
	
	var IdUser = $("#IdUser").val();
	$(".flexme3").flexigrid({
		dataType : 'xml',
	    colModel : [ 
	        {
		        display : '#',
		        name : 'evo_id',
		        width : 40,
		        sortable : false,
		        align : 'center'
	        },{
		        display : 'No Bukti',
		        name : 'nobukti',
		        width : 120,
		        sortable : false,
		        align : 'left'
	        }, {
	            display : 'Tgl Aju',
	            name : 'tgl_pengajuan',
	            width : 80,
	            sortable : true,
	            align : 'left'
	        }, {
	            display : 'Nama Aju',
	            name : 'nama_aju',
	            width : 100,
	            sortable : false,
	            align : 'left'
	        }, {
	            display : 'Department',
	            name : 'NamaDealer',
	            width : 300,
	            sortable : false,
	            align : 'left'
	        }, {
	            display : 'Vendor',
	            name : 'namaVendor',
	            width : 300,
	            sortable : false,
	            align : 'left'
	        }, {
	            display : 'Amount',
	            name : 'amount',
	            width : 120,
	            sortable : false,
	            align : 'left'
	        }, {
	            display : 'Keperluan',
	            name : 'keterangan',
	            width : 350,
	            sortable : false,
	            align : 'left'
	        }
	    ],
	    sortname : "tgl_pengajuan",
	    sortorder : "asc",
	    usepager : true,
	    useRp : true,
	    rp : 10,
	    rpOptions: [10, 20, 50, 100],
	    showToggleBtn : false,
	    width : 'auto',
	    height : '200',
		onSuccess: function(){
		    onload = hideLoading();
		}
	});
	var level = $("#srcLevel").val();
	var sesi_level  = $("#sesi_level").val();
	$('.flexme3').flexOptions({
		url: "system/data/home.php",
		newp: 1,
		params:[{name:'level', value: level},{name:'IdUser',value: IdUser },{name:'sesi_level',value: sesi_level }]
	}).flexReload();
	
	$('#search').click(function(event){
		var IdUser = $("#IdUser").val();
		var searchText = $("#searchText").val();
		var level = $("#srcLevel").val();
		var sesi_level  = $("#sesi_level").val();
		var dt = [{name:'searchText',value: searchText },{name:'level',value: level },{name:'IdUser',value: IdUser },{name:'sesi_level',value: sesi_level }];
		$(".flexme3").flexOptions({url: "system/data/home.php", params: dt }).flexReload();
	});
});

function proval(){
	var IdUser = $("#IdUser").val();
	$.ajax({ 
		url: 'system/data/home.php',
		data: { action:'proval', 'IdUser' : IdUser },
		type: 'post',
		beforeSend: function(){
			$("#tot_wait_1").html('<i class="fa fa-spin fa-spinner"></i>');
			$("#tot_wait_2").html('<i class="fa fa-spin fa-spinner"></i>');
			$("#tot_wait_3").html('<i class="fa fa-spin fa-spinner"></i>');
			$("#tot_wait_4").html('<i class="fa fa-spin fa-spinner"></i>');
			$("#tot_wait_5").html('<i class="fa fa-spin fa-spinner"></i>');
			$("#tot_wait_tax").html('<i class="fa fa-spin fa-spinner"></i>');
			$("#tot_wait_section").html('<i class="fa fa-spin fa-spinner"></i>');
			$("#tot_wait_dept_head").html('<i class="fa fa-spin fa-spinner"></i>');
			$("#tot_wait_direksi").html('<i class="fa fa-spin fa-spinner"></i>');
			$("#tot_wait_fin").html('<i class="fa fa-spin fa-spinner"></i>');
			
			$("#tot_wait_releaser").html('<i class="fa fa-spin fa-spinner"></i>');
			$("#tot_wait_div_fast").html('<i class="fa fa-spin fa-spinner"></i>');
			
			$("#tot_wait_div_head").html('<i class="fa fa-spin fa-spinner"></i>');
			$("#tot_wait_dept_lain").html('<i class="fa fa-spin fa-spinner"></i>');
			$("#tot_req_dept_lain").html('<i class="fa fa-spin fa-spinner"></i>');
		},
		success: function(output) {
			var data = output.split("#");
			$("#tot_wait_1").html(data[0]);
			$("#tot_wait_2").html(data[1]);
			$("#tot_wait_3").html(data[2]);
			$("#tot_wait_4").html(data[3]);
			$("#tot_wait_5").html(data[4]);
			$("#tot_wait_tax").html(data[5]);
			$("#tot_wait_section").html(data[6]);
			$("#tot_wait_dept_head").html(data[7]);
			$("#tot_wait_direksi").html(data[8]);
			$("#tot_wait_fin").html(data[9]);
			$("#tot_wait_releaser").html(data[10]);
			$("#tot_wait_div_fast").html(data[11]);
			
			$("#tot_wait_div_head").html(data[12]);
			$("#tot_wait_dept_lain").html(data[13]);
			$("#tot_req_dept_lain").html(data[14]);
		}
	});
}

function btnval(id){
	if (id=='val') {
		var page = "transaksi-validasi-modify";
		$('#Form').attr('action', page);
		var count = $("input[name='id[]']:checked").length;
		if (count==0 || count>1) {
		    onload = myBad('Approval Progress Monitoring');
		    return false;
		} else {
		    $("#post").html('<input type="hidden" name="modify" value="">');
		    document.forms['Form'].submit();
		}
	} else if (id=='view') {
		var page = "report-reportpengajuan-modify";
		$('#Form').attr('action', page);
		var count = $("input[name='id[]']:checked").length;
		if (count==0 || count>1) {
		    onload = myBad('View Progress');
		    return false;
		} else {
		    $("#post").html('<input type="hidden" name="modify" value="pengajuan-detail">');
		    document.forms['Form'].submit();
		}
	}		
}

function btnapprove(id){
	if (id=='val') {
		//var page = "transaksi-validasi-modify";
		//$('#Form').attr('action', page);
		var count = $("input[name='id[]']:checked").length;
		if (count==0) {
		    onload = myBad('Approval Progress Monitoring');
		    return false;
		} else {
		   // $("#post").html('<input type="hidden" name="modify" value="">');
		    //document.forms['Form'].submit();
			var evoid = [];
			 $("input[name='id[]']:checked").each(function(){
                	evoid.push($(this).val());
            });
			   var IdUser = $("#IdUser").val();
			for (i=0;i<evoid.length;i++) {
				$.ajax({ 
					url: 'system/control/validasi.php',
					data: { 
						action:'validasimulti', 'evoid': evoid[i], 'val': 'Accept', 'IdUser' : IdUser
					},
					type: 'post',
					beforeSend: function(){
						onload = showLoading();
					},
					success: function(output) {
						var msg = output.split("#");
						var ajax1 = $.ajax({ 
							url: 'email.php',
							//data: {'id': msg[2]},
							data: {'id': msg[1]},
							success: function(result) {}
						});
						//var nik = msg[4].split(";");
						//var pesan = msg[3].split(";");
						var nik = msg[3].split(";");
						var pesan = msg[3].split(";");
						
						for (var i = 0; i < nik.length; i++) {
							if (nik[i]!='') {
								var url = "https://nasmoco.net/notifevopay/pushnotif_new.php?nik="+nik[i]+"&pesan="+pesan[i]+"&sumber=EVOPAY&nobukti="+msg[1]+"&sender=evopay&judul=Validasi Voucher Payment";
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
							title: "Validasi Evopay",
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
										document.location.href="home";
									}
								},
								cancel: {
									label: 'No',
									className: 'btn-sm btn-danger',
									callback: function () {
										document.location.href="home";
									}
								}
							}
						});
					}
				});
			}
			
		}
	} 	
}

function btnreject(id){
	if (id=='val') {
		//var page = "transaksi-validasi-modify";
		//$('#Form').attr('action', page);
		var count = $("input[name='id[]']:checked").length;
		if (count==0) {
		    onload = myBad('Approval Progress Monitoring');
		    return false;
		} else {
		    //$("#post").html('<input type="hidden" name="modify" value="">');
		    //document.forms['Form'].submit();
			var evoid = [];
			 $("input[name='id[]']:checked").each(function(){
                	evoid.push($(this).val());
            });
			   var IdUser = $("#IdUser").val();
			for (i=0;i<evoid.length;i++) {
				$.ajax({ 
					url: 'system/control/validasi.php',
					data: { 
						action:'validasimulti', 'evoid': evoid[i], 'val': 'Reject', 'IdUser' : IdUser
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
										document.location.href="home";
									}
								}
							}
						});
					}
				});
			}
		}
	}
}


function provalnew(){
	var IdUser = $("#IdUser").val();
	$.ajax({ 
		url: 'system/data/home.php',
		data: { action:'proval', 'IdUser' : IdUser, level:'TAX'},
		type: 'post',
		beforeSend: function(){						
			$("#tot_wait_tax").html('<i class="fa fa-spin fa-spinner"></i>');
			$("#tot_wait_accounting").html('<i class="fa fa-spin fa-spinner"></i>');
			$("#tot_wait_section").html('<i class="fa fa-spin fa-spinner"></i>');
			$("#tot_wait_dept_head").html('<i class="fa fa-spin fa-spinner"></i>');
			$("#tot_wait_direksi").html('<i class="fa fa-spin fa-spinner"></i>');
			$("#tot_wait_direksi2").html('<i class="fa fa-spin fa-spinner"></i>');
			$("#tot_wait_fin").html('<i class="fa fa-spin fa-spinner"></i>');
			
			$("#tot_wait_releaser").html('<i class="fa fa-spin fa-spinner"></i>');
			$("#tot_wait_div_fast").html('<i class="fa fa-spin fa-spinner"></i>');
			
			$("#tot_wait_div_head").html('<i class="fa fa-spin fa-spinner"></i>');
			$("#tot_wait_dept_lain").html('<i class="fa fa-spin fa-spinner"></i>');
			$("#tot_req_dept_lain").html('<i class="fa fa-spin fa-spinner"></i>');
		},
		success: function(output) {
			$("#tot_wait_tax").html(output);			
			
			//proval_releaser();
			$.ajax({ 
				url: 'system/data/home.php',
				data: { action:'proval', 'IdUser' : IdUser, level:'ACCOUNTING' },
				type: 'post',
				beforeSend: function(){
					$("#tot_wait_accounting").html('<i class="fa fa-spin fa-spinner"></i>');
				},
				success: function(output) {
					$("#tot_wait_accounting").html(output);
					
					$.ajax({ 
						url: 'system/data/home.php',
						data: { action:'proval', 'IdUser' : IdUser, level:'SECTION HEAD' },
						type: 'post',
						beforeSend: function(){
							//$("#tot_wait_section").html('<i class="fa fa-spin fa-spinner"></i>');
						},
						success: function(output) {
							$("#tot_wait_section").html(output);			
							//proval_dept();
							
							$.ajax({ 
								url: 'system/data/home.php',
								data: { action:'proval', 'IdUser' : IdUser, level:'DEPT. HEAD' },
								type: 'post',
								beforeSend: function(){
									$("#tot_wait_dept_head").html('<i class="fa fa-spin fa-spinner"></i>');
								},
								success: function(output) {
									$("#tot_wait_dept_head").html(output);							
									//proval_div();
									
									var IdUser = $("#IdUser").val();
									$.ajax({ 
										url: 'system/data/home.php',
										data: { action:'proval', 'IdUser' : IdUser, level:'DIV. HEAD' },
										type: 'post',
										beforeSend: function(){
											$("#tot_wait_div_head").html('<i class="fa fa-spin fa-spinner"></i>');
										},
										success: function(output) {
											$("#tot_wait_div_head").html(output);			
											//proval_direksi();
											
											$.ajax({ 
												url: 'system/data/home.php',
												data: { action:'proval', 'IdUser' : IdUser, level:'DIREKSI' },
												type: 'post',
												beforeSend: function(){
													$("#tot_wait_direksi").html('<i class="fa fa-spin fa-spinner"></i>');
												},
												success: function(output) {
													$("#tot_wait_direksi").html(output);
													//proval_fin();
													
													$.ajax({ 
														url: 'system/data/home.php',
														data: { action:'proval', 'IdUser' : IdUser, level:'DIREKSI 2' },
														type: 'post',
														beforeSend: function(){
															$("#tot_wait_direksi2").html('<i class="fa fa-spin fa-spinner"></i>');
														},
														success: function(output) {
															$("#tot_wait_direksi2").html(output);
															
															$.ajax({ 
																url: 'system/data/home.php',
																data: { action:'proval', 'IdUser' : IdUser, level:'FINANCE' },
																type: 'post',
																beforeSend: function(){
																	$("#tot_wait_fin").html('<i class="fa fa-spin fa-spinner"></i>');
																},
																success: function(output) {
																	$("#tot_wait_fin").html(output);
																	
																	
																	$.ajax({ 
																		url: 'system/data/home.php',
																		data: { action:'proval', 'IdUser' : IdUser, level:'DEPT. HEAD FINANCE' },
																		type: 'post',
																		beforeSend: function(){
																			$("#tot_wait_releaser").html('<i class="fa fa-spin fa-spinner"></i>');
																		},
																		success: function(output) {
																			$("#tot_wait_releaser").html(output);
																			
																			//proval_releaser2();
																			$.ajax({ 
																				url: 'system/data/home.php',
																				data: { action:'proval', 'IdUser' : IdUser, level:'DEPT. HEAD FINANCE DIV. HEAD FAST' },
																				type: 'post',
																				beforeSend: function(){
																					$("#tot_wait_div_fast").html('<i class="fa fa-spin fa-spinner"></i>');
																				},
																				success: function(output) {
																					$("#tot_wait_div_fast").html(output);
																					
																					proval_req();
																					proval_wait();
																				}
																			});
																			
																			
																		}
																	});
																	
																	
																	
																	
																	
																}
															});
														}
													});
																							
												}
											});
																			
											
										}
									});
									
									
								}
							});
											
						}
					});
				}
			});
					
		}
	});
}

function proval_tax(){
	var IdUser = $("#IdUser").val();
	$.ajax({ 
		url: 'system/data/home.php',
		data: { action:'proval', 'IdUser' : IdUser, level:'TAX'},
		type: 'post',
		beforeSend: function(){						
			$("#tot_wait_tax").html('<i class="fa fa-spin fa-spinner"></i>');
		},
		success: function(output) {
			$("#tot_wait_tax").html(output);			
			//proval_section();
		}
	});
}

function proval_accounting(){
	var IdUser = $("#IdUser").val();
	$.ajax({ 
		url: 'system/data/home.php',
		data: { action:'proval', 'IdUser' : IdUser, level:'ACCOUNTING'},
		type: 'post',
		beforeSend: function(){						
			$("#tot_wait_accounting").html('<i class="fa fa-spin fa-spinner"></i>');
		},
		success: function(output) {
			$("#tot_wait_accounting").html(output);			
			//proval_section();
		}
	});
}

function proval_section(){
	var IdUser = $("#IdUser").val();
	$.ajax({ 
		url: 'system/data/home.php',
		data: { action:'proval', 'IdUser' : IdUser, level:'SECTION HEAD' },
		type: 'post',
		beforeSend: function(){
			$("#tot_wait_section").html('<i class="fa fa-spin fa-spinner"></i>');
		},
		success: function(output) {
			$("#tot_wait_section").html(output);			
			//proval_dept();
		}
	});
}

function proval_dept(){
	var IdUser = $("#IdUser").val();
	$.ajax({ 
		url: 'system/data/home.php',
		data: { action:'proval', 'IdUser' : IdUser, level:'DEPT. HEAD' },
		type: 'post',
		beforeSend: function(){
			$("#tot_wait_dept_head").html('<i class="fa fa-spin fa-spinner"></i>');
		},
		success: function(output) {
			$("#tot_wait_dept_head").html(output);
			
			//proval_div();
		}
	});
}

function proval_div(){
	var IdUser = $("#IdUser").val();
	$.ajax({ 
		url: 'system/data/home.php',
		data: { action:'proval', 'IdUser' : IdUser, level:'DIV. HEAD' },
		type: 'post',
		beforeSend: function(){
			$("#tot_wait_div_head").html('<i class="fa fa-spin fa-spinner"></i>');
		},
		success: function(output) {
			$("#tot_wait_div_head").html(output);			
			//proval_direksi();
		}
	});
}

function proval_direksi(){
	var IdUser = $("#IdUser").val();
	$.ajax({ 
		url: 'system/data/home.php',
		data: { action:'proval', 'IdUser' : IdUser, level:'DIREKSI' },
		type: 'post',
		beforeSend: function(){
			$("#tot_wait_direksi").html('<i class="fa fa-spin fa-spinner"></i>');
		},
		success: function(output) {
			$("#tot_wait_direksi").html(output);
			
			//proval_fin();
		}
	});
}

function proval_direksi2(){
	var IdUser = $("#IdUser").val();
	$.ajax({ 
		url: 'system/data/home.php',
		data: { action:'proval', 'IdUser' : IdUser, level:'DIREKSI 2' },
		type: 'post',
		beforeSend: function(){
			$("#tot_wait_direksi2").html('<i class="fa fa-spin fa-spinner"></i>');
		},
		success: function(output) {
			$("#tot_wait_direksi2").html(output);
			
			//proval_fin();
		}
	});
}

function proval_fin(){
	var IdUser = $("#IdUser").val();
	$.ajax({ 
		url: 'system/data/home.php',
		data: { action:'proval', 'IdUser' : IdUser, level:'FINANCE' },
		type: 'post',
		beforeSend: function(){
			$("#tot_wait_fin").html('<i class="fa fa-spin fa-spinner"></i>');
		},
		success: function(output) {
			$("#tot_wait_fin").html(output);
			
			//proval_releaser();
		}
	});
}

function proval_releaser1(){
	var IdUser = $("#IdUser").val();
	$.ajax({ 
		url: 'system/data/home.php',
		data: { action:'proval', 'IdUser' : IdUser, level:'DEPT. HEAD FINANCE' },
		type: 'post',
		beforeSend: function(){
			$("#tot_wait_releaser").html('<i class="fa fa-spin fa-spinner"></i>');
		},
		success: function(output) {
			$("#tot_wait_releaser").html(output);
			
			//proval_req();
			//proval_wait();
		}
	});
}


function proval_releaser2(){
	var IdUser = $("#IdUser").val();
	$.ajax({ 
		url: 'system/data/home.php',
		data: { action:'proval', 'IdUser' : IdUser, level:'DEPT. HEAD FINANCE DIV. HEAD FAST' },
		type: 'post',
		beforeSend: function(){
			$("#tot_wait_div_fast").html('<i class="fa fa-spin fa-spinner"></i>');
		},
		success: function(output) {
			$("#tot_wait_div_fast").html(output);
			
			//proval_req();
			//proval_wait();
		}
	});
}

function proval_req(){
	var IdUser = $("#IdUser").val();
	$.ajax({ 
		url: 'system/data/home.php',
		data: { action:'proval', 'IdUser' : IdUser, level:'REQDEPTTERKAIT' },
		type: 'post',
		beforeSend: function(){
			$("#tot_req_dept_lain").html('<i class="fa fa-spin fa-spinner"></i>');
		},
		success: function(output) {
			$("#tot_req_dept_lain").html(output);
		}
	});
}

function proval_wait(){
	var IdUser = $("#IdUser").val();
	$.ajax({ 
		url: 'system/data/home.php',
		data: { action:'proval', 'IdUser' : IdUser, level:'WAITDEPTTERKAIT' },
		type: 'post',
		beforeSend: function(){
			$("#tot_wait_dept_lain").html('<i class="fa fa-spin fa-spinner"></i>');
		},
		success: function(output) {
			$("#tot_wait_dept_lain").html(output);
		}
	});
}

function proval_acc(){
	var IdUser = $("#IdUser").val();
	$.ajax({ 
		url: 'system/data/home.php',
		data: { action:'proval', 'IdUser' : IdUser, level:'ACCOUNTING' },
		type: 'post',
		beforeSend: function(){
			$("#tot_wait_1").html('<i class="fa fa-spin fa-spinner"></i>');
		},
		success: function(output) {
			$("#tot_wait_1").html(output);
		}
	});
}

function proval_sec(){
	var IdUser = $("#IdUser").val();
	$.ajax({ 
		url: 'system/data/home.php',
		data: { action:'proval', 'IdUser' : IdUser, level:'SECTION HEAD' },
		type: 'post',
		beforeSend: function(){
			$("#tot_wait_2").html('<i class="fa fa-spin fa-spinner"></i>');
		},
		success: function(output) {
			$("#tot_wait_2").html(output);
		}
	});
}

function proval_adh(){
	var IdUser = $("#IdUser").val();
	$.ajax({ 
		url: 'system/data/home.php',
		data: { action:'proval', 'IdUser' : IdUser, level:'ADH' },
		type: 'post',
		beforeSend: function(){
			$("#tot_wait_3").html('<i class="fa fa-spin fa-spinner"></i>');
		},
		success: function(output) {
			$("#tot_wait_3").html(output);
		}
	});
}

function proval_kacab(){
	var IdUser = $("#IdUser").val();
	$.ajax({ 
		url: 'system/data/home.php',
		data: { action:'proval', 'IdUser' : IdUser, level:'KEPALA CABANG' },
		type: 'post',
		beforeSend: function(){
			$("#tot_wait_4").html('<i class="fa fa-spin fa-spinner"></i>');
		},
		success: function(output) {
			$("#tot_wait_41").html(output);
		}
	});
}

function proval_kasir(){
	var IdUser = $("#IdUser").val();
	$.ajax({ 
		url: 'system/data/home.php',
		data: { action:'proval', 'IdUser' : IdUser, level:'KASIR' },
		type: 'post',
		beforeSend: function(){
			$("#tot_wait_5").html('<i class="fa fa-spin fa-spinner"></i>');
		},
		success: function(output) {
			$("#tot_wait_51").html(output);
		}
	});
}

function toggle(source) {
	var checkboxes = document.querySelectorAll('#chk-1');
	//alert(checkboxes.length);	
	for (var i = 0; i < checkboxes.length; i++) {
		var cekcek = checkboxes;
			cekcek[i].checked = source.checked;
	}
}
/*
function proval4(){
	var IdUser = $("#IdUser").val();
	$.ajax({ 
		url: 'system/data/home.php',
		data: { action:'proval', 'IdUser' : IdUser },
		type: 'post',
		beforeSend: function(){
			$("#tot_wait_1").html('<i class="fa fa-spin fa-spinner"></i>');
			$("#tot_wait_2").html('<i class="fa fa-spin fa-spinner"></i>');
			$("#tot_wait_3").html('<i class="fa fa-spin fa-spinner"></i>');
			$("#tot_wait_4").html('<i class="fa fa-spin fa-spinner"></i>');
			$("#tot_wait_5").html('<i class="fa fa-spin fa-spinner"></i>');
			$("#tot_wait_tax").html('<i class="fa fa-spin fa-spinner"></i>');
			$("#tot_wait_section").html('<i class="fa fa-spin fa-spinner"></i>');
			$("#tot_wait_dept_head").html('<i class="fa fa-spin fa-spinner"></i>');
			$("#tot_wait_direksi").html('<i class="fa fa-spin fa-spinner"></i>');
			$("#tot_wait_fin").html('<i class="fa fa-spin fa-spinner"></i>');
			$("#tot_wait_div_fast").html('<i class="fa fa-spin fa-spinner"></i>');
			$("#tot_wait_div_head").html('<i class="fa fa-spin fa-spinner"></i>');
			$("#tot_wait_dept_lain").html('<i class="fa fa-spin fa-spinner"></i>');
			$("#tot_req_dept_lain").html('<i class="fa fa-spin fa-spinner"></i>');
		},
		success: function(output) {
			var data = output.split("#");
			$("#tot_wait_1").html(data[0]);
			$("#tot_wait_2").html(data[1]);
			$("#tot_wait_3").html(data[2]);
			$("#tot_wait_4").html(data[3]);
			$("#tot_wait_5").html(data[4]);
			$("#tot_wait_tax").html(data[5]);
			$("#tot_wait_section").html(data[6]);
			$("#tot_wait_dept_head").html(data[7]);
			$("#tot_wait_direksi").html(data[8]);
			$("#tot_wait_fin").html(data[9]);
			$("#tot_wait_div_fast").html(data[10]);
			$("#tot_wait_div_head").html(data[11]);
			$("#tot_wait_dept_lain").html(data[12]);
			$("#tot_req_dept_lain").html(data[13]);
		}
	});
}*/
