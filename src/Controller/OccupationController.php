<?php

namespace App\Controller;

use App\Entity\Occupation;
use App\Form\OccupationType;
use App\Repository\OccupationRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/occupation")
 * @IsGranted("ROLE_TEACHER")
 */
class OccupationController extends AbstractController
{
    /**
     * @Route("/", name="occupation_index", methods={"GET"})
     */
    public function index(OccupationRepository $occupationRepository): Response
    {
        return $this->render('occupation/index.html.twig', [
            'occupations' => $occupationRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="occupation_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $occupation = new Occupation();
        $form = $this->createForm(OccupationType::class, $occupation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($occupation);
            $entityManager->flush();

            return $this->redirectToRoute('occupation_index');
        }

        return $this->render('occupation/new.html.twig', [
            'occupation' => $occupation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="occupation_show", methods={"GET"})
     */
    public function show(Occupation $occupation): Response
    {
        return $this->render('occupation/show.html.twig', [
            'occupation' => $occupation,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="occupation_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Occupation $occupation): Response
    {
        $form = $this->createForm(OccupationType::class, $occupation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('occupation_index');
        }

        return $this->render('occupation/edit.html.twig', [
            'occupation' => $occupation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="occupation_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Occupation $occupation): Response
    {
        if ($this->isCsrfTokenValid('delete'.$occupation->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($occupation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('occupation_index');
    }
}
