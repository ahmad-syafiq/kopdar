<?php if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$_CONFIG['template'] = 'dashboard';
$sys->layout_change('dashboard');

switch ($Bbc->mod['task'])
{
	case 'main': // Untuk menampilkan halaman utama dashboard
		include 'main.php';
		break;

	case 'partner': // Untuk menampilkan list & add mitra / driver
		include 'partner.php';
		break;
	case 'partner_edit': // Untuk menambahkan mitra / driver
		include 'partner_edit.php';
		break;
	case 'partner_list': // Untuk menampilkan list mitra / driver
		include 'partner_list.php';
		break;

	case 'transaction': // Untuk menampilkan list $ add transaksi mitra / driver
		include 'transaction.php';
		break;
	case 'transaction_edit': // Untuk menambahkan transaksi mitra / driver
		include 'transaction_edit.php';
		break;
	case 'transaction_list': // Untuk menampilkan list transaksi mitra / driver
		include 'transaction_list.php';
		break;

	case 'balance': // Untuk menampilkan list $ add deposit mitra / driver
		include 'balance.php';
		break;
	case 'balance_edit': // Untuk menambahkan deposit mitra / driver
		include 'balance_edit.php';
		break;
	case 'balance_list': // Untuk menampilkan list deposit mitra / driver
		include 'balance_list.php';
		break;

	case 'report': // Untuk menampilkan summary transaksi bulanan mitra / driver
		include 'report.php';
		break;




	case 'account':
		include 'account.php';
		break;
	case 'account_password':
		include 'account_password.php';
		break;

	case 'logout':
		redirect('user/logout');
		break;


	case 'sync': // untuk syncron data
		$sys->set_layout('blank.php');
 		$task = isset($_URI['3']) ? preg_replace('~[.\/]~', '_', $_URI['3']) : ''; // diamankan dari orang iseng
 		if ($task) {
 			$file = __DIR__.'/sync/'.$task.'.php';
 			if (is_file($file)) {
 				include $file;
 			}else{
 				echo 'Invalid action <b>'.$task.'</b> has been received...';
 			}
 		}else{
 			echo 'Invalid action <b>'.$Bbc->mod['task'].'</b> has been received...';
 		}
		break;


	default:
		echo 'Invalid action <b>'.$Bbc->mod['task'].'</b> has been received...';
		break;
}

