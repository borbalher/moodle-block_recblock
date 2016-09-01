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
 * Recblock block bartle_items_moodle.php file
 *
 * @package    block_recblock
 * @copyright  Boris Ballester Hernandez <borbalher@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/blocks/recblock/classes/recommender/items/recommender_item.php');
require_once($CFG->dirroot . '/blocks/recblock/classes/data/items/idataitems.php');
require_once($CFG->dirroot . '/blocks/recblock/lib.php');

class bartle_items_moodle implements idataitems{

	private $regexp = '/\{EJ\:([AESK]+)\}/i'; //Expresion regular que usaremos para obtener el tipo de actividad del modulo

	public function get_regexp(){
		return $this->get_regexp();
	}

	private function set_vark_type(&$cmbartlevalues, $bartletype){
		switch ($bartletype){
			case 'A':
				$cmbartlevalues[0] = 1;
				break;
			case 'E':
				$cmbartlevalues[1] = 1;
				break;
			case 'S':
				$cmbartlevalues[2] = 1;
				break;
			case 'K':
				$cmbartlevalues[3] = 1;
				break;
		}
	}

	public function get_data_items(){
		global $COURSE;
		$coursemodulesinfo = get_fast_modinfo($COURSE->id);
		$bartleactivities = block_recblock_get_bartle_modules_type();
		$bartlemode = get_config('block_recblock','bartle_item_mode');

		$items = [];
		foreach ($coursemodulesinfo->cms as $cm){ //Para cada modulo del curso...
			global $DB;
			$modtype =  $DB->get_record_sql('SELECT id FROM {modules} WHERE name = :modname', array("modname"=>$cm->modname));

			if(in_array($modtype->id,$bartleactivities)){//Si es un tipo de actividad que contemplamos
				$cmbartlevalues = [];
				$matches = [];

				preg_match($this->regexp,  $cm->name, $matches); //Comprobamos si el tipo de actividad/recurso esta contenido en el nombre

				if(!empty($matches)){//Si tiene etiqueta creamos el vector de caracteristicas indicado
					$cmbartlevalues = array(0,0,0,0);
					$cmtag = str_split(strtoupper($matches[1]));

					foreach($cmtag as $bartletype){
						$this->set_bartle_type($cmbartlevalues,$bartletype);
					}
				}else if(empty($matches) && $bartlemode=="d"){ //Si no la contiene cogemos los tipos por defecto en caso de que no sea modo 'solamente etiquetas'
					$cmdefaultbartlevalues = $DB->get_record_sql('SELECT achiever, explorer, socializer, killer FROM {block_recblock_dtb} WHERE moduleid = :moduleid', array("moduleid"=> $modtype->id));
					$cmbartlevalues = array($cmdefaultbartlevalues->achiever,$cmdefaultbartlevalues->explorer,$cmdefaultbartlevalues->socializer,$cmdefaultbartlevalues->killer);
				}

				if(!empty($cmbartlevalues)){
					$cmbartle = new recommender_item($cm->id, $cm->name,$modtype, $cm->url, $cmbartlevalues);
					array_push($items,$cmbartle);
				}
			}
		}
		return $items;
	}
}
