<?php
link_js('includes/lib/pea/includes/formIsRequire.js', false);
?>

<div style="height:100vh; display:table; width:100%;">
	<div class="row" style="display:table-cell; vertical-align:middle">
		<div class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2" id="login_form">
			<div class="text-center">
		    <img src="<?php echo _URL.'images/'.$_CONFIG['site']['logo'];?>" alt="Logo" class="img-responsive center-block" style="max-height:120px;">
		  </div>
			<h3 class="text-center"><?php echo lang('Please login to your account');?></h3>
			<form class="center-block formIsRequire" method="POST" action="">
				<div class="form-group">
				  <label for="username"><?php echo lang('Username');?></label>
				  <input type="any" class="form-control" id="username" placeholder="<?php echo lang('Username');?>" req="any true" name="usr">
				</div>
				<div class="form-group">
				  <label for="password"><?php echo lang('Password');?></label>
				  <input type="password" class="form-control" id="password" placeholder="<?php echo lang('Password');?>" req="any true" name="pwd">
				</div>
				<input type="hidden" name="url" value="<?php echo $user_url; ?>" />
				<button type="submit" id="login_action" class="btn btn-default btn-block"><?php echo lang('Login');?></button>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
_Bbc(function($) {
	if ($(".alert.alert-info").length) {
	  $(".alert.alert-info").prependTo("#login_form");
	}

	$("#login_form form").on("submit", function () {
		$("#login_action").prop("disabled", true).text("Loading...");
	});
});
</script>