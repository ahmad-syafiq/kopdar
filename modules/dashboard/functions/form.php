<?php if (!defined('_VALID_BBC')) exit('No direct script access allowed');

function dashboard_form_date($format='d-m-y', $search_key='search_ondate', $search_val='', $caption='Periode')
{
	global $sys;
	$sys->link_js(_URL.'includes/lib/pea/includes/FormDate.js', false);
	$sys->link_js(_URL.'modules/dashboard/assets/js/datepicker.js', false);

	if (empty($format)) $format = 'd-m-y';

	if (!empty($_POST[$search_key])) {
		$search_val = date($format, strtotime($_POST[$search_key]));
	}
	
	$datepicker_format = dashboard_date_convert($format);

	$out = '<div class="form-group">'.
						'<label class="sr-only">'.$caption.'</label>'.
						'<input type="text" name="'.$search_key.'" value="'.$search_val.'" class="form-control datepicker" title="'.$caption.'" placeholder="'.$caption.'" data-autoclose="true" data-format="'.$datepicker_format.'" data-min-view-mode="months" data-start-view="months">'.
						'<input type="hidden" id="periode_value" value="'.$search_val.'">'.
					'</div>'.
	'';
	return $out;
}



