<?php
/**
 * @author    Philip Bergman <philip@zicht.nl>
 * @copyright Zicht Online <http://www.zicht.nl>
 */
namespace PBergman\Bundle\BeanstalkBundle\Tests\Traits;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class DispatcherMockTrait
 *
 * @package     PBergman\Bundle\BeanstalkBundle\Tests\Traits
 *
 * @var \PHPUnit_Framework_TestCase $this
 */
trait DispatcherMockTrait
{
    /**
     * @param   array|callable[] $callbacks
     * @return \PHPUnit_Framework_MockObject_MockObject|EventDispatcherInterface
     */
    public function getNewMockDispatcherCallable(array $callbacks)
    {
        if (!$this instanceof \PHPUnit_Framework_TestCase){
            throw new \RuntimeException('This trait should only be used in a phpunit test envirement.');
        }

        $counter = 0;
        $dispatcher = $this->getMock(EventDispatcherInterface::class);
        $dispatcher
            ->expects($this->exactly(count($callbacks)))
            ->method('hasListeners')
            ->will($this->returnValue(true))
        ;

        $dispatcher
            ->expects($this->exactly(count($callbacks)))
            ->method('dispatch')
            ->willReturnCallback(function($name, Event $event) use ($callbacks, &$counter) {
                $callbacks[$counter++]($name, $event);
            });

        return $dispatcher;
    }

    /**
     * @param  array $names
     * @return \PHPUnit_Framework_MockObject_MockObject|EventDispatcherInterface
     */
    public function getNewMockDispatcherChecKName(array $names)
    {
        $callable = [];

        foreach ($names as $name) {
            $callable[] = function($eventName) use ($name) {
                $this->assertSame($name, $eventName);
            };
        }

        return $this->getNewMockDispatcherCallable($callable);
    }
}