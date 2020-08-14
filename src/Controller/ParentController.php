<?php

namespace App\Controller;

use App\Entity\Student;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/parent")
 * @IsGranted("ROLE_PARENT")
 */
class ParentController extends AbstractController
{
    /**
     * @Route("/", name="parent")
     */
    public function index()
    {
        return $this->render('parent/index.html.twig');
    }

    /**
     * @Route("/add", name="parent_add_student")
     * @param Request $request
     * @param StudentRepository $studentRepository
     * @return RedirectResponse|Response
     */
    public function addStudent(Request $request, StudentRepository $studentRepository)
    {
        $form = $this->createFormBuilder()
            ->add('ine')
            ->add('firstname')
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $student = $studentRepository->findOneBy([
                'ine' => $form->getData()['ine'],
                'firstname' => $form->getData()['firstname'],
                ]);
            if(empty($student)) {
                $this->addFlash('danger', 'Aucun enfant trouvé');
                return $this->redirectToRoute('parent_add_student');
            } else {
                $student->addParent($this->getUser());
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($student);
            $entityManager->flush();

            $message = $student->getFirstname() . ' ' . $student->getLastname() . ' est désormais rattaché à votre compte';
            $this->addFlash('success', $message);
            return $this->redirectToRoute('parent');
        }

        return $this->render('parent/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/remove", name="parent_remove_student")
     * @param Student $student
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function removeFromClass(Student $student, EntityManagerInterface $em)
    {
        if(isset($_GET["delete"])) {
            $name = $student->getFirstname() . ' ' . $student->getLastname();
            $student->removeParent($this->getUser());
            $em->persist($student);
            $em-> flush();

            $this->addFlash('success', "$name n'est plus rattaché à votre compte");
            return $this->redirectToRoute('parent');
        }
        return $this->render('parent/remove.html.twig', [
            'student' => $student,
        ]);
    }
}
