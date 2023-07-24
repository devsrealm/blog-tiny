<?php

namespace App\Controller;

use App\Helper;
use Knp\Component\Pager\PaginatorInterface;
use Pimcore\Controller\FrontendController;
use Pimcore\Model\DataObject\BlogPost;
use Pimcore\Model\DataObject\Data\UrlSlug;
use Pimcore\Model\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use \Pimcore\Model\DataObject;

class BlogCategoryController extends FrontendController
{
    /**
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request): Response
    {
        return $this->render('blog/index.html.twig');
    }

    /**
     * @param Request $request
     * @param $slug
     * @param PaginatorInterface $paginator
     * @return Response
     * @throws \Exception
     */
    #[Route('/category/{slug}')]
    public function singleAction(Request $request, $slug, PaginatorInterface $paginator): Response
    {
        $blogPostCategory =  DataObject\BlogPostCategory::getBySlug($slug)?->current();

        $posts = Helper::GetBlogPosts($request, function (BlogPost\Listing $posts) use ($blogPostCategory) {
            $posts->addConditionParam('Category__id = ' . $blogPostCategory->getId());
        });

        if (($blogPostCategory instanceof DataObject\BlogPostCategory && $blogPostCategory->isPublished()) === false){
            # Post Not Found, Return 404 or Just Redirect To HomePage For Now
            return $this->redirect('/');
        }

        return $this->render('blog/category.html.twig',
            [
                'search' => $request->get('search', ''),
                'data' => $blogPostCategory,
                'pagination' => $posts,
                'category' => $blogPostCategory,
            ]);
    }

}
