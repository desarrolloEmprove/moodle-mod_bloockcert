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
 * bloockcert module core interaction API
 *
 * @package    mod_bloockcert
 * @copyright  2024 Edwin Martinez <desarrollo@emprove.com.mx>
 * @copyright  2013 based on work by Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Add bloockcert instance.
 *
 * @param stdClass $data
 * @param mod_bloockcert_mod_form $mform
 * @return int new bloockcert instance id
 */
function bloockcert_add_instance($data, $mform) {
    global $DB;

    // Create a template for this bloockcert to use.
    $context = context_module::instance($data->coursemodule);
    $template = \mod_bloockcert\template::create($data->name, $context->id);

    // Add the data to the DB.
    $data->templateid = $template->get_id();
    $data->protection = \mod_bloockcert\certificate::set_protection($data);
    $data->timecreated = time();
    $data->timemodified = $data->timecreated;
    $data->id = $DB->insert_record('bloockcert', $data);

    // Add a page to this bloockcert.
    $template->add_page();

    return $data->id;
}

/**
 * Update bloockcert instance.
 *
 * @param stdClass $data
 * @param mod_bloockcert_mod_form $mform
 * @return bool true
 */
function bloockcert_update_instance($data, $mform) {
    global $DB;

    $data->protection = \mod_bloockcert\certificate::set_protection($data);
    $data->timemodified = time();
    $data->id = $data->instance;

    return $DB->update_record('bloockcert', $data);
}

/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id
 * @return bool true if successful
 */
function bloockcert_delete_instance($id) {
    global $CFG, $DB;

    // Ensure the bloockcert exists.
    if (!$bloockcert = $DB->get_record('bloockcert', array('id' => $id))) {
        return false;
    }

    // Get the course module as it is used when deleting files.
    if (!$cm = get_coursemodule_from_instance('bloockcert', $id)) {
        return false;
    }

    // Delete the bloockcert instance.
    if (!$DB->delete_records('bloockcert', array('id' => $id))) {
        return false;
    }

    // Now, delete the template associated with this certificate.
    if ($template = $DB->get_record('bloockcert_templates', array('id' => $bloockcert->templateid))) {
        $template = new \mod_bloockcert\template($template);
        $template->delete();
    }

    // Delete the bloockcert issues.
    if (!$DB->delete_records('bloockcert_issues', array('bloockcertid' => $id))) {
        return false;
    }

    // Delete any files associated with the bloockcert.
    $context = context_module::instance($cm->id);
    $fs = get_file_storage();
    $fs->delete_area_files($context->id);

    return true;
}

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 * This function will remove all posts from the specified bloockcert
 * and clean up any related data.
 *
 * @param stdClass $data the data submitted from the reset course.
 * @return array status array
 */
function bloockcert_reset_userdata($data) {
    global $DB;

    $componentstr = get_string('modulenameplural', 'bloockcert');
    $status = array();

    if (!empty($data->reset_bloockcert)) {
        $sql = "SELECT cert.id
                  FROM {bloockcert} cert
                 WHERE cert.course = :courseid";
        $DB->delete_records_select('bloockcert_issues', "bloockcertid IN ($sql)", array('courseid' => $data->courseid));
        $status[] = array('component' => $componentstr, 'item' => get_string('deleteissuedcertificates', 'bloockcert'),
            'error' => false);
    }

    return $status;
}

/**
 * Implementation of the function for printing the form elements that control
 * whether the course reset functionality affects the bloockcert.
 *
 * @param mod_bloockcert_mod_form $mform form passed by reference
 */
function bloockcert_reset_course_form_definition(&$mform) {
    $mform->addElement('header', 'bloockcertheader', get_string('modulenameplural', 'bloockcert'));
    $mform->addElement('advcheckbox', 'reset_bloockcert', get_string('deleteissuedcertificates', 'bloockcert'));
}

/**
 * Course reset form defaults.
 *
 * @param stdClass $course
 * @return array
 */
function bloockcert_reset_course_form_defaults($course) {
    return array('reset_bloockcert' => 1);
}

/**
 * Returns information about received bloockcert.
 * Used for user activity reports.
 *
 * @param stdClass $course
 * @param stdClass $user
 * @param stdClass $mod
 * @param stdClass $bloockcert
 * @return stdClass the user outline object
 */
