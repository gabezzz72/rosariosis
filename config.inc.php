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

// --- DEBUGGING START ---
echo '<pre>';
echo '--- RosarioSIS Config Debug ---<br /><br />';

echo '1. DATABASE_URL value:<br />';
var_dump( $database_url );
echo '<br />';

if ( $database_url ) {
    $db_parts = parse_url( $database_url );

    echo '2. Parsed URL parts:<br />';
    var_dump( $db_parts );
    echo '<br />';

    $dbhost = $db_parts['host'] ?? 'PARSE_ERROR_HOST';
    $dbport = $db_parts['port'] ?? 'PARSE_ERROR_PORT';
    $dbname = ltrim( $db_parts['path'] ?? '', '/' );
    $dbuser = $db_parts['user'] ?? 'PARSE_ERROR_USER';
    $dbpassword = $db_parts['pass'] ?? 'PARSE_ERROR_PASSWORD';

    echo '3. Values being set:<br />';
    echo "Host: " . htmlspecialchars( $dbhost ) . "<br />";
    echo "Port: " . htmlspecialchars( $dbport ) . "<br />";
    echo "DB Name: " . htmlspecialchars( $dbname ) . "<br />";
    echo "User: " . htmlspecialchars( $dbuser ) . "<br />";
    
    // Check if the password is empty or not parsed
    if ( $dbpassword === 'PARSE_ERROR_PASSWORD' ) {
        echo "Password: NOT FOUND (parse_url() did not find 'pass')<br />";
    } elseif ( empty( $dbpassword ) ) {
        echo "Password: EMPTY (parsed, but the value is an empty string)<br />";
    } else {
        echo "Password: FOUND (length: " . strlen( $dbpassword ) . ")<br />";
    }

} else {
    echo '2. DATABASE_URL is not set or empty. Using fallback.<br />';

    $dbhost = '34.60.71.195';
    $dbport = '5432';
    $dbname = 'postgres';
    $dbuser = 'postgres';
    $dbpassword = 'IHateNiger12!';
}

echo '<br />--- End Debug ---';
echo '</pre>';
exit; // Stop the script from running further
// --- DEBUGGING END ---


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
