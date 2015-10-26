<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Response;

class QuitResponse extends AbstractResponse
{
    /**
     * @inheritdoc
     */
    function __construct($response = null, $data = null)
    {
        parent::__construct($response, $data);
    }


    /**
     * @return bool
     */
    public function isSuccess()
    {
        return true;
    }
}