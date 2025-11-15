<?php
/**
 * Upsell Premium
 *
 * @package Quiz module
 */

if ( file_exists( 'modules/School_Setup/includes/Addon.fnc.php' ) )
{
	// @since RosarioSIS 11.4
	require_once 'modules/School_Setup/includes/Addon.fnc.php';

	if ( function_exists( 'AddonUpsellPremium' )
		&& User( 'PROFILE' ) === 'admin'
		&& ( ! file_exists( 'modules/Quiz_Premium/' ) || ROSARIO_DEBUG ) )
	{
		// @since RosarioSIS 12.1
		echo AddonUpsellPremium( 'module', 'Quiz', 'PREMIUM.md' );
	}
}

DrawHeader( ProgramTitle() );

$lang_2_chars = mb_substr( $_SESSION['locale'], 0, 2 );

$link_lang = '';

if ( $lang_2_chars === 'fr'
	|| $lang_2_chars === 'es' )
{
	$link_lang = $lang_2_chars . '/';
}

$note[] = sprintf(
	dgettext( 'Quiz', 'This program is available in the %s module.' ),
	'<a href="https://www.rosariosis.org/' . $link_lang . 'modules/quiz/#premium-module" target="_blank">' .
		dgettext( 'Quiz', 'Quiz Premium' ) . '</a>'
);

echo ErrorMessage( $note, 'note' );

$screenshot_lang = '';

if ( $lang_2_chars === 'fr'
	|| $lang_2_chars === 'es' )
{
	$screenshot_lang = '_' . $lang_2_chars;
}

echo '<img src="' . URLEscape( $screenshot_url ) . '" width="1440" class="center">';
