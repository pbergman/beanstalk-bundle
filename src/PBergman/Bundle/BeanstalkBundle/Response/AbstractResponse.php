<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Response;

abstract class AbstractResponse implements ResponseInterface
{
    /** @var string  */
    protected $response;
    /** @var mixed */
    protected $data;

    /**
     * @inheritdoc
     */
    function __construct($response, $data = null)
    {
        $this->data = $data;
        $this->response = $response;
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @inheritdoc
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return bool
     */
    abstract public function isSuccess();

}