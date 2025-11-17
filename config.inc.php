<?php
/**
 * RosarioSIS Configuration
 *
 * This configuration file is designed to work with Koyeb.
 * It automatically parses the DATABASE_URL environment variable.
 */

// --- Database Configuration ---

// KOYEB FIX: The DATABASE_URL is not being set.
// We are hardcoding the credentials from your fallback.
// To fix this long-term, link your database service in Koyeb's settings.

$dbhost = '34.60.71.195';
$dbport = '5432';
$dbname = 'postgres';
$dbuser = 'postgres';
$dbpassword = 'IHateNiger12!';


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
