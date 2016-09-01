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
 * Recblock block recommender_user_model.php file
 *
 * @package    block_recblock
 * @copyright  Boris Ballester Hernandez <borbalher@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

abstract class recommender_user_model{
	protected $profile;

	public function __construct(idatauserprofile $dataprofile){
		$this->profile = $dataprofile->get_data_user_profile();
	}

	public function get_user_profile(){
		return $this->profile;
	}

	public function set_user_profile($profile){
		$this->profile = $profile;
	}

	abstract public function has_preference($itemkey);
	abstract public function no_preference_value();
	abstract public function is_relevant($itemkey);
	abstract public function like($itemkey);
}
