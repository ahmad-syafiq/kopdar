<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

function dashboard_date_period($month='')
{
	$dates        = DateTime::createFromFormat('M Y', $month);
	$dates_start  = (clone $dates)->modify('first day of this month');
	$dates_end    = (clone $dates)->modify('last day of this month')->modify('+1 day');
	$dates_period = new DatePeriod($dates_start, new DateInterval('P1D'), $dates_end);
	$dates_list   = [];
	foreach ($dates_period as $dt) {
	  $dates_list[] = $dt->format('Y-m-d');
	}
	return $dates_list;
}

function dashboard_date_convert($format)
{
  $map = [
    'Y' => 'yyyy',
    'y' => 'yy',
    'm' => 'mm',
    'n' => 'm',
    'F' => 'MM',
    'M' => 'M',
    'd' => 'dd',
    'j' => 'd',
  ];

  return strtr($format, $map);
}
