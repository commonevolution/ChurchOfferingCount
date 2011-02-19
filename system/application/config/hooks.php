<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	http://codeigniter.com/user_guide/general/hooks.html
|
*/
$hook['pre_controller'][] = array(
								'class'    => 'Auth_filter',
								'function' => 'before',
								'filename' => 'Auth.php',
								'filepath' => 'hooks',
								'params'   => array()
								);
$hook['pre_controller'][] = array(
								'class'    => 'ExceptionHook',
								'function' => 'SetExceptionHandler',
								'filename' => 'ExceptionHook.php',
								'filepath' => 'hooks'
								);
/* End of file hooks.php */
/* Location: ./system/application/config/hooks.php */
?>