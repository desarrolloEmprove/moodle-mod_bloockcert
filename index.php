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
 * This page lists all the instances of bloockcert in a particular course.
 *
 * @package    mod_bloockcert
 * @copyright  2024 Edwin Martinez <desarrollo@emprove.com.mx>
 * @copyright  2013 based on work by Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

$id = required_param('id', PARAM_INT); // Course ID.

$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);

// Requires a login.
require_login($course);

// Set up the page variables.
$pageurl = new moodle_url('/mod/bloockcert/index.php', array('id' => $course->id));
\mod_bloockcert\page_helper::page_setup($pageurl, context_course::instance($id),
    get_string('modulenameplural', 'bloockcert'));

// Additional page setup needed.
$PAGE->set_pagelayout('incourse');
$PAGE->navbar->add(get_string('modulenameplural', 'bloockcert'));

// Add the page view to the Moodle log.
$event = \mod_bloockcert\event\course_module_instance_list_viewed::create(array(
    'context' => context_course::instance($course->id)
));
$event->add_record_snapshot('course', $course);
$event->trigger();

// Get the bloockcerts, if there are none display a notice.
if (!$bloockcerts = get_all_instances_in_course('bloockcert', $course)) {
    echo $OUTPUT->header();
    notice(get_string('nobloockcerts', 'bloockcert'), new moodle_url('/course/view.php', array('id' => $course->id)));
    echo $OUTPUT->footer();
    exit();
}

// Create the table to display the different custom certificates.
$table = new html_table();

if ($usesections = course_format_uses_sections($course->format)) {
    $table->head = array(get_string('sectionname', 'format_'.$course->format), get_string('name'),
        get_string('receiveddate', 'bloockcert'));
} else {
    $table->head = array(get_string('name'), get_string('receiveddate', 'bloockcert'));
}

$currentsection = '';
foreach ($bloockcerts as $bloockcert) {
    // Check if the bloockcert is visible, if so show text as normal, else show it as dimmed.
    if ($bloockcert->visible) {
        $link = html_writer::tag('a', $bloockcert->name, array('href' => new moodle_url('/mod/bloockcert/view.php',
            array('id' => $bloockcert->coursemodule))));
    } else {
        $link = html_writer::tag('a', $bloockcert->name, array('class' => 'dimmed',
            'href' => new moodle_url('/mod/bloockcert/view.php', array('id' => $bloockcert->coursemodule))));
    }
    // If we are at a different section then print a horizontal rule.
    if ($bloockcert->section !== $currentsection) {
        if ($currentsection !== '') {
            $table->data[] = 'hr';
        }
        $currentsection = $bloockcert->section;
    }
    // Check if there is was an issue provided for this user.
    if ($certrecord = $DB->get_record('bloockcert_issues', array('userid' => $USER->id, 'bloockcertid' => $bloockcert->id))) {
        $issued = userdate($certrecord->timecreated);
    } else {
        $issued = get_string('notissued', 'bloockcert');
    }
    // Only display the section column if the course format uses sections.
    if ($usesections) {
        $table->data[] = array($bloockcert->section, $link, $issued);
    } else {
        $table->data[] = array($link, $issued);
    }
}

echo $OUTPUT->header();
echo html_writer::table($table);
echo $OUTPUT->footer();
