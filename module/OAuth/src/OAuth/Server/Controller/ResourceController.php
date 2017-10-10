<?php
/**
 * @author：Ethan <ethantsien@gmail.com>
 * @version：$Id$
 */

namespace OAuth\Server\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use OAuth2;

class ResourceController extends AbstractRestfulController
{
    public function indexAction()
    {
        $server = $this->getServiceLocator()->get('oauth_server');

        if (!$server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $server->getResponse()->send();
die;
        }
        
        return new JsonModel(array('success' => true, 'message' => 'You accessed my APIs!'));
    }
}
