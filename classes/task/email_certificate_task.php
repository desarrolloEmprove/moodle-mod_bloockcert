<?php
// This file is part of Moodle - http://moodle.org/
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
 * A scheduled task for emailing certificates.
 *
 * @package    mod_bloockcert
 * @copyright  2024 Edwin Martínez <desarrollo@emprove.com.mx>
 * @copyright  2017 based on work by Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_bloockcert\task;

use mod_bloockcert\helper;

/**
 * A scheduled task for emailing certificates.
 *
 * @package    mod_bloockcert
 * @copyright  2024 Edwin Martínez <desarrollo@emprove.com.mx>
 * @copyright  2017 based on work by Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class email_certificate_task extends \core\task\scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('taskemailcertificate', 'bloockcert');
    }

    /**
     * Execute.
     */
    public function execute() {
        global $DB, $PAGE;

        // Get all the certificates that have requested someone get emailed.
        $emailotherslengthsql = $DB->sql_length('c.emailothers');
        $sql = "SELECT c.*, ct.id as templateid, ct.name as templatename, ct.contextid, co.id as courseid,
                       co.fullname as coursefullname, co.shortname as courseshortname
                  FROM {bloockcert} c
                  JOIN {bloockcert_templates} ct
                    ON c.templateid = ct.id
                  JOIN {course} co
                    ON c.course = co.id
                 WHERE (c.emailstudents = :emailstudents
                        OR c.emailteachers = :emailteachers
                        OR $emailotherslengthsql >= 3)";
        if (!$bloockcerts = $DB->get_records_sql($sql, array('emailstudents' => 1, 'emailteachers' => 1))) {
            return;
        }

        // The renderers used for sending emails.
        $htmlrenderer = $PAGE->get_renderer('mod_bloockcert', 'email', 'htmlemail');
        $textrenderer = $PAGE->get_renderer('mod_bloockcert', 'email', 'textemail');
        foreach ($bloockcerts as $bloockcert) {
            // Do not process an empty certificate.
            $sql = "SELECT ce.*
                      FROM {bloockcert_elements} ce
                      JOIN {bloockcert_pages} cp
                        ON cp.id = ce.pageid
                      JOIN {bloockcert_templates} ct
                        ON ct.id = cp.templateid
                     WHERE ct.contextid = :contextid";
            if (!$DB->record_exists_sql($sql, ['contextid' => $bloockcert->contextid])) {
                continue;
            }

            // Get the context.
            $context = \context::instance_by_id($bloockcert->contextid);

            // Set the $PAGE context - this ensure settings, such as language, are kept and don't default to the site settings.
            $PAGE->set_context($context);

            // Get the person we are going to send this email on behalf of.
            $userfrom = \core_user::get_noreply_user();

            // Store teachers for later.
            $teachers = get_enrolled_users($context, 'moodle/course:update');

            $courseshortname = format_string($bloockcert->courseshortname, true, array('context' => $context));
            $coursefullname = format_string($bloockcert->coursefullname, true, array('context' => $context));
            $certificatename = format_string($bloockcert->name, true, array('context' => $context));

            // Used to create the email subject.
            $info = new \stdClass;
            $info->coursename = $courseshortname; // Added for BC, so users who have edited the string don't lose this value.
            $info->courseshortname = $courseshortname;
            $info->coursefullname = $coursefullname;
            $info->certificatename = $certificatename;

            // Get a list of all the issues.
            $userfields = helper::get_all_user_name_fields('u');
            $sql = "SELECT u.id, u.username, $userfields, u.email, ci.id as issueid, ci.emailed
                      FROM {bloockcert_issues} ci
                      JOIN {user} u
                        ON ci.userid = u.id
                     WHERE ci.bloockcertid = :bloockcertid";
            $issuedusers = $DB->get_records_sql($sql, array('bloockcertid' => $bloockcert->id));

            // Now, get a list of users who can access the certificate but have not yet.
            $enrolledusers = get_enrolled_users(\context_course::instance($bloockcert->courseid), 'mod/bloockcert:view');
            foreach ($enrolledusers as $enroluser) {
                // Check if the user has already been issued.
                if (in_array($enroluser->id, array_keys((array) $issuedusers))) {
                    continue;
                }

                // Now check if the certificate is not visible to the current user.
                $cm = get_fast_modinfo($bloockcert->courseid, $enroluser->id)->instances['bloockcert'][$bloockcert->id];
                if (!$cm->uservisible) {
                    continue;
                }

                // Don't want to email those with the capability to manage the certificate.
                if (has_capability('mod/bloockcert:manage', $context, $enroluser->id)) {
                    continue;
                }

                // Only email those with the capability to receive the certificate.
                if (!has_capability('mod/bloockcert:receiveissue', $context, $enroluser->id)) {
                    continue;
                }

                // Check that they have passed the required time.
                if (!empty($bloockcert->requiredtime)) {
                    if (\mod_bloockcert\certificate::get_course_time($bloockcert->courseid,
                            $enroluser->id) < ($bloockcert->requiredtime * 60)) {
                        continue;
                    }
                }

                // Ensure the cert hasn't already been issued, e.g via the UI (view.php) - a race condition.
                $issueid = $DB->get_field('bloockcert_issues', 'id',
                    array('userid' => $enroluser->id, 'bloockcertid' => $bloockcert->id), IGNORE_MULTIPLE);
                if (empty($issueid)) {
                    // Ok, issue them the certificate.
                    $issueid = \mod_bloockcert\certificate::issue_certificate($bloockcert->id, $enroluser->id);
                }

                // Add them to the array so we email them.
                $enroluser->issueid = $issueid;
                $enroluser->emailed = 0;
                $issuedusers[] = $enroluser;
            }

            // Remove all the users who have already been emailed.
            foreach ($issuedusers as $key => $issueduser) {
                if ($issueduser->emailed) {
                    unset($issuedusers[$key]);
                }
            }

            // If there are no users to email we can return early.
            if (!$issuedusers) {
                continue;
            }

            // Create a directory to store the PDF we will be sending.
            $tempdir = make_temp_directory('certificate/attachment');
            if (!$tempdir) {
                return;
            }

            // Now, email the people we need to.
            foreach ($issuedusers as $user) {
                // Set up the user.
                cron_setup_user($user);

                $userfullname = fullname($user);
                $info->userfullname = $userfullname;

                // Now, get the PDF.
                $template = new \stdClass();
                $template->id = $bloockcert->templateid;
                $template->name = $bloockcert->templatename;
                $template->contextid = $bloockcert->contextid;
                $template = new \mod_bloockcert\template($template);
                $filecontents = $template->generate_pdf(false, $user->id, true);

                // Set the name of the file we are going to send.
                $filename = $courseshortname . '_' . $certificatename;
                $filename = \core_text::entities_to_utf8($filename);
                $filename = strip_tags($filename);
                $filename = rtrim($filename, '.');
                $filename = str_replace('&', '_', $filename) . '.pdf';

                // Create the file we will be sending.
                $tempfile = $tempdir . '/' . md5(microtime() . $user->id) . '.pdf';
                file_put_contents($tempfile, $filecontents);

                if ($bloockcert->emailstudents) {
                    $renderable = new \mod_bloockcert\output\email_certificate(true, $userfullname, $courseshortname,
                        $coursefullname, $certificatename, $bloockcert->contextid);

                    $subject = get_string('emailstudentsubject', 'bloockcert', $info);
                    $message = $textrenderer->render($renderable);
                    $messagehtml = $htmlrenderer->render($renderable);
                    email_to_user($user, fullname($userfrom), html_entity_decode($subject), $message, $messagehtml,
                        $tempfile, $filename);
                }

                if ($bloockcert->emailteachers) {
                    $renderable = new \mod_bloockcert\output\email_certificate(false, $userfullname, $courseshortname,
                        $coursefullname, $certificatename, $bloockcert->contextid);

                    $subject = get_string('emailnonstudentsubject', 'bloockcert', $info);
                    $message = $textrenderer->render($renderable);
                    $messagehtml = $htmlrenderer->render($renderable);
                    foreach ($teachers as $teacher) {
                        email_to_user($teacher, fullname($userfrom), html_entity_decode($subject), $message, $messagehtml,
                            $tempfile, $filename);
                    }
                }

                if (!empty($bloockcert->emailothers)) {
                    $others = explode(',', $bloockcert->emailothers);
                    foreach ($others as $email) {
                        $email = trim($email);
                        if (validate_email($email)) {
                            $renderable = new \mod_bloockcert\output\email_certificate(false, $userfullname,
                                $courseshortname, $coursefullname, $certificatename, $bloockcert->contextid);

                            $subject = get_string('emailnonstudentsubject', 'bloockcert', $info);
                            $message = $textrenderer->render($renderable);
                            $messagehtml = $htmlrenderer->render($renderable);

                            $emailuser = new \stdClass();
                            $emailuser->id = -1;
                            $emailuser->email = $email;
                            email_to_user($emailuser, fullname($userfrom), html_entity_decode($subject), $message,
                                $messagehtml, $tempfile, $filename);
                        }
                    }
                }

                // Set the field so that it is emailed.
                $DB->set_field('bloockcert_issues', 'emailed', 1, array('id' => $user->issueid));
            }
        }
    }
}
