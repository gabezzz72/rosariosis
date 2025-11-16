<?php

// Add Assessments under the Students menu
$_ROSARIO_MENU['students']['Assessments'] = array(
    'title' => _('Assessments'),
    'link'  => 'modules/Assessments/Assessments.php',
    'search' => false
);

// Admin-only menu for assessment types
if (User('PROFILE_ID') == 1) {
    $_ROSARIO_MENU['school_setup']['Assessment Types'] = array(
        'title' => _('Assessment Types'),
        'link'  => 'modules/Assessments/AssessmentTypes.php',
        'search' => false
    );
}
