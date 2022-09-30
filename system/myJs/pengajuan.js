jQuery(document).ready(function($) {
	var IdUser = $("#IdUser").val();
	var kodedealer = $("#kodedealer").val();
	$(".dataPengajuan").flexigrid({
	    dataType : 'xml',
	    colModel : [ 
	        {
		        display : '#',
		        name : 'evo_id',
		        width : 30,
		        sortable : true,
		        align : 'center'
	        }, {
	            display : 'Dealer / NRM',
	            name : 'dealer',
	            width : 300,
	            sortable : true,
	            align : 'left'
	        }, {
	            display : 'Tipe',
	            name : 'tipe',
	            width : 80,
	            sortable : true,
	            align : 'left'
	        }, {
	            display : 'Status',
	            name : 'status',
	            width : 50,
	            sortable : true,
	            align : 'left'
	        }, {
	            display : 'No. Evo Pay',
	            name : 'nobukti',
	            width : 120,
	            sortable : true,
	            align : 'left'
	        }, {
	            display : 'Tgl Aju',
	            name : 'tgl_pengajuan',
	            width : 80,
	            sortable : true,
	            align : 'left'
	        }, {
	            display : 'Upload File',
	            name : 'upload_file',
	            width : 100,
	            sortable : true,
	            align : 'left'
	        }, {
	            display : 'Upload FP',
	            name : 'upload_fp',
	            width : 100,
	            sortable : true,
	            align : 'left'
	        }, {
	            display : 'Vendor',
	            name : 'kode_vendor',
	            width : 100,
	            sortable : true,
	            align : 'left'
	        }, {
	            display : 'Metode Byr',
	            name : 'metode_bayar',
	            width : 80,
	            sortable : true,
	            align : 'left'
	        }, {
	            display : 'Beneficary Account',
	            name : 'benificary_account',
	            width : 120,
	            sortable : true,
	            align : 'left'
	        }, {
	            display : 'Tgl Bayar',
	            name : 'tgl_bayar',
	            width : 80,
	            sortable : true,
	            align : 'left'
	        }, {
	            display : 'Bank Penerima',
	            name : 'nama_bank',
	            width : 120,
	            sortable : true,
	            align : 'left'
	        }, {
	            display : 'Nama Pemilik',
	            name : 'nama_pemilik',
	            width : 120,
	            sortable : true,
	            align : 'left'
	        }, {
	            display : 'Email Penerima',
	            name : 'email_penerima',
	            width : 100,
	            sortable : true,
	            align : 'left'
	        }, {
	            display : 'Nama Alias',
	            name : 'nama_alias',
	            width : 150,
	            sortable : true,
	            align : 'left'
	        }, {
	            display : 'Bank Pengirim',
	            name : 'nama_bank_pengirim',
	            width : 120,
	            sortable : true,
	            align : 'left'
	        }, {
	            display : 'TF From Account',
	            name : 'tf_from_account',
	            width : 120,
	            sortable : true,
	            align : 'left'
	        }, {
	            display : 'Nominal',
	            name : 'realisasi_nominal',
	            width : 100,
	            sortable : true,
	            align : 'left'
	        }, {
	            display : 'Dpp',
	            name : 'dpp',
	            width : 100,
	            sortable : true,
	            align : 'left'
	        }, {
	            display : 'Ppn',
	            name : 'ppn',
	            width : 100,
	            sortable : true,
	            align : 'left'
	        }, {
	            display : 'Total Pph',
	            name : 'totpph',
	            width : 100,
	            sortable : true,
	            align : 'left'
	        }, {
	            display : 'Total Bayar',
	            name : 'total',
	            width : 100,
	            sortable : true,
	            align : 'left'
	        }, {
	            display : 'NPWP',
	            name : 'npwp',
	            width : 150,
	            sortable : true,
	            align : 'left'
	        }, {
	            display : 'No FP',
	            name : 'no_fj',
	            width : 150,
	            sortable : true,
	            align : 'left'
	        }, {
	            display : 'Keterangan',
	            name : 'keterangan',
	            width : 100,
	            sortable : true,
	            align : 'left'
	        }
	    ],
	    buttons : [ 
	        { name : 'New', bclass : 'add', onpress : button },
	        { name : 'Edit', bclass : 'edit', onpress : button },
	        { name : 'Search', bclass : 'search', onpress : button }
		],
	    title : 'Pengajuan Evo Pay',
	    sortname : "tgl_pengajuan",
	    sortorder : "desc",
	    usepager : true,
	    useRp : true,
	    rp : 10,
	    rpOptions: [10, 20, 50, 100],
	    showToggleBtn : false,
	    width : 'auto',
	    height : '250',
		onSuccess: function(){
		    onload = hideLoading();
		}
	});
	$('.dataPengajuan').flexOptions({
		url : 'system/data/pengajuan.php',
		newp: 1,
		params:[ { name:'IdUser', value: IdUser } ]
	}).flexReload();

	function button(com) {
	    if (com == 'New') {
	    	var KodeDealer = $("#KodeDealer").val();
	        $.ajax({ 
			    url: 'system/view/check.php',
			    data: {'KodeDealer': KodeDealer},
			    type: 'post',
			    beforeSend: function(){
			    	onload = showLoading();
			    },
			    success: function(output) {
			    	onload = hideLoading();
			    	//onload = needValue(output);
					if (output==0) {
			    		onload = needValue('Pengajuan Evo Pay','Database Bulan Belum Ada, silahkan hubungi Admin Accounting');
			    	} else {
			    		$("#post").html('<input type="hidden" name="modify" value="new">');
						document.forms['Form'].submit();
			    	}
				}
			});
	    } else if (com == 'Edit') {
	        var generallen = $("input[name='id[]']:checked").length;
			if (generallen==0 || generallen>1) {
			    onload = myBad('Pengajuan Credit Note');
			    return false;
			} else {
			    var KodeDealer = $("#KodeDealer").val();
		        $.ajax({ 
				    url: 'system/view/check.php',
				    data: {'KodeDealer': KodeDealer},
				    type: 'post',
				    beforeSend: function(){
				    	onload = showLoading();
				    },
				    success: function(output) {
				    	onload = hideLoading();
				    	if (output==0) {
				    		onload = needValue('Pengajuan Evo Pay','Database Bulan Belum Ada, silahkan hubungi Admin Accounting');
				    	} else {
				    		$("#post").html('<input type="hidden" name="modify" value="edit">');
			    			document.forms['Form'].submit();
				    	}
					}
				});
			}
	    } else if (com == 'Search') {
	        bootbox.dialog({
			    message: '<form action="" method="post" class="form-horizontal"> <div class="form-group" style="margin-bottom: 2px;"> <label class="col-md-4 control-label">No. Evo Pay</label> <div class="col-md-8"> <input class="form-control" id="nobukti"> </div></div><div class="form-group" style="margin-bottom: 2px;"> <label class="col-md-4 control-label">Vendor</label> <div class="col-md-8"> <input class="form-control" id="vendor"> </div></div><div class="form-group" style="margin-bottom: 2px;"> <label class="col-md-4 control-label">No. FP</label> <div class="col-md-8"> <input class="form-control" id="no_fj"> </div></div><div class="form-group" style="margin-bottom: 2px;"> <label class="col-md-4 control-label">Tgl Pengajuan</label> <div class="col-md-4"> <input type="date" id="startDate" class="form-control"> </div><div class="col-md-4"> <input type="date" id="endDate" class="form-control"> </div></div></form>',
			    title: "Search",
			    buttons: {
			        main: {
			            label: "Search",
			            className: "btn-sm btn-primary",
			            callback: function() {
			                onload = showLoading();
							var nobukti    = $("#nobukti").val();
							var vendor     = $("#vendor").val();
							var no_fj      = $("#no_fj").val();
							var startDate  = $("#startDate").val();
							var endDate    = $("#endDate").val();
							var dt = [{name:'nobukti',value: nobukti },{name:'vendor',value: vendor },{name:'no_fj',value: no_fj },{name:'startDate',value: startDate },{name:'endDate',value: endDate }];
							$(".dataPengajuan").flexOptions({params: dt}).flexReload();
						}
			        }
			    }
			});
	    } else if (com == 'Cetak') {
			var id = $("input[name='id[]']:checked").val();
			var count = $("input[name='id[]']:checked").length;
			if (count==0 || count>1) {
			    onload = myBad('');
			    return false;
			} else {
			    window.open('print/kaskeluar/'+id+'/');
			}
		}
	}

	$('.flexTagihan').flexigrid({
	    height : 200,
	    width : 'auto',
	    showToggleBtn : false
	});

	$(".acc-menu #2").css('display','block');
	$('#cancel').click(function(event){
		document.location.href="transaksi-pengajuan";
	});
});
function showTagihan(ajuId){
	$.ajax({ 
	    url: 'system/control/validasi.php',
	    data: {action:'showTagihan', 'ajuId': ajuId},
	    type: 'post',
	    beforeSend: function(){
	    	onload = showLoading();
	    },
	    success: function(output) {
	    	onload = hideLoading();
	    	$("#dataVendor").html(output);
			$('#getHutangDtl').modal('show');
		}
	});
}
function showTagihanDtl(KodeDealer,jnsHutang,KodeAP,noTagihan){
	$.ajax({ 
	    url: 'system/view/getDataHutangDtl.php',
	    data: {action: 'getDataHutangDtl','KodeDealer': KodeDealer,'jnsHutang': jnsHutang,'KodeAP': KodeAP,'noTagihan': noTagihan},
	    type: 'post',
	    beforeSend: function(){
	    	onload = showLoading();
	    },
	    success: function(output) {
	    	onload = hideLoading();
	    	if (output=='0') {
	    		onload = needValue('Pengajuan Credit Note','Gagal Koneksi Cabang!');
	    		$("#KodeDealer").val('');
	    		$("#isGetDealer").css("display","none");
	    	} else if (output=='1') {
	    		onload = needValue('Pengajuan Credit Note','Gagal Koneksi HO!');
	    		$("#KodeDealer").val('');
	    		$("#isGetDealer").css("display","none");
	    	} else if (output=='2') {
	    		onload = needValue('Pengajuan Credit Note','Database tidak tersedia!');
	    		$("#KodeDealer").val('');
	    		$("#isGetDealer").css("display","none");
	    	} else {
	    		$("#dataHutangDtl2").html(output);
	    		$("#isGetDealer").css("display","block");
	    		$('#getHutangDtl2').modal('show'); 
	    	}
		}
	});
}