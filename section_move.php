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
 * Move Section in Chapters in Table Content
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package format_mooin4
 * @author Nguefack Kuaguim Perial Dupont
 */

use core_calendar\local\event\forms\update;

require_once('../../../config.php');
require_once($CFG->libdir.'/tablelib.php');
require_once($CFG->libdir.'/filelib.php');
require_once('../mooin4/lib.php');

$move        = optional_param('move', 0, PARAM_INT);

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
/* echo('Context');
var_dump($context); */
$PAGE->set_context($context); // \context_course::instance($course->id)
require_login();
$context = \context_course::instance($_POST['courseid']);
require_capability('moodle/course:movesections', $context);

echo('Current C ' . $_POST['current_chapter']);
echo('Current S ' . $_POST['current_section']);
echo('<br>');
echo('First C ' . $_POST['first_chapter']);
echo('First S ' . $_POST['first_section']);
echo('<br>');
echo('Last C ' . $_POST['last_chapter']);
echo('Last S ' . $_POST['last_section']);
echo('<br>');
echo('Direction ' . $_POST['direction']);
echo('Clicked chapter and Section' . $_POST['chapter_clicked'] . $_POST['section_clicked']);


// Make change only on the right chapter
// Mooin4 Chapter table has to be updated (sectionnumber)
$chapter_table = $DB->get_records('format_mooin4_chapter', ['courseid' => $_POST['courseid']],'sectionid', '*', IGNORE_MISSING);
// Add the case if the section move only appear in the same chapter ( to Add )
if(($_POST['direction'] == 'up') && (intval($_POST['first_chapter']) != intval($_POST['last_chapter']))) {
    // find the right chapter to increment the sectionnumber and to decrement
    foreach ($chapter_table as $key => $value) {
        if(intval($value->sectionid) === intval($_POST['current_chapter'])) {
            $update_chap_current = new stdClass();
            $update_chap_current->id = $value->id;
            $update_chap_current->sectionnumber = intval($value->sectionnumber) + 1;

            // Update the chapter Table
           $DB->update_record('format_mooin4_chapter', $update_chap_current);
        }
    }
    foreach ($chapter_table as $key => $value) {
        if(intval($value->sectionid) === intval($_POST['first_chapter'])) {
            $update_chap_last = new stdClass();
            $update_chap_last->id = $value->id;
            $update_chap_last->sectionnumber = $value->sectionnumber - 1;

            // Update the chapter Table
           $DB->update_record('format_mooin4_chapter', $update_chap_last);
        }
    }
}
if($_POST['direction'] === 'down' && intval($_POST['first_chapter']) != intval($_POST['last_chapter'])) {
    // find the right chapter to increment the sectionnumber and to decrement
    foreach ($chapter_table as $key => $value) {
        if(intval($value->sectionid) === intval($_POST['current_chapter'])) {
            $update_chap_current = new stdClass();
            $update_chap_current->id = $value->id;
            $update_chap_current->sectionnumber = intval($value->sectionnumber) + 1;

            // Update the chapter Table
            $DB->update_record('format_mooin4_chapter', $update_chap_current);
        }
    }
    foreach ($chapter_table as $key => $value) {
        if(intval($value->sectionid) === intval($_POST['last_chapter'])) {
            $update_chap_last = new stdClass();
            $update_chap_last->id = $value->id;
            $update_chap_last->sectionnumber = $value->sectionnumber - 1;

            // Update the chapter Table
            $DB->update_record('format_mooin4_chapter', $update_chap_last);
        }
    }
}

// Mooin4 Section Table has to be updated (chapterid, sectionid, sectionurl)

