<?php require_once ('system/inc/permission.php');  ?>
<?php require_once ('system/view/settingakun.php');  ?>
<?php $kodeGl1 = mysqli_fetch_array(mysqli_query($conn,"SELECT kodeGl FROM settingakun WHERE idSetAkun='27'"));  ?>
<?php $kodeGl2 = mysqli_fetch_array(mysqli_query($conn,"SELECT kodeGl FROM settingakun WHERE idSetAkun='28'"));  ?>
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
				<form action="suratjalan-suratjalandirect-modify" id="Form" method="POST">
				    <span id="post"></span> 
					<table class="flexme1" style="display: none"></table>
					<script type="text/javascript">
					    jQuery(document).ready(function($) {
							$(".flexme1").flexigrid({
							    url : 'system/data/suratjalandirect.php',
							    dataType : 'xml',
							    colModel : [ 
							        {
								        display : '#',
								        name : 'nomor_dsj',
								        width : 40,
								        sortable : true,
								        align : 'center'
							        },{
							            display : 'Kode Cust',
							            name : 'namaPanggilan',
							            width : 100,
							            sortable : true,
							            align : 'left'
							        }, {
							            display : 'Nama Cust',
							            name : 'nama_perusahaan',
							            width : 250,
							            sortable : true,
							            align : 'left'
							        }, {
							            display : 'No DSJ',
							            name : 'nomor_dsj',
							            width : 120,
							            sortable : true,
							            align : 'left'
							        }, {
							            display : 'Tgl DSJ',
							            name : 'tglDsj',
							            width : 80,
							            sortable : true,
							            align : 'left'
							        }, {
							            display : 'No DSO',
							            name : 'nomor_dso',
							            width : 120,
							            sortable : true,
							            align : 'left'
							        }, {
							            display : 'Tgl DSO',
							            name : 'tgl_dso',
							            width : 80,
							            sortable : true,
							            align : 'left'
							        }, {
							            display : 'No PO',
							            name : 'nomor_po',
							            width : 120,
							            sortable : true,
							            align : 'left'
							        }, {
							            display : 'Tgl PO',
							            name : 'tglPO',
							            width : 80,
							            sortable : true,
							            align : 'left'
							        }, {
							            display : 'DN Tipe',
							            name : 'tipeDN',
							            width : 80,
							            sortable : true,
							            align : 'left',
							            hide : true
							        }, {
							            display : 'No DN',
							            name : 'nomor_dn',
							            width : 120,
							            sortable : true,
							            align : 'left'
							        }, {
							            display : 'Tgl Terima',
							            name : 'tglTerima',
							            width : 100,
							            sortable : true,
							            align : 'left'
							        },  {
							            display : 'No Polisi',
							            name : 'nopolisi',
							            width : 100,
							            sortable : true,
							            align : 'left'
							        }, {
							            display : 'Penerima',
							            name : 'penerima',
							            width : 100,
							            sortable : true,
							            align : 'left'
							        }, {
							            display : 'No Inv',
							            name : 'noinv',
							            width : 100,
							            sortable : true,
							            align : 'left'
							        }
							    ],
								buttons : [ 
									{
										name : 'Baru',
										bclass : 'add',
										onpress : button
									},{
										name : 'Detail',
										bclass : 'edit',
										onpress : button
									},{
										name : 'Cetak SJ',
										bclass : 'pdf',
										onpress : button
									},{
										name : 'Gabung SJ',
										bclass : 'excel',
										onpress : button
									}
								],
							    searchitems : [ 
									{ display : 'No DSJ', name : 'nomor_dsj' },
									{ display : 'No SO', name : 'nomor_so' },
									{ display : 'No DN', name : 'nomor_dn' },
									{ display : 'No PO', name : 'nomor_po' },
									{ display : 'Kode Cust', name : 'namaPanggilan' }
								],
								title : "Direct Surat Jalan",
							    sortname : "tglTrans",
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
							function sortAlpha(com) { 
								jQuery('.flexme3').flexOptions({newp:1, params:[{name:'letter_pressed', value: com},{name:'qtype',value:$('select[name=qtype]').val()}]});
								jQuery(".flexme3").flexReload(); 
							}
							function button(com) {
							    if (com == 'Baru') {
							    	$("input[name='id[]']:checked").prop('checked',false);
							        $("#post").html('<input type="hidden" name="modify" value="">');
							        document.forms['Form'].submit();
							    } else if (com == 'Detail') {
									var generallen = $("input[name='id[]']:checked").length;
									if (generallen==0 || generallen>1) {
									    onload = myBad('');
									    return false;
									} else {
									    $("#post").html('<input type="hidden" name="detail" value="">');
									    document.forms['Form'].submit();
									}
							    } else if (com == 'Cetak SJ') {
							    	var id = $("input[name='id[]']:checked").val();
									var count = $("input[name='id[]']:checked").length;
									if (count==0 || count>1) {
									    onload = myBad('');
									    return false;
									} else {
									    window.open('print/suratjalandirect/'+id+'/');
									}
							    } else if (com == 'Gabung SJ') {
							    	$("#post").html('<input type="hidden" name="gabungan" value="">');
									document.forms['Form'].submit();
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
											<label class="form-control-label">Akun BPP dimuka</label>
											<div class="input-group">
												<input type="hidden" id="idSetAkun1" name="idSetAkun[]" value="27">
												<input class="form-control" id="kodeGl1" name="kodeGl1" type="text" value="<?php echo $kodeGl1['kodeGl']; ?>" readonly>
												<div class="input-group-addon">
													<button type="button" onclick="setAkun('kodeGl1');" style="padding: 4px 8px;border: 0;background: #f6f8fa;" disabled>
														<i class="glyphicon glyphicon-search"></i>
													</button>
												</div>
											</div>
										</div>
										<div class="col-sm-4">
											<label class="form-control-label">Akun Persediaan F/G</label>
											<div class="input-group">
												<input type="hidden" id="idSetAkun2" name="idSetAkun[]" value="28">
												<input class="form-control" id="kodeGl2" name="kodeGl2" type="text" value="<?php echo $kodeGl2['kodeGl']; ?>" readonly>
												<div class="input-group-addon">
													<button type="button" onclick="setAkun('kodeGl2');" style="padding: 4px 8px;border: 0;background: #f6f8fa;" disabled>
														<i class="glyphicon glyphicon-search"></i>
													</button>
												</div>
											</div>
										</div>
										<div class="col-sm-4">
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