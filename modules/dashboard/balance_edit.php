<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id      = intval($_GET['id'] ?? 0);
$trx_old = [];
$add_sql = '';
$title   = lang('Tambah Transaksi Deposit');
if (!empty($id)) {
	$trx_old = $db->getRow('SELECT `partner_id`, `type`, `ondate`, `amount` FROM `delivery_partner_balance` WHERE `id`='.$id);
	$add_sql = 'WHERE `id`='.$id;
	$title   = lang('Tambah Transaksi Deposit');
}

$partner_id   = intval($_GET['partner_id'] ?? 0);
$partner_name = !empty($partner_id) ? $db->getOne('SELECT `name` FROM `delivery_partner` WHERE `id`='.$partner_id) : '';
$title       .= !empty($partner_id) ? ' - <b>'.$partner_name.'</b>' : '';

$form = _lib('pea',  'delivery_partner_balance');
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

$form->edit->addInput('ondate', 'date');
$form->edit->input->ondate->setTitle(lang('Tanggal'));
$form->edit->input->ondate->setRequire();
$form->edit->input->ondate->setDefaultValue('now');

$form->edit->addInput('type', 'select');
$form->edit->input->type->setRequire();
$form->edit->input->type->addOption('Topup (+)', 1);
$form->edit->input->type->addOption('Biaya Platform (-)', 2);
$form->edit->input->type->addOption('Bonus Harian (+)', 3);
$form->edit->input->type->addOption('Bonus Bulanan (+)', 4);

$form->edit->addInput('amount', 'text');
$form->edit->input->amount->setTitle(lang('Nominal'));
$form->edit->input->amount->setRequire('any');

$form->edit->addInput('notes', 'text');
$form->edit->input->notes->setTitle(lang('Keterangan'));

$form->edit->addExtraField('created_by', $user->id, 'add');

$form->edit->onSave(function($id) use ($db, $trx_old) {
	$r = $db->getRow('SELECT `partner_id`, `type`, `ondate`, `notes`, `amount` FROM `delivery_partner_balance` WHERE `id`='.$id);
	
	// jika posisi edit, maka data lama dikembalikan dulu (jika topup dikurang, jika transaksi potongan dikembalikan)
	if (!empty($trx_old)) {
		$operator = in_array($trx_old['type'], [1,3,4]) ? '-' : '+';
		$db->Execute('UPDATE `delivery_partner` SET `balance`=`balance`'.$operator.$trx_old['amount'].' WHERE `id`='.$trx_old['partner_id']);
	}

	$operator = in_array($r['type'], [1,3,4]) ? '+' : '-';
	$db->Execute('UPDATE `delivery_partner` SET `balance`=`balance`'.$operator.$r['amount'].' WHERE `id`='.$r['partner_id']);

	if (empty($r['notes'])) {
		switch ($r['type']) {
			case '1':
				$notes = lang('Topup');
				break;
			case '3':
				$notes = lang('Bonus Harian');
				break;
			case '4':
				$notes = lang('Bonus Bulanan');
				break;
			default:
				$notes = '';
				break;
		}
		$db->Execute('UPDATE `delivery_partner_balance` SET `notes`="'.$notes.'" WHERE `id`='.$id);
	}

	// add to report
	$transaction_old = [];
	if (!empty($trx_old)) {
		$transaction_old = ['partner_id' => $trx_old['partner_id'], 'ondate' => $trx_old['ondate']];
		if (in_array($trx_old['type'], [1,3,4])) {
			$transaction_old['balance'] = $trx_old['amount'];
		}else if ($trx_old['type'] == 2) {
			$transaction_old['platform_fee'] = $trx_old['amount'];
		}
	}
	$transaction_new = ['partner_id' => $r['partner_id'], 'ondate' => $r['ondate']];
	if (in_array($r['type'], [1,3,4])) {
		$transaction_new['balance'] = $r['amount'];
	}else if ($r['type'] == 2) {
			$transaction_new['platform_fee'] = $r['amount'];
		}
	_class('async')->run('dashboard_report_update', ['balance', $transaction_old, $transaction_new]);
	_class('async')->run('dashboard_report_daily_update', [$transaction_old, $transaction_new]);
});

$form->edit->setSaveButton('submit_transaction', 'SAVE', 'floppy-disk');
if (empty($id)) $form->edit->setResetButton('submit_transaction', 'List Deposit', 'list');

$msg = empty($id) ? lang('Transaction successfully added').'<script type="text/javascript">setTimeout(function() {window.location.replace(window.location.href); }, 1000);</script>' : lang('Transaction successfully edited');
$form->edit->setSuccessSaveMessage($msg);

$form->edit->action();
echo $form->edit->getForm();
