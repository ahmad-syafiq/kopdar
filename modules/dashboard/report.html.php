<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

?>
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

<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><?php echo lang('Rangkuman Transaksi - %s', $partner_periode) ?></h3>
	</div>
	<div class="panel-body table_summary">
		<?php echo table([
									'Total Transaksi'                                 => money($total_transaction), 
									'Total Pendapatan'                                => money($total_delivery-$total_platform),
									'Saldo Bulan Lalu'                                => money($past_balance),
									'Total Deposit'                                   => money($total_balance),
									'Total Fee Platform'                              => money($total_platform),
									'Saldo Per '.date('d M Y', strtotime($dates_end)) => money($past_balance + $total_balance + $total_reward_daily + $total_reward_monthly - $total_platform),
									'Bonus Harian'                                    => money($total_reward_daily),
									'Bonus Bulanan'                                   => money($total_reward_monthly),
								]);?>
	</div>
</div>


<div class="panel_list_table">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo lang('Transaksi Harian - %s', $partner_periode);?></h3>
		</div>
		<div class="panel-body">
			<div name="roll">
				<table class="table table-bordered table-striped text-center">
					<thead>
						<tr>
							<?php
							foreach ($dates_list as $val) {
								?>
								<th><?php echo substr($val, -2);?></th>
								<?php
							}
							?>
						</tr>
					</thead>
					<tbody>
						<tr>
							<?php
							foreach ($dates_list as $val) {
								?>
								<td><?php echo ($report_data[$val]['transaction'] ?? '-') ?></td>
								<?php
							}
							?>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div class="panel-footer">
			<!-- <button type="button" class="btn btn-default export">button</button> -->
			

			<form action="" method="POST" role="form">
				<button type="submit" name="export" value="excel" class="btn btn-primary">Export</button>
			</form>

		</div>
	</div>

	<?php
	// $form = _lib('pea',  'delivery_transaction');
	// $form->initRoll($add_sql.' ORDER BY `id` DESC');

	// $form->roll->addReport('excel');
	// $form->roll->setSaveTool(false);
	// $form->roll->setDeleteTool(false);

	// $form->roll->addInput('header','header');
	// $form->roll->input->header->setTitle(lang('Laporan ').$title);

	// $form->roll->addInput('ondate','sqlplaintext');
	// $form->roll->input->ondate->setTitle(lang('Tanggal'));
	// $form->roll->input->ondate->setDisplayFunction(function($s){ return date('d M Y', strtotime($s)); }, false);

	// $form->roll->addInput('invoice','sqlplaintext');

	// $form->roll->addInput('partner_name','sqlplaintext');
	// $form->roll->input->partner_name->setTitle(lang('Nama Mitra'));

	// $form->roll->addInput('customer_name','sqlplaintext');
	// $form->roll->input->customer_name->setTitle(lang('Nama Pemesan'));
	// $form->roll->input->customer_name->setDisplayFunction(function($s){ return ucwords($s); });

	// $form->roll->addInput('customer_order','sqlplaintext');
	// $form->roll->input->customer_order->setTitle(lang('Detail Pesanan'));
	// $form->roll->input->customer_order->setDisplayFunction(function($s){ return ucwords($s); });
	// $form->roll->input->customer_order->setDisplayColumn(true);

	// $form->roll->addInput('merchant_name','sqlplaintext');
	// $form->roll->input->merchant_name->setTitle(lang('Nama Kedai'));
	// $form->roll->input->merchant_name->setDisplayFunction(function($s){ return ucwords($s); });

	// $form->roll->addInput('customer_address','sqlplaintext');
	// $form->roll->input->customer_address->setTitle(lang('Alamat Pengiriman'));
	// $form->roll->input->customer_address->setDisplayFunction(function($s){ return ucwords($s); });

	// $form->roll->addInput('amount','sqlplaintext');
	// $form->roll->input->amount->setTitle(lang('Total Belanja'));
	// $form->roll->input->amount->setNumberFormat();
	// $form->roll->input->amount->setExportFunction(function($d){ return str_replace(',', '', $d); });

	// $form->roll->addInput('delivery_fee','sqlplaintext');
	// $form->roll->input->delivery_fee->setTitle(lang('Ongkir'));
	// $form->roll->input->delivery_fee->setNumberFormat();
	// $form->roll->input->delivery_fee->setExportFunction(function($d){ return str_replace(',', '', $d); });

	// $form->roll->addInput('platform_fee','sqlplaintext');
	// $form->roll->input->platform_fee->setTitle(lang('Potongan'));
	// $form->roll->input->platform_fee->setNumberFormat();
	// $form->roll->input->platform_fee->setExportFunction(function($d){ return str_replace(',', '', $d); });


	// $form->roll->action();
	// echo $form->roll->getForm();
	?>
</div>

<style>
	.loadings {
		background: #000;
		position: fixed;
		z-index: 9999;
		opacity: 0.9;
		top: 50%;
		left: 45%;
		padding: 5px;
		margin: 0 auto;
		border: 1px solid #fff;
		border-radius: 5px;
		box-shadow: 0 0 5px #000;
	}
	.loadings_background{
	  background: rgba(255,255,255,.4);
	  height: 100%;
	  width: 100%;
	  left: 0px;
	  top: 0px;
	  position: fixed;
	  z-index: 13000;
	}
	.loadings span {
		 background: url(/kopdar/templates/dashboard/img/loader.gif) no-repeat;
		 color: white;
		 padding-left: 24px;
		 background-size: 18px;
	}
</style>
<script type="text/javascript">
_Bbc(function($) {
	var loading_show = function () {
	  var a = '<div class="loadings_background"><div class="loadings"><span>Loading...</span></div></div>';
	  $('body').append(a)
	};
	var loading_hide = function () {
	  $('body .loadings_background').remove()
	};

	$(".export").click(function(e) {
		e.preventDefault();
		$.ajax({
			url : window.location.href,
			type: "post",
			data: {
				"export": "excel"
			},
			dataType: "json",
			beforeSend: function() {
				loading_show();
			},
			success:function(result){
				// if (result == 'ok') {

				// }else{

				// };
				loading_hide();
			}
		});
	})

});


</script>
