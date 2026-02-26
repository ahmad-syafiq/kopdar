<?php if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$partner_id = intval($_GET['partner_id'] ?? 0);

$form = _lib('pea',  'delivery_transaction');
$form->initSearch();

if (empty($partner_id)) {	
	$form->search->setFormName('delivery_transaction_partners');

	$form->search->addInput('partner_id','selecttable');
	$form->search->input->partner_id->setTitle(lang('Pilih Mitra'));
	$form->search->input->partner_id->addOption(lang('---- Semua Mitra ----'), '');
	$form->search->input->partner_id->setReferenceTable('delivery_partner');
	$form->search->input->partner_id->setReferenceField('name', 'id');
	$form->search->input->partner_id->setAutoComplete();
}

$form->search->addInput('ondate','dateinterval');
$form->search->input->ondate->setIsSearchRange();

$add_sql = $form->search->action();
$keyword = $form->search->keyword();

echo $form->search->getForm();

$title = lang('Transaksi');
if (!empty($partner_id)) {
	$title   .= ' - '.$db->cacheGetOne('SELECT `name` FROM `delivery_partner` WHERE `id`='.$partner_id);
	$add_sql .= ' AND `partner_id`='.$partner_id;
}else
if (!empty($keyword['partner_id'])) {
	$title   .= ' - '.$db->cacheGetOne('SELECT `name` FROM `delivery_partner` WHERE `id`='.intval($keyword['partner_id']));
}

if (!empty($keyword['ondate'])) {
	$title .= ' ('.str_replace('&gt;', '', _func('date', 'interval', $keyword['ondate'], $keyword['ondate_until'] ?? $keyword['ondate'])).')';
}

$sys->nav_change(icon('fa-bar-chart').' '.$title);

$form = _lib('pea',  'delivery_transaction');
$form->initRoll($add_sql.' ORDER BY `ondate` DESC');

$form->roll->addReport('excel');
$form->roll->setSaveTool(false);
$form->roll->setDeleteTool(true);

$form->roll->addInput('header','header');
$form->roll->input->header->setTitle($title);

$form->roll->addInput('ondate','sqlplaintext');
$form->roll->input->ondate->setTitle(lang('Tanggal'));
$form->roll->input->ondate->setDisplayFunction(function($s){ return date('d M Y', strtotime($s)); }, false);

$form->roll->addInput('invoice','sqllinks');
$form->roll->input->invoice->setLinks($Bbc->mod['circuit'].'.transaction_edit');

$form->roll->addInput('partner_name','sqlplaintext');
$form->roll->input->partner_name->setTitle(lang('Nama Mitra'));

$form->roll->addInput('customer_name','sqlplaintext');
$form->roll->input->customer_name->setTitle(lang('Nama Pemesan'));
$form->roll->input->customer_name->setDisplayFunction(function($s){ return ucwords($s); });

$form->roll->addInput('customer_order','sqlplaintext');
$form->roll->input->customer_order->setTitle(lang('Detail Pesanan'));
$form->roll->input->customer_order->setDisplayFunction(function($s){ return ucwords($s); });
$form->roll->input->customer_order->setDisplayColumn(true);

$form->roll->addInput('merchant_name','sqlplaintext');
$form->roll->input->merchant_name->setTitle(lang('Nama Kedai'));
$form->roll->input->merchant_name->setDisplayFunction(function($s){ return ucwords($s); });

$form->roll->addInput('customer_address','sqlplaintext');
$form->roll->input->customer_address->setTitle(lang('Alamat Pengiriman'));
$form->roll->input->customer_address->setDisplayFunction(function($s){ return ucwords($s); });

$form->roll->addInput('amount','sqlplaintext');
$form->roll->input->amount->setTitle(lang('Total Belanja'));
$form->roll->input->amount->setNumberFormat();
$form->roll->input->amount->setExportFunction(function($d){ return str_replace(',', '', $d); });

$form->roll->addInput('delivery_fee','sqlplaintext');
$form->roll->input->delivery_fee->setTitle(lang('Ongkir'));
$form->roll->input->delivery_fee->setNumberFormat();
$form->roll->input->delivery_fee->setExportFunction(function($d){ return str_replace(',', '', $d); });

$form->roll->addInput('platform_fee','sqlplaintext');
$form->roll->input->platform_fee->setTitle(lang('Potongan'));
$form->roll->input->platform_fee->setNumberFormat();
$form->roll->input->platform_fee->setExportFunction(function($d){ return str_replace(',', '', $d); });

if (in_array('3', $user->group_ids)) {
	$form->roll->addInput('created_by', 'selecttable');
	$form->roll->input->created_by->setReferenceTable('bbc_account');
	$form->roll->input->created_by->setReferenceField('name', 'user_id');
	$form->roll->input->created_by->setPlaintext(true);
	$form->roll->input->created_by->setDisplayColumn(false);
	$form->roll->input->created_by->setTitle(lang('Dibuat'));
	$form->roll->input->created_by->addHelp(lang('Admin yang input transaksi'));
}

$form->roll->onDelete(function ($ids) use ($db){
	if (!empty($ids)) {
		$datas = $db->getAll('SELECT `id`, `partner_id`, `merchant_name`, `ondate`, `amount`, `delivery_fee`, `total`, `platform_fee` FROM `delivery_transaction` WHERE `id` IN('.implode(',', $ids).')');
		foreach ($datas as $val) {
			_class('async')->run('dashboard_report_update', ['transaction', $val]);
			_class('async')->run('dashboard_report_update', ['balance', $val]);
			_class('async')->run('dashboard_report_daily_update', [$val]);
			_class('async')->run('dashboard_balance_update', [$val]);
		}
	}
});

$form->roll->action();
echo $form->roll->getForm();





