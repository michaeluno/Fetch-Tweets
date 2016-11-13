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
 */
class FetchTweets_DatabaseTableInstall extends FetchTweets_PluginUtility {


    /**
     * Installs or uninstalls plugin database tables.
     */
    public function __construct( $aDatabaseTables, $bInstallOrUninstall, $sDatabaseClassPrefix='FetchTweets_DatabaseTable_' ) {

        $_sMethodName = $bInstallOrUninstall
            ? 'install'
            : 'uninstall';
            
        foreach( $aDatabaseTables as $_sKey => $_aTable ) {
            
            $_sTableName = $this->getElement( $_aTable, 'name', '' );
            if ( ! $_sTableName ) {
                continue;
            }
            $_sVersion   = $this->getElement( $_aTable, 'version', '' );
            $_sClassName = $sDatabaseClassPrefix . $_sKey;
            
            $_oTable     = new $_sClassName( $_sTableName, $_sVersion );
            $_oTable->$_sMethodName();
            
        }
 
    }
   
}
