<?php require_once ('system/inc/permission.php'); 
$dt=mssql_fetch_array(mssql_query("select * from sys_user where idUser='".$_SESSION['UserID']."'"));
$dept_user = $dt['department'];
$divisi_user=$dt['divisi'];
echo $divisi_user." : ".$dept_user;
?>
<link rel="stylesheet" type="text/css" href="assets/plugins/flexii/css/flexigrid2.css" />
<div class="row">
	<form action="transaksi-validasi-modify" id="Form" method="POST">
	    <span id="post"></span>
	    <input type="hidden" id="IdUser" value="<?php echo $_SESSION['UserID'] ?>">
	    <input type="hidden" id="level" value="<?php echo $_SESSION['level'] ?>">
	    <input type="hidden" id="KodeDealer" value="<?php echo $user['KodeDealer']; ?>" />
	    <div class="col-sm-12">
	        <table class="flexme3" style="display: none"></table>
	        <script type="text/javascript">
	        	jQuery(document).ready(function($) {
	        		$(".flexme3").flexigrid({
					    url : 'system/data/validasi.php?IdUser=<?php echo $_SESSION[UserID] ?>',
					    dataType : 'xml',
					    colModel : [ 
					        {
						        display : '#',
						        name : 'evo_id',
						        width : 30,
						        sortable : true,
						        align : 'center'
					        },{
						        display : 'Over?',
						        name : 'overbudget',
						        width : 50,
						        sortable : true,
						        align : 'center'
					        },{
						        display : 'Status Pengajuan',
						        name : 'stataju',
						        width : 200,
						        sortable : true,
						        align : 'left'
					        }, {
					            display : 'Pembuat',
					            name : 'pembuat',
					            width : 100,
					            sortable : true,
					            align : 'left'
					        },{
						        display : 'Tgl Aju',
						        name : 'tgl_pengajuan',
						        width : 80,
						        sortable : true,
						        align : 'left'
					        },{
						        display : 'Tgl Bayar',
						        name : 'tgl_bayar',
						        width : 80,
						        sortable : true,
						        align : 'left'
					        },{
						        display : 'No Evo Pay',
						        name : 'nobukti',
						        width : 120,
						        sortable : true,
						        align : 'left'
					        }, {
					            display : 'No Tagihan',
					            name : 'notagihan',
					            width : 120,
					            sortable : true,
					            align : 'left'
					        }, {
					            display : 'Supplier',
					            name : 'namavendor',
					            width : 150,
					            sortable : true,
					            align : 'left'
					        }, {
					            display : 'Total Nominal',
					            name : 'totNom',
					            width : 100,
					            sortable : true,
					            align : 'left'
					        }, {
					            display : 'Keterangan',
					            name : 'keterangan',
					            width : 150,
					            sortable : true,
					            align : 'left'
					        }, {
					            display : 'No Akun',
					            name : 'noAkun',
					            width : 100,
					            sortable : true,
					            align : 'left'
					        }, {
					            display : 'Nama Akun',
					            name : 'namaAkun',
					            width : 100,
					            sortable : true,
					            align : 'left'
					        }, {
					            display : 'Nominal',
					            name : 'nominal',
					            width : 100,
					            sortable : true,
					            align : 'left'
					        }, {
					            display : 'RAPB Bulan',
					            name : 'rapbBln',
					            width : 100,
					            sortable : true,
					            align : 'left'
					        }, {
					            display : 'REAL Bulan',
					            name : 'realBln',
					            width : 100,
					            sortable : true,
					            align : 'left'
					        }, {
					            display : 'RAPB S/D',
					            name : 'rapbOg',
					            width : 100,
					            sortable : true,
					            align : 'left'
					        }, {
					            display : 'REAL S/D',
					            name : 'realOg',
					            width : 100,
					            sortable : true,
					            align : 'left'
					        }, {
					            display : 'RAPB Tahun',
					            name : 'rapbThun',
					            width : 100,
					            sortable : true,
					            align : 'left'
					        }, {
					            display : 'REAL Tahun',
					            name : 'realThun',
					            width : 100,
					            sortable : true,
					            align : 'left'
					        }, {
					            display : 'Status',
					            name : '',
					            width : 100,
					            sortable : true,
					            align : 'left'
					        }
					    ],
					    buttons : [ 
							<?php
							if ($_SESSION['level']=='ADMIN' or $_SESSION['level']=='KASIR') {
								if ($_SESSION['kodedealer']!='2010') {
									$srcLevel = "ACCOUNTING";
								} else {
									$srcLevel = "TAX";
								}
							} else {
								$srcLevel = $_SESSION['level'];
							}
							
							
							?>
					        { name : 'Search', bclass : 'search', onpress : button },
							<?php
							
							if ( ($divisi_user=='FINANCE and ACCOUNTING' && $_SESSION['level']=='DEPT. HEAD' && $dept_user=='FINANCE' ) || $_SESSION['level']=='DIREKSI' || $_SESSION['level']=='DIREKSI 2' || $_SESSION['level']=='DIV. HEAD') {  ?>		
								{ name : 'Single Approve', bclass : 'add', onpress : button },
							<?php
							} else { ?>
								{ name : 'Detail Tagihan', bclass : 'add', onpress : button },							 	
							<?php
							}
							
							if ( ($divisi_user=='FINANCE and ACCOUNTING' && $_SESSION['level']=='DEPT. HEAD' && $dept_user=='FINANCE' ) || $_SESSION['level']=='DIREKSI' || $_SESSION['level']=='DIREKSI 2' || $_SESSION['level']=='DIV. HEAD') {  ?>		
								{ name : 'Batch Approve', bclass : 'approve', onpress : button },
								{ name : 'Reject', bclass : 'reject', onpress : button },
							<?php } ?>	
					        // { name : 'Refresh', bclass : 'reset', onpress : button }
					    ],
					    title : 'Form Validasi Pengajuan',
					    sortname : "tgl_pengajuan",
					    sortorder : "desc",
					    usepager : true,
					    useRp : true,
					    rp : 10,
					    rpOptions: [10, 20, 50, 100],
					    showToggleBtn : false,
					    width : 'auto',
					    height : '300',
						onSuccess: function(){
						    onload = hideLoading();
						}
					});
					function button(com) {
					    if (com == 'Search') {
					        bootbox.dialog({
							    message: '<form action="" method="post" class="form-horizontal"><div class="form-group" style="margin-bottom: 2px;"> <label class="col-md-4 control-label">No. Evopay</label><div class="col-md-8"> <input class="form-control" id="NoBuktiPengajuan" name="NoBuktiPengajuan"></div></div><div class="form-group" style="margin-bottom: 2px;<?php echo $display ?>"> <label class="col-md-4 control-label">Tgl Pengajuan</label><div class="col-md-4"> <input type="date" name="startDate" id="startDate" class="form-control"></div><div class="col-md-4"> <input type="date" name="endDate" id="endDate" class="form-control"></div></div></form>',
							    title: "Search",
							    buttons: {
							        main: {
							            label: "Search",
							            className: "btn-sm btn-primary",
							            callback: function() {
							                onload = showLoading();
											var NoBuktiPengajuan    = $("#NoBuktiPengajuan").val();
											var startDate   		= $("#startDate").val();
											var endDate   			= $("#endDate").val();
											var dt = [{name:'NoBuktiPengajuan',value: NoBuktiPengajuan },{name:'startDate',value: startDate },{name:'endDate',value: endDate }];
											$(".flexme3").flexOptions({params: dt}).flexReload();
										}
							        }
							    }
							});
							<?php
						if ( ($divisi_user=='FINANCE and ACCOUNTING' && $_SESSION['level']=='DEPT. HEAD' && $dept_user=='FINANCE' ) || $_SESSION['level']=='DIREKSI' || $_SESSION['level']=='DIREKSI 2' || $_SESSION['level']=='DIV. HEAD') { ?>				
					    } else if (com == 'Single Approve') {
						<? }  else { ?>
						} else if (com == 'Detail Tagihan') {
						<? } ?>
							var count = $("input[name='id[]']:checked").length;
							if (count==0 || count>1) {
							    onload = myBad('Form Validasi Pengajuan');
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
								    		$("#post").html('<input type="hidden" name="modify" value="">');
							    			document.forms['Form'].submit();
								    	}
									}
								});
							}
							
					    } 
						<?php
							
						if ( ($divisi_user=='FINANCE and ACCOUNTING' && $_SESSION['level']=='DEPT. HEAD' && $dept_user=='FINANCE' ) || $_SESSION['level']=='DIREKSI' || $_SESSION['level']=='DIREKSI 2' || $_SESSION['level']=='DIV. HEAD') {  ?>		
							
						else if (com == 'Batch Approve') {
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
								
			  					var IdUser = "<?php echo $_SESSION['UserID'];?>";
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
											
											// $pesan .= "Transaksi telah berhasil di ".$val."!#".$dt['nobukti']."#".$n_bodyIntra."#".$n_nik."#".$n_email."#".$n_no_tlp."#".$n_bodyWa;
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
										}
									});
								}
								
							}
							
						} else if (com == 'Reject') {
							var count = $("input[name='id[]']:checked").length;
							if (count==0) {
								onload = myBad('Approval Progress Monitoring');
								return false;
							} else {
								var evoid = [];
								 $("input[name='id[]']:checked").each(function(){
										evoid.push($(this).val());
								});
								for (i=0;i<evoid.length;i++) {
									$.ajax({ 
										url: 'system/control/validasi.php',
										data: { 
											action:'validasimulti', 'evoid': evoid[i], 'val': 'Reject'
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
						} 
						<? } ?>
					}
				});
			</script>
	    </div>
	</form>
</div>