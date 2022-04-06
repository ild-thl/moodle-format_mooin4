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
 * Lists all the users within a given course.
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core_user
 */

// use format_mooin4\mooin4_online_users_map;

require_once('../../../config.php');
require_once($CFG->libdir.'/tablelib.php');
require_once($CFG->libdir.'/filelib.php');
require_once('../mooin4/lib.php');

define('USER_SMALL_CLASS', 20);   // Below this is considered small.
define('USER_LARGE_CLASS', 200);  // Above this is considered large.
define('DEFAULT_PAGE_SIZE', 20);
define('SHOW_ALL_PAGE_SIZE', 5000);
define('MODE_BRIEF', 0);
define('MODE_USERDETAILS', 1);

global $DB;
global $PAGE;

$page         = optional_param('page', 0, PARAM_INT); // Which page to show.
$perpage      = optional_param('perpage', DEFAULT_PAGE_SIZE, PARAM_INT); // How many per page.
$mode         = optional_param('mode', null, PARAM_INT); // Use the MODE_ constants.
$accesssince  = optional_param('accesssince', 0, PARAM_INT); // Filter by last access. -1 = never.
$search       = optional_param('search', '', PARAM_RAW); // Make sure it is processed with p() or s() when sending to output!
$roleid       = optional_param('roleid', 0, PARAM_INT); // Optional roleid, 0 means all enrolled users (or all on the frontpage).
$contextid    = optional_param('contextid', 0, PARAM_INT); // One of this or.
$courseid     = optional_param('id', 0, PARAM_INT); // This are required.

$PAGE->set_url('/course/format/mooin4/inhalt.php', array(
		'id' => $courseid ));

if ($contextid) {
    $context = context::instance_by_id($contextid, MUST_EXIST);
    if ($context->contextlevel != CONTEXT_COURSE) {
        print_error('invalidcontext');
    }
    $course = $DB->get_record('course', array('id' => $context->instanceid), '*', MUST_EXIST);
} else {
    $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
    $context = context_course::instance($course->id, MUST_EXIST);
}
// Not needed anymore.
// unset($contextid);
// unset($courseid);
// $coursesectionss = $DB->get_records('course_sections', array('course' => $course->id));
// $modinfo = get_fast_modinfo($course);
// $sectionss = $modinfo->get_section_info_all();
$courseformat = course_get_format($course);
$course_new = $courseformat->get_course();

require_login($course);
$PAGE->set_course($course);
$PAGE->set_pagelayout('standard');
$PAGE->set_context(\context_course::instance($courseid));
$PAGE->set_title(get_string("inhalt",'format_mooin4'));
$PAGE->set_heading(get_string('courseinhalt','format_mooin4'));
$PAGE->navbar->add(get_string('inhalt','format_mooin4',));

$systemcontext = context_system::instance();
$isfrontpage = ($course->id == SITEID);

$frontpagectx = context_course::instance(SITEID);

echo $OUTPUT->header();

$arr = [];
foreach ($course_new as $key => $value) {
    if(str_contains($key, 'divisor')){
        // var_dump($value);
        //echo('<br>');
        if($value != 0 && $value != ''){
           $arr[$key] = $value;
        }     
    }
}
// print_r($course_new);

