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
 * format_mooin4_renderer
 *
 * @package    format_mooin4
 * @author     Rodrigo Brandão <https://www.linkedin.com/in/brandaorodrigo>
 * @copyright  2020 Rodrigo Brandão <rodrigo.brandao.contato@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Mooin4 format';
$string['currentsection'] = 'This topic';
$string['editsection'] = 'Edit topic';
$string['deletesection'] = 'Delete topic';
$string['sectionname'] = 'Topic';
$string['section0name'] = 'General';
$string['hidefromothers'] = 'Hide topic';
$string['showfromothers'] = 'Show topic';
$string['showdefaultsectionname'] = 'Show the default sections name';
$string['showdefaultsectionname_help'] = 'If no name is set for the section will not show anything.<br>
By definition an unnamed topic is displayed as <strong>Topic [N]</strong>.';
$string['yes'] = 'Yes';
$string['no'] = 'No';
$string['sectionposition'] = 'Section zero position';
$string['sectionposition_help'] = 'The section 0 will appear together the visible section.<br><br>
<strong>Above the list mooin4</strong><br>Use this option if you want to add some text or resource before the mooin4 list.
<i>Example: Define a picture to illustrate the course.</i><br><br><strong>Below the visible section</strong><br>
Use this option if you want to add some text or resource after the visible section.
<i>Example: Resources or links to be displayed regardless of the visible section.</i><br><br>';
$string['above'] = 'Above the list mooin4';
$string['below'] = 'Below the visible section';
$string['divisor'] = 'Number of sections to category - {$a}';
$string['divisortext'] = 'Title of the Category - {$a}';
$string['divisortext_help'] = 'The category sections is used to separate section by type or modules.
<i>Example: The course has 10 sections divided into two modules: Theoretical (with 5 sections) and Practical (with 5 sections).<br>
Define the title like "Teorical" and set the number of sections to 5.</i><br><br>
Tip: if you want to use the tag <strong>&lt;br&gt;</strong>, type [br].';
$string['colorcurrent'] = 'Color of the current section button';
$string['colorcurrent_help'] = 'The current section is the section marked with highlight.<br>Define a color in hexadecimal.
<i>Example: #fab747</i><br>If you want to use the default color, leave empty.';
$string['colorvisible'] = 'Color of the visible section button';
$string['colorvisible_help'] = 'The visible section is the selected section.<br>Define a color in hexadecimal.
<i>Example: #747fab</i><br>If you want to use the default color, leave empty.';
$string['editing'] = 'The mooin4 are disabled while the edit mode is active.';
$string['sequential'] = 'Sequential';
$string['notsequentialdesc'] = 'Each new categorie begins counting sections from one.';
$string['sequentialdesc'] = 'Count the section numbers ignoring the category.';
$string['sectiontype'] = 'List style';
$string['numeric'] = 'Numeric';
$string['roman'] = 'Roman numerals';
$string['alphabet'] = 'Alphabet';
$string['mooin4tyle'] = 'Button style';
$string['mooin4tyle_help'] = 'Define the shape style of the mooin4.';
$string['circle'] = 'Circle';
$string['square'] = 'Square';
$string['inlinesections'] = 'Inline sections';
$string['inlinesections_help'] = 'Give each section a new line.';
$string['summaryof'] = 'Infos';
$string['courseinfo'] = 'Test Course.';
$string['summary'] = 'Test Course.';
$string['startseite'] = ' Kursstartseite';
$string['navsections'] = 'Navigation in der Kurse.';
$string['navsections_help'] = 'Choose which Navigation should use in der Kurse.';
$string['navsectionscards'] = 'Cards navigation display.';
$string['navsectionstabs'] = 'Tabs navigation display.';
$string['sectionnameinfos'] = 'Title Info Course Page.';
/* $string['mooin4_inhalt_header'] = 'Course Table of content';
$string['mooin4_inhalt_header_help'] = 'Configure your Course format mooin4 table of content Card here.'; */
$string['mooin4_nav_course_news_desc'] = 'Newsforum card description.';
$string['mooin4_nav_course_news_desc_help'] = 'Give a small description for the card newsforum.';
$string['mooin4_nav_course_news'] = 'News forum';
$string['mooin4_nav_course_news_help'] = 'Set the title for the newsforum';
$string['mooin4_nav_course_teilnehmer_desc'] = 'Participant card description.';
$string['mooin4_nav_course_teilnehmer_desc_help'] = 'Give a small description for the card participant.';
$string['mooin4_nav_course_teilnehmer'] = 'Course Participant';
$string['mooin4_nav_course_teilnehmer_help'] = 'Set the title for the participant card.';
$string['mooin4_nav_course_diskussion_desc'] = 'Discussion card description.';
$string['mooin4_nav_course_diskussion_desc_help'] = 'Give a small description for the card discussion.';
$string['mooin4_nav_course_diskussion'] = 'Discussion';
$string['mooin4_nav_course_diskussion_help'] = 'Set the title for the discussion.';
$string['mooin4_nav_course_social_media_desc'] = 'Social Media card description.';
$string['mooin4_nav_course_social_media_desc_help'] = 'Give a small description for the card social media.';
$string['mooin4_nav_course_social_media'] = 'Social Media';
$string['mooin4_nav_course_social_media_help'] = 'Set the title for the social media';
$string['mooin4_nav_course_inhalt_desc'] = 'Table of contents card description.';
$string['mooin4_nav_course_inhalt_desc_help'] = 'Give a small description for the card table of contents.';
$string['mooin4_nav_course_inhalt'] = 'Table of contents';
$string['mooin4_nav_course_inhalt_help'] = 'Set the title for the table of content.';
$string['newsforen'] = ' Newsforen';
$string['teilnehmenden'] = 'Teilnehmende';
$string['diskussionsforen'] = 'Diskussionsforen';
$string['inhaltsverzeichnis'] = 'Inhaltsverzeichnis';
$string['social_media'] = 'Social Media';
$string['newsforen_desc'] = ' Hier finden Sie alle Neuigkeiten zu diesem Kurs.';
$string['teilnehmenden_desc'] = ' Hier finden Sie die Liste aller Teilnehmenden zu diesem Kurs.';
$string['diskussionsforen_desc'] = ' Hier finden Sie alle Diskussionsforen zu diesem Kurs.';
$string['inhaltsverzeichnis_desc'] = ' Inhaltsverzeichnis zu diesem Kurs.';
$string['social_media_desc'] = ' Bleiben Sie mit unseren verschiedenen sozialen Netzwerken auf dem Laufenden.';
$string['mooin4:aluhatsoff'] = 'Sensible Nutzerdaten sehen';
$string['mooin4:readuserpage'] = 'Nutzerdaten sehen';
$string['mooin4:createchapter'] = 'Create a new Chapter in table of content';
$string['mooin4:managechapter'] = 'Manage a specific Chapter in table of content (move,update, deleted)';
$string['mooin4:managesection'] = 'Manage a specific section in chapter (move,update, deleted)';
$string['unenrol'] = 'Unsubscribe from this course';


