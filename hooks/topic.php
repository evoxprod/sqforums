//<?php

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	exit;
}

class hook22 extends _HOOK_CLASS_
{


	/**
	 * Execute
	 *
	 * @return	void
	 */
	public function execute()
	{
		$sqforums = explode( ',', \IPS\Settings::i()->sqforums );
		try
		{
			$topic = \IPS\forums\Topic::loadAndCheckPerms( \IPS\Request::i()->id );
		}
		catch( \OutOfRangeException $e )
		{
			return parent::execute();
		}
		
		if ( in_array( $topic->forum_id, $sqforums ) AND $output = \IPS\MFA\MFAHandler::accessToArea( 'forums', 'Forums', $topic->url() ) )
		{
			/* If the user isn't logged in, and this forum requires a question to be answered, it's safe to assume they don't have access */
			if ( !\IPS\Member::loggedIn()->member_id )
			{
				\IPS\Output::i()->error( $topic->container()->errorMessage(), '1SQF1/1', 403, '' );
			}
			
			\IPS\Output::i()->title = \IPS\Member::loggedIn()->language()->addToStack( 'sqforums_verification_needed' );
			\IPS\Output::i()->output = $output;
			return;
		}
		
		return parent::execute();
	}

}
