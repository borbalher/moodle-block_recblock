<?php
defined('MOODLE_INTERNAL') || die();

class recommender_items{
	protected $items;

	public function __construct(idataitems $dataitems){
		$this->items = $dataitems->get_data_items();
	}

	public function set_items($items){
		$this->items = $items;
	}
	
	public function get_items(){
		return $this->items;
	}

}