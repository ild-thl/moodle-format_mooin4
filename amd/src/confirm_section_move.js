/* eslint-disable no-unused-vars */
/* eslint-disable require-jsdoc */
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
 * Change the position for section inside chapter in table of content (Chapter content one or many lesson),
 * By the way we have to update the format_mooin4_chapter, format_mooin4_section, course_sections Table and the lib.php content data
 *
 * @module     format_mooin4/confirm_section_move
 * @copyright  2022 Perial Dupont Nguefack Kuaguim
 */

/* What is important here is the current section and chapter where we are, and the destionation chapter where we want to move
the selected section inside our table of content*/


define(['jquery', 'jqueryui'], ($, jqueryui) =>{
    var section_count = $("#chapter_body .section_find").length;
    var chapter_count = $(".card #chapter_body").length;

    for (let j = 1; j < chapter_count + 1; j++) {
      for (let i = 1; i < section_count + 1; i++) {
        var val = `#section_move${j}${i}`;
        Y.log(typeof (val));
        $(val).sortable({
            connectWith: ".draggable-zone",
            receive: function(event, ui) {
              var x = $(this).data().uiSortable.bindings[0].classList;

              var cur_chap = $(this).data().uiSortable.bindings[0].classList[3].replace('chapter', '');
              var cur_section = $(this).data().uiSortable.bindings[0].classList[4].replace('section', '');
              this.cur_ch = cur_chap;
              this.cur_sec = cur_section;
              var cur = cur_chap + cur_section;
              Y.log('drop Data : ' + cur);
              var first_chap = $(this).data().uiSortable.bindings[0].childNodes[1].classList[5].replace('chapter-id', '');
              var first_section = $(this).data().uiSortable.bindings[0].childNodes[1].classList[6].replace('section-id', '');
              var course_id = $(this).data().uiSortable.bindings[0].childNodes[1].classList[4].replace('course-id', '');
              var first = first_chap + first_section;
              this.first_ch = first_chap;
              this.first_ch = first_section;
              Y.log('first: : ' + first);
              var last_chap = $(this).data().uiSortable.bindings[0].childNodes[1].nextSibling.classList[5]
              .replace('chapter-id', '');
              var last_section = $(this).data().uiSortable.bindings[0].childNodes[1].nextSibling.classList[6]
              .replace('section-id', '');
              var last = last_chap + last_section;
              this.last_ch = last_chap;
              this.last_sec = last_section;
              Y.log('Last ' + last);
              // Target section
              var target_section = event.target.className;
              Y.log('Target Section' + target_section);
              var section_move = {};
              section_move.courseid = course_id;
              section_move.current_chapter = cur_chap;
              section_move.current_section = cur_section;
              section_move.first_chapter = first_chap;
              section_move.first_section = first_section;
              section_move.last_chapter = last_chap;
              section_move.last_section = last_section;

              if (cur === first) {
                section_move.direction = 'down';
                section_move.chapter_clicked = last_chap;
                section_move.section_clicked = last_section;
              }
              if (cur === last) {
                section_move.direction = 'up';
                section_move.chapter_clicked = first_chap;
                section_move.section_clicked = first_section;
              }
              $.ajax({
                type: 'POST',
                data: section_move,
                url: 'section_move.php',
                success: (section_move) => {
                Y.log('Data Chapter : ' + section_move);
                window.location.reload();
            },
              });
              return true;
            }
          }).disableSelection();
      }
    }
});