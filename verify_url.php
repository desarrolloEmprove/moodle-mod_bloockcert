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
 * Handles verifying the code for a certificate.
 *
 * @package   mod_bloockcert
 * @copyright 2024 Edwin Martinez <desarrollo@emprove.com.mx>
 * @copyright 2017 based on work by Mark Nelson <markn@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// This file does not need require_login because capability to verify can be granted to guests, skip codechecker here.
// @codingStandardsIgnoreLine
require_once('../../config.php');

$contextid = optional_param('contextid', context_system::instance()->id, PARAM_INT);
$urlparam = optional_param('url', '', PARAM_TEXT); // The url we are verifying.

$context = context::instance_by_id($contextid);

// Set up the page.
$pageurl = new moodle_url('/mod/bloockcert/verify_url.php', array('contextid' => $contextid));

if ($urlparam) {
    $pageurl->param('url', $urlparam);
}

// Ok, a certificate was specified.
if ($context->contextlevel != CONTEXT_SYSTEM) {
    $cm = get_coursemodule_from_id('bloockcert', $context->instanceid, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $bloockcert = $DB->get_record('bloockcert', array('id' => $cm->instance), '*', MUST_EXIST);

    // Check if we are allowing anyone to verify, if so, no need to check login, or permissions.
    if (!$bloockcert->verifyany) {
        // Need to be logged in.
        require_login($course, false, $cm);
        // Ok, now check the user has the ability to verify url.
        require_capability('mod/bloockcert:verifyurl', $context);
    } else {
        $PAGE->set_cm($cm, $course);
    }

    $title = $bloockcert->name;
    $checkallofsite = false;
} else {
    $title = $SITE->fullname;
    $checkallofsite = true;
}

\mod_bloockcert\page_helper::page_setup($pageurl, $context, $title);
$PAGE->activityheader->set_attrs(['hidecompletion' => true,
            'description' => '']);

// Additional page setup.
if ($context->contextlevel == CONTEXT_SYSTEM) {
    $PAGE->navbar->add(get_string('verifyurl', 'bloockcert'));
}

// The form we are using to verify these URL.
$form = new \mod_bloockcert\verify_url_form($pageurl);

if ($urlparam) {

    // Get the value in the form.
    $dataform = $form->get_data();
    $url = $dataform->url;

    $result = new stdClass();
    $result->issues = array();

    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => $url.'/v1/health',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    ));

    $response = curl_exec($curl);

    curl_close($curl);

    $data = json_decode($response, true);

    if ($data !== null) {
        $successvalue = $data['success'];

        if ($successvalue === true) {

            $result->success = true;

        } else {
            $result->success = false;
        }
    } else {
        $result->success = false;
    }
}

echo $OUTPUT->header();

echo $form->display();

if (isset($result)) {
    $renderer = $PAGE->get_renderer('mod_bloockcert');
    $result = new \mod_bloockcert\output\verify_certificate_results($result);
    echo $renderer->render($result);
}
echo $OUTPUT->footer();
