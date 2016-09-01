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
 * Recblock block install.php file
 *
 * @package    block_recblock
 * @copyright  Boris Ballester Hernandez <borbalher@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot.'/blocks/recblock/lib.php');

function set_vark_table(){
	$table = block_recblock_get_vark_table_name();
    $varkactivities = block_recblock_get_vark_default_activities();
    $varkactivitiesvalues = block_recblock_get_vark_activities_default_types();

    $records = [];
	foreach($varkactivities as $varkactivity){
		$varkactivityvalues = $varkactivitiesvalues[$varkactivity];
		$record = new stdClass();
        $record->moduleid = $varkactivity;
        $record->visual = $varkactivityvalues[0];
        $record->auditive = $varkactivityvalues[1];
        $record->reading = $varkactivityvalues[2];
        $record->kinesthetic = $varkactivityvalues[3];
        array_push($records,$record);
	}
}

function set_bartle_table(){
    $table = block_recblock_get_bartle_table_name();
    $bartleactivities = block_recblock_get_bartle_default_activities();
    $bartleactivitiesvalues = block_recblock_get_bartle_activities_default_types();

    $records = [];
	foreach($bartleactivities as $bartleactivity){
		$bartleactivityvalues = $bartleactivitiesvalues[$bartleactivity];
		$record = new stdClass();
        $record->moduleid = $bartleactivity;
        $record->achiever = $bartleactivityvalues[0];
        $record->explorer = $bartleactivityvalues[1];
        $record->socializer = $bartleactivityvalues[2];
        $record->killer = $bartleactivityvalues[3];
        array_push($records,$record);
	}

    global $DB;
    $DB->insert_records($table, $records);	
}

function xmldb_block_recblock_install() {
	set_bartle_table();
	set_vark_table();
}
