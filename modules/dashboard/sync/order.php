<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');
// https://updates.id/kopdar/dashboard/sync/order

$partner = $db->getAssoc('SELECT `id`, `name` FROM `delivery_partner` WHERE 1');

if (!empty($_GET['is_ajax'])) {
	$json = file_get_contents("php://input");
	$data = json_decode($json, true);
	foreach ($data as $val) {
		$partner_id = array_search($val['partner_name'], $partner);

		$merchant_add = 0;
		$merchant_id  = $db->getOne('SELECT `id` FROM `delivery_merchant` WHERE `name`="'.addslashes($val['merchant_name']).'"');
		if (empty($merchant_id)) {
			$db->Insert('delivery_merchant', ['name' => $val['merchant_name']]);
			$merchant_id  = intval($db->Insert_ID());
			$merchant_add = 1;
		}

		$platform_fee = floatval(get_config('delivery', 'config', 'platform_fee') / 100) * $val['delivery_fee'];

		$db->Insert('delivery_transaction', [
																					'ondate'           => $val['ondate'], 
																					'partner_id'       => $partner_id, 
																					'partner_name'     => $val['partner_name'], 
																					'customer_name'    => $val['customer_name'], 
																					'customer_order'   => $val['customer_order'], 
																					'merchant_id'      => $merchant_id, 
																					'merchant_name'    => $val['merchant_name'], 
																					'customer_address' => $val['customer_address'], 
																					'amount'           => $val['amount'], 
																					'delivery_fee'     => $val['delivery_fee'], 
																					'total'            => $val['amount'] + $val['delivery_fee'], 
																					'platform_fee'     => $platform_fee, 
																				]);

		$transaction_id = intval($db->Insert_ID());

		if ($transaction_id) {
			$invoice = 'INV/'.preg_replace('~^\d{2}|-~i', '', $val['ondate']).'/'.date('s').$transaction_id;
			$db->Update('delivery_transaction', ['invoice' =>  $invoice], $transaction_id);
			
			// add to report
			$transaction_new = [
				'id'           => $transaction_id,
				'partner_id'   => $partner_id,
				'ondate'       => $val['ondate'],
				'amount'       => $val['amount'],
				'delivery_fee' => $val['delivery_fee'],
				'platform_fee' => $platform_fee,
			];

			_class('async')->run('dashboard_report_update', ['balance', [], $transaction_new]);
			_class('async')->run('dashboard_report_update', ['transaction', [], $transaction_new]);
			_class('async')->run('dashboard_report_update', ['merchant', [], $transaction_new]);
			_class('async')->run('dashboard_report_daily_update', [[], $transaction_new]);
			_class('async')->run('dashboard_balance_update', [[], $transaction_new, $user->id]);
		}
	}
	echo json_encode(['ok' => 1]);
	exit;
}

if (!empty($_POST['order_sync'])) {
	$order = explode("\n", trim($_POST['order_sync']));
	$data  = [];
	foreach ($order as $value) {
		$dt = preg_split('~\t~is', trim($value));
		if (count($dt) == 7) {
			$data[] = [
									'ondate'           => $_POST['ondate'],
									'partner_name'     => $partner[$_POST['partner']],
									'customer_name'    => $dt[1],
									'customer_order'   => $dt[2],
									'merchant_name'    => $dt[3],
									'customer_address' => $dt[4],
									'amount'           => $dt[5] != '-' ? str_replace('.', '', $dt[5]) : 0,
									'delivery_fee'     => str_replace('.', '', $dt[6])
								];
		}else {
			pr($dt, __FILE__.':'.__LINE__);
		}
	}
	if (!empty($data)) {
		echo table($data, array('Tanggal', 'Nama Mitra', 'Nama Pemesan', 'Detail Pesanan', 'Nama Kedai', 'Alamat Pengiriman', 'Total Belanja', 'Ongkir'));
		echo '<button id="order_sync_save" class="btn btn-primary" data-json="'.htmlspecialchars(json_encode($data)).'">Save All</button>';
	}
}

?>
<div class="col-md-12">
	<form action="" method="POST" role="form">
		<legend>Sync Order</legend>
		<div class="form-group">
			<label>Mitra</label>
			<select name="partner" class="form-control" required="required">
				<option value="" selected="">--- Pilih Mitra ---</option>
				<?php
				echo createOption($partner, $_POST['partner'] ?? '');
				?>
			</select>
		</div>
		<div class="form-group">
			<label>Ondate</label>
			<input type="date" name="ondate" class="form-control" value="<?php echo date('Y-m-d')?>" required="required">
		</div>
		<div class="form-group">
			<label>Order</label>
			<textarea name="order_sync" class="form-control" rows="3" required="required"></textarea>
		</div>
		<button type="submit" class="btn btn-primary">Submit</button>
	</form>
</div>

<script type="text/javascript">
_Bbc(function($) {
	$("#order_sync_save").click(function(){
		var json = $(this).data("json");
		var url  = new URL(window.location.href);
		url.searchParams.set("is_ajax", "1");
    $.ajax({
      url: url,
      type: "POST",
      data: JSON.stringify(json),
      contentType: "application/json",
      dataType: "json",
      success: function(res){
      	if (res.ok) {
      		alert("Data berhasil diinput");
          window.location.replace(window.location.href);
      	}
      }
    });
	});

});
</script>