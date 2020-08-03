<?php

namespace App\Controller;

use App\Entity\Student;
use App\Form\ImportType;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class DirectorController extends AbstractController
{
    /**
     * @Route("/director", name="director")
     * @IsGranted("ROLE_DIRECTOR")
     */
    public function index()
    {
        return $this->render('director/index.html.twig', [
            'controller_name' => 'DirectorController',
        ]);
    }

    /**
     * @Route("/admin/import", name="admin_import")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param StudentRepository $students
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return RedirectResponse|Response
     */
    public function importStudents(
        Request $request,
        EntityManagerInterface $em,
        StudentRepository $students,
        UserPasswordEncoderInterface $passwordEncoder
    )

    {
        // create form
        $form = $this->createForm(ImportType::class);
        $form->handleRequest($request);
        $destination = $this->getParameter('kernel.project_dir') . '/public/uploads/';

        // verify data after submission
        if ($form->isSubmitted() && $form->isValid()) {
            $studentsFile = $form->get('students')->getData();

            // verify extension format
            $ext = $studentsFile->getClientOriginalExtension();
            if ($ext != "csv") {
                $this->addFlash('danger', "Le fichier doit être de type .csv. 
                Format actuel envoyé : .$ext");

                return $this->redirectToRoute('admin_import');
            }

            // rename file
            $originalFilename = pathinfo($studentsFile->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = $originalFilename . ".csv";
            $studentsFile->move(
                $destination,
                $newFilename
            );

            // open the file to put data in DB and compare with old one
            $oldStudents = $students->findAll();
            $csv = fopen($destination . $newFilename, 'r');
            $i = 0;
            $iModif = 0;
            $iAdd = 0;
            while (($data = fgetcsv($csv, 0, ';')) !== FALSE) {
                $data = array_map("utf8_encode", $data);
                // pass the first title line
                if ($i != 0) {
                    // search if student already here
                    // if yes, add , if no update (add or modify)
                    $ine = $data[5];
                    $student = $students->findOneBy(['ine'=> $ine]);
                    if(isset($student)) {
                        $student
                            ->setSection($data[15])
                            ->setIsCaution(0)
                        ;
                        $em->persist($student);
                        $iModif++;
                    }
                    else {
                        // add pupils
                        $student = new Student();
                        $dateNaissance = DateTime::createFromFormat("d/m/Y", $data[3]);
                        $refClassgroup = $data[18];
                        dd($refClassgroup);
                        if($data[4]=="M") {
                            $isGirl = 0;
                        }
                        else {
                            $isGirl = 1;
                        }
                        $student
                            ->setLastname($data[0])
                            ->setFirstname($data[2])
                            ->setIne($data[5])
                            ->setIsGirl($isGirl)
                            ->setDateCreate(new DateTime())
                            ->setIsLead(0)
                            ->setXp(5)
                            ->setGold(50)
                            ->setElixir(50)
                        ;
                        $em->persist($student);
                        $iAdd++;
                    }
                }
                $i++;
            }
            // send confirmations
            $this->addFlash(
                'success',
                "$iAdd élèves correctement ajoutés et $iModif modifiés"
            );
            $em->flush();
            return $this->redirectToRoute('student_index');
        }

        // find all students
        $students = $students->findAll();

        return $this->render('admin/import.html.twig', [
            'form' => $form->createView(),
            'students' => $students,
        ]);
    }
}
