<?php

namespace App\EventSubscriber;

use App\Service\Blog\BlogPostService;
use Pimcore\Event\DataObjectEvents;
use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\Model\DataObject\BlogPost;
use Pimcore\Model\DataObject\BlogPostCategory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AdminSubscriber  implements EventSubscriberInterface
{

    public function __construct(private BlogPostService  $postService)
    {

    }

    public static function getSubscribedEvents(): array
    {
        return [
            // when object is saved
            DataObjectEvents::PRE_UPDATE => [
                ['setBlogSettings', 9],
            ],
        ];
    }

    public function setBlogSettings(DataObjectEvent $events): void
    {
        $object = $events->getObject();
        if (!($object instanceof BlogPost || $object instanceof BlogPostCategory)){
            return;
        }
        $this->postService->setBlogSettings($object);
    }
}