// Fetch the  mooin4_section data in DB
$section_table = $DB->get_records('format_mooin4_section', ['courseid' => $_POST['courseid']],'chapterid', '*', IGNORE_MISSING);
// some useful data
$count_elts = count($section_table);
$move_up = [];
$move_up_other_array = [];
$section_clicked = [];
$section_in_url = '';
if ($_POST['direction'] == 'up') {
    foreach ($section_table as $key => $value) {
        if((intval($value->chapterid) == $_POST['last_chapter'] && intval($value->sectionid) == $_POST['last_section'])) {
            $section_id = explode('=', $value->sectionurl);
            $section_in_url = $section_id[2];
            echo('Section in Url');
            echo($section_in_url);
        }
        if($_POST['first_chapter'] > $_POST['last_chapter']){ // && intval($value->sectionid) >= $_POST['last_section']
            if( (intval($value->chapterid) == $_POST['first_chapter']) || (intval($value->chapterid) >= $_POST['last_chapter']) &&
             ( (intval($value->chapterid) <= $_POST['first_chapter'] && (intval($value->sectionid) >= $_POST['first_section'] || intval($value->sectionid) <= $_POST['first_section'])) )){
                array_push($move_up, (object)[
                    'id' => $value->id,
                    'courseid' => $value->courseid,
                    'chapterid' => $value->chapterid,
                    'sectionid' => $value->sectionid,
                    'sectiontext' => $value->sectiontext,
                    'sectionurl' => $value->sectionurl,
                    'sectiondone' => $value->sectiondone
                ]);
            }  else {
                // This array containt the part before the click section to move
                array_push($move_up_other_array, (object)[
                    'id' => $value->id,
                    'courseid' => $value->courseid,
                    'chapterid' => $value->chapterid,
                    'sectionid' => $value->sectionid,
                    'sectiontext' => $value->sectiontext,
                    'sectionurl' => $value->sectionurl,
                    'sectiondone' => $value->sectiondone
    
                ]);
            }
        }
        if($_POST['first_chapter'] < $_POST['last_chapter']) { // && (intval($value->sectionid) >= $_POST['first_section'])
            if((intval($value->chapterid) == $_POST['last_chapter']) || (intval($value->chapterid) >= $_POST['first_chapter']) &&
            ( (intval($value->chapterid) <= $_POST['last_chapter'] && ( intval($value->sectionid) >= $_POST['last_section'] || intval($value->sectionid) <= $_POST['last_section'])))) {
               array_push($move_up, (object)[
                   'id' => $value->id,
                   'courseid' => $value->courseid,
                   'chapterid' => $value->chapterid,
                   'sectionid' => $value->sectionid,
                   'sectiontext' => $value->sectiontext,
                   'sectionurl' => $value->sectionurl,
                   'sectiondone' => $value->sectiondone
               ]);
           }  else {
               // This array containt the part before the click section to move
               array_push($move_up_other_array, (object)[
                   'id' => $value->id,
                   'courseid' => $value->courseid,
                   'chapterid' => $value->chapterid,
                   'sectionid' => $value->sectionid,
                   'sectiontext' => $value->sectiontext,
                   'sectionurl' => $value->sectionurl,
                   'sectiondone' => $value->sectiondone
   
               ]);
           }
        }
        if($_POST['first_chapter'] == $_POST['last_chapter']) {
            if(intval($value->chapterid) == $_POST['first_chapter']) {
                array_push($move_up, (object)[
                    'id' => $value->id,
                    'courseid' => $value->courseid,
                    'chapterid' => $value->chapterid,
                    'sectionid' => $value->sectionid,
                    'sectiontext' => $value->sectiontext,
                    'sectionurl' => $value->sectionurl,
                    'sectiondone' => $value->sectiondone
                ]);
            }  else {
                array_push($move_up_other_array, (object)[
                    'id' => $value->id,
                    'courseid' => $value->courseid,
                    'chapterid' => $value->chapterid,
                    'sectionid' => $value->sectionid,
                    'sectiontext' => $value->sectiontext,
                    'sectionurl' => $value->sectionurl,
                    'sectiondone' => $value->sectiondone

                ]);
            }
        }
        
    }
    /* echo('Array moveUp');
    echo'<pre>' . var_dump($move_up);

    echo('Array moveUpOtherArray');
    echo'<pre>' . var_dump($move_up_other_array); */
    
    // Make update in the move_up array
    
    foreach ($move_up as $k => $v) {
        
        
        if($_POST['first_chapter'] > $_POST['last_chapter']) {
            
            if(intval($v->chapterid) ==  $_POST['last_chapter']) {
                if (intval($v->sectionid) >= $_POST['last_section']) {
                    $v->sectionid = strval($v->sectionid + 1);

                    $sec_url = explode('=', $v->sectionurl);
                    $sec_id = intval($sec_url[2]) + 1;
                    $v->sectionurl = $sec_url[0] . '=' . $sec_url[1] . '=' . strval($sec_id);
                }
            }
            if(intval($v->chapterid) ==  $_POST['first_chapter']) {
                if (intval($v->sectionid) < $_POST['first_section']) {
                    
                    $sec_url = explode('=', $v->sectionurl);
                    $sec_id = intval($sec_url[2]) + 1;
                    $v->sectionurl = $sec_url[0] . '=' . $sec_url[1] . '=' . strval($sec_id);
                }
                if (intval($v->sectionid) == $_POST['first_section']) {
                    $v->chapterid = $_POST['last_chapter'];
                    $v->sectionid = strval($_POST['last_section']); // +1
                    $sec_url = explode('=', $v->sectionurl);
                    $sec_url[2] = $section_in_url;

                    $v->sectionurl = $sec_url[0] . '=' . $sec_url[1] . '=' . $sec_url[2];
                }
                if (intval($v->sectionid) > $_POST['first_section']) {
                    $v->sectionid = strval($v->sectionid - 1);

                }
                /* $sec_url = explode('=', $v->sectionurl);
                $sec_id = intval($sec_url[2]) + 1;
                $v->sectionurl = $sec_url[0] . '=' . $sec_url[1] . '=' . strval($sec_id);  */
            }
            if (intval($v->chapterid < $_POST['first_chapter']) && intval($v->chapterid) > $_POST['last_chapter']) {
                $sec_url = explode('=', $v->sectionurl);
                $sec_id = intval($sec_url[2]) + 1;
                $v->sectionurl = $sec_url[0] . '=' . $sec_url[1] . '=' . strval($sec_id);
            }
        }
        if($_POST['first_chapter'] < $_POST['last_chapter']) {
            if (intval($v->chapterid) == $_POST['last_chapter']) {
                
                if (intval($v->sectionid) >= $_POST['last_section']) {
                    $v->sectionid = strval($v->sectionid + 1);

                    /* $sec_url = explode('=', $v->sectionurl);
                    $sec_id = intval($sec_url[2]) + 1;
                    $v->sectionurl = $sec_url[0] . '=' . $sec_url[1] . '=' . strval($sec_id);  */
                }
                
                if (intval($v->sectionid) < $_POST['last_section']) {
                    $sec_url = explode('=', $v->sectionurl);
                    $sec_id = intval($sec_url[2]) + 1;
                    $v->sectionurl = $sec_url[0] . '=' . $sec_url[1] . '=' . strval($sec_id);
                }
            }
            if (intval($v->chapterid) == $_POST['first_chapter']) {
                if (intval($v->sectionid) == $_POST['first_section']) {
                    $v->chapterid = $_POST['last_chapter'];
                    $v->sectionid = strval($_POST['last_section']);
                    $sec_url = explode('=', $v->sectionurl);

                    $sec_url[2] = $section_in_url - 1;
                    $v->sectionurl = $sec_url[0] . '=' . $sec_url[1] . '=' . $sec_url[2];
                }
                if (intval($v->sectionid) > $_POST['first_section']) {
                    $v->sectionid = strval($v->sectionid - 1);

                    $sec_url = explode('=', $v->sectionurl);
                    $sec_id = intval($sec_url[2]) - 1;
                    $v->sectionurl = $sec_url[0] . '=' . $sec_url[1] . '=' . strval($sec_id); 
                }
                
                
            }            
            if (intval($v->chapterid > $_POST['first_chapter']) && intval($v->chapterid) < $_POST['last_chapter']) {
                $sec_url = explode('=', $v->sectionurl);
                $sec_id = intval($sec_url[2]) - 1;
                $v->sectionurl = $sec_url[0] . '=' . $sec_url[1] . '=' . strval($sec_id);
            }
        }
        if($_POST['last_chapter'] == $_POST['first_chapter']) {
            if(intval($v->sectionid) >= $_POST['last_section'] && intval($v->sectionid) < $_POST['first_section']) {
                $v->sectionid = strval($v->sectionid + 1);

                $sec_url = explode('=', $v->sectionurl);
                $sec_id = intval($sec_url[2]) + 1;
                $v->sectionurl = $sec_url[0] . '=' . $sec_url[1] . '=' . strval($sec_id);
            } else {
                $v->sectionid = strval($_POST['last_section']);
                $sec_url = explode('=', $v->sectionurl);
                $sec_url[2] = $section_in_url ;
                $v->sectionurl = $sec_url[0] . '=' . $sec_url[1] . '=' . $sec_url[2];  
            }
            
        }
    }
    // merge the two array
    $arr_merge = array_merge($move_up_other_array, $move_up);
    //echo('Array to merge');
    // echo'<pre>' . var_dump($arr_merge);
    
    // Update the data in DB mooin4_section loop throught the new Array associative and make the update correspondently.
    foreach ($arr_merge as $key => $value) {
        $update_section = new stdClass();

        $update_section->id = $value->id;
        $update_section->chapterid = $value->chapterid;
        $update_section->sectionid = $value->sectionid;
        $update_section-> sectionurl = $value->sectionurl;

        $DB->update_record('format_mooin4_section', $update_section);
    }
    
}
if($_POST['direction'] == 'down') {
    foreach ($section_table as $key => $value) {
        if((intval($value->chapterid) == $_POST['first_chapter'] && intval($value->sectionid) == $_POST['first_section'])) {
            $section_id = explode('=', $value->sectionurl);
            $section_in_url = $section_id[2];

            echo('<br> ' . ' Section in url');
            echo($section_in_url);
        }
        if($_POST['last_chapter'] > $_POST['first_chapter']){
            if((intval($value->chapterid) == $_POST['last_chapter']) || ((intval($value->chapterid) == $_POST['first_chapter'] && intval($value->sectionid) > $_POST['first_section']) || 
                (intval($value->chapterid) <= $_POST['last_chapter'] && (intval($value->sectionid) >= $_POST['last_section'] || intval($value->sectionid) <= $_POST['last_section']))) &&
             ( (intval($value->chapterid) >= $_POST['first_chapter'] )) ){
                array_push($move_up, (object)[
                    'id' => $value->id,
                    'courseid' => $value->courseid,
                    'chapterid' => $value->chapterid,
                    'sectionid' => $value->sectionid,
                    'sectiontext' => $value->sectiontext,
                    'sectionurl' => $value->sectionurl,
                    'sectiondone' => $value->sectiondone
                ]);
            }  else {
                // This array containt the part before the click section to move
                array_push($move_up_other_array, (object)[
                    'id' => $value->id,
                    'courseid' => $value->courseid,
                    'chapterid' => $value->chapterid,
                    'sectionid' => $value->sectionid,
                    'sectiontext' => $value->sectiontext,
                    'sectionurl' => $value->sectionurl,
                    'sectiondone' => $value->sectiondone
    
                ]);
            }
        }
        if($_POST['last_chapter'] < $_POST['first_chapter']) {
            if( (intval($value->chapterid) == $_POST['first_chapter'])|| (intval($value->chapterid) >= $_POST['last_chapter'] && intval($value->sectionid) >= $_POST['last_section'] ) &&
                ( (intval($value->chapterid) <= $_POST['first_chapter'] && ( intval($value->sectionid) >= $_POST['first_section'] || intval($value->sectionid) <= $_POST['first_section'])))) {
               array_push($move_up, (object)[
                   'id' => $value->id,
                   'courseid' => $value->courseid,
                   'chapterid' => $value->chapterid,
                   'sectionid' => $value->sectionid,
                   'sectiontext' => $value->sectiontext,
                   'sectionurl' => $value->sectionurl,
                   'sectiondone' => $value->sectiondone
               ]);
           }  else {
               // This array containt the part before the click section to move
               array_push($move_up_other_array, (object)[
                   'id' => $value->id,
                   'courseid' => $value->courseid,
                   'chapterid' => $value->chapterid,
                   'sectionid' => $value->sectionid,
                   'sectiontext' => $value->sectiontext,
                   'sectionurl' => $value->sectionurl,
                   'sectiondone' => $value->sectiondone
   
               ]);
           }
        }
        if($_POST['last_chapter'] == $_POST['first_chapter']) {
            if(intval($value->chapterid) == $_POST['first_chapter']) {
                array_push($move_up, (object)[
                    'id' => $value->id,
                    'courseid' => $value->courseid,
                    'chapterid' => $value->chapterid,
                    'sectionid' => $value->sectionid,
                    'sectiontext' => $value->sectiontext,
                    'sectionurl' => $value->sectionurl,
                    'sectiondone' => $value->sectiondone
                ]);
            }  else {
                array_push($move_up_other_array, (object)[
                    'id' => $value->id,
                    'courseid' => $value->courseid,
                    'chapterid' => $value->chapterid,
                    'sectionid' => $value->sectionid,
                    'sectiontext' => $value->sectiontext,
                    'sectionurl' => $value->sectionurl,
                    'sectiondone' => $value->sectiondone

                ]);
            }
        }

    }
    
    echo('Array to Move Up');
    echo'<pre>' . var_dump($move_up);

    echo('Array to Move Up Other Array');
    echo'<pre>' . var_dump($move_up_other_array);
    // Make update in the move_up array
    
    foreach ($move_up as $k => $v) {
        if($_POST['first_chapter'] > $_POST['last_chapter']){
            if (intval($v->chapterid) == $_POST['first_chapter']) {
                if(intval($v->sectionid) > $_POST['first_section']){
                    $v->sectionid = strval($v->sectionid + 1);

                    /* $sec_url = explode('=', $v->sectionurl);
                    $sec_id = intval($sec_url[2]) + 1;
                    $v->sectionurl = $sec_url[0] . '=' . $sec_url[1] . '=' . strval($sec_id); */
                }else { // intval($v->sectionid) <= $_POST['first_section']
                    $sec_url = explode('=', $v->sectionurl);
                    $sec_id = intval($sec_url[2]) - 1;
                    $v->sectionurl = $sec_url[0] . '=' . $sec_url[1] . '=' . strval($sec_id);
                }
            }
            if (intval($v->chapterid) == $_POST['last_chapter']) {
                if(intval($v->sectionid) == $_POST['last_section']){
                    $v->chapterid = $_POST['first_chapter'];
                    $v->sectionid = strval($_POST['first_section'] + 1); // +1 was not there
                    $sec_url = explode('=', $v->sectionurl);
                    $sec_url[2] = $section_in_url;

                    $v->sectionurl = $sec_url[0] . '=' . $sec_url[1] . '=' . $sec_url[2];
                } else  if(intval($v->sectionid) > $_POST['last_section']) {
                    $v->sectionid = strval($v->sectionid - 1);

                    $sec_url = explode('=', $v->sectionurl);
                    $sec_id = intval($sec_url[2]) - 1;
                    $v->sectionurl = $sec_url[0] . '=' . $sec_url[1] . '=' . strval($sec_id);
                }
                
                
                if(intval($v->chapterid) > $_POST['last_chapter'] && intval($v->chapterid) < $_POST['first_chapter']) {
                   /*  $sec_url = explode('=', $v->sectionurl);
                    $sec_id = intval($sec_url[2]) - 1;
                    $v->sectionurl = $sec_url[0] . '=' . $sec_url[1] . '=' . strval($sec_id); */
                }
            }
            
            if ( intval($v->chapterid) > $_POST['last_chapter'] && intval($v->chapterid < $_POST['first_chapter'])) {
                $sec_url = explode('=', $v->sectionurl);
                $sec_id = intval($sec_url[2]) - 1;
                $v->sectionurl = $sec_url[0] . '=' . $sec_url[1] . '=' . strval($sec_id);
            }
        }
        if($_POST['first_chapter'] < $_POST['last_chapter']) {  
            if (intval($v->chapterid) == $_POST['first_chapter']) {
                
                if(intval($v->sectionid) > $_POST['first_section']) {
                    $v->sectionid = strval(intval($v->sectionid) + 1);

                    $sec_url = explode('=', $v->sectionurl);
                    $sec_id = intval($sec_url[2]) + 1;
                    $v->sectionurl = $sec_url[0] . '=' . $sec_url[1] . '=' . strval($sec_id);
                }
                
            } 

            if (intval($v->chapterid) == $_POST['last_chapter']) {
                
                if(intval($v->sectionid) == $_POST['last_section']){
                    $v->chapterid = $_POST['first_chapter'];
                    $v->sectionid = intval($_POST['first_section']) + 2;
                    $sec_url = explode('=', $v->sectionurl);
                    $sec_url[2] = $section_in_url + 1;

                    $v->sectionurl = $sec_url[0] . '=' . $sec_url[1] . '=' . $sec_url[2];
                }
                if(intval($v->sectionid) > $_POST['last_section']) {
                    $v->sectionid = strval($v->sectionid - 1);
    
                }
                if(intval($v->sectionid) < $_POST['last_section']) {
                    $sec_url = explode('=', $v->sectionurl);
                    $sec_id = intval($sec_url[2]) + 1;

                    $v->sectionurl = $sec_url[0] . '=' . $sec_url[1] . '=' . $sec_id;
    
                }
                
                
                
            }
                    
            if ( intval($v->chapterid) > $_POST['first_chapter'] && intval($v->chapterid < $_POST['last_chapter'])) {
                $sec_url = explode('=', $v->sectionurl);
                $sec_id = intval($sec_url[2]) + 1;
                $v->sectionurl = $sec_url[0] . '=' . $sec_url[1] . '=' . strval($sec_id);
            }
            
        }
        if ($_POST['last_chapter'] == $_POST['first_chapter']) {
            if(intval($v->sectionid) > $_POST['last_section'] && intval($v->sectionid) <= $_POST['first_section']) {
                $v->sectionid = strval($v->sectionid - 1);

                $sec_url = explode('=', $v->sectionurl);
                $sec_id = intval($sec_url[2]) - 1;
                $v->sectionurl = $sec_url[0] . '=' . $sec_url[1] . '=' . strval($sec_id);
            } else {
                $v->sectionid = strval($_POST['first_section']);
                $sec_url = explode('=', $v->sectionurl);
                $sec_url[2] = $section_in_url ;
                $v->sectionurl = $sec_url[0] . '=' . $sec_url[1] . '=' . $sec_url[2];  
            }
        }
        
    }

    
    // merge the two array
    $arr_merge = array_merge($move_up_other_array, $move_up);
    // echo('Array to merge');
    echo'<pre>' . var_dump($arr_merge);
    
    // Update the data in DB mooin4_section loop throught the new Array associative and make the update correspondently.
    foreach ($arr_merge as $key => $value) {
        $update_section = new stdClass();

        $update_section->id = $value->id;
        $update_section->chapterid = $value->chapterid;
        $update_section->sectionid = $value->sectionid;
        $update_section-> sectionurl = $value->sectionurl;

        $DB->update_record('format_mooin4_section', $update_section);
    }
}

