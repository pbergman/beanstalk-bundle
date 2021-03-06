<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Exception\Traits;

use PBergman\Bundle\BeanstalkBundle\Exception\ResponseException;

trait JobTrait
{
    /**
     * @param  int     $job
     * @param  string  $code
     * @return $this
     */
    static function jobDoesNotExist($job, $code = "NOT_FOUND")
    {
        return new self(sprintf('[%s] Job "%s" does not exist.', $code, $job), ResponseException::NOT_FOUND);
    }

    /**
     * @param  int     $job
     * @param  string  $code
     * @return $this
     */
    static function jobDoesNotExistOrNotKickable($job, $code = "NOT_FOUND")
    {
        return new self(
            sprintf('[%s] Job "%s" does not exist or is not in a kickable state.', $code, $job), ResponseException::NOT_FOUND
        );
    }

    /**
     * @return $this
     */
    static function jobDoesNotExistOrNotReservedByClient($id)
    {
        return new self(
            sprintf('Job %u does not exist or is not reserved by the client.', $id), ResponseException::NOT_FOUND
        );
    }
}