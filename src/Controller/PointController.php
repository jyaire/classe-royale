<?php

namespace App\Controller;

use App\Entity\Point;
use App\Entity\Reason;
use App\Entity\Student;
use App\Form\PointType;
use App\Repository\PointRepository;
use App\Repository\ReasonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/point")
 */
class PointController extends AbstractController
{
    /**
     * @Route("/", name="point_index", methods={"GET"})
     */
    public function index(PointRepository $pointRepository): Response
    {
        return $this->render('point/index.html.twig', [
            'points' => $pointRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new/{id}/{type}", name="point_new", methods={"GET","POST"})
     * @param Student $student
     * @param ReasonRepository $reasonRepository
     * @param string $type
     * @param Request $request
     * @return Response
     */
    public function new(Student $student, ReasonRepository $reasonRepository, string $type, Request $request): Response
    {
        $point = new Point();
        $point
            ->setType($type)
            ->setStudent($student);
        $form = $this->createForm(PointType::class, $point);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $point->setDate(new \DateTime());
            $reason = $point->getReason();
            // search if reason already exists
            $search = $reasonRepository->findOneBy(['sentence'=>$reason->getSentence()]);
            if(!empty($search)) {
                $point->setReason($search);
            } else {
                $entityManager->persist($reason);
            }
            if ($reason->getSentence())
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
            $student->setXp($student->getXp()+5);
            $entityManager->persist($point);
            $entityManager->persist($student);
            $entityManager->flush();

            return $this->redirectToRoute('student_show', ['id' => $student->getId()]);
        }

        return $this->render('point/new.html.twig', [
            'point' => $point,
            'student' => $student,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="point_show", methods={"GET"})
     */
    public function show(Point $point): Response
    {
        return $this->render('point/show.html.twig', [
            'point' => $point,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="point_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Point $point): Response
    {
        $form = $this->createForm(PointType::class, $point);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('point_index');
        }

        return $this->render('point/edit.html.twig', [
            'point' => $point,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="point_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Point $point): Response
    {
        if ($this->isCsrfTokenValid('delete'.$point->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($point);
            $entityManager->flush();
        }

        return $this->redirectToRoute('point_index');
    }
}
