<?php

namespace App\Controller;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerController extends AbstractController
{
    #[Route('/mail', name: 'app_mail')]
    public function sendEmail(MailerInterface $mailer, Request $request): Response
    {

        $emailForm = $this->createFormBuilder()
            ->add('nachricht', TextareaType::class,[
                'attr' => array('rows' => '5')
            ])
            ->add('abschicken', SubmitType::class)

            ->getForm();

        $emailForm->handleRequest($request);

        if($emailForm->isSubmitted()){
            $eingabe = $emailForm->getData();
            $text = ($eingabe['nachricht']);
            $tisch ='tisch1';
            $email = (new TemplatedEmail())
                ->from('tisch1@menukarte.wip')
                ->to('kellner@menukarte.wip')
                ->subject('Bestellung')
                
                ->htmlTemplate('mailer/mail.html.twig')

                ->context([
                    'tisch' => $tisch,
                    'text'  => $text
                ]);
            
            $mailer->send($email);
            $this->addFlash('nachricht','Nachricht wurde versendet!');
            return $this->redirect($this->generateUrl('app_mail'));
        }
        
        return $this->render('mailer/index.html.twig',[
            'emailForm' => $emailForm->createView()
        ]);
    }
}
