<?php
/**
 * @author    Philip Bergman <philip@zicht.nl>
 * @copyright Zicht Online <http://www.zicht.nl>
 */
namespace PBergman\Bundle\BeanstalkBundle\Tests\Traits;

trait StringTrait
{

    public function getRandomString($length = 10, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789 ~!@#$%^&*()<>"{}[]|\\`')
    {
        list($ret, $max) = [null, mb_strlen($chars, '8bit') - 1];
        for ($i = 0; $i < $length; ++$i) {
            $ret .= $chars[mt_rand(0, $max)];
        }
        return $ret;
    }
}