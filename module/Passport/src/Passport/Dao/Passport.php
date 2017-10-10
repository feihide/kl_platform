<?php
namespace Passport\Dao;


class Passport extends Base
{

    /**
     * @param $name 登入标示 （username email mobile）
     * @param $pwd password
     * @return bool array on success or false on fail
     * @author ym
     */

    function login($name , $pwd)
    {
        if(empty($name) || empty($pwd)) {
            return false;
        }

	    #1：username login ，2：email login 3:mobile login
	    $type = $this->checkNameType($name) ;

	    if($data = $this->fetchPassportByName($name , $type)) {
		    if($data['is_delete'] =='1') { #此用户已删除
			    return false;
		    }
		    if(!empty($data['pwd'])) {
			    $chash = new \Zend\Crypt\Password\Bcrypt();
			    if($chash->verify($pwd , $data['pwd'])) { #密码匹配
				    return $data;
			    }
		    }
	    }
        return false;
    }

	/**
	 * 获取用户明细
	 * @param $name
	 * @author ym
	 */
	function fetchInfoByName($name)
	{
		if(empty($name)) {
			return false;
		}
		$type = $this->checkNameType($name);
		if($data = $this->fetchPassportByName($name , $type)) {
			$data['login_type'] = $type;
			return $data;
		}
		return false;
	}

    /**
     * @param $name 标示 （1:username; 2:email; 3:mobile;4:at_name）
     */
    function fetchPassportByName($name , $type)
    {
        if(empty($name)) {
            return false;
        }

	    if($data = $this->mixPassportData($name , $type)) {
		    if($data = $this->mixPassportData($data['passport_id'] ,5)){
			    return $data;
		    }
	    }
	    $sql = '';
	    $p = array();
        if($type == '1') {
            $sql .= ' and username = ?';
        }else if($type == '2') {
            $sql .= ' and email=? ';
        } else if($type == '3') {
            $sql .= ' and mobile=?';
        } else if($type =='4') {
	        $sql .= ' and at_name=?';
        }

        if(empty($sql)) {
            return false;
        }
	    $p[] = $name;
        $sql = 'select id ,pwd, portal_code,username,`type`,at_name,email,email_is_verified,mobile,mobile_is_verified ,is_delete from passport where 1=1 '.$sql.' limit 1';
        if($data = $this->db->query($sql ,$p)->toArray()) {
	        $data = array_shift($data);
	        $data['passport_id'] = $data['id'];
	        $this->mixPassportData($data['id'] ,5 , $data);
	        return $data;
        }
	    return false;
    }

	/**
	 * 注册用户 机构
	 * @param $data
	 * @return bool
	 * @author ym
	 */
	function addPassport($data) {

		$passport_id = false;
		for($i = 0;$i<4 ; $i++) {
	        if($passport_id  = $this->getTicketId('passport')) {
		        if(!$this->mixPassportData($passport_id , 5)) { #passport_id 重复
	                break;
		        }
	        }
		}
		if(empty($passport_id)) {
			return false;
		}
        if(!($pwd = $this->defaultMaps($data ,'pwd')) || !($loginname=$this->defaultMaps($data ,'username'))) {
            return false;
        }
		$data['passport_id'] = $passport_id;
		$login_type = $this->checkNameType($loginname); #1：username login ，2：email login 3:mobile login
		$username = '';
		$mobile = $this->defaultMaps($data ,'mobile' ,'');
		$email= $this->defaultMaps($data ,'email','');

		if('1' == $login_type) {
			$username = $loginname;
		} else if('2' == $login_type) {
			$email = $loginname;
		}else {
			$mobile = $loginname;
		}
		if(!empty($username)) {
			if($this->mixPassportData($username,1)) {
				return false;#username 重复
			}
		}
		if(!empty($email)) {
			if($this->mixPassportData($email ,2)) {
				return false;#email 重复
			}
		}
		if(!empty($mobile)) {
			if($this->mixPassportData($mobile ,3)) {
				return false;#mobile 重复
			}
		}

	    $type = $this->defaultMaps($data , 'type' ,0);

	    if('0' == $type ) {
		    $data['at_name'] = empty($data['at_name']) ? 'clang_'.$passport_id : $data['at_name'];
	    }
		if(!$this->defaultMaps($data ,'at_name')) {
			return false;
		}
		if($this->mixPassportData($this->defaultMaps($data ,'at_name') ,4)) {
			return false;#at_name 重复
		}
		$chash = new \Zend\Crypt\Password\Bcrypt();
		$data['pwd'] = $chash->create($pwd) ;#加密pwd

		$this->db->getDriver()->getConnection()->beginTransaction();
        $p = array(
            $passport_id ,
            $this->defaultMaps($data , 'portal_code' ,''),
            $this->defaultMaps($data ,'pwd'),#*
	        $type,$username,
	        $this->defaultMaps($data ,'at_name'),#*
            $email, 0, $mobile,  0,  time(),time()
        );

	    $isok = false;

        $sql ='insert into passport (id , portal_code , pwd,`type`,username,at_name,email,email_is_verified,mobile,mobile_is_verified,ctime,utime) ';
        $sql .=' values (?,?,?,?,?,?,?,?,?,?,?,?)';

        if($this->db->query($sql,$p)) {
	        $isok = true;
	        $data['passport_id'] = $passport_id;
            if($type == '3') { #机构用户
                $this->trigger('passport.dao.org.create', null, $data);
            }
        }
	    if($isok) {

		    // set cache
		    $this->fetchPassportById($passport_id , true) ;
		    //COMMIT
		    $this->db->getDriver()->getConnection()->commit();

                    return $passport_id;
	    } else {
		    $this->db->getDriver()->getConnection()->rollback();
            }

            return false;
    }

