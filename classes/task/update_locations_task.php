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
 * A scheduled task for mooin4_online_users_map to update the user locations, previously it was a block_online_users_map package.
 *
 * 
 * @author		Jan Rieger <jan.rieger@fh-luebeck.de>
 * @license		http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mooin4_online_users_map\task;

class update_locations_task extends \core\task\scheduled_task {

	/**
	 * Get a descriptive name for this task (shown to admins).
	 *
	 * @return string
	 */
	public function get_name() {
		return 'update user locations';
	}

	/**
	 * Run task.
	 */
	public function execute() {
		global $CFG;
		//set_time_limit ( 120 );
		require_once($CFG->dirroot.'../mooin4/lib.php');
        update_users_locations();
	}

}
