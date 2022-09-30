<style type="text/css">
	.info-tiles { cursor: pointer; }
	.info-tiles .tiles-heading { text-transform: none; padding: 2px 10px; }
	.info-tiles .tiles-footer::after { margin-right: 10px; border-right: 5px solid #fff; border-bottom: 5px solid #fff;}
	.info-tiles .tiles-footer { padding: 0px 10px; }
	.info-tiles .tiles-body { font-size: 25px;  padding: 2px 10px; }
	.input-group-addon { background-color: #4f8edc; border: 1px solid #4f8edc;padding: 0;min-width: 0; }
	.input-group-addon button {padding: 2px 10px;border: 0;background: #4f8edc;color: #fff;}
</style>
<script type="text/javascript" src="system/myJs/dashboard.js"></script>
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
							<div class="info-tiles tiles-magenta" id="wait_1">
							    <div class="tiles-heading">
							        <div class="pull-center">Waiting Kepala Bengkel</div>
							    </div>
							    <div class="tiles-body">
							        <div class="pull-left"><i class="fa fa-eye"></i></div>
							        <div class="pull-right" id="tot_wait_1">0</div>
							    </div>
								<div class="tiles-footer">&nbsp;</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="info-tiles tiles-midnightblue" id="wait_2">
							    <div class="tiles-heading">
							        <div class="pull-center">Waiting SPV</div>
							    </div>
							    <div class="tiles-body">
							        <div class="pull-left"><i class="fa fa-eye"></i></div>
							        <div class="pull-right" id="tot_wait_2">0</div>
							    </div>
								<div class="tiles-footer">&nbsp;</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="info-tiles tiles-sky" id="wait_3">
							    <div class="tiles-heading">
							        <div class="pull-center">Waiting ADH</div>
							    </div>
							    <div class="tiles-body">
							        <div class="pull-left"><i class="fa fa-eye"></i></div>
							        <div class="pull-right" id="tot_wait_3">0</div>
							    </div>
								<div class="tiles-footer">&nbsp;</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="info-tiles tiles-orange" id="wait_4">
							    <div class="tiles-heading">
							        <div class="pull-center">Waiting Kepala Cabang</div>
							    </div>
							    <div class="tiles-body">
							        <div class="pull-left"><i class="fa fa-eye"></i></div>
							        <div class="pull-right" id="tot_wait_4">0</div>
							    </div>
								<div class="tiles-footer">&nbsp;</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-3">
							<div class="info-tiles tiles-primary" id="wait_5">
							    <div class="tiles-heading">
							        <div class="pull-center">Waiting Checker</div>
							    </div>
							    <div class="tiles-body">
							        <div class="pull-left"><i class="fa fa-eye"></i></div>
							        <div class="pull-right" id="tot_wait_5">0</div>
							    </div>
								<div class="tiles-footer">&nbsp;</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="info-tiles tiles-green" id="wait_6">
							    <div class="tiles-heading">
							        <div class="pull-center">Waiting Finance</div>
							    </div>
							    <div class="tiles-body">
							        <div class="pull-left"><i class="fa fa-eye"></i></div>
							        <div class="pull-right" id="tot_wait_6">0</div>
							    </div>
								<div class="tiles-footer">&nbsp;</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="info-tiles tiles-danger" id="wait_7">
							    <div class="tiles-heading">
							        <div class="pull-center">Request On Progress</div>
							    </div>
							    <div class="tiles-body">
							        <div class="pull-left"><i class="fa fa-eye"></i></div>
							        <div class="pull-right" id="tot_wait_7">0</div>
							    </div>
								<div class="tiles-footer">&nbsp;</div>
							</div>
						</div>
					</div>
				<?php } else { ?>
					<div class="row">
						<div class="col-md-2">
							<div class="info-tiles tiles-primary" id="wait_8">
							    <div class="tiles-heading">
							        <div class="pull-center">Waiting Checker <br/>Tax</div>
							    </div>
							    <div class="tiles-body">
							        <div class="pull-left"><i class="fa fa-eye"></i></div>
							        <div class="pull-right" id="tot_wait_8">0</div>
							    </div>
								<div class="tiles-footer">&nbsp;</div>
							</div>
						</div>
                        <div class="col-md-2">
							<div class="info-tiles tiles-primary" id="wait_acc">
							    <div class="tiles-heading">
							        <div class="pull-center">Waiting Approval<br/>Accounting</div>
							    </div>
							    <div class="tiles-body">
							        <div class="pull-left"><i class="fa fa-eye"></i></div>
							        <div class="pull-right" id="tot_wait_acc">0</div>
							    </div>
								<div class="tiles-footer">&nbsp;</div>
							</div>
						</div>
						<div class="col-md-2">
							<div class="info-tiles tiles-primary" id="wait_9">
							    <div class="tiles-heading">
							        <div class="pull-center">Waiting Approval <br> Section Head</div>
							    </div>
							    <div class="tiles-body">
							        <div class="pull-left"><i class="fa fa-eye"></i></div>
							        <div class="pull-right" id="tot_wait_9">0</div>
							    </div>
								<div class="tiles-footer">&nbsp;</div>
							</div>
						</div>
						<div class="col-md-2">
							<div class="info-tiles tiles-primary" id="wait_10">
							    <div class="tiles-heading">
							        <div class="pull-center">Waiting Approval <br/>Dept. Head</div>
							    </div>
							    <div class="tiles-body">
							        <div class="pull-left"><i class="fa fa-eye"></i></div>
							        <div class="pull-right" id="tot_wait_10">0</div>
							    </div>
								<div class="tiles-footer">&nbsp;</div>
							</div>
						</div>
						<div class="col-md-2">
							<div class="info-tiles tiles-primary" id="wait_11">
							    <div class="tiles-heading">
							        <div class="pull-center">Waiting Approval <br/>Div. Head</div>
							    </div>
							    <div class="tiles-body">
							        <div class="pull-left"><i class="fa fa-eye"></i></div>
							        <div class="pull-right" id="tot_wait_11">0</div>
							    </div>
								<div class="tiles-footer">&nbsp;</div>
							</div>
						</div>
                         <div class="col-md-2">
                            <div class="info-tiles tiles-primary" id="wait_12">
							    <div class="tiles-heading">
							        <div class="pull-center">Waiting Approval <br>Direksi 1</div>
							    </div>
							    <div class="tiles-body">
							        <div class="pull-left"><i class="fa fa-eye"></i></div>
							        <div class="pull-right" id="tot_wait_12">0</div>
							    </div>
								<div class="tiles-footer">&nbsp;</div>
							</div>
						</div>
					</div>
					<div class="row">
                    	
                        <div class="col-md-2">
                            <div class="info-tiles tiles-primary" id="wait_direksi2">
							    <div class="tiles-heading">
							        <div class="pull-center">Waiting Approval <br>Direksi 2</div>
							    </div>
							    <div class="tiles-body">
							        <div class="pull-left"><i class="fa fa-eye"></i></div>
							        <div class="pull-right" id="tot_wait_direksi2">0</div>
							    </div>
								<div class="tiles-footer">&nbsp;</div>
							</div>
						</div>
                        <div class="col-md-2">
							<div class="info-tiles tiles-primary" id="wait_13">
							    <div class="tiles-heading">
							        <div class="pull-center">Waiting Approval <br>Finance</div>
							    </div>
							    <div class="tiles-body">
							        <div class="pull-left"><i class="fa fa-eye"></i></div>
							        <div class="pull-right" id="tot_wait_13">0</div>
							    </div>
								<div class="tiles-footer">&nbsp;</div>
							</div>
						</div>
						<div class="col-md-2">
							<div class="info-tiles tiles-primary" id="wait_14">
							    <div class="tiles-heading">
							        <div class="pull-center">Waiting Release Div. Head Fast & Fin</div>
							    </div>
							    <div class="tiles-body">
							        <div class="pull-left"><i class="fa fa-eye"></i></div>
							        <div class="pull-right" id="tot_wait_14">0</div>
							    </div>
								<div class="tiles-footer">&nbsp;</div>
							</div>
						</div>
						<div class="col-md-2">
							<div class="info-tiles tiles-primary" id="wait_15">
							    <div class="tiles-heading">
							        <div class="pull-center">Waiting Approval Kasir/Finance</div>
							    </div>
							    <div class="tiles-body">
							        <div class="pull-left"><i class="fa fa-eye"></i></div>
							        <div class="pull-right" id="tot_wait_15">0</div>
							    </div>
								<div class="tiles-footer">&nbsp;</div>
							</div>
						</div>
						<div class="col-md-2">
							<div class="info-tiles tiles-primary" id="wait_16">
							    <div class="tiles-heading">
							        <div class="pull-center">Request On Progress</div>
							    </div>
							    <div class="tiles-body">
							        <div class="pull-left"><i class="fa fa-eye"></i></div>
							        <div class="pull-right" id="tot_wait_16">0</div>
							    </div>
								<div class="tiles-footer">&nbsp;</div>
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
				<h4>Fund Preparation Monitoring</h4>
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-md-4">
						<div class="info-tiles tiles-midnightblue" id="wait_5">
						    <div class="tiles-heading">
						        <div class="pull-center">Payment Request Today</div>
						    </div>
						    <div class="tiles-body">
						        <div class="pull-left">Rp</div>
						        <div class="pull-right" id="tot_fund_1">0</div>
						    </div>
							<div class="tiles-footer">&nbsp;</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="info-tiles tiles-primary" id="wait_1">
						    <div class="tiles-heading">
						        <div class="pull-center">Payment Request H+1</div>
						    </div>
						    <div class="tiles-body">
						        <div class="pull-left">Rp</div>
						        <div class="pull-right" id="tot_fund_2">0</div>
						    </div>
							<div class="tiles-footer">&nbsp;</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="info-tiles tiles-inverse" id="wait_2">
						    <div class="tiles-heading">
						        <div class="pull-center">Payment Request > H+1</div>
						    </div>
						    <div class="tiles-body">
						        <div class="pull-left">Rp</div>
						        <div class="pull-right" id="tot_fund_3">0</div>
						    </div>
							<div class="tiles-footer">&nbsp;</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>