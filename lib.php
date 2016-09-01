<?php
///////////////////////////SETTINGS_FUNCTIONS////////////////////////////////
/**
 * Returns an array with all the implemented recommender algorithms
 *
 * @return array recommender algorithms
 */
function block_recblock_get_recommenders(){
	return array('t' => get_string('tf_idf', 'block_recblock'),
				 'i' => get_string('item_based', 'block_recblock'),
				 'w' => get_string('simple_weight', 'block_recblock'));
}

/**
 * Returns the default recommender algorithm
 *
 * @return string recommender algorithm
 */
function block_recblock_get_default_recommender(){
	return 't';
}

/**
 * Returns the default mode for recommendations
 *
 * @return string recommendation mode
 */
function block_recblock_get_recommenders_default_mode(){
	return 'r';
}

/**
 * Returns an array with the modules types used by bartle recommender
 *
 * @return array module types
 */
function block_recblock_get_bartle_modules_type(){
	return array(2,4,5,6,9,10,13,16,21,22);
}

/**
 * Returns an array with the modules types used by vark recommender
 *
 * @return array module types
 */
function block_recblock_get_vark_modules_type(){
	return array(3,4,6,8,9,10,11,13,14,15,16,17,18,20,21);
}

/**
 * Returns the default item creation mode for vark recommender
 *
 * @return string vark mode
 */
function block_recblock_get_vark_default_item_creation_mode(){
	return 'm';
}
/**
 * Returns the default item creation mode for bartle recommender
 *
 * @return string vark mode
 */
function block_recblock_get_bartle_default_item_creation_mode(){
	return 'm';
}


/**
 * Returns vark items default types table name
 *
 * @return string table name
 */
function block_recblock_get_bartle_table_name(){
	return "block_recblock_dtb";
}

/**
 * Returns log table name of preferred reader, if leagcy then return empty string.
 *
 * @return string table name
 */
function block_recblock_get_log_table_name() {
    // Get prefered sql_internal_table_reader reader (if enabled).
    $logmanager = get_log_manager();
    $readers = $logmanager->get_readers();
    $logtable = '';

    // Get preferred reader.
    if (!empty($readers)) {
        foreach ($readers as $readerpluginname => $reader) {
            // If legacy reader is preferred reader.
            if ($readerpluginname == 'logstore_legacy') {
                break;
            }

            // If sql_internal_table_reader is preferred reader.
            if ($reader instanceof \core\log\sql_internal_table_reader) {
                $logtable = $reader->get_internal_log_table_name();
                break;
            }
        }
    }
    return $logtable;
}

/**
 * Returns crud sql and params.
 *
 * @param string $action action to be filtered.
 * @return array crudsql and crudparams.
 */
function block_recblock_get_crud_sql($action) {
    global $DB;

    switch ($action) {
        case 'view':
            $crud = 'r';
            break;
        case 'post':
            $crud = array('c', 'u', 'd');
            break;
        default:
            $crud = array('c', 'r', 'u', 'd');
    }

    list($crudsql, $crudparams) = $DB->get_in_or_equal($crud, SQL_PARAMS_NAMED, 'crud');
    $crudsql = " AND crud " . $crudsql;
    return array($crudsql, $crudparams);
}
///////////////////////////RECOMMENDER_FUNCTIONS////////////////////////////////
/**
 * Returns the boolean value of the specified expression
 *
 * @param  string|int|float|double $var1 first var
 * @param  string $operator operator
 * @param  string|int|float|double $var2 second var
 * @return bool the value of the expression $var1 $operator $var2
 */
function recblock_evaluate ($var1, $operator, $var2){
	$evaluation = NULL;
	switch ($operator){
		case ">":
			$evaluation = $var1 > $var2;
			break;
		case "<":
			$evaluation = $var1 < $var2;
			break;
		case ">=":
			$evaluation = $var1 >= $var2;
			break;
		case "<=":
			$evaluation = $var1 <= $var2;
			break;
		case "==":
			$evaluation = $var1 == $var2;
			break;
		case "!=":
			$evaluation = $var1 != $var2;
			break;
	}
	return $evaluation;
}

//////////VECTORS
/**
 * Returns a boolean value indicating if the vector contains only positive numbers
 *
 * @param  array $vector vector;
 * @return bool true if the vector contains only positives
 */
function block_recblock_vector_contains_only_positive($vector){
	$contains = true;
	for($i=0;$i<count($vector);$i++){
		if($vector[$i]<0){
			$contains = false;
		}
	}
	return $contains;
}

/**
 * Returns the dot product of two vectors
 *
 * @param  array $v1 vector;
 * @param  array $v2 vector;
 * @return int|double|float dot product
 */
