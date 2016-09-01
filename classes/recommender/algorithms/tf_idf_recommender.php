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
 * Recblock block tf_idf_recommender.php file
 *
 * @package    block_recblock
 * @copyright  Boris Ballester Hernandez <borbalher@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once $CFG->dirroot.'/blocks/recblock/classes/recommender/recommender.php';



class tf_idf_recommender extends recommender{

    protected $tf;
    protected $idf;
    protected $logbase;
    protected $itemsmatrix;
    protected $userweights;


    public function get_log_base(){
    	return $this->logbase;
    }

    public function set_log_base($logbase){
    	$this->logbase = $logbase;
    }

    public function get_tf(){
    	return $this->tf;
    }

    public function set_tf($tf){
    	$this->tf = $tf;
    }

    public function get_idf(){
    	return $this->idf;
    }

    public function set_idf($idf){
    	$this->idf = $idf;
    }

    public function get_items_matrix(){
    	return $this->items_matrix;
    }

    public function set_items_matrix($itemsmatrix){
    	$this->items_matrix = $itemsmatrix;
    }

	public function get_user_weights(){
		return $this->userweights;
	}

	public function set_user_weights($userweights){
		$this->userweights = $userweights;
	}

	public function is_ready(){
		if(!isset($this->logbase)){
			$this->logbase = 10;
		}
		return true;
	}

	protected function build(){
		$numdocuments = count($this->items->get_items());
		$this->tf = $this->itemsmatrix = $this->userweights = [];

		for($i=0;$i<count(current($this->items->get_items())->get_vector());$i++){
			array_push($this->tf,0);
		}

		foreach($this->items->get_items() as $item){
			foreach ($item->get_vector() as $key => $value){
				$this->tf[$key] += $value;
			}

			array_push($this->itemsmatrix,block_recblock_scaling_unit_normalization($item->get_vector()));
		}

		foreach($this->tf as $key => $value){
			$this->idf[$key] = log($numdocuments/(1+$this->tf[$key]), $this->logbase);
			$this->userweights[$key] = block_recblock_dot_product($this->usermodel->get_user_profile(),array_column($this->itemsmatrix,$key));
		}
	}

	protected function create_recommendation_value($key){

		$itemweight = [];
		for($i=0;$i<count(current($this->items->get_items())->get_vector());$i++){
			array_push($itemweight,1);
		}

		for($i=0; $i<count($itemweight);$i++){
			$itemweight[$i]= block_recblock_dot_product($this->itemsmatrix[$key],$this->idf);
		}

		$this->recommendationvalues[$key] = block_recblock_dot_product($itemweight, $this->userweights);
	}

	protected function sort_recommendation_values(){
		arsort($this->recommendationvalues);
	}
}
