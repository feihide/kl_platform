<?php

namespace Platform\Controller;

use CL\Controller\BaseActionController;
use Zend\View\Model\JsonModel;

class PhoneController extends BaseActionController
{
    public function indexAction()
    {
        $roleType = array(
        '0' => '后台开放',
        '1' => '前台开放',
        '2' => '用户开放',
        '3' => '平台开放',
        '4' => '第三方开放',
      );
        $request = $this->getRequest()->getQuery()->toArray();
        $moduleData = $this->getDao('platform', 'modulePhone')->getList('is_delete=0', 1, 100, 'module asc');
        $module = array('' => '请选择');
        foreach ($moduleData as $item) {
            $module[$item['id']] = $item['module'];
        }
        $currentApi = array();
        $api = array('' => '请选择');
        if (isset($request['module']) && !empty($request['module'])) {
            $apiData = $this->getDao('platform', 'apiPhone')->getList('is_delete=0 and  module_id='.$request['module'], 1, 100, 'name asc');
            if (!empty($apiData)) {
                foreach ($apiData as $item) {
                    if (isset($request['api']) && !empty($request['api'])) {
                        if ($request['api'] == $item['id']) {
                            $currentApi = $item;
                        }
                    }

                    $api[$item['id']] = '['.$roleType[$item['role']].']'.$item['name'];
                }
            }
        } else {
            $request['module'] = '';
        }
        $param = array();
        if (isset($request['api']) && !empty($request['api'])) {
            $param = $this->getDao('platform', 'paramPhone')->getList('is_delete=0 and api_id='.$request['api'], 1, 100, 'name asc');
        } else {
            $request['api'] = '';
        }
        if ($_COOKIE['orgin']) {
            $orgin = $_COOKIE['orgin'];
        }
        if ($_COOKIE['phoneapihost']) {
            $apihost = $_COOKIE['phoneapihost'];
        } else {
            $apihost = $this->getServiceLocator()->get('cl_config')['params']['phone_api_domain'];
        }

        return array('request' => $request, 'module' => $module, 'api' => $api, 'param' => $param, 'currentApi' => $currentApi, 'orgin' => $orgin, 'apihost' => $apihost);
    }

    public function changeAction()
    {
        $post = $this->request->getPost()->toArray();
        if (!empty($post['apihost'])) {
            setcookie('phoneapihost', $post['apihost']);
        }
        setcookie('orgin', $post['orgin']);

        return new JsonModel(array('status' => 0));
    }

    public function manageAction()
    {
        $request = $this->getRequest()->getQuery()->toArray();
        if (!isset($request['page'])) {
            $request['page'] = 1;
        }

        $data = $this->getDao('platform', 'modulePhone')->getList('is_delete=0', $request['page'], 100, 'module asc');
        $num = $this->getDao('platform', 'modulePhone')->getCnt('is_delete=0');

        return array('data' => $data, 'num' => $num, 'request' => $request);
    }

    public function detailAction()
    {
        $request = $this->getRequest()->getQuery()->toArray();
        if (!isset($request['route']) || empty($request['route'])) {
            exit('未指定具体API');
        }

        if (!isset($request['page'])) {
            $page = 1;
        } else {
            $page = intval($request['page']);
        }

        $limit = 20;
        $offset = ($page - 1) * $limit;

        $param = array('route' => $request['route']);

        $list = $this->getDao('platform', 'papilog')->getLogList($param, $offset, $limit);
        $num = $this->getDao('platform', 'papilog')->getLogNum($param);

        return  array('list' => $list, 'num' => $num, 'page' => $page);
    }

    public function createmoduleAction()
    {
        $post = $this->getRequest()->getPost()->toArray();

        if (empty($post['id'])) {
            unset($post['id']);
            $post = array_merge($post, array('ctime' => time()));
            $this->getDao('platform', 'modulePhone')->insert($post);
        } else {
            $id = $post['id'];
            unset($post['id']);
            $this->getDao('platform', 'modulePhone')->update($post, 'id='.$id);
        }

        return new JsonModel(array('status' => 0));
    }

    public function deletemoduleAction()
    {
        $post = $this->getRequest()->getPost()->toArray();
        if (!empty($post['id'])) {
            $this->getDao('platform', 'modulePhone')->delete('id='.$post['id']);
        }

        return new JsonModel(array('status' => 0));
    }

