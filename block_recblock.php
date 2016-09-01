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
 * Recblock block caps.
 *
 * @package    block_recblock
 * @copyright  Boris Ballester Hernandez <borbalher@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/blocks/recblock/classes/data/items/vark_items_moodle.php');
require_once($CFG->dirroot . '/blocks/recblock/classes/data/items/bartle_items_moodle.php');
require_once($CFG->dirroot . '/blocks/recblock/classes/recommender/items/recommender_items.php');
require_once($CFG->dirroot . '/blocks/recblock/classes/data/usermodels/like_moodle_profile.php');
require_once($CFG->dirroot . '/blocks/recblock/classes/recommender/usermodels/usage_user_model.php');
require_once($CFG->dirroot . '/blocks/recblock/classes/recommender/algorithms/tf_idf_recommender.php');
require_once($CFG->dirroot . '/blocks/recblock/classes/recommender/algorithms/simple_weight_recommender.php');
require_once($CFG->dirroot . '/blocks/recblock/classes/recommender/algorithms/item_based_recommender.php');



class block_recblock extends block_base {

  function init() {
    $this->title = get_string('pluginname', 'block_recblock');
  }

  function get_content() {
    global $COURSE, $CFG, $USER;

    if ($this->content !== null) {
      return $this->content;
    }

    $content = "";
    
    $roles = get_user_roles(context_course::instance($COURSE->id),  $USER->id, false);
    $role = $roles[key($roles)]->roleid; //Añadimos el parametro roleid que nos indica el rol del usuario dentro del curso

    if($role==5 ||$role==6){
		
	    $varkdataitems = new vark_items_moodle();
		$bartledataitems = new bartle_items_moodle();
	    $vark_items = new recommender_items($varkdataitems);
		$bartle_items = new recommender_items($bartledataitems);
		
		$varkdataprofile = new like_moodle_profile($vark_items,0,-1);		
	    $varkusermodel = new usage_user_model($varkdataprofile);
		
		$bartledataprofile = new like_moodle_profile($bartle_items,0,-1);		
	    $bartleusermodel = new usage_user_model($bartledataprofile);
		
	    $rectype = get_config('block_recblock','recommender_type');
	    $mode = get_config('block_recblock','recommendation_mode');
		
	    $rec = NULL;
	    switch($rectype){
	    	case "t":
	    		$rec = new tf_idf_recommender();			
	    		break;
	    	case "w":
	    		$rec = new simple_weight_recommender();
	    		break;	    	
	    	case "i":
	    		$rec = new item_based_recommender();
	    		$rec->set_threshold(0.6);
	    		$rec->set_threshold_operator(">=");
	    		$rec->set_similarity_function('block_recblock_cosine_similarity');
	    		break;
	    		
	    }
		
	    $rec->set_mode($mode);
	
		$rec->set_recommender_items($vark_items);
		$rec->set_recommender_user_model($varkusermodel);
		$rec->execute();
	
		$varkrecommendation = $rec->recommend();

		$rec->set_recommender_items($bartle_items);
		$rec->set_recommender_user_model($bartleusermodel);
		$rec->execute();
		$bartlerecommendation = $rec->recommend();
		
	    $content = "<h5>".get_string('personal_recommendations', 'block_recblock')."</h5>";
		$content.= "<a href=\"".$CFG->dirroot."/".$bartlerecommendation->get_url()->out_as_local_url()."\">".$bartlerecommendation->get_name()."</a>";
		$content.= "</br>";
		$content.= "<a href=\"".$CFG->dirroot."/".$varkrecommendation->get_url()->out_as_local_url()."\">".$varkrecommendation->get_name()."</a>";
	
   }

    $this->content         =  new stdClass;
    $this->content->text   .= $content;
    $this->content->footer = '';

    return $this->content;
  }

  public function applicable_formats() {
    return array('all' => false,
                 'site' => false,
                 'site-index' => false,
                 'course-view' => true,
                 'course-view-social' => false,
                 'mod' => false,
                 'mod-quiz' => false);
  }

  public function instance_allow_multiple() {
    return false;
  }

  function has_config() {
    return true;
  }

}
