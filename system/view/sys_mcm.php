<link rel="stylesheet" type="text/css" href="assets/plugins/flexii/style.css" />
<script type="text/javascript" src="assets/plugins/flexii/js/flexigrid.pack.js"></script>
<link rel="stylesheet" type="text/css" href="assets/plugins/flexii/css/flexigrid2.css" />
<input type="hidden" id="kode" value="<?php echo $_REQUEST['kode'] ?>">
<input type="hidden" id="no" value="<?php echo $_REQUEST['no'] ?>">
<table class="flexme4" style="display: none"></table>
<script type="text/javascript">
	jQuery(document).ready(function($) {
		var kode = $("#kode").val();
		var no = $("#no").val();
        $(".flexme4").flexigrid({
		    url : 'system/data/sys_mcm.php?kode='+kode,
		    dataType : 'xml',
		    colModel : [ 
		        {
			        display : '#',
			        name : '',
			        width : 30,
			        sortable : false,
			        align : 'center'
		        },{
			        display : 'Kode',
			        name : 'kode',
			        width : 100,
			        sortable : false,
			        align : 'left'
		        },{
			        display : 'Nama Bank',
			        name : 'nama_bank',
			        width : 300,
			        sortable : false,
			        align : 'left'
		        },{
			        display : 'Kombinasi Nama',
			        name : 'kombinasi_nama',
			        width : 250,
			        sortable : false,
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
				{ display : 'Kode', name : 'kode' },
				{ display : 'Nama Bank', name : 'nama_bank'},
				{ display : 'Kombinasi Nama', name : 'kombinasi_nama'},
            ],
		    sortname : "kombinasi_nama",
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
		function sortAlpha(com) { 
		    jQuery('.flexme4').flexOptions({newp:1, params:[{name:'letter_pressed', value: com},{name:'qtype',value:$('select[name=qtype]').val()}]});
		    jQuery(".flexme4").flexReload(); 
		}
		function button(com){
			if (com == 'Pick') {
				var generallen = $("input[name='id[]']:checked").length;
				if (generallen==0 || generallen>1) {
				    onload = myBad('Pengajuan Credit Note');
				    return false;
				} else {
				    var data = $("input[name='id[]']:checked").val();
				    $("#kode_rtgs_kliring_"+no).val(data);
					$("input[name='id[]']").attr('checked', false);
					$('#getsysMcm').modal('hide'); 
				}
			}
		}
	});
</script>