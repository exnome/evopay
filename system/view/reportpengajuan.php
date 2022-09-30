<?php require_once ('system/inc/permission.php'); ?>
<link rel="stylesheet" type="text/css" href="assets/plugins/flexii/css/flexigrid.pack.css" />
<div class="row">
	<form action="report-reportpengajuan-modify" id="Form" method="POST">
	    <span id="post"></span>
	    <input type="hidden" id="IdUser" value="<?php echo $_SESSION['UserID'] ?>">
	    <input type="hidden" id="level" value="<?php echo $_SESSION['level'] ?>">
	    <div class="col-sm-12">
	        <table class="flexme3" style="display: none"></table>
	        <script type="text/javascript">
	        	jQuery(document).ready(function($) {
	        		var IdUser = $("#IdUser").val();
                    $(".flexme3").flexigrid({
					    dataType : 'xml',
						/* No Evopay	
						User Aju	
						Status	
						Tgl Aju	
						Tgl Validasi	
						Lunas/Blm	
						No Pembayaran	
						Tgl Bayar	
						Metode Byr	
						Tipe Aju	
						No Tagihan	
						Kode Vendor	Nama Vendor	Beneficary Account	
						Nama Bank Penerima	Nama Pemilik	Nama Alias	Email Penerima	Nama Bank Pengirim	Transfer From Account	Real. Nominal	
						NPWP	No Faktur	Dpp	Ppn	Pos Biaya	Nominal	Pph	Nilai Pph	Total Bayar	
						Keterangan	Stat Validator	Accept/Reject	Note Section Head	Note Dept. Head	Note Div. Head	Note Direksi	Note Dept. Head Finance	Note Div. Head FAST	
						Status CSV	Department	Dealer
						*/
					    colModel : [ 
					        {display : '#',name : 'evo_id', width : 30, sortable : false, align : 'center'},
							{display : 'No Evo Pay',name : 'nobukti',width : 120,sortable : false, align : 'left'},
							{display : 'User Aju',name : 'userAju',width : 100,sortable : false,align : 'left'},
							{display : 'Stat. Aju',name : '',width : 150, sortable : false, align : 'left'},
							{display : 'Tgl Aju',name : 'tgl_pengajuan', width : 80, sortable : false, align : 'left'},
							// Tgl Validasi
							{display : 'Tgl Tagihan',name : '',width : 80,sortable : false,align : 'left'},
							{display : 'Lunas/Blm',name : '',width : 100,sortable : false,align : 'left'},
							// No Pembayara
							{display : 'No Pembayaran',name : '',width : 100,sortable : false,align : 'left'},
							{display : 'Tgl Bayar',name : 'tgl_bayar',width : 80,sortable : false,align : 'left'},
							{display : 'Metode Byr',name : 'metode_bayar',width : 80,sortable : false,align : 'left'},
							{display : 'Tipe Aju',name : 'tipe',width : 80,sortable : false,align : 'left'},							
							{display : 'No Tagihan',name : '',width : 100,sortable : false,align : 'left'},
							{display : 'Kode Vendor',name : 'kode_vendor',width : 100,sortable : false,align : 'left'},
							{display : 'Nama Vendor',name : 'namaVendor',width : 250,sortable : false,align : 'left'},
							{display : 'Beneficary Account',name : 'benificary_account',width : 100,sortable : false,align : 'left'},
							
							{display : 'Nama Bank Penerima',name : 'nama_bank',width : 100,sortable : false,align : 'left'},
							{display : 'Nama Rekening',name : 'nama_pemilik',width : 100,sortable : false,align : 'left'},
							{display : 'Nama Alias',name : 'nama_alias',width : 100,sortable : false,align : 'left'},
							{display : 'Email Penerima',name : 'email_penerima',width : 100,sortable : false,align : 'left'},
							{display : 'Nama Bank Pengirim',name : 'nama_bank_pengirim',width : 100,sortable : false,align : 'left'},
							{display : 'Transfer From Account',name : 'tf_from_account',width : 100,sortable : false,align : 'left'},
							{display : 'Real. Nominal',name : '',width : 100,sortable : false,align : 'left'},
							
							{display : 'NPWP',name : '',width : 100,sortable : false,align : 'left'},
							{display : 'No Faktur',name : 'no_fj',width : 100,sortable : false,align : 'left'},
							{display : 'Dpp',name : '',width : 100,sortable : false,align : 'left'},
							{display : 'Ppn',name : '',width : 100,sortable : false,align : 'left'},
							{display : 'Pos Biaya',name : '',width : 100,sortable : false,align : 'left'},
							{display : 'Nominal',name : '',width : 100,sortable : false,align : 'left'},
							{display : 'Pph',name : '',width : 100,sortable : false,align : 'left'},
							{display : 'Nilai Pph',name : '',width : 100,sortable : false,align : 'left'},
							{display : 'Total Bayar',name : '',width : 100,sortable : false,align : 'left'},
							
							{display : 'Keterangan',name : '',width : 100,sortable : false,align : 'left'},
							{display : 'Stat Validator',name : '',width : 100,sortable : false,align : 'left'},
							{display : 'Accept/Reject',name : '',width : 100,sortable : false,align : 'left'},
							
					        <?php if ($user['KodeDealer']=='2010') { ?>
					        {display : 'Note Section Head',name : '',width : 100,sortable : false,align : 'left'},
							{display : 'Note Dept. Head',name : '',width : 100,sortable : false,align : 'left'},
							{display : 'Note Div. Head',name : '',width : 100,sortable : false,align : 'left'},
							{display : 'Note Direksi',name : '',width : 100,sortable : false,align : 'left'},
							{display : 'Note Direksi 2',name : '',width : 100,sortable : false,align : 'left'},
							{display : 'Note Dept. Head Finance',name : '',width : 100,sortable : false,align : 'left'},
							{display : 'Note Div. Head FAST',name : '',width : 100,sortable : false,align : 'left'},
					    	
							<?php } else { ?>
					    	{display : 'Note Sect Head',name : '',width : 100,sortable : false,align : 'left'},
							{display : 'Note Adh',name : '',width : 100,sortable : false,align : 'left'},
							{display : 'Note BM',name : '',width : 100,sortable : false,align : 'left'},
							{display : 'Note OM',name : '',width : 100,sortable : false,align : 'left'},
					    	
							<?php } ?>
							
							{display : 'Status CSV',name : 'noCsv', width : 120, sortable : false,align : 'left'},
							{display : 'Department',name : '',width : 100,sortable : false,align : 'left'},
							{display : 'Dealer',name : 'kodedealer',width : 300,sortable : false,align : 'left'},
							
					    ],
					    buttons : [ 
					        { name : 'Export XLS', bclass : 'edit', onpress : button },
					        <?php
					        	if ($_SESSION['level']=='KASIR') {
					        		echo "{ name : 'CIMB Biz C', bclass : 'delete', onpress : button },";
									echo "{ name : 'Mandiri MCM', bclass : 'add', onpress : button },";
					        	}
					        ?>
							{ name : 'Search', bclass : 'search', onpress : button }
					    ],
					    title : 'Report Pengajuan',
					    sortname : "tgl_pengajuan",
					    sortorder : "desc",
					    usepager : true,
					    useRp : true,
					    rp : 10,
					    rpOptions: [10, 20, 50, 100],
					    showToggleBtn : false,
					    width : 'auto',
					    height : '300',
						onSuccess: function(){
						    onload = hideLoading();
						}
					});
                    $('.flexme3').flexOptions({
						url : 'system/data/reportpengajuan.php',
						newp: 1,
						params:[ { name:'IdUser', value: IdUser } ]
					}).flexReload();
					function button(com) {
					    if (com == 'Export XLS') {
							var generallen = $("input[name='id[]']:checked").length;
							
							if (generallen>0) {
								var id = "";
								$("input[name='id[]']:checked").each(function(){
								    id += $(this).val()+',';
								});
								id = id.slice(0,-1);
								window.open('system/view/reportpengajuan_xls.php?id='+id+'&IdUser='+IdUser);
								//window.open('system/view/reportpengajuan_xls.php?nobukti='+nobukti+'&startDate='+startDate+'&endDate='+endDate+'&validasi='+validasi+'&noCsv='+noCsv+'&txtSearch='+txtSearch+'&IdUser='+IdUser+'&divisi='+divisi+'&department='+department+'&id='+id);
							} else {
								var nobukti    = $("#nobukti2").val();
								var startDate  = $("#startDate2").val();
								var endDate    = $("#endDate2").val();
								var validasi   = $("#validasi2").val();
								var noCsv      = $("#noCsv2").val();
								var txtSearch  = $("#txtSearch2").val();
								var divisi  = $("#divisi2").val();
								var department  = $("#department2").val();
								window.open('system/view/reportpengajuan_xls.php?nobukti='+nobukti+'&startDate='+startDate+'&endDate='+endDate+'&validasi='+validasi+'&noCsv='+noCsv+'&txtSearch='+txtSearch+'&divisi='+divisi+'&department='+department+'&IdUser='+IdUser);
							}
							return true;
					    } else if (com == 'CIMB Biz C') {
					    	var generallen = $("input[name='id[]']:checked").length;
					        if (generallen==0) {
					            onload = myBad('Report Pengajuan');
					            return false;
					        } else {
					            var id = $("input[name='id[]']").length;
								var evo = ""; var error = ""; var noCsv = ""; var metode = "";
								for (var i = 1; i <= id; i++) {
									if ($("#chk_"+i).is(":checked")) {
										var chk = $("#chk_"+i).val();
										var stat = $("#stat_"+i).val();
										var metod = $("#metod_"+i).val();
										if (stat!='') {
											error = "1";
											if (noCsv!=stat) {
												noCsv = stat;
												metode = metod;
											}
										} else {
											evo += chk+",";
										}
									}
								}
								if (error!='' && metode=='CIMB Biz C') {
									$("#post").html('<input type="hidden" name="modify" value="CIMB Biz C"><input type="hidden" name="nobukti" value="'+noCsv+'">');
									document.forms['Form'].submit();
									error = "";
								} else if (error!='' && metode!='CIMB Biz C') {
									onload = needValue('Report Pengajuan','Sudah Upload!');
								} else {
									var evo_id = evo.slice(0,-1);
									$.ajax({ 
										url: 'system/control/pengajuan.php',
										data: { action:'transfer', 'evo_id': evo_id, 'metode': 'CIMB Biz C'},
										type: 'post',
										beforeSend: function(){
											onload = showLoading();
										},
										success: function(output) {
											onload = hideLoading();
											$("#post").html('<input type="hidden" name="modify" value="CIMB Biz C"><input type="hidden" name="nobukti" value="'+output+'">');
											document.forms['Form'].submit();
										}
									});
								}
					        }
					    } else if (com == 'Mandiri MCM') {
							var generallen = $("input[name='id[]']:checked").length;
					        if (generallen==0) {
					        	onload = myBad('Report Pengajuan');
					            return false;
							} else {
								var id = $("input[name='id[]']").length;
								var evo = ""; var error = ""; var noCsv = ""; var metode = "";
								for (var i = 1; i <= id; i++) {
									if ($("#chk_"+i).is(":checked")) {
										var chk = $("#chk_"+i).val();
										var stat = $("#stat_"+i).val();
										var metod = $("#metod_"+i).val();
										if (stat!='') {
											error = "1";
											if (noCsv!=stat) {
												noCsv = stat;
												metode = metod;
											}
										} else {
											evo += chk+",";
										}
									}
								}
								if (error!='' && metode=='Mandiri MCM') {
									$("#post").html('<input type="hidden" name="modify" value="Mandiri MCM"><input type="hidden" name="nobukti" value="'+noCsv+'">');
									document.forms['Form'].submit();
									error = "";
								} else if (error!='' && metode!='Mandiri MCM') {
									onload = needValue('Report Pengajuan','Sudah Upload!');
								} else {
									var evo_id = evo.slice(0,-1);
									$.ajax({ 
										url: 'system/control/pengajuan.php',
										data: { action:'transfer', 'evo_id': evo_id, 'metode': 'Mandiri MCM'},
										type: 'post',
										beforeSend: function(){
											onload = showLoading();
										},
										success: function(output) {
											onload = hideLoading();
											$("#post").html('<input type="hidden" name="modify" value="Mandiri MCM"><input type="hidden" name="nobukti" value="'+output+'">');
											document.forms['Form'].submit();
										}
									});
								}
							}
					    } else if (com == 'Search') {
					        bootbox.dialog({
							    //message: '<form action="" method="post" class="form-horizontal"> <div class="form-group" style="margin-bottom: 2px;"> <label class="col-md-4 control-label">No Evo Pay</label> <div class="col-md-8"><input type="text" id="nobukti" name="nobukti" class="form-control"/></div></div><div class="form-group" style="margin-bottom: 2px;"> <label class="col-md-4 control-label">No Tagihan</label> <div class="col-md-8"><input type="text" id="notagihan" name="notagihan" class="form-control"/></div></div><div class="form-group" style="margin-bottom: 2px;"> <label class="col-md-4 control-label">Status CSV</label> <div class="col-md-4"> <select id="statCsv" class="form-control"> <option value="">- All -</option> <option value="0">Belom Upload</option> <option value="1">Sudah Upload</option> </select> </div><div class="col-md-4"> <input type="text" id="noCsv" name="noCsv" class="form-control" disabled/> </div></div><div class="form-group" style="margin-bottom: 2px;"> <label class="col-md-4 control-label">Tgl Pengajuan</label> <div class="col-md-4"><input type="date" name="startDate" id="startDate" class="form-control" value="<?php echo date('Y-m-01'); ?>"/></div><div class="col-md-4"><input type="date" name="endDate" id="endDate" class="form-control" value="<?php echo date('Y-m-t'); ?>"/></div></div><div class="form-group" style="margin-bottom: 2px;"> <label class="col-md-4 control-label">Validasi</label> <div class="col-md-8"> <select id="validasi" class="form-control"> <option value="">- All -</option> <option value="0">Belom Proses</option> <option value="1">Accept</option> <option value="2">Reject</option> </select> </div></div><div class="form-group" style="margin-bottom: 2px;"> <label class="col-md-4 control-label">Divisi</label> <div class="col-md-8"> <select id="divisi" class="form-control"> <?php echo getDivisi("".$IdUser.""); ?> </select> </div></div><div class="form-group" style="margin-bottom: 2px;"> <label class="col-md-4 control-label">Department</label> <div class="col-md-8"> <select id="department" class="form-control"> <?php echo getDept("".$IdUser.""); ?> </select> </div></div><div class="form-group" style="margin-bottom: 2px;"> <label class="col-md-4 control-label">Search</label> <div class="col-md-8"><input type="text" id="txtSearch" name="txtSearch" class="form-control"/></div></div></form>',
								message: '<form action="" method="post" class="form-horizontal"> <div class="form-group" style="margin-bottom: 2px;"> <label class="col-md-4 control-label">No Evo Pay</label> <div class="col-md-8"><input type="text" id="nobukti" name="nobukti" class="form-control"/></div></div><div class="form-group" style="margin-bottom: 2px;"> <label class="col-md-4 control-label">No Tagihan</label> <div class="col-md-8"><input type="text" id="notagihan" name="notagihan" class="form-control"/></div></div><div class="form-group" style="margin-bottom: 2px;"> <label class="col-md-4 control-label">Status Bayar</label> <div class="col-md-4"> <select id="statBayar" class="form-control"> <option value="">- All -</option> <option value="0">Belum Bayar</option> <option value="1">Sudah Bayar</option> </select> </div><div class="col-md-4"> <input type="hidden" id="noCsv" name="noCsv" class="form-control" disabled/> </div></div><div class="form-group" style="margin-bottom: 2px;"> <label class="col-md-4 control-label">Tgl Pengajuan</label> <div class="col-md-4"><input type="date" name="startDate" id="startDate" class="form-control" value="<?php echo date('Y-m-01'); ?>"/></div><div class="col-md-4"><input type="date" name="endDate" id="endDate" class="form-control" value="<?php echo date('Y-m-t'); ?>"/></div></div><div class="form-group" style="margin-bottom: 2px;"> <label class="col-md-4 control-label">Validasi</label> <div class="col-md-8"> <select id="validasi" class="form-control"> <option value="">- All -</option> <option value="0">Belom Proses</option> <option value="1">Accept</option> <option value="2">Reject</option> </select> </div></div><div class="form-group" style="margin-bottom: 2px;"> <label class="col-md-4 control-label">Divisi</label> <div class="col-md-8"> <select id="divisi" class="form-control"> <?php echo getDivisi("".$IdUser.""); ?> </select> </div></div><div class="form-group" style="margin-bottom: 2px;"> <label class="col-md-4 control-label">Department</label> <div class="col-md-8"> <select id="department" class="form-control"> <?php echo getDept("".$IdUser.""); ?> </select> </div></div><div class="form-group" style="margin-bottom: 2px;"> <label class="col-md-4 control-label">Search</label> <div class="col-md-8"><input type="text" id="txtSearch" name="txtSearch" class="form-control"/></div></div></form>',
							    title: "Search",
							    buttons: {
							        main: {
							            label: "Search",
							            className: "btn-sm btn-primary",
							            callback: function() {
							                onload = showLoading();
											var nobukti    = $("#nobukti").val();
											var notagihan    = $("#notagihan").val();
											var startDate  = $("#startDate").val();
											var endDate    = $("#endDate").val();
											var validasi   = $("#validasi").val();
											var statBayar    = $("#statBayar").val();
											var noCsv      = $("#noCsv").val();
											var txtSearch  = $("#txtSearch").val();
											var divisi  = $("#divisi").val();
											var department  = $("#department").val();
											
                                            $("#nobukti2").val(nobukti);
											$("#notagihan2").val(notagihan);
											$("#startDate2").val(startDate);
											$("#endDate2").val(endDate);
											$("#validasi2").val(validasi);
											$("#statBayar2").val(statBayar);
											$("#noCsv2").val(noCsv);
											$("#txtSearch2").val(noCsv);
											$("#divisi2").val(divisi);
											$("#department2").val(department);
											var dt = [
                                                {name:'nobukti',value: nobukti }, {name:'notagihan',value: notagihan }, {name:'startDate',value: startDate },
                                                {name:'endDate',value: endDate }, {name:'validasi',value: validasi }, 
                                                {name:'statBayar',value: statBayar }, {name:'noCsv',value: noCsv }, 
                                                {name:'txtSearch',value: txtSearch }, {name:'IdUser',value: IdUser }, 
                                                {name:'divisi',value: divisi }, {name:'department',value: department }
                                            ];
											$(".flexme3").flexOptions({params: dt}).flexReload();
										}
							        }
							    }
							});

							/*$('#statCsv').change(function(event){
								var statCsv = $("#statCsv").val();
								if (statCsv==0) {
									$("#noCsv").attr('disabled','disabled');
								} else {
									$("#noCsv").removeAttr('disabled');
								}
							});*/
							$('#statBayar').change(function(event){
								var statBayar = $("#statBayar").val();
								if (statBayar==0) {
									$("#noCsv").attr('disabled','disabled');
								} else {
									$("#noCsv").removeAttr('disabled');
								}
							});
							
					    } 
					}
				});
				function getDetail(evo_id){
					$("#post").html('<input type="hidden" name="modify" value="pengajuan-detail"><input type="hidden" name="evo_id" value="'+evo_id+'">');
					document.forms['Form'].submit();
				}
				function getBuktiKas(id){
					window.open('system/view/reportpengajuan_buktikas.php?id='+id);
							return true;
				}
			</script>
	    </div>
	    <input type="hidden" id="nobukti2" value="">
	    <input type="hidden" id="notagihan2" value="">
		<input type="hidden" id="startDate2" value="<?php echo date('Y-m-01'); ?>">
		<input type="hidden" id="endDate2" value="<?php echo date('Y-m-t'); ?>">
		<input type="hidden" id="validasi2" value="">
		<input type="hidden" id="statBayar2" value="">
		<input type="hidden" id="noCsv2" value="">
		<input type="hidden" id="divisi2" value="">
		<input type="hidden" id="department2" value="">
		<input type="hidden" id="txtSearch2"  value="">
	</form>
