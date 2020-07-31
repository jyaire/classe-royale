<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        foreach ($this->getUser()->getRoles() as $role) {
            if ($role == "ROLE_DIRECTOR") {
                return $this->redirectToRoute('director');
            }
    }
        return $this->render('home/index.html.twig');
    }
}
