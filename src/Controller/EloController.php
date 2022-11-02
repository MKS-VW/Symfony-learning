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
    public function menu(GerichtRepository $gr)
    {
        $gerichte = $gr->findAll();

        $zufall = array_rand($gerichte, 2);

        return $this->render('elo/index.html.twig', [
            'gericht1' => $gerichte[$zufall[0]],
            'gericht2' => $gerichte[$zufall[1]],
        ]);
    }

    #[Route('/vote/{id1}/{id2}', name: 'vote')]
    public function vote($id1, $id2, GerichtRepository $gr, ManagerRegistry $doctrine): Response{
        
        $em = $doctrine->getManager();
        $gericht1 = $gr->find($id1);
        $gericht2 = $gr->find($id2);
        if (!$gericht1) {
            throw $this->createNotFoundException(
                'Teller Nr. '.$id1.' leer'
            );
        }
        
        // Parameter 3,4 für Anzahl Wertungen (Zähler nicht implementiert)
        $elo = $this->eloRechner($gericht1->getElo(),$gericht2->getElo(),1,1);

        // Werte für Message
        $old1=$gericht1->getElo();
        $bez1=$gericht1->getName();
        $old2=$gericht2->getElo();
        $bez2=$gericht2->getName();
        $diff=$elo[0]-$old1;

        // Neuer Wert nach Bewertung
        $gericht1->setElo($elo[0]);
        $gericht2->setElo($elo[1]);

        $em->flush();

        $this->addFlash('erfolg',"$bez1: $old1->$elo[0] - $bez2: $old2->$elo[1] - Differenz: $diff"); 
        
        return $this->redirect($this->generateUrl('app_elo'));
    }
    
    public function eloRechner($elo1, $elo2, $duelle1, $duelle2){
        // Konstanten, die sich nach einer gewissen Anzahl(6) Wertungen verändern
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
