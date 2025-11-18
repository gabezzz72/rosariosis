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

// Set the student
if ( $_REQUEST['student_id'] === 'new' )
{
    // Cannot add scores to a new student
    ErrorMessage( array( _( 'You must select a student first.' ) ), 'fatal' );
}
else
{
    SetStudent( $_REQUEST['student_id'] );
}

DrawHeader( _( 'Assessments' ) );

// Check permissions
// Admin can edit, others are read-only
$can_edit = ( User( 'PROFILE' ) === 'admin' && AllowEdit() );

// Handle form submission
if ( ! empty( $_POST['values'] )
    && $can_edit )
{
    foreach ( (array) $_POST['values'] as $assessment_type_id => $values )
    {
        $assessment_score_id = $values['assessment_score_id'];

        if ( empty( $values['score'] ) && empty( $values['assessment_date'] ) && empty( $values['comment'] ) )
        {
            // If all fields are empty, delete the record if it exists
            if ( ! empty( $assessment_score_id ) )
            {
                DBQuery( "DELETE FROM student_assessments_scores
                    WHERE assessment_score_id='" . (int) $assessment_score_id . "'
                    AND student_id='" . Student( 'STUDENT_ID' ) . "'" );
            }
            // If it doesn't exist, do nothing
        }
        else
        {
            // Format date for SQL
            $assessment_date = $values['assessment_date'] ?
                "'" . PrepareDate( $values['assessment_date'] ) . "'" : 'NULL';

            if ( ! empty( $assessment_score_id ) )
            {
                // Update existing score
                DBQuery( "UPDATE student_assessments_scores
                    SET assessment_date=" . $assessment_date . ",
                        score='" . $values['score'] . "',
                        comment='" . $values['comment'] . "',
                        syear='" . UserSyear() . "'
                    WHERE assessment_score_id='" . (int) $assessment_score_id . "'
                    AND student_id='" . Student( 'STUDENT_ID' ) . "'" );
            }
            else
            {
                // Add new score
                DBQuery( "INSERT INTO student_assessments_scores
                    (student_id, assessment_type_id, syear, assessment_date, score, comment)
                    VALUES(
                        '" . Student( 'STUDENT_ID' ) . "',
                        '" . (int) $assessment_type_id . "',
                        '" . UserSyear() . "',
                        " . $assessment_date . ",
                        '" . $values['score'] . "',
                        '" . $values['comment'] . "'
                    )" );
            }
        }
    }

    // Success message
    $note[] = _( 'Data saved' );
    Note( $note );
}

// Get all assessment types for the current school year
$types_ret = DBGet( "SELECT assessment_type_id, category, title
    FROM student_assessments_types
    WHERE syear='" . UserSyear() . "'
    AND school_id='" . UserSchool() . "'
    ORDER BY sort_order, category, title" );

if ( ! $types_ret )
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

    if ( ! $can_edit )
    {
        // If read-only and no types, nothing to show.
        exit;
    }
}

// Get all scores for this student
$scores_ret = DBGet( "SELECT assessment_score_id, assessment_type_id,
    assessment_date, score, comment
    FROM student_assessments_scores
    WHERE student_id='" . Student( 'STUDENT_ID' ) . "'
    AND syear='" . UserSyear() . "'", array(), array( 'assessment_type_id' ) );

// Start form
echo '<form action="' . URLEscape( 'Modules.php?modname=' . $_REQUEST['modname'] . '&student_id=' . Student( 'STUDENT_ID' ) ) . '" method="POST">';

DrawHeader( '', SubmitButton( _( 'Save' ), '', $can_edit ? '' : 'disabled' ) );
echo '<br />';

// Display table
$last_category = '';

foreach ( (array) $types_ret as $type )
{
    if ( $type['category'] !== $last_category )
    {
        if ( $last_category !== '' )
        {
            // Close previous table
            PopTable( 'footer' );
            echo '<br />';
        }
        PopTable( 'header', $type['category'] );
        ?>
        <table class="width-100p fixed-col">
            <tr class="st-alternate">
                <th class="width-25p"><?php echo _( 'Assessment' ); ?></th>
                <th class="width-15p"><?php echo _( 'Date' ); ?></th>
                <th class="width-20p"><?php echo _( 'Score' ); ?></th>
                <th><?php echo _( 'Comment' ); ?></th>
            </tr>
        <?php
        $last_category = $type['category'];
    }

    $type_id = $type['assessment_type_id'];
    $score_data = isset( $scores_ret[$type_id] ) ? $scores_ret[$type_id][1] : array();

    // Hidden field for score ID
    echo '<input type="hidden" name="values[' . $type_id . '][assessment_score_id]" value="' .
        AttrEscape( $score_data['assessment_score_id'] ) . '">';
    ?>
    <tr>
        <td><?php echo $type['title']; ?></td>
        <td>
            <?php
            if ( $can_edit )
            {
                echo DateInput(
                    $score_data['assessment_date'],
                    'values[' . $type_id . '][assessment_date]',
                    '',
                    false
                );
            }
            else
            {
                echo ProperDate( $score_data['assessment_date'] );
            }
            ?>
        </td>
        <td>
            <?php
            if ( $can_edit )
            {
                echo '<input type="text" name="values[' . $type_id . '][score]" value="' .
                    AttrEscape( $score_data['score'] ) . '" class="width-100p">';
            }
            else
            {
                echo $score_data['score'];
            }
            ?>
        </td>
        <td>
            <?php
            if ( $can_edit )
            {
                echo '<textarea name="values[' . $type_id . '][comment]" class="width-100p">' .
                    $score_data['comment'] . '</textarea>';
            }
            else
            {
                // Use nl2br to respect line breaks in read-only view
                echo nl2br( $score_data['comment'] );
            }
            ?>
        </td>
    </tr>
    <?php
}

if ( $last_category !== '' )
{
    // Close final table
    PopTable( 'footer' );
}

echo '<br /><div class="center">' . SubmitButton( _( 'Save' ), '', $can_edit ? '' : 'disabled' ) . '</div>';
echo '</form>';
