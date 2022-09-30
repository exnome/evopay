<?php session_start(); ?>
<style type="text/css">
	.info-tiles { cursor: pointer; }
	.info-tiles .tiles-heading { text-transform: none; padding: 2px 10px; }
	.info-tiles .tiles-footer::after { margin-right: 10px; border-right: 5px solid #fff; border-bottom: 5px solid #fff;}
	.info-tiles .tiles-footer { padding: 0px 10px; }
	.info-tiles .tiles-body { font-size: 25px;  padding: 2px 10px; }
	.input-group-addon { background-color: #4f8edc; border: 1px solid #4f8edc;padding: 0;min-width: 0; }
	.input-group-addon button {padding: 2px 10px;border: 0;background: #4f8edc;color: #fff;}
	/*@media (min-width: 768px){.col-md-3{width: 25%;}}
	@media (min-width: 992px){.col-md-3{width: 20%;}}
	@media (min-width: 1200px){.col-md-3{width: 16,67%;}}*/
	.btn-val, .btn-approve, .btn-reject { padding: 3px 15px 3px 15px;width: 100%; }
	.btn, .input-group { height:40px; margin-top:10px;}
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
					<style type="text/css">
						@media (min-width: 768px){.col-md-3{width: 25%;}}
						@media (min-width: 992px){.col-md-3{width: 20%;}}
						@media (min-width: 1200px){.col-md-3{width: 16,67%;}}
					</style>
					<div class="row">
						<div class="col-md-3">
							<div class="info-tiles tiles-brown" id="wait_1">
							    <div class="tiles-heading">
							        <div class="pull-center">Wait Checker Tax</div>
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
							        <div class="pull-center">Wait Approval Sect. Head</div>
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
							        <div class="pull-center">Wait Approval ADH</div>
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
							        <div class="pull-center">Wait Approval Kacab</div>
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
				<?php } else {
				
				
				 ?>
                 <style type="text/css">
				 	.col-xs-5ths,
					.col-sm-5ths,
					.col-md-5ths,
					.col-lg-5ths {
						position: relative;
						min-height: 1px;
						padding-right: 8px;
						padding-left: 8px;
					}
					
					.col-xs-5ths {
						width: 20%;
						float: left;
					}
					
					@media (min-width: 768px) {
						.col-sm-5ths {
							width: 20%;
							float: left;
						}
					}
					
					@media (min-width: 992px) {
						.col-md-5ths {
							width: 20%;
							float: left;
						}
					}
					
					@media (min-width: 1200px) {
						.col-lg-5ths {
							width: 20%;
							float: left;
						}
					}
				 
				 </style>
					<div class="row">
						<div class="col-md-5ths">
							<div class="info-tiles tiles-midnightblue" id="wait_tax">
							    <div class="tiles-heading">
							        <div class="pull-center">Tax Checker</div>
							    </div>
							    <div class="tiles-body">
							        <div class="pull-left"><i class="fa fa-eye"></i></div>
							        <div class="pull-right" id="tot_wait_tax">0</div>
							    </div>
								<div class="tiles-footer" id="capt_wait_tax">&nbsp;</div>
							</div>
						</div>
                        <div class="col-md-5ths">
							<div class="info-tiles tiles-midnightblue" id="wait_accounting">
							    <div class="tiles-heading">
							        <div class="pull-center">Accounting</div>
							    </div>
							    <div class="tiles-body">
							        <div class="pull-left"><i class="fa fa-eye"></i></div>
							        <div class="pull-right" id="tot_wait_accounting">0</div>
							    </div>
								<div class="tiles-footer" id="capt_wait_accounting">SELECTED</div>
							</div>
						</div>
						<div class="col-md-5ths">
							<div class="info-tiles tiles-midnightblue" id="wait_section">
							    <div class="tiles-heading">
							        <div class="pull-center">Sect. Head</div>
							    </div>
							    <div class="tiles-body">
							        <div class="pull-left"><i class="fa fa-eye"></i></div>
							        <div class="pull-right" id="tot_wait_section">0</div>
							    </div>
								<div class="tiles-footer" id="capt_wait_section">&nbsp;</div>
							</div>
						</div>
						<div class="col-md-5ths">
							<div class="info-tiles tiles-midnightblue" id="wait_dept_head">
							    <div class="tiles-heading">
							        <div class="pull-center">Dept. Head</div>
							    </div>
							    <div class="tiles-body">
							        <div class="pull-left"><i class="fa fa-eye"></i></div>
							        <div class="pull-right" id="tot_wait_dept_head">0</div>
							    </div>
								<div class="tiles-footer" id="capt_wait_dept_head">&nbsp;</div>
							</div>
						</div>
						<div class="col-md-5ths">
							<div class="info-tiles tiles-midnightblue" id="wait_div_head">
							    <div class="tiles-heading">
							        <div class="pull-center">Div. Head</div>
							    </div>
							    <div class="tiles-body">
							        <div class="pull-left"><i class="fa fa-eye"></i></div>
							        <div class="pull-right" id="tot_wait_div_head">0</div>
							    </div>
								<div class="tiles-footer" id="capt_wait_div_head">&nbsp;</div>
							</div>
						</div>
                   </div>
                   <div class="row">
						<div class="col-md-5ths">
							<div class="info-tiles tiles-midnightblue" id="wait_direksi">
							    <div class="tiles-heading">
							        <div class="pull-center">Direksi 1</div>
							    </div>
							    <div class="tiles-body">
							        <div class="pull-left"><i class="fa fa-eye"></i></div>
							        <div class="pull-right" id="tot_wait_direksi">0</div>
							    </div>
								<div class="tiles-footer" id="capt_wait_direksi">&nbsp;</div>
							</div>
						</div>
                        <div class="col-md-5ths">
							<div class="info-tiles tiles-midnightblue" id="wait_direksi2">
							    <div class="tiles-heading">
							        <div class="pull-center">Direksi 2</div>
							    </div>
							    <div class="tiles-body">
							        <div class="pull-left"><i class="fa fa-eye"></i></div>
							        <div class="pull-right" id="tot_wait_direksi2">0</div>
							    </div>
								<div class="tiles-footer" id="capt_wait_direksi2">&nbsp;</div>
							</div>
						</div>
						<div class="col-md-5ths">
							<div class="info-tiles tiles-midnightblue" id="wait_fin">
							    <div class="tiles-heading">
							        <div class="pull-center">Finance Checker</div>
							    </div>
							    <div class="tiles-body">
							        <div class="pull-left"><i class="fa fa-eye"></i></div>
							        <div class="pull-right" id="tot_wait_fin">0</div>
							    </div>
								<div class="tiles-footer" id="capt_wait_fin">&nbsp;</div>
							</div>
						</div>
						<div class="col-md-5ths">
							<div class="info-tiles tiles-midnightblue" id="wait_releaser">
							    <div class="tiles-heading">
							        <div class="pull-center">Releaser 1</div>
							    </div>
							    <div class="tiles-body">
							        <div class="pull-left"><i class="fa fa-eye"></i></div>
							        <div class="pull-right" id="tot_wait_releaser">0</div>
							    </div>
								<div class="tiles-footer" id="capt_wait_releaser">&nbsp;</div>
							</div>
						</div>
                        <div class="col-md-5ths">
							<div class="info-tiles tiles-midnightblue" id="wait_div_fast">
							    <div class="tiles-heading">
							        <div class="pull-center">Releaser 2</div>
							    </div>
							    <div class="tiles-body">
							        <div class="pull-left"><i class="fa fa-eye"></i></div>
							        <div class="pull-right" id="tot_wait_div_fast">0</div>
							    </div>
								<div class="tiles-footer" id="capt_wait_div_fast">&nbsp;</div>
							</div>
						</div>
						<div class="col-md-5ths">
							<?php 
							// yangdimintaiapprovalmaka data request akanmasukke bucket tersebut, dan caption bucket tersebutadalah: Request Others Department
							// yang mengajukanrequest approvalke Department lain (termasukbila user tersebutadalah Division Head dari Department yang mengajukan request), makadata akanmasukke bucket tersebutdengan caption Wait Related Department
							
							#---- cek req / waiting dept lain
							function cekapprove(){
								require_once ('system/inc/conn.php');
								$IdUser = $_SESSION['UserID'];
								
								//$user = mssql_fetch_array(mssql_query("select * from sys_user where IdUser = '".$IdUser."'"));
								if ($_SESSION['level']=='ADMIN') {
									$sql = "select count(*) as tot 
											from DataEvo a
											inner join sys_user b on a.deptterkait = b.department and a.kodedealer = b.kodedealer
											inner join DataEvoVal c on a.nobukti = c.nobukti and b.tipe = c.level and b.department = c.deptterkait
											inner join DeptTerkait d on b.IdUser = d.IdUser
											where isnull(a.tglbayar,'') = '' and b.IdUser = '".$IdUser."' and isnull(c.validasi,'') = '' ";
									
									//echo "<pre>$sql</pre>";
									// return $sql;
									$rsl = mssql_query($sql);
									$dt = mssql_fetch_array($rsl);
								}
								
								return isset($dt['tot']) ? $dt['tot'] : 0;
							}
														
							function cekwaiting(){
								require_once ('system/inc/conn.php');
								$IdUser = $_SESSION['UserID'];
								
								$user = mssql_fetch_array(mssql_query("select * from sys_user where IdUser = '".$IdUser."'"));
								if ($user['tipe']=='ADMIN') { $s_admin = "and pengaju = '".$IdUser."'"; } else { $s_admin = ""; }
								if ($user['tipe']=='SECTION HEAD' or $user['tipe']=='ADH') { $s_section = "and sect = '".$IdUser."'"; } else { $s_section = ""; }
								if ($user['tipe']=='DEPT. HEAD' or $user['tipe']=='KEPALA CABANG' and $lvl!='DEPT. HEAD FINANCE / DIV. HEAD FAST') { 
									$s_dept = "and dept = '".$IdUser."'"; 
								} else { 
									$s_dept = ""; 
								}
								if ($user['tipe']=='DIV. HEAD' and $lvl!='DEPT. HEAD FINANCE / DIV. HEAD FAST') {
									$s_div = "and (div = '".$IdUser."' or div = 'all')"; 
								} else { 
									$s_div = ""; 
								}
								
								/*$sql = "
									select count(*) as tot from (
										select * from
											(select nobukti,kodedealer,userentry as pengaju,IdAtasan as sect,
											(select IdAtasan from sys_user where IdUser=a.IdAtasan) as dept,
											(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.IdAtasan)) as div,
									(SELECT top 1 level FROM DataEvoVal 
										where nobukti=a.nobukti and ISNULL(level, '')!='' and ISNULL(validasi, '')='' order by idVal desc) as lvl,
									(SELECT top 1 deptterkait FROM DataEvoVal 
										where nobukti=a.nobukti and ISNULL(level, '')!='' and ISNULL(validasi, '')='' order by idVal desc) as deptterkaitval
									
											from DataEvo a
											where isnull(a.tglbayar,'') = ''
										) x where ISNULL(lvl, '')!='' and kodedealer = '".$user['KodeDealer']."'  $s_admin $s_section $s_dept $s_div
										and lvl != 'TAX' and isnull(deptterkaitval,'') <> ''
									) x GROUP BY lvl";*/
									
								if ($user['KodeDealer']=='2010') {
									$dealer = " and y.is_dealer = '0' ";
								} else {
									$dealer = " and y.is_dealer = '1' ";
								}	
								
								$sql = "
									select count(*) as tot from (
									select x.nobukti, x.kodedealer, x.idAtasan, x.pengaju, x.tipe, x.sect, x.dept, x.div, x.lvl, x.deptterkait
														from (	
										select a.nobukti, a.kodedealer, a.idAtasan, a.pengaju, a.tipe, a.sect,
																	
										case
											when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
											when a.tipe = 'DEPT. HEAD' then a.pengaju
											when a.tipe = 'DIV. HEAD' then '' else (select IdAtasan from sys_user where IdUser=a.idAtasan) end dept,
												
										case 
											when a.tipe = 'SECTION HEAD' then (select IdAtasan from sys_user 
													where IdUser=(select IdAtasan from sys_user where IdUser=a.pengaju))
											when a.tipe = 'DEPT. HEAD' then (select IdAtasan from sys_user where IdUser=a.pengaju)
											when a.tipe = 'DIV. HEAD' then '' else 
											(select IdAtasan from sys_user where IdUser=(select IdAtasan from sys_user where IdUser=a.sect)) end div,
										
										(SELECT top 1 level FROM DataEvoVal x
											left join sys_level y on x.level = y.nama_lvl $dealer
											where nobukti=a.nobukti and ISNULL(level, '')!=''
											and ISNULL(validasi, '')='' 
											and isnull(deptterkait,'') <> ''
											order by y.urutan asc) as lvl,
												
										(SELECT top 1 deptterkait FROM DataEvoVal x
											left join sys_level y on x.level = y.nama_lvl $dealer
											where nobukti=a.nobukti and ISNULL(level, '')!=''
											and ISNULL(validasi, '')='' 
											order by y.urutan asc) as deptterkait								
											
										from (
											select nobukti,x.kodedealer, x.idAtasan, userentry as pengaju, y.tipe,
											case
											when y.tipe = 'SECTION HEAD' then userentry
											when y.tipe = 'DEPT. HEAD' then '' 
											when y.tipe = 'DIV. HEAD' then '' else x.IdAtasan  end sect	
											from DataEvo x
											inner join sys_user y on x.userentry = y.IdUser
											where isnull(x.deptterkait,'') <> ''
										) a
										
									) x 
									where ISNULL(lvl, '')!=''  and x.kodedealer = '".$user['KodeDealer']."' $s_admin $s_section $s_dept $s_div
									and isnull(deptterkait,'') <> '') x GROUP BY lvl";
										
								
								//echo "<pre>$sql</pre>";
								// return $sql;
								$rsl = mssql_query($sql);
								$dt = mssql_fetch_array($rsl);
								return isset($dt['tot']) ? $dt['tot'] : 0;
							}
							
							if ($_SESSION['level']=='TAX' or $_SESSION['level']=='ACCOUNTING' or $_SESSION['level']=='DIREKSI' or $_SESSION['level']=='DIREKSI 2' or $_SESSION['level']=='FINANCE') {
								$jml_approve = 0;
								$jml_waiting = 0;
							}else{
								$jml_approve = cekapprove();
								$jml_waiting = cekwaiting();
							}
							
							
							if ($jml_approve>0) { ?>                            
                            <div class="info-tiles tiles-midnightblue" id="req_dept_lain">
							    <div class="tiles-heading">
							        <div class="pull-center">Request Others Department</div>
							    </div>
							    <div class="tiles-body">
							        <div class="pull-left"><i class="fa fa-eye"></i></div>
							        <div class="pull-right" id="tot_req_dept_lain">0</div>
							    </div>
								<div class="tiles-footer" id="capt_req_dept_lain">&nbsp;</div>
							</div>
							<?php $sesi_dept = $_SESSION['evo_dept']; ?>
                            <?php } 
							
							
							if ($jml_waiting>0) {  
								$sesi_dept = ""; ?>
                            <div class="info-tiles tiles-midnightblue" id="wait_dept_lain">
							    <div class="tiles-heading">
							        <div class="pull-center">Wait Related Department</div>
							    </div>
							    <div class="tiles-body">
							        <div class="pull-left"><i class="fa fa-eye"></i></div>
							        <div class="pull-right" id="tot_wait_dept_lain">0</div>
							    </div>
								<div class="tiles-footer" id="capt_wait_dept_lain">&nbsp;</div>
							</div>
                            <?php } ?>
                            
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
								<input type="text" id="searchText" class="form-control">
								<input type="hidden" id="srcLevel" class="form-control" value="<?php echo $srcLevel; ?>">
								<input type="hidden" id="sesi_level" class="form-control" value="<?php echo $_SESSION['level']; ?>">
								<input type="hidden" id="sesi_kodedealer" class="form-control" value="<?php echo $_SESSION['kodedealer']; ?>">
                            	<input type="hidden" id="sesi_dept" class="form-control" value="<?php echo $sesi_dept;?>">
                            	<input type="hidden" id="evo_dept" class="form-control" value="<?php echo $_SESSION['evo_dept']; ?>">
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
	<div class="col-md-12">
		<div class="panel">
			<div class="panel-body">
				<!-- <iframe src="https://sup.nantapack.com/print/suratjalan/554/" width="100%"></iframe> -->
				<!-- <iframe src="https://mki.nantapack.com/print/kwitansi/236/" width="100%"></iframe> -->
				<!-- <iframe src="https://nantapack.com:2083" width="100%"></iframe> -->
			</div>
		</div>
	</div>
</div>