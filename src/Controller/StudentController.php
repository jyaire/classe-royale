<?php

namespace App\Controller;

use App\Entity\Student;
use App\Form\StudentType;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/student")
 */
class StudentController extends AbstractController
{
    /**
     * @Route("/", name="student_index", methods={"GET"})
     * @param StudentRepository $studentRepository
     * @return Response
     */
    public function index(StudentRepository $studentRepository): Response
    {
        return $this->render('student/index.html.twig', [
            'students' => $studentRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="student_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $student = new Student();
        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($student);
            $entityManager->flush();

            return $this->redirectToRoute('student_index');
        }

        return $this->render('student/new.html.twig', [
            'student' => $student,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="student_show", methods={"GET"})
     * @param Student $student
     * @return Response
     */
    public function show(Student $student): Response
    {
        return $this->render('student/show.html.twig', [
            'student' => $student,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="student_edit", methods={"GET","POST"})
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
     * @param Request $request
     * @param Student $student
     * @return Response
     */
    public function editAvatar(Request $request, Student $student): Response
    {
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
