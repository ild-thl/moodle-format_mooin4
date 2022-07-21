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
 * Lists all the users within a given course.
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core_user
 */

require_once('../../../config.php');
require_once($CFG->libdir.'/tablelib.php');
require_once($CFG->libdir.'/filelib.php');
require_once('../mooin4/lib.php');

define('USER_SMALL_CLASS', 20);   // Below this is considered small.
define('USER_LARGE_CLASS', 200);  // Above this is considered large.
define('DEFAULT_PAGE_SIZE', 20);
define('SHOW_ALL_PAGE_SIZE', 5000);
define('MODE_BRIEF', 0);
define('MODE_USERDETAILS', 1);

global $DB;
global $PAGE;

$page         = optional_param('page', 0, PARAM_INT); // Which page to show.
$perpage      = optional_param('perpage', DEFAULT_PAGE_SIZE, PARAM_INT); // How many per page.
$mode         = optional_param('mode', null, PARAM_INT); // Use the MODE_ constants.
$accesssince  = optional_param('accesssince', 0, PARAM_INT); // Filter by last access. -1 = never.
$search       = optional_param('search', '', PARAM_RAW); // Make sure it is processed with p() or s() when sending to output!
$roleid       = optional_param('roleid', 0, PARAM_INT); // Optional roleid, 0 means all enrolled users (or all on the frontpage).
$contextid    = optional_param('contextid', 0, PARAM_INT); // One of this or.
$courseid     = optional_param('id', 0, PARAM_INT); // This are required.

$PAGE-> set_url('/course/format/mooin4/testsort.php');
/* $PAGE->set_url('/course/format/mooin4/inhalt.php', array(
		'id' => $courseid )); */

// include __DIR__."/config.php";
$PAGE->set_context(context_system::instance());

// set_url('/testsort.php');
// $PAGE->set_url('/course/format/mooin4/inhalt.php', array('id' => $courseid ));
$PAGE->set_pagelayout('admin');

