<?php

namespace App\Controller;

use App\Entity\Classgroup;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TeacherController extends AbstractController
{
    /**
     * @Route("/teacher", name="teacher")
     * @IsGranted("ROLE_TEACHER")
     */
    public function index()
    {
        if(count($this->getUser()->getClassgroups()) != 0) {
            $classgroups = $this->getUser()->getClassgroups();
            foreach($classgroups as $classgroup) {
                $school = $classgroup->getSchool();
            }
            return $this->render('teacher/index.html.twig', [
                'school' => $school,
            ]);
        }
        else {
            return $this->render('teacher/index.html.twig', [
                'school' => null,
            ]);
        }
    }

    /**
     * @Route("/addTeacher/{classgroup}", name="teacher_addClassgroup")
     * @param Request $request
     * @param Classgroup $classgroup
     */
    public function addTeacher(Classgroup $classgroup)
    {
        $user = $this->getUser();
        $user->addClassgroup($classgroup);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        $message = $user->getFirstname() . ' ' . $user->getLastname() . ' est associé(e) à ' . $classgroup->getName();
        $this->addFlash('success', $message);
        return $this->redirectToRoute('teacher');
    }
}
