<?php if (!defined('_VALID_BBC')) exit('No direct script access allowed');

if (empty($user->id)) {
	redirect(site_url('user/login'));
}
