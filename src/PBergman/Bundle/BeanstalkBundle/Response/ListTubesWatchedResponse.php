<?php
namespace PBergman\Bundle\BeanstalkBundle\Response;

class ListTubesWatchedResponse extends AbstractResponse
{
    /**
     * @return bool
     */
    public function isSuccess()
    {
        return self::RESPONSE_OK === $this->response;
    }
}