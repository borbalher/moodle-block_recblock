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
 * Recblock block usage_user_model.php file
 *
 * @package    block_recblock
 * @copyright  Boris Ballester Hernandez <borbalher@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once $CFG->dirroot.'/blocks/recblock/classes/recommender/usermodels/recommender_user_model.php';

class usage_user_model extends recommender_user_model{

	public function has_preference($itemkey){
		$preference = false;
		if($this->profile[$itemkey]!=0){
			$preference = true;
		}
		return $preference;
	}

	public function no_preference_value(){
		return 0;
	}

	public function is_relevant($expectedvalue){
		if($expectedvalue==1){
			return true;
		}else{
			return false;
		}
	}

	public function like($itemkey){
		$like = false;
		if($this->profile[$itemkey]==1){
			$like = true;
		}
		return $like;
	}

}
