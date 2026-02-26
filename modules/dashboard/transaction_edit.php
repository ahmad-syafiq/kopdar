<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id      = intval($_GET['id'] ?? 0);
$trx_old = [];
$add_sql = '';
$title   = lang('Tambah Transaksi');
if (!empty($id)) {
	$trx_old = $db->getRow('SELECT `invoice`, `partner_id`, `merchant_name`, `ondate`, `amount`, `delivery_fee`, `total`, `platform_fee` FROM `delivery_transaction` WHERE `id`='.$id);
	$add_sql = 'WHERE `id`='.$id;
	$title   = lang('Edit Transaksi "%s"', $trx_old['invoice']);
}

$partner_id   = intval($_GET['partner_id'] ?? 0);
$partner_name = !empty($partner_id) ? $db->getOne('SELECT `name` FROM `delivery_partner` WHERE `id`='.$partner_id) : '';
$title       .= !empty($partner_id) ? ' - <b>'.$partner_name.'</b>' : '';

$form = _lib('pea',  'delivery_transaction');
$form->initEdit($add_sql);

$form->edit->addInput('header','header');
$form->edit->input->header->setTitle($title);

if (empty($id)) {
	if (empty($partner_id)) {
		$form->edit->addInput('partner_id', 'selecttable');
		$form->edit->input->partner_id->setTitle(lang('Nama Mitra'));
		$form->edit->input->partner_id->setReferenceTable('delivery_partner');
		$form->edit->input->partner_id->setReferenceField('name', 'id');
		$form->edit->input->partner_id->setAutoComplete(true);
		$form->edit->input->partner_id->setAllowNew(true);
		$form->edit->input->partner_id->setRequire();
	}else{
		$form->edit->addExtraField('partner_id', $partner_id);
	}
}

$form->edit->addInput('customer_name', 'text');
$form->edit->input->customer_name->setRequire();
$form->edit->input->customer_name->setTitle(lang('Nama Pemesan'));

$form->edit->addInput('customer_order', 'text');
$form->edit->input->customer_order->setTitle(lang('Detail Pesanan'));

$form->edit->addInput('merchant_name', 'text');
$form->edit->input->merchant_name->setTitle(lang('Nama Kedai'));

$form->edit->addInput('customer_address', 'text');
$form->edit->input->customer_address->setTitle(lang('Alamat Pengiriman'));

$form->edit->addInput('amount', 'text');
$form->edit->input->amount->setTitle(lang('Total Belanja'));
$form->edit->input->amount->setRequire('number');

$form->edit->addInput('delivery_fee', 'text');
$form->edit->input->delivery_fee->setTitle(lang('Ongkir'));
$form->edit->input->delivery_fee->setRequire('number');
$form->edit->input->delivery_fee->setExtra('data-format="number"');

$form->edit->addInput('ondate', 'date');
$form->edit->input->ondate->setTitle(lang('Tanggal Transaksi'));
$form->edit->input->ondate->setRequire();
$form->edit->input->ondate->setDefaultValue('now');

$form->edit->addExtraField('created_by', $user->id, 'add');

$form->edit->onSave(function($id) use ($db, $partner_name, $trx_old, $user) {
	$r = $db->getRow('SELECT `partner_id`, `merchant_name`, `ondate`, `amount`, `delivery_fee`, `total`, `platform_fee` FROM `delivery_transaction` WHERE `id`='.$id);

	$invoice      = !empty($trx_old['invoice']) ? $trx_old['invoice'] : 'INV/'.preg_replace('~^\d{2}|-~i', '', $r['ondate']).'/'.date('s').$id;
	$merchant_add = 0;
	$merchant_id  = $db->getOne('SELECT `id` FROM `delivery_merchant` WHERE `name`="'.addslashes($r['merchant_name']).'"');
	if (empty($merchant_id)) {
		$db->Insert('delivery_merchant', ['name' => $r['merchant_name']]);
		$merchant_id  = intval($db->Insert_ID());
		$merchant_add = 1;
	}

	if (empty($partner_name)) {
		$partner_name = $db->getOne('SELECT `name` FROM `delivery_partner` WHERE `id`='.$r['partner_id']);
	}

	$platform_cfg = floatval(get_config('delivery', 'config', 'platform_fee') / 100);
	$platform_fee = $platform_cfg * $r['delivery_fee'];
	
	$q = $db->Execute('UPDATE `delivery_transaction` SET 
												`invoice`="'.$invoice.'",
												`total`=`amount`+`delivery_fee`,
												`partner_name`="'.$partner_name.'",
												`merchant_id`='.$merchant_id.',
												`platform_fee`='.$platform_fee.'
											WHERE `id`='.$id);

	$transaction_old = [];
	if (!empty($trx_old)) {
		$transaction_old = [
												'id'            => $id,
												'partner_id'    => $trx_old['partner_id'],
												'merchant_name' => $trx_old['merchant_name'],
												'ondate'        => $trx_old['ondate'],
												'amount'        => $trx_old['amount'],
												'delivery_fee'  => $trx_old['delivery_fee'],
												'total'         => $trx_old['total'],
												'platform_fee'  => $trx_old['platform_fee']
											];
	}

	$transaction_new = [
											'id'            => $id,
											'partner_id'    => $r['partner_id'],
											'merchant_name' => $r['merchant_name'],
											'ondate'        => $r['ondate'],
											'amount'        => $r['amount'],
											'delivery_fee'  => $r['delivery_fee'],
											'total'         => $r['amount'] + $r['delivery_fee'],
											'platform_fee'  => $platform_fee
										];

	_class('async')->run('dashboard_report_update', ['balance', $transaction_old, $transaction_new]);
	_class('async')->run('dashboard_report_update', ['transaction', $transaction_old, $transaction_new]);
	if (empty($transaction_old)) _class('async')->run('dashboard_report_update', ['merchant', $transaction_old, $transaction_new]);
	_class('async')->run('dashboard_report_daily_update', [$transaction_old, $transaction_new]);
	_class('async')->run('dashboard_balance_update', [$transaction_old, $transaction_new, $user->id]);

});

$form->edit->setSaveButton('submit_transaction', 'SAVE', 'floppy-disk');
if (empty($id)) $form->edit->setResetButton('submit_transaction', 'List Transaksi', 'list');

$msg = empty($id) ? lang('Transaction successfully added').'<script type="text/javascript">setTimeout(function() {window.location.replace(window.location.href); }, 1000);</script>' : lang('Transaction successfully edited');
$form->edit->setSuccessSaveMessage($msg);

$form->edit->action();
echo $form->edit->getForm();
