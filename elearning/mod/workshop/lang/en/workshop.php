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
 * Strings for component 'workshop', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package    mod
 * @subpackage workshop
 * @copyright  2009 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['accesscontrol'] = 'Access control';
$string['aggregategrades'] = 'Re-calculate grades';
$string['aggregation'] = 'Grades aggregation';
$string['allocate'] = 'Allocate submissions';
$string['allocatedetails'] = 'expected: {$a->expected}<br />submitted: {$a->submitted}<br />to allocate: {$a->allocate}';
$string['allocation'] = 'Submission allocation';
$string['allocationdone'] = 'Allocation done';
$string['allocationerror'] = 'Allocation error';
$string['allsubmissions'] = 'All submissions';
$string['alreadygraded'] = 'Already graded';
$string['areainstructauthors'] = 'Instructions for submission';
$string['areainstructreviewers'] = 'Instructions for assessment';
$string['areasubmissionattachment'] = 'Submission attachments';
$string['areasubmissioncontent'] = 'Submission texts';
$string['assess'] = 'Assess';
$string['assessedexample'] = 'Assessed example submission';
$string['assessedsubmission'] = 'Assessed submission';
$string['assessingexample'] = 'Assessing example submission';
$string['assessingsubmission'] = 'Assessing submission';
$string['assessment'] = 'Assessment';
$string['assessmentby'] = 'by <a href="{$a->url}">{$a->name}</a>';
$string['assessmentbyfullname'] = 'Assessment by {$a}';
$string['assessmentbyyourself'] = 'Your assessment';
$string['assessmentdeleted'] = 'Assessment deallocated';
$string['assessmentend'] = 'Deadline for assessment';
$string['assessmentenddatetime'] = 'Assessment deadline: {$a->daydatetime} ({$a->distanceday})';
$string['assessmentform'] = 'Assessment form';
$string['assessmentofsubmission'] = '<a href="{$a->assessmenturl}">Assessment</a> of <a href="{$a->submissionurl}">{$a->submissiontitle}</a>';
$string['assessmentreference'] = 'Reference assessment';
$string['assessmentreferenceneeded'] = 'You have to assess this example submission to provide a reference assessment. Click \'Continue\' button to assess the submission.';
$string['assessmentsettings'] = 'Assessment settings';
$string['assessmentstart'] = 'Open for assessment from';
$string['assessmentstartdatetime'] = 'Open for assessment from {$a->daydatetime} ({$a->distanceday})';
$string['assessmentweight'] = 'Assessment weight';
$string['assignedassessments'] = 'Assigned submissions to assess';
$string['assignedassessmentsnone'] = 'You have no assigned submission to assess';
$string['backtoeditform'] = 'Back to editing form';
$string['byfullname'] = 'by <a href="{$a->url}">{$a->name}</a>';
$string['calculategradinggrades'] = 'Calculate assessment grades';
$string['calculategradinggradesdetails'] = 'expected: {$a->expected}<br />calculated: {$a->calculated}';
$string['calculatesubmissiongrades'] = 'Calculate submission grades';
$string['calculatesubmissiongradesdetails'] = 'expected: {$a->expected}<br />calculated: {$a->calculated}';
$string['clearaggregatedgrades'] = 'Clear all aggregated grades';
$string['clearaggregatedgrades_help'] = 'The aggregated grades for submission and grades for assessment will be reset. You can re-calculate these grades from scratch in Grading evaluation phase again.';
$string['clearassessments'] = 'Clear assessments';
$string['clearassessments_help'] = 'The calculated grades for submission and grades for assessment will be reset. The information how the assessment forms are filled is still kept, but all the reviewers must open the assessment form again and re-save it to get the given grades calculated again.';
$string['clearassessmentsconfirm'] = 'Are you sure you want to clear all assessment grades? You will not be able to get the information back on your own, reviewers will have to re-assess the allocated submissions.';
$string['clearaggregatedgradesconfirm'] = 'Are you sure you want to clear the calculated grades for submissions and grades for assessment?';
$string['configexamplesmode'] = 'Default mode of examples assessment in workshops';
$string['configgrade'] = 'Default maximum grade for submission in workshops';
$string['configgradedecimals'] = 'Default number of digits that should be shown after the decimal point when displaying grades.';
$string['configgradinggrade'] = 'Default maximum grade for assessment in workshops';
$string['configmaxbytes'] = 'Default maximum submission file size for all workshops on the site (subject to course limits and other local settings)';
$string['configstrategy'] = 'Default grading strategy for workshops';
$string['createsubmission'] = 'Submit';
$string['daysago'] = '{$a} days ago';
$string['daysleft'] = '{$a} days left';
$string['daystoday'] = 'today';
$string['daystomorrow'] = 'tomorrow';
$string['daysyesterday'] = 'yesterday';
$string['editassessmentform'] = 'Edit assessment form';
$string['editassessmentformstrategy'] = 'Edit assessment form ({$a})';
$string['editingassessmentform'] = 'Editing assessment form';
$string['editingsubmission'] = 'Editing submission';
$string['editsubmission'] = 'Edit submission';
$string['err_multiplesubmissions'] = 'While editing this form, another version of the submission has been saved. Multiple submissions per user are not allowed.';
$string['err_removegrademappings'] = 'Unable to remove the unused grade mappings';
$string['evaluategradeswait'] = 'Please wait until the assessments are evaluated and the grades are calculated';
$string['evaluation'] = 'Grading evaluation';
$string['evaluationmethod'] = 'Grading evaluation method';
$string['evaluationmethod_help'] = 'The grading evaluation method determines how the grade for assessment is calculated. There is currently just one option - comparison with the best assessment.';
$string['example'] = 'Example submission';
$string['exampleadd'] = 'Add example submission';
$string['exampleassess'] = 'Assess example submission';
$string['exampleassesstask'] = 'Assess examples';
$string['exampleassesstaskdetails'] = 'expected: {$a->expected}<br />assessed: {$a->assessed}';
$string['exampleassessments'] = 'Example submissions to assess';
$string['examplecomparing'] = 'Comparing assessments of example submission';
$string['exampledelete'] = 'Delete example';
$string['exampledeleteconfirm'] = 'Are you sure you want to delete the following example submission? Click \'Continue\' button to delete the submission.';
$string['exampleedit'] = 'Edit example';
$string['exampleediting'] = 'Editing example';
$string['exampleneedassessed'] = 'You have to assess all example submissions first';
$string['exampleneedsubmission'] = 'You have to submit your work and assess all example submissions first';
$string['examplesbeforeassessment'] = 'Examples are available after own submission and must be assessed before peer assessment';
$string['examplesbeforesubmission'] = 'Examples must be assessed before own submission';
$string['examplesmode'] = 'Mode of examples assessment';
$string['examplesubmissions'] = 'Example submissions';
$string['examplesvoluntary'] = 'Assessment of example submission is voluntary';
$string['feedbackauthor'] = 'Feedback for the author';
$string['feedbackby'] = 'Feedback by {$a}';
$string['feedbackreviewer'] = 'Feedback for the reviewer';
$string['formataggregatedgrade'] = '{$a->grade}';
$string['formataggregatedgradeover'] = '<del>{$a->grade}</del><br /><ins>{$a->over}</ins>';
$string['formatpeergrade'] = '<span class="grade">{$a->grade}</span> <span class="gradinggrade">({$a->gradinggrade})</span>';
$string['formatpeergradeover'] = '<span class="grade">{$a->grade}</span> <span class="gradinggrade">(<del>{$a->gradinggrade}</del> / <ins>{$a->gradinggradeover}</ins>)</span>';
$string['formatpeergradeoverweighted'] = '<span class="grade">{$a->grade}</span> <span class="gradinggrade">(<del>{$a->gradinggrade}</del> / <ins>{$a->gradinggradeover}</ins>)</span> @ <span class="weight">{$a->weight}</span>';
$string['formatpeergradeweighted'] = '<span class="grade">{$a->grade}</span> <span class="gradinggrade">({$a->gradinggrade})</span> @ <span class="weight">{$a->weight}</span>';
$string['givengrades'] = 'Grades given';
$string['gradecalculated'] = 'Calculated grade for submission';
$string['gradedecimals'] = 'Decimal places in grades';
$string['gradegivento'] = '&gt;';
$string['gradeitemassessment'] = '{$a->workshopname} (assessment)';
$string['gradeitemsubmission'] = '{$a->workshopname} (submission)';
$string['gradeover'] = 'Override grade for submission';
$string['gradesreport'] = 'Workshop grades report';
$string['gradereceivedfrom'] = '&lt;';
$string['gradeinfo'] = 'Grade: {$a->received} of {$a->max}';
$string['gradinggrade'] = 'Grade for assessment';
$string['gradinggrade_help'] = 'This setting specifies the maximum grade that may be obtained for submission assessment.';
$string['gradinggradecalculated'] = 'Calculated grade for assessment';
$string['gradinggradeof'] = 'Grade for assessment (of {$a})';
$string['gradinggradeover'] = 'Override grade for assessment';
$string['gradingsettings'] = 'Grading settings';
$string['chooseuser'] = 'Choose user...';
$string['iamsure'] = 'Yes, I am sure';
$string['info'] = 'Info';
$string['instructauthors'] = 'Instructions for submission';
$string['instructreviewers'] = 'Instructions for assessment';
$string['introduction'] = 'Introduction';
$string['latesubmissions'] = 'Late submissions';
$string['latesubmissions_desc'] = 'Allow submissions after the deadline';
$string['latesubmissions_help'] = 'If enabled, an author may submit their work after the submissions deadline or during the assessment phase. Late submissions cannot be edited though.';
$string['latesubmissionsallowed'] = 'Late submissions are allowed';
$string['maxbytes'] = 'Maximum file size';
$string['modulename'] = 'Workshop';
$string['modulenameplural'] = 'Workshops';
$string['mysubmission'] = 'My submission';
$string['nattachments'] = 'Maximum number of submission attachments';
$string['noexamples'] = 'No examples yet in this workshop';
$string['noexamplesformready'] = 'You must define the assessment form before providing example submissions';
$string['nogradeyet'] = 'No grade yet';
$string['nosubmissionfound'] = 'No submission found for this user';
$string['nosubmissions'] = 'No submissions yet in this workshop';
$string['nothingtoreview'] = 'Nothing to review';
$string['notassessed'] = 'Not assessed yet';
$string['notoverridden'] = 'Not overriden';
$string['noworkshops'] = 'There are no workshops in this course';
$string['noyoursubmission'] = 'You have not submitted your work yet';
$string['nullgrade'] = '-';
$string['participant'] = 'Participant';
$string['participantrevierof'] = 'Participant is reviewer of';
$string['participantreviewedby'] = 'Participant is reviewed by';
$string['phaseassessment'] = 'Assessment phase';
$string['phaseclosed'] = 'Closed';
$string['phaseevaluation'] = 'Grading evaluation phase';
$string['phasesetup'] = 'Setup phase';
$string['phasesubmission'] = 'Submission phase';
$string['pluginadministration'] = 'Workshop administration';
$string['pluginname'] = 'Workshop';
$string['prepareexamples'] = 'Prepare example submissions';
$string['previewassessmentform'] = 'Preview';
$string['publishedsubmissions'] = 'Published submissions';
$string['publishsubmission'] = 'Publish submission';
$string['publishsubmission_help'] = 'Published submissions are available to the others when the workshop is closed.';
$string['reassess'] = 'Re-assess';
$string['receivedgrades'] = 'Grades received';
$string['recentassessments'] = 'Workshop assessments:';
$string['recentsubmissions'] = 'Workshop submissions:';
$string['saveandclose'] = 'Save and close';
$string['saveandcontinue'] = 'Save and continue editing';
$string['saveandpreview'] = 'Save and preview';
$string['selfassessmentdisabled'] = 'Self-assessment disabled';
$string['someuserswosubmission'] = 'There is at least one author who has not yet submitted their work';
$string['sortasc'] = 'Ascending sort';
$string['sortdesc'] = 'Descending sort';
$string['strategy'] = 'Grading strategy';
$string['strategy_help'] = 'The grading strategy determines the assessment form used and the method of grading submissions. There are 4 options:

