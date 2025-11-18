<?php
/**
 * AssessmentTypes (Admin-only)
 *
 * @package RosarioSIS
 * @subpackage modules
 */

// --- DEBUGGING REMOVED ---

// Load the required functions files that EXIST on your server
require_once __DIR__ . '/../../functions/DBGet.fnc.php'; // For DBGet()
require_once __DIR__ . '/../../functions/Buttons.php'; // For SubmitButton()
require_once __DIR__ . '/../../functions/Prompts.php'; // For DeletePrompt()

DrawHeader( _( 'Assessment Types' ) );

// Permissions check
if ( User( 'PROFILE' ) !== 'admin' )
{
    ErrorMessage( array( _( 'You do not have access to this program.' ) ), 'fatal' );
}

// Handle Add/Edit/Delete
if ( $_REQUEST['modfunc'] === 'delete'
    && AllowEdit() )
{
    if ( DeletePrompt( _( 'Assessment Type' ) ) )
    {
        DBQuery( "DELETE FROM student_assessments_types
            WHERE assessment_type_id='" . (int) $_REQUEST['id'] . "'
            AND syear='" . UserSyear() . "'
            AND school_id='" . UserSchool() . "'" );

        // Unset modfunc & ID
        RedirectURL( 'modfunc', 'id' );
    }
}
elseif ( $_REQUEST['modfunc'] === 'add' // 'add' is used for both add and edit
    && AllowEdit() )
{
    // Add/Edit form
    if ( empty( $_REQUEST['id'] ) || $_REQUEST['id'] === 'new' )
    {
        $id = 'new';
        $title = _( 'Add Assessment Type' );
        $inputs = array( 'category' => '', 'title' => '', 'sort_order' => '' );
    }
    else
    {
        $id = (int) $_REQUEST['id'];
        $title = _( 'Edit Assessment Type' );

        $type_ret = DBGet( "SELECT category, title, sort_order
            FROM student_assessments_types
            WHERE assessment_type_id='" . $id . "'
            AND syear='" . UserSyear() . "'
            AND school_id='" . UserSchool() . "'" );

        $inputs = $type_ret[1];
    }

    // Handle form submission
    if ( ! empty( $_POST['values'] )
        && AllowEdit() )
    {
        if ( $id === 'new' )
        {
            // Add new
            DBQuery( "INSERT INTO student_assessments_types
                (syear, school_id, category, title, sort_order)
                VALUES(
                    '" . UserSyear() . "',
                    '" . UserSchool() . "',
                    '" . $_POST['values']['category'] . "',
                    '" . $_POST['values']['title'] . "',
                    '" . (int) $_POST['values']['sort_order'] . "'
                )" );
        }
        else
        {
            // Update existing
            DBQuery( "UPDATE student_assessments_types
                SET category='" . $_POST['values']['category'] . "',
                    title='" . $_POST['values']['title'] . "',
                    sort_order='" . (int) $_POST['values']['sort_order'] . "'
                WHERE assessment_type_id='" . $id . "'" );
        }

        // Redirect to list
        RedirectURL( 'modfunc', 'id' );
    }

    // Draw the form
    echo '<form action="' . URLEscape( 'Modules.php?modname=' . $_REQUEST['modname'] . '&modfunc=add&id=' . $id ) . '" method="POST">';
    DrawHeader( $title );
    PopTable( 'header', $title );
    ?>
    <table class="width-100p">
        <tr><td><?php echo _( 'Category' ); ?>: <input type="text" name="values[category]" value="<?php echo AttrEscape( $inputs['category'] ); ?>" required> (e.g. National Assessments)</td></tr>
        <tr><td><?php echo _( 'Assessment Title' ); ?>: <input type="text" name="values[title]" value="<?php echo AttrEscape( $inputs['title'] ); ?>" required> (e.g. PSAT)</td></tr>
        <tr><td><?php echo _( 'Sort Order' ); ?>: <input type="number" name="values[sort_order]" value="<?php echo AttrEscape( $inputs['sort_order'] ); ?>"></td></tr>
    </table>
    <?php
    PopTable( 'footer' );
    echo '<br /><div class="center">' . SubmitButton( _( 'Save' ) ) . '</div>';
    echo '</form>';
}
else
{
    // List view
    // Manual List Generation (since ListOutput() is not available)

    $types_ret = DBGet( "SELECT assessment_type_id, category, title, sort_order
        FROM student_assessments_types
        WHERE syear='" . UserSyear() . "'
        AND school_id='" . UserSchool() . "'
        ORDER BY sort_order, category, title" );

    // Add link
    // FIX: Replaced MakeLink() with raw <a> tag
    echo '<div class="center">' .
        '<a href="' . URLEscape( 'Modules.php?modname=' . $_REQUEST['modname'] . '&modfunc=add&id=new' ) . '">' .
            _( 'Add Assessment Type' ) .
        '</a>' .
    '</div>';

    PopTable( 'header', _( 'Assessment Types' ) );
    ?>
    <table class="width-100p">
        <thead>
            <tr class="st-alternate">
                <th><?php echo _( 'Category' ); ?></th>
                <th><?php echo _( 'Assessment Title' ); ?></th>
                <th><?php echo _( 'Sort Order' ); ?></th>
                <th><?php echo _( 'Actions' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ( ! $types_ret )
            {
                echo '<tr><td colspan="4" class="center">' . _( 'No assessment types created yet.' ) . '</td></tr>';
            }
            else
            {
                foreach ( (array) $types_ret as $type )
                {
                    echo '<tr>';
                    echo '<td>' . $type['category'] . '</td>';
                    echo '<td>' . $type['title'] . '</td>';
                    echo '<td>' . $type['sort_order'] . '</td>';
                    
                    // Actions
                    echo '<td>';
                    
                    // Edit Link
                    // FIX: Replaced MakeLink() with raw <a> tag
                    $edit_url = URLEscape( 'Modules.php?modname=' . $_REQUEST['modname'] . '&modfunc=add&id=' . $type['assessment_type_id'] );
                    echo '<a href="' . $edit_url . '">' . _( 'Edit' ) . '</a>';
                    
                    echo ' | ';

                    // Delete Link
                    // FIX: Replaced MakeLink() with raw <a> tag and manual onclick for delete prompt
                    $delete_url = URLEscape( 'Modules.php?modname=' . $_REQUEST['modname'] . '&modfunc=delete&id=' . $type['assessment_type_id'] );
                    echo '<a href="' . $delete_url . '" onclick="return DeletePrompt();">' . _( 'Delete' ) . '</a>';

                    echo '</td>';
                    echo '</tr>';
                }
            }
            ?>
        </tbody>
    </table>
    <?php
    PopTable( 'footer' );
}
