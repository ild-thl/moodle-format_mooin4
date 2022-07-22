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
require_once($CFG->dirroot . '/my/lib.php');
require_once('../../../course/lib.php');
require_once($CFG->libdir.'/completionlib.php');

/* require_once($CFG->libdir.'/tablelib.php');
require_once($CFG->libdir.'/filelib.php'); */
require_once('../mooin4/lib.php');

define('USER_SMALL_CLASS', 20);   // Below this is considered small.
define('USER_LARGE_CLASS', 200);  // Above this is considered large.
define('DEFAULT_PAGE_SIZE', 20);
define('SHOW_ALL_PAGE_SIZE', 5000);
define('MODE_BRIEF', 0);
define('MODE_USERDETAILS', 1);

global $DB;
global $PAGE;

$contextid    = optional_param('contextid', 0, PARAM_INT); // One of this or.
$courseid     = optional_param('id', 0, PARAM_INT); // This are required.

// new paramater
$id          = optional_param('id', 0, PARAM_INT);
$name        = optional_param('name', '', PARAM_TEXT);
$edit        = optional_param('edit', -1, PARAM_BOOL); // Turn editing on and off
$hide        = optional_param('hide', 0, PARAM_INT);
$show        = optional_param('show', 0, PARAM_INT);
$idnumber    = optional_param('idnumber', '', PARAM_RAW);
$sectionid   = optional_param('sectionid', 0, PARAM_INT);
$section     = optional_param('section', 0, PARAM_INT);
$move        = optional_param('move', 0, PARAM_INT);
$marker      = optional_param('marker',-1 , PARAM_INT);
$switchrole  = optional_param('switchrole',-1, PARAM_INT); // Deprecated, use course/switchrole.php instead.
$return      = optional_param('return', 0, PARAM_LOCALURL);
$reset  = optional_param('reset', null, PARAM_BOOL);

//end

$url = new moodle_url('/course/format/mooin4/inhalt.php', array('id'=>$courseid));


$PAGE->set_url($url);

// Get User Preferences
get_user_preferences();
$userPreferencesEdit = get_user_preferences('id');
// $teacherPreferenceEdit = get_user_preferences('gotoedit');

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

$courseformat = course_get_format($course);
$course_new = $courseformat->get_course();

// Remove any switched roles before checking login
if ($switchrole == 0 && confirm_sesskey()) {
    role_switch($switchrole, $context);
}

// $section = $DB->get_field('course_sections', 'section', array('id' => $sectionid, 'course' => $courseid), MUST_EXIST);

require_login($course);

// Switchrole - sanity check in cost-order...
$reset_user_allowed_editing = false;
if ($switchrole > 0 && confirm_sesskey() &&
    has_capability('moodle/role:switchroles', $context)) {
    // is this role assignable in this context?
    // inquiring minds want to know...
    $aroles = get_switchable_roles($context);
    if (is_array($aroles) && isset($aroles[$switchrole])) {
        role_switch($switchrole, $context);
        // Double check that this role is allowed here
        require_login($course);
    }
    // reset course page state - this prevents some weird problems ;-)
    $USER->activitycopy = false;
    $USER->activitycopycourse = NULL;
    unset($USER->activitycopyname);
    unset($SESSION->modform);
    $USER->editing = 0;
    $reset_user_allowed_editing = true;
}

$PAGE->set_course($course);
$PAGE->set_pagelayout('standard');
$PAGE->set_context(\context_course::instance($course->id));
$PAGE->set_title(get_string("inhalt",'format_mooin4'));
$PAGE->set_heading(get_string('courseinhalt','format_mooin4'));
$PAGE->navbar->add(get_string('inhalt','format_mooin4',));

// Load JS on the inhalt page
// Edit a Section Title ( Name )
$PAGE->requires->js_call_amd('format_mooin4/edit');
// Add new section in Chapter and in DB [format_mooin4_chapter && course_sections]
$PAGE->requires->js_call_amd('format_mooin4/add');
// Confirm deletein a Section inside a Chapter, Section and Course_sections Table and updating the lib.php content also
$PAGE->requires->js_call_amd('format_mooin4/confirm_section');
// Confirm deleting a Chapter in Chapter Table ( it has to be empty ), update the lib.php content also.
$PAGE->requires->js_call_amd('format_mooin4/confirm_chapter');
// Confirm Move Chapter, update the Chapter, Section, Course_sections Table and lib.php content.
$PAGE->requires->js_call_amd('format_mooin4/confirm_chapter_move');
// Confirm Move Section in Chapter, Update Chapter, Section, Course_sections Table and lib.php content.
$PAGE->requires->js_call_amd('format_mooin4/confirm_section_move');


$systemcontext = context_system::instance();
$isfrontpage = ($course->id == SITEID);

$frontpagectx = context_course::instance(SITEID);
// User roles
$roles = get_user_roles($context, $USER->id, false);

if ($reset_user_allowed_editing) {
    // ugly hack
    unset($PAGE->_user_allowed_editing);
}

if (!isset($USER->editing)) {
    $USER->editing = 0;
}

