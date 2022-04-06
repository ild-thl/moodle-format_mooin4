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
 * format_mooin4_renderer
 *
 * @package    format_mooin4
 * @author     Rodrigo Brandão <https://www.linkedin.com/in/brandaorodrigo>
 * @copyright  2020 Rodrigo Brandão <rodrigo.brandao.contato@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot. '/course/format/topics/lib.php');
// online_users_map setting
include_once($CFG->dirroot.'/lib/datalib.php');

/**
 * format_mooin4
 *
 * @package    format_mooin4
 * @author     Rodrigo Brandão (rodrigobrandao.com.br)
 * @copyright  2017 Rodrigo Brandão
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class format_mooin4 extends format_topics {

    /**
     * Returns true if this course format uses sections
     *
     * @return bool
     */
    public function uses_sections() {
        return true;
    }

    /**
     * course_format_options
     *
     * @param bool $foreditform
     * @return array
     */
    public function course_format_options($foreditform = false) {
        global $PAGE;

        static $courseformatoptions = false;
        $courseconfig = get_config('moodlecourse');
        if ($courseformatoptions === false) {
            $courseconfig = get_config('moodlecourse');

            $courseformatoptions['numsections'] = array(
                'default' => $courseconfig->numsections,
                'type' => PARAM_INT,
            );
            // Try adding course navigation card here
            /* $courseformatoptions['mooin4newsforumtitle'] = array(
                'default' => $courseconfig->newsforumtitle,
                'type' =>PARAM_TEXT,
            );
            $courseformatoptions['mooin4newsforumdesc'] = array(
                'default' => $courseconfig->newsforumdesc,
                'type' =>PARAM_TEXT,
            ); */
            $courseformatoptions['hiddensections'] = array(
                'default' => $courseconfig->hiddensections,
                'type' => PARAM_INT,
            );
            // New adding coursedisplay in mooin4/lib.php to handle each section in the course format
            $courseformatoptions['coursedisplay'] = array(
                'default' => $courseconfig->coursedisplay,
                'type' => PARAM_INT,
            );
            /* $courseformatoptions['navsections'] = array(
                'default' => $courseconfig->navsections,
                'type' => PARAM_INT,
            ); */
            $courseformatoptions['showdefaultsectionname'] = array(
                'default' => get_config('format_mooin4', 'showdefaultsectionname'),
                'type' => PARAM_INT,
            );

            $courseformatoptions['sectionposition'] = array(
                'default' => get_config('format_mooin4', 'sectionposition'),
                'type' => PARAM_INT,
            );

            $courseformatoptions['inlinesections'] = array(
                'default' => get_config('format_mooin4', 'inlinesections'),
                'type' => PARAM_INT,
            );

            $courseformatoptions['sequential'] = array(
                'default' => get_config('format_mooin4', 'sequential'),
                'type' => PARAM_INT,
            );

            $courseformatoptions['sectiontype'] = array(
                'default' => get_config('format_mooin4', 'sectiontype'),
                'type' => PARAM_TEXT,
            );

            $courseformatoptions['mooin4tyle'] = array(
                'default' => get_config('format_mooin4', 'mooin4tyle'),
                'type' => PARAM_TEXT,
            );

            for ($i = 1; $i <= 20; $i++) {
                // TODO Check if we can set up the Number of courses Kategorie
                // it was 12 Modules or Kategories
                $divisortext = get_config('format_mooin4', 'divisortext'.$i);
                if (!$divisortext) {
                    $divisortext = '';
                }
                $courseformatoptions['divisortext'.$i] = array(
                    'default' => $divisortext,
                    'type' => PARAM_TEXT,
                );
                $courseformatoptions['divisor'.$i] = array(
                    'default' => get_config('format_mooin4', 'divisor'.$i),
                    'type' => PARAM_INT,
                );
            }

            $colorcurrent = get_config('format_mooin4', 'colorcurrent');
            if (!$colorcurrent) {
                $colorcurrent = '';
            }

            $courseformatoptions['colorcurrent'] = array(
                'default' => $colorcurrent,
                'type' => PARAM_TEXT,
            );

            $colorvisible = get_config('format_mooin4', 'colorvisible');
            if (!$colorvisible) {
                $colorvisible = '';
            }

            $courseformatoptions['colorvisible'] = array(
                'default' => $colorvisible,
                'type' => PARAM_TEXT,
            );
        }

        if ($foreditform && !isset($courseformatoptions['coursedisplay']['label'])) {
            $courseconfig = get_config('moodlecourse');

            $max = $courseconfig->maxsections ; //100
            if (!isset($max) || !is_numeric($max)) {
                $max = 52;
            }
            
            $sectionmenu = array();
            for ($i = 0; $i <= $max; $i++) {
                $sectionmenu[$i] = "$i";
            }

            $courseformatoptionsedit['numsections'] = array(
                'label' => new lang_string('numberweeks'),
                'element_type' => 'select',
                'element_attributes' => array($sectionmenu),
            );
            // Add the coursedisplay option edit
            $courseformatoptionsedit['coursedisplay'] = array(
                'label' => new lang_string('coursedisplay'),
                'element_type' => 'select',
                'element_attributes' => array(
                    array(
                        COURSE_DISPLAY_MULTIPAGE => new lang_string('coursedisplay_multi'),
                        COURSE_DISPLAY_SINGLEPAGE => new lang_string('coursedisplay_single')
                    )
                ),
                'help' => 'coursedisplay',
                'help_component' => 'moodle',
            );
            
            $courseformatoptionsedit['hiddensections'] = array(
                'label' => new lang_string('hiddensections'),
                'help' => 'hiddensections',
                'help_component' => 'moodle',
                'element_type' => 'select',
                'element_attributes' => array(
                    array(
                        0 => new lang_string('hiddensectionscollapsed'),
                        1 => new lang_string('hiddensectionsinvisible')
                    )
                ),
            );

            /* $courseformatoptionsedit['navsections'] = array(
                'label' => get_string('navsections', 'format_mooin4'),
                'help' => 'navsections',
                'help_component' => 'moodle',
                'element_type' => 'select',
                'element_attributes' => array(
                    array(
                        0 => get_string('navsectionscards', 'format_mooin4'),
                        1 => get_string('navsectionstabs', 'format_mooin4')
                    )
                ),
            ); */
            $courseformatoptionsedit['showdefaultsectionname'] = array(
                'label' => get_string('showdefaultsectionname', 'format_mooin4'),
                'help' => 'showdefaultsectionname',
                'help_component' => 'format_mooin4',
                'element_type' => 'select',
                'element_attributes' => array(
                    array(
                        1 => get_string('yes', 'format_mooin4'),
                        0 => get_string('no', 'format_mooin4'),
                    ),
                ),
            );

            $courseformatoptionsedit['sectionposition'] = array(
                'label' => get_string('sectionposition', 'format_mooin4'),
                'help' => 'sectionposition',
                'help_component' => 'format_mooin4',
                'element_type' => 'select',
                'element_attributes' => array(
                    array(
                        0 => get_string('above', 'format_mooin4'),
                        1 => get_string('below', 'format_mooin4'),
                    ),
                ),
            );

            $courseformatoptionsedit['inlinesections'] = array(
                'label' => get_string('inlinesections', 'format_mooin4'),
                'help' => 'inlinesections',
                'help_component' => 'format_mooin4',
                'element_type' => 'select',
                'element_attributes' => array(
                    array(
                        1 => get_string('yes', 'format_mooin4'),
                        0 => get_string('no', 'format_mooin4'),
                    ),
                ),
            );

            $courseformatoptionsedit['sequential'] = array(
                'label' => get_string('sequential', 'format_mooin4'),
                'help_component' => 'format_mooin4',
                'element_type' => 'select',
                'element_attributes' => array(
                    array(
                        0 => get_string('notsequentialdesc', 'format_mooin4'),
                        1 => get_string('sequentialdesc', 'format_mooin4'),
                    ),
                ),
            );

            $courseformatoptionsedit['sectiontype'] = array(
                'label' => get_string('sectiontype', 'format_mooin4'),
                'help_component' => 'format_mooin4',
                'element_type' => 'select',
                'element_attributes' => array(
                    array(
                        'numeric' => get_string('numeric', 'format_mooin4'),
                        'roman' => get_string('roman', 'format_mooin4'),
                        'alphabet' => get_string('alphabet', 'format_mooin4'),
                    ),
                ),
            );

            $courseformatoptionsedit['mooin4tyle'] = array(
                'label' => get_string('mooin4tyle', 'format_mooin4'),
                'help_component' => 'format_mooin4',
                'element_type' => 'select',
                'element_attributes' => array(
                    array(
                        'circle' => get_string('circle', 'format_mooin4'),
                        'square' => get_string('square', 'format_mooin4'),
                    ),
                ),
            );

            for ($i = 1; $i <= $max; $i++) {
                // it was 12 Modules or Kategories
                $courseformatoptionsedit['divisortext'.$i] = array(
                    'label' => get_string('divisortext', 'format_mooin4', $i),
                    'help' => 'divisortext',
                    'help_component' => 'format_mooin4',
                    'element_type' => 'text',
                );
                $courseformatoptionsedit['divisor'.$i] = array(
                    'label' => get_string('divisor', 'format_mooin4', $i),
                    'help' => 'divisortext',
                    'help_component' => 'format_mooin4',
                    'element_type' => 'select',
                    'element_attributes' => array($sectionmenu),
                );
                /* echo('SECTION MENU');
                echo'<pre>';
                    print_r($sectionmenu);
                echo'</pre>'; */
            }

            $courseformatoptionsedit['colorcurrent'] = array(
                'label' => get_string('colorcurrent', 'format_mooin4'),
                'help' => 'colorcurrent',
                'help_component' => 'format_mooin4',
                'element_type' => 'text',
            );

            $courseformatoptionsedit['colorvisible'] = array(
                'label' => get_string('colorvisible', 'format_mooin4'),
                'help' => 'colorvisible',
                'help_component' => 'format_mooin4',
                'element_type' => 'text',
            );
            

            $courseformatoptions = array_merge_recursive($courseformatoptions, $courseformatoptionsedit);
        }
        // var_dump($courseconfig);
        return $courseformatoptions;
    }

       /**
     * Returns the format's settings and gets them if they do not exist.
     * @param bool $invalidate Invalidate the existing known settings and get a fresh set.  Set when you know the settings have changed.
     * @return array The settings as an array.
     */
    public function get_settings($invalidate = false) {
        if ($invalidate) {
            $this->settings = null;
        }
        if (empty($this->settings) == true) {
            $this->settings = $this->get_format_options();
            foreach ($this->settings as $settingname => $settingvalue) {
                if (isset($settingvalue)) {
                    $settingvtype = gettype($settingvalue);
                    if ((($settingvtype == 'string') && ($settingvalue === '-')) ||
                        (($settingvtype == 'integer') && ($settingvalue === 0))) {
                        // Default value indicator is a hyphen or a number equal to 0.
                        $this->settings[$settingname] = get_config('format_mooin4', 'default'.$settingname);
                    }
                }
            }
        }

        return $this->settings;
    }

    /**
     * update_course_format_options
     *
     * @param stdclass|array $data
     * @param stdClass $oldcourse
     * @return bool
     */
    public function update_course_format_options($data, $oldcourse = null) {
        global $DB;

        $currentsettings = $this->get_settings();
        // var_dump($currentsettings);

        $data = (array)$data; //(array)
        echo("data \n");
        
        if ($oldcourse !== null) {
            $oldcourse = (array)$oldcourse;

            $options = $this->course_format_options();

            foreach ($options as $key => $unused) {
                echo("Key \n");
                if (!array_key_exists($key, $data)) {
                    if (array_key_exists($key, $oldcourse)) {
                        $data[$key] = $oldcourse[$key];
                    } else if ($key === 'numsections') {
                        $maxsection = $DB->get_field_sql('SELECT max(section) from
                        {course_sections} WHERE course = ?', array($this->courseid));
                        if ($maxsection) {
                            $data['numsections'] = $maxsection;
                        }
                    }
                }
            }
        }

        $changed = $this->update_format_options($data);
        echo("update course format");
        
        if ($changed && array_key_exists('numsections', $data)) {
            $numsections = (int)$data['numsections'];
            $sql = 'SELECT max(section) from {course_sections} WHERE course = ?';
            $maxsection = $DB->get_field_sql($sql, array($this->courseid));
            for ($sectionnum = $maxsection; $sectionnum > $numsections; $sectionnum--) {
                if (!$this->delete_section($sectionnum, false)) {
                    break;
                }
            }
        }

        return $changed;//$data

    }

    /**
     * get_view_url
     *
     * @param int|stdclass $section
     * @param array $options
     * @return null|moodle_url
     */
    public function get_view_url($section, $options = array()) {
        global $CFG;

        $course = $this->get_course();
        $card_id = 0;
        // $course_name = $course->shortname;

        //$url = new moodle_url('/course/format/mooin4/infos.php', array('id' => $course->id)); 
        $url = new moodle_url('/course/view.php', array('id' => $course->id)); // anchor: 'card_id_' . $card_id
        
        $sr = null;

        if (array_key_exists('sr', $options)) {
            $sr = $options['sr'];
        }

        if (is_object($section)) {
            $sectionno = $section->section;
        } else {
            $sectionno = $section;
        }
        
        if ($sectionno !== null) {
            if ($sr !== null) {
                if ($sr) {
                    
                    $usercoursedisplay = COURSE_DISPLAY_MULTIPAGE;
                    $sectionno = $sr;
                } else {
                    $usercoursedisplay = COURSE_DISPLAY_SINGLEPAGE;
                }
            } else {
                // var_dump($options['navigation']);
                $usercoursedisplay = $course->coursedisplay;
            }
            // $url->param('lektion', $sectionno);
            if ($sectionno != 0 && $usercoursedisplay == COURSE_DISPLAY_MULTIPAGE) {
                $url->param('section', $sectionno);
            } else {
                if (empty($CFG->linkcoursesections) && !empty($options['navigation'])) {
                    return null;
                }
                $url->param('section', $sectionno);
                
            }
        }
        // echo("url 2 iF \n $url");
        return $url;
    }
    /**
     * Returns the information about the ajax support in the given source format
     *
     * The returned object's property (boolean)capable indicates that
     * the course format supports Moodle course ajax features.
     *
     * @return stdClass
     */
   /*  public function supports_ajax() {
        $ajaxsupport = new stdClass();
        $ajaxsupport->capable = true;
        return $ajaxsupport;
    } */

  /**
     * Loads all of the course sections into the navigation.
     *
     * @param global_navigation $navigation
     * @param navigation_node $node The course node within the navigation
     * @return void
     */
    public function extend_course_navigation($navigation, navigation_node $node) {
        global $PAGE;
        // If section is specified in course/view.php, make sure it is expanded in navigation.
        if ($navigation->includesectionnum === false) {
            $selectedsection = optional_param('section', null, PARAM_INT);
            if ($selectedsection !== null && (!defined('AJAX_SCRIPT') || AJAX_SCRIPT == '0') &&
                    $PAGE->url->compare(new moodle_url('/course/view.php'), URL_MATCH_BASE)) {
                $navigation->includesectionnum = $selectedsection;
            }
        }

        // Check if there are callbacks to extend course navigation.
        parent::extend_course_navigation($navigation, $node);

        // We want to remove the general section if it is empty.
        $modinfo = get_fast_modinfo($this->get_course());
        $sections = $modinfo->get_sections();
        if (!isset($sections[0])) {
            // The general section is empty to find the navigation node for it we need to get its ID.
            $section = $modinfo->get_section_info(0);
            $generalsection = $node->get($section->id, navigation_node::TYPE_SECTION);
            if ($generalsection) {
                // We found the node - now remove it.
                $generalsection->remove();
            }
        }
    }

    public function get_context() {

        global $SITE;
        if($SITE->id == $this->courseid) {
            //Use the context of the page with should be the course category
            global $PAGE;
            return $PAGE->context;
        }else{
            return context_course::instance($this->courseid);
        }
    }
    /**
     * Adds format options elements to the course/section edit form
     *
     * This function is called from {@link course_edit_form::definition_after_data()}
     *
     * @param MoodleQuickForm $mform form the elements are added to
     * @param bool $forsection 'true' if this is a section edit form, 'false' if this is course edit form
     * @return array array of references to the added form elements
     */

    public function create_edit_form_elements(&$mform, $forsection = false) {
        global $CFG, $OUTPUT, $PAGE, $USER, $COURSE, $DB;

        $elements = parent::create_edit_form_elements($mform, $forsection);
        
        if(!$forsection && (empty($COURSE->id) || $COURSE->id == SITEID)) {
            // Add "numsections" element to the create course form - it will force new course to be prepopulated
            // with empty sections.
            // The "Number of sections" option is no longer available when editing course, instead teachers should
            // delete and add sections when needed.

            $maxsections = get_config('moodlecourse', 'maxsections');
            $numsections = $mform->getElementValue('numsections');
            $numsections = $numsections[0];

            if($numsections > $maxsections) {
                $element = $mform->getElement('numsections');
                for($i = $maxsections + 1; $i <= $numsections; $i++){
                    $element->addOption("$i", $i);
                }
            }
        }

        // $context = $this->get_context();
        $this->content = new stdClass();

        static $courseformatoptions = false;
        $courseconfig = get_config('moodlecourse');

        $options = $this->course_format_options();
        
        // Add new Settings in course Format mooin4 Lib for the inhaltsverzeichnis card.
        $attributes=array('size'=>'20');
        
        $elements[] = $mform->addElement('header', 'mooin4_inhalt_header', get_string('mooin4_inhalt_header', 'format_mooin4'));
        $mform->addHelpButton('mooin4_inhalt_header', 'mooin4_inhalt_header', 'format_mooin4', true);
        $elements[] = $mform->addElement('hidden', 'config_chapEnable', '', array('id' => 'id_config_chapEnable') ); // get_string('mooin4_inhalt_header', 'format_mooin4')
        //$elements[] = $mform->setDefault('config_chapEnable', 0);
        //$elements[] = $mform->setType('config_chapEnable', PARAM_INT);
        
       
        return $elements;
    }

    /**
     * Returns true if the course has a front page.
     *
     * @return boolean false
     */
    public function has_view_page() {
        return true;
    }
    
    // >>> Online users map lib
        /**
     * Generate the HTML content for the google map
     *
     * @return string HTML string to display google map
     */
    function get_html_googlemap($the_real_course_id, $local_lat = '', $local_lng = '', $local_zoom = 0){
        global $CFG, $USER;

        $retStr = "<script src='https://maps.googleapis.com/maps/api/js?sensor=false&key=AIzaSyB9aGvzBI_6uFkLuFsg6EVEmFPLw2_6yPI' type='text/javascript'></script>";
        $retStr = "<script src='https://maps.googleapis.com/maps/api/js?sensor=false&key=AIzaSyCtJMjs-3xW1VK_lwUm9UORpeI4zA1qVuM' type='text/javascript'></script>";
        $retStr .= "<link rel='stylesheet' type='text/css' href='".$CFG->wwwroot."/blocks/online_users_map/style.css' />";
        $retStr .= "<div id='mooin4_online_users_googlemap'></div>";
        $retStr .= "<script type='text/javascript' src='".$CFG->wwwroot."/mooin4/online_users_map/online_users_map.php?courseid=".$the_real_course_id."&local_lat=".$local_lat."&local_lng=".$local_lng."&local_zoom=".$local_zoom."' defer='defer'></script>";
        return $retStr;
    }

    /**
     * Generate the HTML content for the OSM map
     *
     * @return string HTML string to display OSM map
     */
    function get_html_osmmap(){
        global $CFG;
        $retStr = "<script type='text/javascript' src='".$CFG->wwwroot."/blocks/online_users_map/online_users_map_osm.php' defer='defer'></script>";
        $retStr .= "<script src='http://www.openlayers.org/api/OpenLayers.js'></script>";
        $retStr .= "<link rel='stylesheet' type='text/css' href='".$CFG->wwwroot."/blocks/online_users_map/style.css' />";
        $retStr .= "<script src='http://www.openstreetmap.org/openlayers/OpenStreetMap.js'></script>";
        $retStr .= "<div id='mooin4_online_users_osmmap'></div>"; 
        return $retStr;
    }

    /**
     * Updates the lat/lng for users
     * @uses $CFG,$DB
     */
    function update_users_locations(){
        global $CFG,$DB;
        //get all the users without a lat/lng
        $sql = "SELECT u.id, u.city, u.country, boumc.id AS b_id, u.firstname, u.lastname 
                    FROM {user} u
                    LEFT OUTER JOIN {mooin4_online_users_map} boumc
                    ON  u.id = boumc.userid
                    WHERE (boumc.id IS NULL
                    OR u.city != boumc.city     
                    OR u.country != boumc.country)
                    AND u.city != ''";

        if($CFG->mooin4_online_users_map_update_limit == 0){
            $results = $DB->get_records_sql($sql,array());
        } else {
            $results = $DB->get_records_sql($sql,array(),0,$CFG->mooin4_online_users_map_update_limit);
        }
        
        if (!$results){
            if ($CFG->mooin4_online_users_map_debug){
                //echo "\nThere are no locations to update." ; 
                mtrace("There are no locations to update."); 
            }
            return true;
        }
        //loop through results and get location for each user
        mtrace('Updating '.count($results).' users...');
        //die('cancel...');
        //$cities = array('Spunciems', 'Neveronys', 'Risskov', 'Ramdala', 'Roiu');
        foreach ($results as $user){
            //  if (!in_array($user->city, $cities)) {
            //      continue;
            //  }
            if ($CFG->mooin4_online_users_map_debug){
                //echo "\nUpdating location for ".$user->firstname." ".$user->lastname." (looking up: ".$user->city.",".$user->country." )...";  
                mtrace("Updating location for ".$user->firstname." ".$user->lastname." (looking up: ".$user->city.", ".$user->country." )...");  
            }
            //get the coordinates:
            $response = getURLContent($CFG->mooin4_online_users_map_geonamesurl,"/search?username=".$CFG->mooin4_online_users_map_geonamesusername."&maxRows=1&q=".urlencode($user->city)."&country=".urlencode($user->country));
            
            if($response != "" && $xml = simplexml_load_string($response)){
                $boumc = new StdClass;
                if (isset($xml->geoname->lat)){
                    $boumc->userid = $user->id;
                    $boumc->lat = floatval($xml->geoname->lat);
                    $boumc->lng = floatval($xml->geoname->lng);
                    $boumc->city = $user->city;
                    $boumc->country = $user->country;
                    //echo "\nuserid: $boumc->userid lat: $boumc->lat lng: $boumc->lng city: $boumc->city country: $boumc->country";
                    
                    //if existing record from mooin4_online_users_map then update
                    if (isset($user->b_id)){    
                        //echo "\nupdating record";
                        $boumc->id = $user->b_id;
                        $DB->update_record("mooin4_online_users_map",$boumc);
                    } else {            
                        //else create a new record
                        //echo "\ninserting record";
                        $DB->insert_record("mooin4_online_users_map",$boumc);
                    }
                    if ($CFG->mooin4_online_users_map_debug){
                        //echo "\n\tlocation updated" ;  
                        mtrace("location updated");
                    }
                } else {
                if ($CFG->mooin4_online_users_map_debug){
                    //echo "\n\tlocation not found in Geonames database" ;  
                    mtrace("location not found in Geonames database");
                } 
                }
            } else {
                if ($CFG->mooin4_online_users_map_debug){
                    //echo "\n\tlocation not found due to no or invalid response" ;  
                    mtrace("location not found due to no or invalid response");
                }
            }
        }
    }

    /**
     * Gets the content of a url request
     * @uses $CFG
     * @return String body of the returned request
     */
    function getURLContent($domain,$path){

        global $CFG;

        $message = "GET $domain$path HTTP/1.0\r\n";
        $msgaddress = str_replace("http://","",$domain);
        $message .= "Host: $msgaddress\r\n";
        $message .= "Connection: Close\r\n";
        $message .= "\r\n";
        
        if($CFG->proxyhost != "" && $CFG->proxyport != 0){
            $address = $CFG->proxyhost;
            $port = $CFG->proxyport;
        } else {
            $address = str_replace("http://","",$domain);
            $port = 80;
        }

        /* Attempt to connect to the proxy server to retrieve the remote page */
        if(!$socket = fsockopen($address, $port, $errno, $errstring, 20)){
            echo "Couldn't connect to host $address: $errno: $errstring\n";
            return "";
        }

        fwrite($socket, $message);
        $content = "";
        while (!feof($socket)){
                $content .= fgets($socket, 1024);
        }

        fclose($socket);
        $retStr = extractBody($content);
        return $retStr;
    }

    /**
     * removes the headers from a url response
     * @return String body of the returned request
     */
    function extractBody($response){

        $crlf = "\r\n";
        // split header and body
        $pos = strpos($response, $crlf . $crlf);
        if($pos === false){
            return($response);
        }

        $header = substr($response, 0, $pos);
        $body = substr($response, $pos + 2 * strlen($crlf));
        // parse headers
        $headers = array();
        $lines = explode($crlf, $header);

        foreach($lines as $line){
            if(($pos = strpos($line, ':')) !== false){
                $headers[strtolower(trim(substr($line, 0, $pos)))] = trim(substr($line, $pos+1));
            }
        }

        return $body;

    }

    /**
     * Gets the timetosee value
     * @uses $CFG
     * @return Integer
     */
    function getTimeToShowUsers(){
        global $CFG;
        $timetoshowusers = 300; //Seconds default
        if (isset($CFG->mooin4_online_users_map_timetosee)) {
            $timetoshowusers = $CFG->mooin4_online_users_map_timetosee * 60;
        }
        return $timetoshowusers;
    }


    /**
     * Gets the lat/lng coords of the current user
     * @uses $CFG,$USER,$DB
     * @return Array of decimal
     */
    function getCurrentUserLocation(){
        global $CFG,$USER,$DB;
        $coords = array();
        
        $sql = "SELECT boumc.userid, boumc.lat, boumc.lng 
                FROM {mooin4_online_users_map} boumc 
                WHERE userid=?";
        $c = $DB->get_record_sql($sql,array($USER->id));
        if($c){
            $coords['lat'] = $c->lat;
            $coords['lng'] = $c->lng;
        }
        return $coords;
    }
}
// >>> Remove all the map function in the futur.
/**
 * Implements callback inplace_editable() allowing to edit values in-place
 *
 * @param string $itemtype
 * @param int $itemid
 * @param mixed $newvalue
 * @return \core\output\inplace_editable
 */
function format_mooin4_inplace_editable($itemtype, $itemid, $newvalue) {
        global $DB, $CFG;

        require_once($CFG->dirroot . '/course/lib.php');

        if ($itemtype === 'sectionname' || $itemtype === 'sectionnamenl') {
            $section = $DB->get_record_sql(
                'SELECT s.* FROM {course_sections} s JOIN {course} c ON s.course = c.id WHERE s.id = ? AND c.format = ?',
                array($itemid, 'mooin4'),
                MUST_EXIST
            );
            
            return course_get_format($section->course)->inplace_editable_update_section_name($section, $itemtype, $newvalue);
        }
        return null;
}
