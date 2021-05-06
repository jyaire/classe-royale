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
use App\Repository\SubjectRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/card")
 * @IsGranted("ROLE_USER")
 */
class CardController extends AbstractController
{
    /**
     * @Route("/", name="card_index", methods={"GET"})
     * @param CardRepository $cardRepository
     * @param SubjectRepository $subjectRepository
     * @return Response
     */
    public function index(CardRepository $cardRepository, SubjectRepository $subjectRepository): Response
    {
        return $this->render('card/index.html.twig', [
            'cards' => $cardRepository->findAll(),
            'subjects' => $subjectRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="card_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
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
     * @param Student $student
     * @param CardRepository $cardRepository
     * @param SubjectRepository $subjectRepository
     * @return Response
     */
    public function indexStudent(Student $student, CardRepository $cardRepository, SubjectRepository $subjectRepository): Response
    {
        return $this->render('card/index.html.twig', [
            'student' => $student,
            'cards' => $cardRepository->findAll(),
            'subjects' => $subjectRepository->findAll(),
        ]);
    }

    /**
     * @Route("/type/{type}/{student}", name="card_index_type", methods={"GET"}, defaults={"student": null})
     * @param CardRepository $cardRepository
     * @param ?Student $student
     * @param string $type
     * @return Response
     */
    public function indexType(string $type, CardRepository $cardRepository, ?Student $student): Response
    {
        return $this->render('card/type.html.twig', [
            'cards' => $cardRepository->findBy(['type'=>$type]),
            'type' => $type,
            'student' => $student,
        ]);
    }

    /**
     * @Route("/subject/{id}", name="card_index_subject", methods={"GET"})
     * @param CardRepository $cardRepository
     * @param Subject $subject
     * @return Response
     */
    public function indexSubject(Subject $subject): Response
    {
        $subjects=[];
        array_push($subjects, $subject);
        return $this->render('card/index.html.twig', [
            'subject' => $subject,
            'subjects' => $subjects,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="card_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
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
     * @Route("/{card}/{student}", name="card_show", methods={"GET"}, defaults={"student": null}))
     * @param Card $card
     * @param ?Student $student
     * @return Response
     */
    public function show(Card $card, ?Student $student, CardRepository $cardRepository): Response
    {
        //verify if card can be unlock
        $unlockingCard = false;
        if($student != null and $card->getLevel()>1) {
            $search = $cardRepository->findOneBy([
                'name'=> $card->getName(),
                'level' => $card->getLevel()-1,
                ]);
            foreach($student->getCards() as $studentCard) {
                if($studentCard == $search) {
                    $unlockingCard = true;
                    break;
                }
            }
        }
        return $this->render('card/show.html.twig', [
            'card' => $card,
            'student' => $student,
            'unlockingCard' => $unlockingCard,
        ]);
    }

    /**
     * @Route("/{id}", name="card_delete", methods={"DELETE"})
     * @IsGranted("ROLE_ADMIN")
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
     * @IsGranted("ROLE_TEACHER")
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
            $verb = "perdue";
            $student->removeCard($card);
            $message = 'La carte "' . $card->getName() . '" a été retirée à ' . $student->getFirstname();
        } else {
            $point->setQuantity($card->getLevel());
            $action = "Gain";
            $verb = "ajoutée";
            $student->addCard($card);
            $message = $student->getFirstname() . ' a gagné la carte "' . $card->getName() . '"';
        }
            
        if($card->getType()=="apprentissage") {
            $type = "gold";
            $sentence = $action . ' de la carte d\'apprentissage "' . $card->getName() . '" - Niveau ' . $card->getLevel();
            if($card->getLevel()==1) {
                $message2 = $card->getLevel() . " pièce d'or a été " . $verb;
            } else {
                $message2 = $card->getLevel() . " pièces d'or ont été " . $verb . 's';
            }
        } else {
            $type = "elixir";
            $sentence = $action . ' de la carte de comportement "' . $card->getName() . '" - Niveau ' . $card->getLevel();
            if($card->getLevel()==1) {
                $message2 = $card->getLevel() . " goutte d'élixir a été " . $verb;
            } else {
                $message2 = $card->getLevel() . " gouttes d'élixir ont été " . $verb . 's';
            }
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
        $this->addFlash('success', $message2);

        return $this->redirectToRoute('card_index_student', [
            'student' => $student->getId(),
        ]);
    }
}
