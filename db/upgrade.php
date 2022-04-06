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

/* function xmldb_format_mooin4_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();


    if ($oldversion < 2020072801) {
        $table = new xmldb_table('course_mooin4_nav_card');

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table->add_field('news_forum', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('news_dorum_desc', XMLDB_TYPE_CHAR, '120', null, XMLDB_NOTNULL, null, null);
        $table->add_field('teilnehmenden', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('teilnehmenden_desc', XMLDB_TYPE_CHAR, '120', null, XMLDB_NOTNULL, null, null);
        $table->add_field('diskussionsforen', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('diskussionsforen_desc', XMLDB_TYPE_CHAR, '120', null, XMLDB_NOTNULL, null, null);
        $table->add_field('social_media', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('social_media_desc', XMLDB_TYPE_CHAR, '120', null, XMLDB_NOTNULL, null, null);
        $table->add_field('inhaltsverzeichnis', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('inhaltsverzeichnis_desc', XMLDB_TYPE_CHAR, '120', null, XMLDB_NOTNULL, null, null);

        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        // }

        upgrade_plugin_savepoint(true, 2020072801, 'format', 'mooin4');
    }
    
    purge_all_caches();
    
    return true;
} */