<?php
/**
 * RosarioSIS Configuration
 *
 * This configuration file is designed to work with Koyeb.
 * It automatically parses the DATABASE_URL environment variable.
 */

// --- Database Configuration ---

// Automatically parse Koyeb's DATABASE_URL
$database_url = getenv( 'DATABASE_URL' );

if ( $database_url ) {
    $db_parts = parse_url( $database_url );

    $dbhost = $db_parts['host'] ?? '';
    $dbport = $db_parts['port'] ?? '5432';
    $dbname = ltrim( $db_parts['path'] ?? '', '/' );
    $dbuser = $db_parts['user'] ?? '';
    $dbpassword = $db_parts['pass'] ?? '';
} else {
    // Fallback for local development (if you want)
    // WARNING: Do not use these in production on Koyeb
    $dbhost = '34.60.71.195';
    $dbport = '5432';
    $dbname = 'postgres';
    $dbuser = 'postgres';
    $dbpassword = 'IHateNiger12!';
}

$dbtype = 'pgsql'; // Do not change


// --- General Configuration ---

/**
 * Unique identifier for your RosarioSIS instance.
 * @example 'MySchool'
 * @example 'District'
 */
$RosarioPath = '/var/www/html/';

/**
 * Unique identifier for your RosarioSIS instance.
 * @example 'MySchool'
 * @example 'District'
 */
$DatabaseType = 'PostgreSQL';

/**
 * Should RosarioSIS automatically create the database?
 * Set to true for the first time.
 * Set to false after installation.
 */
$CreateDatabase = false;

/**
 * Database Server.
 * @example 'localhost'
 */
$DatabaseServer = $dbhost;

/**
 * Database Port.
 * @example '5432'
 */
$DatabasePort = $dbport;

/**
 * Database Name.
 * @example 'rosariosis'
 */
$DatabaseName = $dbname;

/**
 * Database User.
 * @example 'rosariosis_user'
 */
$DatabaseUser = $dbuser;

/**
 * Database Password.
 * @example 'rosariosis_password'
 */
$DatabasePassword = $dbpassword;


// --- Other Settings ---

/**
 * Default School.
 * Set the default school by its Title.
 * @example 'My School'
 */
$DefaultSchool = '';

/**
 * Force HTTPS connection
 * Set to true if you're using an SSL certificate (recommended)
 * Koyeb handles this, so 'true' is safe.
 */
$force_https = true;

/**
 * Session Name
 * Change this to a unique random string.
 */
$SessionName = 'RosarioSIS';

/**
 * Error logging
 * Set to 'Off' for production
 */
$error_logging = 'Off'; // 'On' for debugging, 'Off' for production

/**
 * PHP Locale
 */
$locale = 'en_US.utf8';
setlocale( LC_ALL, $locale );

// --- End of Configuration ---
