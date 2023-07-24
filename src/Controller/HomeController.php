<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends FrontendController
{
    /**
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function indexAction(Request $request): Response
    {
        return $this->render('layout/main.html.twig');
    }

}
