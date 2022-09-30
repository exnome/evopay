<?php require_once ('system/inc/permission.php');  ?>
<link rel="stylesheet" type="text/css" href="assets/plugins/flexii/css/flexigrid.pack.css" />
<div class="row">
	<form action="master-deptterkait-modify" id="Form" method="POST">
	    <span id="post"></span> 
	    <div class="col-sm-12">
	        <table class="flexme3" style="display: none"></table>
	        <script type="text/javascript">
	            jQuery(document).ready(function($) {
					$(".flexme3").flexigrid({
					    url : 'system/data/deptterkait.php',
					    dataType : 'xml',
					    colModel : [ 
					        {
						        display : '#',
						        name : 'Id',
						        width : 40,
						        sortable : true,
						        align : 'center'
					        }, {
					            display : 'Kode User',
					            name : 'IdUser',
					            width : 100,
					            sortable : true,
					            align : 'left'
					        }, {
					            display : 'Nama User',
					            name : 'namaUser',
					            width : 150,
					            sortable : true,
					            align : 'left'
					        }, {
					            display : 'NIK',
					            name : 'nik',
					            width : 100,
					            sortable : true,
					            align : 'left'
					        }, {
								display : 'Dealer',
					            name : 'NamaDealer',
					            width : 300,
					            sortable : true,
					            align : 'left'
					        }, {
					            display : 'Level',
					            name : 'tipe',
					            width : 120,
					            sortable : true,
					            align : 'left'
					        }, {
					            display : 'Divisi',
					            name : 'divisi',
					            width : 120,
					            sortable : true,
					            align : 'left'
					        }, {
					            display : 'Departemen',
					            name : 'departemen',
					            width : 120,
					            sortable : true,
					            align : 'left'
					        }, {
					            display : 'Level',
					            name : 'levelvalidator',
					            width : 80,
					            sortable : true,
					            align : 'center'
					        }
					    ],
					    buttons : [ 
					        {
					            name : 'New',
					            bclass : 'add',
					            onpress : button
					        }/*,{
					            name : 'Edit',
					            bclass : 'edit',
					            onpress : button
					        }*/,{
					            name : 'Delete',
					            bclass : 'delete',
					            onpress : button
					        },{
							    name : 'Search',
							    bclass : 'search',
							    onpress : button
							}
					    ],
					    title : 'Departement Terkait',
					    sortname : "IdUser",
					    sortorder : "asc",
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
					function button(com) {
					    if (com == 'New') {
					        $("#post").html('<input type="hidden" name="new" value="">');
					        document.forms['Form'].submit();
					    } else if (com == 'Edit') {
					        var generallen = $("input[name='id[]']:checked").length;
					        if (generallen==0 || generallen>1) {
					            onload = myBad('User');
					            return false;
					        } else {
					            $("#post").html('<input type="hidden" name="edit" value="">');
					            document.forms['Form'].submit();
					        }
					    } else if (com == 'Delete') {
							var generallen = $("input[name='id[]']:checked").length;
							if (generallen==0 || generallen>1) {
							    onload = myBad('User');
							    return false;
							} else {
							    var Id = $("input[name='id[]']:checked").val();
								$.ajax({ 
								    url: 'system/control/deptterkait.php',
								    data: {action:'delete', 'id': Id},
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
										    title: "user",
										    buttons: {
										        main: {
										            label: "Ok",
										            className: "btn-sm btn-primary",
										            callback: function() {
										                $('.flexme3').flexReload();
										            }
										        }
										    }
										});
									}
								});
							}
						} else if (com == 'Search') {
					        bootbox.dialog({
							    message: '<form action="" method="post" class="form-horizontal"><div class="form-group" style="margin-bottom: 2px;"><label class="col-md-4 control-label">Kode User</label><div class="col-md-8"><input class="form-control" id="UserName" name="UserName" autocomplete="off"></div></div><div class="form-group" style="margin-bottom: 2px;"><label class="col-md-4 control-label">Nama User</label><div class="col-md-8"><input class="form-control" id="NamaLengkap" name="NamaLengkap" autocomplete="off"></div></div><div class="form-group" style="margin-bottom: 2px;"><label class="col-md-4 control-label">Departement</label><div class="col-md-8"><input class="form-control" id="Departement" name="Departement" autocomplete="off"></div></div></form>',
							    title: "Search",
							    buttons: {
							        main: {
							            label: "Search",
							            className: "btn-sm btn-primary",
							            callback: function() {
							                onload = showLoading();
											var UserName   = $("#UserName").val();
											var NamaLengkap   = $("#NamaLengkap").val();
											var Departement   = $("#Departement").val();
											var dt = [{name:'UserName',value: UserName },{name:'NamaLengkap',value: NamaLengkap },{name:'Departement',value: Departement }];
											$(".flexme3").flexOptions({params: dt}).flexReload();
										}
							        }
							    }
							});
					    }
					}
				});
	        </script>
	    </div>
	</form>
</div>