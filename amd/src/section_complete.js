/* eslint-disable camelcase */
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
 * Get the Section Data to set the section as completed when the user click on the button in bottom the page
 *
 * @module     format_mooin4/section_complete
 * @copyright  2022 Perial Dupont Nguefack Kuaguim
 */

// What is important here is the current chapter that we want to move inside our table of content
define(['jquery'], ($) =>{
    var section_count = $(".bottom_complete").length;
    Y.log('Section Count', section_count);
    $('.bottom_complete').click(function(event) {
        Y.log(event);
    });
});