<?php if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$form = _lib('pea',  'delivery_partner');
$form->initSearch();

$form->search->addInput('keyword','keyword');
$form->search->input->keyword->addSearchField('name', false);
$form->search->input->keyword->setTitle(lang('Nama Mitra'));

$add_sql = $form->search->action();
$keyword = $form->search->keyword();

echo $form->search->getForm();


$sys->nav_change(icon('fa-id-badge').' '.lang('Mitra'));

$form = _lib('pea',  'delivery_partner');
$form->initRoll($add_sql.' ORDER BY `id` DESC');

$form->roll->setSaveTool(false);
// $form->roll->setDisableInput('delete', 0, '!=', 'balance'); // yang memiliki saldo tidak bisa dihapus

$form->roll->addInput('id','sqlplaintext');
$form->roll->input->id->setDisplayColumn(false);

$url  = $Bbc->mod['circuit'].'.';
$menu = [
	$url.'transaction' => icon('fa-list-alt').' List Transaksi',
	$url.'report'      => icon('fa-bar-chart').' Laporan'
];

if (in_array('3', $user->group_ids)) {
	$menu[$url.'balance'] = icon('fa-money').' Deposit';
}

$form->roll->addInput('head','multiinput');
$form->roll->input->head->setTitle(lang('Nama Mitra'));
$form->roll->input->head->addInput('edit', 'editlinks', 'info');
$form->roll->input->head->addInput('name', 'sqllinks');
$form->roll->input->edit->setCaption('');
$form->roll->input->edit->setGetName('partner_id');
$form->roll->input->edit->setFieldName('id AS edit');
$form->roll->input->edit->setLinks($menu);
$form->roll->input->name->setLinks($url.'partner_edit');
$form->roll->input->name->setDisplayFunction(function($s){ return ucwords($s); });

$form->roll->addInput('phone','sqlplaintext');

$form->roll->addInput('balance','sqlplaintext');
$form->roll->input->balance->setNumberFormat();

$form->roll->addInput('join_at','sqlplaintext');
$form->roll->input->join_at->setDateFormat('d M Y');

$form->roll->onDelete(function ($ids) use ($db){
	if (!empty($ids)) $db->Execute('UPDATE `delivery_report` SET `partner`=`partner`-'.count($ids));
});

$form->roll->action();
echo $form->roll->getForm();

