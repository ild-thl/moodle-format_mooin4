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
 * Add a new Lektion inside a chapter
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

// Get the Previous chapter data and build a new array for comparaison
$before_chapter_array = [];
$before_chapter_db = $DB->get_records('format_mooin4_chapter', ['courseid' => $_POST['courseid']], 'id', '*');
foreach ($before_chapter_db as $key => $value) {
    array_push($before_chapter_array, $value->sectionnumber);
}

// Chage the data in lib.php
// $b = 'divisortext' . strval($POST['sectionid']);
$c = 'divisor' . strval($_POST['sectionid']);
$d = 'numsections';
// $e = json_encode($course_new->$c);
// $int_numsections = intval($e);


// (array)$course_new->$b = $POST['chapter_title'];
(array)$course_new->$c = $_POST['sectionnumber'];
(array)$course_new->$d += 1;

$courseformat->update_course_format_options($course_new);

// Updateby adding new lektion in  the right chapter data in DB(format_mooin_chapter)
$addNewSection = new stdClass();
$addNewSection->id = $_POST['id'];
$addNewSection->courseid = $_POST['courseid'];
$addNewSection->chapter_title = $_POST['chapter_title'];
$addNewSection->sectionid = $_POST['sectionid'];
$addNewSection->sectionnumber = $_POST['sectionnumber'];

$addSection = $DB->update_record('format_mooin4_chapter', $addNewSection);

// Update the course_sections Table
$chapter_db = $DB->get_records('format_mooin4_chapter', ['courseid' => $_POST['courseid']], 'id', '*');

// Found the exact number an section we have to splice to get the right position for the new section

$new_chapter_data = [];
$section_cut_value = 0;
$before_value = 0;
foreach ($chapter_db as $key => $value) {
    array_push($new_chapter_data, $value->sectionnumber);
}

for ($i=0; $i < count($before_chapter_array); $i++) { 
    for ($j=0; $j < count($new_chapter_data); $j++) {
        // echo('I : ' . $i .'<br>' . 'J : ' . $j . '<br>' );
        if( $i === $j){
            if(($before_chapter_array[$i] != $new_chapter_data[$j]) && $section_cut_value === 0 ) {
                $section_cut_value = $new_chapter_data[$j];
            } 
            if($before_chapter_array[$i] === $new_chapter_data[$j]) {
                $before_value += $new_chapter_data[$j];
            }
            if(($before_chapter_array[$i] != $new_chapter_data[$j])) {
                $section_cut_value = $before_value + $new_chapter_data[$j];
            }
        }
    }
}
// echo("Section new id : " . $section_cut_value . '<br>');
// Fetch the data in course_sections Table
$sections_in_course = $DB->get_records('course_sections', ['course' => $_POST['courseid']], 'section', '*');
$last_id = 0;
$array_id = [];
foreach ($sections_in_course as $k => $v) {
    array_push($array_id, $v->id);
}
// Get the id for the new Section
$last_id = end($array_id);

// The data to add in the course_sections table
$section_to_add = new \stdClass();

$section_to_add->id = $last_id + 1;
$section_to_add->course = $_POST['courseid'];
$section_to_add->section = $section_cut_value;
$section_to_add->name = null;
$section_to_add->summary = '';
$section_to_add->summaryformat = FORMAT_HTML;
$section_to_add->sequence = '';
$section_to_add->visible = 1;
$section_to_add->availability = null;
$section_to_add->timemodified = time();

// Convert to Array to merge easily
$res_section_to_add = json_decode(json_encode($section_to_add), true);
echo ( " Section Cut Value : " . $section_cut_value . '<br>');
$val = $section_cut_value;
foreach ($sections_in_course as $key => $value) {
   if($value->section == $val){
       $value->section = $val + 1;
       $val += 1;
   }
}
// echo"<pre>"; print_r($sections_in_course);
 $res_array = json_decode(json_encode($sections_in_course), true);
 $index_add_section = count($sections_in_course) +1;
// Merge the two array to build the new one with the right section id.
$result = array_merge(array_slice($res_array, 0, $section_cut_value, true) + array( $index_add_section => $res_section_to_add) + array_slice($res_array, $section_cut_value, count($res_array) - $section_cut_value , true));

// echo count($result) . '<br>';
// echo"<pre>"; print_r($result);
// update the value in DB course_sections
for ($i = count($result) - 1; $i >= 2 ; $i--) {
    if($section_cut_value <= $result[$i]['section']) {
        
        $section_to_update = new stdClass();

        $section_to_update-> id = $result[$i]['id'];
        $section_to_update-> course = $result[$i]['course'];
        $section_to_update-> section =$result[$i]['section'];
        $section_to_update-> name =$result[$i]['name'];
        $section_to_update-> summary = $result[$i]['summary'];
        $section_to_update-> summaryformat = $result[$i]['summaryformat'];
        $section_to_update-> sequence = $result[$i]['sequence'];
        $section_to_update-> visible = $result[$i]['visible'];
        $section_to_update-> availability = $result[$i]['availability'];
        $section_to_update-> timemodified = $result[$i]['timemodified'];
        
       
        $DB->update_record('course_sections', $section_to_update);
    }
}

$read_val  = $DB->get_records('course_sections', ['course' => $_POST['courseid']], 'section', '*');


// Redirect the browser
header("Refresh: 0");
