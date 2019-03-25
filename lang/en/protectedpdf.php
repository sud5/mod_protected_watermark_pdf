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
 * Strings for component 'protectedpdf', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package    mod_protectedpdf
* @copyright  2017 Sudhanshu Gupta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['clicktodownload'] = 'Click {$a} link to download the file.';
$string['clicktoopen2'] = 'Click {$a} link to view the file.';
$string['configdisplayoptions'] = 'Select all options that should be available, existing settings are not modified. Hold CTRL key to select multiple fields.';
$string['configframesize'] = 'When a web page or an uploaded file is displayed within a frame, this value is the height (in pixels) of the top frame (which contains the navigation).';
$string['configparametersettings'] = 'This sets the default value for the Parameter settings pane in the form when adding some new protectedpdfs. After the first time, this becomes an individual user preference.';
$string['configpopup'] = 'When adding a new protected pdf which is able to be shown in a popup window, should this option be enabled by default?';
$string['configpopupdirectories'] = 'Should popup windows show directory links by default?';
$string['configpopupheight'] = 'What height should be the default height for new popup windows?';
$string['configpopuplocation'] = 'Should popup windows show the location bar by default?';
$string['configpopupmenubar'] = 'Should popup windows show the menu bar by default?';
$string['configpopupresizable'] = 'Should popup windows be resizable by default?';
$string['configpopupscrollbars'] = 'Should popup windows be scrollable by default?';
$string['configpopupstatus'] = 'Should popup windows show the status bar by default?';
$string['configpopuptoolbar'] = 'Should popup windows show the tool bar by default?';
$string['configpopupwidth'] = 'What width should be the default width for new popup windows?';
$string['contentheader'] = 'Content';
$string['displayoptions'] = 'Available display options';
$string['displayselect'] = 'Display';
$string['displayselect_help'] = 'This setting, together with the file type and whether the browser allows embedding, determines how the file is displayed. Options may include:

* Automatic - The best display option for the file type is selected automatically
* Embed - The file is displayed within the page below the navigation bar together with the file description and any blocks
* Force download - The user is prompted to download the file
* Open - Only the file is displayed in the browser window
* In pop-up - The file is displayed in a new browser window without menus or an address bar
* In frame - The file is displayed within a frame below the navigation bar and file description
* New window - The file is displayed in a new browser window with menus and an address bar';
$string['displayselect_link'] = 'mod/file/mod';
$string['displayselectexplain'] = 'Choose display type, unfortunately not all types are suitable for all files.';
$string['dnduploadprotectedpdf'] = 'Create file protectedpdf';
$string['encryptedcode'] = 'Encrypted code';
$string['filenotfound'] = 'Protectedpdf not found, sorry.';
$string['filterfiles'] = 'Use filters on file content';
$string['filterfilesexplain'] = 'Select type of file content filtering, please note this may cause problems for some Flash and Java applets. Please make sure that all text files are in UTF-8 encoding.';
$string['filtername'] = 'Protectedpdf names auto-linking';
$string['forcedownload'] = 'Force download';
$string['framesize'] = 'Frame height';
$string['legacyfiles'] = 'Migration of old course file';
$string['legacyfilesactive'] = 'Active';
$string['legacyfilesdone'] = 'Finished';
$string['modulename'] = 'Protected PDF';
$string['modulename_help'] = 'The file module enables a teacher to provide a file as a course protectedpdf. Where possible, the file will be displayed within the course interface; otherwise students will be prompted to download it. The file may include supporting files, for example an HTML page may have embedded images or Flash objects.

Note that students need to have the appropriate software on their computers in order to open the file.

A file may be used

* To share presentations given in class
* To include a mini website as a course protectedpdf
* To provide draft files of certain software programs (eg Photoshop .psd) so students can edit and submit them for assessment';
$string['modulename_link'] = 'mod/protectedpdf/view';
$string['modulenameplural'] = 'Protected PDFs';
$string['notmigrated'] = 'This legacy protectedpdf type ({$a}) was not yet migrated, sorry.';
$string['optionsheader'] = 'Display options';
$string['page-mod-protectedpdf-x'] = 'Any file module page';
$string['pluginadministration'] = 'Protectedpdf module administration';
$string['pluginname'] = 'Protected pdf';
$string['popupheight'] = 'Pop-up height (in pixels)';
$string['popupheightexplain'] = 'Specifies default height of popup windows.';
$string['popupprotectedpdf'] = 'This protectedpdf should appear in a popup window.';
$string['popupprotectedpdflink'] = 'If it didn\'t, click here: {$a}';
$string['popupwidth'] = 'Pop-up width (in pixels)';
$string['popupwidthexplain'] = 'Specifies default width of popup windows.';
$string['printintro'] = 'Display protected pdf description';
$string['printintroexplain'] = 'Display protected pdf description below content? Some display types may not display description even if enabled.';
$string['protectedpdf:addinstance'] = 'Add a new protected pdf';
$string['protectedpdfcontent'] = 'Protectedpdfs and subfolders';
$string['protectedpdfdetails_sizetype'] = '{$a->size} {$a->type}';
$string['protectedpdf:exportprotectedpdf'] = 'Export protected pdf';
$string['protectedpdf:view'] = 'View protected pdf';
$string['selectmainfile'] = 'Please select the main file by clicking the icon next to file name.';
$string['showsize'] = 'Show size';
$string['showsize_help'] = 'Displays the file size, such as \'3.1 MB\', beside links to the file.

If there are multiple files in this protected pdf, the total size of all files is displayed.';
$string['showsize_desc'] = 'Display file size on course page?';
$string['showtype'] = 'Show type';
$string['showtype_desc'] = 'Display file type (e.g. \'Word document\') on course page?';
$string['showtype_help'] = 'Displays the type of the file, such as \'Word document\', beside links to the file.

If there are multiple files in this protected pdf, the start file type is displayed.

If the file type is not known to the system, it will not display.';
$string['timingheader'] = 'Timing';
$string['setpassword'] = 'Protect with password';
$string['setpassword_help'] = 'If set to \'yes\', pdfs will be password protected.';
$string['allowprinting'] = 'Allow Printing without Comments';
$string['allowprinting_help'] = 'If set to \'yes\', pdfs will be allowed to print.';
$string['allowcomments'] = 'Allow Printing with Comments';
$string['allowcomments_help'] = 'If we allow comments to print then we are allowing the contents to be copied.';
$string['applywatermark'] = 'Apply Watermark';
$string['applywatermark_help'] = 'If set to \'yes\',watermark on pdf will be Applied';
$string['notifyusers'] = 'Notify Users';
$string['notifyusers_help'] = 'If set to \'yes\', all the users will be notified.';
$string['instruction'] = 'IMPORTANT: Your password for this pdf is your email address';
$string['contentwatermarking'] = 'Content of Watermarking';
$string['contentwatermarking_help'] = 'If not entered the default text "This individual at %email% is granted a single-user, non-exclusive license to use this book
 "';
$string['customizepassword'] = 'Customize Password';
$string['watermarking_placeholder'] = 'use %email% in place of email and %empid% for employee id';
$string['password'] = 'Enter Your Password';
$string['defaultpassword_email'] = 'Email Address of the user';
$string['password_help'] = 'Email Address of the user is the Default Password';
$string['enable'] = 'Enable';
$string['mailsubject'] = 'Award Course Materials available for ';
$string['hello'] = 'Hello ';
$string['text'] = 'Course materials are now available for Please download prior to class start on ';
$string['thanksmessage'] = 'We look forward to seeing you in the training session.';
$string['remark'] = 'Thank you';
