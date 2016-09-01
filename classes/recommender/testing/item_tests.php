<?php

require_once 'test.php';

class items_tests{
	protected $recitems;
	protected $tests;
	
	public function __construct(recommender_items $recitems, array $tests=[]){
		$this->recitems = $recitems;
		$this->tests = $tests;
	}
	
	public function get_rec_items(){
		return $this->recitems;
	}
	
	public function get_tests(){
		return $this->tests;
	}
		
	public function print_items_csv(){
		echo nl2br("ITEM DATASET: \n");
		foreach($this->recitems->get_items() as $item){
			
			$str = $item->get_name();
			foreach($item->get_vector() as $vector){
				$str .= ";".$vector; 
			}
			echo nl2br($str."\n");
		}
	}
	
	public function get_mean_recalls(){
		
		$meanrecalls = $this->tests[0]->get_recalls();
		for($i=1;$i<count($this->tests);$i++){
			for($j=0;$j<count($this->tests[$i]->get_recalls());$j++){
				$meanrecalls[$j] +=  $this->tests[$i]->get_recalls()[$j];
			}
		}
		
		foreach($meanrecalls as &$meanrecall){
			$meanrecall = $meanrecall/count($this->tests);
		}
		return $meanrecalls;
	}

	public function get_mean_precisions(){
	
		$meanprecisions = $this->tests[0]->get_precisions();
		for($i=1;$i<count($this->tests);$i++){
			for($j=0;$j<count($this->tests[$i]->get_precisions());$j++){
				$meanprecisions[$j] +=  $this->tests[$i]->get_precisions()[$j];
			}
		}
	
		foreach($meanprecisions as &$meanprecision){
			$meanprecision = $meanprecision/count($this->tests);
		}
		return $meanprecisions;
	}

	public function get_mean_fscores(){
	
		$meanfscores = $this->tests[0]->get_fscores();
		for($i=1;$i<count($this->tests);$i++){
			for($j=0;$j<count($this->tests[$i]->get_fscores());$j++){
				$meanfscores[$j] +=  $this->tests[$i]->get_fscores()[$j];
			}
		}
	
		foreach($meanfscores as &$meanfscore){
			$meanfscore = $meanfscore/count($this->tests);
		}
		return $meanfscores;
	}
	
	public function print_tests_csv(){
		echo nl2br("USER PROFILES: \n");
		echo count($this->tests)."</br>";
		foreach($this->tests as $test){
			foreach($test->get_user_models() as $usermodel){
				$userprofile = $usermodel->get_user_profile();
				$str = $userprofile[0];
				for($i=1;$i<count($userprofile);$i++){
					$str .= ";".$userprofile[$i];
				}
				echo nl2br($str."\n");
			}
			echo nl2br("RECALLS:\n");
			$str = $test->get_recalls()[0];
			for($i=1;$i<count($test->get_recalls());$i++){
				$str .= ";".$test->get_recalls()[$i];
			}
			echo nl2br($str."\n");
			echo nl2br("PRECISIONS:\n");
			$str = $test->get_precisions()[0];
			for($i=1;$i<count($test->get_precisions());$i++){
				$str .= ";".$test->get_precisions()[$i];
			}
			echo nl2br($str."\n");
			echo nl2br("FSCORES:\n");
			$str = $test->get_fscores()[0];
			for($i=1;$i<count($test->get_fscores());$i++){
				$str .= ";".$test->get_fscores()[$i];
			}
			echo nl2br($str."\n");
			echo "</br>";
		}
	}
	
	public function print_all(){
		$this->print_items_csv();
		$this->print_tests_csv();
	}
}