function bloockcert_user_outline($course, $user, $mod, $bloockcert) {
    global $DB;

    $result = new stdClass();
    if ($issue = $DB->get_record('bloockcert_issues', array('bloockcertid' => $bloockcert->id, 'userid' => $user->id))) {
        $result->info = get_string('receiveddate', 'bloockcert');
        $result->time = $issue->timecreated;
    } else {
        $result->info = get_string('notissued', 'bloockcert');
    }

    return $result;
}

/**
 * Returns information about received bloockcert.
 * Used for user activity reports.
 *
 * @param stdClass $course
 * @param stdClass $user
 * @param stdClass $mod
 * @param stdClass $bloockcert
 * @return string the user complete information
 */
function bloockcert_user_complete($course, $user, $mod, $bloockcert) {
    global $DB, $OUTPUT;

    if ($issue = $DB->get_record('bloockcert_issues', array('bloockcertid' => $bloockcert->id, 'userid' => $user->id))) {
        echo $OUTPUT->box_start();
        echo get_string('receiveddate', 'bloockcert') . ": ";
        echo userdate($issue->timecreated);
        echo $OUTPUT->box_end();
    } else {
        print_string('notissued', 'bloockcert');
    }
}

/**
 * Serves certificate issues and other files.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @return bool|null false if file not found, does not return anything if found - just send the file
 */
function bloockcert_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload) {
    global $CFG;

    require_once($CFG->libdir . '/filelib.php');

    // We are positioning the elements.
    if ($filearea === 'image') {
        if ($context->contextlevel == CONTEXT_MODULE) {
            require_login($course, false, $cm);
        } else if ($context->contextlevel == CONTEXT_SYSTEM && !has_capability('mod/bloockcert:manage', $context)) {
            return false;
        }

        $relativepath = implode('/', $args);
        $fullpath = '/' . $context->id . '/mod_bloockcert/image/' . $relativepath;

        $fs = get_file_storage();
        $file = $fs->get_file_by_hash(sha1($fullpath));
        if (!$file || $file->is_directory()) {
            return false;
        }

        send_stored_file($file, 0, 0, $forcedownload);
    }
}

/**
 * The features this activity supports.
 *
 * @uses FEATURE_GROUPS
 * @uses FEATURE_GROUPINGS
 * @uses FEATURE_GROUPMEMBERSONLY
 * @uses FEATURE_MOD_INTRO
 * @uses FEATURE_COMPLETION_TRACKS_VIEWS
 * @uses FEATURE_GRADE_HAS_GRADE
 * @uses FEATURE_GRADE_OUTCOMES
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, null if doesn't know
 */
function bloockcert_supports($feature) {
    switch ($feature) {
        case FEATURE_GROUPINGS:
        case FEATURE_MOD_INTRO:
        case FEATURE_SHOW_DESCRIPTION:
        case FEATURE_COMPLETION_TRACKS_VIEWS:
        case FEATURE_BACKUP_MOODLE2:
        case FEATURE_GROUPS:
            return true;
        default:
            return null;
    }
}

/**
 * Used for course participation report (in case bloockcert is added).
 *
 * @return array
 */
function bloockcert_get_view_actions() {
    return array('view', 'view all', 'view report');
}

/**
 * Used for course participation report (in case bloockcert is added).
 *
 * @return array
 */
function bloockcert_get_post_actions() {
    return array('received');
}

/**
 * Function to be run periodically according to the moodle cron.
 */
function bloockcert_cron() {
    return true;
}

/**
 * Serve the edit element as a fragment.
 *
 * @param array $args List of named arguments for the fragment loader.
 * @return string
 */
function mod_bloockcert_output_fragment_editelement($args) {
    global $DB;

    // Get the element.
    $element = $DB->get_record('bloockcert_elements', array('id' => $args['elementid']), '*', MUST_EXIST);

    $pageurl = new moodle_url('/mod/bloockcert/rearrange.php', array('pid' => $element->pageid));
    $form = new \mod_bloockcert\edit_element_form($pageurl, array('element' => $element));

    return $form->render();
}

/**
 * This function extends the settings navigation block for the site.
 *
 * It is safe to rely on PAGE here as we will only ever be within the module
 * context when this is called.
 *
 * @param settings_navigation $settings
 * @param navigation_node $bloockcertnode
 */
