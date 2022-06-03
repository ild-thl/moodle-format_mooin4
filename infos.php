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
 * Displays external information about a course using the course format mooin4
 * @package    core_course implement in course format mooin4
 * @copyright  1999 onwards Martin Dougiamas  http://dougiamas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// use tool_brickfield\local\areas\core_course\shortname;

    require_once("../../../config.php");
    require_once("../lib.php");

    global $DB;
    global $PAGE;
    global $OUTPUT;
    global $USER;

    require_login();
    //require_login($course);
    if (isguestuser()){
        print_error('noguest');
    }
    $courseid = required_param('id', PARAM_INT); // Course id
    $name = optional_param('name', false, PARAM_RAW); // Course short name
    // $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);


    if (!$courseid and !$name) {
        print_error("unspecifycourseid");
    }

    if ($name) {
        if (!$course = $DB->get_record("course", array("shortname"=>$name))) {
            print_error("invalidshortname");
        }
    } else {
        if (!$course = $DB->get_record("course", array("id"=>$courseid))) {
            print_error("invalidcourseid");
        }
    }

    $site = get_site();

    if ($CFG->forcelogin) {
        require_login();
    }

    require_login($courseid, false);
    $context = context_course::instance($courseid);
    // require_capability('moodle/course:viewhiddenactivities', $context);

    if (!core_course_category::can_view_course_info($course) && !is_enrolled($context, null, '', true)) {
        print_error('cannotviewcategory', '', $CFG->wwwroot .'/');
    }

    $pageurl = new moodle_url('/course/format/mooin4/infos.php'.'?id='.$courseid);

    $PAGE->set_url($pageurl);
    $PAGE->set_course($course);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title(get_string("summaryof",'format_mooin4', $course->fullname));
    $PAGE->set_heading($course->fullname );
    $PAGE->navbar->add(get_string('startseite','format_mooin4',));

    //set_user_preference('card_display', 1, $USER->id);
    //set_user_preference('mooin4_display', 0, $USER->id);
    // set_user_preference('card_display','yes');
    // set_user_preference('mooin4_display', 'no');

    // Set user preferences for the user
    $listOfPreferences = array('card_display' => 'yes', 'mooin4_display' => null);
    set_user_preferences($listOfPreferences);

    echo $OUTPUT->header();
    // print enrol info
    /* if ($texts = enrol_get_course_description_texts($course)) {
        echo $OUTPUT->box_start('generalbox icons');
        echo implode($texts);
        echo $OUTPUT->box_end();
    } */
    $courserenderer = $PAGE->get_renderer('core', 'course');
    //echo $courserenderer->course_info_box($course);
    $course_name = $course->shortname;
    // var_dump($course);
    // $course_nav = $DB->get_record("course_navigation", array("id"=>$courseid));
    $desc = $course->summary;
    
    // var_dump($course);
    // get_user_preferences('card_display',0, $USER->id);
    // var_dump( get_user_preferences());
    /* if(get_user_preferences('card_display')) {

    } */
    $infos_data[] = array(
        "title"   => $course_name, 
        "desc" =>strip_tags($desc), 
        "title_url" => new moodle_url('/course/view.php', array('id'=>$course->id)),
        "svg" => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-laptop" viewBox="0 0 16 16">
        <path d="M13.5 3a.5.5 0 0 1 .5.5V11H2V3.5a.5.5 0 0 1 .5-.5h11zm-11-1A1.5 1.5 0 0 0 1 3.5V12h14V3.5A1.5 1.5 0 0 0 13.5 2h-11zM0 12.5h16a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 12.5z"/>
      </svg>'
    );
    $infos_data[] = array(
        "title"   => get_string('inhaltsverzeichnis', 'format_mooin4'), 
        "desc" => get_string('inhaltsverzeichnis_desc', 'format_mooin4'),
        "title_url" => new moodle_url('/course/format/mooin4/inhalt.php', array('id'=>$course->id)),
        "svg" => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-bookmark-fill" viewBox="0 0 16 16">
        <path d="M2 2v13.5a.5.5 0 0 0 .74.439L8 13.069l5.26 2.87A.5.5 0 0 0 14 15.5V2a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2z"/>
      </svg>'
    );
    
    $infos_data[] = array(
        "title"   => get_string('newsforen', 'format_mooin4'), 
        "desc" => get_string('newsforen_desc', 'format_mooin4'), 
        "title_url" => new moodle_url('/mod/forum/view.php', array('f'=>$course->id - 1)),
        "svg" => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-chat-dots-fill" viewBox="0 0 16 16">
        <path d="M16 8c0 3.866-3.582 7-8 7a9.06 9.06 0 0 1-2.347-.306c-.584.296-1.925.864-4.181 1.234-.2.032-.352-.176-.273-.362.354-.836.674-1.95.77-2.966C.744 11.37 0 9.76 0 8c0-3.866 3.582-7 8-7s8 3.134 8 7zM5 8a1 1 0 1 0-2 0 1 1 0 0 0 2 0zm4 0a1 1 0 1 0-2 0 1 1 0 0 0 2 0zm3 1a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
      </svg>'
    );
        
    $infos_data[] = array(
        "title"   => get_string('diskussionsforen', 'format_mooin4'), 
        "desc" => get_string('diskussionsforen_desc', 'format_mooin4'),
        "title_url" => new moodle_url('/course/format/mooin4/forum_view.php', array('id'=>$course->id)),
        "svg" => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-list-check" viewBox="0 0 16 16">
        <path fill-rule="evenodd" d="M5 11.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zM3.854 2.146a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 1 1 .708-.708L2 3.293l1.146-1.147a.5.5 0 0 1 .708 0zm0 4a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 1 1 .708-.708L2 7.293l1.146-1.147a.5.5 0 0 1 .708 0zm0 4a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 0 1 .708-.708l.146.147 1.146-1.147a.5.5 0 0 1 .708 0z"/>
      </svg>'
    );
    
    $infos_data[] = array(
        "title"   => get_string('teilnehmenden', 'format_mooin4'), 
        "desc" => get_string('teilnehmenden_desc', 'format_mooin4'),
        "title_url" => new moodle_url('/course/format/mooin4/users.php', array('id'=>$course->id)),
        "svg" => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-people-fill" viewBox="0 0 16 16">
        <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1H7zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
        <path fill-rule="evenodd" d="M5.216 14A2.238 2.238 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.325 6.325 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1h4.216z"/>
        <path d="M4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z"/>
      </svg>'
    );
    
    $infos_data[] = array(
        "title"   => get_string('social_media', 'format_mooin4'), 
        "desc" => get_string('social_media_desc', 'format_mooin4'), 
        "title_url" => new moodle_url('/course/format/mooin4/view_social.php', array('id'=>$course->id)),
        "svg" => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-share-fill" viewBox="0 0 16 16">
        <path d="M11 2.5a2.5 2.5 0 1 1 .603 1.628l-6.718 3.12a2.499 2.499 0 0 1 0 1.504l6.718 3.12a2.5 2.5 0 1 1-.488.876l-6.718-3.12a2.5 2.5 0 1 1 0-3.256l6.718-3.12A2.5 2.5 0 0 1 11 2.5z"/>
      </svg>'
    );
        

    $templatecontext = (object)[
        'cards' => $infos_data,
        /* 'title' => [$course_name, $course_name] ,
        'desc'  => [strip_tags($desc), strip_tags($desc)],
        'title_url'  => new moodle_url('/course/view.php', array('id'=>$course->id)) */
    ];
    
    echo $OUTPUT -> render_from_template('format_mooin4/manage', $templatecontext);
    echo "<br />";
    
    // Trigger event, course information viewed.
    $eventparams = array('context' => $context, 'objectid' => $course->id);
    $event = \core\event\course_information_viewed::create($eventparams);
    $event->trigger();
    // var_dump($event);

    // Remove the User preference for the card page each time the user come throught this page
    // unset_user_preference('card_display');
    $listOfPreferences = array('mooin4_display' => 'yes', 'card_display' => null);

    set_user_preferences($listOfPreferences);

    // var_dump( get_user_preferences());
    echo $OUTPUT->footer();

