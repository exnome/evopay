<?php require_once ('system/inc/permission.php');  ?>
<?php require_once ('system/view/settingakun.php');  ?>
<?php $kodeGl1 = mysqli_fetch_array(mysqli_query($conn,"SELECT kodeGl FROM settingakun WHERE idSetAkun='13'"));  ?>
<?php $kodeGl2 = mysqli_fetch_array(mysqli_query($conn,"SELECT kodeGl FROM settingakun WHERE idSetAkun='14'"));  ?>
<?php $kodeGl3 = mysqli_fetch_array(mysqli_query($conn,"SELECT kodeGl FROM settingakun WHERE idSetAkun='15'"));  ?>
<?php $kodeGl4 = mysqli_fetch_array(mysqli_query($conn,"SELECT kodeGl FROM settingakun WHERE idSetAkun='16'"));  ?>
<link rel="stylesheet" type="text/css" href="assets/flexii/css/flexigrid.pack.css" />
<style type="text/css">
	.flexigrid {
		font-family: Arial, Helvetica, sans-serif;
	    font-size: 12px;
	    position: relative;
	    border: 1px solid #d8e2e7;
	    overflow: hidden;
	    color: #000;
	    border-top: 0px solid #b1c2c6;
	}
</style>
<div class="container-fluid">
	<div class="row">
		<div class="col-xl-12">
			<section class="card card-wann0 mb-3">
				<form action="utility-adjustment-modify" id="Form" method="POST">
				    <span id="post"></span> 
					<table class="flexme1" style="display: none"></table>
					<script type="text/javascript">
					    jQuery(document).ready(function($) {
							$(".flexme1").flexigrid({
							    url : 'system/data/adjustment.php',
							    dataType : 'xml',
							    colModel : [ 
							        {
								        display : '#',
								        name : 'idAdjust',
								        width : 40,
								        sortable : true,
								        align : 'center'
							        }, {
							            display : 'No Bukti',
							            name : 'noAdjust',
							            width : 120,
							            sortable : true,
							            align : 'left'
							        }, {
							            display : 'Tanggal',
							            name : 'tglAdjust',
							            width : 80,
							            sortable : true,
							            align : 'left'
							        }, {
							            display : 'Tipe Produk',
							            name : 'tipeProduk',
							            width : 100,
							            sortable : true,
							            align : 'left'
							        }, {
							            display : 'Tahun',
							            name : 'periodThn',
							            width : 80,
							            sortable : true,
							            align : 'left'
							        }, {
							            display : 'Bulan',
							            name : 'periodBln',
							            width : 80,
							            sortable : true,
							            align : 'left'
							        }, {
							            display : 'Tipe Adjust',
							            name : 'tipeAdjust',
							            width : 100,
							            sortable : true,
							            align : 'left'
							        }, {
							            display : 'Kode Item',
							            name : 'kodeProduk',
							            width : 100,
							            sortable : true,
							            align : 'left'
							        }, {
							            display : 'Qty',
							            name : 'qtyAdjust',
							            width : 100,
							            sortable : true,
							            align : 'left'
							        }, {
							            display : 'HPP',
							            name : 'hppAdjust',
							            width : 100,
							            sortable : true,
							            align : 'left'
							        }, {
							            display : 'Keterangan',
							            name : 'ketAdjust',
							            width : 300,
							            sortable : true,
							            align : 'left'
							        }
							    ],
								buttons : [ 
									{
										name : 'Baru',
										bclass : 'add',
										onpress : button
									}
								],
							    searchitems : [ 
									{ display : 'No Bukti', name : 'noAdjust' },
									{ display : 'Kode Item', name : 'kodeProduk' }
								],
								title : "Adjustment",
							    sortname : "tglAdjust",
							    sortorder : "desc",
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
							function sortAlpha(com) { 
								jQuery('.flexme3').flexOptions({
									newp:1, 
									params:[{name:'letter_pressed', value: com},{name:'qtype',value:$('select[name=qtype]').val()}]
								});
								jQuery(".flexme3").flexReload(); 
							}
							function button(com) {
							    if (com == 'Baru') {
							        var kodeGl1 = $("#kodeGl1").val();
									var kodeGl2 = $("#kodeGl2").val();
									var kodeGl3 = $("#kodeGl3").val();
									var kodeGl4 = $("#kodeGl4").val();
									if (kodeGl1=='') {
										onload = myBad('Akun Selisih Material masih kosong!');
									} else if (kodeGl2=='') {
										onload = myBad('Akun Material masih kosong!');
									} else if (kodeGl3=='') {
										onload = myBad('Akun Selisih FG masih kosong!');
									} else if (kodeGl4=='') {
										onload = myBad('Akun FG masih kosong!');
									} else if (cekAkun(kodeGl1)=='0') {
										onload = myBad('Akun Selisih Material belum tersimpan!');
									} else if (cekAkun(kodeGl2)=='0') {
										onload = myBad('Akun Material belum tersimpan!');
									} else if (cekAkun(kodeGl3)=='0') {
										onload = myBad('Akun Selisih FG belum tersimpan!');
									} else if (cekAkun(kodeGl4)=='0') {
										onload = myBad('Akun FG belum tersimpan!');
									} else {
										$("#post").html('<input type="hidden" name="new" value="">');
							        	document.forms['Form'].submit();
									}
							    } else if (com == 'Detail') {
									var id = $("input[name='id[]']:checked").val();
									var count = $("input[name='id[]']:checked").length;
									if (count==0 || count>1) {
									    onload = myBad('');
									    return false;
									} else {
									    $("#post").html('<input type="hidden" name="detail" value="">');
									    document.forms['Form'].submit();
									}
							    }
							}
						});
					</script>
				</form>
			</section>
		</div>
	</div>
</div>
<div class="container-fluid">
	<div class="row">
    	<div class="col-xl-12">
			<section class="card card-wann0 mb-0">
				<header class="card-header">Setting Akun</header>
				<div class="card-block">
					<form id="example-form" action="#" class="form-wizard">
						<input class="form-control" id="IdUser" type="hidden" value="<?php echo $_SESSION['IdUser']; ?>">
						<div class="box-typical-body">
							<div class="row">
								<div class="col-lg-12">
									<div class="form-group row">
										<div class="col-sm-4">
											<label class="form-control-label">Akun Selisih Material</label>
											<div class="input-group">
												<input type="hidden" id="idSetAkun1" name="idSetAkun[]" value="13">
												<input class="form-control" id="kodeGl1" name="kodeGl1" type="text" value="<?php echo $kodeGl1['kodeGl']; ?>" readonly>
												<div class="input-group-addon">
													<button type="button" onclick="setAkun('kodeGl1');" style="padding: 4px 8px;border: 0;background: #f6f8fa;" disabled>
														<i class="glyphicon glyphicon-search"></i>
													</button>
												</div>
											</div>
										</div>
										<div class="col-sm-4">
											<label class="form-control-label">Akun Material</label>
											<div class="input-group">
												<input type="hidden" id="idSetAkun2" name="idSetAkun[]" value="14">
												<input class="form-control" id="kodeGl2" name="kodeGl2" type="text" value="<?php echo $kodeGl2['kodeGl']; ?>" readonly>
												<div class="input-group-addon">
													<button type="button" onclick="setAkun('kodeGl2');" style="padding: 4px 8px;border: 0;background: #f6f8fa;" disabled>
														<i class="glyphicon glyphicon-search"></i>
													</button>
												</div>
											</div>
										</div>
									</div>
									<div class="form-group row">
										<div class="col-sm-4">
											<label class="form-control-label">Akun Selisih FG</label>
											<div class="input-group">
												<input type="hidden" id="idSetAkun3" name="idSetAkun[]" value="15">
												<input class="form-control" id="kodeGl3" name="kodeGl3" type="text" value="<?php echo $kodeGl3['kodeGl']; ?>" readonly>
												<div class="input-group-addon">
													<button type="button" onclick="setAkun('kodeGl3');" style="padding: 4px 8px;border: 0;background: #f6f8fa;" disabled>
														<i class="glyphicon glyphicon-search"></i>
													</button>
												</div>
											</div>
										</div>
										<div class="col-sm-4">
											<label class="form-control-label">Akun FG</label>
											<div class="input-group">
												<input type="hidden" id="idSetAkun4" name="idSetAkun[]" value="16">
												<input class="form-control" id="kodeGl4" name="kodeGl4" type="text" value="<?php echo $kodeGl4['kodeGl']; ?>" readonly>
												<div class="input-group-addon">
													<button type="button" onclick="setAkun('kodeGl4');" style="padding: 4px 8px;border: 0;background: #f6f8fa;" disabled>
														<i class="glyphicon glyphicon-search"></i>
													</button>
												</div>
											</div>
										</div>
										<div class="col-sm-3">
											<label class="form-control-label">&nbsp;</label>
											<button type="button" id="saveAkun" class="btn-primary btn btn-sm" disabled>Save Changes</button>
										</div>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
			</section>
		</div>
	</div>
</div>