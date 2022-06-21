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
 * Move Chapter in Table Content
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package format_mooin4
 * @author Nguefack Kuaguim Perial Dupont
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

// Make all the DB call that we'll need to implement our tasks
$data_mooin4_chapter = $DB->get_records('format_mooin4_chapter', ['courseid' => $_POST['courseid']],'sectionid', '*', IGNORE_MISSING);
$data_mooin4_all_section = $DB->get_records('format_mooin4_section', ['courseid' => $_POST['courseid']],'chapterid', '*', IGNORE_MISSING);

// $data_section = $DB->get_records('course_sections', ['course' => $_POST['courseid']],'section', '*', IGNORE_MISSING);

// Get the current chapter we want to swap
$sql_cur_chap = "SELECT * FROM mdl_format_mooin4_chapter fmc WHERE fmc.courseid = {$_POST['courseid']} AND  fmc.sectionid = {$_POST['sectionid']}";
$data_cur_chap = $DB->get_records_sql($sql_cur_chap);
// All sections in current chapter
// $sql_all_sec_in_cur_ch = "SELECT * FROM mdl_format_mooin4_section fms WHERE fms.courseid = {$_POST['courseid']} AND  fms.chapterid = {$_POST['sectionid']}";
// $data_ch_cur_sections = $DB->get_records_sql($sql_all_sec_in_cur_ch);

$arr_to_swap = [];
$arr_sections_in_chapter = [];

