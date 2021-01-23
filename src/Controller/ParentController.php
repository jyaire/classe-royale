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
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @Route("/parent")
 */
class ParentController extends AbstractController
{
    /**
     * @Route("/", name="parent")
     * @IsGranted("ROLE_PARENT")
     */
    public function index()
    {
        return $this->render('parent/index.html.twig');
    }

    /**
     * @Route("/add", name="parent_add_student")
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @param StudentRepository $studentRepository
     * @return RedirectResponse|Response
     */
    public function addStudent(Request $request, StudentRepository $studentRepository, AuthorizationCheckerInterface $authChecker)
    {
        $form = $this->createFormBuilder()
            ->add('invit', TextType::class, [
                'label' => "Code enfant (fourni par l'école)",
            ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // add student to parent if code is ok
            $student = $studentRepository->findOneBy([
                'invit' => $form->getData()['invit'],
                ]);
            if(empty($student)) {
                $this->addFlash('danger', 'Aucun enfant trouvé');
                return $this->redirectToRoute('parent_add_student');
            } else {
                $student->addParent($this->getUser());
            }

            $entityManager = $this->getDoctrine()->getManager();

            // add role_parent to user if not already parent
            if (!$authChecker->isGranted('ROLE_PARENT')) {
                $user = $this->getUser();
                $roles = $user->getRoles();
                array_push($roles, "ROLE_PARENT");
                $user->setRoles($roles);
                $entityManager->persist($user);
                $message = 'Vous êtes devenu "parent", il faut vous reconnecter';
                $this->addFlash('danger', $message);
            }

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
     * @IsGranted("ROLE_PARENT")
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
