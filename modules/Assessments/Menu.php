<?php
/**
 * Assessments module Menu.php
 *
 * This file adds the "Assessments" program to the "Students" module.
 *
 * @since 1.0
 */

// Add the "Assessments" program to the Students module.
// FIX: Moved from 'admin' category to 'main' (Student Info) category.
$menu['Students']['main']['Assessments'] = [
	'title' => _( 'Assessments' ),
	'gpa' => '0', // Not used in GPA calculations.
	'default' => '0', // Not a default program.
	'admin' => '0', // Accessible by non-admin users (teachers, parents) if permissions are set.
	'no_lang' => '0',
	'no_load' => '0',
	'force' => '0', // Force access.
];

// Add permissions for the Assessments program.
// FIX: The key MUST also be the module's folder name.
$exceptions['Assessments'] = [
	'C' => '1', // C for Create (add new).
	'R' => '1', // R for Read (view).
	'U' => '1', // U for Update (edit).
	'D' => '1', // D for Delete.
	'S' => '0', // S for Student (can student view?).
	'P' => '0', // P for Parent (can parent view?).
];
