<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

include_once __DIR__ . '/functions/balance.php';
include_once __DIR__ . '/functions/chart.php';
include_once __DIR__ . '/functions/date.php';
include_once __DIR__ . '/functions/form.php';
include_once __DIR__ . '/functions/report.php';

function dashboard_panel($file_list='', $file_edit='', $header_right='Tambah Data', $header_left='')
{
	global $Bbc, $sys, $db, $user;
	$sys->link_css('assets/css/panel-split.css');
	$sys->link_js('assets/js/panel-split.js');

	$bt   = debug_backtrace();
	$path = str_replace(_ROOT, '', dirname($bt[0]['file'])).'/';
	$path = _mst($path);

	ob_start();
	include _ROOT.$path.$file_list;
	$data_list = ob_get_contents();
	ob_end_clean();

	ob_start();
	include _ROOT.$path.$file_edit;
	$data_edit = ob_get_contents();
	ob_end_clean();

	$output = '';
	if (!empty($data_list) && !empty($data_edit))
	{
		$data_add = '';
		if (!empty($header_right)) {
			$data_add = '<button id="panel_button" class="btn btn-primary btn-sm pull-right show">'.
										$header_right.
									'</button>';
		}

		$output = '<div class="row" style="background-color: white;">'.
								'<div class="col-md-12">'.
									'<div class="clearfix">'.
										'<div class="pull-left">'.
											$header_left.
										'</div>'.
										$data_add.
									'</div>'.
									'<div class="row_flex">'.
										'<div id="panel_list" class="full">'.
											'<div class="panel_list_table">'.
												$data_list.
											'</div>'.
										'</div>'.
										'<div id="panel_edit">'.
											$data_edit.
										'</div>'.
									'</div>'.
								'</div>'.
							'</div>';
	}
	return $output;
}


function dashboard_card($value=0, $title='', $icon='', $link='', $color='')
{
	$output = '<div class="col-lg-3 col-xs-6">'.
						    '<div class="small-box bg-'.($color ?? 'yellow').'">'.
						      '<div class="inner">'.
						        '<h3>'.$value.'</h3>'.
						        '<p>'.$title.'</p>'.
						      '</div>'.
						      '<div class="icon">'.
						        '<i class="'.$icon.'"></i>'.
						      '</div>'.
						      // '<a href="'.$link.'" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>'.
						      '<span class="small-box-footer"></span>'.
						    '</div>'.
						  '</div>';
	return $output;
}

function dashboard_money_indonesia($number=0) {
  $minus  = $number < 0;
  $number = abs($number);

  if ($number >= 1000000000) {
    $out = number_format($number / 1000000000, 1, ',', '') . ' M';
  } elseif ($number >= 1000000) {
    $out = number_format($number / 1000000, 1, ',', '') . ' JT';
  } elseif ($number >= 1000) {
    $out = number_format($number / 1000, 1, ',', '') . ' RB';
  } else {
    $out = number_format($number, 1, ',', '');
  }

  return ($minus ? '-' : '') . preg_replace('~,0~i', '', $out);
}
