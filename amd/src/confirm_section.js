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
 * Delete a Section (Lektion) Title in Chapter Card.
 *
 * @module     format_mooin4/confirm_section
 * @copyright  2022 Perial Dupont Nguefack Kuaguim
 */
 define(['jquery', 'core/modal_factory', 'core/str', 'core/modal_events', 'core/notification'],
    ($, ModalFactory, String, ModalEvents, Notification) => {
    var trigger = $('.section_delete');
    var courseIndex = 0;
    var chapterIndex = 0;
    var sectionIndex = 0;
    // Var chapterSection = 0;
    // Var title = '';
    var sectionIdUrl = 0;
    var idC = 0;
    var titleC = '';
    var sectionCount = 0;
    var section_number = 0;
    trigger.click(function(event) {
        courseIndex = event.currentTarget.classList[0].substring(9, event.currentTarget.classList[0].length);
        chapterIndex = event.currentTarget.classList[1].substring(10, event.currentTarget.classList[1].length);
        sectionIndex = event.currentTarget.classList[2].substring(10, event.currentTarget.classList[2].length);
        // Get the section-id base on the url
        const url = event.currentTarget.classList[3];

        idC = $('.id_chapter' + chapterIndex);
        titleC = $('.chapter_title' + chapterIndex);
        sectionCount = $('.chapter_sectionnumber' + chapterIndex);
        sectionIdUrl = url.substring(url.lastIndexOf('=') + 1);
        /* Y.log(sectionIdUrl);
        Y.log('CourseIndex : ' + courseIndex);
        Y.log('ChapterIndex : ' + chapterIndex);
        Y.log('SectionIndex : ' + sectionIndex);
        Y.log('id_chapter : ' + parseInt(idC[0].defaultValue));
        Y.log('Sectionnumber : ' + parseInt(sectionCount[0].defaultValue)); */
        const sectionNum = parseInt(sectionCount[0].defaultValue);
        section_number = sectionNum - 1;

    });

    ModalFactory.create({
        type: ModalFactory.types.SAVE_CANCEL,
        title: String.get_string('delete_section', 'format_mooin4'), // 'Title delete Section',
        body: '<p> Möchten Sie wirklich diese Lektion löschen ?</p>',
        preShowCallback: function(triggerElement, modal) {
            triggerElement = $(triggerElement);
            modal.params = {
            'courseid': courseIndex,
            'chapterid': chapterIndex,
            'sectionid': sectionIndex,
            'id_chapter': parseInt(idC[0].defaultValue),
            'chapter_title': titleC[0].defaultValue,
            'sectionnumber': section_number,
            'section_id_url': sectionIdUrl};
            modal.setSaveButtonText(String.get_string('delete_section', 'format_mooin4'));
        },
        large: true,
    }, trigger)
    .done(function(modal) {
        modal.getRoot().on(ModalEvents.save, function(e) {
            e.preventDefault();
            let footer = Y.one('.modal-footer');
            footer.setContent('Deleting section...');
            let spinner = M.util.add_spinner(Y, footer);
            spinner.show();
            Y.log(modal.params);
            var deletedata = {};
            deletedata.courseid = modal.params.courseid;
            deletedata.chapterid = modal.params.chapterid;
            deletedata.sectionid = modal.params.sectionid;
            deletedata.idchapter = modal.params.id_chapter;
            deletedata.chaptertitle = modal.params.chapter_title;
            deletedata.sectionidurl = modal.params.section_id_url;
            deletedata.sectionnumber = modal.params.sectionnumber;
            $.ajax({
                type: 'POST',
                data: deletedata,
                url: 'delete_section.php',
                success: (deletedata) => {
                    Y.log(deletedata);
                    Notification.addNotification({
                        message: ' You have successfully deleted the section',
                        type: 'success'
                    });
                    window.location.reload();
                },
            });
        });
    });
 });