<?php session_start(); ?>
<style type="text/css">
	.info-tiles, .ket { cursor: pointer; }
	.info-tiles .tiles-heading { text-transform: none; padding: 2px 10px; font-size: 20px;}
	.info-tiles .tiles-footer::after { margin-right: 10px; border-right: 5px solid #fff; border-bottom: 5px solid #fff;}
	.info-tiles .tiles-footer { padding: 0px 10px; }
	.info-tiles .tiles-body { font-size: 20px;  padding: 2px 10px; }
	.input-group-addon { background-color: #4f8edc; border: 1px solid #4f8edc;padding: 0;min-width: 0; }
	.input-group-addon button {padding: 2px 10px;border: 0;background: #4f8edc;color: #fff;}
	/*@media (min-width: 768px){.col-md-3{width: 25%;}}
	@media (min-width: 992px){.col-md-3{width: 20%;}}
	@media (min-width: 1200px){.col-md-3{width: 16,67%;}}*/
	.btn-val, .btn-approve, .btn-reject { padding: 3px 15px 3px 15px;width: 100%; }
	.btn, .input-group { height:40px; margin-top:10px;}
	.ket { padding:3px; }
</style>
<script type="text/javascript" src="system/myJs/beranda.js"></script>
<link rel="stylesheet" type="text/css" href="assets/plugins/flexii/css/flexigrid.pack.css" />

<div class="row">
	<div class="col-md-12">
		<div class="panel">
			<div class="panel-heading">
				<h4>Approval Progress Monitoring</h4>
			</div>
			<div class="panel-body" style="padding-bottom: 0;">
					
					<style type="text/css">
						@media (min-width: 768px){.col-md-3{width: 25%;}}
						@media (min-width: 992px){.col-md-3{width: 20%;}}
						@media (min-width: 1200px){.col-md-3{width: 16,67%;}}
					</style>
					<div class="row">
						<div class="col-md-4">
							<div class="info-tiles tiles-primary">
							    <div class="tiles-heading">
							        <div class="pull-center" id="thingstodo">Things To Do</div>
							    </div>
							    <div class="tiles-body" id="thingstodobody">
                                	<?php if ($_SESSION['level']=='TAX') { ?>
							         <div class="ket" id="wait_tax">Approval Tax (<span id="tot_wait_tax"></span>)</div>
                                     <?php } ?>
                                    
                                     <?php if ($_SESSION['level']=='ACCOUNTING') { ?>
							         <div class="ket" id="wait_accounting">Approval Accounting (<span id="tot_wait_accounting"></span>)</div>
                                    <?php } ?>
                                    
                                     <?php if ($_SESSION['level']=='SECTION HEAD') { ?>
							         <div class="ket" id="wait_section">Approval Sect Head (<span id="tot_wait_section"></span>)</div>
                                    <?php } ?>
                                    
                                     <?php if ($_SESSION['level']=='DEPT. HEAD') { ?>
							         <div class="ket" id="wait_dept_head">Approval Dept Head (<span id="tot_wait_dept_head"></span>)</div>
                                    <?php } ?>
                                    
                                     <?php if ($_SESSION['level']=='DIV. HEAD') { ?>
							         <div class="ket" id="wait_div_head">Approval Div Head (<span id="tot_wait_div_head"></span>)</div>
                                    <?php } ?>
                                    
                                     <?php if ($_SESSION['level']=='DIREKSI') { ?>
							         <div class="ket" id="wait_direksi">Approval Direksi 1 (<span id="tot_wait_direksi"></span>)</div>
                                    <?php } ?>
                                    
                                     <?php if ($_SESSION['level']=='DIREKSI 2') { ?>
							         <div class="ket" id="wait_direksi2">Approval Direksi 2 (<span id="tot_wait_direksi2"></span>)</div>
                                    <?php } ?>
                                    
                                     <?php if ($_SESSION['level']=='FINANCE') { ?>
							         <div class="ket" id="wait_fin">Approval Finance (<span id="tot_wait_fin"></span>)</div>
                                    <?php } ?>
                                    
                                     <?php if ($_SESSION['level']=='DEPT. HEAD' and $_SESSION['evo_dept']=='FINANCE') { ?>
							         <div class="ket" id="wait_releaser">Approval Releaser 1 (<span id="tot_wait_releaser"></span>)</div>
                                    <?php } ?>
                                    
                                     <?php if ($_SESSION['level']=='DIV. HEAD' and $_SESSION['evo_divisi']=='FINANCE and ACCOUNTING' and $_SESSION['evo_dept']=='all') { ?>
							         <div class="ket" id="wait_div_fast">Approval Releaser 2 (<span id="tot_wait_div_fast"></span>)</div>
                                    <?php } ?> 
							    </div>
								<div class="tiles-footer" id="capt_wait_1"></div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="info-tiles tiles-green" id="bucket_all">
							    <div class="tiles-heading">
							        <div class="pull-center"></div>
							    </div>
                                <div class="tiles-body">Tampilkan Semua Progress Bucket
							    </div>
								<div class="tiles-footer" id="capt_wait_1"></div>
							</div>
						</div>						
					</div>
			
			</div>
		</div>
	</div>
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
    
    <input type="hidden" id="srcLevel" class="form-control" value="<?php echo $srcLevel; ?>">
    <input type="hidden" id="sesi_level" class="form-control" value="<?php echo $_SESSION['level']; ?>">
    <input type="hidden" id="sesi_kodedealer" class="form-control" value="<?php echo $_SESSION['kodedealer']; ?>">
    <input type="hidden" id="sesi_dept" class="form-control" value="<?php echo $sesi_dept;?>">
    <input type="hidden" id="evo_dept" class="form-control" value="<?php echo $_SESSION['evo_dept']; ?>">
    
	<div class="col-md-12" id="list_validasi">
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
								<span class="input-group-addon">
								  <button type="button" id="search">
									<i class="fa fa-search"></i>
								  </button>
								</span>
							</div>
						</div>
						<div class="col-sm-2" id="btn-val"></div>
                        <div class="col-sm-2" id="btn-approve"></div><!--
                        <div class="col-sm-2" id="btn-reject"></div>-->
						<div class="col-md-8">&nbsp;</div>
						<div class="col-md-12">
                        <?php if ($_SESSION['level']=='DIREKSI 2') { ?><input type="checkbox" onclick='toggle(this);'> check/unchecked All <?php } ?>
							<table class="flexme3" style="display: none"></table>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>