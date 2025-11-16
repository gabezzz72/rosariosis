<?php
/**
 * Assessment Types (Admin-only)
 *
 * @package RosarioSIS
 * @subpackage modules
 */

global $RosarioPath;
require_once $RosarioPath . 'ProgramFunctions/List.php';
require_once $RosarioPath . 'ProgramFunctions/Update.php';

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
    $types_ret = DBGet( "SELECT assessment_type_id, category, title, sort_order
        FROM student_assessments_types
        WHERE syear='" . UserSyear() . "'
        AND school_id='" . UserSchool() . "'
        ORDER BY sort_order, category, title" );

    $columns = array(
        'category' => _( 'Category' ),
        'title' => _( 'Assessment Title' ),
        'sort_order' => _( 'Sort Order' ),
    );

    $link['add']['html'] = array(
        'title' => _( 'Add Assessment Type' ),
        'link' => 'Modules.php?modname=' . $_REQUEST['modname'] . '&modfunc=add&id=new',
    );
    $link['remove']['html'] = array(
        'title' => _( 'Delete Assessment Type' ),
        'link' => 'Modules.php?modname=' . $_REQUEST['modname'] . '&modfunc=delete',
        'extra' => 'confirm',
    );
    $link['edit']['html'] = array(
        'title' => _( 'Edit Assessment Type' ),
        'link' => 'Modules.php?modname=' . $_REQUEST['modname'] . '&modfunc=add', // 'add' is used for edit too
        'val' => 'assessment_type_id',
    );

    ListOutput(
        $types_ret,
        $columns,
        'Assessment Type',
        'Assessment Types',
        $link,
        false,
        array( 'search' => false )
    );
}
