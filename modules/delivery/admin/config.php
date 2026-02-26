<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$form = _class('bbcconfig');
$_setting = array(
	'platform_fee' => array(
		'text'    => 'Biaya Platform',
		'type'    => 'text',
		'default' => '20',
		'add'     => '%',
		'tips'    => 'Potongan biaya yang dikenakan ke mitra diambil dari ongkir.'
	)
);
$output = array(
	'config'=> $_setting,
	'name'	=> 'config',
	'title'	=> 'Global Configuration'
);
$form->set($output);
echo $form->show();