function block_recblock_dot_product($v1,$v2){

	if(block_recblock_same_size_vectors($v1,$v2)){
		$product = 0;
		for($i=0;$i<count($v1);$i++){
			$product += ($v1[$i] * $v2[$i]);
		}
		return $product;
	}else{
		return NULL;
	}
}

/**
 * Returns the length of a vector
 *
 * @param  array $vector vector;
 * @return int|double|float length of the vector
 */
function block_recblock_get_vector_length($vector){
	$length = 0;
	for($i=0;$i<count($vector);$i++){
		$length += pow($vector[$i],2);
	}

	return sqrt($length);
}

/**
 * Returns the max and min values of a vector
 *
 * @param  array $vector vector;
 * @return array max and min values
 */
function block_recblock_get_vector_max_min($vector){
	$max = 0;
	$min = PHP_INT_MAX;

	for($i=0;$i<count($vector);$i++){
		if($vector[$i]<$min){
			$min = $vector[$i];
		}
		if($vector[$i]>$max){
			$max = $vector[$i];
		}
	}

	return array("max"=>$max,"min"=>$min);
}

/**
 * Returns the mean of the vector values
 *
 * @param  array $vector vector;
 * @return int|double|float mean
 */
function block_recblock_get_vector_mean($vector){
	$mean = 0;

	for($i=0;$i<count($vector);$i++){
		$mean += $vector[$i];
	}

	return $mean / count($vector);
}

/**
 * Returns the standard deviation of the vector values
 *
 * @param  array $vector vector;
 * @return int|double|float stdev
 */
function block_recblock_get_vector_standard_deviation($vector){
	$var = 0;
	$mean = block_recblock_get_vector_mean($vector);
	for($i=0;$i<count($vector);$i++){
		$var += pow($vector[$i]-$mean,2);
	}

	return sqrt($var / (count($vector)-1));
}

/**
 * Returns the sum of the vector values
 *
 * @param  array $vector vector;
 * @return int|double|float sum
 */
function block_recblock_get_vector_sum($vector){
	$sum = 0;
	for($i=0;$i<count($vector);$i++){
		$sum += $vector[$i];
	}
	return $sum;
}

/**
 * Returns a boolean value indicating if the vectors sizes are equal
 *
 * @param  array $v1 vector;
 * @param  array $v2 vector;
 * @return bool true if the vector contains only positives
 */
function block_recblock_same_size_vectors($v1,$v2){
	if(count($v1)==count($v2)){
		return true;
	}else{
		return false;
	}
}

////////NORMALIZATION
/**
 * Returns a vector normalized using feature scaling normalization
 *
 * @param  array $vector vector;
 * @return array normalized vector
 */
function block_recblock_feature_scaling_normalization($vector){
	$vectornorm = [];
	$maxmin = block_recblock_get_vector_max_min($vector);

	for($i=0;$i<count($vector);$i++){
		$z = ($vector[$i] - $maxmin["min"]) / ($maxmin["max"]-$maxmin["min"]);
		array_push($vectornorm,$z);
	}

	return $vectornorm;
}

/**
 * Returns a vector normalized using max scaling normalization
 *
 * @param  array $vector vector;
 * @return array normalized vector
 */
function block_recblock_max_normalization($vector){
	$vectornorm = [];
	$max = block_recblock_get_vector_max_min($vector)["max"];

	for($i=0;$i<count($vector);$i++){
		$z = $vector[$i] / $max;
		array_push($vectornorm,$z);
	}

	return $vectornorm;
}

/**
 * Returns a vector normalized using rescaling normalization
 *
 * @param  array $vector vector;
 * @return array normalized vector
 */
function block_recblock_rescaling_normalization($vector){
	$vectornorm = [];
	$mean = block_recblock_get_vector_mean($vector);
	$maxmin = block_recblock_get_vector_max_min($vector);

	for($i=0;$i<count($vector);$i++){
		$z = ($vector[$i] - $mean) / ($maxmin["max"]-$maxmin["min"]);
		array_push($vectornorm,$z);
	}

	return $vectornorm;
}

/**
 * Returns a vector normalized using scaling unit normalization
 *
 * @param  array $vector vector;
 * @return array normalized vector
 */
function block_recblock_scaling_unit_normalization($vector){
	$vectornorm = [];
	$vlength = block_recblock_get_vector_length($vector);

	for($i=0;$i<count($vector);$i++){
		$z = $vector[$i]  / $vlength;
		array_push($vectornorm,$z);
	}

	return $vectornorm;
}

/**
 * Returns a vector normalized using standard normalization
 *
 * @param  array $vector vector;
 * @return array normalized vector
 */
function block_recblock_standard_normalization($vector){

	$vectornorm = [];
	$mean = block_recblock_get_vector_mean($vector);
	$dev = block_recblock_get_vector_standard_deviation($vector,$mean);

	for($i=0;$i<count($vector);$i++){
		$z = ($vector[$i] - $mean) / $dev;
		array_push($vectornorm,$z);
	}

	return $vectornorm;
}

