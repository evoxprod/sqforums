//<?php


/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * Install Code
 */
class ips_plugins_setup_install
{
	/**
	 * Install Extension
	 *
	 * @return	array	If returns TRUE, upgrader will proceed to next step. If it returns any other value, it will set this as the value of the 'extra' GET parameter and rerun this step (useful for loops)
	 */
	public function step1()
	{
		$fileData = <<<'EOF'
<?php

namespace IPS\forums\extensions\core\MFAArea;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * Multi-Factor Authentication Area
 */
class _Forums
{
	/**
	 * Is this area available and should show in the ACP configuration?
	 *
	 * @return	bool
	 */
	public function isEnabled()
	{
		/* Make sure the plugin itself is enabled. */
		try
		{
			$plugin = \IPS\Plugin::constructFromData( \IPS\Db::i()->select( '*', 'core_plugins', array( "plugin_location=?", 'sqforums' ) )->first() );
			
			if ( $plugin->_enabled )
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}
		catch( \Exception $e )
		{
			return FALSE;
		}
	}
}
EOF;
		$filePath = \IPS\ROOT_PATH . '/applications/forums/extensions/core/MFAArea/Forums.php';
		
		if ( !is_dir( \IPS\ROOT_PATH . '/applications/forums/extensions/core/MFAArea' ) )
		{
			@\mkdir( \IPS\ROOT_PATH . '/applications/forums/extensions/core/MFAArea' );
			@\chmod( \IPS\ROOT_PATH . '/applications/forums/extensions/core/MFAArea', \IPS\IPS_FOLDER_PERMISSION );
		}
		
		if ( file_exists( $filePath ) )
		{
			return TRUE;
		}
		else if ( @\file_put_contents( \IPS\ROOT_PATH . '/applications/forums/extensions/core/MFAArea/Forums.php', $fileData ) )
		{
			@chmod( \IPS\ROOT_PATH . '/applications/forums/extensions/core/MFAArea/Forums.php', \IPS\IPS_FILE_PERMISSION );
			
			return TRUE;
		}
		else
		{
			return array( 'html' => "<p>Please upload the Forums.php file located in the \"tools\" folder of the download to /applications/forums/extensions/core/MFAArea/" );
		}
	}
}