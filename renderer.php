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
 * format_buttons_renderer
 *
 * @package    format_buttons
 * @author     Rodrigo Brandão <https://www.linkedin.com/in/brandaorodrigo>
 * @copyright  2020 Rodrigo Brandão <rodrigo.brandao.contato@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/format/topics/renderer.php');
//moodleform is defined in formslib.php
require_once("$CFG->libdir/formslib.php");

/**
 * Format_buttons_renderer
 *
 * @package    format_buttons
 * @author     Rodrigo Brandão <https://www.linkedin.com/in/brandaorodrigo>
 * @copyright  2020 Rodrigo Brandão <rodrigo.brandao.contato@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class format_buttons_renderer extends format_topics_renderer
{
    protected $courseformat; // Our course format object as defined in lib.php
    
     /**
     * Constructor method, calls the parent constructor.
     *
     * @param moodle_page $page
     * @param string $target one of rendering target constants
     */
    public function __construct(moodle_page $page, $target) {
        parent::__construct($page, $target);
        $this->courseformat = course_get_format($page->course);
        // Since format_topics_renderer::section_edit_control_items() only displays the 'Highlight' control
        // when editing mode is on we need to be sure that the link 'Turn editing mode on' is available for a user
        // who does not have any other managing capability.
        $page->set_other_editing_capability('moodle/course:setcurrentsection');
    }
    /**
     * Get_button_section
     *
     * @param stdclass $course
     * @param string $name
     * @return string
     */
    protected function get_color_config($course, $name) {
        $return = false;
        if (isset($course->{$name})) {
            $color = str_replace('#', '', $course->{$name});
            $color = substr($color, 0, 6);
            if (preg_match('/^#?[a-f0-9]{6}$/i', $color)) {
                $return = '#'.$color;
            }
        }
        return $return;
    }

    /**
     * Number_to_roman
     *
     * @param integer $number
     * @return string
     */
    protected function number_to_roman($number) {
        $number = intval($number);
        $return = '';
        $romanarray = [
            'M' => 1000,
            'CM' => 900,
            'D' => 500,
            'CD' => 400,
            'C' => 100,
            'XC' => 90,
            'L' => 50,
            'XL' => 40,
            'X' => 10,
            'IX' => 9,
            'V' => 5,
            'IV' => 4,
            'I' => 1
        ];
        foreach ($romanarray as $roman => $value) {
            $matches = intval($number / $value);
            $return .= str_repeat($roman, $matches);
            $number = $number % $value;
        }
        return $return;
    }

    /**
     * Number_to_alphabet
     *
     * @param integer $number
     * @return string
     */
    protected function number_to_alphabet($number) {
        $number = $number - 1;
        $alphabet = range("A", "Z");
        $lektion = 'Lektion '; // Add the Lektion infront of the course section
        if ($number <= 25) {
            return $lektion . $alphabet[$number];
        } else if ($number > 25) {
            $dividend = ($number + 1);
            $alpha = '';
            while ($dividend > 0) {
                $modulo = ($dividend - 1) % 26;
                $alpha = $alphabet[$modulo] . $alpha;
                $dividend = floor((($dividend - $modulo) / 26));
            }
            return $lektion . $alpha;
        }
    }

    /**
     * Get_button_section
     *
     * @param stdclass $course
     * @param string $sectionvisible
     * @return string
     */
    protected function get_button_section($course, $sectionvisible) {
        global $PAGE;
        $html = '';
        $css = '';

        $html.=html_writer::start_tag('div', array('class'=>'carousel-container'));
        $html.=html_writer::start_tag('div', array('class'=>'carousel', 'id' =>'slider'));
        $html.=html_writer::start_tag('div', array('class'=>'carousel-inner'));

        if ($colorcurrent = $this->get_color_config($course, 'colorcurrent')) {
            $css .=
            '#buttonsectioncontainer .buttonsection.current {
                background: ' . $colorcurrent . ';
            }
            ';
        }
        if ($colorvisible = $this->get_color_config($course, 'colorvisible')) {
            $css .=
            '#buttonsectioncontainer .buttonsection.sectionvisible {
                background: ' . $colorvisible . ';
            }
            ';
        }
        if ($css) {
            $html .= html_writer::tag('style', $css);
        }

        $withoutdivisor = true;
        for ($k = 1; $k <= 12; $k++) {
            if ($course->{'divisor' . $k}) {
                $withoutdivisor = false;
            }
        }
        if ($withoutdivisor) {
            $course->divisor1 = 999;
        }
        $divisorshow = false;
        $count = 1;
        $currentdivisor = 1;
        $modinfo = get_fast_modinfo($course);
        $inline = '';
        $lektion = 'Lektion ';
        foreach ($modinfo->get_section_info_all() as $section => $thissection) {
            if ($section == 0) {
                continue;
            }
            if ($section > $course->numsections) {
                // Code Add to set the number Section to numsection in the DB if the existing sections are greather than the sections in the DB.
                continue;
            }
            if ($course->hiddensections && !(int)$thissection->visible) {
                continue;
            }
            if (isset($course->{'divisor' . $currentdivisor}) && $count > $course->{'divisor' . $currentdivisor}) {
                $currentdivisor++;
                $count = 1;
            }
            if (isset($course->{'divisor' . $currentdivisor}) &&
                $course->{'divisor' . $currentdivisor} != 0 &&
                !isset($divisorshow[$currentdivisor])) {
                $currentdivisorhtml = format_string($course->{'divisortext' . $currentdivisor});
                $currentdivisorhtml = str_replace('[br]', '<br>', $currentdivisorhtml);
                $currentdivisorhtml = html_writer::tag('div', $currentdivisorhtml, ['class' => 'divisortext']);
                if ($course->inlinesections) {
                    $inline = 'inlinebuttonsections';
                }
                // $html .= html_writer::tag('div', $currentdivisorhtml, ['class' => "divisorsection $inline"]);
                $divisorshow[$currentdivisor] = true;
            }
            $id = 'buttonsection-' . $section;
            if ($course->sequential) {
                $name = $lektion . $section;  
            } else {
                if (isset($course->{'divisor' . $currentdivisor}) &&
                $course->{'divisor' . $currentdivisor} == 1) {
                    $name = '&bull;&bull;&bull;';
                } else {
                    $name = $lektion . $count;
                }
            }
            if ($course->sectiontype == 'alphabet' && is_numeric($name)) {
                $name = $this->number_to_alphabet($name);
            }
            if ($course->sectiontype == 'roman' && is_numeric($name)) {
                $name = $this->number_to_roman($name);
            }
            $class = 'buttonsection';
            $onclick = 'M.format_buttons.show(' . $section . ',' . $course->id . ')';
            if (!$thissection->available &&
                !empty($thissection->availableinfo)) {
                $class .= ' sectionhidden';
            } else if (!$thissection->uservisible || !$thissection->visible) {
                $class .= ' sectionhidden';
                $onclick = false;
            }
            if ($course->marker == $section) {
                $class .= ' current';
            }
            if ($sectionvisible == $section) {
                $class .= ' sectionvisible';
            }
            if ($PAGE->user_is_editing()) {
                $onclick = false;
            }
            // $html.= html_writer::div('Previous', 'previous_btn', array('id' => 'btn_previous')); // <div class="toad" id="tophat">Mr</div>
            
            $html .= html_writer::tag('div', $name, ['id' => $id, 'class' => $class, 'onclick' => $onclick]);
            $count++;
        }
        $html.=html_writer::end_tag('div');

        // svg icon start
        $html.=html_writer::start_tag('div', array('class'=>'carousel__buttons'));
        $html.=html_writer::start_tag('div', array('class'=>'carousel__gradient--left'));
        $html.=html_writer::end_tag('div');
        $html.=html_writer::start_tag('div', array('class'=>'carousel__gradient--right'));
        $html.=html_writer::end_tag('div');
        $html.=html_writer::start_tag('a', array('class'=>'carousel__button--prev'));
        $html.=html_writer::start_tag('svg', ['xmlns'=> 'http://www.w3.org/2000/svg', 'width'=>'32', 'height'=> '32', 'fill'=>'currentColor', 'class'=> 'bi bi-chevron-left', 'viewBox'=>'0 0 16 16']);
        $html.=html_writer::start_tag('path', ['fill-rule'=>'evenodd','d'=>'M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z']); // <i class="bi bi-chevron-right"></i>
        $html.=html_writer::end_tag('path');
        $html.=html_writer::end_tag('svg');
        $html.=html_writer::end_tag('a');
        $html.=html_writer::start_tag('a', array('class'=>'carousel__button--next'));
        $html.=html_writer::start_tag('svg', ['xmlns'=> 'http://www.w3.org/2000/svg', 'width'=>'32', 'height'=> '32', 'fill'=>'currentColor', 'class'=> 'bi bi-chevron-right', 'viewBox'=>'0 0 16 16']);
        $html.=html_writer::start_tag('path', ['fill-rule'=>'evenodd','d'=>'M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z']); // <i class="bi bi-chevron-right"></i>
        $html.=html_writer::end_tag('path');
        $html.=html_writer::end_tag('svg');
        $html.=html_writer::end_tag('a');
        //End Svg tag
        $html.=html_writer::end_tag('div');
        $html.=html_writer::end_tag('div');
        $html.=html_writer::end_tag('div');
        $html = html_writer::tag('div', $html, ['id' => 'buttonsectioncontainer', 'class' => $course->buttonstyle]);
        if ($PAGE->user_is_editing()) {
            $html .= html_writer::tag('div', get_string('editing', 'format_buttons'), ['class' => 'alert alert-warning alert-block fade in']);
        }
        
        
        return $html;
    }

            /**
     * Get_button_section
     *
     * @param stdclass $course
     * @param string $sectionvisible
     * @return string
     */
    protected function bottom_get_button_section($course, $sectionvisible) {

        global $PAGE;
        $html = '';
        $css = '';

        $html.=html_writer::start_tag('div', array('class'=>'bottom_carousel-container'));
        $html.=html_writer::start_tag('div', array('class'=>'bottom_carousel-inner'));
        if ($colorcurrent = $this->get_color_config($course, 'colorcurrent')) {
            $css .=
            '#bottom_buttonsectioncontainer .bottom_buttonsection.current {
                background: ' . $colorcurrent . ';
            }
            ';
        }
        if ($colorvisible = $this->get_color_config($course, 'colorvisible')) {
            $css .=
            '#bottom_buttonsectioncontainer .bottom_buttonsection.sectionvisible {
                background: ' . $colorvisible . ';
            }
            ';
        }
        if ($css) {
            $html .= html_writer::tag('style', $css);
        }

        /* $withoutdivisor = true;
        for ($k = 1; $k <= 12; $k++) {
            if ($course->{'divisor' . $k}) {
                $withoutdivisor = false;
            }
        }
        if ($withoutdivisor) {
            $course->divisor1 = 999;
        } */
        $divisorshow = false;
        $count = 1;
        $currentdivisor = 1;
        $modinfo = get_fast_modinfo($course);
        $inline = '';
        $sections = $course -> numsections;
        
        foreach ($modinfo->get_section_info_all() as $section => $thissection) {
            if ($section <= 1) {
               // disable the previous button in the bottom nav bar

               //code come here...

                continue;
            }
            if ($section > $course->numsections) {
                // Code Add to set the number Section to numsection in the DB if the existing sections are greather than the sections in the DB.
                //disable the next button in the bottom nav bar

                // Code come here...
                
                continue;
            }
            if ($course->hiddensections && !(int)$thissection->visible) {
                continue;
            }
            /* if (isset($course->{'divisor' . $currentdivisor}) &&
                $count > $course->{'divisor' . $currentdivisor}) {
                $currentdivisor++;
                $count = 1;
            } */
            if (isset($course->{'divisor' . $currentdivisor}) &&
                $course->{'divisor' . $currentdivisor} != 0 &&
                !isset($divisorshow[$currentdivisor])) {
                $currentdivisorhtml = format_string($course->{'divisortext' . $currentdivisor});
                $currentdivisorhtml = str_replace('[br]', '<br>', $currentdivisorhtml);
                $currentdivisorhtml = html_writer::tag('div', $currentdivisorhtml, ['class' => 'divisortext']);
                if ($course->inlinesections) {
                    $inline = 'inlinebuttonsections';
                }
                $html .= html_writer::tag('div', $currentdivisorhtml, ['class' => "divisorsection $inline"]);
                $divisorshow[$currentdivisor] = true;
            }
            $id = 'bottom_buttonsection-' . $section;
           
            if ($course->sequential) {
                $name = '&bull;&bull;&bull;';
                
            } else {
                if (isset($course->{'divisor' . $currentdivisor}) &&
                $course->{'divisor' . $currentdivisor} == 1) {
                    $name = '&bull;&bull;&bull;';
                } else {
                    $name = '&bull;&bull;&bull;';
                 
                }
            }
            $class = 'bottom_buttonsection';
            $onclick = 'M.format_buttons.show(' . $sections . ',' . $section . ',' . $course->id . ')';
            if (!$thissection->available &&
                !empty($thissection->availableinfo)) {
                $class .= ' sectionhidden';
            } else if (!$thissection->uservisible || !$thissection->visible) {
                $class .= ' sectionhidden';
                $onclick = false;
            }
            if ($course->marker == $section) {
                $class .= ' current';
            }
            if ($sectionvisible == $section) {
                $class .= ' sectionvisible';
            }
            if ($PAGE->user_is_editing()) {
                $onclick = false;
            }
            
            $html .= html_writer::tag('div', '', ['id' => $id, 'class' => $class, 'onclick' => $onclick]);
            $count++;
        }
        
        $html.=html_writer::start_tag('div', array('class'=>'bottom_previous', 'id' => 'id_bottom_previous'));
        $html.=html_writer::start_tag('svg', ['xmlns'=> 'http://www.w3.org/2000/svg', 'width'=>'32', 'height'=> '32', 'fill'=>'currentColor', 'class'=> 'bi bi-chevron-left', 'viewBox'=>'0 0 16 16']);
        $html.=html_writer::start_tag('path', ['fill-rule'=>'evenodd','d'=>'M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z']);
        $html.=html_writer::end_tag('path');
        $html.=html_writer::end_tag('svg');
        
        $html.=html_writer::end_tag('div');

        
        $html.=html_writer::start_tag('div', array('class'=>'bottom_home', 'id' => 'id_bottom_home'));
        $html.=html_writer::start_tag('svg', ['xmlns'=> 'http://www.w3.org/2000/svg', 'width'=>'32', 'height'=> '32', 'fill'=>'currentColor', 'class'=> 'bi bi-house-door-fill', 'viewBox'=>'0 0 16 16']);
        $html.=html_writer::start_tag('path', ['fill-rule'=>'evenodd','d'=>'M6.5 14.5v-3.505c0-.245.25-.495.5-.495h2c.25 0 .5.25.5.5v3.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5z']);
        $html.=html_writer::end_tag('path');
        $html.=html_writer::end_tag('svg');
        
        $html.=html_writer::end_tag('div');

        $html.=html_writer::start_tag('div', array('class'=>'bottom_next', 'id' => 'id_bottom_next'));
        $html.=html_writer::start_tag('svg', ['xmlns'=> 'http://www.w3.org/2000/svg', 'width'=>'32', 'height'=> '32', 'fill'=>'currentColor', 'class'=> 'bi bi-chevron-right', 'viewBox'=>'0 0 16 16']);
        $html.=html_writer::start_tag('path', ['fill-rule'=>'evenodd','d'=>'M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z']);
        $html.=html_writer::end_tag('path');
        $html.=html_writer::end_tag('svg');
        $html.=html_writer::end_tag('div');

        $html.=html_writer::end_tag('div');
        

        $html = html_writer::tag('div', $html, ['id' => 'bottom_buttonsectioncontainer', 'class' => $course->buttonstyle]);
        if ($PAGE->user_is_editing()) {
            $html .= html_writer::tag('div', get_string('editing', 'format_buttons'), ['class' => 'alert alert-warning alert-block fade in']);
        }
        
        
        return $html;
    }

    /**
     * Start_nav_list
     *
     * @return string
     */
    protected function start_nav_list() {
        return html_writer::start_tag('div', ['class' => 'buttons_nav']);
    }

    /**
     * Start_section_list
     *
     * @return string
     */
    protected function start_section_list() {
        return html_writer::start_tag('ul', ['class' => 'buttons'], array('id' => 'buttons_id'));
    }

    /**
     * End_section_list
     *
     * @return string
     */
    protected function end_section_list() {
        return html_writer::end_tag('ul');
    }
    /**
     * End_nav_list
     *
     * @return string
     */
    protected function end_nav_list() {
        return html_writer::end_tag('div');
    }
    /**
     * Section_header
     *
     * @param stdclass $section
     * @param stdclass $course
     * @param bool $onsectionpage
     * @param int $sectionreturn
     * @return string
     */
    protected function section_header($section, $course, $onsectionpage, $sectionreturn = null) {
        global $PAGE;

        $o = '';
        $currenttext = '';
        $sectionstyle = '';

        if ($section->section != 0) {
            // Only in the non-general sections.
            if (!$section->visible) {
                $sectionstyle = ' hidden';
            }
            if (course_get_format($course)->is_section_current($section)) {
                $sectionstyle = ' current';
            }
        }
        
        $o.= html_writer::start_tag('li', array('id' => 'section-'.$section->section,
             'class' => 'section main clearfix'.$sectionstyle, 'role'=>'region',
             'aria-label'=> get_section_name($course, $section)));

        // Create a span that contains the section title to be used to create the keyboard section move menu.
        $o .= html_writer::tag('span', get_section_name($course, $section), array('class' => 'hidden sectionname'));

        $leftcontent = $this->section_left_content($section, $course, $onsectionpage);
        $o.= html_writer::tag('div', $leftcontent, array('class' => 'left side'));

        $rightcontent = $this->section_right_content($section, $course, $onsectionpage);
        $o.= html_writer::tag('div', $rightcontent, array('class' => 'right side'));
        $o.= html_writer::start_tag('div', array('class' => 'content'));

        // When not on a section page, we display the section titles except the general section if null
        $hasnamenotsecpg = (!$onsectionpage && ($section->section != 0 || !is_null($section->name)));

        // When on a section page, we only display the general section title, if title is not the default one
        $hasnamesecpg = ($onsectionpage && ($section->section == 0 && !is_null($section->name)));

        $classes = ' accesshide';
        if ($hasnamenotsecpg || $hasnamesecpg) {
            $classes = '';
        }
        $sectionname = html_writer::tag('span', $this->section_title($section, $course));

        // Button format - ini
        if ($course->showdefaultsectionname) {
            $o.= $this->output->heading($sectionname, 3, 'sectionname' . $classes);
        }
        // Button format - end

        $o .= $this->section_availability($section);

        $o .= html_writer::start_tag('div', array('class' => 'summary'));
        if ($section->uservisible || $section->visible) {
            // Show summary if section is available or has availability restriction information.
            // Do not show summary if section is hidden but we still display it because of course setting
            // "Hidden sections are shown in collapsed form".
            // $o .= $this->format_summary_text('Some Summary text');
            $o .= $this->format_summary_text($section);
        }
        $o .= html_writer::end_tag('div');

        return $o;
    }

     /**
     * Generate the html for the 'Jump to' menu on a single section page.
     *
     * @param stdClass $course The course entry from DB
     * @param array $sections The course_sections entries from the DB
     * @param $displaysection the current displayed section number.
     *
     * @return string HTML to output.
     */
    protected function section_nav_selection($course, $sections, $displaysection) {
        global $CFG;
        $o = '';
        $sectionmenu = array();
        $sectionmenu[course_get_url($course)->out(false)] = get_string('maincoursepage');
        $modinfo = get_fast_modinfo($course);
        $section = 1;
        $numsections = course_get_format($course)->get_last_section_number();
        while ($section <= $numsections) {
            $thissection = $modinfo->get_section_info($section);
            $showsection = $thissection->uservisible or !$course->hiddensections;
            if (($showsection) && ($section != $displaysection) && ($url = course_get_url($course, $section))) {
                $sectionmenu[$url->out(false)] = get_section_name($course, $section);
            }
            $section++;
        }

        $select = new url_select($sectionmenu, '', array('' => get_string('jumpto')));
        $select->class = 'jumpmenu';
        $select->formid = 'sectionmenu';
        $o .= $this->output->render($select);

        return $o;
    }

       /**
     * Output the html for a single section page .
     *
     * @param stdClass $course The course entry from DB
     * @param array $sections (argument not used)
     * @param array $mods (argument not used)
     * @param array $modnames (argument not used)
     * @param array $modnamesused (argument not used)
     * @param int $displaysection The section number in the course which is being displayed
     */
    public function print_single_section_page($course, $sections, $mods, $modnames, $modnamesused, $displaysection) {
        $modinfo = get_fast_modinfo($course);
        $course = course_get_format($course)->get_course();

        //$url =  new moodle_url('/course/format/buttons/infos.php', array('id'=>$course->id));
    
        // $PAGE->set_url($url);
        //redirect($url, false);

        // Can we view the section in question?
        if (!($sectioninfo = $modinfo->get_section_info($displaysection)) || !$sectioninfo->uservisible) {
            // This section doesn't exist or is not available for the user.
            // We actually already check this in course/view.php but just in case exit from this function as well.
            print_error('unknowncoursesection', 'error', course_get_url($course),
                format_string($course->fullname));
        }

        // Copy activity clipboard..
        echo $this->course_activity_clipboard($course, $displaysection);
        $thissection = $modinfo->get_section_info(0);
        if ($thissection->summary or !empty($modinfo->sections[0]) or $this->page->user_is_editing()) {
            echo $this->start_section_list();
            echo $this->section_header($thissection, $course, true, $displaysection);
            echo $this->courserenderer->course_section_cm_list($course, $thissection, $displaysection);
            echo $this->courserenderer->course_section_add_cm_control($course, 0, $displaysection);
            echo $this->section_footer();
            echo $this->end_section_list();
        }
        // Start single-section div
        echo html_writer::start_tag('div', array('class' => 'single-section'));

        // The requested section page.
        $thissection = $modinfo->get_section_info($displaysection);

        // Title with section navigation links.
        $sectionnavlinks = $this->get_nav_links($course, $modinfo->get_section_info_all(), $displaysection);
        $sectiontitle = '';
        $sectiontitle .= html_writer::start_tag('div', array('class' => 'section-navigation navigationtitle'));
        $sectiontitle .= html_writer::tag('span', $sectionnavlinks['previous'], array('class' => 'mdl-left'));
        $sectiontitle .= html_writer::tag('span', $sectionnavlinks['next'], array('class' => 'mdl-right'));
        // Title attributes
        $classes = 'sectionname';
        if (!$thissection->visible) {
            $classes .= ' dimmed_text';
        }
        $sectionname = html_writer::tag('span', $this->section_title_without_link($thissection, $course));
        $sectiontitle .= $this->output->heading($sectionname, 3, $classes);

        $sectiontitle .= html_writer::end_tag('div');
        // echo $sectiontitle;

        // Now the list of sections..
        echo $this->start_section_list();

        echo $this->section_header($thissection, $course, true, $displaysection);

        echo $this->courserenderer->course_section_cm_list($course, $thissection, $displaysection);
        echo $this->courserenderer->course_section_add_cm_control($course, $displaysection, $displaysection);
        echo $this->section_footer();
        echo $this->end_section_list();

        // Display section bottom navigation.
        $sectionbottomnav = '';
        $sectionbottomnav .= html_writer::start_tag('div', array('class' => 'section-navigation mdl-bottom'));
        $sectionbottomnav .= html_writer::tag('span', $sectionnavlinks['previous'], array('class' => 'mdl-left'));
        $sectionbottomnav .= html_writer::tag('span', $sectionnavlinks['next'], array('class' => 'mdl-right'));
        $sectionbottomnav .= html_writer::tag('div', $this->section_nav_selection($course, $sections, $displaysection),
            array('class' => 'mdl-align'));
        $sectionbottomnav .= html_writer::end_tag('div');
        echo $sectionbottomnav;

        // Close single-section div.
        echo html_writer::end_tag('div');
    }

    /**
     * Print_multiple_section_page
     *
     * @param stdclass $course
     * @param array $sections (argument not used)
     * @param array $mods (argument not used)
     * @param array $modnames (argument not used)
     * @param array $modnamesused (argument not used)
     */
    public function print_multiple_section_page($course, $sections, $mods, $modnames, $modnamesused) {
        
        global $USER;
        global $PAGE;

        // $courseconfig = get_config('moodlecourse');
        // echo('$courseconfig');
        // var_dump($course->numsections);

       // $url = new moodle_url('/course/view.php', array('id' => $course->id));
     
        
        $modinfo = get_fast_modinfo($course);
        // var_dump($modinfo -> sections);

        $course = course_get_format($course)->get_course();

        $context = context_course::instance($course->id);
        // Title with completion help icon.
        $completioninfo = new completion_info($course);

        // buttons format - ini
        if (isset($_COOKIE['sectionvisible_' . $course->id])) {
            $sectionvisible = $_COOKIE['sectionvisible_' . $course->id];
        } else if ($course->marker > 0) {
            $sectionvisible = $course->marker;
        } else {
            $sectionvisible = 1;
        }
        $htmlsection = false;
        foreach ($modinfo->get_section_info_all() as $section => $thissection) {
            $htmlsection[$section] = '';
            if ($section == 0) {
                $section0 = $thissection;
                continue;
            }
            if ($section > $course->numsections) {
                continue;
            }
            /* If is not editing verify the rules to display the sections */
            if (!$PAGE->user_is_editing()) {
                if ($course->hiddensections && !(int)$thissection->visible) {
                    continue;
                }
                if (!$thissection->available && !empty($thissection->availableinfo)) {
                    $htmlsection[$section] .= $this->section_header($thissection, $course, false, 0);
                    continue;
                }
                if (!$thissection->uservisible || !$thissection->visible) {
                    $htmlsection[$section] .= $this->section_hidden($section, $course->id);
                    continue;
                }
            }
            $htmlsection[$section] .= $this->section_header($thissection, $course, false, 0);
            if ($thissection->uservisible) {
                $htmlsection[$section] .= $this->courserenderer->course_section_cm_list($course, $thissection, 0);
                $htmlsection[$section] .= $this->courserenderer->course_section_add_cm_control($course, $section, 0);

            }
            $htmlsection[$section] .= $this->section_footer();
        }
        if ($section0->summary || !empty($modinfo->sections[0]) || $PAGE->user_is_editing()) {
            $htmlsection0 = $this->section_header($section0, $course, false, 0);
            $htmlsection0 .= $this->courserenderer->course_section_cm_list($course, $section0, 0);
            $htmlsection0 .= $this->courserenderer->course_section_add_cm_control($course, 0, 0);
            $htmlsection0 .= $this->section_footer();
        }
        echo $completioninfo->display_help_icon();
        echo $this->output->heading($this->page_title(), 2, 'accesshide');
        echo $this->course_activity_clipboard($course, 0);
        echo $this->start_section_list();
        if ($course->sectionposition == 0 and isset($htmlsection0)) {
            echo html_writer::tag('span', $htmlsection0, ['class' => 'above']);
        }
        echo $this->get_button_section($course, $sectionvisible);
        
       //  $sections_array = array(); // $modinfo -> sections;
        foreach ($htmlsection as $current) {

            // array_push($sections_array, $current);
            echo $current;
           
        }
        if ($course->sectionposition == 1 and isset($htmlsection0)) {
            echo html_writer::tag('span', $htmlsection0, ['class' => 'below']);
        }
        if ($PAGE->user_is_editing() and has_capability('moodle/course:update', $context)) {
            foreach ($modinfo->get_section_info_all() as $section => $thissection) {
                if ($section <= $course->numsections or empty($modinfo->sections[$section])) {
                    continue;
                }
                echo $this->stealth_section_header($section);
                echo $this->courserenderer->course_section_cm_list($course, $thissection, 0);
                echo $this->stealth_section_footer();
            }
            echo $this->end_section_list();
            echo html_writer::start_tag('div', ['id' => 'changenumsections', 'class' => 'mdl-right']);
            $straddsection = get_string('increasesections', 'moodle');
            $url = new moodle_url('/course/changenumsections.php', ['courseid' => $course->id,
                'increase' => true, 'sesskey' => sesskey()]);
            $icon = $this->output->pix_icon('t/switch_plus', $straddsection);
            echo html_writer::link($url, $icon.get_accesshide($straddsection), ['class' => 'increase-sections']);
            if ($course->numsections > 0) {
                $strremovesection = get_string('reducesections', 'moodle');
                $url = new moodle_url('/course/changenumsections.php', ['courseid' => $course->id,
                    'increase' => false, 'sesskey' => sesskey()]);
                $icon = $this->output->pix_icon('t/switch_minus', $strremovesection);
                echo html_writer::link(
                    $url,
                    $icon.get_accesshide($strremovesection),
                    ['class' => 'reduce-sections']
                );
            }
            echo html_writer::end_tag('div');
        } else {
            echo $this->end_section_list();
        }
        // Set & edit User preferences
        $listOfPreferences = array('buttons_display' => null, 'card_display' => 'yes');

        set_user_preferences($listOfPreferences);

        // var_dump( get_user_preferences());
        if (!$PAGE->user_is_editing()) {
            $PAGE->requires->js_init_call('M.format_buttons.init', [$course->numsections, $sectionvisible, $course->id]);
        }
        
        // var_dump($sections_array);
        echo $this-> bottom_get_button_section($course,$sectionvisible);
        // Button format - end
    //}
    }
}
