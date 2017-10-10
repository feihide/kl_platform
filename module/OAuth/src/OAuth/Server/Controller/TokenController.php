<?php
/**
 * @author：Ethan <ethantsien@gmail.com>
 * @version：$Id$
 */

namespace OAuth\Server\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use OAuth2;

class TokenController extends AbstractRestfulController
{
    public function indexAction()
    {
        $server = $this->getServiceLocator()->get('oauth_server');

        $server->handleTokenRequest(OAuth2\Request::createFromGlobals())->send();

        exit;
    }
}