// Direction Up (Move up)
if($_POST['sectionid'] > 1 && $_POST['direction'] == 'up'){
    // Get the previous data in chapter table
    $prev_chapter_id = $_POST['sectionid'] - 1;
    $sql_chapter_up = "SELECT * FROM mdl_format_mooin4_chapter fmc WHERE fmc.courseid = {$_POST['courseid']} AND fmc.sectionid = {$prev_chapter_id}";
    $data_chapter_up = $DB->get_records_sql($sql_chapter_up);

    // Get all the sections with the right chapterid in table format_mooin4_section for previous chapter to make the swap easily.
    // All Previous Sections in previous chapter
    $sql_all_sec_in_prev_ch = "SELECT * FROM mdl_format_mooin4_section fms WHERE fms.courseid = {$_POST['courseid']} AND  fms.chapterid = {$prev_chapter_id}";
    $data_ch_up_prev_sections = $DB->get_records_sql($sql_all_sec_in_prev_ch);

    // Build a new array base on the data that come from format_mooin4_chapter
    foreach ($data_mooin4_chapter as $k => $v) {
       array_push($arr_to_swap, $v);
    }
    // start the array with index 1 instead of 0
    array_unshift($arr_to_swap,"");
    unset($arr_to_swap[0]);
  
    for ($i=1; $i < count($arr_to_swap) +1; $i++) { 
        if($i == $_POST['sectionid']){
            $arr_to_swap[$i -1] ->sectionid += 1; 
            $arr_to_swap[$i] -> sectionid -= 1;

            $arr_to_swap[$i - 1]->sectionid = strval($arr_to_swap[$i - 1]->sectionid);
            $arr_to_swap[$i]->sectionid = strval($arr_to_swap[$i]->sectionid);
        }
    }

   // update the format_mooin4_chapter Table
   $extra_mooin4_chapter = $DB->get_records('format_mooin4_chapter', ['courseid' => $_POST['courseid']],'sectionid', '*', IGNORE_MISSING);
    foreach ($extra_mooin4_chapter as $key => $value) {
        foreach ($arr_to_swap as $key => $val) {
            if(intval($value->id) === intval($val->id)){
                if(intval($value->sectionid) != intval($val->sectionid)){
                    // implement update in mooin4_chapter
                    $up_chapter = new stdClass();
                    $up_chapter->id = $value->id;
                    $up_chapter->sectionid = $val->sectionid;

                    $DB->update_record('format_mooin4_chapter', $up_chapter);
                }
            }
        }
    }
   // Change in format_mooin4_section

   $arr_sections_in_prev_chapter = [];
   $chapter_id_prev_chap = 0;
   $chapter_id_cur_chap = 0;
   $section_id_previous = [];
   $section_id_current = [];
   $extra_data_mooin4_all_section = $DB->get_records('format_mooin4_section', ['courseid' => $_POST['courseid']],'chapterid', '*', IGNORE_MISSING);
    foreach ($extra_data_mooin4_all_section as $k => $v) {
        // put all the section with the same chapterid in an array
        if($v->chapterid == $prev_chapter_id) {
            array_push($arr_sections_in_prev_chapter, $v);
            $chapter_id_prev_chap = $v->chapterid;
        };
        if($v->chapterid == $_POST['sectionid']){
            array_push($arr_sections_in_chapter, $v);
            $chapter_id_cur_chap = $v->chapterid;
        }

        /* foreach ($extra_mooin4_chapter as $key => $value) {
            if(intval($value->sectionid) == $v->chapterid) {
                $number_sec_prev_chapter = intval($value->sectionid);
                $number_sec_cur_chapter = intval($value->sectionid);
            }
        } */
    }
   // update chapterid and url
   // Previous Chapter Content
   $number_sec_prev_chapter = count($arr_sections_in_prev_chapter);
   $number_sec_cur_chapter = count($arr_sections_in_chapter);
   
   for ($i=0; $i < $number_sec_prev_chapter; $i++) { 
        $arr_sections_in_prev_chapter[$i]->chapterid += 1;
        $arr_sections_in_prev_chapter[$i]->chapterid = strval($arr_sections_in_prev_chapter[$i]->chapterid);
        // Split the url to get the section id
        $sec_url = explode( "=", $arr_sections_in_prev_chapter[$i]->sectionurl);

        array_push($section_id_previous, intval($sec_url[2]));
        
        $sec_id = intval($sec_url[2]) + $number_sec_cur_chapter;
        $arr_sections_in_prev_chapter[$i] -> sectionurl = $sec_url[0] . '=' . $sec_url[1] . '=' . strval($sec_id);
   }
   
   for ($j=0; $j < $number_sec_cur_chapter; $j++) { 
        $arr_sections_in_chapter[$j]->chapterid -= 1;
        $arr_sections_in_chapter[$j]->chapterid = strval($arr_sections_in_chapter[$j]->chapterid);
        // Split the url to get the section id
        $sec_url = explode( "=", $arr_sections_in_chapter[$j]->sectionurl);

        array_push($section_id_current, intval($sec_url[2]));

        $sec_id = intval($sec_url[2]) - $number_sec_prev_chapter;
        $arr_sections_in_chapter[$j] -> sectionurl = $sec_url[0] . '=' . $sec_url[1] . '=' . strval($sec_id);
   }
   
  
   foreach ($extra_data_mooin4_all_section as $key => $value) {
       foreach ($arr_sections_in_prev_chapter as $k => $v) {
        if(intval($value->id) == intval($v->id)){
            $up_sec_in_prev_chap = new stdClass();

            $up_sec_in_prev_chap->id = $value->id;
            $up_sec_in_prev_chap->chapterid = $v->chapterid;
            $up_sec_in_prev_chap->sectionurl = $v->sectionurl;

            $DB->update_record('format_mooin4_section', $up_sec_in_prev_chap);
        }
       }
       foreach ($arr_sections_in_chapter as $k => $val) {
           if(intval($value->id) == intval($val->id)) {
            $up_sec_in_cur_chap = new stdClass();

            $up_sec_in_cur_chap->id = $value->id;
            $up_sec_in_cur_chap->chapterid = $val->chapterid;
            $up_sec_in_cur_chap->sectionurl = $val->sectionurl;

            $DB->update_record('format_mooin4_section', $up_sec_in_cur_chap);
           }
       }
   }
   // lib.php
    $courseformat = course_get_format($course);
    $course_new = $courseformat->get_course();
   // Update order for the swap chapter and section number in chapter
    $arr = [];

    foreach ($course_new as $key => $value) {
        if(strpos($key, 'divisor') === 0 ){
            if($value >= 0 && $value != ''){
               $arr[$key] = $value;
            }     
        }
    }
    for ($i=0; $i < count($arr)/2 + 1; $i++) {
        // previous in loop
       
        //$swap_arr = [];
        $divisor = '';
        $divisortext = '';
        if(($i > 1) && $i == intval($_POST['sectionid'])){
            
            $a = $i - 1;
            $pc = 'divisor' . strval($a);
            $pe = 'divisortext' . strval($a);
            // current in loop
            $c = 'divisor' . strval($i);
            $e = 'divisortext' . strval($i);

            $divisor = $arr['divisor' . strval($a)];
            $divisortext = $arr['divisortext' . strval($a)];

           (array)$course_new->$pc = $arr['divisor' . strval($i)];
           (array)$course_new->$pe = $arr['divisortext' . strval($i)];

           (array)$course_new->$c = $divisor;
           (array)$course_new->$e = $divisortext;
            
           $courseformat->update_course_format_options($course_new);
        }
    }
   // Change in course_sections
   // Get all the sections in a course except the initial one (0)
    $sql_section = "SELECT * FROM mdl_course_sections cs WHERE cs.course = {$_POST['courseid']} AND cs.section != 0 ORDER BY cs.section";
    $data_section = $DB->get_records_sql($sql_section);
    
    $update_sections_in_cs = [];
    foreach ($data_section as $key => $val) {
        if(in_array(intval($val->section), $section_id_previous)) {

            array_push($update_sections_in_cs, (object)[

                'id' => $val->id,
                'course' => $val->course,
                'section' => $val->section + count($section_id_current),
                'name' => $val->name,
                'summary' => $val->summary,
                'summaryformat' => $val->summaryformat,
                'sequence' => $val->sequence,
                'visible' => $val->visible,
                'availability' => $val->availability,
                'timemodified' => $val->timemodified,
            ]);
            
            // Delete all the previous elements in table course_sections to be able to make a simple insertion of the new elements.
            $DB->delete_records('course_sections', ['course' =>$val->course, 'section' =>$val->section]);
        } 
        if(in_array(intval($val->section), $section_id_current)) {

            array_push($update_sections_in_cs, (object)[

                'id' => $val->id,
                'course' => $val->course,
                'section' => $val->section - count($section_id_previous),
                'name' => $val->name,
                'summary' => $val->summary,
                'summaryformat' => $val->summaryformat,
                'sequence' => $val->sequence,
                'visible' => $val->visible,
                'availability' => $val->availability,
                'timemodified' => $val->timemodified,
            ]);

            $DB->delete_records('course_sections', ['course' =>$val->course, 'section' =>$val->section]);
        }
   }
   // Insert the new update elements in Table course_sections from the $update_sections_in_cs
    
    $DB->insert_records('course_sections', $update_sections_in_cs);

   //Purge all cache to directly see the changes occur in frontend.
    rebuild_course_cache($val->course, true);
}

