<?php require_once ('system/inc/permission.php'); ?>
<?php $r = mssql_fetch_array(mssql_query("select IdUser,namaUser,passWord,tipe,Email from sys_user where IdUser = '".$_SESSION['UserID']."'")); ?>
<script type="text/javascript">
	jQuery(document).ready(function($) {
		$('#editNama').click(function(){
			$('#namaUser').removeAttr("readonly");
			$('#editNama').css("display","none");
			$('#cancelNama').css("display","inline");
			$('#saveNama').css("display","inline");
		});
		$('#cancelNama').click(function(){
			$('#namaUser').attr("readonly","readonly");
			$('#editNama').css("display","inline");
			$('#cancelNama').css("display","none");
			$('#saveNama').css("display","none");
		});
		$('#saveNama').click(function(){
			var IdUser = $('#IdUser').val();
			var namaUser = $('#namaUser').val();
			$.ajax({ 
			    url: 'system/control/account.php',
			    data: { action:'change-nama', 'IdUser': IdUser, 'namaUser': namaUser },
			    type: 'post',
			    beforeSend: function(){
			    	onload = showLoading();
			    },
			    success: function(output) {
			    	onload = hideLoading();
			        bootbox.dialog({
			        	closeButton : false,
			        	className : "resize",
					    message: output,
					    title: "Account",
					    buttons: {
					        main: {
					            label: "Ok",
					            className: "btn-sm btn-primary",
					            callback: function() {
					                document.location.href="master-account";
					            }
					        }
					    }
					});
				}
			});
		});

		$('#editEmail').click(function(){
			$('#Email').removeAttr("readonly");
			$('#editEmail').css("display","none");
			$('#cancelEmail').css("display","inline");
			$('#saveEmail').css("display","inline");
		});
		$('#cancelEmail').click(function(){
			$('#Email').attr("readonly","readonly");
			$('#editEmail').css("display","inline");
			$('#cancelEmail').css("display","none");
			$('#saveEmail').css("display","none");
		});
		$('#saveEmail').click(function(){
			var IdUser = $('#IdUser').val();
			var Email = $('#Email').val();
			$.ajax({ 
			    url: 'system/control/account.php',
			    data: {action:'change-email', 'IdUser': IdUser, 'Email': Email},
			    type: 'post',
			    beforeSend: function(){
			    	onload = showLoading();
			    },
			    success: function(output) {
			    	onload = hideLoading();
			        bootbox.dialog({
			        	closeButton : false,
			        	className : "resize",
					    message: output,
					    title: "Account",
					    buttons: {
					        main: {
					            label: "Ok",
					            className: "btn-sm btn-primary",
					            callback: function() {
					                document.location.href="master-account";
					            }
					        }
					    }
					});
				}
			});
		});

		$('#editPass').click(function(){
			$('#currpass').removeAttr("readonly");
			$('#newpass').removeAttr("readonly");
			$('#newpass').css("display","inline");
			$('#currpass').attr("placeholder","Current Password");
			$('#editPass').css("display","none");
			$('#cancelPass').css("display","inline");
			$('#savePass').css("display","inline");
		});
		$('#cancelPass').click(function(){
			$('#currpass').attr("readonly","readonly");
			$('#newpass').attr("readonly","readonly");
			$('#newpass').css("display","none");
			$('#currpass').attr("placeholder","");
			$('#editPass').css("display","inline");
			$('#cancelPass').css("display","none");
			$('#savePass').css("display","none");
		});
		$('#savePass').click(function(){
			var IdUser = $('#IdUser').val();
			var oldpass = $('#oldpass').val();
			var currpass = $('#currpass').val();
			var newpass = $('#newpass').val();
			$.ajax({ 
			    url: 'system/control/account.php',
			    data: { action:'change-password', 'IdUser': IdUser, 'oldpass': oldpass, 'currpass': currpass, 'newpass': newpass },
			    type: 'post',
			    beforeSend: function(){
			    	onload = showLoading();
			    },
			    success: function(output) {
			    	onload = hideLoading();
			        var data = output.split("#",2);
			        bootbox.dialog({
			        	closeButton : false,
			        	className : "resize",
					    message: data[1],
					    title: "Account",
					    buttons: {
					        main: {
					            label: "Ok",
					            className: "btn-sm btn-primary",
					            callback: function() {
					                if (data[0]=='0') {
					                	$('#currpass').val('');
										$('#newpass').val('');
					                } else {
					                	document.location.href="system/control/logout.php";
					                }
					            }
					        }
					    }
					});
				}
			});
		});
	});
