<?php

// Add menu item under Students
$_ROSARIO_MENU['Students']['Assessments'] = array(
    'title' => _('Assessments'),
    'link'  => 'modules/Assessments/Assessments.php',
    'search' => false
);

// Add menu item for Assessment Types (Admin only)
if (User('PROFILE') == 'admin' || User('PROFILE_ID') == 1) {
    $_ROSARIO_MENU['School_Setup']['Assessment Types'] = array(
        'title' => _('Assessment Types'),
        'link'  => 'modules/Assessments/AssessmentTypes.php',
        'search' => false
    );
}
