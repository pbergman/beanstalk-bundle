<?php
namespace PBergman\Bundle\BeanstalkBundle\Response;

class ListTubesResponse extends AbstractArrayResponse
{
    /**
     * will return true if dispatch was a success
     *
     * @return bool
     */
    public function isSuccess()
    {
        return self::RESPONSE_OK === $this->response;
    }
}