// Lib.php have to be update ( sectionnumber increment or decrement)
// Fetch the lib.php structur
$courseformat = course_get_format($course);
$course_new = $courseformat->get_course();

// Increment & decrement the section number in the right chapter.
if(intval($_POST['first_chapter']) != intval($_POST['last_chapter']) ) {
       // Update order for the swap chapter and section number in chapter
       $arr = [];

       foreach ($course_new as $key => $value) {
           if(strpos($key, 'divisor') === 0 ){
               if($value >= 0 && $value != ''){
                  $arr[$key] = $value;
               }     
           }
       }
       if($_POST['direction'] == 'up') {
            for ($i=0; $i < count($arr)/2 + 1; $i++) {
        
                $divisor = '';
                $divisortext = '';
                if( $i == intval($_POST['first_chapter'])){
                    $c = 'divisor' . strval($i);
                    (array)$course_new->$c -= 1;
                }
                if( $i == intval($_POST['last_chapter'])) {
                    $c = 'divisor' . strval($i);
                    (array)$course_new->$c += 1;
                }
            }
            
           $courseformat->update_course_format_options($course_new);
        }
        if($_POST['direction'] == 'down') {
            for ($i=0; $i < count($arr)/2 + 1; $i++) {
           
                $divisor = '';
                $divisortext = '';
                if( $i == intval($_POST['first_chapter'])){
                    $c = 'divisor' . strval($i);
                    (array)$course_new->$c += 1;
                }
                if( $i == intval($_POST['last_chapter'])) {
                    $c = 'divisor' . strval($i);
                    (array)$course_new->$c -= 1;
                }
            }
            
           $courseformat->update_course_format_options($course_new);
        }
}

