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
 * Delete a chapter in table of content (Chapter content one or many lesson).
 *
 * @module     format_mooin4/confirm_chapter
 * @copyright  2022 Perial Dupont Nguefack Kuaguim
 */

define(['jquery', 'core/modal_factory', 'core/str', 'core/modal_events', 'core/notification'],
($, ModalFactory, String, ModalEvents, Notification) => { // Notification
    var trigger = $('.chapter_delete');

    var chapterSectionNumber = 0;
    var chapterIndex = 0;
    // Var val = 1;
    var courseIdC = '';
    trigger.click(function(event) {
        chapterIndex = event.currentTarget.classList[2].substring(7, event.currentTarget.classList[2].length);

        let sectionCount = $('.chapter_sectionnumber' + chapterIndex);
        let idC = $('.id_chapter' + chapterIndex);
        // TitleC = $('.chapter_title' + chapterIndex);
        courseIdC = $('.id_course' + chapterIndex);

        window.chapterSectionNumber = parseInt(sectionCount[0].defaultValue);
        var dataChapter = {};
        dataChapter.id = parseInt(idC[0].defaultValue);
        dataChapter.courseid = parseInt(courseIdC[0].defaultValue);
        // DataChapter.chapter_title = titleC[0].defaultValue;
        dataChapter.sectionid = parseInt(chapterIndex);
        dataChapter.sectionnumber = chapterSectionNumber;
        Y.log("1 Chapter section number :" + window.chapterSectionNumber);
        let val = window.chapterSectionNumber;
        if (val > 0) {
            Y.log("Chapter section number > 0:" + val);
            ModalFactory.create({
                type: ModalFactory.types.CONFIRM,
                title: String.get_string('delete_chapter', 'format_mooin4'), // 'Title delete Section',
                body: String.get_string('chapter_not_empty', 'format_mooin4'),
                preShowCallback: function(triggerElement, modal) {
                    triggerElement = $(triggerElement);
                    modal.params = {
                    'sectionnumber': chapterSectionNumber};
                    // Modal.setSaveButtonText(String.get_string('chapter_okey', 'format_mooin4'));
                    // modal.setCancelButton.dismiss();
                    // modal.setConfirmButtonText(String.get_string('chapter_okey', 'format_mooin4'));
                },
                large: true,
            }, trigger)
            .done(function(modal) {
                modal.getRoot().on(ModalEvents.save, function(e) {
                    e.preventDefault();
                    location.reload();
                });
            });
        }
        if (val == 0) {
            Y.log("Chapter section number == 0 :" + val);
            ModalFactory.create({
                type: ModalFactory.types.SAVE_CANCEL,
                title: String.get_string('delete_chapter', 'format_mooin4'), // 'Title delete Section',
                body: String.get_string('chapter_empty', 'format_mooin4'),
                preShowCallback: function(triggerElement, modal) {
                    triggerElement = $(triggerElement);
                    modal.params = {
                    'courseid': parseInt(courseIdC[0].defaultValue),
                    'chapterid': parseInt(chapterIndex),
                    'sectionnumber': chapterSectionNumber};
                    modal.setSaveButtonText(String.get_string('delete_chapter', 'format_mooin4'));
                },
                large: true,
            }, trigger)
            .done(function(modal) {
                modal.getRoot().on(ModalEvents.save, function(e) {
                    e.preventDefault();
                    let footer = Y.one('.modal-footer');
                    footer.setContent('Deleting chapter...');
                    let spinner = M.util.add_spinner(Y, footer);
                    spinner.show();
                    Y.log(modal.params);
                    var deletedata = {};
                    deletedata.courseid = modal.params.courseid;
                    deletedata.chapterid = modal.params.chapterid;
                    deletedata.sectionnumber = modal.params.sectionnumber;
                    $.ajax({
                        type: 'POST',
                        data: deletedata,
                        url: 'delete_chapter.php',
                        success: (deletedata) => {
                            Y.log(deletedata);
                            Notification.addNotification({
                                message: ' You have successfully deleted the chapter',
                                type: 'success'
                            });
                            window.location.reload();
                        },
                    });
                });
            });
        }
    });
});