/**
 * Returns a vector normalized using sum normalization
 *
 * @param  array $vector vector;
 * @return array normalized vector
 */
function block_recblock_sum_normalization($vector){
	$vectornorm = [];
	$sum = block_recblock_get_vector_sum($vector);

	for($i=0;$i<count($vector);$i++){
		$z = $vector[$i] / $sum;
		array_push($vectornorm,$z);
	}

	return $vectornorm;
}

////////DISTANCE FUNCTIONS
//[SIMILARITY/DISSIMILARITY] RANGE VALUE

//DISSIMILARITY [0,1] 0==SIMILAR
/**
 * Returns the Bray Curtis distance between two vectors
 *
 * @param  array $vector vector;
 * @return array normalized vector
 */
function block_recblock_bray_curtis_distance($v1,$v2){
	$distance = 0;
	$num = 0;
	$denom = 0;

	for($i=0;$i<count($v1);$i++){
		$num += abs($v1[$i]-$v2[$i]);
		$denom += (abs($v1[$i])+abs($v2[$i]));
	}

	return ($num /$denom);
}

//DISSIMILARITY [0,N] 0==SIMILAR
function block_recblock_canberra_distance($v1,$v2){
	$distance = 0;
	for($i=0;$i<count($v1);$i++){
		$num = abs($v1[$i]-$v2[$i]);
		$denom = (abs($v1[$i])+abs($v2[$i]));
		if($denom!=0){
			$distance += ($num /$denom);
		}
	}
	return $distance;
}

//[DISSIMILARITY] [0,N] 0==SIMILARITY
function block_recblock_chebyshev_distance($v1,$v2){
	$distance = 0;
	for($i=0;$i<count($v1);$i++){
		$value = abs($v1[$i] - $v2[$i]);
		if($value>$distance){
			$distance = $value;
		}
	}
	return $distance;
}

//[DISSIMILARITY] [0,N] 0==SIMILARITY
function block_recblock_sqeuclidean_distance($v1,$v2){
	$distance = 0;
	for($i=0;$i<count($v1);$i++){
		$difcoordinates = pow($v1[$i] - $v2[$i],2);
		$distance += $difcoordinates;
	}
	return $distance;
}

//[DISSIMILARITY] [0,N] 0==SIMILARITY
function block_recblock_euclidean_distance($v1,$v2){
	return sqrt(block_recblock_sqeuclidean_distance($v1,$v2));
}

//[DISSIMILARITY] [0,N] 0==SIMILARITY
function block_recblock_weighted_euclidean_distance($v1,$v2,$w){
	$distance = 0;
	for($i=0;$i<count($v1);$i++){
		$difcoordinates = $w[$i] * pow($v1[$i] - $v2[$i],2);
		$distance += $difcoordinates;
	}

	return sqrt($distance);
}

//[DISSIMILARITY] [0,N] 0==SIMILAR
function block_recblock_taxicab_distance($v1,$v2){
	$distance = 0;
	for($i=0;$i<count($v1);$i++){
		$distance += abs($v1[$i] - $v2[$i]);
	}
	return $distance;
}

/////////SIMILARITY/DISSIMILARITY FUNCTIONS
//[SIMILARITY/DISSIMILARITY] RANGE VALUE

//SIMILARITY [0,1] 1==SIMILAR
function block_recblock_cosine_similarity($v1,$v2){
	return block_recblock_dot_product($v1,$v2)/(block_recblock_get_vector_length($v1)*block_recblock_get_vector_length($v2));
}

//SIMILARITY [0,1] 1==SIMILAR
function block_recblock_dice_coefficient($v1,$v2){
	return (2*block_recblock_dot_product($v1,$v2))/(pow(block_recblock_get_vector_length($v1),2)+pow(block_recblock_get_vector_length($v2),2));
}

//SIMILARITY [0,1] 1==SIMILAR
function block_recblock_pearson_coefficient($v1,$v2){
	$size = count($v1);
	$sumv1 = 0;//4
	$sumv2 = 0;//1
	$squaresumv1 = 0;//4
	$squaresumv2 = 0;//1
	$product = 0;//1

	for($i=0;$i<count($v1);$i++){
		$sumv1 += $v1[$i];
		$sumv2 += $v2[$i];
		$squaresumv1 += pow($v1[$i],2);
		$squaresumv2 += pow($v2[$i],2);
		$product += ($v1[$i]*$v2[$i]);
	}

	$numerator = $size * $product - ($sumv1*$sumv2);
	$denominator = sqrt($size*$squaresumv1-pow($sumv1,2))*sqrt($size*$squaresumv2-pow($sumv2,2));

	$coefficient = 0 ;
	if($denominator!=0){
		$coefficient = $numerator/$denominator;
	}
	return $coefficient;
}