echo $OUTPUT->header();
echo $OUTPUT->heading('Sortable list examples');
$dragdrop = $OUTPUT->render_from_template('core/drag_handle', ['movetitle' => get_string('move')]);
?>

    <div class="container">
    <div class="row">
        <div class="col-sm-4">

            <!-- =========================================== Example 1 ============================================ -->
            <h2>Example 1. Without handles</h2>
            <?php
            $PAGE->requires->js_amd_inline(<<<EOT1
        require(['core/sortable_list'], function(SortableList) {
            new SortableList('.sort-example-1', {moveHandlerSelector: null});
            $('.sort-example-1 > li').on(SortableList.EVENTS.DROP, function(evt, info) {
                console.log('Example 1 event ' + evt.type);
                console.log(info);
            });
        })
EOT1
            );
            ?>
            <style type="text/css">
                .sortable-list-current-position { background-color: lightblue; }
            </style>

            <ul class="sort-example-1 unlist">
                <li data-drag-type="move">Apple</li>
                <li data-drag-type="move">Orange</li>
                <li data-drag-type="move">Banana <a href="#">link</a></li>
                <li data-drag-type="move">Strawberry</li>
            </ul>

            <!-- =========================================== Example 2 ============================================ -->
            <h2>Example 2. With handles</h2>
            <?php
            $PAGE->requires->js_amd_inline(<<<EOT2
        require(['jquery', 'core/sortable_list'], function($, SortableList) {
            new SortableList($('.sort-example-2 tbody')[0]);
            $('.sort-example-2 tr').on(SortableList.EVENTS.DROP, function(evt, info) {
                console.log('Example 2 event ' + evt.type);
                console.log(info);
            });
        })
EOT2
            );
            ?>

            <table class="sort-example-2 table-sm table-bordered">
                <thead>
                <tr><th>Header</th></tr>
                </thead>
                <tbody>
                <tr><td><?=$dragdrop?> Apple</td></tr>
                <tr><td><?=$dragdrop?> Orange</td></tr>
                <tr><td><?=$dragdrop?> Banana <a href="#">link</a></td></tr>
                <tr><td><?=$dragdrop?> Strawberry</td></tr>
                </tbody>
            </table>
        </div>

        <!-- =========================================== Example 3 ============================================ -->
        <div class="col-sm-4">
            <h2>Example 3. Several lists</h2>
            <?php
            $PAGE->requires->js_amd_inline(<<<EOT3
        require(['core/sortable_list'], function(SortableList) {
            new SortableList('.sort-example-3[data-sort-enabled=1]', {moveHandlerSelector: null});
            $('.sort-example-3 > li').on(SortableList.EVENTS.DROP, function(evt, info) {
                console.log('Example 3 event ' + evt.type);
                console.log(info);
            });
        })
EOT3
            );
            ?>
            <style type="text/css">
                .sort-example-3 li { padding: 3px; border: 1px solid #eee; }
                .sort-example-3.sortable-list-target {
                    border: 1px dotted black;
                    background-color: #f1f3cb;
                    min-height: 20px;
                }
            </style>

            <h3>First list</h3>
            <ul class="sort-example-3 unlist" data-sort-enabled="1">
                <li data-drag-type="move">Apple</li>
                <li data-drag-type="move">Orange</li>
                <li data-drag-type="move">Banana <a href="#">link</a></li>
                <li data-drag-type="move">Strawberry</li>
            </ul>

            <h3>Second list</h3>
            <ul class="sort-example-3 unlist" data-sort-enabled="1">
                <li data-drag-type="move">Cat</li>
                <li data-drag-type="move">Dog</li>
                <li data-drag-type="move">Fish</li>
                <li data-drag-type="move">Hippo</li>
            </ul>

            <h3>Third list</h3>
            <ul class="sort-example-3 unlist" data-sort-enabled="1">
            </ul>

        </div>

        <!-- =========================================== Example 4 ============================================ -->
        <div class="col-sm-4">
            <h2>Example 4. Drop effect</h2>
            <?php
            $PAGE->requires->js_amd_inline(<<<EOT4
        require(['core/sortable_list'], function(SortableList) {
            new SortableList('.sort-example-4', {
                moveHandlerSelector: null
            });
            $('.sort-example-4 > li').on(SortableList.EVENTS.DROP, function(evt, info) {
                info.element.addClass('temphighlight');
                setTimeout(function() {
                    info.element.removeClass('temphighlight');
                }, 3000);
                console.log('Example 4 event ' + evt.type);
                console.log(info);
            });
        })
EOT4
            );
            ?>
            <style type="text/css">
                .sort-example-4 li { padding: 3px; border: 1px solid #eee; }
                .sort-example-4 li.sortable-list-current-position { opacity: 0.5; }
                .temphighlight {
                    -webkit-animation: target-fade 1s;
                    -moz-animation: target-fade 1s;
                    -o-animation: target-fade 1s;
                    animation: target-fade 1s;
                }

                @-webkit-keyframes target-fade,
                @-moz-keyframes target-fade,
                @-o-keyframes target-fade,
                @keyframes target-fade {
                    from { background-color: #EBF09E; } /* [1] */
                    to { background-color: transparent; }
                }

                @-moz-keyframes target-fade {
                    from { background-color: #EBF09E; } /* [1] */
                    to { background-color: transparent; }
                }

                @-o-keyframes target-fade {
                    from { background-color: #EBF09E; } /* [1] */
                    to { background-color: transparent; }
                }

                @keyframes target-fade {
                    from { background-color: #EBF09E; } /* [1] */
                    to { background-color: transparent; }
                }

            </style>

            <ul class="sort-example-4 unlist">
                <li data-drag-type="move">Apple</li>
                <li data-drag-type="move">Orange</li>
                <li data-drag-type="move">Banana <a href="#">link</a></li>
                <li data-drag-type="move">Strawberry</li>
            </ul>
        </div>
    </div>
    </div>

<div>
    <!-- =========================================== Example 5 horizontal ============================================ -->
        <h2>Example 5. Horizontal list</h2>
    <?php
    $PAGE->requires->js_amd_inline(<<<EOT3
        require(['core/sortable_list'], function(SortableList) {
            new SortableList('.sort-example-5', {isHorizontal: true});
            $('.sort-example-5 > li').on(SortableList.EVENTS.DROP, function(evt, info) {
                console.log('Example 5 event ' + evt.type);
                console.log(info);
            });
        })
EOT3
    );
    ?>
    <style type="text/css">
        .sort-example-5 li { padding: 3px; border: 1px solid #eee; }
    </style>

    <ul class="list-inline sort-example-5">
        <li class="list-inline-item"><?=$dragdrop?> Lorem ipsum<br>line 2<br>line 3</li>
        <li class="list-inline-item"><?=$dragdrop?> Phasellus iaculis<br>line 2<br>line 3</li>
        <li class="list-inline-item"><?=$dragdrop?> Nulla volutpat<br>line 2<br>line 3</li>
    </ul>
</div>

    <div>
        <!-- =========================================== Example 6 Hierarchy ============================================ -->
        <h2>Example 6. Hirarchy</h2>
        <?php
        $PAGE->requires->js_amd_inline(<<<EOT3
        require(['jquery', 'core/sortable_list', 'core/str'], function($, SortableList, str) {
            var elementName = function(element) {
                var name = element.attr('data-destination-name');
                return $.Deferred().resolve(name ? name : element.text());
            };
            var sort = new SortableList('.sort-example-6 ul');

            sort.getElementName = elementName;
            sort.getDestinationName = function(parentElement, afterElement) {
                if (!afterElement.length) {
                    if (parentElement.attr('data-is-root')) {
                        return $.Deferred().resolve('To the very top'); // In real life use strings here!
                    } else {
                        return elementName(parentElement.parent()).then(function(txt) {
                            return str.get_string('totopofsection', 'moodle', txt);
                        });
                    }
                } else {
                    return elementName(afterElement).then(function(txt) {
                        return str.get_string('movecontentafter', 'moodle', txt);
                    });
                }
            };

            $('.sort-example-6 ul > *').on(SortableList.EVENTS.DROP, function(evt, info) {
                console.log('Example 6 event ' + evt.type);
                console.log(info);
                evt.stopPropagation(); // Important for nested lists to prevent multiple targets.
            });
        })
EOT3
        );
        ?>
        <style type="text/css">
            .sort-example-6 li { padding: 3px; border: 1px solid #eee; }
        </style>

        <div class="sort-example-6">
            <ul id="l0" data-is-root="1">
                <li data-destination-name="Folder 1"><?=$dragdrop?> Folder 1
                    <ul id="l1"></ul>
                </li>
                <li data-destination-name="Folder 2"><?=$dragdrop?> Folder 2
                    <ul id="l2">
                        <li><?=$dragdrop?> Item 2-1</li>
                        <li><?=$dragdrop?> Item 2-2</li>
                        <li><?=$dragdrop?> Item 2-3</li>
                    </ul>
                </li>
                <li data-destination-name="Folder 3"><?=$dragdrop?> Folder 3
                    <ul id="l3">
                        <li><?=$dragdrop?> Item 3-1</li>
                        <li><?=$dragdrop?> Item 3-2</li>
                        <li><?=$dragdrop?> Item 3-3</li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>


    <div>
        <!-- =========================================== Example 7 Embedded lists (examples of callbacks) ============================================ -->
        <h2>Example 7. Embedded lists (examples of callbacks) </h2>
        <?php
        $PAGE->requires->js_amd_inline(<<<EOT3
        require(['core/sortable_list', 'core/str'], function(SortableList, str) {
            var sectionName = function(element) {
                return $.Deferred().resolve(element.attr('data-sectionname'));
            };
            
            // Sort sections.
            var sortSections = new SortableList('.sort-example-7a', {
                // We need a specific handler here because otherwise the handler from embedded activities triggers section move.
                moveHandlerSelector: '.sort-example-7a > li > h3 > [data-drag-type=move]'
            });
            sortSections.getElementName = sectionName;
            $('.sort-example-7a > *').on('sortablelist-drop sortablelist-dragstart sortablelist-drag sortablelist-dragend', function(evt, info) {
                console.log('Example 7 section event ' + evt.type);
                console.log(info);
                evt.stopPropagation(); // Important for nested lists to prevent multiple targets.
            });

            // Sort activities.
            var sortActivities = new SortableList('.sort-example-7b');
            sortActivities.getDestinationName = function(parentElement, afterElement) {
                if (!afterElement.length) {
                    return sectionName(parentElement.parent()).then(function (txt) {
                        return str.get_string('totopofsection', 'moodle', txt);
                    });
                } else {
                    return str.get_string('afterresource', 'moodle', afterElement.text());
                }
            };

            $('.sort-example-7b > *').on('sortablelist-drop sortablelist-dragstart sortablelist-drag sortablelist-dragend', function(evt, info) {
                console.log('Example 7 activity event ' + evt.type);
                console.log(info);
                evt.stopPropagation(); // Important for nested lists to prevent multiple targets.
            });
        })
EOT3
        );
        ?>
        <style type="text/css">
            .sort-example-7b {
                min-height: 20px;
                width: 100%;
            }
            .sort-example-7b.sortable-list-target {
                border: 1px dotted black;
                background-color: #f1f3cb;
            }
        </style>

        <div>
            <ul class="sort-example-7a">
                <li data-sectionname="Section A">
                    <h3><?=$dragdrop?> Section A</h3>
                    <ul class="sort-example-7b">

                    </ul>
                </li>
                <li data-sectionname="Section B">
                    <h3><?=$dragdrop?> Section B</h3>
                    <ul class="sort-example-7b">
                        <li><?=$dragdrop?> Item B-1</li>
                        <li><?=$dragdrop?> Item B-2</li>
                        <li><?=$dragdrop?> Item B-3</li>
                    </ul>
                </li>
                <li data-sectionname="Section C">
                    <h3><?=$dragdrop?> Section C</h3>
                    <ul class="sort-example-7b">
                        <li><?=$dragdrop?> Item C-1</li>
                        <li><?=$dragdrop?> Item C-2</li>
                        <li><?=$dragdrop?> Item C-3</li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>

<?php
echo $OUTPUT->footer();
