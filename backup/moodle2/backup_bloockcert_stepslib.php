<?php
// This file is part of the bloockcert module for Moodle - http://moodle.org/
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
 * Define all the backup steps that will be used by the backup_bloockcert_activity_task.
 *
 * @package    mod_bloockcert
 * @copyright  2024 Edwin Martinez <desarrollo@emprove.com.mx>
 * @copyright  2013 based on work by Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define the complete bloockcert structure for backup, with file and id annotations.
 *
 * @package    mod_bloockcert
 * @copyright  2024 Edwin Martinez <desarrollo@emprove.com.mx>
 * @copyright  2013 based on work by Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_bloockcert_activity_structure_step extends backup_activity_structure_step {

    /**
     * Define the structure of the backup file.
     *
     * @return backup_nested_element
     */
    protected function define_structure() {

        // The instance.
        $bloockcert = new backup_nested_element('bloockcert', array('id'), array(
            'templateid', 'name', 'intro', 'introformat', 'requiredtime', 'verifyany', 'emailstudents',
            'emailteachers', 'emailothers', 'protection', 'timecreated', 'timemodified'));

        // The template.
        $template = new backup_nested_element('template', array('id'), array(
            'name', 'contextid', 'timecreated', 'timemodified'));

        // The pages.
        $pages = new backup_nested_element('pages');
        $page = new backup_nested_element('page', array('id'), array(
            'templateid', 'width', 'height', 'leftmargin', 'rightmargin',
            'sequence', 'timecreated', 'timemodified'));

        // The elements.
        $element = new backup_nested_element('element', array('id'), array(
            'pageid', 'name', 'element', 'data', 'font', 'fontsize',
            'colour', 'posx', 'posy', 'width', 'refpoint', 'sequence',
            'alignment', 'timecreated', 'timemodified'));

        // The issues.
        $issues = new backup_nested_element('issues');
        $issue = new backup_nested_element('issue', array('id'), array(
            'bloockcertid', 'userid', 'timecreated', 'emailed', 'code'));

        // Build the tree.
        $bloockcert->add_child($issues);
        $issues->add_child($issue);
        $bloockcert->add_child($template);
        $template->add_child($pages);
        $pages->add_child($page);
        $page->add_child($element);

        // Define sources.
        $bloockcert->set_source_table('bloockcert', array('id' => backup::VAR_ACTIVITYID));

        // Define template source.
        $template->set_source_table('bloockcert_templates', array('contextid' => backup::VAR_CONTEXTID));

        // Define page source.
        $page->set_source_table('bloockcert_pages', array('templateid' => backup::VAR_PARENTID));

        // Define element source, each element belongs to a page.
        $element->set_source_table('bloockcert_elements', array('pageid' => backup::VAR_PARENTID));

        // If we are including user info then save the issues.
        if ($this->get_setting_value('userinfo')) {
            $issue->set_source_table('bloockcert_issues', array('bloockcertid' => backup::VAR_ACTIVITYID));
        }

        // Annotate the user id's where required.
        $issue->annotate_ids('user', 'userid');

        // Define file annotations.
        $bloockcert->annotate_files('mod_bloockcert', 'intro', null);
        $bloockcert->annotate_files('mod_bloockcert', 'image', null, context_course::instance($this->get_courseid())->id);

        // Return the root element (bloockcert), wrapped into standard activity structure.
        return $this->prepare_activity_structure($bloockcert);
    }
}
