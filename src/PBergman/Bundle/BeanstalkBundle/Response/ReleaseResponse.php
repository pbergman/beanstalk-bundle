<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Response;

class ReleaseResponse extends AbstractResponse
{
    /**
     * @return bool
     */
    public function isSuccess()
    {
        return self::RESPONSE_RELEASED === $this->response;
    }
}
