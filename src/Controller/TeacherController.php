<?php

namespace App\Controller;

use App\Entity\Classgroup;
use App\Repository\ClassgroupRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class TeacherController extends AbstractController
{
    /**
     * @Route("/teacher", name="teacher")
     * @IsGranted("ROLE_TEACHER")
     */
    public function index()
    {
    
        return $this->render('teacher/index.html.twig');
    }

    /**
     * @Route("/teacher/invite", name="invite_teacher")
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @param StudentRepository $studentRepository
     * @return RedirectResponse|Response
     */
    public function invite(Request $request, ClassgroupRepository $classgroupRepository, AuthorizationCheckerInterface $authChecker)
    {
        $form = $this->createFormBuilder()
            ->add('invit', TextType::class, [
                'label' => "Code enseignant reçu par mail",
            ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // add teacher to classgroup if code is ok

            $entityManager = $this->getDoctrine()->getManager();

            $classgroup = $classgroupRepository->findOneBy([
                'invit' => $form->getData()['invit'],
                ]);
            if(empty($classgroup)) {
                $this->addFlash('danger', 'Ce code est invalide');
                return $this->redirectToRoute('invite_teacher');
            } else {
                $classgroup->addTeacher($this->getUser());
                $entityManager->persist($classgroup);
            }

            // add role_teacher to user if not already teacher
            if (!$authChecker->isGranted('ROLE_TEACHER')) {
                $user = $this->getUser();
                $roles = $user->getRoles();
                array_push($roles, "ROLE_TEACHER");
                $user->setRoles($roles);
                $entityManager->persist($user);
                $message = 'Vous êtes devenu "enseignant", il faut vous reconnecter';
                $this->addFlash('danger', $message);
            }

            $entityManager->flush();

            $message = 'Vous êtes enseignant dans la classe "' . $classgroup->getName() . '"';
            $this->addFlash('success', $message);
            return $this->redirectToRoute('teacher');
        }

        return $this->render('teacher/invite.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/addTeacher/{classgroup}", name="teacher_addClassgroup")
     * @param Request $request
     * @param Classgroup $classgroup
     */
    public function addTeacher(Classgroup $classgroup)
    {
        $user = $this->getUser();
        $user->addClassgroup($classgroup);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        $message = $user->getFirstname() . ' ' . $user->getLastname() . ' est associé(e) à ' . $classgroup->getName();
        $this->addFlash('success', $message);
        return $this->redirectToRoute('teacher');
    }
}
