<?php require_once ('system/inc/permission.php');  ?>
<script type="text/javascript" src="system/myJs/user.js"></script>
<link rel="stylesheet" type="text/css" href="assets/plugins/flexii/css/flexigrid2.css" />
<script type="text/javascript">
	jQuery(document).ready(function($) {
		
		$('#statususer').attr({'disabled':'disabled', 'value':''});
						
		$('#KodeDealer').on('change',function(){
			var kodedealer = $('#KodeDealer').val();
			$.ajax({ 
				url: 'system/data/home.php',
				data: { action:'getLevel', 'kodedealer' : kodedealer },
				type: 'post',
				beforeSend: function(){
					onload = showLoading();
				},
				success: function(output) {
					onload = hideLoading();
					if (output!='') {
						$('#tipe').removeAttr('disabled');
						$('#tipe').html(output);
						$('#tipe').on('change',function(){
							var tipe = $('#tipe').val();
							$.ajax({ 
								url: 'system/data/home.php',
								data: { action:'getDivisi', 'kodedealer' : kodedealer, 'tipe' : tipe },
								type: 'post',
								beforeSend: function(){
									onload = showLoading();
								},
								success: function(output2) {
									onload = hideLoading();
									if (output2!='') {
										$('#divisi').removeAttr('disabled');
										$('#divisi').html(output2);
										$('#divisi').on('change',function(){
											var divisi = $('#divisi').val();
											if (kodedealer=='2010') {
												$.ajax({ 
													url: 'system/data/home.php',
													data: { action:'getDepartment', 'kodedealer' : kodedealer, 'tipe' : tipe, 'divisi' : divisi },
													type: 'post',
													beforeSend: function(){
														onload = showLoading();
													},
													success: function(output3) {
														onload = hideLoading();
														if (output3) {
															$('#department').removeAttr('disabled');
															$('#department').html(output3);
															$('#department').on('change',function(){
																var department = $('#department').val();
																$.ajax({ 
																	url: 'system/data/home.php',
																	data: { action:'getAtasan', 'kodedealer' : kodedealer, 'tipe' : tipe, 'divisi' : divisi, 'department' : department },
																	type: 'post',
																	beforeSend: function(){
																		onload = showLoading();
																	},
																	success: function(output4) {
																		onload = hideLoading();
																		if (output4!='') {
																			$('#IdAtasan').html(output4);
																			$('#IdAtasan').removeAttr('disabled');
																		} else {
																			$('#IdAtasan').html('<option value="">- Pilih -</option>');
																			$('#IdAtasan').attr({'disabled':'disabled', 'value':''});
																		}
																	}
																});
															});
														} else {
															$('#department').html('<option value="">- Pilih -</option>');
															$('#department').attr({'disabled':'disabled', 'value':''});
															$('#IdAtasan').attr({'disabled':'disabled', 'value':''});
														}
													}
												});	
											} else if (kodedealer!='2010') {
												$.ajax({ 
													url: 'system/data/home.php',
													data: { action:'getAtasan', 'kodedealer' : kodedealer, 'tipe' : tipe, 'divisi' : divisi },
													type: 'post',
													beforeSend: function(){
														onload = showLoading();
													},
													success: function(output4) {
														onload = hideLoading();
														if (output4!='') {
															$('#IdAtasan').html(output4);
															$('#IdAtasan').removeAttr('disabled');
														} else {
															$('#IdAtasan').html('<option value="">- Pilih -</option>');
															$('#IdAtasan').attr({'disabled':'disabled', 'value':''});
														}
													}
												});	
											}
										});
									} else {
										$('#divisi').html('<option value="">- Pilih -</option>');
										$('#divisi').attr({'disabled':'disabled', 'value':''});
										$('#department').attr({'disabled':'disabled', 'value':''});
										$('#IdAtasan').attr({'disabled':'disabled', 'value':''});
									}
								}
							});
						});
					} else {
						$('#tipe').html('<option value="">- Pilih -</option>');
						$('#tipe').attr({'disabled':'disabled', 'value':''});
						$('#divisi').attr({'disabled':'disabled', 'value':''});
						$('#department').attr({'disabled':'disabled', 'value':''});
						$('#IdAtasan').attr({'disabled':'disabled', 'value':''});
					}
				}
			});
		});
		
		$('#tipe').on('change',function(){
			var kodedealer = $('#KodeDealer').val();
			var tipe = $('#tipe').val();
			$.ajax({ 
				url: 'system/data/home.php',
				data: { action:'getDivisi', 'kodedealer' : kodedealer, 'tipe' : tipe },
				type: 'post',
				beforeSend: function(){
					onload = showLoading();
				},
				success: function(output2) {
					onload = hideLoading();
					if (output2!='') {
						$('#divisi').removeAttr('disabled');
						$('#divisi').html(output2);
						$('#divisi').on('change',function(){
							var divisi = $('#divisi').val();
							if (kodedealer=='2010') {
								$.ajax({ 
									url: 'system/data/home.php',
									data: { action:'getDepartment', 'kodedealer' : kodedealer, 'tipe' : tipe, 'divisi' : divisi },
									type: 'post',
									beforeSend: function(){
										onload = showLoading();
									},
									success: function(output3) {
										onload = hideLoading();
										if (output3) {
											$('#department').removeAttr('disabled');
											$('#department').html(output3);
											$('#department').on('change',function(){
												var department = $('#department').val();
												$.ajax({ 
													url: 'system/data/home.php',
													data: { action:'getAtasan', 'kodedealer' : kodedealer, 'tipe' : tipe, 'divisi' : divisi, 'department' : department },
													type: 'post',
													beforeSend: function(){
														onload = showLoading();
													},
													success: function(output4) {
														onload = hideLoading();
														if (output4!='') {
															$('#IdAtasan').html(output4);
															$('#IdAtasan').removeAttr('disabled');
														} else {
															$('#IdAtasan').html('<option value="">- Pilih -</option>');
															$('#IdAtasan').attr({'disabled':'disabled', 'value':''});
														}
													}
												});
											});
										} else {
											$('#department').html('<option value="">- Pilih -</option>');
											$('#department').attr({'disabled':'disabled', 'value':''});
											$('#IdAtasan').attr({'disabled':'disabled', 'value':''});
										}
									}
								});	
							} else if (kodedealer!='2010') {
								$.ajax({ 
									url: 'system/data/home.php',
									data: { action:'getAtasan', 'kodedealer' : kodedealer, 'tipe' : tipe, 'divisi' : divisi },
									type: 'post',
									beforeSend: function(){
										onload = showLoading();
									},
									success: function(output4) {
										onload = hideLoading();
										if (output4!='') {
											$('#IdAtasan').html(output4);
											$('#IdAtasan').removeAttr('disabled');
										} else {
											$('#IdAtasan').html('<option value="">- Pilih -</option>');
											$('#IdAtasan').attr({'disabled':'disabled', 'value':''});
										}
									}
								});	
							}
						});
					} else {
						$('#divisi').html('<option value="">- Pilih -</option>');
						$('#divisi').attr({'disabled':'disabled', 'value':''});
						$('#department').attr({'disabled':'disabled', 'value':''});
						$('#IdAtasan').attr({'disabled':'disabled', 'value':''});
					}
					
					$.ajax({ 
						url: 'system/data/home.php',
						data: { action:'getStatusUser', 'kodedealer' : kodedealer, 'tipe' : tipe },
						type: 'post',
						beforeSend: function(){
							//onload = showLoading();
						},
						success: function(output3) {
							//onload = hideLoading();
							$('#statususer').html(output3);
							$('#statususer').removeAttr('disabled');
						}
					});
					
				}
			});
		});

		$('#divisi').on('change',function(){
			var kodedealer = $('#KodeDealer').val();
			var tipe = $('#tipe').val();
			var divisi = $('#divisi').val();
			if (kodedealer=='2010') {
				$.ajax({ 
					url: 'system/data/home.php',
					data: { action:'getDepartment', 'kodedealer' : kodedealer, 'tipe' : tipe, 'divisi' : divisi },
					type: 'post',
					beforeSend: function(){
						onload = showLoading();
					},
					success: function(output3) {
						onload = hideLoading();
						if (output3) {
							$('#department').removeAttr('disabled');
							$('#department').html(output3);
							$('#department').on('change',function(){
								var department = $('#department').val();
								$.ajax({ 
									url: 'system/data/home.php',
									data: { action:'getAtasan', 'kodedealer' : kodedealer, 'tipe' : tipe, 'divisi' : divisi, 'department' : department },
									type: 'post',
									beforeSend: function(){
										onload = showLoading();
									},
									success: function(output4) {
										onload = hideLoading();
										if (output4!='') {
											$('#IdAtasan').html(output4);
											$('#IdAtasan').removeAttr('disabled');
										} else {
											$('#IdAtasan').html('<option value="">- Pilih -</option>');
											$('#IdAtasan').attr({'disabled':'disabled', 'value':''});
										}
									}
								});
							});
						} else {
							$('#department').html('<option value="">- Pilih -</option>');
							$('#department').attr({'disabled':'disabled', 'value':''});
							$('#IdAtasan').attr({'disabled':'disabled', 'value':''});
						}
					}
				});	
			} else if (kodedealer!='2010') {
				$.ajax({ 
					url: 'system/data/home.php',
					data: { action:'getAtasan', 'kodedealer' : kodedealer, 'tipe' : tipe, 'divisi' : divisi },
					type: 'post',
					beforeSend: function(){
						onload = showLoading();
					},
					success: function(output4) {
						onload = hideLoading();
						if (output4!='') {
							$('#IdAtasan').html(output4);
							$('#IdAtasan').removeAttr('disabled');
						} else {
							$('#IdAtasan').html('<option value="">- Pilih -</option>');
							$('#IdAtasan').attr({'disabled':'disabled', 'value':''});
						}
					}
				});	
			}
		});

		$('#department').on('change',function(){
			var kodedealer = $('#KodeDealer').val();
			var tipe = $('#tipe').val();
			var divisi = $('#divisi').val();
			var department = $('#department').val();
			$.ajax({ 
				url: 'system/data/home.php',
				data: { action:'getAtasan', 'kodedealer' : kodedealer, 'tipe' : tipe, 'divisi' : divisi, 'department' : department },
				type: 'post',
				beforeSend: function(){
					onload = showLoading();
				},
				success: function(output4) {
					onload = hideLoading();
					if (output4!='') {
						$('#IdAtasan').html(output4);
						$('#IdAtasan').removeAttr('disabled');
					} else {
						$('#IdAtasan').html('<option value="">- Pilih -</option>');
						$('#IdAtasan').attr({'disabled':'disabled', 'value':''});
					}
				}
			});
		});
    });
