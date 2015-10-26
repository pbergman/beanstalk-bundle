<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Response;

class PeekResponse extends AbstractResponse
{
    /** @var int */
    protected $id;

    /**
     * @param string    $response
     * @param mixed     $data
     * @param int       $id
     */
    function __construct($response, $data = null, $id = null)
    {
        parent::__construct($response, $data);
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return (int) $this->id;
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return self::RESPONSE_FOUND === $this->response;
    }
}