<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$sys->link_css('assets/css/panel-split.css');

$partner_id = intval($_GET['partner_id'] ?? 0);
if (empty($partner_id)) {
	echo msg(lang('Mohon sertakan mitra driver yang ingin kamu lihat laporannya'), 'warning');
	return false;
}

if (!empty($_POST['search_submit_search'])) {
	if ($_POST['search_submit_search'] == 'RESET') {
		unset($_POST['search_ondate']);
	}
}

$partner_name = ucwords($db->cacheGetOne('SELECT `name` FROM `delivery_partner` WHERE `id`='.$partner_id));
$title = lang('Laporan - '.$partner_name);

$format       = 'M Y';
$report_month = $_POST['search_ondate'] ?? date($format);
$title       .= ' ('.$report_month.')';

$partner_periode = $partner_name.' ('.$report_month.')';

$sys->nav_change(icon('fa-bar-chart').' '.$title);

$dates_list  = dashboard_date_period($report_month);
$dates_first = reset($dates_list);
$dates_end   = end($dates_list);
$dates_date  = array_map(function($date) {return substr($date, -2);}, $dates_list);

$add_sql = '`partner_id`='.$partner_id.' AND `ondate` BETWEEN "'.$dates_first.'" AND "'.$dates_end.'"';

$report_data = $db->getAssoc('SELECT `ondate`, `transaction`, `delivery_fee`, `platform_fee`, `balance`, `reward_daily`, `reward_monthly` FROM `delivery_report_daily` WHERE '.$add_sql);

$total_transaction = 0;
$total_delivery    = 0;
$total_platform    = 0;
$total_balance     = 0;
$total_reward_daily     = 0;
$total_reward_monthly     = 0;

foreach ($report_data as $val) {
  $total_transaction    += $val['transaction'];
  $total_delivery       += $val['delivery_fee'];
  $total_platform       += $val['platform_fee'];
  $total_balance        += $val['balance'];
  $total_reward_daily   += $val['reward_daily'];
  $total_reward_monthly += $val['reward_monthly'];
}

$past_transaction = $db->getRow('SELECT SUM(`platform_fee`) `platform_fee`, SUM(`balance`) `balance`, SUM(`reward_daily`) `reward_daily`, SUM(`reward_monthly`) `reward_monthly` FROM `delivery_report_daily` WHERE `partner_id`='.$partner_id.' AND `ondate` < "'.$dates_first.'"');
$past_balance     = $past_transaction['balance'] + $past_transaction['reward_daily'] + $past_transaction['reward_monthly'] - $past_transaction['platform_fee'];

if (!empty($_POST['export'])) {
	// generate transaksi harian
	$dates_date       = array_map(function($date) {return substr($date, -2);}, $dates_list);
	$transaction_date = [];
	foreach ($dates_list as $val) {
		$transaction_date[] = ($report_data[$val]['transaction'] ?? '0');
	}

	$excel_title = lang('Laporan - %s', $partner_periode);

	$data = array(
		'Transaksi - '.$partner_name => [
			$excel_title,
			'',
			'Transaksi Harian - '.$partner_name,
			$dates_date,
			$transaction_date,
			'',
			['Total Transaksi', '', $total_transaction],
			['Total Pendapatan', '', ($total_delivery-$total_platform)],
			['Saldo Bulan Lalu', '', $past_balance],
			['Total Deposit', '', $total_balance],
			['Total Fee Platform', '', $total_platform],
			['Saldo Per '.date('d M Y', strtotime($dates_end)),  '', ($past_balance + $total_balance + $total_reward_daily + $total_reward_monthly - $total_platform)],
			['Bonus Harian',  '', $total_reward_daily],
			['Bonus Bulanan',  '', $total_reward_monthly]
		],
	);

	_lib('excel')->create($data)->download($excel_title.'.xlsx');				
	die();					
}


include tpl('report');


include 'report_balance.php';
?>
<div class="panel_list_table">
	<?php
	include 'report_transaction.php';
	?>
</div>
<?php
