//<?php

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	exit;
}

class hook20 extends _HOOK_CLASS_
{


	/**
	 * [Node] Add/Edit Form
	 *
	 * @param	\IPS\Helpers\Form	$form	The form
	 * @return	void
	 */
	public function form( &$form )
	{
		parent::form( $form );
		
		if ( $this->sub_can_post AND !$this->redirect_url )
		{
			$current = explode( ',', \IPS\Settings::i()->sqforums );
			$form->addTab( 'security_questions' );
			$form->add( new \IPS\Helpers\Form\YesNo( 'forums_sq_require', (bool) in_array( $this->_id, $current ) ) );
		}
	}

	/**
	 * [Node] Format form values from add/edit form for save
	 *
	 * @param	array	$values	Values from the form
	 * @return	array
	 */
	public function formatFormValues( $values )
	{
		if ( array_key_exists( 'forums_sq_require', $values ) )
		{
			$current = explode( ',', \IPS\Settings::i()->sqforums );
			if ( $values['forums_sq_require'] )
			{
				if ( !in_array( $this->_id, $current ) )
				{
					$current[] = $this->_id;
					
					\IPS\Db::i()->update( 'core_sys_conf_settings', array( "conf_value" => implode( ',', $current ) ), array( "conf_key=?", 'sqforums' ) );
					unset( \IPS\Data\Store::i()->settings );
				}
			}
			else
			{
				$new = array();
				foreach( $current AS $forum )
				{
					if ( $forum != $this->_id )
					{
						$new[] = $forum;
					}
				}
				
				\IPS\Db::i()->update( 'core_sys_conf_settings', array( "conf_value" => implode( ',', $new ) ), array( "conf_key=?", 'sqforums' ) );
				unset( \IPS\Data\Store::i()->settings );
			}
			
			unset( $values['forums_sq_require'] );
		}
		return parent::formatFormValues( $values );
	}
	
	/**
	 * Get last post data
	 *
	 * @return	array|NULL
	 */
	public function lastPost()
	{
		$sqforums = explode( ',', \IPS\Settings::i()->sqforums );
		if ( in_array( $this->_id, $sqforums ) AND \IPS\MFA\MFAHandler::accessToArea( 'forums', 'Forums', $this->url() ) )
		{
			return NULL;
		}
		
		return parent::lastPost();
	}
}
