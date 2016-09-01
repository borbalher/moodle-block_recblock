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
 * Recblock block recommender_item.php file
 *
 * @package    block_recblock
 * @copyright  Boris Ballester Hernandez <borbalher@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

class recommender_item{
	protected $id;
	protected $name;
	protected $type;
	protected $url;
	protected $vector;

	public function __construct($id,$name,$type,$url,$vector){
		$this->id = $id;
		$this->name = $name;
		$this->type = $type;
		$this->url = $url;
		$this->vector = $vector;
	}

	public function get_id(){
		return $this->id;
	}

	public function get_name(){
		return $this->name;
	}

	public function get_type(){
		return $this->type;
	}

	public function get_url(){
		return $this->url;
	}

	public function set_id($id){
		$this->id = $id;
	}

	public function set_name($name){
		$this->name = $name;
	}

	public function set_type($type){
		$this->type = $type;
	}

	public function set_url($url){
		$this->url = $url;
	}

	public function set_vector($vector){
		$this->vector = $vector;
	}

	public function get_vector(){
		return $this->vector;
	}
}
