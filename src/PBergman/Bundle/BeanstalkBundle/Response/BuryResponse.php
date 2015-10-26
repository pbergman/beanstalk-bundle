<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Response;

class BuryResponse extends AbstractResponse
{
    /**
     * @return bool
     */
    public function isSuccess()
    {
        return self::RESPONSE_BURIED === $this->response;
    }
}