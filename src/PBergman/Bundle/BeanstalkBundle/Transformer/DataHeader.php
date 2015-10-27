<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Transformer;

/**
 * Class DataHeader
 *
 * @package PBergman\Bundle\BeanstalkBundle\Transformer
 */
class DataHeader implements PackInterface
{
    const SIGNATURE = 0x4245414e;

    const T_BOOLEAN = 1;
    const T_INTEGER = 2;
    const T_DOUBLE  = 4;
    const T_STRING  = 8;
    const T_ARRAY   = 16;
    const T_OBJECT  = 32;
    const T_NULL    = 64;

    /** @var int */
    protected $type;
    /** @var string  */
    protected $name;
    /** @var int */
    protected $serialized;
    /** @var int */
    protected $compressed;
    /** @var int */
    protected $pid;
    /** @var string */
    protected $hash;
    /** @var int */
    protected $size;

    /**
     * @inheritdoc
     */
    function __construct()
    {
        $this->pid = posix_getpid();
    }


    /**
     * will return a  binary string
     *
     * @return string
     */
    public function pack()
    {
        return pack(
            'SLA4SCCCA*',
            15 + strlen($this->name),
            self::SIGNATURE,
            $this->hash,
            $this->pid,
            $this->compressed,
            $this->serialized,
            $this->type,
            $this->name
        );
    }

    /**
     * will unpack binary string and return new instance of self with properties set
     *
     * @param   string  $data
     * @return  self
     */
    static function unpack($data)
    {
        $info  = unpack('Slength', substr($data, 0, 2));
        $info += unpack('Lsignature/A4hash/Spid/Cserialized/Ccompressed/Ctype/A*name', substr($data, 2, $info['length'] - 2));

        if (self::SIGNATURE !== $info['signature']) {
            throw new \RuntimeException(sprintf('Signature mismatch 0x%x !== 0x%x', $info['signature'], self::SIGNATURE));
        }

        return (new self())
            ->setSize($info['length'])
            ->setHash($info['hash'])
            ->setPid($info['pid'])
            ->setType($info['type'])
            ->setCompressed($info['serialized'])
            ->setSerialized($info['compressed'])
            ->setName($info['name'])
            ;
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
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param  mixed $hash
     * @return $this;
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
        return $this;
    }


    /**
     * @return int|null
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * @param  mixed $pid
     * @return $this;
     */
    public function setPid($pid)
    {
        $this->pid = $pid;
        return $this;
    }


    /**
     * @return bool
     */
    public function isCompressed()
    {
        return (bool) $this->compressed;
    }

    /**
     * @param  bool $compressed
     * @return $this;
     */
    public function setCompressed($compressed)
    {
        $this->compressed = (int) $compressed;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSerialized()
    {
        return (bool) $this->serialized;
    }

    /**
     * @param  bool $serialized
     * @return $this;
     */
    public function setSerialized($serialized)
    {
        $this->serialized = (int) $serialized;
        return $this;
    }

    /**
     * @param  int  $size
     * @return $this;
     */
    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param  int $type
     * @return $this;
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }
}