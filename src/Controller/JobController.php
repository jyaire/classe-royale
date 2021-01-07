<?php

namespace App\Controller;

use App\Entity\Job;
use App\Entity\Classgroup;
use App\Entity\Occupation;
use App\Form\JobType;
use App\Form\OccupationType;
use App\Repository\JobRepository;
use DateTime;
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
     * @Route("/", name="job_index_all", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function indexAll(JobRepository $jobRepository): Response
    {
        $jobs = $jobRepository->findAll();
        
        return $this->render('job/indexAll.html.twig', [
            'jobs' => $jobs,
        ]);
    }
    
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
     * @Route("/classgroup/{classgroup}", name="job_index", methods={"GET"}, defaults={"classgroup": null})
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
            $jobs = $jobRepository->findBy(['classgroup'=>$classgroup]);
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
     * @Route("/remove/{job}", name="job_remove", methods={"GET"})
     */
    public function remove(Job $job): Response
    {
        $classgroup = $job->getClassgroup();
        $job->setClassgroup(null);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($job);
        $entityManager->flush();

        $this->addFlash(
            'success',
            'Le métier a été retiré de votre classe'
            );
        return $this->redirectToRoute('job_index', [
            'classgroup'=>$classgroup->getId(),
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

    /**
     * @Route("/add/{job}", name="job_add", methods={"GET","POST"})
     */
    public function add(Request $request, Job $job): Response
    {
        $form = $this->createForm(OccupationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();

            $count=0;
            foreach ($form->get('student')->getData() as $student) {
                $occupation = new Occupation();
                $occupation
                    ->setStudent($student)
                    ->setJob($job)
                    ->setDateStart(new DateTime())
                    ->setSalary($form->get('salary')->getData())
                    ;
                    $count++;
                $entityManager->persist($occupation);
            }
            
            $entityManager->flush();
            $message = $count . 'élève(s) ajouté(s) à ce métier';
            $this->addFlash(
                'success',
                $message
                );

            return $this->redirectToRoute('job_index', [
                'classgroup' => $job->getClassgroup()->getId(),
            ]);
        }

        return $this->render('job/add.html.twig', [
            'job' => $job,
            'form' => $form->createView(),
        ]);
    }
}
