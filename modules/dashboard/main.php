<?php if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$sys->nav_change(icon('fa-dashboard').' '.lang('Dashboard'));

$report_data = $db->getRow('SELECT 
		SUM(`partner`) AS `partner`, SUM(`transaction`) AS `transaction`, SUM(`balance`) AS `balance`, SUM(`merchant`) AS `merchant`
	FROM `delivery_report`
	WHERE 1
');

$partners = $db->cacheGetAssoc('SELECT `id`, `name` FROM `delivery_partner` WHERE 1');

$format       = 'M Y';
$report_month = $_POST['search_ondate'] ?? date($format);

$dates_list  = dashboard_date_period($report_month);
$dates_first = reset($dates_list);
$dates_end   = end($dates_list);

$add_sql = '`ondate` BETWEEN "'.$dates_first.'" AND "'.$dates_end.'"';

$report_data_daily = $db->getAll('SELECT `partner_id`, `ondate`, `transaction`, `platform_fee` FROM `delivery_report_daily` WHERE '.$add_sql);

$chart_transaction = [];
$total_transaction = 0;
$chart_platform    = [];
$total_platform    = 0;
foreach ($report_data_daily as $r) {
  $chart_transaction[$r['ondate']][$r['partner_id']] = $r['transaction'];
  $total_transaction += $r['transaction'];

  $chart_platform[$r['ondate']][$r['partner_id']] = $r['platform_fee'];
  $total_platform += $r['platform_fee'];
}

$chart_color = dashboard_chart_color(array_keys($partners));

$chart_transaction_json = [];
$chart_platform_json    = [];
foreach ($dates_list as $ondate) {
	$row_transaction = ['y' => $ondate];
	$row_platform 	 = ['y' => $ondate];

	foreach ($partners as $p_id => $p_name) {
		$row_transaction[$p_id] = $chart_transaction[$ondate][$p_id] ?? 0;
		$row_platform[$p_id]    = $chart_platform[$ondate][$p_id] ?? 0;
	}
	$chart_transaction_json[] = $row_transaction;
	$chart_platform_json[]    = $row_platform;
}

include tpl('main');




