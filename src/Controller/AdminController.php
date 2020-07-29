<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index()
    {
        return $this->render('admin/index.html.twig');
    }

    /**
     * @Route("/admin/addAdmin", name="admin_addAdmin")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function addAdmin(Request $request, UserRepository $userRepository)
    {
        $form = $this->createFormBuilder()
            ->add('email', EmailType::class)
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->getData();
            $user = $userRepository->findOneBy(['email' => $email]);
            $roles = $user->getRoles();
            array_push($roles, "ROLE_ADMIN");
            $user->setRoles($roles);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $message = $user->getFirstname() . ' ' . $user->getLastname() . ' est désormais administrateur';
            $this->addFlash('success', $message);
            return $this->redirectToRoute('admin');
        }

        return $this->render('admin/addAdmin.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
