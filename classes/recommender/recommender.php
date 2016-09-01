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
 * Recblock block recommender.php file
 *
 * @package    block_recblock
 * @copyright  Boris Ballester Hernandez <borbalher@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once 'items/recommender_items.php';
require_once 'usermodels/recommender_user_model.php';

abstract class recommender{

	protected $usermodel;
	protected $items;

	protected $recommendationvalues;

	protected $mode;
	protected $top;

	protected $newitemoperator;
	protected $newitemvalue;

	public function __construct(recommender_user_model $usermodel=NULL,recommender_items $items=NULL, $mode="r", $top=4){
		$this->items = $items;
		$this->usermodel = $usermodel;
		$this->mode = $mode;
		$this->recommendationvalues = [];
		$this->top = $top;
	}

	public function reset_recommendation_values(){
		$this->recommendationvalues = [];
	}

	public function get_recommender_user_model(){
		return $this->usermodel;
	}

	public function set_recommender_user_model(recommender_user_model $usermodel){
		$this->usermodel = $usermodel;
	}

	public function get_recommender_items(){
		return $this->items;
	}

	public function set_recommender_items(recommender_items $items){
		$this->items = $items;
	}

	public function get_mode(){
		return $this->mode;
	}

	public function set_mode($mode){
		$this->mode = $mode;
	}

	public function set_top($top){
		$this->top = $top;
	}

	public function get_top(){
		return  $this->top;
	}

	public function get_recommendation_values(){
		return $this->recommendationvalues;
	}

	public function set_recommendation_values($recommendationvalues){
		$this->recommendationvalues = $recommendationvalues;
	}

	public function execute(){
		if($this->items != NULL && $this->usermodel!=NULL && $this->is_ready()){
			$this->build();
			$this->create_recommendation_values();
			$this->sort_recommendation_values();
		}
	}

	abstract public function is_ready();

	protected function create_recommendation_values(){
		$this->reset_recommendation_values();
		foreach($this->items->get_items() as $key => $item){
			if($this->usermodel->has_preference($key)==false){
				$this->create_recommendation_value($key);
			}
		}
	}

	protected function get_best_recommendation(){
		return $this->items->get_items()[key($this->recommendationvalues)];
	}

	protected function get_random_top_recommendation(){
		$rand = rand(0,(count($this->recommendationvalues)/$top)+1);

		for($i=0;$i<$rand;$i++){
			next($this->recommendationvalues);
		}

		return  $this->items->get_items()[key($this->recommendationvalues)];
	}

	public function recommend(){
		$recomendation = NULL;
		if(!empty($this->recommendationvalues)){

			switch($this->mode){
				case "b":
					$recomendation =  $this->get_best_recommendation();
					break;
				case "r":
					$recomendation =  $this->get_random_top_recommendation();
					break;
			}
		}
		return $recomendation;
	}

	abstract protected function build(); //Funcion para inicializar los atributos del recomendador
	abstract protected function create_recommendation_value($key);//Como se crea cada valor de recomendacion
	abstract protected function sort_recommendation_values();//Como hay que ordenar las recomendaciones? IMP! al ordenarse deben mantener la key original
}
