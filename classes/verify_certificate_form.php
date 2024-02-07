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
 * This files contains the form for verifying a certificate.
 *
 * @package    mod_bloockcert
 * @copyright  2024 Edwin Martínez <desarrollo@emprove.com.mx>
 * @copyright  2017 based on work by Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_bloockcert;

defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');

require_once($CFG->libdir . '/formslib.php');

/**
 * The form for verifying a certificate.
 *
 * @package    mod_bloockcert
 * @copyright  2024 Edwin Martínez <desarrollo@emprove.com.mx>
 * @copyright  2017 based on work by Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class verify_certificate_form extends \moodleform
{

    /**
     * Form definition.
     */
    public function definition()
    {
        $mform = &$this->_form;
        global $USER, $DB;

        $contextid = optional_param('contextid', \context_system::instance()->id, PARAM_INT);

        // template.
        $sql = 'SELECT id FROM {bloockcert_templates} WHERE contextid = :contextid';
        $params = ['contextid' => $contextid,];

        $template = $DB->get_field_sql($sql, $params);

        // cert.
        $userid = $USER->id;
        $bloockcertid = $template;

        $sql = 'SELECT urlcert FROM {bloockcert_issues} WHERE userid = :userid AND bloockcertid = :bloockcertid';
        $params = ['userid' => $userid, 'bloockcertid' => $bloockcertid];

        $urlcert = $DB->get_field_sql($sql, $params);

        $configvalue = get_config('bloockcert', 'verifycertificate');

        $mform->addElement('text', 'url_certificate', get_string('url_certificate', 'bloockcert'));
        $mform->setDefault('url_certificate', $urlcert);
        $mform->setType('url_certificate', PARAM_ALPHANUM);

        $mform->addElement('submit', 'verify', get_string('verify', 'bloockcert'), array('onclick' => 'openNewTab();'));


        $mform->addElement('html', '<script>
            function openNewTab() {
                var url = "' . $configvalue . '/?url=' . $urlcert . '";
                window.open(url, "_blank");
                return false;
            }
        </script>');
    }

    /**
     * Validation.
     *
     * @param array $data
     * @param array $files
     * @return array the errors that were found
     */
    public function validation($data, $files)
    {
        $errors = array();

        if ($data['code'] === '') {
            $errors['code'] = get_string('invalidcode', 'bloockcert');
        }

        return $errors;
    }
}
