<?php

namespace App\Controller;

use App\Entity\Card;
use App\Entity\Subject;
use App\Entity\Student;
use App\Entity\Point;
use App\Entity\Reason;
use App\Form\CardType;
use App\Repository\CardRepository;
use App\Repository\ReasonRepository;
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
     * @Route("/student/{student}", name="card_index_student", methods={"GET"})
     * @param CardRepository $cardRepository
     * @param Student $student
     * @return Response
     */
    public function indexStudent(Student $student, CardRepository $cardRepository): Response
    {
        return $this->render('card/index.html.twig', [
            'cards' => $cardRepository->findAll(),
            'student' => $student,
        ]);
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

    /**
     * @Route("/edit/{id}", name="card_edit", methods={"GET","POST"})
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
     * @Route("/{id}/{student}", name="card_show", methods={"GET"}, defaults={"student": null}))
     * @param Card $card
     * @param ?Student $student
     * @return Response
     */
    public function show(Card $card, ?Student $student): Response
    {
        return $this->render('card/show.html.twig', [
            'card' => $card,
            'student' => $student,
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
     * @Route("/win/{card}/{student}/{remove}", name="card_win", methods={"GET"}, defaults={"remove"=null})
     * @param Card $card
     * @param Student $student
     * @param ReasonRepository $reasonRepository
     * @param ?string $remove
     * @return Response
     */
    public function win(Card $card, Student $student, ReasonRepository $reasonRepository, ?string $remove): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $point = new Point;

        if ($remove == "remove") {
            $point->setQuantity(-$card->getLevel());
            $action = "Perte";
            $student->removeCard($card);
            $message = 'La carte "' . $card->getName() . '" a été retirée à ' . $student->getFirstname();
        } else {
            $point->setQuantity($card->getLevel());
            $action = "Gain";
            $student->addCard($card);
            $message = $student->getFirstname() . ' a gagné la carte "' . $card->getName() . '"';
        }
            
        if($card->getType()=="apprentissage") {
            $type = "gold";
            $sentence = $action . ' de la carte d\'apprentissage "' . $card->getName() . '" - Niveau ' . $card->getLevel();
        } else {
            $type = "elixir";
            $sentence = $action . ' de la carte de comportement "' . $card->getName() . '" - Niveau ' . $card->getLevel();
        }
        // search if reason already exists
        $search = $reasonRepository->findOneBy(['sentence'=>$sentence]);
        if(!empty($search)) {
            $point->setReason($search);
        } else {
            $reason = new Reason;
            $reason->setSentence($sentence);
            $entityManager->persist($reason);
            $point->setReason($reason);
        }
        $point
            ->setStudent($student)
            ->setType($type)
            ->setDate(new \DateTime())
            ->setAuthor($this->getUser());
        $entityManager->persist($point);

        switch($type) {
            case "gold":
                $win = $student->getGold() + $point->getQuantity();
                $student->setGold($win);
                break;
            case "elixir":
                $win = $student->getElixir() + $point->getQuantity();
                $student->setElixir($win);
                break;
        }

        if($remove == "remove") {
            $student->setXp($student->getXp()-5);
        } else {
           $student->setXp($student->getXp()+5); 
        }

        $entityManager->persist($student);
        $entityManager->flush();

        $this->addFlash('success', $message);

        return $this->redirectToRoute('card_index_student', [
            'student' => $student->getId(),
        ]);
    }
}
