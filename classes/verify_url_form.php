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
 * The form for verifying the URL client.
 *
 * @package    mod_bloockcert
 * @copyright  2024 Edwin Martínez <desarrollo@emprove.com.mx>
 * @copyright  2017 based on work by Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class verify_url_form extends \moodleform {

    /**
     * Form definition.
     */
    public function definition() {
        $mform =& $this->_form;

        $configvalue = get_config('bloockcert', 'urlclient');

        $mform->addElement('text', 'url', get_string('urlclient', 'bloockcert'));
        $mform->setDefault('url', $configvalue);

        $mform->setType('url', PARAM_TEXT);

        $mform->addElement('submit', 'verify', get_string('verify', 'bloockcert'));
    }
}
