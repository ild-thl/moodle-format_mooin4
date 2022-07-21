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
 * Exemple to see how the drag & drop work
 *
 * @package    format_mooin4
 * @author     Rodrigo Brandão <https://www.linkedin.com/in/brandaorodrigo>
 * @copyright  2020 Rodrigo Brandão <rodrigo.brandao.contato@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['movecoursemodule'] = 'Move resource';
$string['movecoursesection'] = 'Move section';
$string['movecontent'] = 'Move {$a}';
$string['movecontentafter'] = 'After "{$a}"';
$string['movecontenttothetop'] = 'To the top of the list';
$string['movecategorycontentto'] = 'Move into';
$string['movecategorysuccess'] = 'Successfully moved category \'{$a->moved}\' into category \'{$a->to}\'';
$string['movecategoriessuccess'] = 'Successfully moved {$a->count} categories into category \'{$a->to}\'';