<?php require_once ('system/inc/permission.php');  ?>
<script type="text/javascript" src="system/myJs/deptterkait.js"></script>
<link rel="stylesheet" type="text/css" href="assets/plugins/flexii/css/flexigrid2.css" />
<script type="text/javascript">
	jQuery(document).ready(function($) {
		$('#divisi').on('change',function(){
			var noid =  $('#validator_nid').val();
			
			for (i=0;i<noid;i++) {
				$("#rowvalidator"+i).remove();
			}
			$('#validator_nid').val('0');

			$('#user').html('<option value="">- Pilih -</option>');
			$('#user').attr({'disabled':'disabled', 'value':''});
			
			var kodedealer = $('#KodeDealer').val();
			var divisi = $('#divisi').val();
			$.ajax({ 
				url: 'system/data/home.php',
				data: { action:'getDepartment', 'kodedealer' : kodedealer, 'divisi' : divisi },
				type: 'post',
				beforeSend: function(){
					onload = showLoading();
				},
				success: function(output4) {
					onload = hideLoading();
					if (output4!='') {
						$('#department').html(output4);
						$('#department').removeAttr('disabled');
					} else {
						$('#department').html('<option value="">- Pilih -</option>');
						$('#department').attr({'disabled':'disabled', 'value':''});
					}
				}
			});
		});
		
		$('#department').on('change',function(){
			var noid =  $('#validator_nid').val();
			
			for (i=0;i<noid;i++) {
				$("#rowvalidator"+i).remove();
			}
			$('#validator_nid').val('0');
			
			var kodedealer = $('#KodeDealer').val();
			var divisi = $('#divisi').val();
			var department = $('#department').val();
			$.ajax({ 
				url: 'system/data/home.php',
				data: { action:'getUser', 'kodedealer' : kodedealer, 'divisi' : divisi, 'department' : department },
				type: 'post',
				beforeSend: function(){
					onload = showLoading();
				},
				success: function(output4) {
					onload = hideLoading();
					//alert(output4);
					if (output4!='') {
						$('#user').html(output4);
						$('#user').removeAttr('disabled');
					} else {
						$('#user').html('<option value="">- Pilih -</option>');
						$('#user').attr({'disabled':'disabled', 'value':''});
					}
				}
			});
		});
		
		$('#user').on('change',function(){
			var noid =  $('#validator_nid').val();
			
			for (i=0;i<noid;i++) {
				$("#rowvalidator"+i).remove();
			}
			$('#validator_nid').val('0');
		});
		
		
    });
	function addvalidator() {						
		var noid =  $('#validator_nid').val();
		var kodedealer = $('#KodeDealer').val();
		var divisi = $('#divisi').val();
		var department = $('#department').val();
		
		if (noid==0) {
			var atasan = $('#user').val();
		} else {
			var noidmin = parseInt(noid) - 1;
			var atasan = $('#user'+noidmin).val();
			
			if (typeof atasan === 'undefined') {
				var atasan = $('#user0').val();
			} else {
				
			}
		}
		
		$('.userval').on('change',function(){
			//var noidmin = parseInt(noid) - 1;
			//$("#rowvalidator"+noidmin).remove();
			//$('#validator_nid').val('0');
			
			var text = $('.userval').attr('class'); 
			var text1 = text.split(" ");
			var noidnext = parseInt(text1[2]) + 1;
			$("#rowvalidator"+noidnext).remove();
		});
		
		
		if (atasan!='') {
			$.ajax({ 
				url: 'system/data/home.php',
				data: { action:'getUser', 'kodedealer' : kodedealer, 'divisi' : divisi, 'department' : department, 'atasan':atasan },
				type: 'post',
				beforeSend: function(){
					onload = showLoading();
					$("#validator_detail").append("<tr id='rowvalidator" + noid + "'><td><select id='user" + noid + "' class='form-control userval " + noid + "'><option value=''>- Pilih -</option></select></td><td>&nbsp;&nbsp;<a href='#' onClick='removevalidator(\"#rowvalidator"+noid+"\"); return false;'><img src='assets/img/cross.png' border='0'></a></td></tr>");
				},
				success: function(output4) {
					onload = hideLoading();
					if (output4!='') {
						$('#user'+ noid).html(output4);
						$('#user'+ noid).removeAttr('disabled');
					} else {
						$('#user'+ noid).html('<option value="">- Pilih -</option>');
						$('#user'+ noid).attr({'disabled':'disabled', 'value':''});
					}
					
					noid = (noid - 1) + 2;	
					document.getElementById("validator_nid").value = noid;		
				}
			});
		} else {
			alert('Pilih dulu user validator nya !');
		}
							
														
	}				

	function removevalidator(noid) {
		$(noid).remove();
	}
