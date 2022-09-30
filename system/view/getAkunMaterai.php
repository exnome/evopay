<?php
	$Tipe = addslashes($_REQUEST['Tipe']);
	$IdUser = addslashes($_REQUEST['IdUser']);
	$KodeDealer = addslashes($_REQUEST['KodeDealer']);
	include '../inc/koneksi.php';
	if ($msg=='0') {
		echo "0";
	} else if ($msg=='1') {
		echo "1";
	} else if (!mssql_select_db("[$table]",$connCab)) {
		echo "2";
	} else if ($msg=='3') {
?>
		<link rel="stylesheet" type="text/css" href="assets/plugins/flexii/style.css" />
		<script type="text/javascript" src="assets/plugins/flexii/js/flexigrid.pack.js"></script>
		<link rel="stylesheet" type="text/css" href="assets/plugins/flexii/css/flexigrid.pack.css" />
		<input type="hidden" id="KodeDealer" value="<?php echo $_REQUEST['KodeDealer'] ?>">
		<input type="hidden" id="IdUser" value="<?php echo $_REQUEST['IdUser'] ?>">
		<!--<input type="hidden" id="Tipe" value="<?php echo $_REQUEST['Tipe'] ?>">
		-->
        <table class="flexme1" style="display: none"></table>
		<script type="text/javascript">
		    jQuery(document).ready(function($) {
		    	//var Tipe = $("#Tipe").val();
		    	var IdUser = $("#IdUser").val();
		    	var KodeDealer = $("#KodeDealer").val();
		    	var id = "<?php echo $_REQUEST['id'] ?>";
				$(".flexme1").flexigrid({
				    dataType : 'xml',
				    colModel : [ 
				        {
					        display : '#',
					        name : 'KodeGl',
					        width : 40,
					        sortable : true,
					        align : 'center'
				        }, {
				            display : 'Kode Gl',
				            name : 'KodeGl',
				            width : 100,
				            sortable : true,
				            align : 'left'
				        }, {
				            display : 'Nama Gl',
				            name : 'NamaGl',
				            width : 200,
				            sortable : true,
				            align : 'left'
				        }
				    ],
				    buttons : [ 
				        {
				            name : 'Pick',
				            bclass : 'add',
				            onpress : button
				        }
				    ],
				    searchitems : [ 
		              { display : 'Kode Gl', name : 'KodeGl' },
		              { display : 'Nama Gl', name : 'NamaGl'},
		            ],
				    sortname : "KodeGl",
				    sortorder : "asc",
				    usepager : true,
				    useRp : true,
				    rp : 10,
				    rpOptions: [10, 20, 50, 100],
				    showToggleBtn : false,
				    width : 'auto',
				    height : '200',
					onSuccess: function(){
					    onload = hideLoading();
					}
				});
				// $("#dataAkunAja .tDiv2").after('<div style="font-size: 14px;line-height: 2;float: right;">Total Nominal : <span id="totNomPick">0</span></div>');
				$('.flexme1').flexOptions({
					url : 'system/data/getAkunMaterai.php',
					newp: 1,
					params:[ { name:'KodeDealer', value: KodeDealer },{ name:'IdUser', value: IdUser } ]
				}).flexReload();
				
				function sortAlpha(com) { 
		            jQuery('.flexme4').flexOptions({
		            	newp:1, params:[{name:'letter_pressed', value: com},{name:'qtype',value:$('select[name=qtype]').val()}]
		            });
		            jQuery(".flexme4").flexReload(); 
		        }
				function button(com) {
				    if (com == 'Pick') {
						var generallen = $("input[name='id[]']:checked").length;
						if (generallen==0 || generallen>1) {
						    onload = myBad('Pengajuan Credit Note');
						    return false;
						} else {
						    var data = $("input[name='id[]']:checked").val();
						    var r = data.split("#",2);
						    if (id) {
						    	$("#kodeAkun_"+id).val(r[0]);
								$("#ketAkun_"+id).val(r[0]+" | "+r[1]);
						    } else {
						    	$("#kodeAkunMaterai").val(r[0]);
								$("#namaAkunMaterai").val(r[1]);
						    }
							$("input[name='id[]']").attr('checked', false);
							$('#getAkunMaterai').modal('hide'); 
						}
					}
				}
			});
			// function getNominal(id,value){
			// 	if ($("#chk-"+id).is(":checked")) {
			// 		var totNomPick = $("#totNomPick").html();
			// 		totNomPick = parseInt(totNomPick) + parseInt(value);
			// 		$("#totNomPick").html(totNomPick);
			// 	} else {
			// 		var totNomPick = $("#totNomPick").html();
			// 		totNomPick = parseInt(totNomPick) - parseInt(value);
			// 		$("#totNomPick").html(totNomPick);
			// 	}
			// }
		</script>
<?php } ?>