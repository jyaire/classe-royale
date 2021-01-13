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
    
        return $this->render('teacher/index.html.twig');
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
