<?php

require_once 'abstract-xh-paging-model.php';
require_once 'class-xh-paging-model-simple.php';
class XH_Paging_Model extends XH_Abstract_Paging_Model {
	var $urlCallback=null;
	
	public function __construct($page_index, $page_size, $total_count, $urlCallback=null) {
		parent::__construct ( $page_index, $page_size, $total_count );
		$this->urlCallback = $urlCallback;
	}
	
	
	protected function url($page_index) {
		if($this->urlCallback==null){
			return '';
		}
		
		return call_user_func_array($this->urlCallback, array(
			'page_index'=>$page_index
		));
	}
	
	public function wp(){
		$output ='<div class="tablenav-pages"><span class="displaying-num">'.esc_html(sprintf(__('%s items',XH_WECHAT),$this->total_count)).'</span>';
		$output.='<span class="pagination-links">';
		
		if(! $this->is_first_page){
			$output.='<a class="first-page" href="'.esc_attr($this->url(1)).'"><span class="screen-reader-text">'.__('first page',XH_WECHAT).'</span><span aria-hidden="true">«</span></a>';
			$output.='<a class="prev-page" href="'.esc_attr($this->url($this->page_index-1)).'"><span class="screen-reader-text">'.__('prev page',XH_WECHAT).'</span><span aria-hidden="true">‹</span></a>';
		}else{
			$output.='<span class="tablenav-pages-navspan" aria-hidden="true">«</span>';
			$output.='<span class="tablenav-pages-navspan" aria-hidden="true">‹</span>';
		}
		
 		$output.='<span class="paging-input"> '.sprintf(__('page %s, %s pages',XH_WECHAT),'<input class="current-page" style="width:30px;" type="text" value="'.esc_attr($this->page_index).'"  aria-describedby="table-paging">','<span class="total-pages">'.esc_html($this->page_count).'</span>').' </span>';

 		if(!$this->is_last_page){
 			$output .='<a class="next-page" href="'.esc_attr($this->url($this->page_index+1)).'"><span class="screen-reader-text">'.__('next page',XH_WECHAT).'</span><span aria-hidden="true">›</span></a>';
 			$output .='<a class="last-page" href="'.esc_attr($this->url($this->page_count)).'"><span class="screen-reader-text">'.__('last page',XH_WECHAT).'</span><span aria-hidden="true">»</span></a></span>';
 		}else{
 			$output.='<span class="tablenav-pages-navspan" aria-hidden="true">›</span>';
 			$output.='<span class="tablenav-pages-navspan" aria-hidden="true">»</span>';
 		}

		$output.='</div>';
		return $output;
	}
	
	public function bootstrap($class = 'pagination') {
	
		if ($this->page_count <= 0) {
			return '';
		}
		$output = '<ul class="' . $class . '">';
		
		if (! $this->is_first_page) {
			$output .= '<li class="first"><a href="' . $this->url ( $this->page_index - 1 ) . '"><<</a></li>';
		} else {
			$output .= '<li class="first disabled"><span><<</span></li>';
		}
		
		if ($this->start_page_index > 1) {
			$output .= '<li><a href="' . $this->url ( 1 ) . '">1</a></li>';
			if ($this->start_page_index > 2) {
				$output .= '<li><span>...</span></li> ';
			}
		}
		
		for($i = $this->start_page_index; $i <= $this->end_page_index; $i ++) {
			$output .= '<li ' . ($i == $this->page_index ? 'class="page active"' : 'class="page"') . '><a href="' . $this->url ( $i ) . '">' . $i . '</a></li>';
		}
		
		if ($this->end_page_index < $this->page_count) {
			if ($this->end_page_index < $this->page_count - 1) {
				$output .= ' <li><span>...</span></li>';
			}
			$output .= '<li ><a href="' . $this->url ( $this->page_count ) . '">' . $this->page_count . '</a></li>';
		}
		
		if ($this->is_last_page) {
			$output .= '<li class="last disabled"><span>>></span></li>';
		} else {
			$output .= ' <li class="last"><a href="' . $this->url ( $this->page_index + 1 ) . '">>></a></li>';
		}
		
		$output .= "</ul>";
		return $output;
	}
}