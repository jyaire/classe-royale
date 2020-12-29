<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @return RedirectResponse|Response
     */
    public function index(AuthorizationCheckerInterface $authorizationChecker)
    {
        if ($this->getUser()) {
            $accessTeacher = $authorizationChecker->isGranted("ROLE_TEACHER");
            $accessDirector = $authorizationChecker->isGranted("ROLE_DIRECTOR");
            $accessAdmin = $authorizationChecker->isGranted("ROLE_ADMIN");
            $accessParent = $authorizationChecker->isGranted("ROLE_PARENT");

                if ($accessTeacher) {
                    return $this->redirectToRoute('teacher');
                }
                elseif ($accessDirector) {
                    return $this->redirectToRoute('director');
                }
                elseif ($accessAdmin) {
                    return $this->redirectToRoute('admin');
                }
                elseif ($accessParent) {
                    return $this->redirectToRoute('parent');
                }
        }
        else {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('home/index.html.twig');
    }

    /**
     * @Route("/infos", name="infos")
     */
    public function infos()
    {
        return $this->render('home/infos.html.twig');
    }

    /**
     * @Route("/mentions", name="mentions")
     */
    public function mentions()
    {
        return $this->render('home/mentions.html.twig');
    }
}
