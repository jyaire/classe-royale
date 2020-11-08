<?php

namespace App\Controller;

use App\Entity\Classgroup;
use App\Entity\Student;
use App\Form\ClassgroupType;
use App\Repository\ClassgroupRepository;
use Doctrine\ORM\EntityManagerInterface;
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
     * @param ClassgroupRepository $classgroupRepository
     * @return Response
     */
    public function index(ClassgroupRepository $classgroupRepository): Response
    {
        return $this->render('classgroup/index.html.twig', [
            'classgroups' => $classgroupRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="classgroup_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
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
     * @Route("/{id}/{ranking}", name="classgroup_show", methods={"GET"}, defaults={"ranking": null})
     * @param Classgroup $classgroup
     * @param string $ranking
     * @return Response
     */
    public function show(Classgroup $classgroup, ?string $ranking): Response
    {
        return $this->render('classgroup/show.html.twig', [
            'classgroup' => $classgroup,
            'ranking' => $ranking,
            ]);
    }

    /**
     * @Route("/{id}/edit", name="classgroup_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Classgroup $classgroup
     * @return Response
     */
    public function edit(Request $request, Classgroup $classgroup): Response
    {
        $form = $this->createForm(ClassgroupType::class, $classgroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('school_show', ['id'=>$classgroup->getSchool()->getId()]);
        }

        return $this->render('classgroup/edit.html.twig', [
            'classgroup' => $classgroup,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/remove", name="classgroup_remove")
     * @param Classgroup $classgroup
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function removeFromClass(Classgroup $classgroup, EntityManagerInterface $em)
    {
        if(isset($_GET["delete"])) {
            $name = $classgroup->getName();
            $oldSchool = $classgroup->getSchool();
            $oldSchoolName = $oldSchool->getName();
            $classgroup->setSchool(null);
            $em->persist($classgroup);
            $em-> flush();

            $this->addFlash('success', "$name est retirée de $oldSchoolName'");
            return $this->redirectToRoute('school_show', ['id'=>$oldSchool->getId()]);
        }
        return $this->render('classgroup/remove.html.twig', [
            'classgroup' => $classgroup,
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
