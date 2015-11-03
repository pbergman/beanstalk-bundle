<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Exception\Traits;

use PBergman\Bundle\BeanstalkBundle\Exception\ResponseException;

trait TubeTrait
{
    /**
     * @param  string  $tube
     * @return $this
     */
    static function tubeDoesNotExist($tube)
    {
        return new self(sprintf('Tube "%s" does not exist', $tube), ResponseException::NOT_FOUND);
    }
}