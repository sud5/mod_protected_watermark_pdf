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
 * PHPUnit data generator tests.
 *
 * @package mod_protectedpdf
 * @category phpunit
 * @copyright 2013 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * PHPUnit data generator testcase.
 *
 * @package    mod_protectedpdf
 * @category phpunit
 * @copyright 2013 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_protectedpdf_generator_testcase extends advanced_testcase {
    public function test_generator() {
        global $DB, $SITE;

        $this->resetAfterTest(true);

        // Must be a non-guest user to create protectedpdfs.
        $this->setAdminUser();

        // There are 0 protectedpdfs initially.
        $this->assertEquals(0, $DB->count_records('protectedpdf'));

        // Create the generator object and do standard checks.
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_protectedpdf');
        $this->assertInstanceOf('mod_protectedpdf_generator', $generator);
        $this->assertEquals('protectedpdf', $generator->get_modulename());

        // Create three instances in the site course.
        $generator->create_instance(array('course' => $SITE->id));
        $generator->create_instance(array('course' => $SITE->id));
        $protectedpdf = $generator->create_instance(array('course' => $SITE->id));
        $this->assertEquals(3, $DB->count_records('protectedpdf'));

        // Check the course-module is correct.
        $cm = get_coursemodule_from_instance('protectedpdf', $protectedpdf->id);
        $this->assertEquals($protectedpdf->id, $cm->instance);
        $this->assertEquals('protectedpdf', $cm->modname);
        $this->assertEquals($SITE->id, $cm->course);

        // Check the context is correct.
        $context = context_module::instance($cm->id);
        $this->assertEquals($protectedpdf->cmid, $context->instanceid);

        // Check that generated protectedpdf module contains a file.
        $fs = get_file_storage();
        $files = $fs->get_area_files($context->id, 'mod_protectedpdf', 'content', false, '', false);
        $this->assertEquals(1, count($files));
    }
}
