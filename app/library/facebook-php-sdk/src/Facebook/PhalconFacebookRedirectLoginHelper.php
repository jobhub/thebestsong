<?php

namespace Facebook;


/**
 * Class FacebookRedirectLoginHelper
 * @package Facebook
 * @author Fosco Marotto <fjm@fb.com>
 * @author David Poll <depoll@fb.com>
 */
class PhalconFacebookRedirectLoginHelper extends FacebookRedirectLoginHelper   {
	protected function storeState($state) {
		$session = \Phalcon\DI::getDefault()->getSession();
		
		$session->set('facebook.state',$state);
	}
	
	protected function loadState() {
		$session = \Phalcon\DI::getDefault()->getSession();
		
		$this->state = $session->get('facebook.state');
		
		return $this->state;
	}

}

?>
