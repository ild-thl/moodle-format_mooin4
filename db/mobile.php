<?php

$addons = [
    'format_mooin4' => [
        'handlers' => [ // Different places where the plugin will display content.
            'mooin4' => [ // Handler unique name (alphanumeric).
                'delegate' => 'CoreCourseFormatDelegate', // Delegate (where to display the link to the plugin)
                'method' => 'mobile_course_view', // Main function in \format_mooin4\output\mobile.
                'styles' => [
                    'url' => $CFG->wwwroot . '/course/format/mooin4/mobile.css',
                    'version' => 2019041000//2019041000 2020072801
                ],
                'displaysectionselector' => true, // Set to false to disable the default section selector.
                'displayenabledownload' => true, // Set to false to hide the "Enable download" option in the course context menu.
                'init' => 'mooin4_init'
            ]
        ]
    ]
];