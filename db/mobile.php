<?php

$addons = [
    'format_buttons' => [
        'handlers' => [ // Different places where the plugin will display content.
            'buttons' => [ // Handler unique name (alphanumeric).
                'delegate' => 'CoreCourseFormatDelegate', // Delegate (where to display the link to the plugin)
                'method' => 'mobile_course_view', // Main function in \format_buttons\output\mobile.
                'styles' => [
                    'url' => $CFG->wwwroot . '/course/format/buttons/mobile.css',
                    'version' => 2019041000//2019041000 2020072801
                ],
                'displaysectionselector' => true, // Set to false to disable the default section selector.
                'displayenabledownload' => true, // Set to false to hide the "Enable download" option in the course context menu.
                'init' => 'buttons_init'
            ]
        ]
    ]
];