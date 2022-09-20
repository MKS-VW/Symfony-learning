<?php

namespace App\Controller;

use App\Form\GerichtType;
use App\Entity\Gericht;
use App\Repository\GerichtRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

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
    public function anlegen(Request $request, ManagerRegistry $doctrine): Response{
        
        $gericht = new Gericht();
        //Formular
        $form = $this->createForm(GerichtType::class, $gericht);
        $form->handleRequest($request);
        
        if ($form->isSubmitted()){
            //EntityManager
            $em = $doctrine->getManager();
            $bild = $form->get('anhang')->getData();
            // $bild = $request->files->get('gericht')['anhang'];

            if($bild){
                $dateiname = md5(uniqid()).'.'.$bild->guessClientExtension();
                
            }
            
            $bild->move(
                $this->getParameter('bilder_ordner'),
                $dateiname
            );

            $gericht->setBild($dateiname);
            $em->persist($gericht);
            $em->flush();
            return $this->redirect($this->generateUrl('app_gericht.bearbeiten'));  //kehrt zurück
        }

        return $this->render('gericht/anlegen.html.twig', [
            'anlegenForm' => $form->createView()
        ]);
    }
    
    #[Route('/entfernen/{id}', name: 'entfernen')]
    public function entfernen($id, GerichtRepository $gr, ManagerRegistry $doctrine){
        
        $em = $doctrine->getManager();
        $gericht = $gr->find($id);
        $em->remove($gericht);
        $em->flush();

        //message
        $this->addFlash('erfolg','Gericht wurde erfolgreich entfernt');

        return $this->redirect($this->generateUrl('app_gericht.bearbeiten'));  //kehrt zurück
    }

    #[Route('/anzeigen/{id}', name: 'anzeigen')]
    public function anzeigen(Gericht $gericht){
        // dump($gericht);

        return $this->render('gericht/anzeigen.html.twig', [
            'gericht' => $gericht
        ]);
    }
}

