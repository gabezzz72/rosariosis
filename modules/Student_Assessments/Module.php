<?php
/**
 * Student Assessments module
 * Module.php - handles install/uninstall
 *
 * @package RosarioSIS
 * @subpackage modules
 */

/**
 * Student Assessments
 *
 * @param string $action 'install', 'uninstall', 'enable', 'disable'
 * @return boolean true on success
 */
function Student_Assessments_Module( $action )
{
    if ( $action === 'install' )
    {
        $sql = file_get_contents( __DIR__ . '/install.sql' );

        if ( $sql )
        {
            // Use db_query to execute the SQL
            db_query( $sql );
        }
    }
    elseif ( $action === 'uninstall' )
    {
        $sql = file_get_contents( __DIR__ . '/uninstall.sql' );

        if ( $sql )
        {
            // Use db_query to execute the SQL
            db_query( $sql );
        }
    }

    return true;
}
