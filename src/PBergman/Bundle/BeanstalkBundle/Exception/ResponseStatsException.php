<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Exception;

class ResponseStatsException extends ResponseException
{
    use Traits\JobTrait;
    use Traits\TubeTrait;

}