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
 * Delete a Section inside a Chapter
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

$contextid    = optional_param('contextid', 0, PARAM_INT); // One of this or.
$courseid     = optional_param('id', 0, PARAM_INT); // This are required. required_param('id', PARAM_INT); 

if ($contextid) {
    $context = context::instance_by_id($contextid, MUST_EXIST);
    if ($context->contextlevel != CONTEXT_COURSE) {
        print_error('invalidcontext');
    }
    $course = $DB->get_record('course', array('id' => $context->instanceid), '*', MUST_EXIST);
} else {
    $course = $DB->get_record('course', array('id' => $_POST['courseid']), '*', MUST_EXIST);
    $context = context_course::instance($course->id, MUST_EXIST);
}

$courseformat = course_get_format($course);
$course_new = $courseformat->get_course();

$url = new moodle_url('/course/format/mooin4/inhalt.php', array('id' => $course->id));

// Delete the section in the DB ( format_mooin4_section)
// Update the right sectionnumber in chapter table  in format_mooin4_chapter
// Update the section in course sections table
// Update the number section in lib.php file

$deletesec = new stdClass();
$deletesec->courseid = $_POST['courseid'];
$deletesec->chapterid = $_POST['chapterid'];
$deletesec->sectionid = $_POST['sectionid'];


$sectionIdUrl = $_POST['sectionidurl'];
$courseId = $_POST['courseid'];
$sectionId = $_POST['sectionid'];
$chapterId = $_POST['chapterid'];
// Delete the Section in the DB table format_mooin4_section
// $DB->delete_records('format_mooin4_section', ['course' => $POST['courseid'], 'section' =>$_POST['sectionid']]);
$DB-> delete_records_select('format_mooin4_section', "(courseid = {$courseId} AND chapterid = {$chapterId} AND sectionid = {$sectionId})");

// Update the format mooin4 section after deleted the right section
$update_section = $DB->get_records('format_mooin4_section', ['courseid' => $_POST['courseid']], 'chapterid','*', IGNORE_MISSING);
foreach ($update_section as $key => $value) {
    $secId = 0;
    // fetch the sectionid in the url
    if(strlen(strrchr($value->sectionurl,"=")) < 3){
        $secId = substr($value->sectionurl, -1, strlen($value->sectionurl));
    }else {
        $secId = substr($value->sectionurl, -2, strlen($value->sectionurl));
    }

    // change the $value->sectionid to the last sectionid in the url
    if($sectionIdUrl < $secId){
        $section_id_update = new stdClass();

        $section_id_update->id = $value->id;
        if(strlen(strrchr($value->sectionurl,"=")) < 3){
            $section_id_update->sectionurl = substr($value->sectionurl,0, -1) . strval($secId - 1);
        }else {
            $section_id_update->sectionurl = substr($value->sectionurl,0, -2) . strval($secId- 1);
        }

        $DB->update_record('format_mooin4_section', $section_id_update);
    }
}

// Update the chapter sectionid in table format_mooin4_section
$update_sectionid_in_chapter = $DB->get_records('format_mooin4_section', ['courseid' => $_POST['courseid'], 'chapterid' => $_POST['chapterid']], 'sectionid','*', IGNORE_MISSING);
foreach ($update_sectionid_in_chapter as $key => $value) {
    if ($value->sectionid > intval($_POST['sectionid'])) {
        $sec_in_chapter_update = new stdClass();
        
        $sec_in_chapter_update->id = $value->id;
        $sec_in_chapter_update->sectionid = intval($value->sectionid) -1;

        $DB->update_record('format_mooin4_section', $sec_in_chapter_update);
    }
}
// Update the sectionnumber in table format_mooin4_chapter
$updatechaptersection = new stdClass();
$updatechaptersection->id = $_POST['idchapter'];
$updatechaptersection->courseid = $_POST['courseid'];
$updatechaptersection->chapter_title = $_POST['chaptertitle'];
$updatechaptersection->sectionid = $_POST['chapterid'];
$updatechaptersection->sectionnumber = $_POST['sectionnumber'];
//Reduice the section number in Chapter
$chapterValue = $DB->get_records('format_mooin4_chapter', ['courseid' => $_POST['courseid'], 'sectionid' => $_POST['chapterid']], 'sectionid','*', IGNORE_MISSING);
foreach ($chapterValue as $key => $value) {
    if($value->courseid == $_POST['courseid'] && $value->sectionid == $_POST['chapterid']) {
        $DB->update_record('format_mooin4_chapter', $updatechaptersection);
    }
}

// Delete the section in Table course_sections
 $DB->delete_records('course_sections', ['course' => $courseId, 'section' => $sectionIdUrl]);
// $sec_data = $DB-> delete_records_select('course_sections', "(course = {$courseId} AND section = {$sectionIdUrl})");

// update the sections order in Table sections_sections
$sections_data = $DB->get_records('course_sections', ['course' => $courseId],'section', '*', IGNORE_MISSING);
// convert into a array
$sections_data_array = (array) $sections_data;

//
foreach ($sections_data_array as $key => $value) {
   if(intval($_POST['sectionidurl']) < $value->{'section'} && intval($_POST['courseid'] == $value->{'course'})) {
        echo('Inside');
        $section_to_update = new stdClass();

        $section_to_update-> id =  $value->{'id'};
        $section_to_update-> section = $value->{'section'} - 1;

        $res = $DB->update_record('course_sections', $section_to_update);
   }
}

// update the lib.php data
$c = 'divisor' . strval($_POST['chapterid']);
$d = 'numsections';

(array)$course_new->$c = $_POST['sectionnumber'];
(array)$course_new->$d -= 1;

$courseformat->update_course_format_options($course_new);
// Redirect the browser
header("Refresh: 0");