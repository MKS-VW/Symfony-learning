<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GerichtController extends AbstractController
{
    #[Route('/gericht', name: 'app_gericht')]
    public function index(): Response
    {
        return $this->render('gericht/index.html.twig', [
            'controller_name' => 'GerichtController',
        ]);
    }
}
