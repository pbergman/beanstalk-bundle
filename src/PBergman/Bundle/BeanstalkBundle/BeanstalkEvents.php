<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle;

/**
 * Class BeanstalkEvents
 *
 * @package namespace PBergman\Bundle\BeanstalkBundle
 */
final class BeanstalkEvents
{
    /**  This class is not meant to be instantiated. */
    private function __construct() {}

    const PRE_DISPATCH_PUT                    = 'beanstalk.pre.dispatch.put';
    const PRE_DISPATCH_USE                    = 'beanstalk.pre.dispatch.use';
    const PRE_DISPATCH_RESERVE                = 'beanstalk.pre.dispatch.reserve';
    const PRE_DISPATCH_RESERVE_WITH_TIMEOUT   = 'beanstalk.pre.dispatch.reserve.with.timeout';
    const PRE_DISPATCH_DELETE                 = 'beanstalk.pre.dispatch.delete';
    const PRE_DISPATCH_RELEASE                = 'beanstalk.pre.dispatch.release';
    const PRE_DISPATCH_BURY                   = 'beanstalk.pre.dispatch.bury';
    const PRE_DISPATCH_TOUCH                  = 'beanstalk.pre.dispatch.touch';
    const PRE_DISPATCH_WATCH                  = 'beanstalk.pre.dispatch.watch';
    const PRE_DISPATCH_IGNORE                 = 'beanstalk.pre.dispatch.ignore';
    const PRE_DISPATCH_PEEK                   = 'beanstalk.pre.dispatch.peek';
    const PRE_DISPATCH_PEEK_READY             = 'beanstalk.pre.dispatch.peek.ready';
    const PRE_DISPATCH_PEEK_DELAYED           = 'beanstalk.pre.dispatch.peek.delayed';
    const PRE_DISPATCH_PEEK_BURIED            = 'beanstalk.pre.dispatch.peek.buried';
    const PRE_DISPATCH_KICK                   = 'beanstalk.pre.dispatch.kick';
    const PRE_DISPATCH_KICK_JOB               = 'beanstalk.pre.dispatch.kick.job';
    const PRE_DISPATCH_STATS_JOB              = 'beanstalk.pre.dispatch.stats.job';
    const PRE_DISPATCH_STATS_TUBE             = 'beanstalk.pre.dispatch.stats.tube';
    const PRE_DISPATCH_STATS                  = 'beanstalk.pre.dispatch.stats';
    const PRE_DISPATCH_LIST_TUBES             = 'beanstalk.pre.dispatch.list.tubes';
    const PRE_DISPATCH_LIST_TUBE_USED         = 'beanstalk.pre.dispatch.list.tube.used';
    const PRE_DISPATCH_LIST_TUBES_WATCHED     = 'beanstalk.pre.dispatch.list.tubes.watched';
    const PRE_DISPATCH_QUIT                   = 'beanstalk.pre.dispatch.quit';
    const PRE_DISPATCH_PAUSE_TUBE             = 'beanstalk.pre.dispatch.pause.tube';
    
    const POST_DISPATCH_PUT                   = 'beanstalk.post.dispatch.put';
    const POST_DISPATCH_USE                   = 'beanstalk.post.dispatch.use';
    const POST_DISPATCH_RESERVE               = 'beanstalk.post.dispatch.reserve';
    const POST_DISPATCH_RESERVE_WITH_TIMEOUT  = 'beanstalk.post.dispatch.reserve.with.timeout';
    const POST_DISPATCH_DELETE                = 'beanstalk.post.dispatch.delete';
    const POST_DISPATCH_RELEASE               = 'beanstalk.post.dispatch.release';
    const POST_DISPATCH_BURY                  = 'beanstalk.post.dispatch.bury';
    const POST_DISPATCH_TOUCH                 = 'beanstalk.post.dispatch.touch';
    const POST_DISPATCH_WATCH                 = 'beanstalk.post.dispatch.watch';
    const POST_DISPATCH_IGNORE                = 'beanstalk.post.dispatch.ignore';
    const POST_DISPATCH_PEEK                  = 'beanstalk.post.dispatch.peek';
    const POST_DISPATCH_PEEK_READY            = 'beanstalk.post.dispatch.peek.ready';
    const POST_DISPATCH_PEEK_DELAYED          = 'beanstalk.post.dispatch.peek.delayed';
    const POST_DISPATCH_PEEK_BURIED           = 'beanstalk.post.dispatch.peek.buried';
    const POST_DISPATCH_KICK                  = 'beanstalk.post.dispatch.kick';
    const POST_DISPATCH_KICK_JOB              = 'beanstalk.post.dispatch.kick.job';
    const POST_DISPATCH_STATS_JOB             = 'beanstalk.post.dispatch.stats.job';
    const POST_DISPATCH_STATS_TUBE            = 'beanstalk.post.dispatch.stats.tube';
    const POST_DISPATCH_STATS                 = 'beanstalk.post.dispatch.stats';
    const POST_DISPATCH_LIST_TUBES            = 'beanstalk.post.dispatch.list.tubes';
    const POST_DISPATCH_LIST_TUBE_USED        = 'beanstalk.post.dispatch.list.tube.used';
    const POST_DISPATCH_LIST_TUBES_WATCHED    = 'beanstalk.post.dispatch.list.tubes.watched';
    const POST_DISPATCH_QUIT                  = 'beanstalk.post.dispatch.quit';
    const POST_DISPATCH_PAUSE_TUBE            = 'beanstalk.post.dispatch.pause.tube';
    
    const STREAM_WRITE                        = 'beanstalk.stream.write';
    const STREAM_READ                         = 'beanstalk.stream.read';
}
