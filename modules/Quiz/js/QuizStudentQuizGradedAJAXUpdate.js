/**
 * QuizStudentQuizGradedAJAXUpdate() function JS
 *
 * @package Quiz module
 */

var pointsInputId = $('.points-input-id').last().val();

// Update Grade POINTS input with Total.
if ( $( pointsInputId ).length ) {
	$( pointsInputId ).val( $('.quiz-total-points').last().val() );
}

var pointsTotalId = $('.quiz-points-total-id').last().val();

// Update Total points header.
if ( $( pointsTotalId ).length ) {
	$( pointsTotalId ).html( $('.quiz-total-points').last().val() );
}
