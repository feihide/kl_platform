<?php
/**
 * @author：ym
 * @version：$Id $
 */
return array(
    'controllers' => array(
        'invokables' => array(
	        'passport\controller\password' => 'Passport\Controller\PasswordController',#重置密码
            'passport\controller\passport' => 'Passport\Controller\PassportController',#login
			'passport\controller\orgs'  => 'Passport\Controller\OrgController', #机构注册
	        'passport\controller\users'  => 'Passport\Controller\UserController',#C端用户注册 更新基本信息
            'passport\controller\edus' => 'Passport\Controller\EduController',#C端用户新增更新其教育信息
	        'passport\controller\jobs' => 'Passport\Controller\JobController',#C端用户新增更新其工作履历
        ),
    ),
);
