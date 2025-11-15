/**
 * ParentAgreementLoginForma() function JS
 *
 * @package Parent Agreement plugin
 */

$('#agreement-form input[type="reset"]').click(function() {
	// Logout.
	window.location.href = "index.php?modfunc=logout&token=" + $('#logout_token').val();
});
