<?php

class recommender_test{
	protected $recommender;
	protected $tn;
	protected $tp;
	protected $fn;
	protected $fp;
	
	public function __construct(recommender $rec = NULL){
		$this->recommender = $rec;
	}
	
	public function get_recall(){
		$recall = 1;
		if(($this->tp+$this->fn)!=0){
			$recall = $this->tp / ($this->tp+$this->fn);
		}
		
		return  $recall;
	}

	public function get_precision(){
		$precision = 0;
		if(($this->tp+$this->fp)!=0){
			$precision = $this->tp / ($this->tp+$this->fp);
		}
		
		return $precision;
	}
	
	public function get_fscore(){
		$fscore = 0;
		if(($this->get_recall() + $this->get_precision())!=0){
			$fscore = (2*$this->get_precision()*$this->get_recall())/ ($this->get_recall() + $this->get_precision());
		}
		
		return $fscore;
	}
	
	public function get_recommender(){
		return $this->recommender;
	}
	
	public function set_recommender(recommender $rec){
		$this->recommender = $rec;
	}
	

	private function reset_pn_values(){
		$this->tp = $this->tn = $this->fp = $this->fn = 0;
	}
	
	private function is_in_top_n($key, $array){
		$isintop = false;
		$counter = 0;
		$countertop = (count($this->recommender->get_recommendation_values())/$this->recommender->get_top())+1;
		while(!$isintop && $counter<$countertop){
			if(key($array)==$key){
				$isintop = true;
			}else{
				next($array);
				$counter++;
			}
		}

		return $isintop;
	}
	
	public function execute_tests_users($usermodels){
		
		if($this->recommender != NULL){
			$this->reset_pn_values();
			foreach($usermodels as $usermodel){
				$this->recommender->set_recommender_user_model($usermodel);
				$this->execute_tests_user();
			}		
		}
	}

	private function execute_tests_user(){
		foreach($this->recommender->get_recommender_items()->get_items() as $key => $value){
			if($this->recommender->get_recommender_user_model()->has_preference($key)){
				$expectedvalue = $this->recommender->get_recommender_user_model()->get_user_profile()[$key];
			
				$usermodel = $this->recommender->get_recommender_user_model();
				$userprofile = $usermodel->get_user_profile();
				$userprofile[$key] = $this->recommender->get_recommender_user_model()->no_preference_value();
				$usermodel->set_user_profile($userprofile);
				
				$this->recommender->set_recommender_user_model($usermodel);
				$this->recommender->execute();
			
				$recommendations = $this->recommender->get_recommendation_values();
				
				if($this->is_in_top_n($key, $recommendations)){
					if($this->recommender->get_recommender_user_model()->is_relevant($expectedvalue)){
						$this->tp++;
					}else{
						$this->fp++;
					}
				}else{
					if($this->recommender->get_recommender_user_model()->is_relevant($expectedvalue)){
						$this->fn++;
					}else{
						$this->tn++;
					}
				}
				
				$userprofile[$key] = $expectedvalue;
				$usermodel->set_user_profile($userprofile);
				$this->recommender->set_recommender_user_model($usermodel);	
			}
		}
	}
}