$inhaltblock = array();
$sectionblock = array();
$check = [];
$arr_values = [];
$arr_result= [];
$last_arr = [];
$a = 1;
$j = 0;
$h = 1;
$sectionUrl = new moodle_url('http://localhost/moodle/course/view.php', array('id' => $courseid));
foreach ($arr as $key => $value) {
    // Create a new array to save the Category data text and index
    if(str_contains($key, 'divisortext')) {
        // $inhaltblock['courseId'] = $courseid;
        array_push($inhaltblock, (object)[
            'id' =>$a++,
            'divisortext' => $value,
            'courseid' => $courseid,
            'idsection' => $h++
            
        ]);
    }
    // Create a new Section structure to deal with index, text and checkbox
     
    if(!strstr($key, 'text')){
        $t = $j;
        if ($value > 0) {
            // echo('<br>' . 'Value ' . $j++ . ' ' . $value . '<br>');
            $inhaltblock[$j++]->sectionnumber = $value;
            
            $dataobjects = new stdClass();
            $dataobjects->id = $inhaltblock[$t]->id;
            $dataobjects->courseid = $inhaltblock[$t]->courseid;
            $dataobjects->chapter_title = $inhaltblock[$t]->divisortext;
            $dataobjects->sectionid = $inhaltblock[$t]->idsection;
            $dataobjects->sectionnumber = $inhaltblock[$t]->sectionnumber;

            // Insert Data in format_mooin4_chapter;
            $dbtest_chapter = $DB->get_records('format_mooin4_chapter', array(), 'sectionid', 'sectionid', IGNORE_MISSING);
            if (empty($dbtest_chapter) && !in_array($dataobjects->sectionid, (array)$dbtest_chapter)) {
                $DB->insert_record('format_mooin4_chapter', $dataobjects);
                
            }
            unset($dataobjects);
            
            array_push($arr_values,$value);
            for ($i=1; $i < $value +1; $i++) {
                $k = count($check) +1; // section index
                array_push($check, (object)[
                    'sectionId' => $i,
                    'sectionDone' => 0,
                    'sectionText' => 'Section Title ' . $k,
                    'sectionUrl' => 'http://localhost/moodle/course/view.php?id=' . $courseid . '#section=' . $k,
                ]);
            } 
        }
    }    
};

echo('<br>');

// Create the new array for Section list base on the number of section in each categorie
foreach ($arr_values as $key => $value) {
    $sectionblock = array_slice($check, 0, $value);
    array_splice($check, 0, $value);
    array_push($arr_result,$sectionblock );  
}
// print_r($arr_result);
// print_r($arr_result);
// Create the new structure for the table of content, each categorie with is specific sections number and if the section has been done or not
for ($i=0; $i <count($inhaltblock) ; $i++) { 
    for ($j=0; $j < count($arr_result); $j++) { 
        if($i == $j) {
            /* $inhaltblock[$i] = (array)$inhaltblock[$i];
            $inhaltblock[$i]['sectionData'] = $arr_result[$j];
            $inhaltblock[$i] = (object)$inhaltblock[$i]; */
            // One line
            $inhaltblock[$i] = (object) array_merge( (array)$inhaltblock[$i], array( 'sectionData' => $arr_result[$j] ) );
        }
    }
}

/* $json_pretty = json_encode($inhaltblock, JSON_PRETTY_PRINT);
echo "<pre>" . $json_pretty . "<pre/>";
echo('<br>'); */


$templatecontext = (object)[
    'blokinhalt' => array_values($inhaltblock),
    'editurl' => new moodle_url('/course/format/mooin4/edit.php', array('id' => $courseid )),    
];

echo $OUTPUT->render_from_template('format_mooin4/manage_inhalt', $templatecontext);

// var_dump($course_new);
// Some test to get DB table content
$dbtest_section = $DB->get_records('format_mooin4_section', array(), 'id', '*', IGNORE_MISSING);
$dbtest_chapter = $DB->get_records('format_mooin4_chapter', array(), 'sectionid','sectionid', IGNORE_MISSING);
$dbtest_course = $DB->get_records('course', array(), 'id', '*', IGNORE_MISSING);

// print_r($dbtest_course);
if (isset($dbtest_section)) {
    //print_r($dbtest_section);
    //var_dump(array_key_last($dbtest_chapter));
} else {
    echo('doesn\'t exist');
}

// Set preference $courseid to save the id 
$listOfPreferencesEdit = array('id' => $courseid);

set_user_preferences($listOfPreferencesEdit);
echo $OUTPUT->footer();