<?php
/*
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * This file is part of the Course Menu block for Moodle
 *
 * The Course Menu block for Moodle software package is Copyright � 2008 onwards NetSapiensis AB and is provided under
 * the terms of the GNU GENERAL PUBLIC LICENSE Version 3 (GPL). This program is free software: you can redistribute it
 * and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation,
 * either version 3 of the License, or (at your option) any later version.
 *
 * This program is free software: you can redistribute it and/or modify it under the terms of the GNU
 * General Public License as published by the Free Software Foundation, either version 3 of the License,
 * or (at your option) any later version. This program is distributed in the hope that
 * it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
 * PARTICULAR PURPOSE.
 *
 * See the GNU General Public License for more details. You should have received a copy of the GNU General Public
 * License along with this program.
 * If not, see <http://www.gnu.org/licenses/>.
 * ---------------------------------------------------------------------------------------------------------------------
 */

if (!isset ($chapters)) {
    error('Unauthorized');
}
$chapShow = $this->config->chapEnable ? 'i/hide' : 'i/show';
// $subChapShow = $this->config->subChapEnable ? 'i/hide' : 'i/show';
?>
<?php if ($this->page->course->id == SITEID) : ?>
    <?php echo get_string('notapplicable', 'format_mooin4') ?>
<?php else : ?>
    <div class="showHideCont">
        <a class="showHide minus" rel="div_chapters" href="javascript:void(0)">
            <?php echo get_string('hide', 'format_mooin4') ?>
        </a>
        <a class="showHide plus" rel="div_chapters" href="javascript:void(0)" style="display: none;">
            <?php echo get_string('show', 'format_mooin4') ?>
        </a>
    </div>
    <div class="clear"></div>
    <div id="t_div_chapters">
        <div class="fitem">
            <div class="fitemtitle" id="chapEnableContainer">
                <a href="javascript:void(0)" id="chap-enable">
                    <img src="<?php echo $OUTPUT->image_url($chapShow) ?>" border="0" class="show-hide" alt="" />
                    <?php echo get_string('chaptering', 'format_mooin4') ?>
                </a>
            </div>
            <div class="felement">&nbsp;</div>
        </div>
        <div class="fitem cm-chapter-enable" <?php if (!$this->config->chapEnable) echo 'style="display: none"' ?>>
            <div class="fitemtitle"><label><?php echo get_string('numberofchapter', 'format_mooin4') ?></label></div>
            <div class="felement ftext">
                <input style="width: 114px;" name="config_chaptersCount" type="text" id="chaptersCount" value="<?php echo count($this->config->chapters) ?>" />
                <button type="button" id="btn-change-chap-no"><?php echo get_string('change', 'format_mooin4') ?></button>
            </div>
        </div>
       // subchapters was here
        <div id="chaptersContainer" class="fitem cm-chapter-enable" <?php if (!$this->config->chapEnable) echo 'style="display: none;"' ?>>
            <div class="fitemtitle">&nbsp;</div>
            <div class="felement">
                <button type="button" id="btn-default-grouping">
                    <?php echo get_string('defaultgrouping', 'format_mooin4') ?>
                </button>
            </div>
        </div>
        <div class="fitem cm-chapter-enable" <?php if (!$this->config->chapEnable) echo 'style="display: none"' ?>>
            <div class="fitemtitle"><label><?php echo get_string('structure', 'format_mooin4') ?></label></div>
            <div class="felement" id="chaptersTableContainer">
                <table class="cm-table-structure">
                    <tbody></tbody>
                </table>
            </div>
        </div>
        
    </div>
<?php endif ?>