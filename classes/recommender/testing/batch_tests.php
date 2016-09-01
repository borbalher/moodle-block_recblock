<?php

require_once 'item_tests.php';

class batch_tests{


	protected $recomenders;
	protected $rectest;
	protected $recalls;
	protected $precisions;
	protected $fscores;
	protected $batchtests;
	
	protected $usersdataset;
	protected $itemsdataset;
	
	protected $recommendertest;

	public function get_batch_tests(){
		return $this->batchtests;
	}

	public function __construct($recomenders, recommender_test $recommendertest, $itemsdataset, $usersdataset){

		$this->recomenders = $recomenders;
		$this->recommendertest = $recommendertest;

		$this->itemsdataset = $itemsdataset;
		$this->usersdataset = $usersdataset;
		
		$this->batchtests = $this->recalls = $this->precisions = $this->fscores = [];
	}

	public function execute(){
		$this->batchtests = [];
		foreach($this->itemsdataset as $recitems){
			$tests = [];
			foreach($this->usersdataset as $recusers){
				$recalls = $precisions = $fscores = [];
				foreach ($this->recomenders as $rec){
					$rec->set_recommender_items($recitems);
					$this->recommendertest->set_recommender($rec);
					$this->recommendertest->execute_tests_users($recusers);
					array_push($recalls, $this->recommendertest->get_recall());
					array_push($precisions, $this->recommendertest->get_precision());
					array_push($fscores, $this->recommendertest->get_fscore());
				}
				array_push($tests, new test($recusers,$recalls, $precisions, $fscores));					
			}
			array_push($this->batchtests, new items_tests($recitems,$tests));
		}
	}
}
