<?php
require_once '../../../lib.php';
require_once '../algorithms/item_based_recommender.php';
require_once '../algorithms/binary_tf_idf_recommender.php';
require_once '../algorithms/simple_weight_recommender.php';
require_once '../testing/recommender_test.php';
require_once '../../data/items/simulated_binary_items.php';
require_once '../../data/usermodels/simulated_like_user_profile.php';
require_once '../usermodels/like_user_model.php';
require_once '../testing/batch_tests.php';

set_time_limit(300);


$maxactions  = 100;
$minactions  = 0;

$numitems  = 10;
$numusers = 100;

$numitemsdataset = 10;
$numusersdataset = 20;

$likevalue = 0;
$dislikevalue =-1.5;

$recommenders = [];

$varkibrec = new item_based_recommender();
$varkibrec->set_threshold(0.6);
$varkibrec->set_threshold_operator(">=");
$varkibrec->set_similarity_function('block_recblock_cosine_similarity');

$varktfrec = new binary_tf_idf_recommender();

$varkswrec = new simple_weight_recommender();

$recommendertest = new recommender_test();

array_push($recommenders,$varkibrec,$varkswrec,$varktfrec);

$itemsdataset = [];
for($i=0;$i<$numitemsdataset;$i++){
	$itemgenerator = new simulated_binary_items($numitems,4);
	$items = new recommender_items($itemgenerator);
	array_push($itemsdataset,$items);
}


$usersdataset = [];
for($i=0;$i<$numusersdataset;$i++){
	$usermodels = [];
	for($j=0;$j<$numusers;$j++){
		$profilegenerator = new simulated_like_user_profile($numitems,$minactions,$maxactions,$likevalue,$dislikevalue);
		$usermodel = new like_user_model($profilegenerator);
		array_push($usermodels, $usermodel);
	}
	array_push($usersdataset, $usermodels);
}

$vbt = new batch_tests($recommenders, $recommendertest, $itemsdataset, $usersdataset);
$vbt->execute();

foreach($vbt->get_batch_tests() as $test){
	echo "MEAN RECALLS: </br>";
	for($i=0;$i<count($test->get_mean_recalls());$i++){
		echo $i.";".$test->get_mean_recalls()[$i]."</br>";
	}
	echo "MEAN PRECISIONS: </br>";
	for($i=0;$i<count($test->get_mean_precisions());$i++){
		echo $i.";".$test->get_mean_precisions()[$i]."</br>";
	}

	echo "MEAN FSCORES: </br>";
	for($i=0;$i<count($test->get_mean_fscores());$i++){
		echo $i.";".$test->get_mean_fscores()[$i]."</br>";
	}

	echo "</br>";
	$test->print_all();
	echo "</br>";

}

