<?php

namespace App\Controller;

use App\Entity\Classgroup;
use App\Form\ClassgroupType;
use App\Repository\ClassgroupRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/classgroup")
 */
class ClassgroupController extends AbstractController
{
    /**
     * @Route("/", name="classgroup_index", methods={"GET"})
     */
    public function index(ClassgroupRepository $classgroupRepository): Response
    {
        return $this->render('classgroup/index.html.twig', [
            'classgroups' => $classgroupRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="classgroup_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $classgroup = new Classgroup();
        $form = $this->createForm(ClassgroupType::class, $classgroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($classgroup);
            $entityManager->flush();

            return $this->redirectToRoute('classgroup_index');
        }

        return $this->render('classgroup/new.html.twig', [
            'classgroup' => $classgroup,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="classgroup_show", methods={"GET"})
     * @param Classgroup $classgroup
     * @return Response
     */
    public function show(Classgroup $classgroup): Response
    {
        if ($this->getUser()) {
            foreach ($this->getUser()->getRoles() as $role) {
                if ($role == "ROLE_ADMIN") {
                    return $this->render('classgroup/show.html.twig', [
                        'classgroup' => $classgroup,
                    ]);
                }
            }
        }
        else {
            return $this->render('classgroup/play.html.twig', [
                'classgroup' => $classgroup,
            ]);
        }
    }

    /**
     * @Route("/{id}/edit", name="classgroup_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Classgroup $classgroup): Response
    {
        $form = $this->createForm(ClassgroupType::class, $classgroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('classgroup_index');
        }

        return $this->render('classgroup/edit.html.twig', [
            'classgroup' => $classgroup,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="classgroup_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Classgroup $classgroup): Response
    {
        if ($this->isCsrfTokenValid('delete'.$classgroup->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($classgroup);
            $entityManager->flush();
        }

        return $this->redirectToRoute('classgroup_index');
    }
}
