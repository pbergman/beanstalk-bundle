<?php
/**
 * @author    Philip Bergman <philip@zicht.nl>
 * @copyright Zicht Online <http://www.zicht.nl>
 */
namespace PBergman\Bundle\BeanstalkBundle\Tests;

use PBergman\Bundle\BeanstalkBundle\Response\AbstractArrayResponse;
use PBergman\Bundle\BeanstalkBundle\Response\ResponseInterface;

class AbstractArrayResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testData()
    {

        $stub = $this->getMockForAbstractClass(AbstractArrayResponse::class, [ResponseInterface::RESPONSE_FOUND,[]]);

        $this->assertSame(0, count($stub));
        $stub->setData(['foo', 'bar']);
        $this->assertSame(2, count($stub));
        $this->assertSame(['foo', 'bar'], $stub->getData());

    }
}