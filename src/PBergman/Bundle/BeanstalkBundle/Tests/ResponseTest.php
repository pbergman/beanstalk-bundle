<?php
/**
 * @author    Philip Bergman <philip@zicht.nl>
 * @copyright Zicht Online <http://www.zicht.nl>
 */
namespace PBergman\Bundle\BeanstalkBundle\Tests;

class ResponseTest extends \PHPUnit_Framework_TestCase
{
    use Traits\BeanstalkTrait;

    /**
     * @expectedException        \PBergman\Bundle\BeanstalkBundle\Exception\ResponseErrorException
     * @expectedExceptionMessage The server cannot allocate enough memory for the job. The client should try again later
     */
    public function testOutOfMemory()
    {
        $producer = $this->getNewBeanstalk()->getProducer('default');
        $producer->getConnection()->writeReponse("OUT_OF_MEMORY\r\n");
        $producer->stats();
    }

    /**
     * @expectedException        \PBergman\Bundle\BeanstalkBundle\Exception\ResponseErrorException
     * @expectedExceptionMessage A bug in the server, please report it at http://groups.google.com/group/beanstalk-talk
     */
    public function testInternalError()
    {
        $producer = $this->getNewBeanstalk()->getProducer('default');
        $producer->getConnection()->writeReponse("INTERNAL_ERROR\r\n");
        $producer->stats();
    }

    /**
     * @expectedException        \PBergman\Bundle\BeanstalkBundle\Exception\ResponseErrorException
     * @expectedExceptionMessage The client sent a command that the server does not know.
     */
    public function testUnknownCommand()
    {
        $producer = $this->getNewBeanstalk()->getProducer('default');
        $producer->getConnection()->writeReponse("UNKNOWN_COMMAND\r\n");
        $producer->stats();
    }

    /**
     * @expectedException        \PBergman\Bundle\BeanstalkBundle\Exception\ResponseErrorException
     * @expectedExceptionMessage The client sent a command line that was not well-formed.
     */
    public function testBabFormat()
    {
        $producer = $this->getNewBeanstalk()->getProducer('default');
        $producer->getConnection()->writeReponse("BAD_FORMAT\r\n");
        $producer->stats();
    }

}