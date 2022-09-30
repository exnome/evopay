<?php
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
	<input type="hidden" id="KodeDealer" value="<?php echo $KodeDealer; ?>">
	<table class="flexme4" style="display: none"></table>
	<script type="text/javascript">
	    jQuery(document).ready(function($) {
			var KodeDealer = $("#KodeDealer").val();
			$(".flexme4").flexigrid({
				// url : 'system/data/getBank.php',
			    dataType : 'xml',
			    colModel : [ 
			        {
				        display : '#',
				        name : 'KodeBank',
				        width : 40,
				        sortable : true,
				        align : 'center'
			        }, {
			            display : 'Kode Bank',
			            name : 'KodeBank',
			            width : 100,
			            sortable : true,
			            align : 'left'
			        }, {
			            display : 'Nama Bank',
			            name : 'NamaBank',
			            width : 250,
			            sortable : true,
			            align : 'left'
			        }, {
			            display : 'No Rekening',
			            name : 'NoRekening',
			            width : 150,
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
					{ display : 'Bank', name : 'bank'},
					{ display : 'Bank Account', name : 'akun_bank'},
	            ],
			    sortname : "KodeBank",
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
			$('.flexme4').flexOptions({
				url : 'system/data/getBank.php',
				newp: 1,
				params:[ { name:'KodeDealer', value: KodeDealer } ]
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
					    var r = data.split("#");
					    $("#kode_bank_pengirim").val(r[0]);
					    $("#nama_bank_pengirim").val(r[1]);
						$("#tf_from_account").val(r[2]);
						$("input[name='id[]']").attr('checked', false);
						$('#getBank').modal('hide'); 
					}
				}
			}
		});
	</script>
<?php } ?>