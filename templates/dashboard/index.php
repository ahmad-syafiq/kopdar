<?php $is_admin = _ADMIN != '' ? true : false; 
// pr($sys, __FILE__.':'.__LINE__);die();
?>
<!DOCTYPE html>
<html>
  <head>
    <?php echo $sys->meta(true);?>
    <?php if (ini_get('display_errors') == 1) echo '<meta name="robots" content="noindex">'; ?>
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo $_CONFIG['site']['icon'] ? site_url('images/'.$_CONFIG['site']['icon']) : ''?>">
  </head>

  <body class="hold-transition skin-yellow <?php echo $is_admin ? '' : 'fixed' ?> sidebar-mini">
    <!-- Site wrapper -->
    <div class="wrapper">
      <!-- Header Menu -->
      <header class="main-header">
        <div class="wrapper_logo">
          <?php echo $sys->block_show('logo');?>
        </div>
        <nav class="navbar navbar-static-top">
          <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <?php echo $sys->block_show('header');?>
        </nav>
      </header>
      <!-- /.main-header -->

      <!-- Sidebar Menu -->
      <aside class="main-sidebar <?php echo $is_admin ? 'admin' : '' ?>">
        <section class="sidebar">
          <?php echo $sys->block_show('left');?>
        </section>
      </aside>
      <!-- /.main-sidebar -->

      <!-- Content Wrapper. Contains page content -->
      <div id="content_wrapper" class="content-wrapper">
        <?php echo $sys->block_show('content_top');?>
        <section class="content">
          <?php echo trim($Bbc->content);?>
        </section>
      </div>
      <!-- /.content-wrapper -->

      <footer class="main-footer">
        <div class="pull-right hidden-xs">
          <!-- <b>Version</b> 2.4.18 -->
        </div>
        <?php echo config('site','footer');?>
      </footer>

    </div>
    <!-- ./wrapper -->

    <script src="<?php echo _URL;?>templates/admin/bootstrap/js/bootstrap.min.js"></script>
    <script src="<?php echo $sys->template_url;?>assets/bower_components/raphael/raphael.min.js"></script>
    <script src="<?php echo $sys->template_url;?>assets/bower_components/morris.js/morris.min.js"></script>
    <script src="<?php echo $sys->template_url;?>assets/bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
    <script src="<?php echo $sys->template_url;?>assets/bower_components/fastclick/lib/fastclick.js"></script>
    <script src="<?php echo $sys->template_url;?>assets/dist/js/adminlte.js"></script>
    <script src="<?php echo $sys->template_url;?>assets/dist/js/demo.js"></script>

    <?php
    // echo $sys->template_js;
    $sys->link_js($sys->template_url.'js/application.js', false);
    ?>

  </body>
</html>
