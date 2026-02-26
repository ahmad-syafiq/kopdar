<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

if (_URI != '/' && !empty($_GET['return'])) // jika dijalankan di http://localhost
{
  if (!preg_match('~^https?~i', $_GET['return'])) // ada case waktu user/notAllowed GET returnnya ga ada https
  {
    $_GET['return'] = preg_replace('~'.trim(_URI, '/').'(?:%2F|/)~i', '', $_GET['return']);
  }
}
