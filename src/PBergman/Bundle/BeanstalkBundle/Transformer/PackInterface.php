<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Transformer;

/**
 * Interface PackInterface
 *
 * @package PBergman\Bundle\BeanstalkBundle\Transformer
 */
interface PackInterface
{
    /**
     * will return a  binary string
     *
     * @return string
     */
    public function pack();

    /**
     *  will unpack binary string and return new instance of self with properties set
     *
     * @param   string  $data
     * @return  self
     */
    static public function unpack($data);
}