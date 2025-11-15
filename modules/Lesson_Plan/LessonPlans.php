<?php
/**
 * Lesson Plans
 *
 * @package Lesson Plan module
 */

if ( file_exists( 'modules/School_Setup/includes/Addon.fnc.php' ) )
{
	// @since RosarioSIS 11.4
	require_once 'modules/School_Setup/includes/Addon.fnc.php';

	if ( function_exists( 'AddonUpsellPremium' )
		&& User( 'PROFILE' ) === 'admin'
		&& ( ! file_exists( 'modules/Lesson_Plan_Premium/' ) || ROSARIO_DEBUG ) )
	{
		// @since RosarioSIS 12.1
		echo AddonUpsellPremium( 'module', 'Lesson_Plan', 'PREMIUM.md' );
	}
}

if ( ! empty( $_REQUEST['cp_id'] ) )
{
	require_once 'modules/Lesson_Plan/Read.php';
}
else
{
	require_once 'modules/Lesson_Plan/LessonPlansList.php';
}
