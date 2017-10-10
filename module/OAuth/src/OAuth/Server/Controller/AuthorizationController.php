<?php
/**
 * @author：Ethan <ethantsien@gmail.com>
 * @version：$Id$
 */

namespace OAuth\Server\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use OAuth2;

class AuthorizationController extends AbstractRestfulController
{
    public function indexAction()
    {
        $request = OAuth2\Request::createFromGlobals();
        $response = new OAuth2\Response();
        $server = $this->getServiceLocator()->get('oauth_server');

        if (!$server->validateAuthorizeRequest($request, $response)) {
            $response->send();
            die;
        }

        if (empty($_POST)) {
            exit('
                <form method="post">
                <label>Do You Authorize TestClient?</label><br />
                <input type="submit" name="authorized" value="yes">
                <input type="submit" name="authorized" value="no">
                </form>');
        }

        $is_authorized = ($_POST['authorized'] === 'yes');
        $server->handleAuthorizeRequest($request, $response, $is_authorized);
        if ($is_authorized) {
            $code = substr($response->getHttpHeader('Location'), strpos($response->getHttpHeader('Location'), 'code=')+5, 40);
            exit("SUCCESS! Authorization Code: $code");
        }

        $response->send();

        exit;

    }
}
