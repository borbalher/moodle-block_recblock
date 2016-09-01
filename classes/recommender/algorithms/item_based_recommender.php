<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Recblock block item_based_recommender.php file
 *
 * @package    block_recblock
 * @copyright  Boris Ballester Hernandez <borbalher@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/blocks/recblock/classes/recommender/recommender.php');


class item_based_recommender extends recommender{
	protected $itemsmatrix;
	protected $threshold;
	protected $thresholdoperator;

	protected $similarityfunction;

	public function set_similarity_function($similarityfunction){
		$this->similarityfunction = $similarityfunction;
	}

	public function get_similarity_function(){
		return $this->similarityfunction;
	}

	public function set_threshold($threshold){
		$this->threshold = $threshold;
	}


	public function set_threshold_operator($thresholdoperator){
		$this->thresholdoperator = $thresholdoperator;
	}

	public function get_items_matrix(){
		return $this->items_matrix;
	}

	public function set_items_matrix($itemsmatrix){
		$this->items_matrix = $itemsmatrix;
	}

	private function calculate_item_matrix($i,$j,$func){
		return $func($this->items->get_items()[$i]->get_vector(),$this->items->get_items()[$j]->get_vector());
	}

	protected function build(){
		$size = count($this->items->get_items());
		for($i=0;$i<$size;$i++){
			for($j=0;$j<$size;$j++){
				$this->itemsmatrix[$i][$j] = $this->calculate_item_matrix($i,$j,$this->similarityfunction);
			}
		}
	}

	private function get_similar_items($itemkey){
		$similaritems = [];
		foreach($this->items->get_items() as $similaritemkey => $value){
			if($this->usermodel->is_relevant($similaritemkey) && recblock_evaluate($this->usermodel->get_user_profile()[$similaritemkey], $this->thresholdoperator, $this->threshold) ){
				array_push($similaritems,$similaritemkey);
			}
		}
		return $similaritems;

	}
	private function prediction($itemkey){
		$similaritems = $this->get_similar_items($itemkey);

		$numerator = 0;
		$denominator = 0;

		foreach($similaritems as $similaritemkey){
			$numerator += ($this->itemsmatrix[$itemkey][$similaritemkey])*$this->usermodel->get_user_profile()[$similaritemkey];
			$denominator += abs($this->itemsmatrix[$itemkey][$similaritemkey]);
		}


		return $numerator / $denominator;

	}

	public function is_ready(){
		if(isset($this->threshold) && isset($this->thresholdoperator) && isset($this->similarityfunction)){
			return true;
		}else{
			return false;
		}
	}

	protected function create_recommendation_value($key){
		$this->recommendationvalues[$key] = $this->prediction($key);
	}

	protected function sort_recommendation_values(){
		arsort($this->recommendationvalues);
	}

}
