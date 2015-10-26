<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Response;

class PutResponse extends AbstractResponse
{
    /**
     * @return bool
     */
    public function isSuccess()
    {
        return self::RESPONSE_INSERTED === $this->response;
    }
}