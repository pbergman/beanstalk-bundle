<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Transformer;

/**
 * Class DataContainer
 *
 * @package PBergman\Bundle\BeanstalkBundle\Transformer
 */
class DataContainer
{
    /** @var array|object|string|int|double|float|null */
    protected $data;
    /** @var DataHeader  */
    protected $header;

    /**
     * @inheritdoc
     */
    function __construct($data, DataHeader $header)
    {
        $this->data = $data;
        $this->header = $header;
    }


    /**
     * @return array|float|int|null|object|string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return DataHeader
     */
    public function getHeader()
    {
        return $this->header;
    }
}