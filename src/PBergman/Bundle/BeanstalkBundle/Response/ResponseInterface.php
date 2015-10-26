<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Response;

interface ResponseInterface
{
    const RESPONSE_NOT_IGNORED   = 'NOT_IGNORED';
    const RESPONSE_NOT_FOUND     = 'NOT_FOUND';
    const RESPONSE_OK            = 'OK';
    const RESPONSE_FOUND         = 'FOUND';
    const RESPONSE_PAUSED        = 'PAUSED';
    const RESPONSE_KICKED        = 'KICKED';
    const RESPONSE_USING         = 'USING';
    const RESPONSE_INSERTED      = 'INSERTED';
    const RESPONSE_BURIED        = 'BURIED';
    const RESPONSE_EXPECTED_CRLF = 'EXPECTED_CRLF';
    const RESPONSE_JOB_TOO_BIG   = 'JOB_TOO_BIG';
    const RESPONSE_DRAINING      = 'DRAINING';
    const RESPONSE_DEADLINE_SOON = 'DEADLINE_SOON';
    const RESPONSE_TIMED_OUT     = 'TIMED_OUT';
    const RESPONSE_RESERVED      = 'RESERVED';
    const RESPONSE_DELETED       = 'DELETED';
    const RESPONSE_RELEASED      = 'RELEASED';
    const RESPONSE_TOUCHED       = 'TOUCHED';
    const RESPONSE_WATCHING      = 'WATCHING';

    /**
     * should return the data on success, error message
     * on failure or null if none was returned on success
     *
     * @return mixed
     */
    public function getData();

    /**
     * should return the response given by Beanstalk
     *
     * @return string
     */
    public function getResponse();

    /**
     * will return true if dispatch was a success
     *
     * @return bool
     */
    public function isSuccess();
}