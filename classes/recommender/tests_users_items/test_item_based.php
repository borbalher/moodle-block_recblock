<?php
require_once '../../../lib.php';
require_once '../algorithms/item_based_recommender.php';
require_once '../algorithms/simple_weight_recommender.php';
require_once '../algorithms/binary_tf_idf_recommender.php';
require_once '../testing/recommender_test.php';
require_once '../../data/items/simulated_binary_items.php';
require_once '../../data/usermodels/simulated_like_user_profile.php';

require_once '../usermodels/like_user_model.php';


set_time_limit(300);


$maxactions  = 250;
$minactions  = 0;

$minitems  = 50;
$maxitems  = 10;

$maxusers  = 50;
$minusers  = 10;

$numitems = rand($minitems,$maxitems);
$dataitems = new simulated_binary_items($numitems, 4);
$items = new recommender_items($dataitems);

$likevalue = 0;
$dislikevalue =-1.5;

$numusers = rand($minusers,$maxusers);
$usermodels = [];
for($i=0;$i<$numusers;$i++){
	$usermodel = new like_user_model(new simulated_like_user_profile($numitems, $minactions, $maxactions, $likevalue, $dislikevalue));
	array_push($usermodels,$usermodel);
}


$ibrec = new item_based_recommender();
$ibrec->set_threshold(0.6);
$ibrec->set_threshold_operator(">=");
$ibrec->set_similarity_function('block_recblock_cosine_similarity');
$ibrec->set_recommender_items($items);

echo "numitems;numusers;likevalue;dislikevalue</br>";
echo $numitems.";".$numusers.";".$likevalue.";".$dislikevalue."</br>";

echo "recall;precision;fscore</br>";
$rectest = new recommender_test($ibrec);
$rectest->execute_tests_users($usermodels);
echo $rectest->get_recall().";".$rectest->get_precision().";".$rectest->get_fscore()."</br>";

$swrec = new simple_weight_recommender();
$swrec->set_recommender_items($items);
$rectest = new recommender_test($swrec);
$rectest->execute_tests_users($usermodels);
echo $rectest->get_recall().";".$rectest->get_precision().";".$rectest->get_fscore()."</br>";

$tf = new binary_tf_idf_recommender();
$tf->set_recommender_items($items);
$rectest = new recommender_test($tf);
$rectest->execute_tests_users($usermodels);
echo $rectest->get_recall().";".$rectest->get_precision().";".$rectest->get_fscore()."</br>";


/*
 * $numtests = 5;
for($i=0;$i<$numtests;$i++){
	$rectest->execute_tests_users($usermodels);
	
	$likevalue = rand(0.0,3.0);
	$dislikevalue = rand(0.0,-3.0);
	
	$auxusermodels = [];
	for($j=0;$j<$numusers;$j++){
		$usermodel = new like_user_model(new simulated_like_user_profile($numitems, $minactions, $maxactions, $likevalue, $dislikevalue));
		array_push($auxusermodels,$usermodel);
	}	
	$usermodels = $auxusermodels;
	echo $likevalue.";".$dislikevalue.";".$rectest->get_recall().";".$rectest->get_precision().";".$rectest->get_precision().";".$rectest->get_fscore()."</br>";
}*/





