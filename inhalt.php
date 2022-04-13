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
// User roles
$roles = get_user_roles($context, $USER->id, false);

echo $OUTPUT->header();

$arr = [];
$userrole = true;
foreach ($course_new as $key => $value) {
    if(strpos($key, 'divisor') === 0 ){
        //str_contains($key, 'divisor')
        // var_dump($value);
        if($value != 0 && $value != ''){
           $arr[$key] = $value;
        }     
    }
}
// print_r($arr);

$inhaltblock = array();
$sectionblock = array();
$check = [];
$arr_values = [];
$arr_result= [];
$last_arr = [];
$a = 1;
$j = 0;
$h = 1;
$sectionUrl = new moodle_url('/course/view.php', array('id' => $courseid));

foreach ($arr as $key => $value) {
   /*  print_r($value);
    echo('<br>'); */
    // Create a new array to save the Category data text and index
    if(strpos($key, 'divisortext') === 0) {
        // str_contains($key, 'divisortext')
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
        $db_chapter_courseid = [];
        $db_chapter_sectionid = [];
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
            $db_chapter = $DB->get_records('format_mooin4_chapter', array(), 'id', '*', IGNORE_MISSING);
            foreach ($db_chapter as $key => $valuecheck) {
                array_push($db_chapter_courseid,$valuecheck->courseid . $valuecheck->sectionid);
            }
            
            // echo(count((array)$dataobjects));
           // array_count_values((array)$dataobjects);
           $val = $dataobjects->courseid . $dataobjects->sectionid;
           if(!in_array($val, $db_chapter_courseid)){
            $DB->insert_record('format_mooin4_chapter', $dataobjects);
           }
           unset($dataobjects);
            
            array_push($arr_values,$value);
            for ($i=1; $i < $value +1; $i++) {
                $k = count($check) +1; // section index
                array_push($check, (object)[
                    'sectionId' => $i,
                    'sectionDone' => 0,
                    'sectionText' => 'Section Title ' . $i,
                    'sectionUrl' => $sectionUrl. '#section=' . $k,
                    'chapterId' => $j
                ]);
                $db_section_sectionid = [];
                $datasection = new  stdClass();

                $datasection->id = $k;
                $datasection->sectionid = $i;
                $datasection->sectiondone = 0;
                $datasection->sectiontext = 'Section Title ' . $i;
                $datasection->sectionurl = $sectionUrl . '#section=' . $k;
                $datasection->chapterid = $j;
                $datasection->courseid = $courseid;

                //print_r($datasection);
                //echo('<br>');

                // Insert Data in format_mooin4_section;
                $db_section = $DB->get_records('format_mooin4_section', array(), 'id', '*', IGNORE_MISSING);
                if(empty($db_section)){
                    $DB->insert_record('format_mooin4_section', $datasection);
                } else {
                    foreach ($db_section as $k => $v) {
                        array_push($db_section_sectionid, $v->courseid . $v->chapterid . $v->sectionid);
                    }
                    //print_r($db_section_sectionid);
                    $valsection = $courseid . $j . $i;
                    //print_r($valsection);
                    if(!in_array($valsection, $db_section_sectionid)){
                        $DB->insert_record('format_mooin4_section', $datasection);
                    }
                }
                
                //print_r($datasection);
            } 
            
        }
    }    
};

echo('<br>');
// Get the data from the DB Chapter and section

$db_chapter = "SELECT * FROM mdl2_format_mooin4_chapter  fmc WHERE fmc.courseid = {$courseid}";
$sql_chapter = $DB->get_records_sql($db_chapter);

$db_section = "SELECT * FROM mdl2_format_mooin4_section fms WHERE fms.courseid = {$courseid}";
$sql_section = $DB->get_records_sql($db_section);

// print_r($data_section);
// Create the new array for Section list base on the number of section in each categorie
foreach ($arr_values as $key => $value) {
    $sectionblock = array_slice($sql_section, 0, $value); // $check = $sql_section
    array_splice($sql_section, 0, $value); // $check = $sql_section
    array_push($arr_result,$sectionblock );
}
// print_r($arr_result);
$inhalt = array_merge((array) $sql_chapter);

// Create the new structure for the table of content, each categorie with is specific sections number and if the section has been done or not
for ($i=0; $i < count($inhalt) ; $i++) { //$sql_chapter == $inhaltblock
    for ($j=0 ; $j < count($arr_result); $j++) { 
        // print_r($arr_result[$j]);;
        // echo('<br>');
        if($i == $j) {
            /* $inhaltblock[$i] = (array)$inhaltblock[$i];
            $inhaltblock[$i]['sectionData'] = $arr_result[$j];
            $inhaltblock[$i] = (object)$inhaltblock[$i]; */
            // One line
            $inhalt[$i] = (object) array_merge( (array)$inhalt[$i], array( 'sectionData' => $arr_result[$j] ) );
        }
    }
}

/* $json_pretty = json_encode($inhalt, JSON_PRETTY_PRINT);
echo "<pre>" . $json_pretty . "<pre/>"; */

echo('<br>');
//Check if the user is a student (roleid = 5) or not.
$checkstudent= [];
foreach ($roles as $key => $value) {
    array_push($checkstudent, $value->roleid);
}
if(!in_array('5',$checkstudent)){
    $userrole = false;
}else {
    $userrole = true;
}
// var_dump($checkstudent);
$templatecontext = (object)[
    'blokinhalt' => array_values($inhalt),
    'editurl' => new moodle_url('/course/format/mooin4/edit.php', array('id' => $courseid )),
    'myCondition' => $userrole
];

echo $OUTPUT->render_from_template('format_mooin4/manage_inhalt', $templatecontext);

// Set preference $courseid to save the id 
$listOfPreferencesEdit = array('id' => $courseid);

set_user_preferences($listOfPreferencesEdit);
echo $OUTPUT->footer();