<?php

namespace App\Controller;

use App\Repository\PointRepository;
use App\Repository\StudentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LastController extends AbstractController
{
    /**
     * @Route("/last", name="last_index", methods={"GET"})
     * @param PointRepository $pointRepository
     * @return Response
     */
    public function index(PointRepository $pointRepository): Response
    {
        return $this->render('point/index.html.twig', [
            'points' => $pointRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{user}/{id}/last", name="last_custom", methods={"GET"})
     * @param PointRepository $pointRepository
     * @param StudentRepository $studentRepository
     * @return Response
     */
    public function custom(
        PointRepository $pointRepository, 
        StudentRepository $studentRepository, 
        string $user, 
        int $id
        ): Response
    {
        if($user == 'student') {
            $points = $pointRepository->findBy(['student'=>$id]);
        }
        elseif($user == 'classgroup') {
            /* to be debugged
            $points = [];
            $students = $studentRepository->findBy(['classgroup'=>$id]);
            foreach($students as $student) {
                $points = $pointRepository->findBy(['student'=>$student]);
                array_push($points, $points);
            }
            */
            $points = $pointRepository->findBy(['student'=>$id]);
        }
        else {
            $points = $pointRepository->findAll();
        }
        
        return $this->render('point/index.html.twig', [
            'points' => $points,
        ]);
    }
}
