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
 * Define all the restore steps that will be used by the restore_bloockcert_activity_task.
 *
 * @package    mod_bloockcert
 * @copyright  2024 Edwin Martinez <desarrollo@emprove.com.mx>
 * @copyright  2013 based on work by Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define the complete bloockcert structure for restore, with file and id annotations.
 *
 * @package    mod_bloockcert
 * @copyright  2024 Edwin Martinez <desarrollo@emprove.com.mx>
 * @copyright  2013 based on work by Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_bloockcert_activity_structure_step extends restore_activity_structure_step {

    /**
     * Define the different items to restore.
     *
     * @return array the restore paths
     */
    protected function define_structure() {
        // The array used to store the path to the items we want to restore.
        $paths = array();

        // The bloockcert instance.
        $paths[] = new restore_path_element('bloockcert', '/activity/bloockcert');

        // The templates.
        $paths[] = new restore_path_element('bloockcert_template', '/activity/bloockcert/template');

        // The pages.
        $paths[] = new restore_path_element('bloockcert_page', '/activity/bloockcert/template/pages/page');

        // The elements.
        $paths[] = new restore_path_element('bloockcert_element', '/activity/bloockcert/template/pages/page/element');

        // Check if we want the issues as well.
        if ($this->get_setting_value('userinfo')) {
            $paths[] = new restore_path_element('bloockcert_issue', '/activity/bloockcert/issues/issue');
        }

        // Return the paths wrapped into standard activity structure.
        return $this->prepare_activity_structure($paths);
    }

    /**
     * Handles restoring the bloockcert activity.
     *
     * @param stdClass $data the bloockcert data
     */
    protected function process_bloockcert($data) {
        global $DB;

        $data = (object) $data;
        $data->course = $this->get_courseid();
        $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        // Insert the bloockcert record.
        $newitemid = $DB->insert_record('bloockcert', $data);

        // Immediately after inserting record call this.
        $this->apply_activity_instance($newitemid);
    }

    /**
     * Handles restoring a bloockcert page.
     *
     * @param stdClass $data the bloockcert data
     */
    protected function process_bloockcert_template($data) {
        global $DB;

        $data = (object) $data;
        $oldid = $data->id;

        $data->contextid = $this->task->get_contextid();
        $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        $newitemid = $DB->insert_record('bloockcert_templates', $data);
        $this->set_mapping('bloockcert_template', $oldid, $newitemid);

        // Update the template id for the bloockcert.
        $bloockcert = new stdClass();
        $bloockcert->id = $this->get_new_parentid('bloockcert');
        $bloockcert->templateid = $newitemid;
        $DB->update_record('bloockcert', $bloockcert);
    }

    /**
     * Handles restoring a bloockcert template.
     *
     * @param stdClass $data the bloockcert data
     */
    protected function process_bloockcert_page($data) {
        global $DB;

        $data = (object) $data;
        $oldid = $data->id;

        $data->templateid = $this->get_new_parentid('bloockcert_template');
        $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        $newitemid = $DB->insert_record('bloockcert_pages', $data);
        $this->set_mapping('bloockcert_page', $oldid, $newitemid);
    }

    /**
     * Handles restoring a bloockcert element.
     *
     * @param stdclass $data the bloockcert data
     */
    protected function process_bloockcert_element($data) {
        global $DB;

        $data = (object) $data;
        $oldid = $data->id;

        $data->pageid = $this->get_new_parentid('bloockcert_page');
        $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        $newitemid = $DB->insert_record('bloockcert_elements', $data);
        $this->set_mapping('bloockcert_element', $oldid, $newitemid);
    }

    /**
     * Handles restoring a bloockcert issue.
     *
     * @param stdClass $data the bloockcert data
     */
    protected function process_bloockcert_issue($data) {
        global $DB;

        $data = (object) $data;
        $oldid = $data->id;

        $data->bloockcertid = $this->get_new_parentid('bloockcert');
        $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->userid = $this->get_mappingid('user', $data->userid);

        $newitemid = $DB->insert_record('bloockcert_issues', $data);
        $this->set_mapping('bloockcert_issue', $oldid, $newitemid);
    }

    /**
     * Called immediately after all the other restore functions.
     */
    protected function after_execute() {
        parent::after_execute();

        // Add the files.
        $this->add_related_files('mod_bloockcert', 'intro', null);

        // Note - we can't use get_old_contextid() as it refers to the module context.
        $this->add_related_files('mod_bloockcert', 'image', null, $this->get_task()->get_info()->original_course_contextid);
    }
}
