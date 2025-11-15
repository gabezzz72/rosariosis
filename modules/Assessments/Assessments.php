<?php
/**
 * Assessments module main file
 *
 * This program allows users to define custom assessments (like PSAT)
 * and enter scores for students.
 *
 * @since 1.0
 */

// Main entry point.
// FIX: Paths must be relative to the webroot (/var/www/html/),
// because this file is included by /var/www/html/Modules.php.
require_once 'ProgramFunctions/DrawHeader.f.php';
require_once 'ProgramFunctions/program_init.f.php';
require_once 'ProgramFunctions/SearchForm.f.php';

// Ensure a student is selected.
if ( empty( $_REQUEST['student_id'] ) )
{
	// No student selected.
	DrawHeader( _( 'Assessments' ) );

	echo '<form action="' . URLEscape( 'Modules.php?modname=' . $_REQUEST['modname']  ) . '" method="GET">';
	$extra = [
		'search_modname' => 'Students',
		'include_inactive' => true,
		'ignore_area' => true,
	];
	SearchForm( $extra );
	echo '</form>';

	DrawFooter();
	exit;
}

// Student is selected, proceed.
DrawHeader( _( 'Assessments' ) );

// Program logic goes here.

// Handle form submissions (Add Score, Add Assessment Type, Delete, etc.)
if ( ! empty( $_REQUEST['buttonAction'] ) )
{
	// Check for a valid token to prevent CSRF.
	// Always check this in a real application.
	// if ( ! VerifyToken() ) {
	//	... exit or error ...
	// }

	// --- Add New Assessment Type ---
	if ( $_REQUEST['buttonAction'] === 'AddType' && ! empty( $_REQUEST['new_assessment_title'] ) )
	{
		$new_title = trim( $_REQUEST['new_assessment_title'] );

		if ( ! empty( $new_title ) )
		{
			// Check if it already exists for this school.
			$exists_sql = "SELECT 1 FROM assessments
				WHERE SCHOOL_ID='" . UserSchool() . "'
				AND TITLE='" . DBEscapeString( $new_title ) . "'";
			$exists = DBGetOne( $exists_sql );

			if ( empty( $exists ) )
			{
				$insert_sql = "INSERT INTO assessments (SCHOOL_ID, SYEAR, TITLE)
					VALUES ('" . UserSchool() . "', '" . UserSyear() . "', '" . DBEscapeString( $new_title ) . "')";
				DBQuery( $insert_sql );
				echo '<div class="notice-success">' . _( 'Assessment type created.' ) . '</div>';
			}
			else
			{
				echo '<div class="notice-error">' . _( 'Assessment type already exists.' ) . '</div>';
			}
		}
	}

	// --- Add New Score for Student ---
	if ( $_REQUEST['buttonAction'] === 'AddScore' && ! empty( $_REQUEST['assessment_id'] ) && isset( $_REQUEST['score'] ) )
	{
		$assessment_id = (int) $_REQUEST['assessment_id'];
		$score = trim( $_REQUEST['score'] );
		$score_date = RequestedDate( 'score_date' ); // Get formatted date from datepicker.

		if ( $assessment_id > 0 && $score !== '' )
		{
			$insert_sql = "INSERT INTO assessment_scores (STUDENT_ID, ASSESSMENT_ID, SCORE_DATE, SCORE)
				VALUES ('" . (int) $_REQUEST['student_id'] . "',
				'" . $assessment_id . "',
				'" . $score_date . "',
				'" . DBEscapeString( $score ) . "')";
			DBQuery( $insert_sql );
			echo '<div class="notice-success">' . _( 'Score added.' ) . '</div>';
		}
		else
		{
			echo '<div class="notice-error">' . _( 'Please select an assessment and enter a score.' ) . '</div>';
		}
	}

	// --- Delete a Score ---
	if ( $_REQUEST['buttonAction'] === 'DeleteScore' && ! empty( $_REQUEST['score_id'] ) )
	{
		$score_id = (int) $_REQUEST['score_id'];

		$delete_sql = "DELETE FROM assessment_scores
			WHERE SCORE_ID='" . $score_id . "'
			AND STUDENT_ID='" . (int) $_REQUEST['student_id'] . "'";
		DBQuery( $delete_sql );
		echo '<div class="notice-success">' . _( 'Score deleted.' ) . '</div>';
	}
}


// --- 1. Display Form to Add New Assessment Score ---

