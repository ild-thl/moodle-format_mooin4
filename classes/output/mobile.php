<?php

class mobile {

    /**
     * Main course page.
     *
     * @param array $args Standard mobile web service arguments
     * @return array
     */
    public static function mobile_course_view($args) {
        global $OUTPUT, $CFG;

        $course = get_course($args['courseid']);
        require_login($course);
        $html = $OUTPUT->render_from_template('format_buttons/mobile_course', []);

        // $displaysections = \core_course\management\helper::get_list_of_section_ids($courseid);

        return [
            'templates' => [
                [
                    'id' => 'main',
                    'html' => $html
                ]
            ],
            /* 'otherdata' => [
               ...
            ] */
        ];
    }
}