///////BINARY SIMILARITY
//DISSIMILARITY [0,N] 0==SIMILAR
function block_recblock_hamming_distance($v1,$v2){
	$distance = 0;
	for($i=0;$i<count($v1);$i++){
		if($v1[$i]!=$v2[$i]){
			$distance++;
		}
	}
	return $distance;
}

//SIMILARITY [0,1] 1==SIMILAR
function block_recblock_jaccard_similarity($v1,$v2){
	$f01 = 0; //v1[i] = 0  v[i] = 1
	$f10 = 0; //v1[i] = 1  v[i] = 0
	$f11 = 0; //v1[i] = 1  v[i] = 1

	for($i=0;$i<count($v1);$i++){
		if($v1[$i]==1 && $v2[$i]==1){
			$f11++;
		}else if($v1[$i]==1 && $v2[$i]==0){
			$f10++;
		}else if($v1[$i]==0 && $v2[$i]==1){
			$f01++;
		}
	}
	return $f11/($f01+$f10+$f11);
}

//DISSIMILARITY [0,N] 0==SIMILAR
function block_recblock_rogers_tanimoto_similarity($v1,$v2){
	$f01 = 0; //v1[i] = 0  v[i] = 1
	$f10 = 0; //v1[i] = 1  v[i] = 0
	$f11 = 0; //v1[i] = 1  v[i] = 1
	$f00 = 0; //v1[i] = 0  v[i] = 0

	for($i=0;$i<count($v1);$i++){
		if($v1[$i]==1 && $v2[$i]==1){
			$f11++;
		}else if($v1[$i]==1 && $v2[$i]==0){
			$f10++;
		}else if($v1[$i]==0 && $v2[$i]==1){
			$f01++;
		}else{
			$f00++;
		}
	}

	return (2*($f10+$f01))/($f11+2*($f10+$f01)+$f00);
}

//DISSIMILARITY [0,N] 0==SIMILAR
function block_recblock_russell_rao_similarity($v1,$v2){
	$f01 = 0; //v1[i] = 0  v[i] = 1
	$f10 = 0; //v1[i] = 1  v[i] = 0
	$f00 = 0; //v1[i] = 0  v[i] = 0

	for($i=0;$i<count($v1);$i++){
		if($v1[$i]==0 && $v2[$i]==1){
			$f01++;
		}else if($v1[$i]==1 && $v2[$i]==0){
			$f10++;
		}else if($v1[$i]==0 && $v2[$i]==0){
			$f00++;
		}
	}

	return ($f10+$f01+$f00)/count($v1);
}

//SIMILARITY [0,1] 1==SIMILAR
function block_recblock_smc_similarity($v1,$v2){
	$matches = 0;
	for($i=0;$i<count($v1);$i++){
		if($v1[$i]==$v2[$i]){
			$matches++;
		}
	}
	return ($matches/count($v1));
}

//DISSIMILARITY [0,N] 0==SIMILAR
function block_recblock_sokal_sneath_similarity($v1,$v2){
	$f01 = 0; //v1[i] = 0  v[i] = 1
	$f10 = 0; //v1[i] = 1  v[i] = 0
	$f11 = 0; //v1[i] = 1  v[i] = 1

	for($i=0;$i<count($v1);$i++){
		if($v1[$i]==1 && $v2[$i]==1){
			$f11++;
		}else if($v1[$i]==1 && $v2[$i]==0){
			$f10++;
		}else if($v1[$i]==0 && $v2[$i]==1){
			$f01++;
		}
	}

	return (2*($f10+$f01))/($f11+2*($f10+$f01));
}

//SIMILARITY [-1,1] 1==SIMILAR
function block_recblock_yule_q_similarity($v1,$v2){
	$f01 = 0; //v1[i] = 0  v[i] = 1
	$f10 = 0; //v1[i] = 1  v[i] = 0
	$f11 = 0; //v1[i] = 1  v[i] = 1
	$f00 = 0; //v1[i] = 0  v[i] = 0

	for($i=0;$i<count($v1);$i++){
		if($v1[$i]==1 && $v2[$i]==1){
			$f11++;
		}else if($v1[$i]==1 && $v2[$i]==0){
			$f10++;
		}else if($v1[$i]==0 && $v2[$i]==1){
			$f01++;
		}else{
			$f00++;
		}
	}

	if($f11+$f00==0){
		$coefficient = -1;
	}else if($f11*$f00 - $f10*$f01==0){
		$coefficient = 0;
	}else if($f10+$f01==0){
		$coefficient = 1;
	}else{
		$numerator = $f11*$f00 - $f10*$f01;
		$denominator = $f11*$f00 + $f10*$f01;
		$coefficient = $numerator/$denominator;
	}
	return $coefficient;
}
