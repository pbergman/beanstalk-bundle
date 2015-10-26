<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Response;

class StatsTubeResponse extends AbstractArrayResponse
{
    const KEY_NAME                     = 'name';
    const KEY_CURRENT_JOBS_URGENT      = 'current-jobs-urgent';
    const KEY_CURRENT_JOBS_READY       = 'current-jobs-ready';
    const KEY_CURRENT_JOBS_RESERVED    = 'current-jobs-reserved';
    const KEY_CURRENT_JOBS_DELAYED     = 'current-jobs-delayed';
    const KEY_CURRENT_JOBS_BURIED      = 'current-jobs-buried';
    const KEY_TOTAL_JOBS               = 'total-jobs';
    const KEY_CURRENT_USING            = 'current-using';
    const KEY_CURRENT_WAITING          = 'current-waiting';
    const KEY_CURRENT_WATCHING         = 'current-watching';
    const KEY_PAUSE                    = 'pause';
    const KEY_CMD_DELETE               = 'cmd-delete';
    const KEY_CMD_PAUSE_TUBE           = 'cmd-pause-tube';
    const KEY_PAUSE_TIME_LEFT          = 'pause-time-left';

    /**
     * will return true if dispatch was a success
     *
     * @return bool
     */
    public function isSuccess()
    {
        return self::RESPONSE_OK === $this->response;
    }
}