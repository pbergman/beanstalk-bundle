<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Response;

class ReserveResponse extends AbstractResponse
{
    protected $id;

    /**
     * @inheritdoc
     */
    function __construct($response, $id = null, $data = null)
    {
        parent::__construct($response, $data);
        $this->id = $id;
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return self::RESPONSE_RESERVED === $this->response;
    }

    /**
     * @return null
     */
    public function getId()
    {
        return (int) $this->id;
    }
}