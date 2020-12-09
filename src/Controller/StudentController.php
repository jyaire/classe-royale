<?php

namespace App\Controller;

use App\Entity\Classgroup;
use App\Entity\Student;
use App\Form\StudentType;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/student")
 * @IsGranted("ROLE_USER")
 */
class StudentController extends AbstractController
{
    /**
     * @Route("/all/{order}", name="student_index", methods={"GET"}, defaults={"order": null})
     * @IsGranted("ROLE_ADMIN")
     * @param StudentRepository $studentRepository
     * @return Response
     */
    public function index(?string $order, StudentRepository $studentRepository): Response
    {
        if($order == "alone") {
            $students = $studentRepository->findBy(['classgroup'=>null]);
        } 
        else { 
            $students = $studentRepository->findAll();
        }
        return $this->render('student/index.html.twig', [
            'students' => $students,
            'order' => $order,
        ]);
    }

    /**
     * @Route("/{id}/new", name="student_new", methods={"GET","POST"})
     * @IsGranted("ROLE_TEATCHER")
     * @param Request $request
     * @param Classgroup $classgroup
     * @param StudentRepository $studentRepository
     * @return Response
     */
    public function new(Request $request, Classgroup $classgroup, StudentRepository $studentRepository): Response
    {
        if (isset($_GET['student'])) {
            $student = $studentRepository->findOneBy(['id'=>$_GET['student']]);
            $student
                ->setClassgroup($classgroup)
                ->setDateModif(new \DateTime())
            ;
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($student);
            $entityManager->flush();

            $name = $student->getFirstname(). ' ' . $student->getLastname();
            $this->addFlash('success', "$name est désormais dans cette classe");
            return $this->redirectToRoute('classgroup_show', ['id'=>$classgroup->getId()]);
        }

        $availableStudents = $studentRepository->findBy(['classgroup'=>null]);
        $student = new Student();
        $student->setClassgroup($classgroup);
        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $student
                ->setElixir(10)
                ->setGold(10)
                ->setXp(5)
                ->setIsLead(0)
                ->setDateCreate(new \DateTime())
                ;
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($student);
            $entityManager->flush();

            return $this->redirectToRoute('classgroup_show', ['id'=>$classgroup->getId()]);
        }

        return $this->render('student/new.html.twig', [
            'student' => $student,
            'classgroup' => $classgroup,
            'availableStudents' => $availableStudents,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="student_show", methods={"GET"})
     * @IsGranted("ROLE_USER")
     * @param Student $student
     * @return Response
     */
    public function show(Student $student): Response
    {
        // if parent is connected, verify user can view the student
        if($this->isGranted('ROLE_PARENT')) {
            foreach($this->getUser()->getStudents() as $child) {
                $classChild = $child->getClassgroup();
                if($student->getClassgroup() == $classChild) {
                    return $this->render('student/show.html.twig', [
                        'student' => $student,
                    ]);
                }
                else {
                    $this->addFlash('danger', "Vous ne pouvez voir que les élèves dans les classes de vos enfants");
                    return $this->redirectToRoute('parent');
                }
            }
        }

        return $this->render('student/show.html.twig', [
            'student' => $student,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="student_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_TEACHER")
     * @param Request $request
     * @param Student $student
     * @return Response
     */
    public function edit(Request $request, Student $student): Response
    {
        $form = $this->createForm(StudentType::class, $student);
        $form->remove('ine');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('student_show', ['id'=>$student->getId()]);
        }

        return $this->render('student/edit.html.twig', [
            'student' => $student,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/remove", name="student_remove")
     * @IsGranted("ROLE_TEACHER")
     * @param Student $student
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function removeFromClass(Student $student, EntityManagerInterface $em)
    {
        if(isset($_GET["delete"])) {
            $name = $student->getFirstname() . ' ' . $student->getLastname();
            $oldClassgroup = $student->getClassgroup();
            $student->setClassgroup(null);
            $em->persist($student);
            $em-> flush();

            $this->addFlash('success', "$name n'est plus dans la classe");
            return $this->redirectToRoute('classgroup_show', ['id'=>$oldClassgroup->getId()]);
        }
        return $this->render('student/remove.html.twig', [
            'student' => $student,
        ]);
    }

    /**
     * @Route("/{id}", name="student_delete", methods={"DELETE"})
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @param Student $student
     * @return Response
     */
    public function delete(Request $request, Student $student): Response
    {
        if ($this->isCsrfTokenValid('delete'.$student->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($student);
            $entityManager->flush();
        }

        return $this->redirectToRoute('student_index');
    }

    /**
     * @Route("/{id}/edit/avatar", name="student_edit_avatar", methods={"GET","POST"})
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @param Student $student
     * @return Response
     */
    public function editAvatar(Request $request, Student $student): Response
    {
        foreach($this->getUser()->getStudents() as $child) {
            if($child != $student) {
                $this->addFlash('danger', 'Vous ne pouvez modifer que l\'avatar de vos propres enfants');
                return $this->redirectToRoute('parent');
            }
        }
        $form = $this->createFormBuilder()
            ->add('avatar', FileType::class)
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $directory = "avatars/";
            $file = $form['avatar']->getData();
            $extension = $file->guessExtension();
            if ($extension == 'png' or $extension == 'jpg' or $extension == 'jpeg') {
                $fileName = $student->getIne().'.'.$extension;
                $file->move($directory, $fileName);
                $student->setAvatar($fileName);

                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('student_show', ['id'=>$student->getId()]);
                }
            $this->addFlash('danger', 'Votre image doit être au format jpg, jpeg ou png');
            return $this->redirectToRoute('student_edit_avatar', ['id'=>$student->getId()]);
        }

        return $this->render('student/avatar.html.twig', [
            'student' => $student,
            'form' => $form->createView(),
        ]);
    }
}
