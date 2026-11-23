<?php
/**
 * Configuration WordPress — versionnée, sans aucun secret.
 *
 * Les secrets (base de données, clés et salts) viennent des variables
 * d'environnement ou du fichier .env à la racine du projet (non versionné,
 * voir .env.example). Sous DDEV, la base de données et les URL sont
 * fournies par web/wp-config-ddev.php, généré par DDEV.
 *
 * Arborescence :
 *   web/            docroot
 *   web/wp/         cœur WordPress (installé par Composer, non versionné)
 *   web/wp-content/ thèmes, plugins, uploads
 *
 * Ce fichier est chargé par web/wp-config.php : il reste hors du docroot.
 * Il ne charge pas wp-settings.php lui-même : c'est web/wp-config.php qui
 * le fait, car WP-CLI exige de trouver cette ligne dans le wp-config.php
 * qu'il lit.
 *
 * @package WPStarter
 */

$root_dir    = __DIR__;
$webroot_dir = $root_dir . '/web';

require_once $root_dir . '/vendor/autoload.php';

// Charge .env s'il existe ; les variables déjà définies dans l'environnement priment.
if ( file_exists( $root_dir . '/.env' ) ) {
	Dotenv\Dotenv::createImmutable( $root_dir )->safeLoad();
}

/**
 * Lit une variable d'environnement (getenv, $_ENV ou $_SERVER).
 *
 * @param string $key     Nom de la variable.
 * @param mixed  $default Valeur par défaut.
 * @return mixed
 */
function wp_starter_env( $key, $default = null ) {
	$value = getenv( $key );
	if ( false === $value ) {
		$value = $_ENV[ $key ] ?? $_SERVER[ $key ] ?? null;
	}
	return ( null === $value || '' === $value ) ? $default : $value;
}

/* Environnement */
$wp_env = wp_starter_env( 'WP_ENV', 'production' );
define( 'WP_ENVIRONMENT_TYPE', $wp_env );

/* DDEV : base de données, WP_HOME, WP_SITEURL, WP_DEBUG */
$ddev_settings = $webroot_dir . '/wp-config-ddev.php';
if ( 'true' === getenv( 'IS_DDEV_PROJECT' ) && is_readable( $ddev_settings ) ) {
	require_once $ddev_settings;
}

/* Base de données (hors DDEV) */
defined( 'DB_NAME' ) || define( 'DB_NAME', wp_starter_env( 'DB_NAME' ) );
defined( 'DB_USER' ) || define( 'DB_USER', wp_starter_env( 'DB_USER' ) );
defined( 'DB_PASSWORD' ) || define( 'DB_PASSWORD', wp_starter_env( 'DB_PASSWORD', '' ) );
defined( 'DB_HOST' ) || define( 'DB_HOST', wp_starter_env( 'DB_HOST', 'localhost' ) );
define( 'DB_CHARSET', wp_starter_env( 'DB_CHARSET', 'utf8mb4' ) );
define( 'DB_COLLATE', wp_starter_env( 'DB_COLLATE', '' ) );
if ( empty( $table_prefix ) ) {
	$table_prefix = wp_starter_env( 'DB_PREFIX', 'wp_' ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
}

/* URL : le site est servi à la racine, WordPress depuis /wp */
defined( 'WP_HOME' ) || define( 'WP_HOME', rtrim( (string) wp_starter_env( 'WP_HOME', '' ), '/' ) );
defined( 'WP_SITEURL' ) || define( 'WP_SITEURL', WP_HOME . '/wp' );

/* wp-content hors du cœur */
define( 'WP_CONTENT_DIR', $webroot_dir . '/wp-content' );
define( 'WP_CONTENT_URL', WP_HOME . '/wp-content' );
define( 'WP_DEFAULT_THEME', 'starter-theme' );

/* Clés et salts */
foreach ( array( 'AUTH_KEY', 'SECURE_AUTH_KEY', 'LOGGED_IN_KEY', 'NONCE_KEY', 'AUTH_SALT', 'SECURE_AUTH_SALT', 'LOGGED_IN_SALT', 'NONCE_SALT' ) as $wp_starter_key ) {
	define( $wp_starter_key, wp_starter_env( $wp_starter_key, '' ) );
}
unset( $wp_starter_key );

/* Debug */
defined( 'WP_DEBUG' ) || define( 'WP_DEBUG', 'development' === $wp_env );
define( 'WP_DEBUG_DISPLAY', 'development' === $wp_env );
define( 'SCRIPT_DEBUG', 'development' === $wp_env );

/*
 * En local, WordPress relit les fichiers du thème (patterns, theme.json)
 * à chaque requête au lieu de les garder en cache. En production, ce
 * cache n'est invalidé que par un changement de version du thème
 * (en-tête Version de style.css).
 */
defined( 'WP_DEVELOPMENT_MODE' ) || define( 'WP_DEVELOPMENT_MODE', 'development' === $wp_env ? 'theme' : '' );

/* ABSPATH et le chargement de wp-settings.php sont dans web/wp-config.php. */
