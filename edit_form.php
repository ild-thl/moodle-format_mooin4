<?php
 
class buttons_online_users_map_edit_form extends block_edit_form {
 
    protected function specific_definition($mform) {

        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

		$mform->addElement('text', 'config_title', get_string('blocktitleonlineusermap', 'format_buttons'));
		$mform->setDefault('config_title', 'default value');
		$mform->setType('config_title', PARAM_MULTILANG);
		
		$mform->addElement('advcheckbox', 'config_onlyusers', get_string('onlyusers', 'format_buttons'));
        $mform->setDefault('config_onlyusers', 1);
		
		$mform->addElement('text', 'config_mapheight', get_string('height', 'format_buttons'), array('size'=>4));
        $mform->setType('config_mapheight', PARAM_INT);
        $mform->setDefault('config_mapheight', 300);
		$mform->addRule('config_mapheight', null, 'required', null, 'client');
		
		$mform->addElement('text', 'config_local_centre_lat', get_string('centrelat', 'format_buttons'));
        $mform->setType('config_map_centre_lat', PARAM_RAW);
        //$mform->setDefault('map_centre_lat', 53.869);
		
		$mform->addElement('text', 'config_local_centre_lng', get_string('centrelng', 'format_buttons'));
        $mform->setType('config_map_centre_lng', PARAM_RAW);
        //$mform->setDefault('map_centre_lng', 10.687);
		
		$mform->addElement('text', 'config_local_init_zoom', get_string('zoomlevel', 'format_buttons'), array('size'=>4));
        $mform->setType('config_map_init_zoom', PARAM_INT);
        //$mform->setDefault('map_init_zoom', 5);
		
    }
}