	/**
	 * 更新C端用户 基本信息
	 * @param $data
	 * @return bool
	 */
	function updatePassport($data)
	{
		if(empty($data['passport_id'])) {
			return false;
		}
		$sql = '';
		$p = array();
		if(isset($data['portal_code'])) {
			$sql .='portal_code=?,';
			$p[] = $data['portal_code'];
		}
		if(isset($data['pwd'])) {
			$sql .= 'pwd=?,';
			$chash = new \Zend\Crypt\Password\Bcrypt();
			$p[] = $chash->create($data['pwd']) ;#加密pwd
		}
		if(isset($data['at_name'])) {
			//检查at_name 是否已经用了
			if($rst = $this->mixPassportData($data['at_name'],4)) {
				if($rst['passport_id'] != $data['passport_id']) {
					return false;#at_name 已被使用
				}
			}
			$sql .='at_name=?,';
			$p[] = $data['at_name'];
		}
		if(isset($data['avatar'])) {
			$sql .='avatar=?,';
			$p[] = $data['avatar'];
		}
		if(isset($data['mobile'])) {
			//检查at_name 是否已经用了
			if($rst = $this->mixPassportData($data['mobile'],3)) {
				if($rst['passport_id'] != $data['passport_id']) {
					return false;#at_name 已被使用
				}
			} else { #新的mobile
				$sql .='mobile=?,mobile_is_verified=0,';
				$p[] = $data['mobile'];
			}
		}
		if(isset($data['mobile_is_verified'])) {
			$sql .='mobile_is_verified=?,';
			$p[] = $data['mobile_is_verified'];
		}
		if(isset($data['email'])) {
			if($rst = $this->mixPassportData($data['email'],2)) {
				if($rst['passport_id'] != $data['passport_id']) {
					return false;#at_name 已被使用
				}
			} else {#新的email
				$sql .='email=?,email_is_verified=0,';
				$p[] = $data['email'];
			}
		}
		if(isset($data['email_is_verified'])) {
			$sql .='email_is_verified=?,';
			$p[] = $data['email_is_verified'];
		}
		if(empty($sql)) {
			return false;
		}
		$sql .= 'utime=?';
		$p[] = time();
		$p[] = $data['passport_id'];

		$this->db->getDriver()->getConnection()->beginTransaction();

		$sql ='update passport set '.trim($sql,',').' where id=?';

		if($this->db->query($sql,$p)) {
			$this->db->getDriver()->getConnection()->commit();
			// set cache
			$this->fetchPassportById($data['passport_id'] ,true);
			return true;
		}
		$this->db->getDriver()->getConnection()->rollback();
		return false;

	}

	function deletePassport($passport_id)
	{
		if(empty($passport_id)) {
			return false;
		}
		$sql ='update passport set is_delete=1 ,utime=? where id=?';
		if($this->db->query($sql , array(time() ,$passport_id ))) {
			// set cache
			$this->fetchPassportById($passport_id ,true);
			return true;
		}
		return false;
	}

	/**
	 * 通过passport_id 获取passport infomation
	 * @param $passport_id
	 * @param bool $force_from_db
	 * @return bool|mixed
	 * @author ym
	 */
	function fetchPassportById($passport_id ,$force_from_db = false) {
        if(empty($passport_id)) {
            return false;
        }

        if(!$force_from_db && ($data = $this->mixPassportData($passport_id , 5))) {
            return $data;
        }

        $sql = 'select id ,pwd, portal_code,username,`type`,at_name,email,email_is_verified,mobile,mobile_is_verified,is_delete from passport ';
        $sql .= ' where id=? and is_delete=0 limit 1';

        if($data = $this->db->query($sql , array($passport_id))->toArray()) {
	        $data = array_shift($data);
	        $data['passport_id'] = $passport_id;
	        $this->mixPassportData($passport_id , 5 ,$data);
            return $data;
        }
        return false;
    }
    /**
     * 通过passport ids返回 passport信息
     * @param $data
     * @author ym
     */
    function fetchPassportsByIds($passport_mixid = false) {

        $max_limit = 100;
        if(empty($passport_mixid)) {
            return false;
        }
        $passport_mixid = is_array($passport_mixid) ? $passport_mixid : array($passport_mixid);
        $data = array();
        $passport_mixid = array_slice($passport_mixid , 0 ,$max_limit);#limit fetch nums
        foreach($passport_mixid as $id) {
            $data[$id] = $this->fetchPassportById($id);
        }
        return $data;
    }

}
