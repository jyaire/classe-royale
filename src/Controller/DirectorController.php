<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DirectorController extends AbstractController
{
    /**
     * @Route("/director", name="director")
     * @IsGranted("ROLE_DIRECTOR")
     */
    public function index()
    {
        return $this->render('director/index.html.twig', [
            'controller_name' => 'DirectorController',
        ]);
    }
}