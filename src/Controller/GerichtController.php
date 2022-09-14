<?php

namespace App\Controller;

use App\Entity\Gericht;
use App\Repository\GerichtRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/gericht', name: 'app_gericht.')]
class GerichtController extends AbstractController
{
    #[Route('/', name: 'bearbeiten')]
    public function index(GerichtRepository $gr): Response{
        $gerichte = $gr->findAll();
        
        return $this->render('gericht/index.html.twig', [
            'gerichte' => $gerichte
        ]);
    }
    
    #[Route('/anlegen', name: 'anlegen')]
    public function anlegen(ManagerRegistry $doctrine): Response{
        $gericht = new Gericht();
        $gericht->setName("Pizza");

        //EntityManager
        $em = $doctrine->getManager();
        $em->persist($gericht);
        $em->flush();

        //Response
        return new Response("Gericht wurde angelegt");
    }
}

