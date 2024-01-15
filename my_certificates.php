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
 * Handles viewing the certificates for a certain user.
 *
 * @package    mod_bloockcert
 * @copyright  2024 Edwin Martinez <desarrollo@emprove.com.mx>
 * @copyright  2016 based on work by Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

$userid = optional_param('userid', $USER->id, PARAM_INT);
$download = optional_param('download', null, PARAM_ALPHA);
$courseid = optional_param('course', null, PARAM_INT);
$downloadcert = optional_param('downloadcert', '', PARAM_BOOL);
if ($downloadcert) {
    $certificateid = required_param('certificateid', PARAM_INT);
    $bloockcert = $DB->get_record('bloockcert', array('id' => $certificateid), '*', MUST_EXIST);

    // Check there exists an issued certificate for this user.
    if (!$issue = $DB->get_record('bloockcert_issues', ['userid' => $userid, 'bloockcertid' => $bloockcert->id])) {
        throw new moodle_exception('You have not been issued a certificate');
    }
}
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', \mod_bloockcert\certificate::BLOOCKCERT_PER_PAGE, PARAM_INT);
$pageurl = $url = new moodle_url('/mod/bloockcert/my_certificates.php', array('userid' => $userid,
    'page' => $page, 'perpage' => $perpage));

// Requires a login.
if ($courseid) {
    require_login($courseid);
} else {
    require_login();
}

// Check that we have a valid user.
$user = \core_user::get_user($userid, '*', MUST_EXIST);

// If we are viewing certificates that are not for the currently logged in user then do a capability check.
if (($userid != $USER->id) && !has_capability('mod/bloockcert:viewallcertificates', context_system::instance())) {
    throw new moodle_exception('You are not allowed to view these certificates');
}

$PAGE->set_url($pageurl);
$PAGE->set_context(context_user::instance($userid));
$PAGE->set_title(get_string('mycertificates', 'bloockcert'));
$PAGE->set_pagelayout('standard');
$PAGE->navigation->extend_for_user($user);

// Check if we requested to download a certificate.
if ($downloadcert) {
    $template = $DB->get_record('bloockcert_templates', array('id' => $bloockcert->templateid), '*', MUST_EXIST);
    $templateid = $template->id;
    $template = new \mod_bloockcert\template($template);
    $template->save_pdf(false, $userid, $templateid);
    exit();
}

$table = new \mod_bloockcert\my_certificates_table($userid, $download);
$table->define_baseurl($pageurl);

if ($table->is_downloading()) {
    $table->download();
    exit();
}

// Additional page setup.
$PAGE->navbar->add(get_string('profile'), new moodle_url('/user/profile.php', array('id' => $userid)));
$PAGE->navbar->add(get_string('mycertificates', 'bloockcert'));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('mycertificates', 'bloockcert'));
echo html_writer::div(get_string('mycertificatesdescription', 'bloockcert'));
$table->out($perpage, false);
echo $OUTPUT->footer();
