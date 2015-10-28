<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Response;

use PBergman\Bundle\BeanstalkBundle\Exception\InvalidArgumentException;

abstract class AbstractArrayResponse extends \ArrayObject implements ResponseInterface
{
    /** @var string  */
    protected $response;

    /**
     * @inheritdoc
     */
    function __construct($response, array $data)
    {
        parent::__construct($data);
        $this->response = $response;
    }

    /**
     * @inheritdoc
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        return $this->getArrayCopy();
    }

    /**
     * @inheritdoc
     */
    public function setData($data)
    {
        $this->exchangeArray($data);
        return $this;
    }

    /**
     * @inheritdoc
     */
    function __call($name, $args = array())
    {
        if (preg_match('/^get(?P<method>\w+)$/', $name, $m)) {
            $key = $this->camelToLowerDash($m['method']);
            if (array_key_exists($key, $this)) {
                return $this[$key];
            }
        }

        throw new InvalidArgumentException(sprintf('Call to undefined method %s::%s()', __CLASS__, $name));
    }

    /**
     * Convert camelcase to dash string, for example CmdPut becomes cmd-put
     *
     * @param   $string
     * @return  mixed
     */
    protected function camelToLowerDash($string)
    {
        return
            preg_replace_callback(
                '/(^|[a-z])([A-Z])/',
                function($m){
                    return strtolower(strlen($m[1]) ? sprintf('%s-%s', $m[1], $m[2]) : $m[2]);
                },
                $string
            );
    }
}