if ($PAGE->user_allowed_editing()) {
    if (($edit == 1) and confirm_sesskey()) {
        $USER->editing = 1;
        // Redirect to site root if Editing is toggled on frontpage
        if ($course->id == SITEID) {
            redirect($CFG->wwwroot .'/?redirect=0');
        } else if (!empty($return)) {
            redirect($CFG->wwwroot . $return);
        } else {
            $url = new moodle_url($PAGE->url, array('notifyeditingon' => 1));
            redirect($url);
        }
    } else if (($edit == 0) and confirm_sesskey()) {
        $USER->editing = 0;
        if(!empty($USER->activitycopy) && $USER->activitycopycourse == $course->id) {
            $USER->activitycopy       = false;
            $USER->activitycopycourse = NULL;
        }
        // Redirect to site root if Editing is toggled on frontpage
        if ($course->id == SITEID) {
            redirect($CFG->wwwroot .'/?redirect=0');
        } else if (!empty($return)) {
            redirect($CFG->wwwroot . $return);
        } else {
            redirect($PAGE->url);
        }
    }

    if (has_capability('moodle/course:sectionvisibility', $context)) {
        if ($hide && confirm_sesskey()) {
            set_section_visible($course->id, $hide, '0');
            redirect($PAGE->url);
        }

        if ($show && confirm_sesskey()) {
            set_section_visible($course->id, $show, '1');
            redirect($PAGE->url);
        }
    }

    if (!empty($section) && !empty($move) &&
            has_capability('moodle/course:movesections', $context) && confirm_sesskey()) {
        $destsection = $section + $move;
        if (move_section_to($course, $section, $destsection)) {
            if ($course->id == SITEID) {
                redirect($CFG->wwwroot . '/?redirect=0');
            } else {
                redirect(course_get_url($course));
            }
        } else {
            echo $OUTPUT->notification('An error occurred while moving a section');
        }
    }
} else {
    $USER->editing = 0;
}

// We are currently keeping the button here from 1.x to help new teachers figure out
// what to do, even though the link also appears in the course admin block.  It also
// means you can back out of a situation where you removed the admin block. :)
if ($PAGE->user_allowed_editing()) {
    $buttons = $OUTPUT->edit_button($PAGE->url);
    $PAGE->set_button($buttons);
}
// End for the test
echo $OUTPUT->header();

$editing_on = true;

if ($USER->editing == 1) {
    $editing_on = false;
    // MDL-65321 The backup libraries are quite heavy, only require the bare minimum.
    require_once($CFG->dirroot . '/backup/util/helper/async_helper.class.php');

    if (async_helper::is_async_pending($id, 'course', 'backup')) {
        echo $OUTPUT->notification(get_string('pendingasyncedit', 'backup'), 'warning');
    }
} else {
    $editing_on = true;
}

$section_all_content = $DB->get_records('course_sections', ['course' => $courseid]);
foreach ($section_all_content as $key => $value) {
    // echo($value->section . '<br>');
    //echo"<pre>"; print_r($value);
}

$inhaltblock = array();
$sectionblock = array();
$check = [];
$arr_values = [];
$arr_result= [];
$last_arr = [];
$a = 0;
$j = 0;
$h = 0;
$arr = [];
$userrole = true;
$sectionUrl = new moodle_url('/course/view.php', array('id' => $courseid));
$chapterafteredit = [];
$sectionafteredit = [];

