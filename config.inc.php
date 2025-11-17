<?php
/**
 * RosarioSIS config file
 *
 *
 * @package RosarioSIS
 */

// ** PostgreSQL database connection ** //
// Full path to the pg_dump & psql PostgreSQL client utilities,
// useful for backup & restore.
// Examples: 'C:/Program Files/PostgreSQL/10/bin/' or '/usr/bin/'
// Make sure the path ends with a slash.
// Leave empty to disable backup & restore.
$rosario_config['PG_PATH'] = '/usr/bin/';

/**
 * Automatically read database credentials from Koyeb Environment Variables.
 *
 * If 'DATABASE_URL' is provided by Koyeb, it will be parsed.
 * Otherwise, it will look for individual ROSARIO_DB_* variables.
 * Finally, it will fall back to the default 'localhost' values.
 */

// Defaults
$db_host = 'localhost';
$db_port = '5432';
$db_name = 'rosariosis';
$db_user = 'rosariosis';
$db_pass = 'rosariosis';

// Check for Koyeb's DATABASE_URL
$db_url = getenv('DATABASE_URL');

if ( $db_url )
{
    // Parse the URL
    $db_parts = parse_url( $db_url );

    $db_host = $db_parts['host'] ?? $db_host;
    $db_port = $db_parts['port'] ?? $db_port;
    $db_name = ltrim( $db_parts['path'], '/' ) ?? $db_name;
    $db_user = $db_parts['user'] ?? $db_user;
    $db_pass = $db_parts['pass'] ?? $db_pass;
}
else
{
    // Fallback to individual variables if DATABASE_URL is not set
    $db_host = getenv('ROSARIO_DB_HOST') ?: $db_host;
    $db_port = getenv('ROSARIO_DB_PORT') ?: $db_port;
    $db_name = getenv('ROSARIO_DB_NAME') ?: $db_name;
    $db_user = getenv('ROSARIO_DB_USER') ?: $db_user;
    $db_pass = getenv('ROSARIO_DB_PASSWORD') ?: $db_pass;
}

$rosario_config['DB_HOST'] = $db_host;
$rosario_config['DB_PORT'] = $db_port;
$rosario_config['DB_NAME'] = $db_name;
$rosario_config['DB_USER'] = $db_user;
$rosario_config['DB_PASS'] = $db_pass;


// ** School SCEP (certificate enrollment) ** //
// $rosario_config['SCEP_URL'] = 'https://scep.myschool.org/scep';
// $rosario_config['SCEP_CHALLENGE'] = 'challenge';

// ** Other schools ** //
// If you want to run multiple schools with one installation,
// copy this file to config_SCHOONAME.inc.php
// (where SCHOONAME is the value of the 'school' parameter in the URL)
// and edit the database connection info.

/**
 * Automatically set $RosarioPath.
 * DO NOT EDIT THIS.
 */
$RosarioPath = dirname( __FILE__ ) . '/';

// Set warning_level to 0 for production, 1 for development.
$warning_level = 0;
