<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

function dashboard_report($type='', $amount=1, $ondate='', $action='+')
{
	if (empty($type)) {
		return false;
	}

	if (empty($ondate)) {
		$ondate = date('Y-m-d');
	}

	global $db;
	$signature = preg_replace('~-\d{2}$|-~i', '', $ondate);
	$report_id = $db->getOne('SELECT `id` FROM `delivery_report` WHERE `signature`='.$signature);
	if (empty($report_id)) {
		$db->Insert('delivery_report', array('signature' => $signature));
		$report_id = $db->Insert_ID();
	}
	$q = $db->Execute('UPDATE `delivery_report` SET '.$type.'='.$type.$action.$amount.' WHERE `id`='.$report_id);
	if (!$q) {
		_class('async')->run(__FUNCTION__);
	}
}

function dashboard_report_update($type='', $trx_old=[], $trx_new=[])
{
	switch ($type) {
		case 'balance':
			if (!empty($trx_old)) {
				if (isset($trx_old['platform_fee'])) {
					dashboard_report($type, $trx_old['platform_fee'], $trx_old['ondate']); // jika transaksi, fee lama kembalikan ke saldo mitra baru potong data updatenya
				}else if (!empty($trx_old['balance'])) {
					dashboard_report($type, $trx_old['balance'], $trx_old['ondate'], '-'); // jika topup, deposit lama potong saldo baru tambahkan data updatenya
				}
			}
			if (!empty($trx_new)) {
				if (isset($trx_new['platform_fee'])) {
					dashboard_report($type, $trx_new['platform_fee'], $trx_new['ondate'], '-'); // jika transaksi, fee lama kembalikan ke saldo mitra baru potong data updatenya
				}else if (!empty($trx_new['balance'])) {
					dashboard_report($type, $trx_new['balance'], $trx_new['ondate']); // jika topup, deposit lama potong saldo baru tambahkan data updatenya
				}
			}
			break;
		case 'transaction':
			if (!empty($trx_old)) {
				dashboard_report($type, 1, $trx_old['ondate'], '-'); // delete transaksi
			}
			if (!empty($trx_new)) {
				dashboard_report($type, 1, $trx_new['ondate']); // tambah transaksi
			}
			break;
		case 'merchant':
			if (!empty($trx_new)) {
				dashboard_report($type, 1, $trx_new['ondate']); // tambah kedai
			}
			break;
		case 'partner':
			if (!empty($trx_new)) {
				dashboard_report($type, 1, $trx_new['ondate']); // tambah mitra driver
			}
			break;
	}
}



function dashboard_report_daily($partner_id=0, $amount=0, $delivery_fee=0, $platform_fee=0, $balance=0, $ondate='', $action='+')  //--
{
	if (empty($partner_id)) {
		return false;
	}

	if (empty($ondate)) {
		$ondate = date('Y-m-d');
	}

	global $db;
	$amount       = floatval($amount);
	$delivery_fee = floatval($delivery_fee);
	$total        = floatval($amount+$delivery_fee);
	$platform_fee = floatval($platform_fee);
	$transaction  = !empty($platform_fee) ? 1 : 0;

	$signature = preg_replace('~-~i', '', $ondate).$partner_id;
	$report_id = $db->getOne('SELECT `id` FROM `delivery_report_daily` WHERE `signature`='.$signature);
	if (empty($report_id)) {
		$db->Insert('delivery_report_daily', array('partner_id' => $partner_id, 'ondate' => $ondate, 'signature' => $signature));
		$report_id = $db->Insert_ID();
	}
	$q = $db->Execute('UPDATE `delivery_report_daily` 
											SET `transaction`=`transaction`'.$action.$transaction.', 
											`amount`=`amount`'.$action.$amount.', 
											`delivery_fee`=`delivery_fee`'.$action.$delivery_fee.',
											`total`=`total`'.$action.$total.',
											`platform_fee`=`platform_fee`'.$action.$platform_fee.',
											`balance`=`balance`'.$action.$balance.'
											WHERE `id`='.$report_id);
	if (!$q) {
		_class('async')->run(__FUNCTION__);
	}
}

function dashboard_report_daily_update($trx_old=[], $trx_new=[]) // jika posisi edit, data lama akan dihapus dulu baru insert data yang baru karena mempengaruhi summary report jadi biar gampang aja rumusnya
{
	if (!empty($trx_old)) {
		dashboard_report_daily($trx_old['partner_id'], ($trx_old['amount'] ?? 0), ($trx_old['delivery_fee'] ?? 0), ($trx_old['platform_fee'] ?? 0), ($trx_old['balance'] ?? 0), $trx_old['ondate'], '-'); // delete report lama
	}
	if (!empty($trx_new)) {
		dashboard_report_daily($trx_new['partner_id'], ($trx_new['amount'] ?? 0), ($trx_new['delivery_fee'] ?? 0), ($trx_new['platform_fee'] ?? 0), ($trx_new['balance'] ?? 0), $trx_new['ondate']); // insert report baru
	}
}

