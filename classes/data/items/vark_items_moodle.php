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
 * Recblock block vark_items_moodle.php file
 *
 * @package    block_recblock
 * @copyright  Boris Ballester Hernandez <borbalher@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

define('LESSON_TYPE_MODULE_ID', 13);
define('QUIZ_TYPE_MODULE_ID', 16);

require_once($CFG->dirroot . '/blocks/recblock/classes/recommender/items/recommender_item.php');
require_once($CFG->dirroot . '/blocks/recblock/classes/data/items/idataitems.php');
require_once($CFG->dirroot . '/blocks/recblock/lib.php');

class vark_items_moodle implements idataitems{

	private $regexp = '/\{EA\:([VARK]+)\}/i'; //Expresion regular que usaremos para obtener el tipo de actividad del modulo

	public function get_regexp(){
		return $this->get_regexp();
	}

	private function set_vark_type(&$cmvarkvalues, $varktype){
		switch ($varktype){
			case 'V':
				$cmvarkvalues[0] = 1;
				break;
			case 'A':
				$cmvarkvalues[1] = 1;
				break;
			case 'R':
				$cmvarkvalues[2] = 1;
				break;
			case 'K':
				$cmvarkvalues[3] = 1;
				break;
		}
	}

	private function reattemp_quiz($quizcmid){
		global $DB;

		$quizcm = $DB->get_record_sql('SELECT instance FROM {course_modules} WHERE id = :cmid', array("cmid"=>$quizcmid));
		$quiz = $DB->get_record_sql('SELECT attempts FROM {quiz} WHERE id = :quizid', array("quizid"=>$quizcm->instance));
		if($quiz->attempts != 0){
			return false;
		}else{
			return true;
		}
	}

	private function retake_lesson($lessoncmid){
		global $DB;

		$lessoncm = $DB->get_record_sql('SELECT instance FROM {course_modules} WHERE id = :cmid', array("cmid"=>$lessoncmid));
		$lesson = $DB->get_record_sql('SELECT retake FROM {lesson} WHERE id = :lessonid', array("lessonid"=>$lessoncm->instance));
		if($lesson->retake != 1){
			return false;
		}else{
			return true;
		}
	}

	public function get_data_items(){
		global $COURSE;

		$coursemodulesinfo = get_fast_modinfo($COURSE->id);
		$varkactivities = block_recblock_get_vark_modules_type();
		$varkmode = get_config('block_recblock','vark_item_mode');

		$items = [];
		foreach ($coursemodulesinfo->cms as $cm){ //Para cada modulo del curso...
			global $DB;
			$modtype =  $DB->get_record_sql('SELECT id FROM {modules} WHERE name = :modname', array("modname"=>$cm->modname));

			if(in_array($modtype->id,$varkactivities)){//Si es un tipo de actividad que contemplamos
				$continue = true;

				if($modtype->id == QUIZ_TYPE_MODULE_ID){
					$continue = $this->retake_lesson($cm->id);
				}else if($modtype->id == LESSON_TYPE_MODULE_ID){
					$continue = $this->reattemp_quiz($cm->id);
				}

				if($continue){

					$cmvarkvalues = [];
					$matches = [];

					preg_match($this->regexp,  $cm->name, $matches); //Comprobamos si el tipo de actividad/recurso esta contenido en el nombre

					if(!empty($matches)){//Si tiene etiqueta creamos el vector de caracteristicas indicado
						$cmvarkvalues = array(0,0,0,0);
						$cmtag = str_split(strtoupper($matches[1]));

						foreach($cmtag as $varktype){
							$this->set_vark_type($cmvarkvalues,$varktype);
						}
					}else if(empty($matches) && $varkmode=="d"){ //Si no la contiene cogemos los tipos por defecto en caso de que no sea modo 'solamente etiquetas'
						$cmdefaultvarkvalues = $DB->get_record_sql('SELECT visual, auditive, reading, kinesthetic FROM {block_recblock_dtv} WHERE moduleid = :moduleid', array("moduleid"=> $modtype->id));
						$cmvarkvalues = array($cmdefaultvarkvalues->visual,$cmdefaultvarkvalues->auditive,$cmdefaultvarkvalues->reading,$cmdefaultvarkvalues->kinesthetic);
					}

					if(!empty($cmvarkvalues)){
						$cmvark = new recommender_item($cm->id, $cm->name,$modtype, $cm->url, $cmvarkvalues);
						array_push($items,$cmvark);
					}
				}
			}
		}
		return $items;
	}
}
