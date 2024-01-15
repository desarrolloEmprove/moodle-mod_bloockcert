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
 * Definition of log events
 *
 * @package    mod_bloockcert
 * @copyright  2024 Edwin Martinez <desarrollo@emprove.com.mx>
 * @copyright  2013 based on work by Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$logs = array(
    array('module' => 'bloockcert', 'action' => 'view', 'mtable' => 'bloockcert', 'field' => 'name'),
    array('module' => 'bloockcert', 'action' => 'add', 'mtable' => 'bloockcert', 'field' => 'name'),
    array('module' => 'bloockcert', 'action' => 'update', 'mtable' => 'bloockcert', 'field' => 'name'),
    array('module' => 'bloockcert', 'action' => 'received', 'mtable' => 'bloockcert', 'field' => 'name'),
);
