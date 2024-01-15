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
 * Handles viewing a bloockcert.
 *
 * @package    mod_bloockcert
 * @copyright  2024 Edwin Martinez <desarrollo@emprove.com.mx>
 * @copyright  2013 based on work by Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

$id = required_param('id', PARAM_INT);
$downloadown = optional_param('downloadown', false, PARAM_BOOL);
$downloadtable = optional_param('download', null, PARAM_ALPHA);
$downloadissue = optional_param('downloadissue', 0, PARAM_INT);
$deleteissue = optional_param('deleteissue', 0, PARAM_INT);
$confirm = optional_param('confirm', false, PARAM_BOOL);
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', \mod_bloockcert\certificate::BLOOCKCERT_PER_PAGE, PARAM_INT);

$cm = get_coursemodule_from_id('bloockcert', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$bloockcert = $DB->get_record('bloockcert', array('id' => $cm->instance), '*', MUST_EXIST);
$template = $DB->get_record('bloockcert_templates', array('id' => $bloockcert->templateid), '*', MUST_EXIST);

// Ensure the user is allowed to view this page.
require_login($course, false, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/bloockcert:view', $context);

$canreceive = has_capability('mod/bloockcert:receiveissue', $context);
$canmanage = has_capability('mod/bloockcert:manage', $context);
$canviewreport = has_capability('mod/bloockcert:viewreport', $context);

// Initialise $PAGE.
$pageurl = new moodle_url('/mod/bloockcert/view.php', array('id' => $cm->id));
\mod_bloockcert\page_helper::page_setup($pageurl, $context, format_string($bloockcert->name));

// Check if the user can view the certificate based on time spent in course.
if ($bloockcert->requiredtime && !$canmanage) {
    if (\mod_bloockcert\certificate::get_course_time($course->id) < ($bloockcert->requiredtime * 60)) {
        $a = new stdClass;
        $a->requiredtime = $bloockcert->requiredtime;
        $url = new moodle_url('/course/view.php', ['id' => $course->id]);
        notice(get_string('requiredtimenotmet', 'bloockcert', $a), $url);
        die;
    }
}

// Check if we are deleting an issue.
if ($deleteissue && $canmanage && confirm_sesskey()) {
    if (!$confirm) {
        $nourl = new moodle_url('/mod/bloockcert/view.php', ['id' => $id]);
        $yesurl = new moodle_url(
            '/mod/bloockcert/view.php',
            [
                'id' => $id,
                'deleteissue' => $deleteissue,
                'confirm' => 1,
                'sesskey' => sesskey()
            ]
        );

        // Show a confirmation page.
        $PAGE->navbar->add(get_string('deleteconfirm', 'bloockcert'));
        $message = get_string('deleteissueconfirm', 'bloockcert');
        echo $OUTPUT->header();
        echo $OUTPUT->heading(format_string($bloockcert->name));
        echo $OUTPUT->confirm($message, $yesurl, $nourl);
        echo $OUTPUT->footer();
        exit();
    }

    // Delete the issue.
    $DB->delete_records('bloockcert_issues', array('id' => $deleteissue, 'bloockcertid' => $bloockcert->id));

    // Redirect back to the manage templates page.
    redirect(new moodle_url('/mod/bloockcert/view.php', array('id' => $id)));
}

$event = \mod_bloockcert\event\course_module_viewed::create(array(
    'objectid' => $bloockcert->id,
    'context' => $context,
));
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('bloockcert', $bloockcert);
$event->trigger();

// Check that we are not downloading a certificate PDF.
if (!$downloadown && !$downloadissue) {
    // Get the current groups mode.
    if ($groupmode = groups_get_activity_groupmode($cm)) {
        groups_get_activity_group($cm, true);
    }

    // Generate the table to the report if there are issues to display.
    if ($canviewreport) {
        // Get the total number of issues.
        $reporttable = new \mod_bloockcert\report_table($bloockcert->id, $cm, $groupmode, $downloadtable);
        $reporttable->define_baseurl($pageurl);

        if ($reporttable->is_downloading()) {
            $reporttable->download();
            exit();
        }
    }

    // If the current user has been issued a bloockcert generate HTML to display the details.
    $issuehtml = '';
    $issues = $DB->get_records('bloockcert_issues', array('userid' => $USER->id, 'bloockcertid' => $bloockcert->id));
    if ($issues && !$canmanage) {
        // Get the most recent issue (there should only be one).
        $issue = reset($issues);
        $issuestring = get_string('receiveddate', 'bloockcert') . ': ' . userdate($issue->timecreated);
        $issuehtml = $OUTPUT->box($issuestring);
    }

    // Get the current context in view.php.
    $context = context_module::instance($cm->id);
    $PAGE->requires->js('/mod/bloockcert/js/spinner.js');

    // Output all the page data.
    echo $OUTPUT->header();
    echo $issuehtml;
    // Create the button to download the bloockcert.
    if ($canreceive) {
        echo html_writer::start_tag('div', array('id' => 'button-container', 'class' => 'container text-center'));
        echo html_writer::start_tag('form', array('action' => '/mod/bloockcert/view.php', 'method' => 'get'));
        echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'id', 'value' => $cm->id));
        echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'downloadown', 'value' => true));
        echo html_writer::tag('button', 'Obtén tu certificado', array('class' => 'btn btn-primary', 'id' => 'download-button', 'onclick' => 'showSpinner()'));
        echo html_writer::end_tag('form');
        echo html_writer::tag('h3', '', array('id' => 'dynamic-text', 'style' => 'display: none;'));
        echo html_writer::empty_tag('br');
        echo html_writer::tag('p', 'Esto puede tardar varios minutos...', array('id' => 'text-spinner', 'style' => 'display: none;'));
        echo html_writer::empty_tag('br');
        echo html_writer::empty_tag('img', array('id' => 'loading-image', 'class' => 'text-center', 'style' => 'display: none;', 'src' => '/mod/bloockcert/pix/progress.gif'));
        echo html_writer::tag('h3', 'Certificado descargado', array('id' => 'title-succes', 'style' => 'display: none;'));
        echo html_writer::empty_tag('br');
        echo html_writer::tag('button', 'Regresar al curso', array('class' => 'btn-primary', 'id' => 'return-button', 'onclick' => 'history.go(-1)', 'style' => 'display: none;'));
        echo html_writer::end_tag('div');
    }
    if (isset($reporttable)) {
        $numissues = \mod_bloockcert\certificate::get_number_of_issues($bloockcert->id, $cm, $groupmode);
        echo $OUTPUT->heading(get_string('listofissues', 'bloockcert', $numissues), 3);
        groups_print_activity_menu($cm, $pageurl);
        echo $reporttable->out($perpage, false);
    }
    echo $OUTPUT->footer($course);
    exit();
} else if ($canreceive || $canmanage) { // Output to pdf.
    // Set the userid value of who we are downloading the certificate for.
    $userid = $USER->id;
    if ($downloadown) {
        // Create new bloockcert issue record if one does not already exist.
        if (!$DB->record_exists('bloockcert_issues', array('userid' => $USER->id, 'bloockcertid' => $bloockcert->id))) {
            \mod_bloockcert\certificate::issue_certificate($bloockcert->id, $USER->id);
        }

        // Set the custom certificate as viewed.
        $completion = new completion_info($course);
        $completion->set_module_viewed($cm);
    } else if ($downloadissue && $canviewreport) {
        $userid = $downloadissue;
    }

    // Hack alert - don't initiate the download when running Behat.
    if (defined('BEHAT_SITE_RUNNING')) {
        redirect(new moodle_url('/mod/bloockcert/view.php', array('id' => $cm->id)));
    }

    \core\session\manager::write_close();

    // Now we want to generate the PDF.
    $template = new \mod_bloockcert\template($template);
    $template->save_pdf(false, $userid, $bloockcert->id);
    exit();
}
