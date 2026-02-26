<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$form = _lib('pea',  'delivery_transaction');
$form->initRoll($add_sql.' ORDER BY `ondate`');

$form->roll->setFormName('transaction');
$form->roll->addReport('excel');
$form->roll->setSaveTool(false);
$form->roll->setDeleteTool(false);

$form->roll->addInput('header','header');
$form->roll->input->header->setTitle(lang('Laporan Transaksi - %s', $partner_periode));

$form->roll->addInput('ondate','sqlplaintext');
$form->roll->input->ondate->setTitle(lang('Tanggal'));
$form->roll->input->ondate->setDisplayFunction(function($s){ return date('d M Y', strtotime($s)); }, false);

$form->roll->addInput('invoice','sqlplaintext');

$form->roll->addInput('partner_name','sqlplaintext');
$form->roll->input->partner_name->setTitle(lang('Nama Mitra'));

$form->roll->addInput('customer_name','sqlplaintext');
$form->roll->input->customer_name->setTitle(lang('Nama Pemesan'));
$form->roll->input->customer_name->setDisplayFunction(function($s){ return ucwords($s); });

$form->roll->addInput('customer_order','sqlplaintext');
$form->roll->input->customer_order->setTitle(lang('Detail Pesanan'));
$form->roll->input->customer_order->setDisplayFunction(function($s){ return ucwords($s); });

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

$form->roll->addInput('created_by', 'selecttable');
$form->roll->input->created_by->setReferenceTable('bbc_account');
$form->roll->input->created_by->setReferenceField('name', 'user_id');
$form->roll->input->created_by->setPlaintext(true);
$form->roll->input->created_by->setTitle(lang('Dibuat'));
$form->roll->input->created_by->addHelp(lang('Admin yang input transaksi'));

$form->roll->action();
echo $form->roll->getForm();