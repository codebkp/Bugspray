<?php

/* COPYRIGHT BUGSPRAY PROJECT 2014 */

if( !defined( 'CDIR' ) )
    exit( 'There is no <i>current working directory</i> defined' );

if( !defined( 'IN_ADMIN' ) )
    define( 'IN_ADMIN', false );

if( !defined( 'NO_TPL' ) )
    define( 'NO_TPL', false );

if( !defined( 'IN_INSTALL' ) )
    define( 'IN_INSTALL', false );

if( !defined( 'NO_PLUGINS' ) )
    define( 'NO_PLUGINS', false );

if( !defined( 'IN_INSTALL_COMPLETION' ) )
    define( 'IN_INSTALL_COMPLETION', false );

if( !file_exists( CDIR . '/core/config.inc.php' ) && !IN_INSTALL && !IN_INSTALL_COMPLETION )
    exit( 'Not installed. Go to <a href="' . ( IN_ADMIN ? '../' : '' ) . 'install">/install</a>');

/**
 * Start the SESSION cookie, allowing to save login data and such
 */
session_start();

/**
 * Define commonly used variables
 */
define( 'LOGGED_IN', ( IN_ADMIN === true
                        ? !empty( $_SESSION[ 'admin_user_id' ] )
                        : !empty( $_SESSION[ 'user_id'       ] ) ) );

define( 'USERID', LOGGED_IN
                        ? ( IN_ADMIN === true
                             ? $_SESSION[ 'admin_user_id' ]
                             : $_SESSION[ 'user_id' ] )
                        : -1 );

/**
 * Load template classes
 */
if( !NO_TPL ) require_once CDIR . '/core/pagedata.class.php';
if( !NO_TPL ) require_once CDIR . '/core/rain.tpl.class.php';

/**
 * Load configuration and functions script if not in install
 */
if( !IN_INSTALL )
{
    require_once CDIR . '/core/config.inc.php';
    require_once CDIR . '/core/functions.inc.php';

    /**
     * Load required classes
     */
    require_once CDIR . '/core/miqrodb.class.php';
    require_once CDIR . '/core/settings.class.php';
    require_once CDIR . '/core/pluginmanager.class.php';
    require_once CDIR . '/core/db.loader.php';


    /**
     * Set up the database connection and interface
     */
    DB::loaderInit( new MiqroDB ( new MySQLi( $db_host, $db_user, $db_password, $db_database ) ) );
    MiqroDB::$debug = (bool)setting( 'debug_mode' );
    
    /**
     * Set up system settings.
     */
    Settings::init();
    
    /**
     * Check for deleted accounts/wrong sessions
     */
    if( LOGGED_IN && !IN_ADMIN && !DB::table( prefix( 'users' ) )->select( 'id', [ 'where' => 'id = \'' . DB::escape( $_SESSION[ 'user_id' ] ) . '\'' ] ) )
    {
        unset( $_SESSION[ 'user_id' ] );
        header( 'Location: ' . Settings::get( 'base_url' ) );
    }
    
    if( LOGGED_IN &&  IN_ADMIN && !DB::table( prefix( 'users' ) )->select( 'id', [ 'where' => 'id = \'' . DB::escape( $_SESSION[ 'admin_user_id' ] ) . '\'' ] ) )
    {
        unset( $_SESSION[ 'admin_user_id' ] );
    }
    
    /**
     * Load language data
     */
    $language = setting( 'language', 'english' );

    require_once CDIR . '/language/' . ( file_exists( CDIR . '/language/' . $language . '.lang.php' ) ? $language : 'english' )  . '.lang.php';

    /**
     * Set up TPL object if required
     */
    if( !NO_TPL )
    {
        RainTPL::configure( 'tpl_ext', 'tpl' );
        RainTPL::configure( 'tpl_dir', ( IN_ADMIN === false ? 'theme/' . setting( 'theme' ) : 'tpl' ) . '/' );
        RainTPL::configure( 'path_replace', false );
        RainTPL::configure( 'base_url', setting( 'base_url' ) . ( IN_ADMIN === true ? '/admin' : '' ) . '/' );

        $tpl = new RainTPL();

        $tpl->assign( 'settings', Settings::all() );
        if( !IN_ADMIN ) $tpl->assign( 'projects', DB::table( prefix( 'projects' ) )->select( '*', [ 'where' => 'enabled = \'1\'' ] )->getAssoc( 'id' ) );
        if(  IN_ADMIN ) $tpl->assign( 'projects', DB::table( prefix( 'projects' ) )->select( '*' )->getAssoc( 'id' ) );
        $tpl->assign( 'server', [
            'available_themes' => glob( CDIR . '/theme/*', GLOB_ONLYDIR ),
            'available_languages' => glob( CDIR . '/language/*.lang.php' )
        ] );
        $tpl->assign( 'loggedIn', LOGGED_IN );
    }

    /**
     * Load plugins
     */
    if( !NO_PLUGINS )
    {
        PluginManager::updateDisables();
        foreach( glob( CDIR . '/plugins/*.plugin.php' ) as $file )
            try { include $file; } catch ( Exception $e ) { echo 'Can\'t load plugin' . $file; }
    }
    
    /**
     * Assign language variable to TPL object
     * (becasue plugins may alter the state of the $l variable ) 
     */
    if( !NO_TPL )
    {
        $l = str_replace( '%baseurl%', setting( 'base_url' ), $l );
        $l = str_replace( '%sitename%', setting( 'site_name' ), $l );
        $l = str_replace( '%admin_email%', setting( 'admin_email' ), $l );
        $tpl->assign( 'lang', $l );
    }
}