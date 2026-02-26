<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$partner_id = intval($_GET['partner_id'] ?? 0);

$form = _lib('pea',  'delivery_partner_balance');
$form->initSearch();

if (empty($partner_id)) {	
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


$title = lang('Deposit');
if (!empty($partner_id)) {
	$title   .= ' - '.$db->getOne('SELECT `name` FROM `delivery_partner` WHERE `id`='.$partner_id);
	$add_sql .= ' AND `partner_id`='.$partner_id;
}

if (!empty($keyword['created'])) {
	$title .= ' ('.str_replace('&gt;', '', _func('date', 'interval', $keyword['created'], $keyword['created_until'])).')';
}


$sys->nav_change(icon('fa-money').' '.$title);

$form = _lib('pea',  'delivery_partner_balance');
$form->initRoll($add_sql.' ORDER BY `ondate` DESC');

$form->roll->addReport('excel');
$form->roll->setSaveTool(false);
$form->roll->setDeleteTool(true);

$form->roll->addInput('header','header');
$form->roll->input->header->setTitle($title);

$form->roll->addInput('partner_id','selecttable');
$form->roll->input->partner_id->setTitle(lang('Nama Mitra'));
$form->roll->input->partner_id->setReferenceTable('delivery_partner');
$form->roll->input->partner_id->setReferenceField('name', 'id');
$form->roll->input->partner_id->setPlaintext(true);
$form->roll->input->partner_id->setDisplayFunction('ucwords');

$form->roll->addInput('ondate','sqllinks');
$form->roll->input->ondate->setTitle(lang('Tanggal'));
// $form->roll->input->ondate->setDateFormat('d M Y');
$form->roll->input->ondate->setLinks($Bbc->mod['circuit'].'.balance_edit');
$form->roll->input->ondate->setDisplayFunction(function($s){ return date('d M Y', strtotime($s)); }, false);

$form->roll->addInput('notes','sqlplaintext');
$form->roll->input->notes->setTitle(lang('Keterangan'));
$form->roll->input->notes->setDisplayFunction(function($s){ return !empty($s) ? ucwords($s) : '-'; });

$form->roll->addInput('credit', 'sqlplaintext');
$form->roll->input->credit->setTitle('Credit');
$form->roll->input->credit->setFieldName('CONCAT(type,"`",amount) AS credit');
$form->roll->input->credit->setDisplayFunction(function($a){$r = explode('`', $a); return in_array($r[0], [1,3,4]) ? money($r[1]) : ' '; });
$form->roll->input->credit->setExportFunction(function($d){ return str_replace(',', '', $d); });

$form->roll->addInput('debit', 'sqlplaintext');
$form->roll->input->debit->setTitle('Debit');
$form->roll->input->debit->setFieldName('CONCAT(type,"`",amount) AS debit');
$form->roll->input->debit->setDisplayFunction(function($a){$r = explode('`', $a); return ($r[0]==2) ? money($r[1]) : ' '; });
$form->roll->input->debit->setExportFunction(function($d){ return str_replace(',', '', $d); });

$form->roll->onDelete(function ($ids) use ($db){
	if (!empty($ids)) {
		$datas = $db->getAll('SELECT `id`, `partner_id`, `ondate`, `type`, `amount` FROM `delivery_partner_balance` WHERE `id` IN('.implode(',', $ids).')');
		foreach ($datas as $val) {
			if (in_array($val['type'], [1,3,4])) {
				$operator = '-';
				_class('async')->run('dashboard_report_update', ['balance', $val, []]);
			}elseif ($val['type'] == 2) {
				$operator = '+';
				_class('async')->run('dashboard_report_update', ['balance', [], $val]);
			}
			
			$db->Execute('UPDATE `delivery_partner` SET `balance`=`balance`'.$operator.$val['amount'].' WHERE `id`='.$val['partner_id']);
		}
	}
});

$form->roll->action();
echo $form->roll->getForm();