// Anfang mooin4 Online users map config
$string['titleonlineusersmap'] = 'Karte der Teilnehmenden';

// config setting titles
$string['centrelat'] = 'Ursprüngliche Breite';
$string['centrelng'] = 'Ursprüngliche Länge';
$string['centreuser'] = 'Zentraler Besucherort';
$string['debug'] = 'Fehlernachrichten zeigen';
$string['googleapikey'] = 'Google Maps API Schlüssel'; 
$string['offline'] = 'Offline Teilnehmer zeigen';
$string['timetosee'] = 'Nach Inaktivität entfernen';
$string['updatelimit'] = 'Maximale Orte zum hochladen'; 
$string['zoomlevel'] = 'Anfängliche Zoom-Stufe';

// config setting explanations
$string['configcentrelat'] = 'Anfängliche zentrale Breite der Karte - in ganzem Dezimalformat (keine Grad/Minuten)';
$string['configcentrelng'] = 'Anfängliche zentrale Länge der Karte - in ganzem Dezimalformat (keine Grad/Minuten)';
$string['configcentreuser'] = 'Karte auf den augenblicklichen Besucherort hin zentrieren mit obiger Zoomstufe. Diese Einstellung hat Vorrang gegenüber obigen Breite/Länge Koordinaten, es sei denn der augenblickliche Besucher hat keinen gültigen Ort';
$string['configdebug'] = 'Während Cron läuft Fehlermeldungen anzeigen';
$string['configgoogleapikey'] = 'Google Maps API Schlüssel, enthält einen Schlüssel von $a'; 
$string['configoffline'] = 'Offline Teilnehmer auch anzeigen?';
$string['configtimetosee'] = 'Anzahl an Minuten, die eine Periode von Inaktivität bestimmen, nach der ein Teilnehmer nicht mehr länger als online angesehen wird.';
$string['configupdatelimit'] = 'Maximale Zahl an Orten für ein Hochladen bei jedem Cron damit es keine Auswirkung auf die Arbeitsleistung hat. Dies muss eine ganze Zahl größer oder gleich 0 sein. Beim Setzen von 0 werden alle Datensätze aktualisiert.'; 
$string['configzoomlevel'] = 'Anfängliche Zoomstufe der Karte.';

$string['periodnminutes'] = 'der letzten {$a} Minuten';

// oncampus //////////////////////////
$string['onlyusers'] = 'Nur in oncampus Teilnehmerübersicht anzeigen';
$string['blocktitleonlineusermap'] = 'Block Überschrift';
$string['height'] = 'Height';
$string['profile_city_descr'] = 'Bitte tragen Sie Ihren Wohnort in Ihr Profil ein, damit die Teilnehmendenkarte möglichst vollständig angezeigt werden kann.';

// End mooin4 Online users map config
$string['map_title'] = 'Karte der Teilnehmenden';
$string['map_descr'] = 'Bitte tragen Sie Ihren Wohnort in Ihr Profil ein, damit die Teilnehmendenkarte möglichst vollständig angezeigt werden kann.';

// Inhaltsverzeichnis 
$string['inhalt'] = 'Inhaltsverzeichnis';
$string['courseinhalt'] = 'Inhalt';
$string['notapplicable'] = "Not applicable";
$string['hide'] = "Hide";
$string['icon'] = 'Icon';
$string['show'] = "Show";
$string['chaptering'] = 'Activate Chapter Grouping';
$string['numberofchapter'] = 'Number of chapters';
$string['change'] = 'Change';
$string['defaultgrouping'] = 'Default grouping';
$string['structure'] = 'Structure';

// Inhalt Page Content
$string['delete_section'] = 'Delete Section';
$string['delete_chapter'] = 'Delete Chapter';
$string['chapter_not_empty'] = 'Chapter has to be empty, before you can delete it !';
$string['chapter_okey'] = 'OK';
$string['chapter_empty'] = '';
$string['chapter_empty'] = 'Do you really want to definetly delete this chapter ?';
$string['reseterror'] = 'Please reset and error occured';
$string['resetpage_course'] = 'Reset page course please';