echo '<form action="' . URLEscape( 'Modules.php?modname=' . $_REQUEST['modname'] . '&student_id=' . $_REQUEST['student_id'] ) . '" method="POST">';
// Add token for security
// DrawObject( array( 'buttonAction' => 'AddScore' ), array(), 'hidden' );

echo '<table class="col-1-1 width-100-col-1 center">';

// --- Select Assessment Type ---
$assessments_sql = "SELECT ASSESSMENT_ID, TITLE
	FROM assessments
	WHERE SCHOOL_ID='" . UserSchool() . "'
	ORDER BY TITLE";
$assessments_result = DBGet( $assessments_sql, [], [ 'Assessment' ] );

$assessment_options = [];
if ( ! empty( $assessments_result['Assessment'] ) )
{
	foreach ( (array) $assessments_result['Assessment'] as $assessment )
	{
		$assessment_options[$assessment['ASSESSMENT_ID']] = $assessment['TITLE'];
	}
}

echo '<tr><td>' . _( 'Assessment' ) . '</td><td>' .
	DrawSelectInput(
		'assessment_id',
		_( 'Assessment' ),
		$assessment_options,
		'',
		'required',
		false
	) . '</td></tr>';

// --- Score Input ---
echo '<tr><td>' . _( 'Score' ) . '</td><td>' .
	DrawTextInput(
		'score',
		_( 'Score' ),
		'text',
		'size=10 maxlength=20',
		true,
		'' // Default score
	) . '</td></tr>';

// --- Date Input ---
echo '<tr><td>' . _( 'Date' ) . '</td><td>' .
	DrawDateInput(
		'score_date',
		_( 'Date' ),
		'',
		true,
		true,
		'',
		true
	) . '</td></tr>';

// --- Submit Button ---
echo '<tr><td colspan="2" class="center">' .
	DrawButton( _( 'Add Score' ), 'primary', 'buttonAction="AddScore"' ) .
	'</td></tr>';

echo '</table></form><br>';


// --- 2. Display Existing Scores for this Student ---

$scores_sql = "SELECT s.SCORE_ID, s.SCORE, s.SCORE_DATE, a.TITLE
	FROM assessment_scores s
	JOIN assessments a ON s.ASSESSMENT_ID=a.ASSESSMENT_ID
	WHERE s.STUDENT_ID='" . (int) $_REQUEST['student_id'] . "'
	ORDER BY s.SCORE_DATE DESC, a.TITLE";
$scores_result = DBGet( $scores_sql );

$columns = [
	'TITLE' => _( 'Assessment' ),
	'SCORE' => _( 'Score' ),
	'SCORE_DATE' => _( 'Date' ),
	'DELETE' => _( 'Delete' ),
];

$link = [];
$link['DELETE']['link'] = URLEscape( 'Modules.php?modname=' . $_REQUEST['modname'] . '&student_id=' . $_REQUEST['student_id'] . '&buttonAction=DeleteScore' );
$link['DELETE']['variables'] = [ 'score_id' => 'SCORE_ID' ];

$LO_options = [
	'responsive' => true,
	'alternate' => true,
	'link' => $link,
	'header_color' => 'gray',
	'download' => false,
	'search' => false,
];

ListOutput( $scores_result, $columns, 'Student Assessment Scores', 'student_scores_table', $LO_options );


// --- 3. Display Form to Add New Assessment Type (Admin only) ---

// You should wrap this in a security check, e.g. if ( User( 'PROFILE' ) === 'admin' )
echo '<br><hr><br>';
echo '<form action="' . URLEscape( 'Modules.php?modname=' . $_REQUEST['modname'] . '&student_id=' . $_REQUEST['student_id'] ) . '" method="POST">';
// Add token for security
// DrawObject( array( 'buttonAction' => 'AddType' ), array(), 'hidden' );

echo '<table class="col-1-1 width-100-col-1 center">';
echo '<tr><th colspan="2">' . _( 'Add New Assessment Type' ) . '</th></tr>';

echo '<tr><td>' . _( 'Title' ) . '</td><td>' .
	DrawTextInput(
		'new_assessment_title',
		_( 'Title' ),
		'text',
		'size=20 maxlength=100',
		true,
		'' // Default title
	) . '</td></tr>';

echo '<tr><td colspan="2" class="center">' .
	DrawButton( _( 'Create Assessment Type' ), 'primary', 'buttonAction="AddType"' ) .
	'</td></tr>';

echo '</table></form>';


DrawFooter();
