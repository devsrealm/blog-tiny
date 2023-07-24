<?php

namespace App\Service\Blog;

use App\Helper;
use Pimcore\Model\DataObject\BlogPost;
use Pimcore\Model\DataObject\BlogPostCategory;
use Symfony\Component\String\Slugger\AsciiSlugger;

class BlogPostService
{

    public function setBlogSettings(BlogPost|BlogPostCategory $post)
    {
        if (empty(trim($post->getSeoTitle()))){
            $post->setSeoTitle($post->getTitle());
        }

        if (empty(trim($post->getShortDescription()))){
            $post->setShortDescription(Helper::GenerateExcerpt($post->getContent()));
        }

        if (empty(trim($post->getSeoDescription()))){
            $post->setSeoDescription($post->getShortDescription());
        }

        $slugger = new AsciiSlugger();
        if (empty($post?->getSlug())){
            $post->setSlug($slugger->slug($post->getTitle(), '-')->lower());
        } else {
            # Fix Slug in case user gets it wrong
            $post->setSlug($slugger->slug($post->getSlug(), '-')->lower());
        }

    }
}
