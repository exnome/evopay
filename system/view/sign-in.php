<?php 
	$nik = isset($_REQUEST['nik']) ? $_REQUEST['nik'] : null;
	$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;
	$nobuk = isset($_REQUEST['nobuk']) ? $_REQUEST['nobuk'] : null; 
	$tipe_validasi = isset($_REQUEST['tipe_validasi']) ? $_REQUEST['tipe_validasi'] : null; 
	//if ($nobuk) {
	//	header("Location: https://nasmoco.net/h355frttRIJkdFz5bM3m/index.php?nobuk=".$nobuk);
	//}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
	    <meta charset="utf-8">
	    <title>.: Sign In :.</title>
	    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	    <meta name="author" content="Irwan Prajito">
		<link rel="stylesheet" href="assets/css/styles.min.css?=113">
		<link rel="shorcut icon" href="assets/img/favicon.ico" type="image/x-icon">
		<link rel="stylesheet" href="assets/css/styles.css?=113">
        <link rel='stylesheet' type='text/css' href='assets/css/wann.css' />
        <script type="text/javascript" src="assets/js/wann.js"></script>
        <script type="text/javascript" src="assets/plugins/flexii/jquery.min.js"></script>
        <?php if ($nik) { ?>
            <script type="text/javascript"> 
                jQuery(document).ready(function($) { 
                    onload = loginIntra();
                });
            </script>
        <?php } ?>
        <script type="text/javascript">
			jQuery(document).ready(function($) {
				// $(document).keypress(
				// 	function(event){
				// 	 	if (event.which == '13') {
				// 	    	onload = login();
				// 	  	}
				// });
				var input1 = document.getElementById("username");
				input1.addEventListener("keyup", function(event) {
					if (event.keyCode === 13) {
						event.preventDefault();
						document.getElementById("Sign").click();
					}
				});
				var input2 = document.getElementById("password");
				input2.addEventListener("keyup", function(event) {
					if (event.keyCode === 13) {
						event.preventDefault();
						document.getElementById("Sign").click();
					}
				});
        		$('#Sign').click(function(){
					onload = login();
				});
        	});
        	function login(){
        		var username = $('#username').val();
				var password = $('#password').val();
				var evo_id 	 = $('#evo_id').val();
				$.ajax({ 
				    url: 'system/control/login.php',
				    data: { action:'web', 'username': username,'password': password},
				    type: 'post',
				    beforeSend: function(){
				    	onload = showLoading();
				    },
				    success: function(output) {
				    	onload = hideLoading();
						var data = output.split('#',3);
						if (data[0]==1) {
							bootbox.dialog({
								closeButton : false,
								className : "resizeSign",
								message: data[2],
								title: data[1],
								timeOut : 10000
							});
							<?php 
							if ($nobuk) {
								echo "document.forms['Form'].submit();";
							} else {
								echo 'document.location.href="home";';
							}
						?>
						} else {
							bootbox.dialog({
								closeButton : false,
								className : "resizeSign",
								message: data[2],
								title: data[1],
								buttons: {
									main: {
										label: "Ok",
										className: "btn-sm btn-primary",
										callback: function() {
											if (data[0]=='1') {
												<?php 
													if ($nobuk) {
														echo "document.forms['Form'].submit();";
													} else {
														echo 'document.location.href="home";';
													}
												?>
											} else {
												$('#username').val('');
												$('#password').val('');
											}
										}
									}
								}
							});
						}
						
						/*
				       	var data = output.split('#',3);
				        bootbox.dialog({
						    closeButton : false,
				        	className : "resizeSign",
						    message: data[2],
						    title: data[1],
						    buttons: {
						        main: {
						            label: "Ok",
						            className: "btn-sm btn-primary",
						            callback: function() {
						                if (data[0]=='1') {
						                	<?php 
						                		if ($nobuk) {
						                			echo "document.forms['Form'].submit();";
						                		} else {
						                			echo 'document.location.href="home";';
						                		}
						                	?>
						                } else {
						                	$('#username').val('');
											$('#password').val('');
						                }
						            }
						        }
						    },
    						timeOut : 2000
						});
						*/
					
							
					}
				});
        	}

        	function loginIntra(){
        		var nik = "<?php echo $_REQUEST['nik']; ?>";
        		var id = "<?php echo $_REQUEST['id']; ?>";
        		var tipe_validasi = "<?php echo $_REQUEST['tipe_validasi']; ?>";
				$.ajax({ 
				    url: 'system/control/login.php',
				    data: { action:'app', 'nik': nik, 'id': id, 'tipe_validasi': tipe_validasi },
				    type: 'post',
				    beforeSend: function(){
				    	onload = showLoading();
				    },
				    success: function(output) {
				    	onload = hideLoading();
				       	var data = output.split('#');
						if (data[0]==1) {
							bootbox.dialog({
								closeButton : false,
								className : "resizeSign",
								message: data[2],
								title: data[1],
								timeOut : 10000
							});

							if (data[4]=='biasa') {
								//$('#id').val(data[3]);
								$('#chkIntra').val(data[3]);
								document.forms['Form'].submit();
							} else {
								document.location.href="home";
							}
							
						} else {
							bootbox.dialog({
								closeButton : false,
								className : "resizeSign",
								message: data[2],
								title: data[1],
								buttons: {
									main: {
										label: "Ok",
										className: "btn-sm btn-primary",
										callback: function() {
											if (data[0]=='1') {
												//$('#chkIntra').val(data[3]);
												//document.forms['Form'].submit();
												// document.location.href="home";
												if (data[4]=='biasa') {
													//$('#id').val(data[3]);
													$('#chkIntra').val(data[3]);
													document.forms['Form'].submit();
												} else {
													document.location.href="home";
												}
											} else {
												$('#username').val('');
												$('#password').val('');
											}
										}
									}
								},
							});
						}
						
						
				        /*bootbox.dialog({
						    closeButton : false,
				        	className : "resizeSign",
						    message: data[2],
						    title: data[1],
						    buttons: {
						        main: {
						            label: "Ok",
						            className: "btn-sm btn-primary",
						            callback: function() {
						                if (data[0]=='1') {
						                	//$('#chkIntra').val(data[3]);
						                	//document.forms['Form'].submit();
						                	// document.location.href="home";
						                	if (data[4]=='biasa') {
												//$('#id').val(data[3]);
												$('#chkIntra').val(data[3]);
												document.forms['Form'].submit();
											} else {
												document.location.href="home";
											}
										} else {
						                	$('#username').val('');
											$('#password').val('');
						                }
						            }
						        }
						    },
    						timeOut : 2000
						});*/
					}
				});
        	}
        </script>
	</head>

	<body class="focusedform">
		<div class="verticalcenter">
			<!-- <a href="#"><img src="assets/img/logo-big.png" alt="Logo" class="brand"/></a> -->
			<div class="panel panel-primary">
				<?php if ($nik) { ?>
				<form action="transaksi-validasi-modify" class="form-horizontal" id="Form" method="POST" style="margin-bottom: 0px !important;">
					<input type="hidden" name="modify" value="">
					<input type='hidden' id='chkIntra' name='id[]'/><!--
					<input type='hidden' id='id' name='id'/>-->
				<?php } else if ($nobuk) { ?>
				<form action="report-reportpengajuan-modify" class="form-horizontal" id="Form" method="POST" style="margin-bottom: 0px !important;">
					<input type="hidden" name="modify" value="pengajuan-detail">
					<input type="hidden" name="nobuk" value="<?php echo $nobuk; ?>">
				<?php } else { ?>
				<form action="#" method="POST" class="form-horizontal" style="margin-bottom: 0px !important;">
				<?php } ?>
					<div class="panel-body">
						<h4 class="text-center" style="margin-bottom: 25px;">Log in Evopay</h4>
						<div class="form-group">
							<div class="col-sm-12">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-user"></i></span>
									<input type="text" class="form-control" id="username" placeholder="Username">
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-12">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-lock"></i></span>
									<input type="password" class="form-control" id="password" placeholder="Password">
								</div>
							</div>
						</div>
					</div>
					<div class="panel-footer">
						<div class="pull-right">
							<button type="button" class="btn btn-primary" id="Sign">Log In</button>
						</div>
					</div>
				</form>
			</div>
		</div>
		<script type='text/javascript' src='assets/js/jquery-1.10.2.min.js'></script> 
        <script type='text/javascript' src='assets/js/jqueryui-1.10.3.min.js'></script> 
        <script type='text/javascript' src='assets/js/bootstrap.min.js'></script> 
        <script type='text/javascript' src='assets/plugins/bootbox/bootbox.min.js'></script>
	</body>
</html>