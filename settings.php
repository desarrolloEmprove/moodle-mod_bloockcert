<?php
// This file is part of the Certificate module for Moodle - http://moodle.org/
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
 * Creates a link to the upload form on the settings page.
 *
 * @package    mod_bloockcert
 * @copyright  2024 Edwin Martinez <desarrollo@emprove.com.mx>
 * @copyright  2013 based on work by Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$url = $CFG->wwwroot . '/mod/bloockcert/verify_certificate.php';

$ADMIN->add('modsettings', new admin_category('bloockcert', get_string('pluginname', 'mod_bloockcert')));
$settings = new admin_settingpage('modsettingbloockcert', new lang_string('bloockcertsettings', 'mod_bloockcert'));

$settings->add(new admin_setting_configtext('bloockcert/urlclient',
    get_string('urlclient', 'bloockcert'), get_string('urlclient_help', 'bloockcert'), '', PARAM_TEXT));

$settings->add(new \mod_bloockcert\admin_setting_link('bloockcert/verifyurl',
    get_string('verifyurl', 'bloockcert'), get_string('verifyurldesc', 'bloockcert'),
    get_string('verifyurl', 'bloockcert'), new moodle_url('/mod/bloockcert/verify_url.php'), ''));

$settings->add(new admin_setting_configcheckbox('bloockcert/verifyallcertificates',
    get_string('verifyallcertificates', 'bloockcert'),
    get_string('verifyallcertificates_desc', 'bloockcert', $url),
    0));

$settings->add(new admin_setting_configcheckbox('bloockcert/showposxy',
    get_string('showposxy', 'bloockcert'),
    get_string('showposxy_desc', 'bloockcert'),
    0));

$settings->add(new \mod_bloockcert\admin_setting_link('bloockcert/verifycertificate',
    get_string('verifycertificate', 'bloockcert'), get_string('verifycertificatedesc', 'bloockcert'),
    get_string('verifycertificate', 'bloockcert'), new moodle_url('/mod/bloockcert/verify_certificate.php'), ''));

$settings->add(new \mod_bloockcert\admin_setting_link('bloockcert/managetemplates',
    get_string('managetemplates', 'bloockcert'), get_string('managetemplatesdesc', 'bloockcert'),
    get_string('managetemplates', 'bloockcert'), new moodle_url('/mod/bloockcert/manage_templates.php'), ''));

$settings->add(new \mod_bloockcert\admin_setting_link('bloockcert/uploadimage',
    get_string('uploadimage', 'bloockcert'), get_string('uploadimagedesc', 'bloockcert'),
    get_string('uploadimage', 'bloockcert'), new moodle_url('/mod/bloockcert/upload_image.php'), ''));

$settings->add(new admin_setting_heading('defaults',
    get_string('modeditdefaults', 'admin'), get_string('condifmodeditdefaults', 'admin')));

$yesnooptions = [
    0 => get_string('no'),
    1 => get_string('yes'),
];

$settings->add(new admin_setting_configselect('bloockcert/emailstudents',
    get_string('emailstudents', 'bloockcert'), get_string('emailstudents_help', 'bloockcert'), 0, $yesnooptions));
$settings->add(new admin_setting_configselect('bloockcert/emailteachers',
    get_string('emailteachers', 'bloockcert'), get_string('emailteachers_help', 'bloockcert'), 0, $yesnooptions));
$settings->add(new admin_setting_configtext('bloockcert/emailothers',
    get_string('emailothers', 'bloockcert'), get_string('emailothers_help', 'bloockcert'), '', PARAM_TEXT));
$settings->add(new admin_setting_configselect('bloockcert/verifyany',
    get_string('verifycertificateanyone', 'bloockcert'), get_string('verifycertificateanyone_help', 'bloockcert'),
    0, $yesnooptions));
$settings->add(new admin_setting_configtext('bloockcert/requiredtime',
    get_string('coursetimereq', 'bloockcert'), get_string('coursetimereq_help', 'bloockcert'), 0, PARAM_INT));
$settings->add(new admin_setting_configcheckbox('bloockcert/protection_print',
    get_string('preventprint', 'bloockcert'),
    get_string('preventprint_desc', 'bloockcert'),
    0));
$settings->add(new admin_setting_configcheckbox('bloockcert/protection_modify',
    get_string('preventmodify', 'bloockcert'),
    get_string('preventmodify_desc', 'bloockcert'),
    0));
$settings->add(new admin_setting_configcheckbox('bloockcert/protection_copy',
    get_string('preventcopy', 'bloockcert'),
    get_string('preventcopy_desc', 'bloockcert'),
    0));

$ADMIN->add('bloockcert', $settings);

// Element plugin settings.
$ADMIN->add('bloockcert', new admin_category('bloockcertelements', get_string('elementplugins', 'bloockcert')));
$plugins = \core_plugin_manager::instance()->get_plugins_of_type('bloockcertelement');
foreach ($plugins as $plugin) {
    $plugin->load_settings($ADMIN, 'bloockcertelements', $hassiteconfig);
}

// Tell core we already added the settings structure.
$settings = null;