function bloockcert_extend_settings_navigation(settings_navigation $settings, navigation_node $bloockcertnode) {
    global $DB, $PAGE;

    $keys = $bloockcertnode->get_children_key_list();
    $beforekey = null;
    $i = array_search('modedit', $keys);
    if ($i === false && array_key_exists(0, $keys)) {
        $beforekey = $keys[0];
    } else if (array_key_exists($i + 1, $keys)) {
        $beforekey = $keys[$i + 1];
    }

    if (has_capability('mod/bloockcert:manage', $settings->get_page()->cm->context)) {
        // Get the template id.
        $templateid = $DB->get_field('bloockcert', 'templateid', array('id' => $settings->get_page()->cm->instance));
        $node = navigation_node::create(get_string('editbloockcert', 'bloockcert'),
                new moodle_url('/mod/bloockcert/edit.php', array('tid' => $templateid)),
                navigation_node::TYPE_SETTING, null, 'mod_bloockcert_edit',
                new pix_icon('t/edit', ''));
        $bloockcertnode->add_node($node, $beforekey);
    }

    if (has_capability('mod/bloockcert:verifycertificate', $settings->get_page()->cm->context)) {
        $node = navigation_node::create(get_string('verifycertificate', 'bloockcert'),
            new moodle_url('/mod/bloockcert/verify_certificate.php', array('contextid' => $settings->get_page()->cm->context->id)),
            navigation_node::TYPE_SETTING, null, 'mod_bloockcert_verify_certificate',
            new pix_icon('t/check', ''));
        $bloockcertnode->add_node($node, $beforekey);
    }

    return $bloockcertnode->trim_if_empty();
}

/**
 * Add nodes to myprofile page.
 *
 * @param \core_user\output\myprofile\tree $tree Tree object
 * @param stdClass $user user object
 * @param bool $iscurrentuser
 * @param stdClass $course Course object
 * @return bool
 */
function mod_bloockcert_myprofile_navigation(core_user\output\myprofile\tree $tree, $user, $iscurrentuser, $course) {
    $params = [
        'userid' => $user->id
    ];
    if ($course) {
        $params['course'] = $course->id;
    }
    $url = new moodle_url('/mod/bloockcert/my_certificates.php', $params);
    $node = new core_user\output\myprofile\node('miscellaneous', 'mybloockcerts',
        get_string('mycertificates', 'bloockcert'), null, $url);
    $tree->add_node($node);
}

/**
 * Handles editing the 'name' of the element in a list.
 *
 * @param string $itemtype
 * @param int $itemid
 * @param string $newvalue
 * @return \core\output\inplace_editable
 */
function mod_bloockcert_inplace_editable($itemtype, $itemid, $newvalue) {
    global $DB, $PAGE;

    if ($itemtype === 'elementname') {
        $element = $DB->get_record('bloockcert_elements', array('id' => $itemid), '*', MUST_EXIST);
        $page = $DB->get_record('bloockcert_pages', array('id' => $element->pageid), '*', MUST_EXIST);
        $template = $DB->get_record('bloockcert_templates', array('id' => $page->templateid), '*', MUST_EXIST);

        // Set the template object.
        $template = new \mod_bloockcert\template($template);
        // Perform checks.
        if ($cm = $template->get_cm()) {
            require_login($cm->course, false, $cm);
        } else {
            $PAGE->set_context(context_system::instance());
            require_login();
        }
        // Make sure the user has the required capabilities.
        $template->require_manage();

        // Clean input and update the record.
        $updateelement = new stdClass();
        $updateelement->id = $element->id;
        $updateelement->name = clean_param($newvalue, PARAM_TEXT);
        $DB->update_record('bloockcert_elements', $updateelement);

        return new \core\output\inplace_editable('mod_bloockcert', 'elementname', $element->id, true,
            $updateelement->name, $updateelement->name);
    }
}

/**
 * Get icon mapping for font-awesome.
 */
function mod_bloockcert_get_fontawesome_icon_map() {
    return [
        'mod_bloockcert:download' => 'fa-download'
    ];
}

/**
 * Force custom language for current session.
 *
 * @param string $language
 * @return bool
 */
function mod_bloockcert_force_current_language($language): bool {
    global $USER;

    $forced = false;
    if (empty($language)) {
        return $forced;
    }

    $activelangs = get_string_manager()->get_list_of_translations();
    $userlang = $USER->lang ?? current_language();

    if (array_key_exists($language, $activelangs) && $language != $userlang) {
        force_current_language($language);
        $forced = true;
    }

    return $forced;
}
