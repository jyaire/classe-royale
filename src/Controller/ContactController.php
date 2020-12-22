<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact")
     */
    public function index(Request $request, MailerInterface $mailer)
    {
        $form = $this->createForm(ContactType::class);

        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()) {

            $contactFormData = $form->getData();
            
            $message = (new TemplatedEmail())
                ->from($contactFormData['email'])
                ->to('jyaire@gmail.com')
                ->subject('Classe Royale : ' .$contactFormData['subject'])
                ->htmlTemplate('contact/mail.html.twig')
                ->context([
                    'contactFormData' => $contactFormData,
                ])
                ;
            $mailer->send($message);

            $this->addFlash('success', 'Votre message a été envoyé, nous vous répondrons rapidement');

            return $this->redirectToRoute('home');
        }



        return $this->render('contact/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
    
}
