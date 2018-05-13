<?php
if (! defined ( 'ABSPATH' ))
	exit (); // Exit if accessed directly

require 'interface-xh-log-handler.php';
require 'class-xh-log-file-handler.php';
class XH_Log {
	private $handler = null;
	private $level = 15;
	private static $instance = null;
	private function __construct() {
	}
	
	private function __clone() {
	}
	
	public static function Init($handler = null, $level = 15) {
		if (! self::$instance instanceof self) {
			self::$instance = new self ();
			self::$instance->__setHandle ( $handler );
			self::$instance->__setLevel ( $level );
		}
		return self::$instance;
	}
	private function __setHandle($handler) {
		$this->handler = $handler;
	}
	private function __setLevel($level) {
		$this->level = $level;
	}
	public static function DEBUG($msg) {
		if(!self::$instance){
			return;
		}
		self::$instance->ERROR( $msg );
	}
	public static function WARN($msg) {
	if(!self::$instance){
			return;
		}
		self::$instance->ERROR( $msg );
	}
	public static function ERROR($msg) {
		if(!self::$instance){
			return;
		}
		
		$debugInfo = debug_backtrace ();
		$stack = "[";
		foreach ( $debugInfo as $key => $val ) {
			if (array_key_exists ( "file", $val )) {
				$stack .= ",file:" . $val ["file"];
			}
			if (array_key_exists ( "line", $val )) {
				$stack .= ",line:" . $val ["line"];
			}
			if (array_key_exists ( "function", $val )) {
				$stack .= ",function:" . $val ["function"];
			}
		}
		$stack .= "]";
		self::$instance->write ( 8, $stack . $msg );
	}
	public static function INFO($msg) {
		if(!self::$instance){
			return;
		}
		self::$instance->ERROR (  $msg );
	}
	private function getLevelStr($level) {
		switch ($level) {
			case 1 :
				return 'debug';
				break;
			case 2 :
				return 'info';
				break;
			case 4 :
				return 'warn';
				break;
			case 8 :
				return 'error';
				break;
			default :
		}
	}
	protected function write($level, $msg) {
		if (($level & $this->level) == $level) {
			$msg = '[' . date_i18n ( 'Y-m-d H:i:s' ) . '][' . $this->getLevelStr ( $level ) . '] ' . $msg . "\n";
			$this->handler->write ( $msg );
		}
	}
}
