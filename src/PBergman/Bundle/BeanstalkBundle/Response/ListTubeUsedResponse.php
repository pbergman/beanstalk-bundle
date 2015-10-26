<?php
namespace PBergman\Bundle\BeanstalkBundle\Response;

class ListTubeUsedResponse extends AbstractResponse
{
    /**
     * @return bool
     */
    public function isSuccess()
    {
        return self::RESPONSE_USING === $this->response;
    }
}