<?php
/**
 * @author：Ethan <ethantsien@gmail.com>
 * @version：$Id$
 */
return array(
		'controllers' => array(
				'invokables' => array(
						'orgs' => 'Portal\Controller\OrgController',
						'org\controller\groups'=>'Portal\Controller\GroupController',
						'org\controller\privilege\groups'=>'Portal\Controller\PrivilegeController',
						'org\controller\members'=>'Portal\Controller\MemberController',
						'org\controller\membergroups'=>'Portal\Controller\MemberGroupController',
						'org\controller\services'=>'Portal\Controller\ServiceController',
						'org\controller\privilege\membergroups'=>'Portal\Controller\MemberPrivilegeController',
				),
		),
);
