<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Response;

class PauseTubeResponse extends AbstractResponse
{
    /**
     * @return bool
     */
    public function isSuccess()
    {
        return self::RESPONSE_PAUSED === $this->response;
    }
}