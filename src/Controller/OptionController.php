<?php

namespace App\Controller;

use App\Entity\Option;
use App\Form\OptionType;
use App\Repository\OptionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/option")
 */
class OptionController extends AbstractController
{
    /**
     * @Route("/", name="option_index", methods={"GET"})
     */
    public function index(OptionRepository $optionRepository): Response
    {
        return $this->render('option/index.html.twig', [
            'options' => $optionRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="option_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $option = new Option();
        $form = $this->createForm(OptionType::class, $option);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($option);
            $entityManager->flush();

            return $this->redirectToRoute('option_index');
        }

        return $this->render('option/new.html.twig', [
            'option' => $option,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="option_show", methods={"GET"})
     */
    public function show(Option $option): Response
    {
        return $this->render('option/show.html.twig', [
            'option' => $option,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="option_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Option $option): Response
    {
        $form = $this->createForm(OptionType::class, $option);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('option_index');
        }

        return $this->render('option/edit.html.twig', [
            'option' => $option,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="option_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Option $option): Response
    {
        if ($this->isCsrfTokenValid('delete'.$option->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($option);
            $entityManager->flush();
        }

        return $this->redirectToRoute('option_index');
    }
}
