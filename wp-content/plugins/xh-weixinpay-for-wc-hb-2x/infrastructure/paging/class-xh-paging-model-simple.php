<?php
class XH_Paging_Model_Simple extends XH_Abstract_Paging_Model{
	public function __construct($page_index, $page_size, $total_count){
		parent::__construct ( $page_index, $page_size, $total_count );
	}

}