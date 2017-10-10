<?php
/**
 * @author：Ethan <ethantsien@gmail.com>
 * @version：$Id: Bcrypt.php 24 2013-11-20 06:52:08Z quanwei $
 */

namespace CL\Crypt\Password;

use Zend\Crypt\Password\PasswordInterface;

class Bcrypt implements PasswordInterface
{

    /**
     * @var string
     */
    protected $cost = '14';

    public function __construct()
    {
        if (version_compare(PHP_VERSION, '5.5.0') < 0) {
            throw new Exception\RuntimeException(
                '\CL\Crypt\Password\Bcrypt 需要PHP 5.5.0以上'
            );
        }
    }

    /**
     * Create a password hash for a given plain text password
     *
     * @param  string $password The password to hash
     * @return string The formatted password hash
     */
    public function create($password)
    {
        // 目前使用PASSWORD_BCRYPT保证生成固定的60个字符串
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => $this->cost]);
    }

    /**
     * Create a password hash for a given plain text password
     *
     * @param  string $password The password to hash
     * @return string The formatted password hash
     */
    public function verify($password, $hash)
    {
        return password_verify($password, $hash);
    }

    /**
     * 根据服务器的性能生成合适的cost
     *
     * @return Bcrypt
     */
    public function generateCost()
    {
        $timeTarget = 0.2; 

        $cost = 9;
        do {
            $cost++;
            $start = microtime(true);
            password_hash("test", PASSWORD_BCRYPT, ["cost" => $cost]);
            $end = microtime(true);
        } while (($end - $start) < $timeTarget);

        $this->setCost($cost);

        return $this;
    }

    /**
     * Set the cost parameter
     *
     * @param  int|string $cost
     * @throws Exception\InvalidArgumentException
     * @return Bcrypt
     */
    public function setCost($cost)
    {
        if (!empty($cost)) {
            $cost = (int) $cost;
            if ($cost < 4 || $cost > 31) {
                throw new Exception\InvalidArgumentException(
                    'The cost parameter of bcrypt must be in range 04-31'
                );
            }
            $this->cost = sprintf('%1$02d', $cost);
        }
        return $this;
    }

    /**
     * Get the cost parameter
     *
     * @return string
     */
    public function getCost()
    {
        return $this->cost;
    }
}
