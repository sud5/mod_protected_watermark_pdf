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
 * Protectedpdf configuration form
 *
 * @package    mod_protectedpdf
 * @copyright  2017 Sudhanshu Gupta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/course/moodleform_mod.php');
require_once($CFG->dirroot . '/mod/protectedpdf/locallib.php');
require_once($CFG->libdir . '/filelib.php');

class mod_protectedpdf_mod_form extends moodleform_mod {

    function definition() {
        global $CFG, $DB, $COURSE;
        $mform = & $this->_form;

        $config = get_config('protectedpdf');

        if ($this->current->instance and $this->current->tobemigrated) {
            // protectedpdf not migrated yet
            $protectedpdf_old = $DB->get_record('protectedpdf_old', array('oldid' => $this->current->instance));
            $mform->addElement('static', 'warning', '', get_string('notmigrated', 'protectedpdf', $protectedpdf_old->type));
            $mform->addElement('cancel');
            $this->standard_hidden_coursemodule_elements();
            return;
        }

        //-------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));
        $mform->addElement('text', 'name', get_string('name'), array('size' => '48'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $this->standard_intro_elements(false);

        //-------------------------------------------------------
        $mform->addElement('header', 'contentsection', get_string('contentheader', 'protectedpdf'));
        $mform->setExpanded('contentsection');

        $filemanager_options = array();
        $filemanager_options['accepted_types'] = '.pdf';
        $filemanager_options['maxbytes'] = 0;
        $filemanager_options['maxfiles'] = -1;
        $filemanager_options['mainfile'] = true;

        $mform->addElement('filemanager', 'files', get_string('selectfiles'), null, $filemanager_options);

        // add legacy files flag only if used
//        if (isset($this->current->legacyfiles) and $this->current->legacyfiles != PROTECTEDPDFLIB_LEGACYFILES_NO) {
//            $options = array(PROTECTEDPDFLIB_LEGACYFILES_DONE   => get_string('legacyfilesdone', 'protectedpdf'),
//                             PROTECTEDPDFLIB_LEGACYFILES_ACTIVE => get_string('legacyfilesactive', 'protectedpdf'));
//            $mform->addElement('select', 'legacyfiles', get_string('legacyfiles', 'protectedpdf'), $options);
//        }
        //  Ask wheteher to allow printing or not
        $mform->addElement('advcheckbox', 'allowprinting', get_string('allowprinting', 'protectedpdf'));
        $mform->addHelpButton('allowprinting', 'allowprinting', 'mod_protectedpdf');
        $mform->setDefault('allowprinting', false);
        //  Ask wheteher to allow printing or not
        $mform->addElement('advcheckbox', 'allowcomments', get_string('allowcomments', 'protectedpdf'));
        $mform->addHelpButton('allowcomments', 'allowcomments', 'mod_protectedpdf');
        $mform->setDefault('allowcomments', false);
        //Adding to ask for content of watermarking
        $mform->addElement('text', 'contentwatermarking', get_string('contentwatermarking', 'mod_protectedpdf'), array('size' => '48', 'placeholder' => get_string('watermarking_placeholder', 'mod_protectedpdf')));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('contentwatermarking', PARAM_TEXT);
        } else {
            $mform->setType('contentwatermarking', PARAM_CLEANHTML);
        }
        $mform->addRule('contentwatermarking', get_string('maximumchars', '', 220), 'maxlength', 220, 'client');
        $mform->addHelpButton('contentwatermarking', 'contentwatermarking', 'mod_protectedpdf');
//        $mform->setDefault('contentwatermarking', true);
//  Ask wheteher to apply watermark or not
        $mform->addElement('advcheckbox', 'applywatermark', get_string('applywatermark', 'protectedpdf'));
        $mform->addHelpButton('applywatermark', 'applywatermark', 'mod_protectedpdf');
        $mform->setDefault('applywatermark', true);
        //Adding to ask for password protected or not
        $mform->addElement('advcheckbox', 'setpassword', get_string('setpassword', 'protectedpdf'));
        $mform->addHelpButton('setpassword', 'setpassword', 'mod_protectedpdf');
        $mform->setDefault('setpassword', true);

        //Adding to ask for notify the users or not
        $group = array('Not Selected');
        $moodlegroups = $DB->get_records('groups', array('courseid' => $COURSE->id), null, 'id,name');
        // print_object($moodlegroups);
        foreach ($moodlegroups as $key => $moodlegroup) {
            $group[] = $moodlegroup->name;
        }
//        $mform->addElement('advcheckbox', 'notifyusers', get_string('notifyusers', 'protectedpdf'));
//        $mform->addHelpButton('notifyusers', 'notifyusers', 'mod_protectedpdf');
//        $mform->setDefault('notifyusers', true);
        //------------------------------------------------------
        $mform->addElement('header', 'customizepassword', get_string('customizepassword', 'protectedpdf'));
        $mform->addElement('text', 'password', get_string('password', 'protectedpdf'), array('size' => '48', 'placeholder' => get_string('defaultpassword_email', 'mod_protectedpdf')));
        $mform->addHelpButton('password', 'password', 'protectedpdf');
        $mform->setType('password', PARAM_RAW);
        //-------------------------------------------------------
        //whether or not activity completion required or not
        $this->standard_coursemodule_elements();

        //-------------------------------------------------------
        $this->add_action_buttons();

        //-------------------------------------------------------
        $mform->addElement('hidden', 'revision');
        $mform->setType('revision', PARAM_INT);
        $mform->setDefault('revision', 1);
    }

    function data_preprocessing(&$default_values) {
        if ($this->current->instance and ! $this->current->tobemigrated) {
            $draftitemid = file_get_submitted_draft_itemid('files');
            file_prepare_draft_area($draftitemid, $this->context->id, 'mod_protectedpdf', 'content', 0, array('subdirs' => true));
            $default_values['files'] = $draftitemid;
        }
        if (!empty($default_values['displayoptions'])) {
            $displayoptions = unserialize($default_values['displayoptions']);
            if (isset($displayoptions['printintro'])) {
                $default_values['printintro'] = $displayoptions['printintro'];
            }
            if (!empty($displayoptions['popupwidth'])) {
                $default_values['popupwidth'] = $displayoptions['popupwidth'];
            }
            if (!empty($displayoptions['popupheight'])) {
                $default_values['popupheight'] = $displayoptions['popupheight'];
            }
            if (!empty($displayoptions['showsize'])) {
                $default_values['showsize'] = $displayoptions['showsize'];
            } else {
                // Must set explicitly to 0 here otherwise it will use system
                // default which may be 1.
                $default_values['showsize'] = 0;
            }
            if (!empty($displayoptions['showtype'])) {
                $default_values['showtype'] = $displayoptions['showtype'];
            } else {
                $default_values['showtype'] = 0;
            }
        }
    }

    function definition_after_data() {
        if ($this->current->instance and $this->current->tobemigrated) {
            // protectedpdf not migrated yet
            return;
        }

        parent::definition_after_data();
    }

    function validation($data, $files) {
        global $USER;

        $errors = parent::validation($data, $files);

        $usercontext = context_user::instance($USER->id);
        $fs = get_file_storage();
        if (!$files = $fs->get_area_files($usercontext->id, 'user', 'draft', $data['files'], 'sortorder, id', false)) {
            $errors['files'] = get_string('required');
            return $errors;
        }
        if (count($files) == 1) {
            // no need to select main file if only one picked
            return $errors;
        } else if (count($files) > 1) {
            $mainfile = false;
            foreach ($files as $file) {
                if ($file->get_sortorder() == 1) {
                    $mainfile = true;
                    break;
                }
            }
            // set a default main file
            if (!$mainfile) {
                $file = reset($files);
                file_set_sortorder($file->get_contextid(), $file->get_component(), $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $file->get_filename(), 1);
            }
        }
        return $errors;
    }

}
