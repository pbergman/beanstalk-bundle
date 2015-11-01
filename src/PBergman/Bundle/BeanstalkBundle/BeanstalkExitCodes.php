<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle;

/**
 * Class BeanstalkExitCodes
 *
 * @package namespace PBergman\Bundle\BeanstalkBundle
 */
final class BeanstalkExitCodes
{
    /**  This class is not meant to be instantiated. */
    private function __construct()
    {
    }

    const EXIT_OK       = 0;
    const EXIT_TIMEOUT  = 1;
}