</script>
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-gray">
			<div class="panel-heading">
				<h4>Account <?php echo $_SESSION['UserID']; ?></h4>
			</div>
			<form id="validate-form" action="test.php" method="POST" class="form-horizontal row-border">
				<div class="panel-body collapse in">
					<div class="form-group">
						<label class="col-sm-2 control-label">Kode User</label>
						<div class="col-sm-4">
						    <input type="text" name="IdUser" id="IdUser" class="form-control" value="<?php echo $r[0]; ?>" readonly>
						</div>
						<label class="col-sm-2 control-label">Nama User</label>
						<div class="col-sm-4">
							<div class="input-group"> 
								<input class="form-control flex" name="namaUser" id="namaUser" type="text" value="<?php echo $r[1]; ?>" readonly> 
								<span class="input-group-btn"> 
									<button id="editNama" type="button" class="btn btn-sm btn-more2"> 
										<i class="fa fa-pencil"></i> 
									</button>
									<button id="cancelNama" type="button" class="btn btn-sm btn-more2" style="display:none"> 
										<i class="fa fa-times"></i> 
									</button>
									<button id="saveNama" type="button" class="btn btn-sm btn-more2" style="display:none"> 
										<i class="fa fa-check"></i> 
									</button>
								</span> 
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">Tipe</label>
						<div class="col-sm-4">
						    <input type="text" name="tipe" id="tipe" class="form-control"  value="<?php echo $r[3]; ?>" readonly>
						</div>
						<label class="col-sm-2 control-label">Area Dealer</label>
						<div class="col-sm-4">
						    <select name="areadealer" id="areadealer" class="form-control" readonly>
						    	<?php
						    		$sql = "
										select NamaDealer from sys_userarea a 
										inner join spk00..dodealer b on a.KodeDealer=b.KodeDealer
										where IdUser = '".$_SESSION['UserID']."'
						    		";
						    		$rsl = mssql_query($sql);
						    		while ($dt = mssql_fetch_array($rsl)) {
						    			echo "<option>$dt[NamaDealer]</option>";
						    		}
						    	?>
						    </select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">Email</label>
						<div class="col-sm-4">
						    <div class="input-group"> 
								<input class="form-control flex" name="Email" id="Email" type="text" value="<?php echo $r[4]; ?>" readonly> 
								<span class="input-group-btn"> 
									<button id="editEmail" type="button" class="btn btn-sm btn-more2"> 
										<i class="fa fa-pencil"></i> 
									</button>
									<button id="cancelEmail" type="button" class="btn btn-sm btn-more2" style="display:none"> 
										<i class="fa fa-times"></i> 
									</button>
									<button id="saveEmail" type="button" class="btn btn-sm btn-more2" style="display:none"> 
										<i class="fa fa-check"></i> 
									</button>
								</span> 
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">Password</label>
						<div class="col-sm-4">
						    <div class="input-group"> 
								<input type="hidden" id="oldpass" class="form-control" value = "<?php echo $r[2]; ?>" readonly>
								<input type="password" id="currpass" class="form-control" readonly>
								<span class="input-group-btn"> 
									<button id="editPass" type="button" class="btn btn-sm btn-more2"> 
										<i class="fa fa-pencil"></i> 
									</button>
									<button id="cancelPass" type="button" class="btn btn-sm btn-more2" style="display:none"> 
										<i class="fa fa-times"></i> 
									</button>
								</span> 
							</div>
						</div>
					</div>
					<div class="form-group">
					    <label class="col-sm-2 control-label">&nbsp;</label>
					    <div class="col-sm-4">
                        	 <div class="input-group"> 
                                <input type="password" id="newpass" class="form-control" placeholder="New Password" style="display:none" readonly>
                                <span class="input-group-btn"> 
                                        <button id="savePass" type="button" class="btn btn-sm btn-more2" style="display:none"> 
                                            <i class="fa fa-check"></i> 
                                        </button>
                                    </span> 
                             </div>   
					    </div>
					</div>
				</div>
				<div class="panel-footer">
					<div class="row">
					    <div class="col-sm-10 col-sm-offset-2">&nbsp;</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>