<?php  
	require_once ('system/inc/permission.php'); 
	$modify = isset($_REQUEST['modify']) ? $_REQUEST['modify'] : null;
	$nobukti = isset($_REQUEST['nobukti']) ? $_REQUEST['nobukti'] : null;
	$mcm = mssql_fetch_array(mssql_query("
		select noCsv,ISNULL(tgl_mcm, convert(varchar, getdate(), 112)) as tgl_mcm,tf_from_account,sum(Amount) totnom,
		(select count(evotf_id) from DataEvoTransfer where noCsv=x.noCsv) totBaris from (
			select noCsv,tgl_mcm,tf_from_account,case when tipe='HUTANG' then htg_stl_pajak else biaya_yg_dibyar end Amount
			from DataEvo a 
			inner join DataEvoTransfer b on a.nobukti=b.nobukti
			where noCsv = '".$nobukti."'
		) x GROUP BY noCsv,tf_from_account,tgl_mcm
	"));
	

	$skipdir = mssql_fetch_array(mssql_query('select skip_direksi,skip_direksi2 from settingAkun where id=1'));
?>
<script type="text/javascript">
	jQuery(document).ready(function($) {
		$(".acc-menu #3").css('display','block');
	});
</script>
<div class="row">
	<div class="col-sm-12">
		<?php if ($modify=='CIMB Biz C') { ?>
			<link rel="stylesheet" type="text/css" href="assets/plugins/flexii/css/flexigrid2.css" />
			<style type="text/css">
				.form-control { border: 0px solid #d2d3d6; }
				.readonly[readOnly] {
			      background: #fff !important;
			    }
			</style>
			<script type='text/javascript' src='assets/plugins/form-datepicker/js/bootstrap-datepicker.js'></script> 
			<form action="report-reportpengajuan-modify" id="Form" method="POST">
				<table class="flexme3" style="display: none"></table>
				<script type="text/javascript">
					jQuery(document).ready(function($) {
				        $(".flexme3").flexigrid({
						    url : 'system/data/reportpengajuantf.php?id=<?php echo $nobukti; ?>&jns=bizc',
						    dataType : 'xml',
						    colModel : [ 
						        {
							        display : '#',
							        name : '',
							        width : 30,
							        sortable : false,
							        align : 'center'
						        },{
							        display : 'Transfer From Account',
							        name : 'tf_from_account',
							        width : 150,
							        sortable : false,
							        align : 'left'
						        },{
							        display : 'Beneficary Account',
							        name : 'benificary_account',
							        width : 150,
							        sortable : false,
							        align : 'left'
						        },{
							        display : 'Account Name',
							        name : 'nama_pemilik',
							        width : 150,
							        sortable : false,
							        align : 'left'
						        },{
							        display : 'Amount',
							        name : 'Amount',
							        width : 150,
							        sortable : false,
							        align : 'left'
						        },{
							        display : 'Keterangan',
							        name : 'Keterangan',
							        width : 150,
							        sortable : false,
							        align : 'left'
						        },{
							        display : 'Pembayaran Via',
							        name : 'bayar_via',
							        width : 150,
							        sortable : false,
							        align : 'left'
						        },{
							        display : 'Nama Bank Penerima',
							        name : 'nama_bank',
							        width : 150,
							        sortable : false,
							        align : 'left'
						        },{
							        display : 'Beneficary Email',
							        name : 'email_penerima',
							        width : 150,
							        sortable : false,
							        align : 'left'
						        },{
							        display : 'Konfirm. Penerima Email',
							        name : 'konfirm_email',
							        width : 150,
							        sortable : false,
							        align : 'left'
						        },{
							        display : 'Jenis Penerima',
							        name : 'jenis_penerima',
							        width : 150,
							        sortable : false,
							        align : 'left'
						        },{
							        display : 'Payment Detail',
							        name : 'payment_detail',
							        width : 150,
							        sortable : false,
							        align : 'left'
						        },{
							        display : 'Kota Bank Penerima',
							        name : 'kota_bank_penerima',
							        width : 150,
							        sortable : false,
							        align : 'left'
						        },{
							        display : 'Kode Ducapil',
							        name : 'kode_dukcapil',
							        width : 150,
							        sortable : false,
							        align : 'left'
						        }
						    ],
						    buttons : [ 
						        {
						            name : 'Export CSV',
						            bclass : 'add',
						            onpress : button
						        },{
						            name : 'Edit',
						            bclass : 'edit',
						            onpress : button
						        },{
								    name : 'Delete',
								    bclass : 'delete',
								    onpress : button
								}
						    ],
						    title : '<?php echo $modify." : ".$nobukti; ?>',
						    sortname : "evotf_id",
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
						    if (com == 'Export CSV') {
								var nobukti    ="<?php echo $nobukti; ?>";
								window.open('system/view/reportpengajuan_csv.php?id='+nobukti);
								return true;
						    } else if (com == 'Edit') {
								var id = $("input[name='id[]']").length;
								var evo = "";
								for (var i = 1; i <= id; i++) {
									var chk = $("#chk_"+i).val();
									var keterangan = $("#keterangan_"+i).val();
									var bayar_via = $("#bayar_via_"+i).val();
									var email_penerima = $("#email_penerima_"+i).val();
									var konfirm_email = $("#konfirm_email_"+i).val();
									var jenis_penerima = $("#jenis_penerima_"+i).val();
									var kota_bank_penerima = $("#kota_bank_penerima_"+i).val();
									var kode_dukcapil = $("#kode_dukcapil_"+i).val();
									evo += chk+"#"+keterangan+"#"+bayar_via+"#"+email_penerima+"#"+konfirm_email+"#"+jenis_penerima+"#"+kota_bank_penerima+"#"+kode_dukcapil+"_cn_";
								}
								var data = evo.slice(0,-4);
								$.ajax({ 
									url: 'system/control/pengajuan.php',
									data: { action:'edit-transfer', 'data': data},
									type: 'post',
									beforeSend: function(){
										onload = showLoading();
									},
									success: function(output) {
										onload = hideLoading();
										$(".flexme3").flexReload();
									}
								});
						    } else if (com == 'Delete') {
								var generallen = $("input[name='id[]']:checked").length;
								if (generallen==0) {
								    onload = myBad('Pengajuan Credit Note');
								    return false;
								} else {
								    var id = $("input[name='id[]']").length;
									var evo = "";
									for (var i = 1; i <= id; i++) {
										if ($("#chk_"+i).is(":checked")) {
											var chk = $("#chk_"+i).val();
											evo += chk+",";
										}
									}
									var data = evo.slice(0,-1);
									$.ajax({ 
										url: 'system/control/pengajuan.php',
										data: { action:'delete-transfer', 'data': data},
										type: 'post',
										beforeSend: function(){
											onload = showLoading();
										},
										success: function(output) {
											onload = hideLoading();
											$(".flexme3").flexReload();
										}
									});
								}
						    } 
						}
					});
					function dukcapil(no){
						var kota_penerima = $("#kota_penerima_"+no).val();
						var data = kota_penerima.split("#");
						$("#kota_bank_penerima_"+no).val(data[0]);
						$("#kode_dukcapil_"+no).val(data[1]);
						$("#kodedukcapil_"+no).html(data[1]);
					}
				</script>
			</form>
		<?php } else if ($modify=='Mandiri MCM') { ?>
			<link rel="stylesheet" type="text/css" href="assets/plugins/flexii/css/flexigrid2.css" />
			<style type="text/css">
				.form-control { border: 0px solid #d2d3d6; }
				.readonly[readOnly] {
			      background: #fff !important;
			    }
			</style>
			<script type='text/javascript' src='assets/plugins/form-datepicker/js/bootstrap-datepicker.js'></script> 
			<form action="report-reportpengajuan-modify" id="Form" method="POST">
				<table class="flexme3" style="display: none"></table>
				<script type="text/javascript">
					jQuery(document).ready(function($) {
				        $(".flexme3").flexigrid({
						    url : 'system/data/reportpengajuantf.php?id=<?php echo $nobukti; ?>&jns=mcm',
						    dataType : 'xml',
						    colModel : [ 
						        {
							        display : '#',
							        name : '',
							        width : 30,
							        sortable : false,
							        align : 'center'
						        },{
							        display : 'No. Rekening Penerima',
							        name : '',
							        width : 150,
							        sortable : false,
							        align : 'left'
						        },{
							        display : 'Nama Penerima',
							        name : '',
							        width : 150,
							        sortable : false,
							        align : 'left'
						        },{
							        display : 'Alamat',
							        name : '',
							        width : 150,
							        sortable : false,
							        align : 'left'
						        },{
							        display : 'Mata Uang',
							        name : '',
							        width : 80,
							        sortable : false,
							        align : 'left'
						        },{
							        display : 'Nominal',
							        name : '',
							        width : 150,
							        sortable : false,
							        align : 'left'
						        },{
							        display : 'Keterangan',
							        name : '',
							        width : 150,
							        sortable : false,
							        align : 'left'
						        },{
							        display : 'Layanan Transfer',
							        name : '',
							        width : 150,
							        sortable : false,
							        align : 'left'
						        },{
							        display : 'RTGS/Kliring',
							        name : '',
							        width : 150,
							        sortable : false,
							        align : 'left'
						        },{
							        display : 'Bank Penerima',
							        name : '',
							        width : 150,
							        sortable : false,
							        align : 'left'
						        },{
							        display : 'Kota Cbg. Pembuka',
							        name : '',
							        width : 150,
							        sortable : false,
							        align : 'left'
						        },{
							        display : 'Konfirm. Email',
							        name : '',
							        width : 150,
							        sortable : false,
							        align : 'left'
						        },{
							        display : 'Alamat Email',
							        name : '',
							        width : 150,
							        sortable : false,
							        align : 'left'
						        },{
							        display : 'Charger Instruction',
							        name : '',
							        width : 150,
							        sortable : false,
							        align : 'left'
						        }
						    ],
						    buttons : [ 
						        {
						            name : 'Export CSV',
						            bclass : 'add',
						            onpress : button
						        },{
						            name : 'Edit',
						            bclass : 'edit',
						            onpress : button
						        },{
								    name : 'Delete',
								    bclass : 'delete',
								    onpress : button
								}
						    ],
						    title : '<?php echo $modify." : ".$nobukti; ?>',
						    sortname : "evotf_id",
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
						$(".tDiv").after('<div class="bDiv" id="wannTest" style="min-height: 65px;border-bottom: 0;display:block;border-top: 1px solid #dbe1e8;padding:10px"> <form class="form-horizontal"> <div class="form-group" style="margin: 0;"> <div class="col-sm-3"> <label class="control-label">Tanggal</label> <input type="text" id="tgl_mcm" class="form-control" style="border: 1px solid #d2d3d6;" value="<?php echo $mcm[tgl_mcm]; ?>"> </div><div class="col-sm-3"> <label class="control-label">Rekening Sumber</label> <input type="text" id="rekSumber" class="form-control" style="border: 1px solid #d2d3d6;" value="<?php echo $mcm[tf_from_account]; ?>" readonly> </div><div class="col-sm-3"> <label class="control-label">Total Baris</label> <input type="text" id="totBaris" class="form-control" style="border: 1px solid #d2d3d6;" value="<?php echo $mcm[totBaris]; ?>" readonly> </div><div class="col-sm-3"> <label class="control-label">Total Jumlah Nominal</label> <input type="text" id="totNominal" class="form-control" style="border: 1px solid #d2d3d6;" value="<?php echo $mcm[totnom]; ?>" readonly> </div></div></form></div>'
						);
				        $('#tgl_mcm').datepicker({ format: 'yyyymmdd' });
						function button(com) {
						    if (com == 'Export CSV') {
								var nobukti    ="<?php echo $nobukti; ?>";
								window.open('system/view/reportpengajuan_csv.php?id='+nobukti);
								return true;
						    } else if (com == 'Edit') {
								var id = $("input[name='id[]']").length;
							    var tgl_mcm = $("#tgl_mcm").val();
								var evo = "";
								for (var i = 1; i <= id; i++) {
									var chk = $("#chk_"+i).val();
									var Alamat = $("#Alamat_"+i).val();
									var layanan_transfer = $("#layanan_transfer_"+i).val();
									var kode_rtgs_kliring = $("#kode_rtgs_kliring_"+i).val();
									var kota_cbg_buka = $("#kota_cbg_buka_"+i).val();
									var konfirm_email = $("#konfirm_email_"+i).val();
									var email_penerima = $("#email_penerima_"+i).val();
									var charger_inst = $("#charger_inst_"+i).val();
									evo += chk+"#"+Alamat+"#"+layanan_transfer+"#"+kode_rtgs_kliring+"#"+kota_cbg_buka+"#"+konfirm_email+"#"+email_penerima+"#"+charger_inst+"_cn_";
								}
								var data = evo.slice(0,-4);
								$.ajax({ 
									url: 'system/control/pengajuan.php',
									data: { action:'edit-transfer-mcm', 'data': data, 'tgl_mcm' : tgl_mcm},
									type: 'post',
									beforeSend: function(){
										onload = showLoading();
									},
									success: function(output) {
										onload = hideLoading();
										$(".flexme3").flexReload();
									}
								});
						    } else if (com == 'Delete') {
								var generallen = $("input[name='id[]']:checked").length;
								if (generallen==0) {
								    onload = myBad('Pengajuan Credit Note');
								    return false;
								} else {
								    var id = $("input[name='id[]']").length;
									var evo = "";
									for (var i = 1; i <= id; i++) {
										if ($("#chk_"+i).is(":checked")) {
											var chk = $("#chk_"+i).val();
											evo += chk+",";
										}
									}
									var data = evo.slice(0,-1);
									$.ajax({ 
										url: 'system/control/pengajuan.php',
										data: { action:'delete-transfer', 'data': data},
										type: 'post',
										beforeSend: function(){
											onload = showLoading();
										},
										success: function(output) {
											onload = hideLoading();
											$(".flexme3").flexReload();
										}
									});
								}
						    } 
						}
					});
					function layanen_tf(no){
						var layanan = $("#layanan_transfer_"+no).val();
						if (layanan == 'LBU' || layanan == 'RBU') {
							if (layanan=='LBU') { var val = '1'; } else if (layanan=='RBU') { var val = '2'; }
							$("#layanan_tf_"+no).css('padding','0px');
							$("#layanan_tf_"+no).html("<div class='input-group'> <input class='form-control readonly' style='$inp' id='kode_rtgs_kliring_"+no+"' readonly type='text'> <span class='input-group-btn'> <button type='button' id='rtgs_"+no+"' onclick='getsysMcm("+no+");' value='"+val+"' class='btn btn-sm btn-more'> <i class='fa fa-th'></i> </button> </span> </div>");
						} else {
							$("#layanan_tf_"+no).css('padding','5px');
							$("#layanan_tf_"+no).html('&nbsp;<input type="hidden" id="kode_rtgs_kliring_'+no+'">');
						}
					}
					function getsysMcm(no){
						var val = $("#rtgs_"+no).val();
						if (val=='1') {
							$('.modal-title').html('Data Kliring');
						} else {
							$('.modal-title').html('Data RTGS');
						}
						$.ajax({ 
						    url: 'system/view/sys_mcm.php',
						    data: { 'kode': val, 'no': no },
						    type: 'post',
						    beforeSend: function(){
						    	onload = showLoading();
						    },
						    success: function(output) {
						    	$("#datasysMcm").html(output);
						    	$('#getsysMcm').modal('show');
						    	onload = hideLoading();
							}
						});
					}
				</script>
			</form>
		<?php } else if ($modify=='pengajuan-detail') { ?>
			<link rel="stylesheet" type="text/css" href="assets/plugins/flexii/css/flexigrid.pack.css" />
			<link rel="stylesheet" type="text/css" href="assets/css/jquery.fileupload.css" />
			<style type="text/css">
				#datarapb th { font-weight: bold;text-align: center; }
				#datarapb td { text-align: right;padding: 0 5px; }
				.input-group-addon {padding: 0;min-width: 0;padding: 2px 10px; }
			</style>
			<script type="text/javascript">
				function getFile(data){
					window.open('system/files/'+data);
				}
			</script>
			<?php
				$id = isset($_POST['evo_id']) ? $_POST['evo_id'] : null;
				$nobuk = isset($_POST['nobuk']) ? $_POST['nobuk'] : null;
				if ($id==null && $nobuk==null) {
					$id = isset($_POST['id'][0]) ? $_POST['id'][0] : null;
					$s_sort = "and a.evo_id = '".$id."'";
				} else if ($nobuk!=null) {
					$s_sort = "and a.nobukti = '".$nobuk."'";
				} else {
					$s_sort = "and a.evo_id = '".$id."'";
				}
				
				$vSql = "select a.*, b.namaUser, b.department, b.divisi, 
								case when a.tipe='HUTANG' then a.htg_stl_pajak else a.biaya_yg_dibyar end as totBayar
						from DataEvo a 
						left join sys_user b on a.userentry = b.IdUser
						where a.evo_id = a.evo_id $s_sort";
				$vw = mssql_fetch_array(mssql_query($vSql));
				$useraju = $vw['namaUser']." (".$vw['divisi']."/".$vw['department'].")";
			?>	
			<div class="panel panel-gray">
				<div class="panel-heading">
					<h4>Report Pengajuan Evo Pay</h4>
				</div>
				<form id="validate-form" action="test.php" method="POST" class="form-horizontal row-border">
					<div class="panel-body collapse in">
						<input type="hidden" id="IdUser" value="<?php echo $_SESSION['UserID']; ?>">
						<input type="hidden" id="level" value="<?php echo $_SESSION['level']; ?>">
						<input type="hidden" id="evo_id" value="<?php echo $vw['evo_id']; ?>">
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
										    		$qry = mssql_query("select a.KodeDealer,NamaDealer from SPK00..dodealer a 
													INNER JOIN sys_user b ON a.KodeDealer=b.KodeDealer
													where IdUser='".$IdUser."'",$conns);	
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
									<?php if ($vw['tipe']=='HUTANG') { ?>
										<section id="getForm">
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
											        <select id="IdAtasan" class="form-control" disabled>
											            <?php
															$KodeDealer = $vw['kodedealer'];
															$divisi = $vw['divisi'];
											            	$IdUser = $vw['userentry'];
															$atasan = mssql_fetch_array(mssql_query("select IdAtasan from sys_user where IdUser = '".$IdUser."'"));
															if ($atasan['IdAtasan']=='all') { $boss = ""; } else { $boss = "and IdUser = '".$atasan['IdAtasan']."'"; }
															// (tipe = 'SECTION HEAD' or tipe = 'ADH') and
															$sql = "select * from sys_user where  divisi in ('".$divisi."','all') and KodeDealer = '".$KodeDealer."' $boss";
															$rsl = mssql_query($sql);
															echo "<option value=''>- Pilih -</option>";
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
											    	<select id="tipehutang" class="form-control" disabled>
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
											        <select type="text" class="form-control" id="metode_bayar" disabled>
											            <?php
											            	$opt = array('' => '- Pilih -', 'Transfer' => 'Transfer', 'Cash' => 'Cash');
											            	foreach ($opt as $key => $value) {
											            		$plh = ($vw['metode_bayar']==$key)?"selected" : ""; 
											            		echo "<option value='$key' $plh>$value</option>";
											            	}
											            ?>
											        </select>
											    </div>
                                                 <label class="col-sm-2 control-label">Departemen Terkait</label>
											    <div class="col-sm-4">
											        <input type="text" class="form-control" id="deptterkait" value="<?php echo $vw['dept_terkait']; ?>" readonly>
											  	</div>
											</div>
											<div class="form-group">
											    <label class="col-sm-2 control-label">Benificary Account</label>
											    <div class="col-sm-4">
											        <input type="text" class="form-control" id="benificary_account" value="<?php echo $vw['benificary_account']; ?>" readonly>
											    </div>
											    <label class="col-sm-2 control-label">Tanggal Bayar</label>
											    <div class="col-sm-4">
											        <?php if ($_SESSION['level']=='ADH') { ?>
														<input type="date" class="form-control" id="tgl_bayar" value="<?php echo $vw[tgl_bayar]; ?>">
													<?php } else { ?>
														<input type="date" class="form-control" id="tgl_bayar" value="<?php echo $vw[tgl_bayar]; ?>" readonly>
													<?php } ?>
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
											<div class="form-group">
											    <label class="col-sm-2 control-label">Nominal</label>
											    <div class="col-sm-4">
											        <input type="text" class="form-control" id="realisasi_nominal" value="<?php echo number_format($vw['realisasi_nominal'],0,",","."); ?>" readonly> 
											    </div>
											</div>
											<div class="form-group">
											    <label class="col-sm-2 control-label">Kode Akun</label>
											    <div class="col-sm-4">
											    	<input type="text" class="form-control" id="kodeAkun" value="<?php echo $vw['kodeAkun']; ?>" readonly>
											        <!-- <div class="input-group">
											            <input type="text" class="form-control" id="kodeAkun" value="<?php echo $vw['kodeAkun']; ?>" readonly>
											            <span class="input-group-addon" style="padding: 0;min-width: 0;"> 
											            	<button type="button" style="padding: 2px 10px;border: 0;" onclick="getAkun();"> 
											            		<i class="fa fa-search"></i>
											            	</button>
											            </span>
											        </div> -->
											    </div>
											    <label class="col-sm-2 control-label">Nama Akun</label>
											    <div class="col-sm-4">
											        <input type="text" class="form-control" id="namaAkun" value="<?php echo $vw['namaAkun']; ?>" readonly>
											    </div>
											</div>
											<div class="form-group">
											    <label class="col-sm-2 control-label">Type Ppn</label>
											    <div class="col-sm-4">
												    	<?php if ($_SESSION['level']=='ADH' or $_SESSION['level']=='TAX') { ?>
															<select type="text" class="form-control" id="tipeppn" onchange="getDppHtg();">
														<?php } else { ?>
															<select type="text" class="form-control" id="tipeppn" onchange="getDppHtg();" disabled>
														<?php } ?>
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
											        <input type="text" class="form-control" id="dpp" value="<?php echo number_format($vw['dpp'],0,",","."); ?>" readonly>
											    </div>
											    <label class="col-sm-2 control-label">Ppn</label>
											    <div class="col-sm-4">
											        <input type="text" class="form-control" id="ppn" value="<?php echo number_format($vw['ppn'],0,",",".");; ?>" readonly>
											    </div>
											</div>
											<div class="form-group">
											    <label class="col-sm-2 control-label">NPWP</label>
											    <div class="col-sm-4">
											        <input type="text" class="form-control" id="npwp" value="<?php echo $vw['npwp']; ?>" readonly>
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
													} else {
														while ($dtpos = mssql_fetch_array($rslpos)) {
															if ($_SESSION['level']=='ACCOUNTING' or $_SESSION['level']=='ADH' or $_SESSION['level']=='TAX') {
																$disabled = "";
															} else {
																$disabled = "disabled";
															}
															if ($vw['tipeppn']!='N') { $is_ppn = '1'; } else { $is_ppn = '0'; }
															if ($vw['npwp']!='') { $is_npwp = '1'; } else { $is_npwp = '0'; }
															echo '
																<div class="form-group">
																    <div class="col-sm-3">
																        <label class="control-label">Tarif Pajak</label>
																        <select type="text" name="trfPajak[]" class="form-control" id="trfPajak_'.$no.'" onchange="trfPajak('.$no.');" '.$disabled.' disabled>';
																       echo  $sql = "select jns_pph,tarif_persen,(jns_pph+'#'+convert(varchar,tarif_persen)) val 
															        		from settingPph where npwp = '".$is_npwp."' order by idpph asc";
																		$rsl = mssql_query($sql);
																		echo "<option value='Non Pph#0#00000000'>Non Pph</option>";
																		while ($dt = mssql_fetch_array($rsl)) {
																			if ($is_ppn=='0') {
																				if ($dt['jns_pph']=='Pph 4 Ayat 2') {
																					$jns = "non_pph_4";
																				} else {
																					$jns = "non_".str_replace(' ', '_', strtolower($dt['jns_pph']));
																				}
																			} else if ($is_ppn=='1') {
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
																        <input type="text" class="form-control" id="nominal_'.$no.'" value="'.$dtpos['nominal'].'" '.$disabled.' readonly>
																  	</div>
																    <div class="col-sm-3">
																        <label class="control-label">Nilai Pph</label>
																        <input type="text" class="form-control" id="nilaiPph_'.$no.'" value="'.$dtpos['nilai_pph'].'" readonly>
																        <!--
																        <div class="input-group">
																            <input type="text" class="form-control" id="nilaiPph_'.$no.'" value="'.$dtpos['nilai_pph'].'" readonly>
																          	<span class="input-group-addon" style="padding: 0;min-width: 0;">
																              <button type="button" style="padding: 2px 10px;border: 0;" onclick="addposhutang();" '.$disabled.'>
																                <i class="fa fa-plus"></i>
																              </button>
																          	</span>
																      	</div>
																      	-->
																    </div>
																	<div class="col-sm-3"> 
																		<label class="control-label">Keterangan Biaya</label>
																		<input type="text" class="form-control" id="keteranganAkun_'.$y.'" maxlength="200" value="'.$dpos['KeteranganAkun'].'" disabled />
																	</div>
																</div>
															';
															$no++;
														}
													}
												?>
											</span>
											<hr style="margin: 10px 0;">
											<div class="form-group">
											    <label class="col-sm-2 control-label">Hutang setelah pajak</label>
											    <div class="col-sm-4">
											        <input type="text" class="form-control" id="htg_stl_pajak" value="<?php echo number_format($vw['htg_stl_pajak'],0,",","."); ?>" readonly>
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
                                               		<select id="IdAtasan" class="form-control" disabled>
											            <?php
															$KodeDealer = $vw['kodedealer'];
															$divisi = $vw['divisi'];
											            	$IdUser = $vw['userentry'];
															$atasan = mssql_fetch_array(mssql_query("select IdAtasan from sys_user where IdUser = '".$IdUser."'"));
															if ($atasan['IdAtasan']=='all') { $boss = ""; } else { $boss = "and IdUser = '".$atasan['IdAtasan']."'"; }
															
															// (tipe = 'SECTION HEAD' or tipe = 'ADH') and 
															$sql = "select * from sys_user where divisi in ('".$divisi."','all') and KodeDealer = '".$KodeDealer."' $boss";
															$rsl = mssql_query($sql);
															echo "<option value=''>- Pilih -</option>";
															while ($dt = mssql_fetch_array($rsl)) {
																$plh = ($vw['IdAtasan']==$dt['IdUser'])?"selected" : ""; 
																echo "<option value='".$dt['IdUser']."' $plh>".$dt['namaUser']."</option>";
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
											        <input type="date" id="tgl_pengajuan" class="form-control" value="<?php echo $vw[tgl_pengajuan]; ?>" readonly>
											  	</div>
											  	<label class="col-sm-2 control-label">Tanggal Bayar</label>
											    <div class="col-sm-4">
											    	<input type="date" class="form-control" id="tgl_bayar" value="<?php echo $vw['tgl_bayar']; ?>" readonly/>
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
											        <input type="text" class="form-control" id="kode_vendor" value="<?php echo $vw['kode_vendor']; ?>" readonly>
											    </div>
											    <label class="col-sm-2 control-label">Nama Vendor</label>
											    <div class="col-sm-4">
											    	<input type="text" name="namaVendor" id="namaVendor" value="<?php echo $vw['namaVendor']; ?>" class="form-control" disabled>
											    </div>
											</div>
											<div class="form-group">
											    <label class="col-sm-2 control-label">Metode Pembayaran</label>
											    <div class="col-sm-4">
											        <select type="text" class="form-control" id="metode_bayar" disabled>
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
											        <input type="text" class="form-control" id="nama_bank_pengirim" value="<?php echo $vw['nama_bank_pengirim']; ?>" readonly>
											  	</div>
											    <label class="col-sm-2 control-label">Transfer From Account</label>
											    <div class="col-sm-4">
											        <input type="text" class="form-control" id="tf_from_account" value="<?php echo $vw['tf_from_account']; ?>" readonly>
											  	</div>
											</div>
											<div class="form-group">
											    <label class="col-sm-2 control-label">Realisasi Nominal</label>
											    <div class="col-sm-4">
											        <input type="text" class="form-control" id="realisasi_nominal" value="<?php echo number_format($vw['realisasi_nominal'],0,",","."); ?>" readonly>
											  	</div>
                                                 <label class="col-sm-2 control-label">Departemen Terkait</label>
											    <div class="col-sm-4">
											        <input type="text" class="form-control" id="deptterkait" value="<?php echo $vw['dept_terkait']; ?>" readonly>
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
											        <input type="text" class="form-control" id="dpp" value="<?php echo number_format($vw['dpp'],0,",","."); ?>" readonly> 
											        <span class="input-group-addon">Ppn</span>
											        <input type="text" class="form-control" id="ppn" value="<?php echo number_format($vw['ppn'],0,",","."); ?>" readonly>
											      </div>
											    </div>
											</div>
											<div class="form-group">
											    <label class="col-sm-2 control-label">NPWP</label>
											    <div class="col-sm-4">
											        <input type="text" class="form-control" id="npwp" value="<?php echo $vw[npwp]; ?>" readonly>
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
															<div class="form-group">
															    <div class="col-sm-3">
															        <label class="control-label">Pos Biaya</label>
															        <input type="hidden" name="posbiaya[]" id="kodeAkun_'.$y.'" value="'.$dpos['pos_biaya'].'">
															        <input type="text" class="form-control" id="ketAkun_'.$y.'" value="'.$dpos['ketAkun'].'" readonly>
															    </div>
															    <div class="col-sm-3">
															        <label class="control-label">Nominal</label>
															        <input type="text" class="form-control" id="nominal_'.$y.'" value="'.number_format($dpos['nominal'],0,",",".").'" disabled>
															  	</div>
															    <div class="col-sm-2">
															        <label class="control-label">Tarif Pajak</label>
															        <select type="text" class="form-control" id="trfPajak_'.$y.'" onchange="trfPajak('.$y.');" disabled>';
															        /*
																	$sql = "select jns_pph,tarif_persen,(jns_pph+'#'+convert(varchar,tarif_persen)) val 
														        		from settingPph where npwp = '".$vw['is_ppn']."' order by idpph asc";
																	*/
																	if ($vw['npwp']!='') { $is_npwp = '1'; } else { $is_npwp = '0'; }
																	$sql = "select jns_pph,tarif_persen,(jns_pph+'#'+convert(varchar,tarif_persen)) val 
														        		from settingPph where npwp = '".$is_npwp."' order by idpph asc";
																			
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
															        <input type="text" class="form-control" id="nilaiPph_'.$y.'" value="'.number_format($dpos['nilai_pph'],0,",",".").'" readonly>
															    </div>
																<div class="col-sm-2"> 
																	<label class="control-label">Keterangan Biaya</label>
																	<input type="text" class="form-control" id="keteranganAkun_'.$y.'" maxlength="200" value="'.$dpos['KeteranganAkun'].'" disabled />
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
											        <input type="text" class="form-control" id="total_dpp" value="<?php echo number_format($vw['total_dpp'],0,",","."); ?>" readonly>
											    </div>
											    <div class="col-sm-3">
											        <input type="hidden" class="form-control" id="over" value="<?php echo $over; ?>" readonly>
											    </div>
											</div>
											<hr style="margin: 10px 0;">
											<div class="form-group">
											    <label class="col-sm-2 control-label">Biaya yg harus dibayar</label>
											    <div class="col-sm-4">
											        <input type="text" class="form-control" id="biaya_yg_dibyar" value="<?php echo number_format($vw['biaya_yg_dibyar'],0,",","."); ?>" readonly>
											    </div>
											</div>
											<div class="form-group">
											    <label class="col-sm-2 control-label">Keterangan</label>
											    <div class="col-sm-10">
													<?php if ($_SESSION['level']=='ADH') { ?>
														<input type="text" class="form-control" id="keterangan" value="<?php echo $vw[keterangan]; ?>">
													<?php } else { ?>
														<input type="text" class="form-control" id="keterangan" value="<?php echo $vw[keterangan]; ?>" readonly>
													<?php } ?>
											    </div>
											</div>
                                            <div class="form-group">
											    <label class="col-sm-2 control-label">Diajukan oleh</label>
											    <div class="col-sm-10">
													<input type="text" class="form-control" id="useraju" value="<?php echo $useraju; ?>" readonly>
											    </div>
											</div>
										</section>
									<?php } ?>
									<section>
										<hr style="margin: 10px 0;">
										<div class="form-group">
											<label class="col-sm-12 control-label"><b>Note :</b></label> 
										</div>
										<?php 
										
										
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
																	 and a.nobukti = '".$vw['nobukti']."'  and isnull(a.deptterkait,'') != ''
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
													<input type="text" class="form-control" id="note_sectionhead" value="<?php	echo $row_deptterkait['note'];?>" readonly>
												</div>
											</div>
										<?php
											}
										}
										
										?>
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
												<label class="col-sm-2 control-label">Note</label> 
												<div class="col-sm-10">
													<input type="text" class="form-control" id="note_sectionhead" <?php note($vw['nobukti'],'SECTION HEAD','',''); ?> >
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
												<label class="col-sm-2 control-label">Note</label> 
												<div class="col-sm-10">
													<input type="text" class="form-control" id="note_dept_head" <?php note($vw['nobukti'],'DEPT. HEAD','',''); ?> >
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
												<label class="col-sm-2 control-label">Note</label> 
												<div class="col-sm-10">
													<input type="text" class="form-control" id="note_div_head" <?php note($vw['nobukti'],'DIV. HEAD','',''); ?> >
												</div>
											</div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Direksi</label> 
                                                <div class="col-sm-1">User :  </div>
                                                <div class="col-sm-4">
                                                        <?php $note_arr = usernote($vw['nobukti'],'DIREKSI','',''); 
                                                            echo $note_arr["user"]; ?>
                                                </div>
                                                <div class="col-sm-1">Waktu : </div>
                                                <div class="col-sm-4"><?php	echo $note_arr["tgl"]; ?></div>
                                            </div>
											<div class="form-group">

												<label class="col-sm-2 control-label">Note</label> 
												<div class="col-sm-10">
													<input type="text" class="form-control" id="note_direksi" <?php note($vw['nobukti'],'DIREKSI','',''); ?> >
												</div>
											</div>
                                            <?php
											
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
												<label class="col-sm-2 control-label">Note</label> 
												<div class="col-sm-10">
													<input type="text" class="form-control" id="note_direksi" <?php note($vw['nobukti'],'DIREKSI 2','',''); ?> >
												</div>
											</div>
                                            <?php } ?>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Dept.Head Finance / Releaser 1</label> 
                                                <div class="col-sm-1">User :  </div>
                                                <div class="col-sm-4">
                                                        <?php $note_arr = usernote($vw['nobukti'],'DEPT. HEAD FINANCE','FINANCE, ADMINISTRATION & SYSTEM TECHNOLOGY','FINANCE'); 
                                                        echo $note_arr["user"]; ?>
                                                </div>
                                               <div class="col-sm-1">Waktu : </div>
                                                <div class="col-sm-4"><?php	echo $note_arr["tgl"]; ?></div>
                                            </div>
											<div class="form-group">
												<label class="col-sm-2 control-label">Note</label> 
												<div class="col-sm-10">
													<input type="text" class="form-control" id="note_dept_head_fin" <?php note($vw['nobukti'],'DEPT. HEAD FINANCE','FINANCE, ADMINISTRATION & SYSTEM TECHNOLOGY','FINANCE'); ?> >
												</div>
											</div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Div.Head FAST / Releaser 2</label> 
                                                <div class="col-sm-1">User : </div>
                                                <div class="col-sm-4">
                                                        <?php $note_arr = usernote($vw['nobukti'],'DEPT. HEAD FINANCE / DIV. HEAD FAST','FINANCE, ADMINISTRATION & SYSTEM TECHNOLOGY','all'); 
                                                        echo $note_arr["user"]; ?>
                                                </div>
                                               <div class="col-sm-1">Waktu :  </div>
                                                <div class="col-sm-4"><?php	echo $note_arr["tgl"]; ?></div>
                                            </div>
											<div class="form-group">
												<label class="col-sm-2 control-label">Note</label> 
												<div class="col-sm-10">
													<input type="text" class="form-control" id="note_div_head_fast" <?php note($vw['nobukti'],'DEPT. HEAD FINANCE / DIV. HEAD FAST','FINANCE, ADMINISTRATION & SYSTEM TECHNOLOGY','all'); ?> >
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
												<label class="col-sm-2 control-label">Note</label> 
												<div class="col-sm-10">
													<input type="text" class="form-control" id="note_sectionhead" <?php note($vw['nobukti'],'SECTION HEAD','',''); ?> >
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
												<label class="col-sm-2 control-label">Note</label> 
												<div class="col-sm-10">
													<input type="text" class="form-control" id="note_adh" <?php note($vw['nobukti'],'ADH','',''); ?> >
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
												<label class="col-sm-2 control-label">Note</label> 
												<div class="col-sm-10">
													<input type="text" class="form-control" id="note_branch_manager" <?php note($vw['nobukti'],'KEPALA CABANG','',''); ?> >
												</div>
											</div>
											<?php if ($vw['tipe']=='BIAYA') { ?>
											<div class="form-group">
												<label class="col-sm-2 control-label">Note OM</label> 
												<div class="col-sm-10">
													<input type="text" class="form-control" id="note_om" <?php note($vw['nobukti'],'OM','',''); ?>  >
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
												<img src="system/files/<?php echo $vw['upload_file']; ?>" alt="Fountain" class="img-responsive img-thumbnail">
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
												<img src="system/files/<?php echo $vw['upload_fp']; ?>" alt="Fountain" class="img-responsive img-thumbnail">
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
				</form>
			</div>
		<?php } ?>
	</div>
</div>
<div class="modal fade modals" id="getsysMcm" tabindex="-1" role="dialog" aria-labelledby="getsysMcm" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h2 class="modal-title">getsysMcm</h2>
			</div>
			<div class="modal-body" style="padding: 0;" id="datasysMcm"></div>
		</div>
	</div>
</div>
<?php
	function note($nobukti,$level,$div,$dept){
		/*$note = mssql_fetch_array(mssql_query("select ketvalidasi from DataEvoVal where nobukti = '".$nobukti."'and level = '".$level."'
												and isnull(deptterkait,'') = ''"));
		if ($div=='' and $dept=='') {
			$note = mssql_fetch_array(mssql_query("select ketvalidasi as note from DataEvoVal where nobukti = '".$nobukti."' and level = '".$level."'
													and isnull(deptterkait,'') = '' "));
		} else if ($div!='' and $dept!='all') {
			$note = mssql_fetch_array(mssql_query("
				select ketvalidasi as note from DataEvoVal where nobukti = '".$nobukti."' and level = 'DEPT. HEAD FINANCE / DIV. HEAD FAST'
				and isnull(deptterkait,'') = ''
			"));
		} else {
			$note = mssql_fetch_array(mssql_query("
				select ketvalidasi2 as note from DataEvoVal where nobukti = '".$nobukti."' and level = 'DEPT. HEAD FINANCE / DIV. HEAD FAST'
				and isnull(deptterkait,'') = ''
			"));
		}
		echo 'value="'.$note['note'].'" readonly';*/
		$note = mssql_fetch_array(mssql_query("select ketvalidasi as note from DataEvoVal where nobukti = '".$nobukti."' and level = '$level'
					and isnull(deptterkait,'') = '' "));
		echo 'value="'.$note['note'].' " readonly';
	}
	
	
	function usernote($nobukti,$level,$div,$dept){
		/*$lastVal = mssql_fetch_array(mssql_query("select level from DataEvoVal where nobukti = '".$nobukti."' and isnull(validasi,'')='' order by IdVal desc"));
		$user = mssql_fetch_array(mssql_query("select * from sys_user where IdUser = '".$_SESSION['UserID']."'"));
		
		
		if ($_SESSION['level']==$level and $lastVal['level']==$level and $div=='' and $dept=='') {
			$readonly  = ""; 
		} else if ($lastVal['level']=='DEPT. HEAD FINANCE / DIV. HEAD FAST' and $_SESSION['level']=='DEPT. HEAD' and $div=='FINANCE, ADMINISTRATION & SYSTEM TECHNOLOGY' and $dept=='FINANCE') {
			$readonly  = ""; 
		} else if ($lastVal['level']=='DEPT. HEAD FINANCE / DIV. HEAD FAST' and $_SESSION['level']=='DIV. HEAD' and $div=='FINANCE, ADMINISTRATION & SYSTEM TECHNOLOGY' and $dept=='all') {
			$readonly  = ""; 
		} else {
			$readonly  = "readonly"; 
		}
	
		
		if ($div=='' and $dept=='') {
			$note = mssql_fetch_array(mssql_query("select a.ketvalidasi as note,b.namauser,  
													CONVERT(varchar,a.tglvalidasi,105) + ' ' +CONVERT(varchar,a.tglvalidasi,108) tglvalidasi
													from DataEvoVal a
													left join sys_user b on a.uservalidasi = b.IdUser
													where nobukti = '".$nobukti."' and level = '".$level."' 
													and isnull(deptterkait,'') = '' ")); // and isnull(validasi,'')=''
		} else if ($div!='' and $dept!='all') {
			$note = mssql_fetch_array(mssql_query("
				select a.ketvalidasi as note, b.namauser,  
				CONVERT(varchar,a.tglvalidasi,105) + ' ' +CONVERT(varchar,a.tglvalidasi,108) tglvalidasi
				from DataEvoVal a
				left join sys_user b on a.uservalidasi = b.IdUser
				where nobukti = '".$nobukti."' and level = 'DEPT. HEAD FINANCE / DIV. HEAD FAST'
				and isnull(deptterkait,'') = '' ")); //  and isnull(validasi,'')=''
		
		} else {
			$note = mssql_fetch_array(mssql_query("
				select a.ketvalidasi2 as note,b.namauser, 
				CONVERT(varchar,a.tglvalidasi,105) + ' ' +CONVERT(varchar,a.tglvalidasi,108) tglvalidasi
				from DataEvoVal a
				left join sys_user b on a.uservalidasi = b.IdUser
				where nobukti = '".$nobukti."' and level = 'DEPT. HEAD FINANCE / DIV. HEAD FAST'
				and isnull(deptterkait,'') = '' "));  //and isnull(validasi,'')=''
		}*/
		
		$note = mssql_fetch_array(mssql_query("
				select a.ketvalidasi as note,b.namauser, 
				CONVERT(varchar,a.tglvalidasi,105) + ' ' +CONVERT(varchar,a.tglvalidasi,108) tglvalidasi
				from DataEvoVal a
				left join sys_user b on a.uservalidasi = b.IdUser
				where nobukti = '".$nobukti."' and level = '$level'
				and isnull(deptterkait,'') = '' "));  //and isnull(validasi,'')=''
		
		$user = $note['namauser'];
		$tgl = $note['tglvalidasi'];
		$note = array("user"=>$user, "tgl"=>$tgl);
		return $note;
	}
?>