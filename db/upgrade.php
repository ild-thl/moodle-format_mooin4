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
 * mooin4 Format - A topics based format that uses a mooin4 of user selectable section to show content.
 *
 * @package    format_mooin4
 * @version    See the value of '$plugin->version' in version.php.
 * @copyright  &copy; 2012 G J Barnard in respect to modifications of standard topics and mooin4 format.
 * @author     Perial Dupont -
 *                           {@link http://moodle.org/user/profile.php?id=442195}
 * @author     Based on code originally written by Paul Krix and Julian Ridden and J Barnad.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// defined('MOODLE_INTERNAL') || die();

function xmldb_format_mooin4_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2022090914) {

        // Define field courseid to be added to format_mooin4_section.
        $table = new xmldb_table('format_mooin4_section');
        $field = new xmldb_field('courseid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'sectionurl');
        $field = new xmldb_field('sectionid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'courseid');
        // Conditionally launch add field courseid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
            // Launch change of default for field sectionid.
            // $dbman->change_field_default($table, $field);
        }

        // Mooin4 savepoint reached.
        upgrade_plugin_savepoint(true, 2022090914, 'format', 'mooin4');
    }
    return true;
}