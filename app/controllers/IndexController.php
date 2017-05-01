<?php

use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;
use Facebook\PhalconFacebookRedirectLoginHelper as FacebookRedirectLoginHelper;


class IndexController extends ControllerBase
{

    public function indexAction()
    {
        
    }
    
	public function fbLoginAction()  {
		// load template
		
	}
    
	
    public function importStylesAction()
    {
		echo "import styles <BR>";
	}

}