    public function gettreeAction()
    {
        $request = $this->getRequest()->getQuery()->toArray();

        if (($request['root'] == 'source')) {
            $request['root'] = 0;
            $root = array('text' => '分类列表', 'expanded' => true, 'classes' => 'important');
        }

        $cond = 'is_delete = 0';
        if (!isset($request['page'])) {
            $request['page'] = 1;
        }

        $child = $this->getDao('platform', 'manageTree')->getList($cond);

        $list = array();
        if (!empty($child['list'])) {
            foreach ($child['list'] as $item) {
                $tmp = array();
                $tmp['text'] = "[ {$item['id']} ]".$item['title'].'['.$item['url'].']';
                $tmp['id'] = $item['id'];
                $tmp['hasChildren'] = true;
                $list[] = $tmp;
            }
        }

        if ($request['root'] == 0) {
            $root['children'] = $list;
            $root = array($root);
        } else {
            $root = $list;
        }

        return new JsonModel($root);
    }

    public function apiAction()
    {
        $request = $this->getRequest()->getQuery()->toArray();
        $moduleId = $request['moduleId'];

        if (!isset($request['page'])) {
            $request['page'] = 1;
        }
        $cond = 'is_delete=0 and module_id='.$moduleId;
        $data = $this->getDao('platform', 'apiPhone')->getList($cond, $request['page'], 100, 'name desc');
        $num = $this->getDao('platform', 'apiPhone')->getCnt($cond);

        $module = $this->getDao('platform', 'modulePhone')->getOne('id='.$moduleId);

        return array('data' => $data, 'num' => $num, 'request' => $request, 'module' => $module);
    }

    public function createapiAction()
    {
        $post = $this->getRequest()->getPost()->toArray();

        if (empty($post['id'])) {
            unset($post['id']);
            $post = array_merge($post, array('ctime' => time()));
            $this->getDao('platform', 'apiPhone')->insert($post);
        } else {
            $id = $post['id'];
            unset($post['id']);
            $this->getDao('platform', 'apiPhone')->update($post, 'id='.$id);
        }

        return new JsonModel(array('status' => 0));
    }

    public function deleteapiAction()
    {
        $post = $this->getRequest()->getPost()->toArray();
        if (!empty($post['id'])) {
            $this->getDao('platform', 'apiPhone')->delete('id='.$post['id']);
        }

        return new JsonModel(array('status' => 0));
    }

    public function paramAction()
    {
        $request = $this->getRequest()->getQuery()->toArray();
        $apiId = $request['apiId'];

        if (!isset($request['page'])) {
            $request['page'] = 1;
        }
        $cond = 'is_delete=0 and api_id='.$apiId;
        $data = $this->getDao('platform', 'paramPhone')->getList($cond, $request['page'], 100, 'name asc');
        $num = $this->getDao('platform', 'paramPhone')->getCnt($cond);

        $api = $this->getDao('platform', 'apiPhone')->getOne('id='.$apiId);

        return array('data' => $data, 'num' => $num, 'request' => $request, 'api' => $api);
    }

    public function createparamAction()
    {
        $post = $this->getRequest()->getPost()->toArray();

        if (empty($post['id'])) {
            unset($post['id']);
            $post = array_merge($post, array('ctime' => time()));
            $this->getDao('platform', 'paramPhone')->insert($post);
        } else {
            $id = $post['id'];
            unset($post['id']);
            $this->getDao('platform', 'paramPhone')->update($post, 'id='.$id);
        }

        return new JsonModel(array('status' => 0));
    }

    public function deleteparamAction()
    {
        $post = $this->getRequest()->getPost()->toArray();
        if (!empty($post['id'])) {
            $this->getDao('platform', 'paramPhone')->delete('id='.$post['id']);
        }

        return new JsonModel(array('status' => 0));
    }

    public function testAction()
    {
        $re = $this->getRequest()->getPost()->toArray();
        $param = array();

        if (!empty($re['param'])) {
            foreach ($re['param'] as $key => $value) {
                $pos = strrpos($re['api_route'], '/'.$key);
                if ($pos && (strrpos($key, ':') !== false)) {
                    $re['api_route'] = str_replace('/'.$key, '/'.$value, $re['api_route']);
                } else {
                    $param[$key] = $value;
                }
            }
        }

        if ($_COOKIE['phoneapihost']) {
            $apihost = $_COOKIE['phoneapihost'];
        } else {
            $apihost = $this->getServiceLocator()->get('cl_config')['params']['phone_api_domain'];
        }

        $url = $apihost.sprintf('%s?%s', $re['api_route'], http_build_query($param));
        $info = $this->callPhoneAPI($re['request_type'], $re['api_route'], $param); //put数据
         $re = json_decode($info, true);
        $accessToken = '';
        if (isset($re['data']['access_token']) && !empty($re['data']['access_token'])) {
            $accessToken = $re['data']['access_token'];
        }

        return new JsonModel(array('status' => 0, 'data' => $info, 'url' => $url, 'accessToken' => $accessToken,  'json' => '<pre>'.print_r(json_decode($info, true), true).'</pre>'));
    }

    public function callPhoneAPI($method, $url, $data = false)
    {
        return $this->getDao('platform', 'paramPhone')->callphoneapi($method, $url, $data);
    }
}
