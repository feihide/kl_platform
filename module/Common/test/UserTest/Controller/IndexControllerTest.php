<?php
/**
 * @author：Ethan <ethantsien@gmail.com>
 * @version：$Id$
 */
namespace UserTest\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class IndexControllerTest extends AbstractHttpControllerTestCase
{
    public function setUp()
    {
        $this->setApplicationConfig(
            include '/home/ethantsien/work/cailang/newsvn/api.cailang.com/config/application.config.php'
        );

        parent::setUp();
    }


    public function testIndexActionCanBeAccessed()
    {
        $this->dispatch('/user');
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('User');
        $this->assertControllerName('User\Controller\Index');
        $this->assertControllerClass('IndexController');
        $this->assertMatchedRouteName('user');
    }

}
