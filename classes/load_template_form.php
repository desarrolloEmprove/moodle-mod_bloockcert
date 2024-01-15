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
 * This file contains the form for loading bloockcert templates.
 *
 * @package    mod_bloockcert
 * @copyright  2024 Edwin Martinez <desarrollo@emprove.com.mx>
 * @copyright  2013 based on work by Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_bloockcert;

defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');

require_once($CFG->libdir . '/formslib.php');

/**
 * The form for loading bloockcert templates.
 *
 * @package    mod_bloockcert
 * @copyright  2024 Edwin Martinez <desarrollo@emprove.com.mx>
 * @copyright  2013 based on work by Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class load_template_form extends \moodleform {

    /**
     * Form definition.
     */
    public function definition() {
        global $DB;

        $mform =& $this->_form;

        // Get the context.
        $context = $this->_customdata['context'];
        $syscontext = \context_system::instance();

        $mform->addElement('header', 'loadtemplateheader', get_string('loadtemplate', 'bloockcert'));

        // Display a link to the manage templates page.
        if ($context->contextlevel != CONTEXT_SYSTEM && has_capability('mod/bloockcert:manage', $syscontext)) {
            $link = \html_writer::link(new \moodle_url('/mod/bloockcert/manage_templates.php'),
                get_string('managetemplates', 'bloockcert'));
            $mform->addElement('static', 'managetemplates', '', $link);
        }

        $arrtemplates = $DB->get_records_menu('bloockcert_templates', ['contextid' => $syscontext->id], 'name ASC', 'id, name');
        if ($arrtemplates) {
            $templates = [];
            foreach ($arrtemplates as $key => $template) {
                $templates[$key] = format_string($template, true, ['context' => $context]);
            }
            $group = array();
            $group[] = $mform->createElement('select', 'ltid', '', $templates);
            $group[] = $mform->createElement('submit', 'loadtemplatesubmit', get_string('load', 'bloockcert'));
            $mform->addElement('group', 'loadtemplategroup', '', $group, '', false);
            $mform->setType('ltid', PARAM_INT);
        } else {
            $msg = \html_writer::tag('div', get_string('notemplates', 'bloockcert'), array('class' => 'alert'));
            $mform->addElement('static', 'notemplates', '', $msg);
        }
    }
}
