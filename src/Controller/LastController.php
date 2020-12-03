<?php

namespace App\Controller;

use App\Repository\PointRepository;
use App\Repository\StudentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class LastController extends AbstractController
{
    /**
     * @Route("/last", name="last_index", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
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
     * @Route("/last/{user}/{id}", name="last_custom", methods={"GET"})
     * @IsGranted("ROLE_USER")
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
        // for points for a student
        if($user == 'student') {
            $points = $pointRepository->findBy(['student'=>$id]);
            $classGroupId = $studentRepository->findOneBy(['id'=>$id])->getClassgroup()->getId();
        }
        // for points for a classgroup
        elseif($user == 'classgroup') {
            $points = [];
            $classGroupId = $id;
            $students = $studentRepository->findBy(['classgroup'=>$classGroupId]);
            foreach($students as $student) {
                $pointsStudent = $pointRepository->findBy(['student'=>$student]);
                $points = array_merge($points, $pointsStudent);
            }
        }
        // for all points for admin
        else {
            $user = null;
            $points = $pointRepository->findAll();
            $classGroupId = null;
        }

        // if parent is connected, verify user can view the student
        if($this->isGranted('ROLE_PARENT')) {
            foreach($this->getUser()->getStudents() as $child) {
                $classGroupId = $child->getClassgroup()->getId();
                $studentId = $child->getId();
                if($user == 'student') { 
                    if($id == $studentId) {
                        return $this->render('point/index.html.twig', [
                            'points' => $points,
                            'user' => $user,
                            'classgroupId' => $classGroupId,
                        ]);
                    }
                    else {
                        $this->addFlash('danger', "Vous ne pouvez voir que les activités de vos enfants");
                        return $this->redirectToRoute('parent');
                    }
                } 
                elseif($user == 'classgroup') { 
                    if($id == $classGroupId) {
                        return $this->render('point/index.html.twig', [
                            'points' => $points,
                            'user' => $user,
                            'classgroupId' => $id,
                        ]);
                    }
                    else {
                        $this->addFlash('danger', "Vous ne pouvez voir que les activités des classes de vos enfants");
                        return $this->redirectToRoute('parent');
                    }
                }
            }
        }
        
        return $this->render('point/index.html.twig', [
            'points' => $points,
            'user' => $user,
            'classgroupId' => $classGroupId,
        ]);
    }
}
