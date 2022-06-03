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
 * Add new Section (Lektion) Title in Chapter Card.
 *
 * @module     format_mooin4/add
 * @copyright  2022 Perial Dupont Nguefack Kuaguim
 */
 define(['jquery'], ($) => {
    // Var addSection = $('.chapteraddsection');
    var chapterId = 0;
    var chapterSectionNumber = 0;
    $('.chapteraddsection').click(function(event) {
        // Get all the data for Chapter so that when a new section is added, we just update the right chapter in DB
        Y.log(event);
        chapterId = event.currentTarget.classList[2].substring(7, event.currentTarget.classList[2].length);
        let idC = $('.id_chapter' + chapterId);
        let titleC = $('.chapter_title' + chapterId);
        let sectionCount = $('.chapter_sectionnumber' + chapterId);
        let courseIdC = $('.id_course' + chapterId);
        // Increase the section nummber in chapter
        chapterSectionNumber = parseInt(sectionCount[0].defaultValue) + 1;
        Y.log('Add new Section increase + 1 : ' + chapterId);
        var dataChapter = {};
        dataChapter.id = parseInt(idC[0].defaultValue);
        dataChapter.courseid = parseInt(courseIdC[0].defaultValue);
        dataChapter.chapter_title = titleC[0].defaultValue;
        dataChapter.sectionid = parseInt(chapterId);
        dataChapter.sectionnumber = chapterSectionNumber;
        Y.log(dataChapter);
        $.ajax({
            type: 'POST',
            data: dataChapter,
            url: 'add_new_section.php',
            success: (dataChapter) => {
                Y.log(dataChapter);
                window.location.reload();
            }
        });
        // Window.location.reload();
    });
 });