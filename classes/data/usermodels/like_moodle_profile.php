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
 * Recblock block like_moodle_profile.php file
 *
 * @package    block_recblock
 * @copyright  Boris Ballester Hernandez <borbalher@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/blocks/recblock/classes/data/usermodels/idatauserprofile.php');
require_once($CFG->dirroot . '/blocks/recblock/lib.php');
require_once($CFG->dirroot . '/blocks/recblock/classes/recommender/items/recommender_items.php');

class like_moodle_profile implements idatauserprofile{

	protected $courseitems;
	protected $likevalue;
	protected $dislikevalue;

	public function __construct(recommender_items $courseitems, $likevalue, $dislikevalue){
		$this->courseitems = $courseitems;
		$this->likevalue = $likevalue;
		$this->dislikevalue = $dislikevalue;
	}

	public function get_data_user_profile(){
		global $COURSE, $USER ,$DB;

		$profile = [];

		$logtable = block_recblock_get_log_table_name(); // Tabla log que usaremos para obtener los datos
		// Solo procederemos si existe la tabla de log
		if (!empty($logtable)) {

			$sqlparams['edulevel'] = core\event\base::LEVEL_PARTICIPATING; //Añadimos el parametro edulevel que nos indica acciones que indican participacion del usuario (2)
			$sqlparams['contextlevel'] = CONTEXT_MODULE; //Añadimos el parametro contexlevel que nos indica el nivel de contexto, en este caso el de modulos (70)
			$sqlparams['userid'] = $USER->id; //Añadimos el parametro userid que nos indica el id del usuario actual
			$roles = get_user_roles(context_course::instance($COURSE->id),  $USER->id, false);
			$sqlparams['roleid'] = $roles[key($roles)]->roleid; //Añadimos el parametro roleid que nos indica el rol del usuario dentro del curso
			$context = context_course::instance($COURSE->id);
			$sqlparams = array_merge($sqlparams,
					$DB->get_in_or_equal($context->get_parent_context_ids(true), SQL_PARAMS_NAMED, 'relatedctx'),
					block_recblock_get_crud_sql()
					);


			$numactions = [];
			foreach($this->courseitems->get_items() as $courseitem){
				$sqlparams['instanceid'] = $courseitem->get_id(); //Añadimos a los parametros el id de la instacia de la actividad/recurso
				$sql = "SELECT  COUNT(DISTINCT l.timecreated) AS count
				FROM {user} u
				JOIN {role_assignments} ra ON u.id = ra.userid AND ra.contextid $relatedctxsql AND ra.roleid = :roleid
				LEFT JOIN {" . $logtable . "} l ON l.contextinstanceid = :instanceid". $crudsql ."
                               AND l.edulevel = :edulevel
                               AND l.anonymous = 0
                               AND l.contextlevel = :contextlevel
                               AND (l.origin = 'web' OR l.origin = 'ws')
                               AND l.userid = ra.userid
                         WHERE u.id = :userid";

				//Resultados de la query, nos devuelve un campo llamado count con el recuento de acciones de la actividad/recurso
				$record = $DB->get_record_sql($sql, $sqlparams);
				array_push($numactions,$record->count);
			}


			$normactions = block_recblock_standard_normalization($numactions);

			foreach($normactions as $normaction){
				if($normaction >= $this->likevalue){
					array_push($profile,1);
				}else if($normaction <= $this->dislikevalue){
					array_push($profile,-1);
				}else{
					array_push($profile,0);
				}
			}


		}
		return $profile;
	}
}
