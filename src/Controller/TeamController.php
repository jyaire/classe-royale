<?php

namespace App\Controller;

use App\Entity\Classgroup;
use App\Entity\Team;
use App\Form\TeamType;
use App\Repository\TeamRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/team")
 */
class TeamController extends AbstractController
{
    /**
     * @Route("/{classgroup}", name="team_index", methods={"GET"})
     * @param Classgroup $classgroup
     * @param TeamRepository $teamRepository
     * @return Response
     */
    public function index(Classgroup $classgroup, TeamRepository $teamRepository): Response
    {
        return $this->render('team/index.html.twig', [
            'teams' => $teamRepository->findBy(['classgroup'=>$classgroup]),
            'classgroup' => $classgroup,
        ]);
    }

    /**
     * @Route("/new/{classgroup}", name="team_new", methods={"GET","POST"})
     * @param Classgroup $classgroup
     * @param Request $request
     * @return Response
     */
    public function new(Classgroup $classgroup, Request $request): Response
    {
        $team = new Team();
        $team->setClassgroup($classgroup);
        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($team);
            $entityManager->flush();

            $this->addFlash('success', "L'équipe a été ajoutée");
            return $this->redirectToRoute('team_index', ['classgroup'=>$classgroup->getId()]);
        }

        return $this->render('team/new.html.twig', [
            'team' => $team,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/show/{id}", name="team_show", methods={"GET"})
     * @param Team $team
     * @return Response
     */
    public function show(Team $team): Response
    {
        return $this->render('team/show.html.twig', [
            'team' => $team,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="team_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Team $team
     * @return Response
     */
    public function edit(Request $request, Team $team): Response
    {
        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', "L'équipe a été modifiée");
            return $this->redirectToRoute('team_index', ['classgroup'=>$team->getClassgroup()->getId()]);
        }

        return $this->render('team/edit.html.twig', [
            'team' => $team,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="team_delete", methods={"DELETE"})
     * @param Request $request
     * @param Team $team
     * @return Response
     */
    public function delete(Request $request, Team $team): Response
    {
        if($team->getStudent() == null) {
        $this->addFlash('danger', "L'équipe doit être vide pour être supprimée");
        return $this->redirectToRoute('team_show', ['id'=>$team->getId()]);
        }
        if ($this->isCsrfTokenValid('delete'.$team->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($team);
            $entityManager->flush();
        }

        $this->addFlash('success', "L'équipe a été supprimée");
        return $this->redirectToRoute('team_index', ['classgroup'=>$team->getClassgroup()->getId()]);
    }
}