</script>
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-gray">
			<div class="panel-heading">
				<h4>Departement Terkait</h4>
			</div>
			<form id="validate-form" action="test.php" method="POST" class="form-horizontal row-border">
				<?php if (isset($_POST['new'])) { ?>
					<div class="panel-body collapse in">
						<div class="form-group">
							<label class="col-sm-2 control-label">Area Dealer</label>
							<div class='col-sm-4'>
								<select id="KodeDealer" class="form-control">
									<?php
										$qry1 = mssql_query("select KodeDealer,NamaDealer from SPK00..dodealer 
																where KodeDealer in ('2010')",$conns);	
										while($row = mssql_fetch_array($qry1)){
											echo "<option value='".$row['KodeDealer']."'>".$row['NamaDealer']."</option>";
										}
									?>
								</select>
							</div>
                            <div class="col-sm-6"></div>
						</div>
						<div class="form-group">
                            <label class="col-sm-2 control-label">Divisi</label>
							<div class="col-sm-4">
							    <select id="divisi" class="form-control">
							    <?php
										$qry1 = mssql_query("select nama_div from sys_divisi where is_dealer = '0' and is_aktif = 1",$conns);	
										echo "<option value=''>- Pilih -</option>";
										while($row = mssql_fetch_array($qry1)){
											echo "<option value='".$row['nama_div']."'>".$row['nama_div']."</option>";
										}
									?>
							    </select>
							</div>
							<label class="col-sm-2 control-label">Departemen</label>
							<div class="col-sm-4">
							    <select id="department" class="form-control" disabled>
							    	<option value=''>- Pilih -</option>
							    </select>
							</div>
						</div>
                        <div class="form-group">
							<label class="col-sm-2 control-label">Validator</label>
							<div class="col-sm-4">
							    <select id="user" class="form-control" disabled>
							    	<option value=''>- Pilih -</option>
							    </select>
							</div>
                            <div class="col-sm-2">
                            	<a href='#' onClick="javascript:addvalidator(); return false;" id="tambah">
                                <img src="assets/img/add.png" title="tambah validator" /></a></div>
                            <div class="col-sm-4"></div>
						</div>
                        
                        <div class="form-group">
                        	<div class="col-sm-2"></div>
                            <div class="col-sm-6">
                                <table width="100%" id="validator_detail" border="0" cellpadding="2" cellspacing="2">
                            	</table>
                            </div>
                            <div class="col-sm-4"><input name="validator_nid" type="hidden" id="validator_nid" value="0" readonly/></div>	
                        </div>
                        
					</div>
                    <div class="panel-footer">
						<div class="row">
						    <div class="col-sm-10 col-sm-offset-2">
						        <div class="btn-toolbar">
						            <button type="button" id="new" class="btn-primary btn">Save</button>
						            <button type="button" id="cancel" class="btn-default btn">Cancel</button>
						        </div>
						    </div>
						</div>
					</div>
				<?php 
					} else if (isset($_POST['edit']) and isset($_POST['id'][0])) { 
						$id = isset($_POST['id']) ? $_POST['id'][0] : null;
						$r=mssql_fetch_array(mssql_query("select * from DeptTerkait where id='".$id."'"));
				?>
					<script type="text/javascript">
						jQuery(document).ready(function($) {
							var kodedealer = $('#KodeDealer').val();
							$.ajax({ 
								url: 'system/data/home.php',
								data: { action:'getLevel', 'kodedealer' : kodedealer, 'value': '<?php echo $r[tipe]; ?>' },
								type: 'post',
								beforeSend: function(){
									onload = showLoading();
								},
								success: function(output) {
									onload = hideLoading();
									$('#tipe').html(output);
									var tipe = $('#tipe').val();
									$.ajax({ 
										url: 'system/data/home.php',
										data: { action:'getDivisi', 'kodedealer' : kodedealer, 'tipe' : tipe, 'value': '<?php echo $r[divisi]; ?>' },
										type: 'post',
										beforeSend: function(){
											onload = showLoading();
										},
										success: function(output) {
											onload = hideLoading();
											$('#divisi').html(output);
											var divisi = $('#divisi').val();
											var department = $('#department').val();
											$.ajax({ 
												url: 'system/data/home.php',
												data: { action:'getAtasan', 'kodedealer' : kodedealer, 'tipe' : tipe, 'divisi' : divisi, 'department' : department , 'value': '<?php echo $r[IdAtasan]; ?>' },
												type: 'post',
												beforeSend: function(){
													onload = showLoading();
												},
												success: function(output) {
													onload = hideLoading();
													$('#IdAtasan').html(output);
													$('#IdAtasan').removeAttr('disabled');
												}
											});
											$('#divisi').removeAttr('disabled');
										}
									});
									$('#tipe').removeAttr('disabled');
								}
							});

					    });
					</script>
					<div class="panel-body collapse in">
						<div class="form-group">
							<label class="col-sm-2 control-label">Area Dealer</label>
							<div class='col-sm-4'>
								<select id="KodeDealer" class="form-control">
									<?php
										$qry1 = mssql_query("select KodeDealer,NamaDealer from SPK00..dodealer 
											where KodeDealer not in ('2176','2120')",$conns);	
										echo "<option value=''>- Pilih -</option>";
										while($row = mssql_fetch_array($qry1)){
											$pilih = ($r['KodeDealer']==$row['KodeDealer'])?"selected" : ""; 
											echo "<option value='".$row['KodeDealer']."' $pilih>".$row['NamaDealer']."</option>";
										}
									?>
								</select>
							</div>
							 <div class="col-sm-6"></div>
						</div>
						<div class="form-group">
                            <label class="col-sm-2 control-label">Divisi</label>
							<div class="col-sm-4">
							    <select id="divisi" class="form-control" disabled>
							    	<option value=''>- Pilih -</option>
							    </select>
							</div>
							<label class="col-sm-2 control-label">Departemen</label>
							<div class="col-sm-4">
							    <select id="department" class="form-control" disabled>
							    	<option value=''>- Pilih -</option>
							    </select>
							</div>
						</div>
                        <div class="form-group">
							<label class="col-sm-2 control-label">Validator</label>
							<div class="col-sm-4">
							    <select id="IdAtasan" class="form-control" disabled>
							    	<option value=''>- Pilih -</option>
							    </select>
							</div>
                            <div class="col-sm-2"><img src="assets/img/add.png" title="tambah validator" /></div>
                            <div class="col-sm-4"></div>
						</div>
					</div>
					<div class="panel-footer">
						<div class="row">
						    <div class="col-sm-10 col-sm-offset-2">
						        <div class="btn-toolbar">
						            <button type="button" id="edit" class="btn-primary btn">Save Changes</button>
						            <button type="button" id="cancel" class="btn-default btn">Cancel</button>
						        </div>
						    </div>
						</div>
					</div>
				<?php } ?>
			</form>
		</div>
	</div>
</div>