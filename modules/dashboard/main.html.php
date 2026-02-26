<?php if (!defined('_VALID_BBC')) exit('No direct script access allowed');
// https://nbs-it.github.io/ionicons-v2.0.1/

$sys->link_js(_URL.'modules/dashboard/assets/js/chart.js', false);

?>
<div class="row">
	<?php 
		echo dashboard_card(dashboard_money_indonesia($report_data['partner']), lang('Mitra Driver'), 'ion ion-person', site_url('dashboard/partner'), 'yellow');
		echo dashboard_card(dashboard_money_indonesia($report_data['transaction']), lang('Transaksi'), 'ion ion-stats-bars', '', 'aqua');
		echo dashboard_card(dashboard_money_indonesia($report_data['balance']), lang('Saldo Deposit'), 'ion ion-cash', '#', 'green');
		echo dashboard_card(dashboard_money_indonesia($report_data['merchant']), lang('Kedai / Toko'), 'ion ion-bag', '#', 'red');
	?>
</div>

<form method="POST" action="" name="search" class="form-inline pull-right" role="form">
	<?php echo dashboard_form_date($format, 'search_ondate', $report_month, 'Periode');?>
	<button type="submit" name="search_submit_search" value="SEARCH" class="btn btn-default btn-secondary">
		<span class="glyphicon glyphicon-search"></span>
	</button>
	<button type="submit" name="search_submit_search" value="RESET" class="btn btn-default btn-secondary">
		<span class="glyphicon glyphicon-remove-circle"></span>
	</button> 
</form>
<div class="clearfix"></div>

<div class="row">
	<div class="col-md-6">
		<div class="box box-info">
		  <div class="box-header with-border">
		    <h3 class="box-title"><?php echo lang('Transaksi %s', $report_month)?></h3>
		  </div>
		  <div class="box-body chart-responsive">
		    <div class="chart line" id="chart_transaction" style="height: 300px;"
			    data-json="<?php echo htmlspecialchars(json_encode($chart_transaction_json))?>"
			    data-json-x="y"
			    data-json-y="<?php echo htmlspecialchars(json_encode(array_keys($partners)))?>"
			    data-json-label="<?php echo htmlspecialchars(json_encode(array_values($partners)))?>"
			    data-json-color="<?php echo htmlspecialchars(json_encode(array_values($chart_color)))?>"
		    ></div>
		  </div>
		  <div class="box-footer with-border"><?php echo lang('Total Transaksi : %s', money($total_transaction)) ?></div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="box box-warning">
		  <div class="box-header with-border">
		    <h3 class="box-title"><?php echo lang('Biaya Platform %s', $report_month)?></h3>
		  </div>
		  <div class="box-body chart-responsive">
		    <div class="chart line" id="chart_platform" style="height: 300px;"
			    data-json="<?php echo htmlspecialchars(json_encode($chart_platform_json))?>"
			    data-json-x="y"
			    data-json-y="<?php echo htmlspecialchars(json_encode(array_keys($partners)))?>"
			    data-json-label="<?php echo htmlspecialchars(json_encode(array_values($partners)))?>"
			    data-json-color="<?php echo htmlspecialchars(json_encode(array_values($chart_color)))?>"
		    ></div>
		  </div>
		  <div class="box-footer with-border"><?php echo lang('Total Biaya Platform : %s', money($total_platform)) ?></div>
		</div>
	</div>
</div>

