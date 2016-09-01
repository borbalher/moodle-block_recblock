<?php

require_once('../lib.php');

class block_recblock_lib_tests extends \PHPUnit_Framework_TestCase{


	public function test_vector_contains_only_positives_vector(){
		$v = [1,2,3,4];
		$onlypositives = block_recblock_vector_contains_only_positive($v);
		$this->assertEquals(true, $onlypositives);
	}

	public function test_vector_not_contains_only_positives(){
		$v = [1,2,-3,4];
		$onlypositives = block_recblock_vector_contains_only_positive($v);
		$this->assertEquals(false, $onlypositives);
	}

	public function test_dot_product_same_size(){
		$v1 = [1,2,3,4];
		$v2 = [1,2,3,4];
		$expecteddotproduct = 30;
		$dotproduct = block_recblock_dot_product($v1,$v2);
		$this->assertEquals($expecteddotproduct, $dotproduct);
	}

	public function test_dot_product_different_size(){
		$v1 = [1,2,3,4];
		$v2 = [1,3,4];
		$expecteddotproduct = NULL;
		$dotproduct = block_recblock_dot_product($v1,$v2);
		$this->assertEquals($expecteddotproduct, $dotproduct);
	}

	public function test_get_vector_length(){
		$v = [1,1,1,1];
		$expectedlength = 2;
		$length = block_recblock_get_vector_length($v);
		$this->assertEquals($expectedlength, $length);
	}

	public function test_get_vector_max_min(){
		$v = [2,4,-1,20];
		$expectedarray = array("max"=>20,"min"=>-1);
		$maxminarray = block_recblock_get_vector_max_min($v);
		$this->assertEquals($expectedarray, $maxminarray);
	}

	public function test_get_vector_mean(){
		$v = [1,2,3,4];
		$expectedmean = 2.5;
		$mean = block_recblock_get_vector_mean($v);
		$this->assertEquals($expectedmean, $mean);
	}

	public function test_get_vector_stdev(){
		$v = [2,4,4,4];
		$expectedstdev = 1;
		$stdev = block_recblock_get_vector_standard_deviation($v);
		$this->assertEquals($expectedstdev, $stdev);
	}

	public function test_get_vector_sum(){
		$v = [1,1,1,1];
		$expectedsum = 4;
		$sum = block_recblock_get_vector_sum($v);
		$this->assertEquals($expectedsum, $sum);
	}

	public function test_same_size_vectors(){
		$v1 = [1,2,3,4];
		$v2 = [1,2,3,4];
		$size = block_recblock_same_size_vectors($v1,$v2);
		$this->assertEquals(true, $size);
	}

	public function test_different_size_vectors(){
		$v1 = [1,2,3,4];
		$v2 = [1,2,3];
		$size = block_recblock_same_size_vectors($v1,$v2);
		$this->assertEquals(false, $size);
	}

///////////////////////////////////////////////////////////////////////////////
	public function test_feature_normalization(){
		$v = [1,2,1,1];
		$expectedvnorm =[0,1,0,0];
		$vnorm =  block_recblock_feature_scaling_normalization($v);
		$this->assertEquals($expectedvnorm, $vnorm);
	}

	public function test_max_normalization(){
		$v = [1,2,1,1];
		$expectedvnorm =[0.5,1,0.5,0.5];
		$vnorm =  block_recblock_max_normalization($v);
		$this->assertEquals($expectedvnorm, $vnorm);
	}

	public function test_rescaling_normalization(){
		$v = [1,2,1,1];
		$expectedvnorm =[-0.25,0.75,-0.25,-0.25];
		$vnorm =  block_recblock_rescaling_normalization($v);
		$this->assertEquals($expectedvnorm, $vnorm);
	}

	public function test_scaling_unit_normalization(){
		$v = [1,1,1,1];
		$expectedvnorm =[0.5,0.5,0.5,0.5];
		$vnorm =  block_recblock_scaling_unit_normalization($v);
		$this->assertEquals($expectedvnorm, $vnorm);
	}

	public function test_standard_normalization(){
		$v = [1,2,1,1];
		$expectedvnorm =[-0.5,1.5,-0.5,-0.5];
		$vnorm =  block_recblock_standard_normalization($v);
		$this->assertEquals($expectedvnorm, $vnorm);
	}

	public function test_sum_normalization(){
		$v = [1,2,1,1];
		$expectedvnorm =[0.2,0.4,0.2,0.2];
		$vnorm =  block_recblock_sum_normalization($v);
		$this->assertEquals($expectedvnorm, $vnorm);
	}


///////////////////////////////////////////////////////////////////////////////
	public function test_bray_curtis_distance(){
		$v1 = [1,0,0,1];
		$v2 = [1,1,0,0];
		$expectedbraycurtis = 2;
		$braycurtis = block_recblock_canberra_distance($v1,$v2);
		$this->assertEquals($expectedbraycurtis, $braycurtis);
	}

	public function test_canberra_distance(){
		$v1 = [1,0,0,1];
		$v2 = [1,1,0,0];
		$expectedcanberra = 2;
		$canberra = block_recblock_canberra_distance($v1,$v2);
		$this->assertEquals($expectedcanberra, $canberra);
	}

