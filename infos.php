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
        "title_url" => new moodle_url('/course/view.php', array('id'=>$course->id))
    );
    $infos_data[] = array(
        "title"   => get_string('inhaltsverzeichnis', 'format_mooin4'), 
        "desc" => get_string('inhaltsverzeichnis_desc', 'format_mooin4'),
        "title_url" => new moodle_url('/course/format/mooin4/inhalt.php', array('id'=>$course->id))
    );
    
    $infos_data[] = array(
        "title"   => get_string('newsforen', 'format_mooin4'), 
        "desc" => get_string('newsforen_desc', 'format_mooin4'), 
        "title_url" => new moodle_url('/mod/forum/view.php', array('f'=>$course->id - 1))
    );
        
    $infos_data[] = array(
        "title"   => get_string('diskussionsforen', 'format_mooin4'), 
        "desc" => get_string('diskussionsforen_desc', 'format_mooin4'),
        "title_url" => new moodle_url('/course/format/mooin4/forum_view.php', array('id'=>$course->id))
    );
    
    $infos_data[] = array(
        "title"   => get_string('teilnehmenden', 'format_mooin4'), 
        "desc" => get_string('teilnehmenden_desc', 'format_mooin4'),
        "title_url" => new moodle_url('/course/format/mooin4/users.php', array('id'=>$course->id))
    );
    
    $infos_data[] = array(
        "title"   => get_string('social_media', 'format_mooin4'), 
        "desc" => get_string('social_media_desc', 'format_mooin4'), 
        "title_url" => new moodle_url('/course/format/mooin4/view_social.php', array('id'=>$course->id))
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

