<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

if (!empty($config['is_link']))
{
	?>
	<a href="<?php echo $output['link'];?>" class="logo" title="<?php echo $output['title'];?>"<?php echo $output['attribute']; ?>>
		<span class="logo-mini"><?php echo image($output['image'], $output['size'], 'alt="'.$output['title'].'" title="'.$output['title'].'"');?></span>
		<span class="logo-lg"><?php echo image($output['image'], $output['size'], 'alt="'.$output['title'].'" title="'.$output['title'].'"');?></span>
	</a>
	<?php
}else{
	?>
	<div class="logo" <?php echo $output['attribute']; ?>>
		<span class="logo-mini"><?php echo image($output['image'], $output['size'], 'alt="'.$output['title'].'" title="'.$output['title'].'"');?></span>
		<span class="logo-lg"><?php echo image($output['image'], $output['size'], 'alt="'.$output['title'].'" title="'.$output['title'].'"');?></span>
	</div>
	<?php
}

?>
<script type="text/javascript">
_Bbc(function($) {
	$(".logo-mini").append(" " + ($(".logo").data("sm") ?? ""));
	$(".logo-lg").append(" " + ($(".logo").data("lg") ?? ""));
});
</script>