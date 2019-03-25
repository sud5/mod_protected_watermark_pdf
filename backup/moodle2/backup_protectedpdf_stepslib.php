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
 * Define all the backup steps that will be used by the backup_protectedpdf_activity_task
 *
 * @package    mod_protectedpdf
 * @copyright  2017 Sudhanshu Gupta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Define the complete protectedpdf structure for backup, with file and id annotations
 */
class backup_protectedpdf_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {

        // To know if we are including userinfo
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated
        $protectedpdf = new backup_nested_element('protectedpdf', array('id'), array(
            'name', 'intro', 'introformat', 'tobemigrated',
            'legacyfiles', 'legacyfileslast', 'display',
            'displayoptions', 'filterfiles', 'revision', 'timemodified','setpassword','contentwatermarking','password',
            'allowprinting','applywatermark','allowcomments'));

        // Build the tree
        // (love this)

        // Define sources
        $protectedpdf->set_source_table('protectedpdf', array('id' => backup::VAR_ACTIVITYID));

        // Define id annotations
        // (none)

        // Define file annotations
        $protectedpdf->annotate_files('mod_protectedpdf', 'intro', null); // This file areas haven't itemid
        $protectedpdf->annotate_files('mod_protectedpdf', 'content', null); // This file areas haven't itemid

        // Return the root element (protectedpdf), wrapped into standard activity structure
        return $this->prepare_activity_structure($protectedpdf);
    }
}
