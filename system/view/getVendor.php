<link rel="stylesheet" type="text/css" href="assets/plugins/flexii/style.css" />
<script type="text/javascript" src="assets/plugins/flexii/js/flexigrid.pack.js"></script>
<link rel="stylesheet" type="text/css" href="assets/plugins/flexii/css/flexigrid.pack.css" />
<input type="hidden" id="KodeDealer" value="<?php echo $_REQUEST['KodeDealer'] ?>">
<input type="hidden" id="Tipe" value="<?php echo $_REQUEST['Tipe'] ?>">
<input type="hidden" id="Tipe" value="<?php echo $_REQUEST['Tipe'] ?>">
<table class="flexme2" style="display: none"></table>
<?php
	$KodeDealer = addslashes($_REQUEST['KodeDealer']);
	include '../inc/koneksi.php';
	//echo $msg;
	if ($msg=='0') {
		//echo "0";
		//echo "<br/>";
		echo $msg;
	} else if ($msg=='1') {
		//echo "1";
//	} else if (!mssql_select_db("[$table]")) {
//		echo "2";
	} else if ($msg=='3') {
?>
	<script type="text/javascript">
	    jQuery(document).ready(function($) {
	    	var KodeDealer = $("#KodeDealer").val();
			var Tipe = $("#Tipe").val();
			var tipehutang = $("#tipehutang").val();
			$(".flexme2").flexigrid({
				dataType : 'xml',
			    colModel : [ 
			        {
			        display : '#',
			        name : 'KodeLgn',
			        width : 40,
			        sortable : true,
			        align : 'center'
			        }, {
			            display : 'Kode Vendor',
			            name : 'KodeLgn',
			            width : 150,
			            sortable : true,
			            align : 'left'
			        }, {
			            display : 'Nama Vendor',
			            name : 'NamaLgn',
			            width : 300,
			            sortable : true,
			            align : 'left'
			        }
			    ],
			    buttons : [ 
			        {
			            name : 'Pick',
			            bclass : 'add',
			            onpress : button
			        }
			    ],
			    searchitems : [ 
	              { display : 'Kode Vendor', name : 'KodeLgn' },
	              { display : 'Nama Vendor', name : 'NamaLgn'},
	            ],
			    sortname : "KodeLgn",
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
			$('.flexme2').flexOptions({
				url:'system/data/getVendor.php', 
				newp: 1,
				params:[{
						name:'KodeDealer', value: KodeDealer
					},{
						name:'Tipe', value: Tipe
					},{
						name:'tipehutang', value: tipehutang
					}
				]
			}).flexReload();
			function sortAlpha(com) { 
	            jQuery('.flexme4').flexOptions({newp:1, params:[{name:'letter_pressed', value: com},{name:'qtype',value:$('select[name=qtype]').val()}]});
	            jQuery(".flexme4").flexReload(); 
	        }
			function button(com) {
			    if (com == 'Pick') {
					var generallen = $("input[name='id[]']:checked").length;
					if (generallen==0 || generallen>1) {
					    onload = myBad('Pengajuan Credit Note');
					    return false;
					} else {
					    var data = $("input[name='id[]']:checked").val();
					    var r = data.split("#");
					    $("#kode_vendor").val(r[0]);
						$("#namaVendor").val(r[1]);
						$("#benificary_account").val(r[2]);
						$("#nama_bank").val(r[3]);
						$("#nama_pemilik").val(r[4]);
						$("#email_penerima").val(r[5]);
						$("#nama_alias").val(r[6]);
						$("#npwp").val(r[8]);
						$("#keterangan").val(r[1].toUpperCase());
						
						var Tipe =  $("#Tipe").val();
						var npwp = r[8];
						
						// ket keterangan akun
						if (Tipe=='HUTANG') {
							var con = $("select[name='trfPajak[]']").length;
							
							if (con==0){
								$("#keteranganAkun_1").val(r[1].toUpperCase());
								$.ajax({ 
									url: 'system/control/akun.php',
									data: { action:'getPphNew', 'npwp': npwp},
									type: 'post',
									beforeSend: function(){
										onload = showLoading();
									},
									success: function(output) {
										onload = hideLoading();
										$("#trfPajak_1").html(output);
									}
								});
								
							} else {		
								for (var i = 1; i <= con; i++) {	
									$("#keteranganAkun_"+i).val(r[1].toUpperCase());
									//onload = getPph(i);
									$.ajax({ 
										url: 'system/control/akun.php',
										data: { action:'getPphNew', 'npwp': npwp},
										type: 'post',
										beforeSend: function(){
											onload = showLoading();
										},
										success: function(output) {
											onload = hideLoading();
											$("#trfPajak_"+i).html(output);
										}
									});
								}
						
							}
						} else if (Tipe=='BIAYA') {		
							var con = $("select[name='posbiaya[]']").length;
							if (con==0){
								$("#keteranganAkun_1").val(r[1].toUpperCase());
								$.ajax({ 
									url: 'system/control/akun.php',
									data: { action:'getPphNew', 'npwp': npwp},
									type: 'post',
									beforeSend: function(){
										onload = showLoading();
									},
									success: function(output) {
										onload = hideLoading();
										$("#trfPajak_1").html(output);
									}
								});
							} else {		
								for (var i = 1; i <= con; i++) {	
									$("#keteranganAkun_"+i).val(r[1].toUpperCase());
									//onload = getPph(i);
									$.ajax({ 
										url: 'system/control/akun.php',
										data: { action:'getPphNew', 'npwp': npwp},
										type: 'post',
										beforeSend: function(){
											onload = showLoading();
										},
										success: function(output) {
											onload = hideLoading();
											$("#trfPajak_"+i).html(output);
										}
									});
									
								}
							}
						}

						$('.gethutang').flexOptions({
							url:'system/data/gethutang.php', 
							newp: 1,
							params:[
								{ name:'KodeLgn', value: r[0] },
								{ name:'KodeDealer', value: KodeDealer },
								{ name:'tipehutang', value: tipehutang }
							]
						}).flexReload();

						
						if (Tipe=='HUTANG') {
							if (r[7]=='' || r[7]==' ') {
								$("#tipeppn").val('N');
							} else {
								$("#tipeppn").val(r[7]);
							}
						} else if (Tipe=='BIAYA') {
							if (r[7]=='' || r[7]==' ' || r[7]=='T') {
								$("#is_ppn").val('0');
								$('#is_ppn').removeAttr("checked");
								$('#is_ppn').attr("disabled","disabled");
								$("#no_fj").attr("readonly","readonly");
							} else {
								$("#is_ppn").val('1');
								$('#is_ppn').prop("checked","checked");
								$("#no_fj").removeAttr('readonly');
							}
							$("#tipeppn").val(r[7]);
						}
						
						$("input[name='id[]']").attr('checked', false);
						$('#getVendor').modal('hide'); 
					}
				}
			}
		});
	</script>
<?php } ?>