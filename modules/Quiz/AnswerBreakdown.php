<?php
/**
 * Answer Breakdown (Premium program)
 *
 * @package Quiz module
 */

$lang_2_chars = mb_substr( $_SESSION['locale'], 0, 2 );

$screenshot_lang = '';

if ( $lang_2_chars === 'fr'
	|| $lang_2_chars === 'es' )
{
	$screenshot_lang = '_' . $lang_2_chars;
}

$screenshot_url = 'https://www.rosariosis.org/wp-content/uploads/2018/12/quiz_premium_screenshot' .
	$screenshot_lang . '.png';

require_once 'modules/Quiz/includes/UpsellPremium.inc.php';
