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
 * Edit the Lektion title server request
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package format_mooin4
 * @author Nguefack
 */
require_once('../../../config.php');
require_once($CFG->libdir.'/tablelib.php');
require_once($CFG->libdir.'/filelib.php');
require_once('../mooin4/lib.php');

global $DB;
global $PAGE;

$url = new moodle_url('/course/format/mooin4/inhalt.php', array('id' => $_POST['courseid']));
$section = 0;
    $saveEditSection = new stdClass();

    $saveEditSection->id = $_POST['id'];
    $saveEditSection->sectiontext = $_POST['sectiontext'];
    $saveEditSection->chapterid = $_POST['chapterid'];
    $saveEditSection->courseid = $_POST['courseid'];
    $saveEditSection->sectionid = $_POST['sectionid'];
    $saveEditSection->sectiondone = $_POST['sectiondone'];
    $saveEditSection->sectionurl = $_POST['sectionurl'];

    $save = $DB->update_record('format_mooin4_section', $saveEditSection);
    // $insert = "insert into TABLE_NAME values('$name','$last_name')";// Do Your Insert Query
    var_dump($save);

    // save the name in Table course_section
    // get the right section
    
    if(strlen(strrchr($_POST['sectionurl'],"=")) < 3){
        $section = intval(substr($_POST['sectionurl'], -1, strlen($_POST['sectionurl'])));
    }else {
        $section = intval(substr($_POST['sectionurl'], -2, strlen($_POST['sectionurl'])));
    }
    echo(' Section :' . $section .'<br>' );
    // DB call to get all the data inside the table
    
    $db_call_data = $DB->get_record('course_sections', array('course' => $_POST['courseid'], 'section' => $section), '*', MUST_EXIST);

    $upSection = new stdClass();

    $upSection->id = $db_call_data->id;
    $upSection->name = $_POST['sectiontext'];
    $upSection->availability = $db_call_data->availability;

    $DB->update_record('course_sections', $upSection);
    // Purge all cache to directly see the changes occur in frontend.
    rebuild_course_cache($_POST['courseid'], true);
    // Redirect the browser
header("Refresh: 0");
    