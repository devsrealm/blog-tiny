<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Pimcore\Model\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use \Pimcore\Model\DataObject;
use Symfony\Component\Routing\Annotation\Route;
use Pimcore\Model\DataObject\Data\UrlSlug;

class BlogController extends FrontendController
{

    /**
     * @param Request $request
     * @param $slug
     * @return Response
     */
    #[Route('/post/{slug}')]
    public function singleAction(Request $request, $slug): Response
    {
        $blogPost =  DataObject\BlogPost::getBySlug($slug)?->current();
        if (($blogPost instanceof DataObject\BlogPost && $blogPost->isPublished()) === false){
            # Post Not Found, Return 404 or Just Redirect To HomePage For Now
            return $this->redirect('/');
        }

        return $this->render('blog/single.html.twig',
            [
            'data' => $blogPost,
            'posted_by' => User::getById($blogPost->getPostedBy())->getName()
            ]);
    }

}
