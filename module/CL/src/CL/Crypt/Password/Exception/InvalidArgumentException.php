<?php
/**
 * @author：Ethan <ethantsien@gmail.com>
 * @version：$Id: InvalidArgumentException.php 24 2013-11-20 06:52:08Z quanwei $
 */

namespace CL\Crypt\Password\Exception;

use Zend\Crypt\Password\Exception;

/**
 * Invalid argument exception
 */
class InvalidArgumentException extends Exception\InvalidArgumentException implements
    ExceptionInterface
{}
