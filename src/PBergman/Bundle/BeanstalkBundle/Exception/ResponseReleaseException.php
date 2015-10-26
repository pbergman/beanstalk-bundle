<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Exception;

class ResponseReleaseException extends ResponseException
{
    use Traits\BuriedTrait;
    use Traits\JobTrait;
}