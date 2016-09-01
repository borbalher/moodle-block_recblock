<?php

require_once 'idatauserprofile.php';
require_once '../../../lib.php';

class simulated_like_user_profile implements idatauserprofile{

	protected $total;
	protected $minactions;
	protected $maxactions;
	protected $likevalue;
	protected $dislikevalue;

	public function __construct($total, $minactions, $maxactions, $likevalue, $dislikevalue){
		$this->total = $total;
		$this->minactions = $minactions;
		$this->maxactions = $maxactions;
		$this->likevalue = $likevalue;
		$this->dislikevalue = $dislikevalue;
	}


    public function get_data_user_profile(){
    	$numactions = [];
    	$profile = [];

    	for($i=0;$i<$this->total;$i++){
    		$randomnumactions = rand($this->minactions,$this->maxactions);
    		array_push($numactions,$randomnumactions);
    	}

    	$normactions = block_recblock_standard_normalization($numactions);

    	foreach($normactions as $normaction){
    		if($normaction >= $this->likevalue){
    			array_push($profile,1);
    		}else if($normaction <= $this->dislikevalue){
    			array_push($profile,-1);
    		}else{
    			array_push($profile,0);
    		}
    	}

    	return $profile;
    }
}