/* if($teacherPreferenceEdit == false){ */
    // echo('Go to Edit is false');
    foreach ($course_new as $key => $value) {
        if(strpos($key, 'divisor') === 0 ){
            //str_contains($key, 'divisor')
            // var_dump($value);
            if($value >= 0 && $value != ''){
               $arr[$key] = $value;
            }     
        }
    }
    // echo count($arr);
    // echo'<pre>' .print_r($arr);
    
        
    foreach ($arr as $key => $value) {
        // Create a new array to save the Category data text and index
        if(strpos($key, 'divisortext') === 0) {
            // str_contains($key, 'divisortext')
            // $inhaltblock['courseId'] = $courseid;
            array_push($inhaltblock, (object)[
                'id' =>++$a,
                'divisortext' => $value,
                'courseid' => $courseid,
                'idsection' => ++$h,
                'sectionnumber' => 0,
                
            ]);
        }
        // Create a new Section structure to deal with index, text and checkbox
        
        if(!strstr($key, 'text')){
            $t = $j;
            $db_chapter_courseid = [];
            $db_chapter_sectionid = [];
            
            if (isset( $inhaltblock[$j])) {
                if ($value >= 0 && $inhaltblock[$j]->divisortext != '') {
                    // echo('<br>' . 'Value ' . $j++ . ' ' . $value . '<br>');
                    // $inhaltblock[$j++]->sectionnumber = 0;
                   //  echo($value);
                    $inhaltblock[$t]->sectionnumber = $value;
    
                    $dataobjects = new stdClass();
                    $dataobjects->id = ++$a;
                    $dataobjects->courseid = $inhaltblock[$t]->courseid;
                    $dataobjects->chapter_title = $inhaltblock[$j++]->divisortext;
                    $dataobjects->sectionid = $inhaltblock[$t]->idsection;
                    $dataobjects->sectionnumber = $inhaltblock[$t]->sectionnumber;
                    
                    
                    // Insert Data in format_mooin4_chapter;
                    $db_chapter = $DB->get_records('format_mooin4_chapter', array(), 'sectionid', '*', IGNORE_MISSING);
                    foreach ($db_chapter as $key => $valuecheck) {
                        array_push($db_chapter_courseid,$valuecheck->courseid . $valuecheck->sectionid);
                    }
                   
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
                            'sectionText' => 'Lesson Title ' . $i,
                            'sectionUrl' => $sectionUrl. '#section=' . $k,
                            'chapterId' => $j
                        ]);
        
                        $db_section_sectionid = [];
                        $datasection = new  stdClass();
        
                        $datasection->id = $k;
                        $datasection->sectionid = $i;
                        $datasection->sectiondone = 0;
                        $datasection->sectiontext = 'Lesson Title ' . $i;
                        $datasection->sectionurl = $sectionUrl . '#section=' . $k;
                        $datasection->chapterid = $j;
                        $datasection->courseid = $courseid;
        
                        // Insert Data in format_mooin4_section;
                        // $db_section = $DB->get_records('format_mooin4_section', array(), 'id', '*', IGNORE_MISSING);
                        $sql_section = "SELECT * FROM mdl_format_mooin4_section fms WHERE fms.courseid = {$courseid}"; // ORDER BY fms.chapterid
                        $db_section = $DB->get_records_sql($sql_section);
                        if(empty($db_section)){
                            $DB->insert_record('format_mooin4_section', $datasection);
                        } else {
                            foreach ($db_section as $k => $v) {
                                array_push($db_section_sectionid, $v->courseid . $v->chapterid . $v->sectionid);
                            }
                            $valsection = $courseid . $j . $i;
                            
                            if(!in_array($valsection, $db_section_sectionid)){
                                // Check the right section_id in url
                                $DB->insert_record('format_mooin4_section', $datasection);
                                $allSects = $DB->get_records('format_mooin4_section', ['courseid' => $courseid], 'chapterid', '*', IGNORE_MISSING); // chapterid
                                $index = 0;
                                foreach ($allSects as $k => $v) {
                                    $index += 1;
                                    $sectionadd = new stdClass();
                                    $sectionadd ->id = $v->id;
                                    $sectionadd ->sectionid = $v -> sectionid;
                                    $sectionadd ->sectiondone =$v -> sectiondone;
                                    $sectionadd ->sectiontext = $v -> sectiontext;
                                    $sectionadd ->sectionurl = $sectionUrl . '#section=' . $index;
                                    $sectionadd ->chapterid = $v->chapterid;
                                    $sectionadd ->courseid = $courseid;
    
                                    // Update the format_mooin4_section table
                                    $DB->update_record('format_mooin4_section', $sectionadd, $bulk = false);
                                }
                            }
                        }
                    } 
                }
            }
            
        }    
    };
   
    // print_r($inhaltblock);
// Get the data from the DB Chapter and section

$db_chapter = "SELECT * FROM mdl_format_mooin4_chapter  fmc WHERE fmc.courseid = {$courseid} ORDER BY fmc.sectionid";
$sql_chapter = $DB->get_records_sql($db_chapter);
// $sql_chapter = $DB->get_records('format_mooin4_chapter', ['courseid'=>$courseid], 'id');

$db_section = "SELECT * FROM mdl_format_mooin4_section fms WHERE fms.courseid = {$courseid} ORDER BY fms.sectionid"; // sectionid
$sql_section = $DB->get_records_sql($db_section);


// echo"<pre>"; print_r($sql_section);

//sort the section Array base on the chapterid and sectionid
$result = json_decode(json_encode($sql_section), true);

function sortById($x, $y) {
    return $x['chapterid'] - $y['chapterid'];
}

usort($result, 'sortById');
//echo "<pre>"; print_r($result);

// End srting the receiving array section from the DB format_mooin4_section.

// Create the new array for Section list base on the number of section in each categorie
foreach ($arr_values as $key => $value) {
    $sectionblock = array_slice($result, 0, $value); // $check = $sql_section
    array_splice($result, 0, $value); // $check = $sql_section
    array_push($arr_result,$sectionblock );
}
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
    'updateurl' => new moodle_url('/course/format/mooin4/edit.php', array('id' => $userPreferencesEdit )),
    'myCondition' => $userrole,
    'editingOn' => $editing_on
];
// <input type="button" readonly class=" form-control-plaintext" id="staticSectionTitle" value= '{{sectiontext}}' style="cursor: pointer;"  onclick="location.href='{{sectionurl}}'">
// <a href='{{& sectionurl }}' class="sectionedit{{courseid}}{{chapterid}}{{sectionid}} editsection" id="sectiontext">{{ sectiontext }}</a>
echo $OUTPUT->render_from_template('format_mooin4/manage_inhalt', $templatecontext);

// Set preference $courseid to save the id 
$listOfPreferencesEdit = array('id' => $courseid, 'gotoedit'=> false);

set_user_preferences($listOfPreferencesEdit);
echo $OUTPUT->footer();