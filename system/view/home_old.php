<style type="text/css">
	.info-tiles { cursor: pointer; }
	.info-tiles .tiles-heading { text-transform: none; }
	.info-tiles .tiles-footer::after { margin-right: 10px; border-right: 5px solid #fff; border-bottom: 5px solid #fff; }
	.info-tiles .tiles-body { font-size: 25px; }
	.input-group-addon { background-color: #4f8edc; border: 1px solid #4f8edc;padding: 0;min-width: 0; }
	.input-group-addon button {padding: 2px 10px;border: 0;background: #4f8edc;color: #fff;}
	@media (min-width: 768px){.col-md-3{width: 25%;}}
	@media (min-width: 992px){.col-md-3{width: 20%;}}
	@media (min-width: 1200px){.col-md-3{width: 16,67%;}}
	.btn-val { padding: 3px 10px;width: 100%; }
</style>
<script type="text/javascript" src="system/myJs/home.js"></script>
<link rel="stylesheet" type="text/css" href="assets/plugins/flexii/css/flexigrid.pack.css" />
<div class="row">
	<div class="col-md-12">
		<div class="panel">
			<div class="panel-heading">
				<h4>Approval Progress Monitoring</h4>
			</div>
			<div class="panel-body" style="padding-bottom: 0;">
				<?php if ($_SESSION['kodedealer']!='2010') { ?>
					<div class="row">
						<div class="col-md-3">
							<div class="info-tiles tiles-brown" id="wait_1">
							    <div class="tiles-heading">
							        <div class="pull-center">Waiting Checker</div>
							    </div>
							    <div class="tiles-body">
							        <div class="pull-left"><i class="fa fa-eye"></i></div>
							        <div class="pull-right" id="tot_wait_1">0</div>
							    </div>
								<div class="tiles-footer" id="capt_wait_1">SELECTED</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="info-tiles tiles-danger" id="wait_2">
							    <div class="tiles-heading">
							        <div class="pull-center">Wait Approval Section Head</div>
							    </div>
							    <div class="tiles-body">
							        <div class="pull-left"><i class="fa fa-eye"></i></div>
							        <div class="pull-right" id="tot_wait_2">0</div>
							    </div>
								<div class="tiles-footer" id="capt_wait_2">&nbsp;</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="info-tiles tiles-orange" id="wait_3">
							    <div class="tiles-heading">
							        <div class="pull-center">Waiting Approval ADH</div>
							    </div>
							    <div class="tiles-body">
							        <div class="pull-left"><i class="fa fa-eye"></i></div>
							        <div class="pull-right" id="tot_wait_3">0</div>
							    </div>
								<div class="tiles-footer" id="capt_wait_3">&nbsp;</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="info-tiles tiles-warning" id="wait_4">
							    <div class="tiles-heading">
							        <div class="pull-center">Waiting Approval Kacab</div>
							    </div>
							    <div class="tiles-body">
							        <div class="pull-left"><i class="fa fa-eye"></i></div>
							        <div class="pull-right" id="tot_wait_4">0</div>
							    </div>
								<div class="tiles-footer" id="capt_wait_4">&nbsp;</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="info-tiles tiles-success" id="wait_5">
							    <div class="tiles-heading">
							        <div class="pull-center">Ready For Settlement</div>
							    </div>
							    <div class="tiles-body">
							        <div class="pull-left"><i class="fa fa-eye"></i></div>
							        <div class="pull-right" id="tot_wait_5">0</div>
							    </div>
								<div class="tiles-footer" id="capt_wait_5">&nbsp;</div>
							</div>
						</div>
					</div>
				<?php } else { ?>
					<div class="row">
						<div class="col-md-2">
							<div class="info-tiles tiles-midnightblue" id="wait_6">
							    <div class="tiles-heading">
							        <div class="pull-center">Waiting Checker <br/>Tax</div>
							    </div>
							    <div class="tiles-body">
							        <div class="pull-left"><i class="fa fa-eye"></i></div>
							        <div class="pull-right" id="tot_wait_6">0</div>
							    </div>
								<div class="tiles-footer" id="capt_wait_6">&nbsp;</div>
							</div>
						</div>
						<div class="col-md-2">
							<div class="info-tiles tiles-midnightblue" id="wait_7">
							    <div class="tiles-heading">
							        <div class="pull-center">Waiting Approval <br/>Section Head</div>
							    </div>
							    <div class="tiles-body">
							        <div class="pull-left"><i class="fa fa-eye"></i></div>
							        <div class="pull-right" id="tot_wait_7">0</div>
							    </div>
								<div class="tiles-footer" id="capt_wait_7">&nbsp;</div>
							</div>
						</div>
						<div class="col-md-2">
							<div class="info-tiles tiles-midnightblue" id="wait_8">
							    <div class="tiles-heading">
							        <div class="pull-center">Waiting Approval <br/>Dept. Head</div>
							    </div>
							    <div class="tiles-body">
							        <div class="pull-left"><i class="fa fa-eye"></i></div>
							        <div class="pull-right" id="tot_wait_8">0</div>
							    </div>
								<div class="tiles-footer" id="capt_wait_8">&nbsp;</div>
							</div>
						</div>
						<div class="col-md-2">
							<div class="info-tiles tiles-midnightblue" id="wait_9">
							    <div class="tiles-heading">
							        <div class="pull-center">Waiting Approval <br/>Direksi</div>
							    </div>
							    <div class="tiles-body">
							        <div class="pull-left"><i class="fa fa-eye"></i></div>
							        <div class="pull-right" id="tot_wait_9">0</div>
							    </div>
								<div class="tiles-footer" id="capt_wait_9">&nbsp;</div>
							</div>
						</div>
						<div class="col-md-2">
							<div class="info-tiles tiles-midnightblue" id="wait_10">
							    <div class="tiles-heading">
							        <div class="pull-center">Waiting <br/>Checker Finance</div>
							    </div>
							    <div class="tiles-body">
							        <div class="pull-left"><i class="fa fa-eye"></i></div>
							        <div class="pull-right" id="tot_wait_10">0</div>
							    </div>
								<div class="tiles-footer" id="capt_wait_10">&nbsp;</div>
							</div>
						</div>
						<div class="col-md-2">
							<div class="info-tiles tiles-midnightblue" id="wait_11">
							    <div class="tiles-heading">
							        <div class="pull-center">Waiting Approval <br/>Div Head Fast & Fin</div>
							    </div>
							    <div class="tiles-body">
							        <div class="pull-left"><i class="fa fa-eye"></i></div>
							        <div class="pull-right" id="tot_wait_11">0</div>
							    </div>
								<div class="tiles-footer" id="capt_wait_11">&nbsp;</div>
							</div>
						</div>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
	<div class="col-md-12">
		<div class="panel">
			<div class="panel-heading">
				<h4>List of Voucher Payment <span id="list_title">Request Waiting on Checker</span></h4>
			</div>
			<div class="panel-body">
				<form action="" id="Form" method="POST">
					<span id="post"></span>
					<div class="row">
						<div class="col-sm-4">
						    <div class="input-group">
								<input type="text" id="searchText" class="form-control">
								<input type="text" id="srcLevel" class="form-control" value="<?php echo $_SESSION['level']; ?>">
								<input type="text" id="sesi_level" class="form-control" value="<?php echo $_SESSION['level']; ?>">
								<span class="input-group-addon">
								  <button type="button" id="search">
									<i class="fa fa-search"></i>
								  </button>
								</span>
							</div>
						</div>
						<div class="col-sm-2" id="btn-val">
							<!-- <?php if ($_SESSION['level']=='ACCOUNTING' or $_SESSION['level']=='TAX') { ?>
								<button type="button" onclick="btnval('val');" class="btn-primary btn btn-val" >Validasi</button>
							<?php } else { ?>
								<button type="button" onclick="btnval('view');" class="btn-primary btn btn-val" >View Progress</button>
							<?php } ?> -->
						</div>
						<div class="col-md-12">&nbsp;</div>
						<div class="col-md-12">
							<table class="flexme3" style="display: none"></table>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>