</div>
<?php
	function getDivisi($IdUser){
		$user = mssql_fetch_array(mssql_query("select * from sys_user where IdUser = '".$IdUser."'"));
		if ($user['KodeDealer']=='2010') { $is_dealer = "0"; } else { $is_dealer = "1"; }
		if ($user['divisi']!='all') { $div = "and nama_div = '".$user['divisi']."'"; } else { $div = ""; }
		$sql = "select nama_div from sys_divisi where is_dealer='".$is_dealer."' and is_aktif=1 $div";
		$rsl = mssql_query($sql);
		$opt = "<option value=\"\">- All -</option>";
		while ($dt = mssql_fetch_array($rsl)) {
			$opt .= "<option value=\"".$dt['nama_div']."\">".$dt['nama_div']."</option>";
		}
		return $opt;
	}

	function getDept($IdUser){
		$user = mssql_fetch_array(mssql_query("select * from sys_user where IdUser = '".$IdUser."'"));
		if ($user['KodeDealer']=='2010') { $is_dealer = "0"; } else { $is_dealer = "1"; }
		if ($user['divisi']!='all') { $div = "and nama_div = '".$user['divisi']."'"; } else { $div = ""; }
		if ($user['department']!='all') { $dept = "and nama_dept = '".$user['department']."'"; } else { $dept = ""; }
		$sql = "select nama_dept from sys_department a inner join sys_divisi b on a.id_sys_div=b.id_sys_div where id_sys_dept=id_sys_dept $div $dept";
		$rsl = mssql_query($sql);
		$opt = "<option value=\"\">- All -</option>";
		while ($dt = mssql_fetch_array($rsl)) {
			$opt .= "<option value=\"".$dt['nama_dept']."\">".$dt['nama_dept']."</option>";
		}
		return $opt;
	}
?>