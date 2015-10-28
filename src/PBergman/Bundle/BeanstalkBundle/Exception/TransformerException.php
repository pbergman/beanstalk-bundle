<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Exception;

class TransformerException extends \Exception implements ExceptionInterface
{
    /**
     * @param   $data
     * @return  $this
     */
    static function unsupportedType($data)
    {
        return new self(sprintf('Unsupported type given: "%s"', gettype($data)));
    }

    /**
     * @param   $a
     * @param   $b
     * @return  $this
     */
    static function crcMismatch($a, $b)
    {
        return new self(sprintf('CRC mismatch 0x%s !== 0x%s', bin2hex($a), bin2hex($b)));
    }
    /**
     * @param   $a
     * @param   $b
     * @return  $this
     */
    static function signatureMismatch($a, $b)
    {
        return new self(sprintf('Signature mismatch 0x%x !== 0x%x', $a, $b));
    }
}
