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
 * Contains the factory class responsible for creating custom certificate instances.
 *
 * @package    mod_bloockcert
 * @copyright  2024 Edwin Martínez <desarrollo@emprove.com.mx>
 * @copyright  2017 based on work by Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_bloockcert;

/**
 * The factory class responsible for creating custom certificate instances.
 *
 * @package    mod_bloockcert
 * @copyright  2024 Edwin Martínez <desarrollo@emprove.com.mx>
 * @copyright  2017 based on work by Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class element_factory {

    /**
     * Returns an instance of the element class.
     *
     * @param \stdClass $element the element
     * @return \mod_bloockcert\element|bool returns the instance of the element class, or false if element
     *         class does not exists.
     */
    public static function get_element_instance($element) {
        // Get the class name.
        $classname = '\\bloockcertelement_' . $element->element . '\\element';

        $data = new \stdClass();
        $data->id = $element->id ?? null;
        $data->pageid = $element->pageid ?? null;
        $data->name = $element->name ?? get_string('pluginname', 'bloockcertelement_' . $element->element);
        $data->element = $element->element;
        $data->data = $element->data ?? null;
        $data->font = $element->font ?? null;
        $data->fontsize = $element->fontsize ?? null;
        $data->colour = $element->colour ?? null;
        $data->posx = $element->posx ?? null;
        $data->posy = $element->posy ?? null;
        $data->width = $element->width ?? null;
        $data->refpoint = $element->refpoint ?? null;
        $data->alignment = $element->alignment ?? null;

        // Ensure the necessary class exists.
        if (class_exists($classname)) {
            return new $classname($data);
        }

        return false;
    }
}
