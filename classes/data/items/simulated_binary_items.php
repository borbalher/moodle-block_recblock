<?php

require_once '../../recommender/items/recommender_item.php';
require_once 'idataitems.php';

class simulated_binary_items implements idataitems{
	
	protected $numitems;
	protected $numvars;
	
	public function __construct($numitems, $numvars){
		$this->numitems = $numitems;
		$this->numvars = $numvars;
	}
	
	public function get_data_items(){
		$items = [];

		$continue = true;
		for($i=0;$i<$this->numitems;$i++){
			$type = [];
			while($continue){				
				for($j=0;$j<$this->numvars;$j++){
					array_push($type, rand(0,1));
				}
				if(block_recblock_get_vector_sum($type)!=0){
					$continue = false;
				}else{
					$type = [];
				}
			}
			$item = new recommender_item($i, "n".$i, $i, "url".$i, $type);
			array_push($items, $item);
			$continue = true;
		}
		return $items;
	}
}