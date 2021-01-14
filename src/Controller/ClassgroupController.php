<?php

namespace App\Controller;

use App\Entity\Classgroup;
use App\Entity\School;
use App\Form\ClassgroupType;
use App\Repository\ClassgroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/classgroup")
 * @IsGranted("ROLE_USER")
 */
class ClassgroupController extends AbstractController
{
    /**
     * @Route("/", name="classgroup_index", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
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
     * @Route("/choose/{school}", name="classgroup_choose", methods={"GET","POST"})
     * @IsGranted("ROLE_TEACHER")
     * @param School $school
     * @param ClassgroupRepository $classgroupRepository
     * @return Response
     */
    public function choose(School $school, ClassgroupRepository $classgroupRepository): Response
    {
        return $this->render('classgroup/choose.html.twig', [
            'school' => $school,
        ]);
    }

    /**
     * @Route("/new/{school}", name="classgroup_new", methods={"GET","POST"})
     * @IsGranted("ROLE_DIRECTOR")
     * @param Request $request
     * @param School $school
     * @return Response
     */
    public function new(School $school, Request $request): Response
    {
        $classgroup = new Classgroup();
        $classgroup->setSchool($school);
        $form = $this->createForm(ClassgroupType::class, $classgroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($classgroup);
            $entityManager->flush();

            $this->addFlash('success', "Classe créée");

            return $this->redirectToRoute('school_show', [
                'id' => $classgroup->getSchool()->getId(),
            ]);
        }

        return $this->render('classgroup/new.html.twig', [
            'classgroup' => $classgroup,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/invit/{classgroup}", name="classgroup_invit", methods={"GET"})
     * @IsGranted("ROLE_TEACHER")
     * @param Classgroup $classgroup
     * @return Response
     */
    public function invite(Classgroup $classgroup): Response
    {
        return $this->render('classgroup/invit.html.twig', [
            'classgroup' => $classgroup,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="classgroup_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_TEACHER")
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
     * @IsGranted("ROLE_DIRECTOR")
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
     * @IsGranted("ROLE_ADMIN")
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

    /**
     * @Route("/{id}/{ranking}", name="classgroup_show", methods={"GET"}, defaults={"ranking": null})
     * @param Classgroup $classgroup
     * @param string $ranking
     * @return Response
     */
    public function show(Classgroup $classgroup, ?string $ranking): Response
    {
        // if parent connected, verify user can view the classgroup
        if($this->isGranted('ROLE_PARENT')) {
            foreach($this->getUser()->getStudents() as $child) {
                $classChild = $child->getClassgroup();
                if($classgroup == $classChild) {
                    return $this->render('classgroup/show.html.twig', [
                        'classgroup' => $classgroup,
                        'ranking' => $ranking,
                        ]);
                }
                else {
                    $this->addFlash('danger', "Vous ne pouvez voir que les classes de vos enfants");
                    return $this->redirectToRoute('parent');
                }
            }
        }
        
        return $this->render('classgroup/show.html.twig', [
            'classgroup' => $classgroup,
            'ranking' => $ranking,
            ]);
    }
}
