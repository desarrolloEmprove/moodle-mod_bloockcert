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
 * Contains the class responsible for data generation during unit tests
 *
 * @package mod_bloockcert
 * @category test
 * @copyright 2024 Edwin Martinez <desarrollo@emprove.com.mx>
 * @copyright 2017 based on work by Mark Nelson <markn@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * The class responsible for data generation during unit tests
 *
 * @package mod_bloockcert
 * @category test
 * @copyright 2024 Edwin Martinez <desarrollo@emprove.com.mx>
 * @copyright 2017 based on work by Mark Nelson <markn@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_bloockcert_generator extends testing_module_generator {

    /**
     * Creates an instance of the custom certificate.
     *
     * @param array|stdClass|null $record
     * @param array|null $options
     * @return stdClass
     */
    public function create_instance($record = null, array $options = null) {
        $record = (object)(array)$record;

        $defaultsettings = array(
            'requiredtime' => 0,
            'emailstudents' => 0,
            'emailteachers' => 0,
            'emailothers' => '',
            'protection' => ''
        );

        foreach ($defaultsettings as $name => $value) {
            if (!isset($record->{$name})) {
                $record->{$name} = $value;
            }
        }

        return parent::create_instance($record, (array)$options);
    }
}
