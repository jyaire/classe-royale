<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ParentController extends AbstractController
{
    /**
     * @Route("/parent", name="parent")
     * @IsGranted("ROLE_PARENT")
     */
    public function index()
    {
        return $this->render('parent/index.html.twig', [
            'controller_name' => 'ParentController',
        ]);
    }
}
