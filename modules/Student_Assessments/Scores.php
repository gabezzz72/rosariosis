<?php
/**
 * Student Assessments Scores
 * Admin: Read/Write
 * Others: Read-Only
 *
 * @package RosarioSIS
 * @subpackage modules
 */

// Load the required functions files that EXIST on your server
require_once __DIR__ . '/../../functions/DBGet.fnc.php'; // For DBGet()
require_once __DIR__ . '/../../functions/Buttons.php'; // For SubmitButton()
require_once __DIR__ . '/../../functions/Inputs.php'; // For DateInput(), PrepareDate(), ProperDate()
require_once __DIR__ . '/../../functions/User.fnc.php'; // For Student(), User(), UserSyear(), UserSchool()
require_once __DIR__ . '/../../functions/Current.php'; // FIX: Add this file for SetStudent()
require_once __DIR__ . '/../../functions/ErrorMessage.fnc.php'; // For ErrorMessage(), Note()
require_once __DIR__ . '/../../functions/AllowEdit.fnc.php'; // For AllowEdit()
require_once __DIR__ . '/../../functions/PopTable.fnc.php'; // For PopTable()
require_once __DIR__ . '/../../functions/DrawHeader.fnc.php'; // For DrawHeader()

// Set the student
// FIX: Use SetStudent() to find student from session OR request
// Then, check if a student was successfully set.
SetStudent( $_REQUEST['student_id'] ?? '' );

if ( ! Student( 'STUDENT_ID' ) )
{
    ErrorMessage( array( _( 'You must select a student first.' ) ), 'fatal' );
}

DrawHeader( _( 'Assessments' ) );

$can_edit = AllowEdit();

// Handle form submission
if ( ! empty( $_POST['values'] )
    && $can_edit )
{
    foreach ( (array) $_POST['values'] as $assessment_type_id => $values )
    {
        $assessment_score_id = $values['assessment_score_id'];

        // Prepare values
        $assessment_date = PrepareDate( $values['assessment_date'] );
        $score = $values['score'];
        $comment = $values['comment'];

        if ( empty( $assessment_score_id ) )
        {
            // Add new score
            if ( ! empty( $assessment_date ) || ! empty( $score ) || ! empty( $comment ) )
            {
                DBQuery( "INSERT INTO student_assessments_scores
                    (student_id, assessment_type_id, syear, assessment_date, score, comment)
                    VALUES(
                        '" . Student( 'STUDENT_ID' ) . "',
                        '" . (int) $assessment_type_id . "',
                        '" . UserSyear() . "',
                        " . ( $assessment_date ? "'" . $assessment_date . "'" : 'NULL' ) . ",
                        '" . $score . "',
                        '" . $comment . "'
                    )" );
            }
        }
        else
        {
            // Update existing score
            DBQuery( "UPDATE student_assessments_scores
                SET assessment_date=" . ( $assessment_date ? "'" . $assessment_date . "'" : 'NULL' ) . ",
                    score='" . $score . "',
                    comment='" . $comment . "'
                WHERE assessment_score_id='" . (int) $assessment_score_id . "'
                AND student_id='" . Student( 'STUDENT_ID' ) . "'" );
        }
    }

    // Refresh page
    RedirectURL( 'modfunc' );
}


// Get all assessment types
$types_ret = DBGet( "SELECT assessment_type_id, category, title
    FROM student_assessments_types
    WHERE syear='" . UserSyear() . "'
    AND school_id='" . UserSchool() . "'
    ORDER BY sort_order, category, title" );

if ( empty( $types_ret ) )
{
    $note = _( 'No assessment types have been created for this school year.' );
    if ( $can_edit )
    {
        // FIX: Replaced MakeLink() with raw <a> tag
         $note .= ' ' . '<a href="' . URLEscape( 'Modules.php?modname=Student_Assessments/AssessmentTypes.php' ) . '">' .
            _( 'Click here to create them.' ) .
         '</a>';
    }
    ErrorMessage( array( $note ), 'note' );
}
else
{
    // Get all scores for this student
    $scores_ret = DBGet( "SELECT assessment_score_id, assessment_type_id, assessment_date, score, comment
        FROM student_assessments_scores
        WHERE student_id='" . Student( 'STUDENT_ID' ) . "'
        AND syear='" . UserSyear() . "'" );

    // Organize scores by assessment_type_id for easy lookup
    $scores = array();
    if ( ! empty( $scores_ret ) )
    {
        foreach ( $scores_ret as $score )
        {
            $scores[$score['assessment_type_id']] = $score;
        }
    }

    // Display form
    echo '<form action="' . URLEscape( 'Modules.php?modname=' . $_REQUEST['modname'] . '&student_id=' . Student( 'STUDENT_ID' ) ) . '" method="POST">';

    $current_category = '';

    foreach ( $types_ret as $type )
    {
        $assessment_type_id = $type['assessment_type_id'];

        // Get score data if it exists
        $score_data = $scores[$assessment_type_id] ?? array();
        $assessment_score_id = $score_data['assessment_score_id'] ?? '';
        $assessment_date = $score_data['assessment_date'] ?? '';
        $score_value = $score_data['score'] ?? '';
        $comment_value = $score_data['comment'] ?? '';

        // Display category header
        if ( $type['category'] !== $current_category )
        {
            if ( $current_category !== '' )
            {
                PopTable( 'footer' );
                echo '</table>'; // Close the table
            }
            $current_category = $type['category'];
            PopTable( 'header', $current_category );
            ?>
            <table class="width-100p">
                <tr class="st-alternate">
                    <th class="width-25p"><?php echo _( 'Assessment' ); ?></th>
                    <th class="width-15p"><?php echo _( 'Date' ); ?></th>
                    <th class="width-20p"><?php echo _( 'Score' ); ?></th>
                    <th class="width-40p"><?php echo _( 'Comment' ); ?></th>
                </tr>
            <?php
        }

        // Display row
        echo '<tr>';
        echo '<td>' . $type['title'] . '</td>';

        if ( $can_edit )
        {
            // Editable fields for Admin
            echo '<input type="hidden" name="values[' . $assessment_type_id . '][assessment_score_id]" value="' . $assessment_score_id . '">';
            
            echo '<td>' . DateInput( $assessment_date, 'values[' . $assessment_type_id . '][assessment_date]' ) . '</td>';
            
            echo '<td><input type="text" name="values[' . $assessment_type_id . '][score]" value="' . AttrEscape( $score_value ) . '" class="width-100p"></td>';
            
            echo '<td><textarea name="values[' . $assessment_type_id . '][comment]" class="width-100p">' .
                $comment_value .
            '</textarea></td>';
        }
        else
        {
            // Read-only fields for others
            echo '<td>' . ( $assessment_date ? ProperDate( $assessment_date ) : '' ) . '</td>';
            echo '<td>' . $score_value . '</td>';
            echo '<td>' . nl2br( $comment_value ) . '</td>';
        }

        echo '</tr>';
    }

    if ( $current_category !== '' )
    {
        PopTable( 'footer' );
        echo '</table>'; // Close the final table
    }

    if ( $can_edit )
    {
        echo '<br /><div class="center">' . SubmitButton( _( 'Save' ) ) . '</div>';
    }

    echo '</form>';
}