	public function test_chebyshev_distance(){
		$v1 = [1,0,0,1];
		$v2 = [1,1,0,0];
		$expectedchebysev = 1;
		$chebysev = block_recblock_chebyshev_distance($v1,$v2);
		$this->assertEquals($expectedchebysev, $chebysev);
	}

	public function test_dice_distance(){
		$v1 = [1,0,0,1];
		$v2 = [1,1,0,0];
		$expecteddice = 0.5;
		$dice = block_recblock_dice_distance($v1,$v2);
		$this->assertEquals($expecteddice, $dice);
	}

	public function test_sqeuclidean_distance(){
		$v1 = [2,0,0,1];
		$v2 = [1,3,1,0];
		$expectedsqeuclidean = 12;
		$sqeuclidean = block_recblock_sqeuclidean_distance($v1,$v2);
		$this->assertEquals($expectedsqeuclidean, $sqeuclidean);
	}

	public function test_euclidean_distance(){
		$v1 = [2,0,0,1];
		$v2 = [1,3,1,0];
		$expectedeuclidean = 3.46;
		$euclidean = round(block_recblock_euclidean_distance($v1,$v2),2);
		$this->assertEquals($expectedeuclidean, $euclidean);
	}

	public function test_weighted_euclidean_distance(){
		$v1 = [2,0,0,1];
		$v2 = [1,3,1,0];
		$w = [0.5,0,1,0.5];
		$expectedweuclidean = 1.41;
		$weuclidean = round(block_recblock_weighted_euclidean_distance($v1,$v2,$w),2);
		$this->assertEquals($expectedweuclidean, $weuclidean);
	}

	public function test_taxicab_distance(){
		$v1 = [2,0,0,1];
		$v2 = [1,3,1,0];
		$expectedtaxicab = 6;
		$taxicab = block_recblock_taxicab_distance($v1,$v2);
		$this->assertEquals($expectedtaxicab, $taxicab);
	}

/////////SIMILARITY

	public function test_cosine_similarity(){
		$v1 = [1,0,0,1];
		$v2 = [1,1,0,0];
		$expectedcosine = 0.5;
		$cosine = round(block_recblock_cosine_similarity($v1,$v2),2);
		$this->assertEquals($expectedcosine, $cosine);
	}

	public function test_dice_coefficient_similarity(){
		$v1 = [1,0,0,1];
		$v2 = [1,1,0,0];
		$expecteddice = 0.5;
		$dice = block_recblock_dice_coefficient($v1,$v2);
		$this->assertEquals($expecteddice, $dice);
	}


	public function test_pearson_coefficient_similarity(){
		$v1 = [1.2,2,3.5,0.8];
		$v2 = [3.2,3.1,5,2.9];
		$expectedpearson = 0.93;
		$pearson = round(block_recblock_pearson_coefficient($v1,$v2),2);
		$this->assertEquals($expectedpearson, $pearson);
	}

///////BINARY SIMILARITY
	public function test_hamming_distance(){
		$v1 = [1,0,0,1];
		$v2 = [1,1,1,0];
		$expectedhamming = 3;
		$hamming = block_recblock_hamming_distance($v1,$v2);
		$this->assertEquals($expectedhamming, $hamming);
	}

	public function test_jaccard_similarity(){
		$v1 = [1,0,0,1];
		$v2 = [1,1,0,0];
		$expectedjaccard = 0.33;
		$jaccard = round(block_recblock_jaccard_similarity($v1,$v2),2);
		$this->assertEquals($expectedjaccard, $jaccard);
	}

	public function test_russell_rao_similarity(){
		$v1 = [1,0,0,1];
		$v2 = [1,1,0,0];
		$expectedjaccard = 0.75;
		$jaccard = block_recblock_russell_rao_similarity($v1,$v2);
		$this->assertEquals($expectedjaccard, $jaccard);
	}

	public function test_roger_tanimoto_similarity(){
		$v1 = [1,0,0,1];
		$v2 = [1,1,0,0];
		$expectedtanimoto = 0.67;
		$tanimoto = round(block_recblock_rogers_tanimoto_coefficient($v1,$v2),2);
		$this->assertEquals($expectedtanimoto, $tanimoto);
	}

	public function test_smc_similarity(){
		$v1 = [1,0,0,1];
		$v2 = [1,1,0,0];
		$expectedsmc = 0.5;
		$smc = block_recblock_smc_similarity($v1,$v2);
		$this->assertEquals($expectedsmc, $smc);
	}

	public function test_sokal_sneath_similarity(){
		$v1 = [1,0,0,1];
		$v2 = [1,1,0,0];
		$expectedsokalsneath = 0.8;
		$sokalsneath = block_recblock_sokal_sneath_similarity($v1,$v2);
		$this->assertEquals($expectedsokalsneath, $sokalsneath);
	}

	public function test_yule_similarity(){
		$v1 = [1,0,0,1];
		$v2 = [1,1,0,0];
		$expectedyule = 1;
		$yule = block_recblock_yule_similarity($v1,$v2);
		$this->assertEquals($expectedyule, $yule);
	}
}
