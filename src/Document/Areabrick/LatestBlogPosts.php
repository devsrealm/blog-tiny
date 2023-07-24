<?php

namespace App\Document\Areabrick;

use App\Helper;
use Pimcore\Model\DataObject\BlogPost;
use Pimcore\Model\Document\Editable\Area\Info;
use Symfony\Component\HttpFoundation\Response;

class LatestBlogPosts extends AbstractAreabrick
{

    public function getName(): string
    {
        return 'Latest Blog Posts';
    }

    /**
     * @throws \Exception
     */
    public function action(Info $info): ?Response
    {
        $info->setParams([
            'search' => $info->getRequest()->get('search', ''),
            'pagination' => Helper::GetBlogPosts($info->getRequest()),
        ]);
        return null;
    }

}
