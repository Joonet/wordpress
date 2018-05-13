<?php
if (! defined ( 'ABSPATH' ))
	exit (); // Exit if accessed directly

class XH_Log_File_Handler implements XH_Log_Handler {
	private $handle = null;
	private $file = null;

	public function __construct($file = '') {
		$this->file=$file;
	}

	public function write($msg) {
		if(!$this->handle){
			if(!empty($this->file))	{
				try {
					$this->handle = fopen ( $this->file, 'a' );
				} catch (Exception $e) {
					//ignore
				}
			}
		}
		if($this->handle){
			fwrite ( $this->handle, $msg, 4096 );
		}
	}

	public function __destruct() {
		if ($this->handle)
			fclose ( $this->handle );
	}
}