<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/user_guide/general/hooks.html
|
*/
$hook['post_controller_constructor'][] = array(
    'class'    => 'Jys_system_switch',
    'function' => 'index',
    'filename' => 'Jys_system_switch.php',
    'filepath' => 'hooks'
);

$hook['post_controller_constructor'][] = array(
    'class'    => 'Jys_permission_check',
    'function' => 'index',
    'filename' => 'Jys_permission_check.php',
    'filepath' => 'hooks'
);