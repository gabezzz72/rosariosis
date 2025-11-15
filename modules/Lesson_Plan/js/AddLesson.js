/**
 * Add item/part list JS
 *
 * 1. Clone only once in global var, before input values are set.
 * Get HTML to fix jQuery insertAfter replacing last list...
 *
 * 2. Empty input & textarea values (just in case).
 *
 * 3. Update input name, id, link onclick & mdPreview id:
 * Replace digit with current lesson plan item i/ID.
 *
 * @link https://stackoverflow.com/questions/51286061/jquery-clone-element-and-replace-part-of-its-name
 */

var lessonPlanItemI = 1,
	$lessonPlanItemList = $( '.lesson-item-list' ).first().clone().html();

function LessonPlanAddItemList() {
	$( '.lesson-item-list' ).last().after( '<div class="lesson-item-list">' + $lessonPlanItemList + '</div>' );

	lessonPlanItemI++;

	var $lastList = $( '.lesson-item-list' ).last(),
		$inputs = $lastList.find('input,textarea');

	$inputs.val('');

	$inputs.prop('name', function(_, curr) {
		return curr.replace(/\d+/, lessonPlanItemI);
	});

	$inputs.prop('id', function(_, curr) {
		return curr.replace(/\d+/, lessonPlanItemI);
	});

	$lastList.find('a').attr('onclick', function(_, curr) {
		if ( ! curr ) {
			return curr;
		}

		return curr.replace(/\d+/, lessonPlanItemI);
	});

	$lastList.find('.markdown-to-html').prop('id', function(_, curr) {
		return curr.replace(/\d+/, lessonPlanItemI);
	});
}

$('.onclick-lesson-plan-add-item').on('click', LessonPlanAddItemList);
