<?php
header('Content-Type: text/html; charset=utf-8'); // sorgt für die korrekte Kodierung
header('Cache-Control: must-revalidate, pre-check=0, no-store, no-cache, max-age=0, post-check=0'); // ist mal wieder wichtig wegen IE
	
	require_once('../../../config.php');
	global $DB, $USER, $CFG;
	
    // Check Section complete or not
    $PAGE->requires->js_call_amd('format_mooin4/section_complete');

	$sectionid = optional_param('sectionid', 0, PARAM_INT);
	$userid = optional_param('userid', 0, PARAM_INT);
	$courseid = optional_param('courseid', 0, PARAM_INT);
	
	if ($userid == $USER->id) {
        // get the exact string of data when the  user push the button section complete [userid, courseid, sectionid]
		$q = intval($_GET['q']);
        $com_q = explode(' ', $q);
        $id = $DB->count_records('user_prefenreces');
        if ($userid == $com_q[0] && $courseid == $com_q[1] && $userid == $com_q[2]) {
            
            //Insert Data in user_preferences table
            //$DB->insert_record($table, $dataobject, $returnid=true, $bulk=false)
            $values = new stdClass();
            $values->id = $id + 1;
            $values->userid = $userid;
            $values->name = 'section_progess'.$q;
            $values->value = $q;

            $DB->insert_record('user_preferences',$values, true, false );
        }
	}
	else {
		echo 0;
	}