</script>
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-gray">
			<div class="panel-heading">
				<h4>User</h4>
			</div>
			<form id="validate-form" action="test.php" method="POST" class="form-horizontal row-border">
				<?php if (isset($_POST['new'])) { ?>
					<div class="panel-body collapse in">
						<div class="form-group">
							<label class="col-sm-2 control-label">Kode User</label>
							<div class="col-sm-4">
							    <input type="text" id="kodeUser" class="form-control" maxlength="14">
							</div>
							<label class="col-sm-2 control-label">Nama User</label>
							<div class="col-sm-4">
							    <input type="text" id="namaUser" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">Email</label>
							<div class="col-sm-4">
							    <input type="text" id="Email" class="form-control">
							</div>
							<label class="col-sm-2 control-label">NIK</label>
							<div class="col-sm-4">
							    <input type="text" id="nik" class="form-control">
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">No Telepon</label>
							<div class="col-sm-4">
							    <input type="text" id="no_tlp" class="form-control" value="+62">
							</div>
							<label class="col-sm-2 control-label">Area Dealer</label>
							<div class='col-sm-4'>
								<select id="KodeDealer" class="form-control">
									<?php
										$qry1 = mssql_query("select KodeDealer,NamaDealer from SPK00..dodealer 
											where KodeDealer not in ('2176','2120')",$conns);	
										echo "<option value=''>- Pilih -</option>";
										while($row = mssql_fetch_array($qry1)){
											echo "<option value='".$row['KodeDealer']."'>".$row['NamaDealer']."</option>";
										}
									?>
								</select>
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-2 control-label">Level</label>
							<div class="col-sm-4">
							    <select id="tipe" class="form-control" disabled>
									<option value=''>- Pilih -</option>
								</select>
							</div>
							<label class="col-sm-2 control-label">Divisi</label>
							<div class="col-sm-4">
							    <select id="divisi" class="form-control" disabled>
							    	<option value=''>- Pilih -</option>
							    </select>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">Departemen</label>
							<div class="col-sm-4">
							    <select id="department" class="form-control" disabled>
							    	<option value=''>- Pilih -</option>
							    </select>
							</div>
							<label class="col-sm-2 control-label">Nama Atasan</label>
							<div class="col-sm-4">
							    <select id="IdAtasan" class="form-control" disabled>
							    	<option value=''>- Pilih -</option>
							    </select>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">Tipe Pengajuan</label>
							<div class="col-sm-4">
							    <select id="tipeAju" class="form-control">
							    	<?php
							    		$qry = mssql_query("select Tipe from TipePengajuan order by idTipe asc",$conns);	
										echo "<option value='all'>ALL</option>";
										while($row = mssql_fetch_array($qry)){
											echo "<option value='$row[Tipe]' $pilih>$row[Tipe]</option>";
										}
							    	?>
							    </select>
							</div>
                            <label class="col-sm-2 control-label">Status User</label>
							<div class="col-sm-4">
							    <select id="statususer" class="form-control" disabled>
							    	<option value=''>- Pilih -</option>
							    </select>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">Pos Biaya (D)</label>
							<div class="col-sm-4">
							    <div class="input-group">
									<input type="text" id="posAkunStart" class="form-control" maxlength="8">
									<span class="input-group-addon" style="padding: 2px 8px;min-width: 0;border-right: 1px;">s/d</span>
									<input type="text" id="posAkunEnd" class="form-control" maxlength="8">
								</div>
							</div>
							<label class="col-sm-2 control-label">Pos Hutang (D)</label>
							<div class="col-sm-4">
							    <div class="input-group">
									<input type="text" id="posAkunHtgStart" class="form-control" maxlength="8">
									<span class="input-group-addon" style="padding: 2px 8px;min-width: 0;border-right: 1px;">s/d</span>
									<input type="text" id="posAkunHtgEnd" class="form-control" maxlength="8">
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">Hak Akses</label>
							<?php
								$qry2 = mssql_query("select distinct KategoriMenu,KategoriUrut from sys_menu m where m.active='1' order by KategoriUrut",$conns);
								while($data = mssql_fetch_array($qry2)){
									echo "
										<div class='col-sm-2'>
										    <label style='margin: 0;'>Menu $data[KategoriMenu]</label>";
										    $qry3 = mssql_query("select IdMenu,Menu from sys_menu m where m.active='1' 
										    	and KategoriUrut='".$data['KategoriUrut']."' order by KategoriUrut",$conns);

										    while($dt4 = mssql_fetch_array($qry3)){
										    	$dt5=mssql_num_rows(mssql_query("select IdMenu from sys_permission where Iduser='".$IdUser."' 
										    		and IdMenu='".$dt4['IdMenu']."'"));
												echo "
										    		<div class='checkbox' style='min-height: auto;'>
											    		<label style='margin: 0;'>
															<input value='$dt4[IdMenu]' type='checkbox' name='IdMenu[]'> $dt4[Menu]
														</label>
													</div>
												";
										    }
									echo "
										</div>";
								}
							?>
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
						$r=mssql_fetch_array(mssql_query("select * from sys_user where IdUser='".$id."'"));
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

							$.ajax({ 
								url: 'system/data/home.php',
								data: { action:'getLevel', 'kodedealer' : kodedealer, 'value': '<?php echo $r[tipe]; ?>' },
								type: 'post',
								beforeSend: function(){
									onload = showLoading();
								},
								success: function(output) {
									onload = hideLoading();
									if (output!='') {
										$('#tipe').removeAttr('disabled');
										$('#tipe').html(output);
										// $('#tipe').on('change',function(){
											var tipe = $('#tipe').val();
											$.ajax({ 
												url: 'system/data/home.php',
												data: { action:'getDivisi', 'kodedealer' : kodedealer, 'tipe' : tipe, 'value': '<?php echo $r[divisi]; ?>' },
												type: 'post',
												beforeSend: function(){
													onload = showLoading();
												},
												success: function(output2) {
													onload = hideLoading();
													if (output2!='') {
														$('#divisi').removeAttr('disabled');
														$('#divisi').html(output2);
														// $('#divisi').on('change',function(){
															var divisi = $('#divisi').val();
															if (kodedealer=='2010') {
																$.ajax({ 
																	url: 'system/data/home.php',
																	data: { action:'getDepartment', 'kodedealer' : kodedealer, 'tipe' : tipe, 'divisi' : divisi, 'value': '<?php echo $r[department]; ?>' },
																	type: 'post',
																	beforeSend: function(){
																		onload = showLoading();
																	},
																	success: function(output3) {
																		onload = hideLoading();
																		if (output3) {
																			$('#department').removeAttr('disabled');
																			$('#department').html(output3);
																			// $('#department').on('change',function(){
																				var department = $('#department').val();
																				$.ajax({ 
																					url: 'system/data/home.php',
																					data: { action:'getAtasan', 'kodedealer' : kodedealer, 'tipe' : tipe, 'divisi' : divisi, 'department' : department, 'value': '<?php echo $r[IdAtasan]; ?>' },
																					type: 'post',
																					beforeSend: function(){
																						onload = showLoading();
																					},
																					success: function(output4) {
																						onload = hideLoading();
																						if (output4!='') {
																							$('#IdAtasan').html(output4);
																							$('#IdAtasan').removeAttr('disabled');
																						} else {
																							$('#IdAtasan').html('<option value="">- Pilih -</option>');
																							$('#IdAtasan').attr({'disabled':'disabled', 'value':''});
																						}
																					}
																				});
																			// });
																		} else {
																			$('#department').html('<option value="">- Pilih -</option>');
																			$('#department').attr({'disabled':'disabled', 'value':''});
																			$('#IdAtasan').attr({'disabled':'disabled', 'value':''});
																		}
																	}
																});	
															} else if (kodedealer!='2010') {
																$.ajax({ 
																	url: 'system/data/home.php',
																	data: { action:'getAtasan', 'kodedealer' : kodedealer, 'tipe' : tipe, 'divisi' : divisi, 'value': '<?php echo $r[IdAtasan]; ?>' },
																	type: 'post',
																	beforeSend: function(){
																		onload = showLoading();
																	},
																	success: function(output4) {
																		onload = hideLoading();
																		if (output4!='') {
																			$('#IdAtasan').html(output4);
																			$('#IdAtasan').removeAttr('disabled');
																		} else {
																			$('#IdAtasan').html('<option value="">- Pilih -</option>');
																			$('#IdAtasan').attr({'disabled':'disabled', 'value':''});
																		}
																	}
																});	
															}
														// });
													} else {
														$('#divisi').html('<option value="">- Pilih -</option>');
														$('#divisi').attr({'disabled':'disabled', 'value':''});
														$('#department').attr({'disabled':'disabled', 'value':''});
														$('#IdAtasan').attr({'disabled':'disabled', 'value':''});
													}
												}
											});
										// });
									} else {
										$('#tipe').html('<option value="">- Pilih -</option>');
										$('#tipe').attr({'disabled':'disabled', 'value':''});
										$('#divisi').attr({'disabled':'disabled', 'value':''});
										$('#department').attr({'disabled':'disabled', 'value':''});
										$('#IdAtasan').attr({'disabled':'disabled', 'value':''});
									}
									
									
									$.ajax({ 
										url: 'system/data/home.php',
										data: { action:'getStatusUser', 'kodedealer' : kodedealer, 'tipe' : '<?php echo $r[tipe]; ?>', 'value': '<?php echo $r[idstatus]; ?>' },
										type: 'post',
										beforeSend: function(){
											//onload = showLoading();
										},
										success: function(output3) {
											//onload = hideLoading();
											$('#statususer').html(output3);
											$('#statususer').removeAttr('disabled');
										}
									});
								}
							});
					    });
					</script>
					<div class="panel-body collapse in">
						<div class="form-group">
							<label class="col-sm-2 control-label">Kode User</label>
							<div class="col-sm-4">
							    <input type="text" id="kodeUser" class="form-control" value="<?php echo $r['IdUser']; ?>" readonly>
							</div>
							<label class="col-sm-2 control-label">Nama User</label>
							<div class="col-sm-4">
							    <input type="text" id="namaUser" class="form-control" value="<?php echo $r['namaUser']; ?>">
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">Email</label>
							<div class="col-sm-4">
							    <input type="text" id="Email" class="form-control" value="<?php echo $r['Email']; ?>">
							</div>
							<label class="col-sm-2 control-label">NIK</label>
							<div class="col-sm-4">
							    <input type="text" id="nik" class="form-control" value="<?php echo $r['nik']; ?>">
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">No Telepon</label>
							<div class="col-sm-4">
							    <input type="text" id="no_tlp" class="form-control" value="<?php echo $r['no_tlp']; ?>">
							</div>
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
						</div>
						
						<div class="form-group">
							<label class="col-sm-2 control-label">Level</label>
							<div class="col-sm-4">
							    <select id="tipe" class="form-control" disabled>
									<option value=''>- Pilih -</option>
								</select>
							</div>
							<label class="col-sm-2 control-label">Divisi</label>
							<div class="col-sm-4">
							    <select id="divisi" class="form-control" disabled>
							    	<option value=''>- Pilih -</option>
							    </select>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">Departemen</label>
							<div class="col-sm-4">
							    <select id="department" class="form-control" disabled>
							    	<option value=''>- Pilih -</option>
							    </select>
							</div>
							<label class="col-sm-2 control-label">Nama Atasan</label>
							<div class="col-sm-4">
							    <select id="IdAtasan" class="form-control" disabled>
							    	<option value=''>- Pilih -</option>
							    </select>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">Tipe Pengajuan</label>
							<div class="col-sm-4">
							    <select id="tipeAju" class="form-control">
							    	<?php
							    		$qry = mssql_query("select Tipe from TipePengajuan order by idTipe asc",$conns);	
										echo "<option value='all'>ALL</option>";
										while($row = mssql_fetch_array($qry)){
											echo "<option value='$row[Tipe]' $pilih>$row[Tipe]</option>";
										}
							    	?>
							    </select>
							</div>
                            <label class="col-sm-2 control-label">Status User</label>
							<div class="col-sm-4">
							    <select id="statususer" class="form-control">
							    	<?php
							    		$qry = mssql_query("select id, namastatus from StatusUser",$conns);	
										while($row = mssql_fetch_array($qry)){
											$pilih = "";
											if (strtolower($row['id'])==$r['idtstatus']) {
												$pilih = "selected";
											} 
											echo "<option value='$row[id]' $pilih>$row[namastatus]</option>";
										}
							    	?>
							    </select>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">Pos Biaya (D)</label>
							<div class="col-sm-4">
							    <div class="input-group">
									<input type="text" id="posAkunStart" class="form-control" maxlength="8" value="<?php echo $r['posAkunStart']; ?>">
									<span class="input-group-addon" style="padding: 2px 8px;min-width: 0;border-right: 1px;">s/d</span>
									<input type="text" id="posAkunEnd" class="form-control" maxlength="8" value="<?php echo $r['posAkunEnd']; ?>">
								</div>
							</div>
							<label class="col-sm-2 control-label">Pos Hutang (D)</label>
							<div class="col-sm-4">
							    <div class="input-group">
									<input type="text" id="posAkunHtgStart" class="form-control" maxlength="8" value="<?php echo $r['posAkunHtgStart']; ?>">
									<span class="input-group-addon" style="padding: 2px 8px;min-width: 0;border-right: 1px;">s/d</span>
									<input type="text" id="posAkunHtgEnd" class="form-control" maxlength="8" value="<?php echo $r['posAkunHtgEnd']; ?>">
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">Hak Akses</label>
							<?php
								$qry2 = mssql_query("select distinct KategoriMenu,KategoriUrut from sys_menu m where m.active='1' order by KategoriUrut",$conns);
								while($data = mssql_fetch_array($qry2)){
									echo "
										<div class='col-sm-2'>
										    <label style='margin: 0;'>Menu $data[KategoriMenu]</label>";
										    $qry3 = mssql_query("select IdMenu,Menu from sys_menu m where m.active='1' 
										    	and KategoriUrut='".$data['KategoriUrut']."' order by KategoriUrut",$conns);

										    while($dt4 = mssql_fetch_array($qry3)){
										    	$dt5=mssql_num_rows(mssql_query("select IdMenu from sys_permission where Iduser='".$r['IdUser']."' 
										    		and IdMenu='".$dt4['IdMenu']."'"));
												if ($dt5=='1') { $plhAkses="checked"; } else { $plhAkses=""; }
										    	echo "
										    		<div class='checkbox' style='min-height: auto;'>
											    		<label style='margin: 0;'>
															<input value='$dt4[IdMenu]' type='checkbox' name='IdMenu[]' $plhAkses> $dt4[Menu]
														</label>
													</div>
												";
										    }
									echo "
										</div>";
								}
							?>
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