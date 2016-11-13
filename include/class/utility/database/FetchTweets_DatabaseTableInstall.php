<?php
/**
 * Fetch Tweets
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2016 Michael Uno
 * 
 */

/**
 * Creates custom database tables for the plugin.
 * 
 * @since       2.5.0
 * @deprecated
 */
class FetchTweets_DatabaseTableInstall extends FetchTweets_PluginUtility {


    /**
     * Installs or uninstalls plugin database tables.
     */
    public function __construct( $aDatabaseTables, $bInstallOrUninstall ) {

        $_sMethodName = $bInstallOrUninstall
            ? 'install'
            : 'uninstall';
            
        foreach( $aDatabaseTables as $_aTable ) {
            
            $_sTableName = $this->getElement( $_aTable, 'name', '' );
            if ( ! $_sTableName ) {
                continue;
            }
            $_sVersion   = $this->getElement( $_aTable, 'version', '' );
            
            $_sClassName = $this->getElement( $_aTable, 'class_name', '' )
            if ( class_exists( $_sClassName ) ) {                
                $_oTable     = new $_sClassName;
                $_oTable->$_sMethodName();
            }
            
        }
 
    }
   
}
