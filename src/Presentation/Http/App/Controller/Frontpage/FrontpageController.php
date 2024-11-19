<?php

declare(strict_types=1);

namespace App\Presentation\Http\App\Controller\Frontpage;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/', name: 'app_frontpage')]
class FrontpageController extends AbstractController
{
    public function __invoke(): Response
    {
        return $this->render('app/page/frontpage/page.html.twig');
    }
}
