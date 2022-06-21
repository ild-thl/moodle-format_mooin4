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
 * Edit Section (Lektion) Title in Chapter Card.
 *
 * @module     format_mooin4/edit
 * @copyright  2022 Perial Dupont Nguefack Kuaguim
 */
define(['jquery'], ($) => {
    var sectionList = $(".editsection");
    var sectionNewEdit = '';
    var sectionNewSave = '';
    var courseIndex = 0;
    var chapterIndex = 0;
    var sectionIndex = 0;
    var saveIndex = 0;
    var idSection = 0;
    var sectionDone = 0;
    var sectionUrl = "";

    $('.section_edit').click(function(event) {
                // Fetch all the chapter- and section-id  to build the current clicked section in event
                courseIndex = event.currentTarget.classList[0].substring(9, event.currentTarget.classList[0].length);
                chapterIndex = event.currentTarget.classList[1].substring(10, event.currentTarget.classList[1].length);
                sectionIndex = event.currentTarget.classList[2].substring(10, event.currentTarget.classList[2].length);
                let chapterSection = chapterIndex + sectionIndex;
                for (let index = 0; index < sectionList.length; index++) {
                    Y.log('Check');
                    Y.log(sectionList[index].classList);
                    // Fetch all the chapter- and section-id  to build the current clicked section in document
                    let courseIndexDoc = sectionList[index].classList[0].substring(9, sectionList[index].classList[0].length);
                    let chapterIndexDoc = sectionList[index].classList[1].substring(10, sectionList[index].classList[1].length);
                    let sectionIndexDoc = sectionList[index].classList[2].substring(10, sectionList[index].classList[2].length);
                    let chapterSectionDoc = chapterIndexDoc + sectionIndexDoc;

                    Y.log('chapterSection : ' + chapterSection + ' != ' + 'chapterSectionDoc : ' + chapterSectionDoc);
                    // Y.log(sectionIndex[sectionIndex.length - 1]);
                    if (chapterSection === chapterSectionDoc) {
                        // Get the section id
                        idSection = $(".id_section" + chapterSectionDoc);
                        Y.log(idSection);
                        sectionDone = $(".section-done" + chapterSectionDoc);
                        sectionNewEdit = $(".sectionEdit" + chapterSectionDoc);
                        sectionNewSave = $(".sectionSave" + chapterSectionDoc);
                        // Let v = $('.section-id' + sectionIndexDoc);
                        sectionNewEdit.css('display', 'none');
                        sectionNewSave.css('display', 'inline-block');
                        saveIndex = courseIndexDoc + chapterIndexDoc + sectionIndexDoc;
                        let inputText = $(".section" + saveIndex);
                        Y.log(inputText);
                        sectionUrl = $(".url_section" + chapterSectionDoc);
                        inputText.attr('readonly', false);
                        inputText.focus();
                        inputText.attr('contentEditable', true);
                        inputText.css("cursor", "default");
                        inputText.prop("onclick", null);
                    }
                }
    });
    $('.section_save').click(function(event) {
                event.preventDefault();
                sectionNewEdit.css('display', 'inline-block');
                sectionNewSave.css('display', 'none');
                var value = $(".section" + saveIndex);
                value.attr("onclick", "return gtag_report_conversion('${sectionUrl[0].defaultValue}');");
                value.attr('readonly', true);
                var data = {};
                Y.log(sectionUrl);
                data.id = idSection[0].defaultValue;
                data.sectiontext = value[0].value;
                data.courseid = courseIndex;
                data.chapterid = chapterIndex;
                data.sectionid = sectionIndex;
                data.sectionurl = sectionUrl[0].defaultValue;
                data.sectiondone = sectionDone[0].defaultValue;
                value.css("cursor", "pointer");
                value.attr('contentEditable', 'false');
                value.blur();
                $.ajax({
                    type: 'POST',
                    data: data,
                    url: 'edit_section_title.php',
                    success: (data) => {
                        Y.log(data);
                        window.location.reload();
                    }
                });
                // Window.location.reload();
                /* Y.log(data); */
    });
});