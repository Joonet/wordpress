<?php
if (! defined ( 'ABSPATH' ))
	exit (); // Exit if accessed directly
 
// 以下为日志
interface XH_Log_Handler {
	public function write($msg);
}
