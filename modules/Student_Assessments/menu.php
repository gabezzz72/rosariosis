<?php
/**
 * Student Assessments module menu
 *
 * @package RosarioSIS
 * @subpackage modules
 */

// School Setup (Admin only)
$menu['School_Setup']['admin']['Student_Assessments/AssessmentTypes.php'] = _( 'Assessment Types' );

// Student Info (All profiles)
$menu['Students']['admin']['Student_Assessments/Scores.php'] = _( 'Assessments' );
$menu['Students']['teacher']['Student_Assessments/Scores.php'] = _( 'Assessments' );
$menu['Students']['parent']['Student_Assessments/Scores.php'] = _( 'Assessments' );
$menu['Students']['student']['Student_Assessments/Scores.php'] = _( 'Assessments' );

// User profiles access
$exceptions['Students']['admin'] = array(
    'Student_Assessments/Scores.php' => true,
);
$exceptions['Students']['teacher'] = array(
    'Student_Assessments/Scores.php' => true,
);
$exceptions['Students']['parent'] = array(
    'Student_Assessments/Scores.php' => true,
);
$exceptions['Students']['student'] = array(
    'Student_Assessments/Scores.php' => true,
);
