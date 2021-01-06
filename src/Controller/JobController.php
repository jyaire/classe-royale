<?php

namespace App\Controller;

use App\Entity\Job;
use App\Entity\Classgroup;
use App\Form\JobType;
use App\Repository\JobRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/job")
 * @IsGranted("ROLE_TEACHER")
 */
class JobController extends AbstractController
{
    /**
     * @Route("/new/{classgroup}", name="job_new", methods={"GET","POST"}, defaults={"classgroup": null})
     */
    public function new(Request $request, ?Classgroup $classgroup, AuthorizationCheckerInterface $authChecker): Response
    {
        if ($classgroup == null and !$authChecker->isGranted('ROLE_ADMIN')) {
            $this->addFlash(
            'danger',
            'Le nouveau métier doit être rattaché à une classe'
            );
            return $this->redirectToRoute('teacher');
        }
        $job = new Job();
        $job->setClassgroup($classgroup);
        $form = $this->createForm(JobType::class, $job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($job);
            $entityManager->flush();
            $this->addFlash(
                'success',
                'Métier ajouté'
                );

            if ($this->isGranted('ROLE_ADMIN') and $classgroup == null) {
                return $this->redirectToRoute('job_index');
            }
            else {
                return $this->redirectToRoute('job_index', [
                    'classgroup' => $classgroup->getId(),
                ]);
            }
        }

        return $this->render('job/new.html.twig', [
            'job' => $job,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{classgroup}", name="job_index", methods={"GET"}, defaults={"classgroup": null})
     */
    public function index(JobRepository $jobRepository, ?Classgroup $classgroup): Response
    {
        if ($classgroup == null) {
            if ($this->isGranted('ROLE_ADMIN')) {
                $jobs = $jobRepository->findBy(['classgroup'=>null]);
            }
            else {
                $this->addFlash(
                    'danger',
                    'Vous ne pouvez afficher que les métiers de votre classe'
                    );
                    return $this->redirectToRoute('teacher');
            }
        }
        else {
            $jobs = $jobRepository->findForClassgroup($classgroup);
        }
        return $this->render('job/index.html.twig', [
            'jobs' => $jobs,
            'classgroup' => $classgroup,
        ]);
    }

    /**
     * @Route("/{id}", name="job_show", methods={"GET"})
     */
    public function show(Job $job): Response
    {
        return $this->render('job/show.html.twig', [
            'job' => $job,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="job_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Job $job): Response
    {
        $form = $this->createForm(JobType::class, $job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('job_index');
        }

        return $this->render('job/edit.html.twig', [
            'job' => $job,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="job_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Job $job): Response
    {
        if ($this->isCsrfTokenValid('delete'.$job->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($job);
            $entityManager->flush();
        }

        return $this->redirectToRoute('job_index');
    }
}