// Course Sections Table Update ( section )
$clicked_section = ' ';
$new_section_id = '';
$clicked_section_data = [];

$course_sections_table = $DB->get_records_sql(
    "SELECT s.* FROM {course_sections} s WHERE s.course = :courseid AND s.section != 0  ORDER BY section",
    array('courseid' => $_POST['courseid'])
);

// Implematation of a new way to update the course_sections table
$count = $DB->count_records('course_sections', array('course' => $_POST['courseid']));

// get the clicked section to move
// find the clicked chapter and section
if($_POST['direction'] == 'up'){
    // Here we should know which one is the clicked_section and the destination section

    foreach ($section_table as $key => $value) {
        // find the section_id in the url
        if((intval($value->chapterid) == $_POST['first_chapter'] && intval($value->sectionid) == $_POST['first_section'])) {
            $section_id = explode('=', $value->sectionurl);
            $clicked_section = $section_id[2];
        }
        if((intval($value->chapterid) == $_POST['last_chapter'] && intval($value->sectionid) == $_POST['last_section'])) {
            $section_id = explode('=', $value->sectionurl);
            $new_section_id = $section_id[2];
        }
    }
    
    // work for course_sections table 
    $section = $clicked_section;
    $destsection = $new_section_id;
    move_section_to($course, $section,$destsection);

    $response = course_get_format($course)->ajax_section_move();
     echo("Response");
    var_dump($response);
    if ($response !== null) {
        echo json_encode($response);
    }
   
}
if($_POST['direction'] == 'down') {
    // Here we should know which one is the clicked_section and the destination section  
    
    // clicked section == last
    foreach ($section_table as $key => $value) {
        // find the section_id in the url
        if((intval($value->chapterid) == $_POST['first_chapter'] && intval($value->sectionid) == $_POST['first_section'])) {
            $section_id = explode('=', $value->sectionurl);
            $new_section_id = $section_id[2];
        }
        if((intval($value->chapterid) == $_POST['last_chapter'] && intval($value->sectionid) == $_POST['last_section'])) {
            $section_id = explode('=', $value->sectionurl);
            $clicked_section = $section_id[2];
        }
    }
    /* echo('Clicked : ' . $clicked_section);
    echo('New Section : ' .  $new_section_id); */

    // work for course_sections table 
    $section = $clicked_section;

    $destsection = $new_section_id;

    if($destsection < $count - 1) {
        $destsection = $new_section_id + 1;
    } else {
        $destsection = $new_section_id;
    }

    echo('Section & '  . ' Destsection');
    echo( $section . ' & ' . $destsection);
    move_section_to($course, $section,$destsection);

    $response = course_get_format($course)->ajax_section_move();
    echo("Response");
    var_dump($response); 
    if ($response !== null) {
        echo json_encode($response);
    }
    
}