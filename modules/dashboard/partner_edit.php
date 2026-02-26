<?php if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id      = intval($_GET['id'] ?? 0);
$title   = !empty($id) ? lang('Edit Mitra "%s"', $db->getOne('SELECT `name` FROM `delivery_partner` WHERE `id`='.$id)) : lang('Tambah Mitra');
$add_sql = !empty($id) ? 'WHERE `id`='.$id : '';

$form = _lib('pea', 'delivery_partner');
$form->initEdit($add_sql);

$form->edit->addInput('header', 'header');
$form->edit->input->header->setTitle($title);

$form->edit->addInput('name', 'text');
$form->edit->input->name->setTitle('Nama Mitra');
$form->edit->input->name->setRequire();

$form->edit->addInput('phone', 'text');
$form->edit->input->phone->setRequire('phone');

$form->edit->addInput('join_at', 'date');

$form->edit->addExtraField('created_by', $user->id, 'add');

$form->edit->onSave(function ($id) use ($db, $form){
	if ($form->type == 'add') {
		// add to report
		$transaction_new = ['ondate' => $db->getOne('SELECT `join_at` FROM `delivery_partner` WHERE `id`='.$id)];
		_class('async')->run('dashboard_report_update', ['partner', [], $transaction_new]);
	}
});

$form->edit->setSaveButton('submit_partner', 'SAVE', 'floppy-disk');
if (empty($id)) $form->edit->setResetButton('submit_partner', 'List Mitra', 'list');

$msg = empty($id) ? lang('Partner successfully added').'<script type="text/javascript">setTimeout(function() {window.location.replace(window.location.href); }, 1000);</script>' : lang('Partner successfully edited');
$form->edit->setSuccessSaveMessage($msg);

$form->edit->action();
echo $form->edit->getForm();

