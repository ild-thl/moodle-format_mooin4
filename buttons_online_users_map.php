<?php
/**
 * Online Users Map block - reworking of the standard Moodle online users
 * block, but this displays the users on a Google map - using the location
 * given in the Moodle profile.
 * @author Alex Little
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package block_online_users_map
 */
namespace format_button;
include_once('../buttons/lib.php');

use stdClass;

class buttons_online_users_map { 

    function get_content() {
        global $USER, $CFG, $COURSE, $PAGE;

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';

        if (empty($this->instance)) {
            return $this->content;
        }

       

        $oc_descr = html_writer::tag('div', get_string('profile_city_descr', 'format_buttons'));
        $this->content->text .= html_writer::tag('div', $oc_descr, array('style' => 'width:100%; text-align:center;'));

        if ($CFG->buttons_online_users_map_type == 'osm') {
            $this->content->text .= get_html_osmmap();
        } else {
            $local_centre_lat = 53.869;
            $local_centre_lng = 10.687;
            $local_init_zoom = 5;
            if (isset($this->config->local_centre_lat)) {
                $local_centre_lat = $this->config->local_centre_lat;
            }
            if (isset($this->config->local_centre_lng)) {
                $local_centre_lng = $this->config->local_centre_lng;
            }
            if (isset($this->config->local_init_zoom)) {
                $local_init_zoom = $this->config->local_init_zoom;
            }
            $this->content->text .= get_html_googlemap($COURSE->id, $local_centre_lat, $local_centre_lng, $local_init_zoom);
        }

        return $this->content;
    }

    function cron() {
        update_users_locations();
        return true;
    }

    function preferred_width() {
        return 210;
    }

    /*
    public function specialization() {
        if (!empty($this->config->title)) {
            $this->title = $this->config->title;
        } else {
            $this->config->title = get_string('pluginname', 'format_buttons');
        }
    }*/
}

?>
