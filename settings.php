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
 * Recblock block settings.
 *
 * @package    block_recblock
 * @copyright  Boris Ballester Hernandez <borbalher@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/blocks/recblock/lib.php');

$settings->add(new admin_setting_heading('block_recblock/recommenders_heading',
                                         get_string('header_recommenders', 'block_recblock'),
                                         get_string('desc_recommenders', 'block_recblock')));

$settings->add(new admin_setting_configselect('block_recblock/recommendation_mode',
                                              get_string('label_recommendation_mode', 'block_recblock'),
                                              get_string('desc_recommendation_mode', 'block_recblock'),
                                              block_recblock_get_recommenders_default_mode(),
                                              array('b' => get_string('best', 'block_recblock'),
                                                    'r' => get_string('random', 'block_recblock'))));
                    
$settings->add(new admin_setting_configselect('block_recblock/recommender_type',
                                             get_string('label_recommenders', 'block_recblock'),
                                             get_string('desc_recommenders', 'block_recblock'),
                                             block_recblock_get_default_recommender(),
                                             block_recblock_get_recommenders()));

											 
$settings->add(new admin_setting_heading('block_recblock/vark_header',
                                         get_string('header_vark', 'block_recblock'),
                                         get_string('desc_vark', 'block_recblock')));

$settings->add(new admin_setting_configselect('block_recblock/vark_item_mode',
                                              get_string('label_item_mode', 'block_recblock'),
                                              get_string('desc_item_mode', 'block_recblock'),
                                              block_recblock_get_vark_default_item_creation_mode(),
                                              array('d' => get_string('database', 'block_recblock'),
                                                    't' => get_string('tag', 'block_recblock'))));

$settings->add(new admin_setting_heading('block_recblock/bartle_header',
                                         get_string('header_bartle', 'block_recblock'),
                                         get_string('desc_bartle', 'block_recblock')));

$settings->add(new admin_setting_configselect('block_recblock/bartle_item_mode',
                                              get_string('label_item_mode', 'block_recblock'),
                                              get_string('desc_item_mode', 'block_recblock'),
                                              block_recblock_get_bartle_default_item_creation_mode(),
                                              array('d' => get_string('database', 'block_recblock'),
                                                    't' => get_string('tag', 'block_recblock'))));