* Accumulative grading - Comments and a grade are given regarding specified aspects
* Comments - Comments are given regarding specified aspects but no grade can be given
* Number of errors - Comments and a yes/no assessment are given regarding specified assertions
* Rubric - A level assessment is given regarding specified criteria';
$string['strategyhaschanged'] = 'The workshop grading strategy has changed since the form was opened for editing.';
$string['submission'] = 'Submission';
$string['submissionby'] = 'Submission by {$a}';
$string['submissionattachment'] = 'Attachment';
$string['submissioncontent'] = 'Submission content';
$string['submissionend'] = 'Submissions deadline';
$string['submissionenddatetime'] = 'Submissions deadline: {$a->daydatetime} ({$a->distanceday})';
$string['submissiongrade'] = 'Grade for submission';
$string['submissiongrade_help'] = 'This setting specifies the maximum grade that may be obtained for submitted work.';
$string['submissiongradeof'] = 'Grade for submission (of {$a})';
$string['submissionsettings'] = 'Submission settings';
$string['submissionstart'] = 'Open for submissions from';
$string['submissionstartdatetime'] = 'Open for submissions from {$a->daydatetime} ({$a->distanceday})';
$string['submissiontitle'] = 'Title';
$string['switchingphase'] = 'Switching phase';
$string['switchphase'] = 'Switch phase';
$string['switchphase10info'] = 'You are about to switch the workshop into the <strong>Setup phase</strong>. In this phase, users cannot modify their submissions or their assessments. Teachers may use this phase to change workshop settings, modify the grading strategy of tweak assessment forms.';
$string['switchphase20info'] = 'You are about to switch the workshop into the <strong>Submission phase</strong>. Students may submit their work during this phase (within the submission access control dates, if set). Teachers may allocate submissions for peer review.';
$string['switchphase30info'] = 'You are about to switch the workshop into the <strong>Assessment phase</strong>. In this phase, reviewers may assess the submissions they have been allocated (within the assessment access control dates, if set).';
$string['switchphase40info'] = 'You are about to switch the workshop into the <strong>Grading evaluation phase</strong>. In this phase, users cannot modify their submissions or their assessments. Teachers may use the grading evaluation tools to calculate final grades and provide feedback for reviewers.';
$string['switchphase50info'] = 'You are about to close the workshop. This will result in the calculated grades appearing in the gradebook. Students may view their submissions and their submission assessments.';
$string['taskassesspeers'] = 'Assess peers';
$string['taskassesspeersdetails'] = 'total: {$a->total}<br />pending: {$a->todo}';
$string['taskassessself'] = 'Assess yourself';
$string['taskinstructauthors'] = 'Provide instructions for submission';
$string['taskinstructreviewers'] = 'Provide instructions for assessment';
$string['taskintro'] = 'Set the workshop introduction';
$string['tasksubmit'] = 'Submit your work';
$string['toolbox'] = 'Workshop toolbox';
$string['undersetup'] = 'The workshop is currently being set up. Please wait until it is switched to the next phase.';
$string['useexamples'] = 'Use examples';
$string['useexamples_desc'] = 'Example submissions are provided for practice in assessing';
$string['useexamples_help'] = 'If enabled, users can try assessing one or more example submissions and compare their assessment with a reference assessment. The grade is not counted in the grade for assessment.';
$string['usepeerassessment'] = 'Use peer assessment';
$string['usepeerassessment_desc'] = 'Students may assess the work of others';
$string['usepeerassessment_help'] = 'If enabled, a user may be allocated submissions from other users to assess and will receive a grade for assessment in addition to a grade for their own submission.';
$string['userdatecreated'] = 'submitted on <span>{$a}</span>';
$string['userdatemodified'] = 'modified on <span>{$a}</span>';
$string['userplan'] = 'Workshop planner';
$string['userplan_help'] = 'The workshop planner displays all phases of the activity and lists the tasks for each phase. The current phase is highlighted and task completion is indicated with a tick.';
$string['useselfassessment'] = 'Use self-assessment';
$string['useselfassessment_help'] = 'If enabled, a user may be allocated their own submission to assess and will receive a grade for assessment in addition to a grade for their submission.';
$string['useselfassessment_desc'] = 'Students may assess their own work';
$string['weightinfo'] = 'Weight: {$a}';
$string['withoutsubmission'] = 'Reviewer without own submission';
$string['workshop:allocate'] = 'Allocate submissions for review';
$string['workshop:editdimensions'] = 'Edit assessment forms';
$string['workshopfeatures'] = 'Workshop features';
$string['workshop:manageexamples'] = 'Manage example submissions';
$string['workshopname'] = 'Workshop name';
$string['workshop:overridegrades'] = 'Override calculated grades';
$string['workshop:peerassess'] = 'Peer assess';
$string['workshop:publishsubmissions'] = 'Publish submissions';
$string['workshop:submit'] = 'Submit';
$string['workshop:switchphase'] = 'Switch phase';
$string['workshop:view'] = 'View workshop';
$string['workshop:viewallassessments'] = 'View all assessments';
$string['workshop:viewallsubmissions'] = 'View all submissions';
$string['workshop:viewauthornames'] = 'View author names';
$string['workshop:viewauthorpublished'] = 'View authors of published submissions';
$string['workshop:viewpublishedsubmissions'] = 'View published submissions';
$string['workshop:viewreviewernames'] = 'View reviewer names';
$string['yourassessment'] = 'Your assessment';
$string['yoursubmission'] = 'Your submission';
