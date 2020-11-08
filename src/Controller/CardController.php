<?php

namespace App\Controller;

use App\Entity\Card;
use App\Entity\Subject;
use App\Form\CardType;
use App\Repository\CardRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/card")
 */
class CardController extends AbstractController
{
    /**
     * @Route("/", name="card_index", methods={"GET"})
     * @param CardRepository $cardRepository
     * @return Response
     */
    public function index(CardRepository $cardRepository): Response
    {
        return $this->render('card/index.html.twig', [
            'cards' => $cardRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="card_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $card = new Card();
        $form = $this->createForm(CardType::class, $card);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            if (!empty($form['image']->getData())) {
                $directory = "cards/";
                $file = $form['image']->getData();
                $extension = $file->guessExtension();
                if ($extension == 'png' or $extension == 'jpg' or $extension == 'jpeg') {
                    $fileName = $card->getName() . '.' . $extension;
                    $file->move($directory, $fileName);
                    $card->setImage($fileName);
                } else {
                    $this->addFlash('danger', 'Votre image doit être au format jpg, jpeg ou png');
                    return $this->redirectToRoute('card_new');
                }
            }
            $entityManager->persist($card);
            $entityManager->flush();

            $this->addFlash('success', 'Carte ajoutée');
            return $this->redirectToRoute('card_index');
        }

        return $this->render('card/new.html.twig', [
            'card' => $card,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="card_show", methods={"GET"})
     * @param Card $card
     * @return Response
     */
    public function show(Card $card): Response
    {
        return $this->render('card/show.html.twig', [
            'card' => $card,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="card_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Card $card
     * @return Response
     */
    public function edit(Request $request, Card $card): Response
    {
        $form = $this->createForm(CardType::class, $card);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            if (!empty($form['image']->getData())) {
                $directory = "cards/";
                $file = $form['image']->getData();
                $extension = $file->guessExtension();
                if ($extension == 'png' or $extension == 'jpg' or $extension == 'jpeg') {
                    $fileName = $card->getName() . '.' . $extension;
                    $file->move($directory, $fileName);
                    $card->setImage($fileName);
                } else {
                    $this->addFlash('danger', 'Votre image doit être au format jpg, jpeg ou png');
                    return $this->redirectToRoute('card_edit', ['id'=>$card->getId()]);
                }
                $entityManager->persist($card);
            }
            $entityManager->flush();

            $this->addFlash('success', 'Carte modifiée');
            return $this->redirectToRoute('card_index');
        }

        return $this->render('card/edit.html.twig', [
            'card' => $card,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="card_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Card $card): Response
    {
        if ($this->isCsrfTokenValid('delete'.$card->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($card);
            $entityManager->flush();
        }

        return $this->redirectToRoute('card_index');
    }

     /**
     * @Route("/type/{type}", name="card_index_type", methods={"GET"})
     * @param CardRepository $cardRepository
     * @param string $type
     * @return Response
     */
    public function indexType(string $type, CardRepository $cardRepository): Response
    {
        return $this->render('card/index.html.twig', [
            'cards' => $cardRepository->findBy(['type'=>$type]),
            'type' => $type,
        ]);
    }

    /**
     * @Route("/subject/{id}", name="card_index_subject", methods={"GET"})
     * @param CardRepository $cardRepository
     * @param Subject $subject
     * @return Response
     */
    public function indexSubject(Subject $subject, CardRepository $cardRepository): Response
    {
        return $this->render('card/index.html.twig', [
            'cards' => $cardRepository->findBy(['subject'=>$subject]),
            'subject' => $subject,
        ]);
    }
}
