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
 * Recblock block simple_weight_recommender.php file
 *
 * @package    block_recblock
 * @copyright  Boris Ballester Hernandez <borbalher@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once $CFG->dirroot.'/blocks/recblock/classes/recommender/recommender.php';


class simple_weight_recommender extends recommender{
	protected $userweights;

	public function get_user_weights(){
		return $this->userweights;
	}

	public function set_user_weights($userweights){
		$this->userweights = $userweights;
	}
	public function is_ready(){
		return true;
	}

	protected function build(){
		$this->userweights = [];
		for($i=0;$i<count(current($this->items->get_items())->get_vector());$i++){
			array_push($this->userweights,1);
		}

		$threshold = count($this->userweights)/2;

		foreach($this->items->get_items() as $key => $value){
			if($this->usermodel->has_preference($key)){
				$itemparams = $this->items->get_items()[$key]->get_vector();
				$sum = block_recblock_get_vector_sum($itemparams);
				if($sum >= $threshold && !$this->usermodel->like($key)){
					for($j=0;$j<count($this->userweights);$j++){
						$this->userweights[$j] /= 2;
					}
				}else if($sum <= $threshold && $this->usermodel->like($key)){
					for($j=0;$j<count($this->userweights);$j++){
						$this->userweights[$j] *= 2;
					}
				}
			}
		}
	}

	protected function create_recommendation_value($key){
		$this->recommendationvalues[$key] = block_recblock_dot_product($this->items->get_items()[$key]->get_vector(),$this->userweights);
	}

	protected function sort_recommendation_values(){
		arsort($this->recommendationvalues);
	}
}
