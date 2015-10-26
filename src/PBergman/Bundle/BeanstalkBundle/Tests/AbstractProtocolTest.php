<?php
/**
 * @author    Philip Bergman <philip@zicht.nl>
 * @copyright Zicht Online <http://www.zicht.nl>
 */
namespace PBergman\Bundle\BeanstalkBundle\Tests;


use PBergman\Bundle\BeanstalkBundle\Protocol\AbstractProtocol;
use PBergman\Bundle\BeanstalkBundle\Server\Connection;

class AbstractProtocolTest extends \PHPUnit_Framework_TestCase
{

    function testDoDispatch()
    {
        $stub = $this->getMockForAbstractClass(AbstractProtocol::class, [new Connection()]);


        $stub->expects($this->any())
            ->method('doDispatch')
            ->will($this->returnValue(true));

        try {
            $this->assertTrue($stub->dispatch());
        } catch (\PBergman\Bundle\BeanstalkBundle\Exception\InvalidArgumentException $e) {
            $this->assertSame(
                'The doDispatch method should return a response that implements ResponseInterface',
                $e->getMessage()
            );
        }

    }
}