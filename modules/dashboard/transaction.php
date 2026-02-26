<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$partner_id = intval($_GET['partner_id'] ?? 0);
if (!empty($partner_id)) {
	$partner_data = $db->getRow('SELECT `name`, `balance` FROM `delivery_partner` WHERE `id`='.$partner_id);
	$balance      = '<h4>'.lang('Saldo %s', $partner_data['name']).' : '.money($partner_data['balance']).'</h4>';
}

echo dashboard_panel('transaction_list.php', 'transaction_edit.php', !empty($partner_id) ? lang('Tambah Transaksi') : '', ($balance ?? ''));

