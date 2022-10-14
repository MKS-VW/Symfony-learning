<?php

namespace App\Controller;

use App\Repository\GerichtRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EloController extends AbstractController
{
    #[Route('/elo', name: 'app_elo')]
    public function menu(GerichtRepository $gr): Response
    {
        $gerichte = $gr->findAll();

        $zufall = array_rand($gerichte, 2);

        return $this->render('elo/index.html.twig', [
            'gericht1' => $gerichte[$zufall[0]],
            'gericht2' => $gerichte[$zufall[1]],
        ]);
    }

    #[Route('/vote/{id1}/{id2}', name: 'vote')]
    public function vote($id1, $id2, GerichtRepository $gr, ManagerRegistry $doctrine){
        
        $em = $doctrine->getManager();
        $gericht1 = $gr->find($id1);
        $gericht2 = $gr->find($id2);
        if (!$gericht1) {
            throw $this->createNotFoundException(
                'Teller Nr. '.$id1.' leer'
            );
        }
        
        $elo = $this->eloRechner($gericht1->getElo(),$gericht2->getElo(),1,1);

        $gericht1->setElo($elo[0]);
        $gericht2->setElo($elo[1]);

        $em->flush();

        $this->addFlash('erfolg','Gewinner ausgewählt');
        
        return $this->redirect($this->generateUrl('app_elo'));
        // $gerichte = $gr->findAll();

        // $zufall = array_rand($gerichte, 2);

        // return $this->render('elo/index.html.twig', [
        //     'gericht1' => $gerichte[$zufall[0]],
        //     'gericht2' => $gerichte[$zufall[1]],
        // ]);
    }
    
    public function eloRechner($elo1, $elo2, $duelle1, $duelle2){
        
        $k1 = $duelle1>6 ? 80 : 160;
        $k2 = $duelle2>6 ? 80 : 160;
        
        $chancen1 = 1/(1+pow(10,($elo2-$elo1)/400));
        $chancen2 = 1/(1+pow(10,($elo1-$elo2)/400));

        $eloNeu1 = round($elo1+$k1*(1-$chancen1),0);
        $eloNeu2 = round($elo2+$k2*(0-$chancen2),0);

        $eloNeu = array($eloNeu1, $eloNeu2);

        return $eloNeu;
    }
}
