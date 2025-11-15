/**
 * QuizGetAddQuestionToQuizForm() function JS
 *
 * @package Quiz module
 */

$('.onchange-quiz-select').on('change', function() {
	this.form.action = this.form.action.replace(/&category_id=.*/, '&category_id=' + this.value);
});
