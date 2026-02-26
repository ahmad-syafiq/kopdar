<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');
?>
<section class="content-header">
  <h1>
    <?php echo end($sys->arrNav)['title'] ?? 'Home'; ?>
  </h1>
	<?php echo $sys->nav_show();?>
</section>
