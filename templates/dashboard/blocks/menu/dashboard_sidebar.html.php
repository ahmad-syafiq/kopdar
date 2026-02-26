<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

if (!function_exists('_dashboard_sidebar'))
{
  function _dashboard_sidebar($menus, $level = -1, $id='')
  {
    $output = '';
    if (!empty($menus))
    {
      $highlight = menu_parent_ids(@$_GET['menu_id'], $menus);
      if ($level == -1)
      {
        $output = call_user_func(__FUNCTION__, menu_parse($menus), ++$level);
      }else
      if (empty($level))
      {
        global $Bbc;
        if (empty($Bbc))
        {
          $Bbc = new stdClass;
        }
        if (empty($Bbc->menu_vertical))
        {
          $Bbc->menu_vertical = 1;
        }else{
          $Bbc->menu_vertical++;
        }
        $id = 'menu_v'.$Bbc->menu_vertical;
        $out = '';
        foreach ($menus as $menu)
        {
          $sub = call_user_func(__FUNCTION__, $menu['child'], ++$level, $id);
          $act = in_array($menu['id'], $highlight) ? ' active' : '';
          $alt = trim(strip_tags($menu['title']));
          if (!empty($sub))
          {
            $out .= '<li class="treeview'.$act.'"><a href="#'.$id.$level.'" title="'.$alt.'"><i class="fa fa-th"></i><span> '.$menu['title'].'</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>'.$sub.'</li>';
          }else{
            $out .= '<li class="'.$act.'"><a href="'.$menu['link'].'" title="'.$alt.'"><i class="fa fa-th"></i><span> '.$menu['title'].'</span></a></li>';
          }
        }
        $output = '<ul id="'.$id.'" class="sidebar-menu" data-widget="tree">'.$out.'</ul>';
      }else {
        $id .= $level;
        $out = '';
        foreach ($menus as $menu)
        {
          $sub = call_user_func(__FUNCTION__, $menu['child'], ++$level, $id);
          $act = in_array($menu['id'], $highlight) ? ' active' : '';
          $alt = trim(strip_tags($menu['title']));
          if (!empty($sub))
          {
            $out .= '<li class="treeview'.$act.'"><a href="#'.$id.$level.'" title="'.$alt.'"><i class="fa fa-circle-o"></i><span> '.$menu['title'].'</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>'.$sub.'</li>';
          }else{
            $out .= '<li class="'.$act.'"><a href="'.$menu['link'].'" title="'.$alt.'"><i class="fa fa-circle-o"></i><span> '.$menu['title'].'</span></a><li>';
          }
        }
        $output = '<ul id="'.$id.'" class="treeview-menu">'.$out.'</ul>';
      }
    }
    return $output;
  }  
}

echo _dashboard_sidebar($menus);
