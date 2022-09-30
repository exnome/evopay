<?php require_once ('system/inc/permission.php');  ?>
<?php $akun = mssql_fetch_array(mssql_query("select * from settingAkun where id=1")); ?>
<script type="text/javascript" src="system/myJs/akun.js"></script>
<link rel="stylesheet" type="text/css" href="assets/plugins/flexii/css/flexigrid.pack.css" />
<style type="text/css">
	.input-group-addon {
		padding: 2px 8px;min-width: 0;border-right: 1px;
	}
	.btn-addon {
		padding: 0;min-width: 0;border: 1px solid #d2d3d6;;
	}
	#btn-trf {
		padding: 2px 8px;border: 0px;font-weight: normal;
	}
</style>
<link rel="stylesheet" type="text/css" href="assets/plugins/flexii/css/flexigrid2.css" />
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-gray">
			<div class="panel-heading">
				<h4>User</h4>
			</div>
			<form id="validate-form" action="test.php" method="POST" class="form-horizontal row-border">
				<div class="panel-body collapse in">
					<div class="form-group">
						<label class="col-sm-2 control-label">Akun Hutang (D)</label>
						<div class="col-sm-4">
						    <div class="input-group">
								<input type="text" id="akun_start_hutang" class="form-control" maxlength="8" value="<?php echo $akun['akun_start_hutang'] ?>">
								<span class="input-group-addon">s/d</span>
								<input type="text" id="akun_end_hutang" class="form-control" maxlength="8" value="<?php echo $akun['akun_end_hutang'] ?>">
							</div>
						</div>
						<label class="col-sm-2 control-label">Akun Biaya (D)</label>
						<div class="col-sm-4">
						    <div class="input-group">
								<input type="text" id="akun_start_biaya" class="form-control" maxlength="8" value="<?php echo $akun['akun_start_biaya'] ?>">
								<span class="input-group-addon">s/d</span>
								<input type="text" id="akun_end_biaya" class="form-control" maxlength="8" value="<?php echo $akun['akun_end_biaya'] ?>">
							</div>
						</div>
					</div>
					<div class="form-group">
						<!-- Biaya Masih Harus Dibayar -->
						<label class="col-sm-2 control-label">Akun BMHD (K)</label>
						<div class="col-sm-4">
						    <input type="text" id="akun_biaya" class="form-control" maxlength="8" value="<?php echo $akun['akun_biaya'] ?>">
						</div>
						<!-- <label class="col-sm-2 control-label">Akun Sublet Dalam Proses (K)</label>
						<div class="col-sm-4"> -->
						<input type="hidden" id="akun_sublet" class="form-control" maxlength="8" value="<?php echo $akun['akun_sublet'] ?>">
						<!-- </div> -->
					</div>
					<hr style="margin: 10px 0;">
					<div class="form-group">
						<label class="col-sm-2 control-label">Akun PPN Mobil</label>
						<div class="col-sm-4">
						    <input type="text" id="akun_ppn_mobil" class="form-control" maxlength="8" value="<?php echo $akun['akun_ppn_mobil'] ?>">
						</div>
						<label class="col-sm-2 control-label">Akun PPN Part</label>
						<div class="col-sm-4">
						    <input type="text" id="akun_ppn_part" class="form-control" maxlength="8" value="<?php echo $akun['akun_ppn_part'] ?>">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">Akun PPN Bengkel</label>
						<div class="col-sm-4">
						    <input type="text" id="akun_ppn_bengkel" class="form-control" maxlength="8" value="<?php echo $akun['akun_ppn_bengkel'] ?>">
						</div>
						<label class="col-sm-2 control-label">Akun PPN Aksesoris</label>
						<div class="col-sm-4">
						    <input type="text" id="akun_ppn_aksesoris" class="form-control" maxlength="8" value="<?php echo $akun['akun_ppn_aksesoris'] ?>">
						</div>
						
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">Akun PPN Lain-Lain</label>
						<div class="col-sm-4">
						    <input type="text" id="akun_ppn" class="form-control" maxlength="8" value="<?php echo $akun['akun_ppn'] ?>">
						</div>
						<label class="col-sm-2 control-label">Akun PPN Sewa</label>
						<div class="col-sm-4">
						    <input type="text" id="akun_ppn_sewa" class="form-control" maxlength="8" value="<?php echo $akun['akun_ppn_sewa'] ?>">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">Akun PPN Sublet</label>
						<div class="col-sm-4">
						    <input type="text" id="akun_ppn_sublet" class="form-control" maxlength="8" value="<?php echo $akun['akun_ppn_sublet'] ?>">
						</div>
					</div>
					<hr style="margin: 10px 0;">
					<div class="form-group">
						<label class="col-sm-12 control-label">Pajak Ber NPWP</label>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">Pph 21</label>
						<div class="col-sm-4"> 
							<div class="input-group">
								<span class="input-group-addon">Akun</span>
								<input type="text" id="pph_21" class="form-control" maxlength="8" value="<?php echo $akun['pph_21'] ?>">
								<span class="input-group-addon btn-addon"> 
									<button type="button" id="btn-trf" data-toggle="dropdown" class="btn btn-default-alt dropdown-toggle">
                                        Trf. Pajak <i class="fa fa-caret-down"></i>
                                    </button>
                                    <ul class="dropdown-menu" style="min-width: 85px;">
                                        <li style="text-align: left;">
                                        	<a href="javascript:void(0);" onclick="add('1','Pph 21','PPH21');">Add Tarif</a>
                                        </li>
                                        <?php
											$sql = "select tarif_persen from settingPph where jns_pph = 'Pph 21' and npwp = '1'";
											$rsl = mssql_query($sql);
											while ($dt = mssql_fetch_array($rsl)) {
												echo '<li style="text-align: left;"><a href="#">'.$dt['tarif_persen'].'% </a></li>';
											}
										?>
                                    </ul>
								</span>
							</div>
						</div>
						<label class="col-sm-2 control-label">Pph 22</label>
						<div class="col-sm-4"> 
							<div class="input-group">
								<span class="input-group-addon">Akun</span>
								<input type="text" id="pph_22" class="form-control" maxlength="8" value="<?php echo $akun['pph_22'] ?>">
								<span class="input-group-addon btn-addon"> 
									<button type="button" id="btn-trf" data-toggle="dropdown" class="btn btn-default-alt dropdown-toggle">
                                        Trf. Pajak <i class="fa fa-caret-down"></i>
                                    </button>
                                    <ul class="dropdown-menu" style="min-width: 85px;">
                                        <li style="text-align: left;">
                                        	<a href="javascript:void(0);" onclick="add('1','Pph 22','PPH22');">Add Tarif</a>
                                        </li>
                                        <?php
											$sql = "select tarif_persen from settingPph where jns_pph = 'Pph 22' and npwp = '1'";
											$rsl = mssql_query($sql);
											while ($dt = mssql_fetch_array($rsl)) {
												echo '<li style="text-align: left;"><a href="#">'.$dt['tarif_persen'].'%</a></li>';
											}
										?>
                                    </ul>
								</span>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">Pph 23</label>
						<div class="col-sm-4"> 
							<div class="input-group">
								<span class="input-group-addon">Akun</span>
								<input type="text" id="pph_23" class="form-control" maxlength="8" value="<?php echo $akun['pph_23'] ?>">
								<span class="input-group-addon btn-addon"> 
									<button type="button" id="btn-trf" data-toggle="dropdown" class="btn btn-default-alt dropdown-toggle">
                                        Trf. Pajak <i class="fa fa-caret-down"></i>
                                    </button>
                                    <ul class="dropdown-menu" style="min-width: 85px;">
                                        <li style="text-align: left;">
                                        	<a href="javascript:void(0);" onclick="add('1','Pph 23','PPH23');">Add Tarif</a>
                                        </li>
                                        <?php
											$sql = "select tarif_persen from settingPph where jns_pph = 'Pph 23' and npwp = '1'";
											$rsl = mssql_query($sql);
											while ($dt = mssql_fetch_array($rsl)) {
												echo '<li style="text-align: left;"><a href="#">'.$dt['tarif_persen'].'%</a></li>';
											}
										?>
                                    </ul>
								</span>
							</div>
						</div>
						<label class="col-sm-2 control-label">Pph 25</label>
						<div class="col-sm-4"> 
							<div class="input-group">
								<span class="input-group-addon">Akun</span>
								<input type="text" id="pph_25" class="form-control" maxlength="8" value="<?php echo $akun['pph_25'] ?>">
								<span class="input-group-addon btn-addon"> 
									<button type="button" id="btn-trf" data-toggle="dropdown" class="btn btn-default-alt dropdown-toggle">
                                        Trf. Pajak <i class="fa fa-caret-down"></i>
                                    </button>
                                    <ul class="dropdown-menu" style="min-width: 85px;">
                                        <li style="text-align: left;">
                                        	<a href="javascript:void(0);" onclick="add('1','Pph 25','PPH25');">Add Tarif</a>
                                        </li>
                                        <?php
											$sql = "select tarif_persen from settingPph where jns_pph = 'Pph 25' and npwp = '1'";
											$rsl = mssql_query($sql);
											while ($dt = mssql_fetch_array($rsl)) {
												echo '<li style="text-align: left;"><a href="#">'.$dt['tarif_persen'].'%</a></li>';
											}
										?>
                                    </ul>
								</span>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">Pph 21 Pihak 3</label>
						<div class="col-sm-4"> 
							<div class="input-group">
								<span class="input-group-addon">Akun</span>
								<input type="text" id="pph_21_pihak_3" class="form-control" maxlength="8" value="<?php echo $akun['pph_21_pihak_3'] ?>">
								<span class="input-group-addon btn-addon"> 
									<button type="button" id="btn-trf" data-toggle="dropdown" class="btn btn-default-alt dropdown-toggle">
                                        Trf. Pajak <i class="fa fa-caret-down"></i>
                                    </button>
                                    <ul class="dropdown-menu" style="min-width: 85px;">
                                        <li style="text-align: left;">
                                        	<a href="javascript:void(0);" onclick="add('1','Pph 21 Pihak 3','PPH21P3');">Add Tarif</a>
                                        </li>
                                        <?php
											$sql = "select tarif_persen from settingPph where jns_pph = 'Pph 21 Pihak 3' and npwp = '1'";
											$rsl = mssql_query($sql);
											while ($dt = mssql_fetch_array($rsl)) {
												echo '<li style="text-align: left;"><a href="#">'.$dt['tarif_persen'].'%</a></li>';
											}
										?>
                                    </ul>
								</span>
							</div>
						</div>
						<label class="col-sm-2 control-label">Pph 4 Ayat 2</label>
						<div class="col-sm-4"> 
							<div class="input-group">
								<span class="input-group-addon">Akun</span>
								<input type="text" id="pph_4" class="form-control" maxlength="8" value="<?php echo $akun['pph_4'] ?>">
								<span class="input-group-addon btn-addon"> 
									<button type="button" id="btn-trf" data-toggle="dropdown" class="btn btn-default-alt dropdown-toggle">
                                        Trf. Pajak <i class="fa fa-caret-down"></i>
                                    </button>
                                    <ul class="dropdown-menu" style="min-width: 85px;">
                                        <li style="text-align: left;">
                                        	<a href="javascript:void(0);" onclick="add('1','Pph 4 Ayat 2','PPH4AY2');">Add Tarif</a>
                                        </li>
                                        <?php
											$sql = "select tarif_persen from settingPph where jns_pph = 'Pph 4 Ayat 2' and npwp = '1'";
											$rsl = mssql_query($sql);
											while ($dt = mssql_fetch_array($rsl)) {
												echo '<li style="text-align: left;"><a href="#">'.$dt['tarif_persen'].'%</a></li>';
											}
										?>
                                    </ul>
								</span>
							</div>
						</div>
					</div>
					<hr style="margin: 10px 0;">
					<div class="form-group">
						<label class="col-sm-12 control-label">Pajak Ber Non NPWP</label>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">Pph 21</label>
						<div class="col-sm-4"> 
							<div class="input-group">
								<span class="input-group-addon">Akun</span>
								<input type="text" id="non_pph_21" class="form-control" maxlength="8" value="<?php echo $akun['non_pph_21'] ?>">
								<span class="input-group-addon btn-addon"> 
									<button type="button" id="btn-trf" data-toggle="dropdown" class="btn btn-default-alt dropdown-toggle">
                                        Trf. Pajak <i class="fa fa-caret-down"></i>
                                    </button>
                                    <ul class="dropdown-menu" style="min-width: 85px;">
                                        <li style="text-align: left;">
                                        	<a href="javascript:void(0);" onclick="add('0','Pph 21','PPH21');">Add Tarif</a>
                                        </li>
                                        <?php
											$sql = "select tarif_persen from settingPph where jns_pph = 'Pph 21' and npwp = '0'";
											$rsl = mssql_query($sql);
											while ($dt = mssql_fetch_array($rsl)) {
												echo '<li style="text-align: left;"><a href="#">'.$dt['tarif_persen'].'%</a></li>';
											}
										?>
                                    </ul>
								</span>
							</div>
						</div>
						<label class="col-sm-2 control-label">Pph 22</label>
						<div class="col-sm-4"> 
							<div class="input-group">
								<span class="input-group-addon">Akun</span>
								<input type="text" id="non_pph_22" class="form-control" maxlength="8" value="<?php echo $akun['non_pph_22'] ?>">
								<span class="input-group-addon btn-addon"> 
									<button type="button" id="btn-trf" data-toggle="dropdown" class="btn btn-default-alt dropdown-toggle">
                                        Trf. Pajak <i class="fa fa-caret-down"></i>
                                    </button>
                                    <ul class="dropdown-menu" style="min-width: 85px;">
                                        <li style="text-align: left;">
                                        	<a href="javascript:void(0);" onclick="add('0','Pph 22','PPH22');">Add Tarif</a>
                                        </li>
                                        <?php
											$sql = "select tarif_persen from settingPph where jns_pph = 'Pph 22' and npwp = '0'";
											$rsl = mssql_query($sql);
											while ($dt = mssql_fetch_array($rsl)) {
												echo '<li style="text-align: left;"><a href="#">'.$dt['tarif_persen'].'%</a></li>';
											}
										?>
                                    </ul>
								</span>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">Pph 23</label>
						<div class="col-sm-4"> 
							<div class="input-group">
								<span class="input-group-addon">Akun</span>
								<input type="text" id="non_pph_23" class="form-control" maxlength="8" value="<?php echo $akun['non_pph_23'] ?>">
								<span class="input-group-addon btn-addon"> 
									<button type="button" id="btn-trf" data-toggle="dropdown" class="btn btn-default-alt dropdown-toggle">
                                        Trf. Pajak <i class="fa fa-caret-down"></i>
                                    </button>
                                    <ul class="dropdown-menu" style="min-width: 85px;">
                                        <li style="text-align: left;">
                                        	<a href="javascript:void(0);" onclick="add('0','Pph 23','PPH23');">Add Tarif</a>
                                        </li>
                                        <?php
											$sql = "select tarif_persen from settingPph where jns_pph = 'Pph 23' and npwp = '0'";
											$rsl = mssql_query($sql);
											while ($dt = mssql_fetch_array($rsl)) {
												echo '<li style="text-align: left;"><a href="#">'.$dt['tarif_persen'].'%</a></li>';
											}
										?>
                                    </ul>
								</span>
							</div>
						</div>
						<label class="col-sm-2 control-label">Pph 25</label>
						<div class="col-sm-4"> 
							<div class="input-group">
								<span class="input-group-addon">Akun</span>
								<input type="text" id="non_pph_25" class="form-control" maxlength="8" value="<?php echo $akun['non_pph_25'] ?>">
								<span class="input-group-addon btn-addon"> 
									<button type="button" id="btn-trf" data-toggle="dropdown" class="btn btn-default-alt dropdown-toggle">
                                        Trf. Pajak <i class="fa fa-caret-down"></i>
                                    </button>
                                    <ul class="dropdown-menu" style="min-width: 85px;">
                                        <li style="text-align: left;">
                                        	<a href="javascript:void(0);" onclick="add('0','Pph 25','PPH25');">Add Tarif</a>
                                        </li>
                                        <?php
											$sql = "select tarif_persen from settingPph where jns_pph = 'Pph 25' and npwp = '0'";
											$rsl = mssql_query($sql);
											while ($dt = mssql_fetch_array($rsl)) {
												echo '<li style="text-align: left;"><a href="#">'.$dt['tarif_persen'].'%</a></li>';
											}
										?>
                                    </ul>
								</span>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">Pph 21 Pihak 3</label>
						<div class="col-sm-4"> 
							<div class="input-group">
								<span class="input-group-addon">Akun</span>
								<input type="text" id="non_pph_21_pihak_3" class="form-control" maxlength="8" value="<?php echo $akun['non_pph_21_pihak_3'] ?>">
								<span class="input-group-addon btn-addon"> 
									<button type="button" id="btn-trf" data-toggle="dropdown" class="btn btn-default-alt dropdown-toggle">
                                        Trf. Pajak <i class="fa fa-caret-down"></i>
                                    </button>
                                    <ul class="dropdown-menu" style="min-width: 85px;">
                                        <li style="text-align: left;">
                                        	<a href="javascript:void(0);" onclick="add('0','Pph 21 Pihak 3','PPH21P3');">Add Tarif</a>
                                        </li>
                                        <?php
											$sql = "select tarif_persen from settingPph where jns_pph = 'Pph 21 Pihak 3' and npwp = '0'";
											$rsl = mssql_query($sql);
											while ($dt = mssql_fetch_array($rsl)) {
												echo '<li style="text-align: left;"><a href="#">'.$dt['tarif_persen'].'%</a></li>';
											}
										?>
                                    </ul>
								</span>
							</div>
						</div>
						<label class="col-sm-2 control-label">Pph 4 Ayat 2</label>
						<div class="col-sm-4"> 
							<div class="input-group">
								<span class="input-group-addon">Akun</span>
								<input type="text" id="non_pph_4" class="form-control" maxlength="8" value="<?php echo $akun['non_pph_4'] ?>">
								<span class="input-group-addon btn-addon"> 
									<button type="button" id="btn-trf" data-toggle="dropdown" class="btn btn-default-alt dropdown-toggle">
                                        Trf. Pajak <i class="fa fa-caret-down"></i>
                                    </button>
                                    <ul class="dropdown-menu" style="min-width: 85px;">
                                        <li style="text-align: left;">
                                        	<a href="javascript:void(0);" onclick="add('0','Pph 4 Ayat 2','PPH4AY2');">Add Tarif</a>
                                        </li>
                                        <?php
											$sql = "select tarif_persen from settingPph where jns_pph = 'Pph 4 Ayat 2' and npwp = '0'";
											$rsl = mssql_query($sql);
											while ($dt = mssql_fetch_array($rsl)) {
												echo '<li style="text-align: left;"><a href="#">'.$dt['tarif_persen'].'%</a></li>';
											}
										?>
                                    </ul>
								</span>
							</div>
						</div>
					</div>
					<hr style="margin: 10px 0;">
					<div class="form-group">
						<label class="col-sm-2 control-label">Plafon</label>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">Petty Cash</label>
						<div class="col-sm-4">
						    <input type="text" id="pety_cash" class="form-control"  value="<?php echo $akun['pety_cash'] ?>">
						</div>
						<label class="col-sm-2 control-label">Melewatkan Direksi</label>
						<div class="col-sm-4">
						    <input type="text" id="skip_direksi" class="form-control"  value="<?php echo $akun['skip_direksi'] ?>">
						</div>
					</div>
                    <div class="form-group">
						<label class="col-sm-2 control-label"></label>
						<div class="col-sm-4">
						</div>
                        <label class="col-sm-2 control-label">Mandatory 2 Direksi</label>
						<div class="col-sm-4">
						    <input type="text" id="skip_direksi2" class="form-control"  value="<?php echo $akun['skip_direksi2'] ?>">
						</div>
					</div>
				</div>
				<div class="panel-footer">
					<div class="row">
					    <div class="col-sm-10 col-sm-offset-2">
					        <div class="btn-toolbar">
					            <button type="button" id="new" class="btn-primary btn">Save Changes</button>
					            <button type="button" class="btn-default btn" onclick="showList();">List Data Tarif</button>
					        </div>
					    </div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<div class="modal fade modals" id="dataTarif" tabindex="-1" role="dialog" aria-labelledby="dataTarif" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h2 class="modal-title">Data Tarif</h2>
			</div>
			<div class="modal-body" style="padding: 0;">
				<table class="flexme4" style="display: none"></table>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function($) {
		$(".flexme4").flexigrid({
			dataType : 'xml',
		    colModel : [ 
		        {
			        display : '#',
			        name : 'idPph',
			        width : 40,
			        sortable : false,
			        align : 'center'
		        }, {
		            display : 'Jns NPWP',
		            name : 'npwp',
		            width : 100,
		            sortable : false,
		            align : 'left'
		        }, {
		            display : 'Jns Pph',
		            name : 'jns_pph',
		            width : 150,
		            sortable : false,
		            align : 'left'
		        }, {
		            display : 'Kode Akun',
		            name : '',
		            width : 150,
		            sortable : false,
		            align : 'left'
		        }, {
		            display : 'Tarif',
		            name : 'tarif_persen',
		            width : 100,
		            sortable : false,
		            align : 'left'
		        }
		    ],
			buttons : [ 
			    {
			        name : 'Delete',
			        bclass : 'delete',
			        onpress : button
			    }
			],
		    sortname : "idPph",
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
		$('.flexme4').flexOptions({
			url : 'system/control/akun.php',
			newp: 1,
			// params:[]
		}).flexReload();

		function button(com) {
		    if (com == 'Delete') {
				var generallen = $("input[name='id[]']:checked").length;
				if (generallen==0 || generallen>1) {
				    onload = myBad('Akun');
				    return false;
				} else {
				    var idPph = $("input[name='id[]']:checked").val();
					$.ajax({ 
					    url: 'system/control/akun.php',
					    data: {action:'delete', 'idPph': idPph},
					    type: 'post',
					    beforeSend: function(){
							onload = showLoading();
						},
					    success: function(output) {
					        onload = hideLoading();
					        bootbox.dialog({
					        	closeButton : false,
							    message: output,
							    className : "resize",
							    title: "Akun",
							    buttons: {
							        main: {
							            label: "Ok",
							            className: "btn-sm btn-primary",
							            callback: function() {
							                $('.flexme4').flexReload();
							            }
							        }
							    }
							});
						}
					});
				}
			}
		}
	});

	function showList(){
		$('#dataTarif').modal('show');
	}
</script>