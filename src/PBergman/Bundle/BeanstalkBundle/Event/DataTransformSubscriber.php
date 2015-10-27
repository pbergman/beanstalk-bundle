<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Event;

use PBergman\Bundle\BeanstalkBundle\Transformer\DataTransformer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use PBergman\Bundle\BeanstalkBundle\BeanstalkEvents;

class DataTransformSubscriber implements EventSubscriberInterface
{

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            BeanstalkEvents::PRE_DISPATCH_PUT => 'preDispatch',
            BeanstalkEvents::POST_DISPATCH_PEEK => 'postDispatch',
            BeanstalkEvents::POST_DISPATCH_PEEK_BURIED => 'postDispatch',
            BeanstalkEvents::POST_DISPATCH_PEEK_DELAYED => 'postDispatch',
            BeanstalkEvents::POST_DISPATCH_PEEK_READY => 'postDispatch',
            BeanstalkEvents::POST_DISPATCH_RESERVE => 'postDispatch',
            BeanstalkEvents::POST_DISPATCH_RESERVE_WITH_TIMEOUT => 'postDispatch',
        ];
    }

    public function preDispatch(PreDispatchEvent $event)
    {
        $payload = $event->getPayload();
        $payload[0] = (new DataTransformer($payload[4], $payload[0]))->pack();
    }

    public function postDispatch(PostDispatchEvent $event)
    {
        if ($event->getResponse()->isSuccess()) {
            $event->getResponse()->setData(
                DataTransformer::unpack($event->getResponse()->getData())
            );
        }
    }
}