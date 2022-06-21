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
 * Change the position for a chapter in table of content (Chapter content one or many lesson),
 * By the way we have to update the format_mooin4_chapter, format_mooin4_section, course_sections Table and the lib.php content data
 *
 * @module     format_mooin4/confirm_chapter_move
 * @copyright  2022 Perial Dupont Nguefack Kuaguim
 */

// What is important here is the current chapter that we want to move inside our table of content
define(['jquery'], ($) =>{
    // Var triggerUp = $('.up');
    // Var triggerDown = $('.down');
    var sec_number_cur_chap = '';
    var courseId = '';
    var cur_chapter_index = 0;

    // Move the chapter Up
    $('.up, .down').click(function(event) {
        cur_chapter_index = event.currentTarget.classList[2].substring(7, event.currentTarget.classList[2].length);
        let sectionCount = $('.chapter_sectionnumber' + cur_chapter_index);
        let idC = $('.id_chapter' + cur_chapter_index);
        let titleC = $('.chapter_title' + cur_chapter_index);
        courseId = $('.id_course' + cur_chapter_index);
        // Get the right section number in the currrent chapter.
        sec_number_cur_chap = parseInt(sectionCount[0].defaultValue);
        let direction = $(this).attr("class").split(' ');
        var dataChapter = {};
        dataChapter.id = parseInt(idC[0].defaultValue);
        dataChapter.courseid = parseInt(courseId[0].defaultValue);
        dataChapter.chapter_title = titleC[0].defaultValue;
        dataChapter.sectionid = parseInt(cur_chapter_index);
        dataChapter.sectionnumber = sec_number_cur_chap;
        dataChapter.direction = direction[3];
        $.ajax({
            type: 'POST',
            data: dataChapter,
            url: 'chapter_move.php',
            success: (dataChapter) => {
                Y.log('Data Chapter : ' + dataChapter);
                window.location.reload();
            },
        });
    });

    // Move the chapter Down
});