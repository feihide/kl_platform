<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace Portal\Event;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;

/**
 * @category   Zend
 * @package    Zend_Mvc
 */
class Listener implements ListenerAggregateInterface
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();
	
    protected $sm;
    
    public function __construct($sm){
    	$this->sm=$sm;
    }
    
    /**
     * Attach to an event manager
     *
     * @param  EventManagerInterface $events
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('passport.dao.org.create', array($this, 'onCreatePassport'));
    }

    /**
     * Detach all our listeners from the event manager
     *
     * @param  EventManagerInterface $events
     * @return void
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    public function onCreatePassport($e)
    {    
        $params = $e->getParams();
        $field='passport_id,brand_id,org_group_id,admin_name,area_id,parent_id,biaodi_id,address,website,tel,introduction,is_delete,ctime';
		$field=explode(',',$field);
        $data=array();
        foreach($field as $item){
			if(isset($params[$item])){
				$data[$item]=$params[$item];
			}
		}
        $this->sm->get('dao_factory')->getDao('portal', 'org')->insert($data);
    }
}
