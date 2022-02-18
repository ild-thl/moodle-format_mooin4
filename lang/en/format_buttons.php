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
 * format_buttons_renderer
 *
 * @package    format_buttons
 * @author     Rodrigo Brandão <https://www.linkedin.com/in/brandaorodrigo>
 * @copyright  2020 Rodrigo Brandão <rodrigo.brandao.contato@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Buttons format';
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
<strong>Above the list buttons</strong><br>Use this option if you want to add some text or resource before the buttons list.
<i>Example: Define a picture to illustrate the course.</i><br><br><strong>Below the visible section</strong><br>
Use this option if you want to add some text or resource after the visible section.
<i>Example: Resources or links to be displayed regardless of the visible section.</i><br><br>';
$string['above'] = 'Above the list buttons';
$string['below'] = 'Below the visible section';
$string['divisor'] = 'Number of sections to group - {$a}';
$string['divisortext'] = 'Title of the grouping - {$a}';
$string['divisortext_help'] = 'The grouping sections is used to separate section by type or modules.
<i>Example: The course has 10 sections divided into two modules: Theoretical (with 5 sections) and Practical (with 5 sections).<br>
Define the title like "Teorical" and set the number of sections to 5.</i><br><br>
Tip: if you want to use the tag <strong>&lt;br&gt;</strong>, type [br].';
$string['colorcurrent'] = 'Color of the current section button';
$string['colorcurrent_help'] = 'The current section is the section marked with highlight.<br>Define a color in hexadecimal.
<i>Example: #fab747</i><br>If you want to use the default color, leave empty.';
$string['colorvisible'] = 'Color of the visible section button';
$string['colorvisible_help'] = 'The visible section is the selected section.<br>Define a color in hexadecimal.
<i>Example: #747fab</i><br>If you want to use the default color, leave empty.';
$string['editing'] = 'The buttons are disabled while the edit mode is active.';
$string['sequential'] = 'Sequential';
$string['notsequentialdesc'] = 'Each new group begins counting sections from one.';
$string['sequentialdesc'] = 'Count the section numbers ignoring the grouping.';
$string['sectiontype'] = 'List style';
$string['numeric'] = 'Numeric';
$string['roman'] = 'Roman numerals';
$string['alphabet'] = 'Alphabet';
$string['buttonstyle'] = 'Button style';
$string['buttonstyle_help'] = 'Define the shape style of the buttons.';
$string['circle'] = 'Circle';
$string['square'] = 'Square';
$string['inlinesections'] = 'Inline sections';
$string['inlinesections_help'] = 'Give each section a new line.';
$string['summaryof'] = 'Infos';
$string['courseinfo'] = 'Schritt für Schritt zum.';
$string['summary'] = 'Schritt für Schritt zum.';
$string['navsections'] = 'Navigation in der Kurse.';
$string['navsections_help'] = 'Choose which Navigation should use in der Kurse.';
$string['navsectionscards'] = 'Cards navigation display.';
$string['navsectionstabs'] = 'Tabs navigation display.';
$string['sectionnameinfos'] = 'Title Info Course Page.';
$string['buttons_nav_header'] = 'Course Navigation.';
$string['buttons_nav_header_help'] = 'Configure your Course format buttons Navigation Card here.';
$string['buttons_nav_course_news_desc'] = 'Newsforum card description.';
$string['buttons_nav_course_news_desc_help'] = 'Give a small description for the card newsforum.';
$string['buttons_nav_course_news'] = 'News forum';
$string['buttons_nav_course_news_help'] = 'Set the title for the newsforum';
$string['buttons_nav_course_teilnehmer_desc'] = 'Participant card description.';
$string['buttons_nav_course_teilnehmer_desc_help'] = 'Give a small description for the card participant.';
$string['buttons_nav_course_teilnehmer'] = 'Course Participant';
$string['buttons_nav_course_teilnehmer_help'] = 'Set the title for the participant card.';
$string['buttons_nav_course_diskussion_desc'] = 'Discussion card description.';
$string['buttons_nav_course_diskussion_desc_help'] = 'Give a small description for the card discussion.';
$string['buttons_nav_course_diskussion'] = 'Discussion';
$string['buttons_nav_course_diskussion_help'] = 'Set the title for the discussion.';
$string['buttons_nav_course_social_media_desc'] = 'Social Media card description.';
$string['buttons_nav_course_social_media_desc_help'] = 'Give a small description for the card social media.';
$string['buttons_nav_course_social_media'] = 'Social Media';
$string['buttons_nav_course_social_media_help'] = 'Set the title for the social media';
$string['buttons_nav_course_inhalt_desc'] = 'Table of contents card description.';
$string['buttons_nav_course_inhalt_desc_help'] = 'Give a small description for the card table of contents.';
$string['buttons_nav_course_inhalt'] = 'Table of contents';
$string['buttons_nav_course_inhalt_help'] = 'Set the title for the table of content.';
$string['newsforen'] = ' Newsforen';
$string['teilnehmenden'] = 'Teilnehmenden';
$string['diskussionsforen'] = 'Diskussionsforen';
$string['inhaltsverzeichnis'] = 'Inhaltsverzeichnis';
$string['social_media'] = 'Social Media';
$string['newsforen_desc'] = ' Hier finden Sie alle Neuigkeiten zu diesem Kurs.';
$string['teilnehmenden_desc'] = ' Hier finden Sie die Liste alle Teilnehmern zu diesem Kurs.';
$string['diskussionsforen_desc'] = ' Hier finden Sie alle Diskussion Foren zu diesem Kurs.';
$string['inhaltsverzeichnis_desc'] = ' Inhaltsverzeichnis zu diesem Kurs.';
$string['social_media_desc'] = ' Bleiben Sie mit unseren verschiedenen sozialen Netzwerken auf dem Laufenden.';
$string['buttons:aluhatsoff'] = 'Sensible Nutzerdaten sehen';
$string['unenrol'] = 'Unsubscribe from this course';


// Anfang Buttons Online users map config
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

// End Buttons Online users map config
$string['map_title'] = 'Karte der Teilnehmenden';
$string['map_descr'] = 'Bitte tragen Sie Ihren Wohnort in Ihr Profil ein, damit die Teilnehmendenkarte möglichst vollständig angezeigt werden kann.';
