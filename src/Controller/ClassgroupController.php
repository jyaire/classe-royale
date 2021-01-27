<?php

namespace App\Controller;

use App\Entity\Classgroup;
use App\Entity\School;
use App\Entity\User;
use App\Form\ClassgroupType;
use App\Repository\ClassgroupRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\MailerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;

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
     * @Route("/invit/pdf/{classgroup}", name="classgroup_invit_pdf", methods={"GET"})
     * @IsGranted("ROLE_TEACHER")
     * @param Classgroup $classgroup
     * @return Response
     */
    public function invitePDF(Classgroup $classgroup)
    {
        // generate invitation code id doen't exist in student table
        $entityManager = $this->getDoctrine()->getManager();
        foreach ($classgroup->getStudents() as $student) {
            if ($student->getInvit() == null) {
                $data = array_map('str_shuffle', [
                    'digit' => '23569',
                    'upper' => 'CDEFGHJKLMNPQRSTUVWXY'
                ]);
                 
                $pwd = str_shuffle(
                    substr($data['digit'], 0 , 4).
                    substr($data['upper'], 0 , 2)
                );
                // add initials to make unique
                $pwd = substr($student->getFirstname(), 0, 1).substr($student->getLastname(), 0, 1).$pwd;
                $student->setInvit($pwd);
                $entityManager->persist($student);
            }
        }
        $entityManager->flush();

        // generate PDF with DomPDF
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($pdfOptions);
        
        $html = $this->renderView('classgroup/invitpdf.html.twig', [
            'classgroup' => $classgroup,
        ]);
        
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();
        $dompdf->stream("invitations_familles.pdf", [
            "Attachment" => false
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
     * @Route("/{id}/addTeacher", name="teacher_add")
     * @param Request $request
     * @param Classgroup $classgroup
     * @param UserRepository $userRepository
     * @return RedirectResponse|Response
     */
    public function teacherAdd(Request $request, Classgroup $classgroup, UserRepository $userRepository, MailerInterface $mailer)
    {
        $form = $this->createFormBuilder()
            ->add('email', EmailType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->getData()['email'];
            $user = $userRepository->findOneBy(['email' => $email]);

            $entityManager = $this->getDoctrine()->getManager();

            if ($user == null) {
                 // generate invitation code id doen't exist in student table
                
                if ($classgroup->getInvit() == null) {
                    
                    $data = array_map('str_shuffle', [
                        'digit' => '23569',
                        'upper' => 'CDEFGHJKLMNPQRSTUVWXY'
                    ]);
                    
                    $invit = str_shuffle(
                        substr($data['digit'], 0 , 2).
                        substr($data['upper'], 0 , 3)
                    );
                    // add id classgroup to make unique
                    $invit = $classgroup->getId().$invit;
                    $classgroup->setInvit($invit);
                    $entityManager->persist($classgroup);
                    $entityManager->flush();
                }

                // send mail to invitated teacher
                $message = (new TemplatedEmail())
                ->from(new Address('bienvenue@classeroyale.fr', 'Classe Royale'))
                ->to($email)
                ->subject('Invitation pour enseigner dans une classe')
                ->htmlTemplate('contact/invitTeacher.html.twig')
                ->context([
                    'classgroup' => $classgroup,
                ])
                ;
                $mailer->send($message);


                $message = "Une invitation a été envoyée à " . $email . " pour cette classe";
                $this->addFlash('success', $message);
            }
            else {
                // attach invitated user to the classgroup
                $teacher = false;
                foreach($user->getRoles() as $role) {
                    if($role == "ROLE_TEACHER") {
                        $teacher = true;
                    }
                }
                if ($teacher == false) {
                    $roles = $user->getRoles();
                    array_push($roles, "ROLE_TEACHER");
                    $user->setRoles($roles);
                }
                $user->addClassgroup($classgroup);

                $entityManager->persist($user);
                $entityManager->flush();

                $message = $user->getFirstname() . ' ' . $user->getLastname() . ' est associé(e) à la classe';
                $this->addFlash('success', $message);
            }

            return $this->redirectToRoute('classgroup_show', ['id'=>$classgroup->getId()]);
        }

        return $this->render('director/addTeacher.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{classgroup}/deleteTeacher/{teacher}", name="teacher_delete")
     * @param Request $request
     * @param Classgroup $classgroup
     * @param User $teacher
     * @return Response
     */
    public function teacherDelete(Request $request, Classgroup $classgroup, User $teacher): Response
    {
        $classgroup->removeTeacher($teacher);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($classgroup);
        $entityManager->flush();

        $message = $teacher->getFirstname() . ' ' . $teacher->getLastname() . ' a été retiré(e) de la classe';
        $this->addFlash('success', $message);

        return $this->redirectToRoute('classgroup_show', ['id'=>$classgroup->getId()]);
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
            $access = false;
            foreach($this->getUser()->getStudents() as $child) {
                $classChild = $child->getClassgroup();
                if($classgroup == $classChild) {
                    $access = true;
                }
            }
            if ($access == true) {
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
        
        return $this->render('classgroup/show.html.twig', [
            'classgroup' => $classgroup,
            'ranking' => $ranking,
            ]);
    }
}
