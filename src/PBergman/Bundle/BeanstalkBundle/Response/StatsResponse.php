<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Response;

/**
 * Class StatsResponse
 *
 * @package PBergman\Bundle\BeanstalkBundle\Response
 */
class StatsResponse extends AbstractArrayResponse
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