<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Transformer;

use PBergman\Bundle\BeanstalkBundle\Exception\TransformerException;

/**
 * Class DataTransformer
 *
 * @package PBergman\Bundle\BeanstalkBundle\Transformer
 */
class DataTransformer implements PackInterface
{
    /** @var string  */
    protected $name;
    /** @var array|object|string|int|double|float|null */
    protected $data;
    /** @var bool  */
    protected $compressed = true;

    /**
     * @inheritdoc
     */
    function __construct($name = null, $data = null, $compress = true)
    {
        $this->name = $name;
        $this->data = $data;
        $this->compressed = $compress;
    }

    /**
     * will return a  binary string
     *
     * @return  string
     * @throws  TransformerException
     */
    public function pack()
    {
        $header = new DataHeader();
        $header
            ->setCompressed(false)
            ->setSerialized(false)
        ;

        switch (strtolower(gettype($this->data))) {
            case 'boolean':
                $header->setType($header::T_BOOLEAN);
                break;
            case 'integer':
                $header->setType($header::T_INTEGER);
                break;
            case 'double':
                $header->setType($header::T_DOUBLE);
                break;
            case 'string':
                $header->setType($header::T_STRING);
                break;
            case 'array':
                $header->setType($header::T_ARRAY);
                break;
            case 'object':
                $header->setType($header::T_OBJECT);
                break;
            case 'resource':
            case 'unknown type':
                throw TransformerException::unsupportedType($this->data);
                break;
        }

        if ($header->getType() === ($header->getType() & ($header::T_OBJECT|$header::T_ARRAY))) {
            $this->data = serialize($this->data);
            $header->setSerialized(true);
        }

        if ($this->compressed) {
            $this->data = gzcompress($this->data, 9);
            $header->setCompressed(true);
        }

        $header
            ->setName($this->name)
            ->setHash($this->getHash())
        ;

        return $header->pack() . $this->data;
    }

    /**
     *  will unpack binary string and return new instance of self with properties set
     *
     * @param   string $data
     * @return  DataContainer
     * @throws  TransformerException
     */
    static function unpack($data)
    {
        $header = DataHeader::unpack($data);
        $data = substr($data, $header->getSize());
        $hash = hash('crc32b', $data, true);

        if ($hash !== $header->getHash()) {
            throw TransformerException::crcMismatch($hash, $header->getHash());
        }

        if ($header->isCompressed()) {
            $data = gzuncompress($data);
        }

        if ($header->isSerialized()) {
            $data = unserialize($data);
        }


        switch ($header->getType()) {
            case $header::T_BOOLEAN:
                $data = (bool) $data;
                break;
            case $header::T_INTEGER:
                $data = (int) $data;
                break;
            case $header::T_DOUBLE:
                $data = (double) $data;
                break;
            case $header::T_STRING:
                $data = (string) $data;
                break;
        }

        return new DataContainer($data, $header);
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param  mixed $name
     * @return $this;
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param  mixed $data
     * @return $this;
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    public function getHash()
    {
        return hash('crc32b', $this->data, true);
    }

    /**
     * @return bool
     */
    public function isCompressed()
    {
        return $this->compressed;
    }

    /**
     * @param  bool $compressed
     * @return $this;
     */
    public function setCompressed($compressed)
    {
        $this->compressed = $compressed;
        return $this;
    }

}