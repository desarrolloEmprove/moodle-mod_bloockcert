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

defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');

require_once($CFG->dirroot . '/mod/bloockcert/backup/moodle2/restore_bloockcert_stepslib.php');

/**
 * The class definition for assigning tasks that provide the settings and steps to perform a restore of the activity.
 *
 * @package    mod_bloockcert
 * @copyright  2024 Edwin Martinez <desarrollo@emprove.com.mx>
 * @copyright  2013 based on work by Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_bloockcert_activity_task extends restore_activity_task {

    /**
     * Define  particular settings this activity can have.
     */
    protected function define_my_settings() {
        // No particular settings for this activity.
    }

    /**
     * Define particular steps this activity can have.
     */
    protected function define_my_steps() {
        // The bloockcert only has one structure step.
        $this->add_step(new restore_bloockcert_activity_structure_step('bloockcert_structure', 'bloockcert.xml'));
    }

    /**
     * Define the contents in the activity that must be processed by the link decoder.
     */
    public static function define_decode_contents() {
        $contents = array();

        $contents[] = new restore_decode_content('bloockcert', array('intro'), 'bloockcert');

        return $contents;
    }

    /**
     * Define the decoding rules for links belonging to the activity to be executed by the link decoder.
     */
    public static function define_decode_rules() {
        $rules = array();

        $rules[] = new restore_decode_rule('BLOOCKCERTVIEWBYID', '/mod/bloockcert/view.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('BLOOCKCERTINDEX', '/mod/bloockcert/index.php?id=$1', 'course');

        return $rules;

    }

    /**
     * Define the restore log rules that will be applied by the {@see restore_logs_processor} when restoring
     * bloockcert logs. It must return one array of {@see restore_log_rule} objects.
     *
     * @return array the restore log rules
     */
    public static function define_restore_log_rules() {
        $rules = array();

        $rules[] = new restore_log_rule('bloockcert', 'add', 'view.php?id={course_module}', '{bloockcert}');
        $rules[] = new restore_log_rule('bloockcert', 'update', 'view.php?id={course_module}', '{bloockcert}');
        $rules[] = new restore_log_rule('bloockcert', 'view', 'view.php?id={course_module}', '{bloockcert}');
        $rules[] = new restore_log_rule('bloockcert', 'received', 'view.php?id={course_module}', '{bloockcert}');
        $rules[] = new restore_log_rule('bloockcert', 'view report', 'view.php?id={course_module}', '{bloockcert}');

        return $rules;
    }

    /**
     * This function is called after all the activities in the backup have been restored. This allows us to get
     * the new course module ids, as they may have been restored after the bloockcert module, meaning no id
     * was available at the time.
     */
    public function after_restore() {
        global $DB;

        // Get the bloockcert elements.
        $sql = "SELECT e.*
                  FROM {bloockcert_elements} e
            INNER JOIN {bloockcert_pages} p
                    ON e.pageid = p.id
            INNER JOIN {bloockcert} c
                    ON p.templateid = c.templateid
                 WHERE c.id = :bloockcertid";
        if ($elements = $DB->get_records_sql($sql, array('bloockcertid' => $this->get_activityid()))) {
            // Go through the elements for the certificate.
            foreach ($elements as $e) {
                // Get an instance of the element class.
                if ($e = \mod_bloockcert\element_factory::get_element_instance($e)) {
                    $e->after_restore($this);
                }
            }
        }
    }
}
