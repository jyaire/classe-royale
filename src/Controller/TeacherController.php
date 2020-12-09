<?php

namespace App\Controller;

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
}
