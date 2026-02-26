<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

// $data = $db->getAll('SELECT * FROM `delivery_transaction` WHERE 1');
// foreach ($data as $key => $r) {
// 	$platform_fee = floatval(get_config('delivery', 'config', 'platform_fee') / 100) * $r['delivery_fee'];
// 	_class('async')->run('dashboard_report_daily', [$r['partner_id'], $r['amount'], $r['delivery_fee'], $platform_fee, $r['ondate']]);
// }