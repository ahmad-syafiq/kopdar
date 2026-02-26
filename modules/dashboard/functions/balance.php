<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

function dashboard_balance_update($trx_old=[], $trx_new=[], $user_id=0)
{
	global $db;
	if (!empty($trx_old)) {
		$db->Execute('UPDATE `delivery_partner` SET `balance`=`balance`+'.$trx_old['platform_fee'].' WHERE `id`='.$trx_old['partner_id']);
	}

	$type = 2;
	if (!empty($trx_new)) {
		$balance_id = $db->getOne('SELECT `id` FROM `delivery_partner_balance` WHERE `type`='.$type.' AND `ref_id`='.$trx_new['id']);
		if (empty($balance_id)) {
			$invoice = 'INV/'.preg_replace('~^\d{2}|-~i', '', $trx_new['ondate']).'/'.date('s').$trx_new['id'];
			$notes   = 'Biaya Platform Transaksi #'.$invoice;
			$db->Execute('INSERT INTO `delivery_partner_balance` SET 
											`partner_id`='.$trx_new['partner_id'].',
											`type`='.$type.',
											`ref_id`='.$trx_new['id'].',
											`created_by`='.$user_id.',
											`notes`="'.addslashes($notes).'"');
			$balance_id = $db->Insert_ID();
		}

		$db->Execute('UPDATE `delivery_partner_balance` SET 
										`ondate`="'.$trx_new['ondate'].'",
										`amount`='.$trx_new['platform_fee'].'
									WHERE `id`='.$balance_id);

		$db->Execute('UPDATE `delivery_partner` SET `balance`=`balance`-'.$trx_new['platform_fee'].' WHERE `id`='.$trx_new['partner_id']);
	}else{
		$db->Execute('DELETE FROM `delivery_partner_balance` WHERE `partner_id`='.$trx_old['partner_id'].' AND `type`='.$type.' AND `ref_id`='.$trx_old['id']);
	}
}
