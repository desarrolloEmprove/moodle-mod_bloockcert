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
 * Contains the event tests for the module bloockcert.
 *
 * @package   mod_bloockcert
 * @copyright 2023 Mark Nelson <mdjnelson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_bloockcert\event;

/**
 * Contains the event tests for the module bloockcert.
 *
 * @package   mod_bloockcert
 * @copyright 2023 Mark Nelson <mdjnelson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class events_test extends \advanced_testcase {

    public function setUp(): void {
        $this->resetAfterTest();
    }

    /**
     * Tests the events are fired correctly when creating a template.
     *
     * @covers \mod_bloockcert\template::create
     */
    public function test_creating_a_template(): void {
        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $template = \mod_bloockcert\template::create('Test name', \context_system::instance()->id);
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_bloockcert\event\template_created', $event);
        $this->assertEquals($template->get_id(), $event->objectid);
    }

    /**
     * Tests the events are fired correctly when creating a page.
     *
     * @covers \mod_bloockcert\template::add_page
     */
    public function test_creating_a_page(): void {
        $template = \mod_bloockcert\template::create('Test name', \context_system::instance()->id);

        $sink = $this->redirectEvents();
        $page = $template->add_page();
        $events = $sink->get_events();

        $this->assertCount(2, $events);

        $pagecreatedevent = array_shift($events);
        $templateupdateevent = array_shift($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_bloockcert\event\page_created', $pagecreatedevent);
        $this->assertEquals($page, $pagecreatedevent->objectid);
        $this->assertDebuggingNotCalled();

        $this->assertInstanceOf('\mod_bloockcert\event\template_updated', $templateupdateevent);
        $this->assertEquals($template->get_id(), $templateupdateevent->objectid);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Tests the events are fired correctly when moving an item.
     *
     * @covers \mod_bloockcert\template::move_item
     */
    public function test_moving_item(): void {
        $template = \mod_bloockcert\template::create('Test name', \context_system::instance()->id);
        $page1id = $template->add_page();
        $template->add_page();

        $sink = $this->redirectEvents();
        $template->move_item('page', $page1id, 'down');
        $events = $sink->get_events();

        $event = reset($events);
        $this->assertInstanceOf('\mod_bloockcert\event\template_updated', $event);
        $this->assertEquals($template->get_id(), $event->objectid);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Tests the events are fired correctly when deleting a template.
     *
     * @covers \mod_bloockcert\template::delete
     */
    public function test_deleting_a_template(): void {
        $template = \mod_bloockcert\template::create('Test name', \context_system::instance()->id);

        $sink = $this->redirectEvents();
        $template->delete();
        $events = $sink->get_events();

        $event = reset($events);
        $this->assertInstanceOf('\mod_bloockcert\event\template_deleted', $event);
        $this->assertEquals($template->get_id(), $event->objectid);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Tests the events are fired correctly when deleting a page.
     *
     * @covers \mod_bloockcert\template::delete_page
     */
    public function test_deleting_a_page(): void {
        $template = \mod_bloockcert\template::create('Test name', \context_system::instance()->id);
        $page1id = $template->add_page();

        $sink = $this->redirectEvents();
        $template->delete_page($page1id);
        $events = $sink->get_events();

        $pagedeletedevent = array_shift($events);
        $templatedeletedevent = array_shift($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_bloockcert\event\page_deleted', $pagedeletedevent);
        $this->assertEquals($page1id, $pagedeletedevent->objectid);
        $this->assertDebuggingNotCalled();

        $this->assertInstanceOf('\mod_bloockcert\event\template_updated', $templatedeletedevent);
        $this->assertEquals($template->get_id(), $templatedeletedevent->objectid);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Tests the events are fired correctly when saving a page.
     *
     * @covers \mod_bloockcert\template::save_page
     */
    public function test_updating_a_page() {
        $template = \mod_bloockcert\template::create('Test name', \context_system::instance()->id);
        $pageid = $template->add_page();

        $width = 'pagewidth_' . $pageid;
        $height = 'pageheight_' . $pageid;
        $leftmargin = 'pageleftmargin_' . $pageid;
        $rightmargin = 'pagerightmargin_' . $pageid;

        $p = new \stdClass();
        $p->tid = $template->get_id();
        $p->$width = 1;
        $p->$height = 1;
        $p->$leftmargin = 1;
        $p->$rightmargin = 1;

        $sink = $this->redirectEvents();
        $template->save_page($p);
        $events = $sink->get_events();

        $pageupdatedevent = array_shift($events);
        $templateupdatedevent = array_shift($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_bloockcert\event\page_updated', $pageupdatedevent);
        $this->assertEquals($pageid, $pageupdatedevent->objectid);
        $this->assertDebuggingNotCalled();

        $this->assertInstanceOf('\mod_bloockcert\event\template_updated', $templateupdatedevent);
        $this->assertEquals($template->get_id(), $templateupdatedevent->objectid);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Tests the events are fired correctly when saving form elements.
     *
     * @covers \mod_bloockcert\element::save_form_elements
     */
    public function test_save_form_elements_insert() {
        $template = \mod_bloockcert\template::create('Test name', \context_system::instance()->id);
        $page1id = $template->add_page();

        $data = new \stdClass();
        $data->name = 'A name';
        $data->element = 'text';
        $data->text = 'Some text';
        $data->pageid = $page1id;
        $data->data = '';
        $data->font = $data->font ?? null;
        $data->fontsize = $data->fontsize ?? null;
        $data->colour = $data->colour ?? null;
        $data->width = $data->width ?? null;
        $data->refpoint = $data->refpoint ?? null;
        $data->alignment = $data->alignment ?? \mod_bloockcert\element::ALIGN_LEFT;
        $data->timemodified = time();

        $sink = $this->redirectEvents();
        $e = \mod_bloockcert\element_factory::get_element_instance($data);
        $e->save_form_elements($data);
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_bloockcert\event\element_created', $event);
        $this->assertEquals($e->get_id(), $event->objectid);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Tests the events are fired correctly when saving form elements.
     *
     * @covers \mod_bloockcert\element::save_form_elements
     */
    public function test_save_form_elements_update() {
        global $DB;

        $template = \mod_bloockcert\template::create('Test name', \context_system::instance()->id);
        $page1id = $template->add_page();

        // Add an element to the page.
        $element = new \stdClass();
        $element->pageid = $page1id;
        $element->name = 'Image';
        $elementid = $DB->insert_record('bloockcert_elements', $element);

        $element = $DB->get_record('bloockcert_elements', ['id' => $elementid]);

        // Add an element to the page.
        $element = new \bloockcertelement_text\element($element);

        $data = new \stdClass();
        $data->name = 'A new name';
        $data->text = 'New text';

        $sink = $this->redirectEvents();
        $element->save_form_elements($data);
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_bloockcert\event\element_updated', $event);
        $this->assertEquals($element->get_id(), $event->objectid);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Tests the events are fired correctly when copying to a template.
     *
     * @covers \mod_bloockcert\element::copy_to_template
     */
    public function test_copy_to_template() {
        global $DB;

        $template = \mod_bloockcert\template::create('Test name', \context_system::instance()->id);
        $page1id = $template->add_page();

        // Add an element to the page.
        $element = new \stdClass();
        $element->pageid = $page1id;
        $element->name = 'image';
        $element->element = 'image';
        $DB->insert_record('bloockcert_elements', $element);

        // Add another template.
        $template2 = \mod_bloockcert\template::create('Test name 2', \context_system::instance()->id);

        $sink = $this->redirectEvents();
        $template->copy_to_template($template2);
        $events = $sink->get_events();
        $this->assertCount(3, $events);

        $pagecreatedevent = array_shift($events);
        $elementcreatedevent = array_shift($events);
        $templatecreatedevent = array_shift($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_bloockcert\event\page_created', $pagecreatedevent);
        $this->assertEquals($pagecreatedevent->objectid, $pagecreatedevent->objectid);
        $this->assertDebuggingNotCalled();

        $this->assertInstanceOf('\mod_bloockcert\event\element_created', $elementcreatedevent);
        $this->assertEquals($elementcreatedevent->objectid, $elementcreatedevent->objectid);
        $this->assertDebuggingNotCalled();

        $this->assertInstanceOf('\mod_bloockcert\event\template_created', $templatecreatedevent);
        $this->assertEquals($template2->get_id(), $templatecreatedevent->objectid);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Tests the events are fired correctly when deleting an element
     *
     * @covers \mod_bloockcert\template::delete_element
     */
    public function test_deleting_an_element(): void {
        global $DB;

        $template = \mod_bloockcert\template::create('Test name', \context_system::instance()->id);
        $page1id = $template->add_page();

        // Add an element to the page.
        $element = new \stdClass();
        $element->pageid = $page1id;
        $element->name = 'image';
        $element->element = 'image';
        $element->id = $DB->insert_record('bloockcert_elements', $element);

        $sink = $this->redirectEvents();
        $template->delete_element($element->id);
        $events = $sink->get_events();

        $elementdeletedevent = array_shift($events);
        $templateupdatedevent = array_shift($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_bloockcert\event\element_deleted', $elementdeletedevent);
        $this->assertEquals($elementdeletedevent->objectid, $element->id);
        $this->assertDebuggingNotCalled();

        $this->assertInstanceOf('\mod_bloockcert\event\template_updated', $templateupdatedevent);
        $this->assertEquals($templateupdatedevent->objectid, $template->get_id());
        $this->assertDebuggingNotCalled();
    }
}
