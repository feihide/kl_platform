<?php
/**
 * @author：Ethan <ethantsien@gmail.com>
 * @version：$Id: RuntimeException.php 49 2013-11-25 06:35:13Z zhangyifei $
 */

namespace CL\Crypt\Password\Exception;

use Zend\Crypt\Password\Exception;

/**
 * Runtime argument exception
 */
class RuntimeException extends Exception\RuntimeException implements
    ExceptionInterface
{
	
}
