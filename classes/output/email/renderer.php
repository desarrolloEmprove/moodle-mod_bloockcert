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
 * Email certificate as html renderer.
 *
 * @package    mod_bloockcert
 * @copyright  2024 Edwin Martínez <desarrollo@emprove.com.mx>
 * @copyright  2017 based on work by Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_bloockcert\output\email;

/**
 * Email certificate as html renderer.
 *
 * @package    mod_bloockcert
 * @copyright  2024 Edwin Martínez <desarrollo@emprove.com.mx>
 * @copyright  2017 based on work by Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends \mod_bloockcert\output\renderer {

    /**
     * The template name for this renderer.
     *
     * @return string
     */
    public function get_template_name() {
        return 'email_certificate_html';
    }
}