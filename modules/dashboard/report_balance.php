<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$form = _lib('pea',  'delivery_partner_balance');
$form->initRoll($add_sql.' AND `type` IN (1,3,4) ORDER BY `ondate`');

$form->roll->setFormName('balance');
$form->roll->addReport('excel');
$form->roll->setSaveTool(false);
$form->roll->setDeleteTool(false);

$form->roll->addInput('header','header');
$form->roll->input->header->setTitle(lang('Laporan Deposit - %s', $partner_periode));

$form->roll->addInput('partner_id','selecttable');
$form->roll->input->partner_id->setTitle(lang('Nama Mitra'));
$form->roll->input->partner_id->setReferenceTable('delivery_partner');
$form->roll->input->partner_id->setReferenceField('name', 'id');
$form->roll->input->partner_id->setPlaintext(true);
$form->roll->input->partner_id->setDisplayFunction('ucwords');

$form->roll->addInput('ondate','sqlplaintext');
$form->roll->input->ondate->setTitle(lang('Tanggal'));
$form->roll->input->ondate->setDateFormat('d M Y');

$form->roll->addInput('notes','sqlplaintext');
$form->roll->input->notes->setTitle(lang('Keterangan'));
$form->roll->input->notes->setDisplayFunction(function($s){ return !empty($s) ? ucwords($s) : '-'; });

$form->roll->addInput('credit', 'sqlplaintext');
$form->roll->input->credit->setTitle('Credit');
$form->roll->input->credit->setFieldName('CONCAT(type,"`",amount) AS credit');
$form->roll->input->credit->setDisplayFunction(function($a){$r = explode('`', $a); return in_array($r[0], [1,3,4]) ? money($r[1]) : ' '; });
$form->roll->input->credit->setExportFunction(function($d){ return str_replace(',', '', $d); });

echo $form->roll->getForm();