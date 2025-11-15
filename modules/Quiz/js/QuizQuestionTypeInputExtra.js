/**
 * QuizQuestionTypeInputExtra() function JS
 *
 * @package Quiz module
 */

$(function() {
	$('.onchange-quiz-question-type-input').on('change', function() {
		quizQuestionAnswerInputUpdate($('#quiz_answer_input_id').val(), this.value);
	});
});

var quizQuestionAnswerInputUpdate = function( answerInputId, typeInputValue ) {
	var answerInput = $( '#' + answerInputId );

	if ( ! answerInput.length ) {
		return false;
	}

	var questionTypeOptions = JSON.parse($('#quiz_question_type_options').val());

	if ( ! questionTypeOptions.hasOwnProperty( typeInputValue ) ) {
		return false;
	}

	var typeOptions = questionTypeOptions[ typeInputValue ];

	// Update Answer input.
	answerInput.value = '';
	answerInput.attr( 'required', typeOptions.required );
	answerInput.attr( 'placeholder', typeOptions.placeholder );

	var answerLabel = answerInput.nextAll( 'label' );

	// Update Answer label.
	answerLabel.html( typeOptions.title );

	if ( typeOptions.title === '' ) {
		// If empty title, hide input.
		answerLabel.hide();
		answerInput.hide();
	} else {
		answerLabel.show();
		answerInput.show();
	}

	return true;
};