// Direction Down (Move Down)
if($_POST['direction'] == 'down' && $_POST['sectionid'] < count($data_mooin4_chapter)){
    // Get the next data in chapter table
    $next_chapter_id = $_POST['sectionid'] + 1;
    $sql_chapter_down = "SELECT * FROM mdl_format_mooin4_chapter fmc WHERE fmc.courseid = {$_POST['courseid']} AND fmc.sectionid = {$next_chapter_id}";
    $data_chapter_down = $DB->get_records_sql($sql_chapter_down);
    
    // Get all the sections with the right chapterid in table format_mooin4_section for next chapter.
    // $sql_all_sec_in_next_ch = "SELECT * FROM mdl_format_mooin4_section fms WHERE fms.courseid = {$_POST['courseid']} AND  fms.chapterid = {$next_chapter_id}";
    // $data_ch_down_next_sections = $DB->get_records_sql($sql_all_sec_in_next_ch);

     // Build a new array base on the data that come from format_mooin4_chapter
     foreach ($data_mooin4_chapter as $k => $v) {
        array_push($arr_to_swap, $v);
     }
     // start the array with index 1 instead of 0
     array_unshift($arr_to_swap,"");
     unset($arr_to_swap[0]);

     for ($i=1; $i < count($arr_to_swap) +1; $i++) { 
         if($i == $_POST['sectionid']){
             $arr_to_swap[$i] ->sectionid += 1; 
             $arr_to_swap[$i + 1] -> sectionid -= 1;

            $arr_to_swap[$i]->sectionid = strval($arr_to_swap[$i]->sectionid);
            $arr_to_swap[$i +1]->sectionid = strval($arr_to_swap[$i + 1]->sectionid);
         }
     }
    // update the format_mooin4_chapter Table
   $extra_mooin4_chapter = $DB->get_records('format_mooin4_chapter', ['courseid' => $_POST['courseid']],'sectionid', '*', IGNORE_MISSING);
   foreach ($extra_mooin4_chapter as $key => $value) {
       foreach ($arr_to_swap as $key => $val) {
           if(intval($value->id) === intval($val->id)){
               if(intval($value->sectionid) != intval($val->sectionid)){
                   // implement update in mooin4_chapter
                   $up_chapter = new stdClass();
                   $up_chapter->id = $value->id;
                   $up_chapter->sectionid = $val->sectionid;

                   $DB->update_record('format_mooin4_chapter', $up_chapter);
               }
           }
       }
   }

   // change in format_mooin4_section
   $arr_sections_in_next_chapter = [];
   $section_id_next = [];
   $section_id_current = [];
   $extra_data_mooin4_all_section = $DB->get_records('format_mooin4_section', ['courseid' => $_POST['courseid']],'chapterid', '*', IGNORE_MISSING);
   foreach ($extra_data_mooin4_all_section as $k => $v) {
        // put all the section with the same chapterid in an array
        if($v->chapterid == $next_chapter_id) {
            array_push($arr_sections_in_next_chapter, $v);
        };
        if($v->chapterid == $_POST['sectionid']){
            array_push($arr_sections_in_chapter, $v);
        }
   }
   // Update chapterid and url
   // Next Chapter Content
   $number_sec_next_chapter = count($arr_sections_in_next_chapter);
   $number_sec_cur_chapter = count($arr_sections_in_chapter);
   for ($i=0; $i < $number_sec_next_chapter; $i++) { 
        $arr_sections_in_next_chapter[$i]->chapterid -= 1;
        $arr_sections_in_next_chapter[$i]->chapterid = strval($arr_sections_in_next_chapter[$i]->chapterid);
        // Split the url to get the section id
        $sec_url = explode( "=", $arr_sections_in_next_chapter[$i]->sectionurl);
        array_push($section_id_next, intval($sec_url[2]));

        $sec_id = intval($sec_url[2]) - $number_sec_cur_chapter;
        $arr_sections_in_next_chapter[$i] -> sectionurl = $sec_url[0] .'='. $sec_url[1] . '=' . strval($sec_id);
   }

   // Current Chapter content
   for ($j=0; $j < $number_sec_cur_chapter; $j++) { 
        $arr_sections_in_chapter[$j]->chapterid += 1;
        $arr_sections_in_chapter[$j]->chapterid = strval($arr_sections_in_chapter[$j]->chapterid);
        // Split the url to get the section id
        $sec_url = explode( "=", $arr_sections_in_chapter[$j]->sectionurl);
        array_push($section_id_current, intval($sec_url[2]));

        $sec_id = intval($sec_url[2]) + count($arr_sections_in_next_chapter);
        $arr_sections_in_chapter[$j] -> sectionurl = $sec_url[0] .'='. $sec_url[1] . '=' . strval($sec_id);
   }
   
   foreach ($extra_data_mooin4_all_section as $key => $value) {
       foreach ($arr_sections_in_next_chapter as $k => $v) {
        if(intval($value->id) == intval($v->id)){
            $up_sec_in_next_chap = new stdClass();

            $up_sec_in_next_chap->id = $value->id;
            $up_sec_in_next_chap->chapterid = $v->chapterid;
            $up_sec_in_next_chap->sectionurl = $v->sectionurl;

            $DB->update_record('format_mooin4_section', $up_sec_in_next_chap);
        }
       }
       foreach ($arr_sections_in_chapter as $k => $val) {
           if(intval($value->id) == intval($val->id)) {
            $up_sec_in_cur_chap = new stdClass();

            $up_sec_in_cur_chap->id = $value->id;
            $up_sec_in_cur_chap->chapterid = $val->chapterid;
            $up_sec_in_cur_chap->sectionurl = $val->sectionurl;

            $DB->update_record('format_mooin4_section', $up_sec_in_cur_chap);
           }
       }
   }
   // lib.php
    // Update order for the swap chapter and section number in chapter
    $courseformat = course_get_format($course);
    $course_new = $courseformat->get_course();
    // Update order for the swap chapter and section number in chapter
    $arr = [];
  
      foreach ($course_new as $key => $value) {
          if(strpos($key, 'divisor') === 0 ){
              if($value >= 0 && $value != ''){
                 $arr[$key] = $value;
              }     
          }
      }
      for ($i=0; $i < count($arr)/2 + 1; $i++) {
          
            $divisor = '';
            $divisortext = '';
            
          if(($i < count($arr) / 2) && $i == intval($_POST['sectionid'])){
            $b = $i + 1;
            // next in loop
            $nc = 'divisor' . strval($b);
            $ne = 'divisortext' . strval($b);
            // current in loop
            $c = 'divisor' . strval($i);
            $e = 'divisortext' . strval($i);
             
            $divisor = $arr['divisor' . strval($i)];
            $divisortext = $arr['divisortext' . strval($i)];

            (array)$course_new->$c = $arr['divisor' . strval($b)];;
            (array)$course_new->$e = $arr['divisortext' . strval($b)];;
  
            (array)$course_new->$nc = $divisor;
            (array)$course_new->$ne = $divisortext;
  
            $courseformat->update_course_format_options($course_new);
          }
      }
   // Change in course_sections
    // Get all the sections in a course except the initial one (0)
    $sql_section = "SELECT * FROM mdl_course_sections cs WHERE cs.course = {$_POST['courseid']} AND cs.section != 0 ORDER BY cs.section";
    $data_section = $DB->get_records_sql($sql_section);
    
    // Build the array of section in previous and current chapter.
    $update_sections_down = [];
    foreach ($data_section as $key => $value) {
        if(in_array(intval($value->section),$section_id_current)) {
            array_push($update_sections_down, (object)[
                'id' => $value->id,
                'course' => $value->course,
                'section' => $value->section + count($section_id_next),
                'name' => $value->name,
                'summary' => $value->summary,
                'summaryformat' => $value->summaryformat,
                'sequence' => $value->sequence,
                'visible' => $value->visible,
                'availability' => $value->availability,
                'timemodified' => $value->timemodified,
            ]);
            $DB->delete_records('course_sections', ['course' =>$value->course, 'section' =>$value->section]);
        }else if(in_array(intval($value->section),$section_id_next)) {
            array_push($update_sections_down, (object)[
                'id' => $value->id,
                'course' => $value->course,
                'section' => $value->section - count($section_id_current),
                'name' => $value->name,
                'summary' => $value->summary,
                'summaryformat' => $value->summaryformat,
                'sequence' => $value->sequence,
                'visible' => $value->visible,
                'availability' => $value->availability,
                'timemodified' => $value->timemodified,
            ]);
            $DB->delete_records('course_sections', ['course' =>$value->course, 'section' =>$value->section]);
        }
    }
    // Insert the new update elements in Table course_sections from the $update_sections_in_cs

    $DB->insert_records('course_sections', $update_sections_down);

   //Purge all cache to directly see the changes occur in frontend.
    rebuild_course_cache($value->course, true);
}