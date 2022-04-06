<?php

include_once("../../../config.php");
// change include_once($CFG->dirroot.'/blocks/online_users_map/lib.php'); to --, because all the implementation for the old file is now in the ../mooin4/lib.php

include_once($CFG->dirroot.'../mooin4/lib.php');

$callback = optional_param('callback', '', PARAM_ALPHA);
$courseid = optional_param('courseid', 1, PARAM_INT);

$timefrom = 100 * floor((time()-getTimeToShowUsers()) / 100); // Round to nearest 100 seconds for better query cache

// Get context so we can check capabilities.
$context = get_context_instance(CONTEXT_COURSE, $courseid);

//Calculate if we are in separate groups
$isseparategroups = ($COURSE->groupmode == SEPARATEGROUPS
                     && $COURSE->groupmodeforce
                     && !has_capability('moodle/site:accessallgroups', $context));

//Get the user current group
$currentgroup = $isseparategroups ? get_and_set_current_group($COURSE, groupmode($COURSE)) : NULL;

$groupmembers = "";
$groupselect = "";
$users = array();

$counter = 0;
 
$select = "SELECT u.id, u.username, u.firstname, u.lastname, u.city, boumc.lat, boumc.lng ";
$from = "FROM {user} u,
			  {mooin4_online_users_map} boumc,
			  {enrol} e,
			  {user_enrolments} ue ";
$where =  "WHERE boumc.userid = u.id 
		   AND u.id = ue.userid 
		   AND ue.enrolid = e.id 
		   AND e.courseid = '".$courseid."' ";
$order = "ORDER BY u.lastname DESC ";

$groupby = "GROUP BY u.id, u.username, u.firstname, u.lastname, u.city, u.picture, boumc.lat, boumc.lng ";

$SQLwithLL = $select . $from . $where . $groupby . $order;
 

if ($pusers = $DB->get_records_sql($SQLwithLL, array(),0, 9000)) {   // We'll just take the most recent 9000 maximum
    foreach ($pusers as $puser) {

		if($CFG -> mooin4_online_users_map_has_names) {
            $puser->fullname = fullname($puser);
        } else {
            $puser->fullname = $puser->city;
        }
        unset($puser->id);
        unset($puser->username);
        unset($puser->lastname);
        unset($puser->firstname);
        unset($puser->lastaccess);
        $puser->online = "true";
        $users[$counter] = $puser;  
        $counter++;
    }
}  

// added by oncampus
// Nutzer aus der selben Stadt zu einem Nutzer zusammenfassen und Anzahl mit �bergeben
if(!$CFG -> mooin4_online_users_map_has_names) {
	$sorted_users = array();
	foreach ($users as $user) {
		$latlng = $user->lat.','.$user->lng;
		//if ($sorted_users[$user->fullname]) {
		if (isset($sorted_users[$latlng])) {
			$u = $sorted_users[$latlng];
			$u->counter++;
			$sorted_users[$latlng] = $u;
		}
		else {
			$user->counter = 1;
			$user->online = "true";
			$sorted_users[$latlng] = $user;
		}
	}
	$counter = 0;
	$users = array();
	foreach ($sorted_users as $fn => $user) {
		$users[$counter] = $user;
		$counter++;
	}
}
// oncampus end

header("Content-type: text/plain");
echo $callback."(".json_encode($users).")";

?>
