<?php
class test{
	
	protected $usermodels;
	
	protected $recalls;
	protected $precisions;
	protected $fscores;
	
	
	public function __construct($usermodels,$recalls,$precisions,$fscores){
		$this->usermodels = $usermodels;
		$this->recalls = $recalls;
		$this->precisions = $precisions;
		$this->fscores = $fscores;
	}
	
	public function get_user_models(){
		return $this->usermodels;
	}
	
	public function get_recalls(){
		return $this->recalls;	
	}

	public function get_precisions(){
		return $this->precisions;
	}
	
	public function get_fscores(){
		return $this->fscores;
	}
	
	public function set_user_models($usermodels){
		$this->usermodels = $usermodels;
	}
	
	public function set_recalls($recalls){
		$this->recalls = $recalls;
	}
	
	public function set_precisions($precisions){
		$this->precisions=$precisions;
	}
	
	public function set_fscores($fscores){
		$this->fscores = $fscores;
	}
	
}

