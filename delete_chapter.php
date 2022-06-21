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
 * Delete a Chapter inside a Course
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package format_mooin4
 * @author Nguefack Perial K
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

// Fetch the lib.php structur
$courseformat = course_get_format($course);
$course_new = $courseformat->get_course();

/* How we have to do
*   Delete the chapter with the right id with the Table format_mooin4_chapter
*   Update the structur lib.php ( Categorie text and section 4valuenumber).
*/

$courseId = $_POST['courseid'];
$chapterId = $_POST['chapterid'];
// Delete the right chapter
$DB->delete_records_select('format_mooin4_chapter', "(courseid = {$courseId} AND sectionid = {$chapterId})");

// Update the Table format_mooin4_chapter
$chapterData = $DB->get_records('format_mooin4_chapter', ['courseid' => $courseId], 'sectionid','*', IGNORE_MISSING);
foreach ($chapterData as $key => $value) {
    if($value->sectionid > intval($_POST['chapterid'])){

        $update_ch = new stdClass();

        $update_ch->id = $value->id;
        $update_ch->sectionid = $value->sectionid - 1;

        $DB->update_record('format_mooin4_chapter', $update_ch);
    }
}

// Update the format_mooin4_section also because we also have the chapterid(sectonid) there
$update_section = $DB->get_records('format_mooin4_section', ['courseid' => $_POST['courseid']], 'chapterid','*', IGNORE_MISSING);
foreach ($update_section as $k => $v) {
    if($v->chapterid > intval($_POST['chapterid'])){
        $update_sect = new stdClass();

        $update_sec->id = $v->id;
        $update_sec->chapterid = $v->chapterid - 1;
        
        $DB->update_record('format_mooin4_section', $update_sec);
    }
}
// Update the lib.php structur
// var_dump($course_new);

$d = 'numsections';
$arr_divisor = [];

foreach ($course_new as $key => $value) {
    if(strpos($key, 'divisor') === 0 ){
        //str_contains($key, 'divisor')
        
        if(( $value != '' && $value != 0 ) || ($value != '' && $value == 0)){ // find a better expression
           $arr_divisor[$key] = $value;
        }     
    }
}
echo('Array Divisor');
var_dump($arr_divisor);

for ($i=1; $i < count($arr_divisor) + 1; $i++) {
    $c = 'divisor' . strval($i);
    $e ='divisortext' . strval($i);

    $cc = 'divisor' . strval($i + 1);
    $ee ='divisortext' . strval($i + 1);
    if( $i <= count($arr_divisor) / 2) {
        if(($arr_divisor['divisor'. strval($i)] == 0) && $arr_divisor['divisortext' . strval($i)] != ''){ // && $arr_divisor[$i + 1 ]['divisor'] != 0

            $v = $i + 1;
            (array)$course_new->$c = $arr_divisor['divisor' . strval($v)];
            (array)$course_new->$e = $arr_divisor['divisortext' . strval($v)];
    
            $arr_divisor['divisor' . strval($i + 1)] = 0;
    
            // (array)$course_new->$cc = 0;
            // (array)$course_new->$ee = '';
            // $ee = 'divisortext' . $i = $arr_divisor[$i + 1]['divisortext'];
        }
        if($i == count($arr_divisor) / 2){
            (array)$course_new->$e = '';
            (array)$course_new->$c = 0;
        }
    }
}

echo('Array course new');
var_dump($course_new);
// update
$courseformat->update_course_format_options($course_new);