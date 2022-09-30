<?php require_once ('system/inc/permission.php');  ?>
<link rel="stylesheet" type="text/css" href="assets/plugins/flexii/css/flexigrid.pack.css" />
<script type="text/javascript" src="system/myJs/pengajuan.js"></script>
<div class="row">	
	<form action="transaksi-pengajuan-modify" id="Form" method="POST">
	    <span id="post"></span>
	    <div class="col-sm-12">
	    	<input type="hidden" id="KodeDealer" value="<?php echo $user['KodeDealer']; ?>" />
	        <table class="dataPengajuan" style="display: none"></table>
	    </div>
	</form>
</div>
<div class="modal fade modals" id="getHutangDtl" tabindex="-1" role="dialog" aria-labelledby="getHutangDtl" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h2 class="modal-title">Tagihan</h2>
			</div>
			<div class="modal-body" style="padding: 0;">
				<table class="flexTagihan">
					<thead>
						<tr>
							<th width="31"><center>No</center></th>
							<th width="150" class="text-center">No Tagihan</th>
							<th width="150" class="text-center">Tanggal</th>
							<th width="150">Jumlah</th>
						</tr>
					</thead>
					<tbody id="dataVendor"></tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<div class="modal fade modals" id="getHutangDtl2" tabindex="-1" role="dialog" aria-labelledby="getHutangDtl2" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h2 class="modal-title">Tagihan Detail</h2>
			</div>
			<div class="modal-body" style="padding: 0;" id="dataHutangDtl2"></div>
		</div>
	</div>
</div>
