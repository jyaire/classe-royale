<?php

namespace App\Controller;

use App\Entity\Classgroup;
use App\Entity\School;
use App\Entity\Student;
use App\Form\ImportType;
use App\Repository\ClassgroupRepository;
use App\Repository\SectionRepository;
use App\Repository\StudentRepository;
use DateTime;
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
     * @Route("/director/school/{id}/import", name="import_students")
     * @param Request $request
     * @param School $school
     * @param EntityManagerInterface $em
     * @param ClassgroupRepository $classgroupRepository
     * @param StudentRepository $students
     * @param SectionRepository $sectionRepository
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return RedirectResponse|Response
     */
    public function importStudents(
        Request $request,
        School $school,
        EntityManagerInterface $em,
        ClassgroupRepository $classgroupRepository,
        StudentRepository $students,
        SectionRepository $sectionRepository,
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

            // open the file to create classgroups if not exist

            $csv = fopen($destination . $newFilename, 'r');
            $i = 0;
            $iClassAdd = 0;
            while (($data = fgetcsv($csv, 0, ';')) !== FALSE) {
                $data = array_map("utf8_encode", $data);
                // pass the first title line
                if ($i != 0) {
                    // search if classgroup already here
                    $refClassgroup = $data[17];
                    $classgroup = $classgroupRepository->findOneBy(['ref'=> $refClassgroup]);
                    // if no, add it
                    if(!isset($classgroup)) {
                        $classgroup = new Classgroup();
                        $section = $sectionRepository->findOneBy(['abbreviation'=>$data[15]]);
                        $classgroup
                            ->setName("Classe n°" . $data[17])
                            ->setRef($data[17])
                            ->setSchool($school)
                            ->addSection($section)
                        ;
                        $em->persist($classgroup);
                        $em->flush();
                        $iClassAdd++;
                    }
                }
                $i++;
            }
            // send confirmation if classgroup added
            if ($iClassAdd > 0) {
                $this->addFlash(
                    'success',
                    "$iClassAdd classes ajoutées"
                );
            }

            // put students in DB and compare with old one
            $csv = fopen($destination . $newFilename, 'r');
            $i = 0;
            $iModif = 0;
            $iAdd = 0;
            while (($data2 = fgetcsv($csv, 0, ';')) !== FALSE) {
                $data = array_map("utf8_encode", $data2);
                // pass the first title line
                if ($i != 0) {
                    // search if student already here
                    // if yes, add , if no update (add or modify)
                    $isGirl = 0;
                    if($data[4]=="F") {
                        $isGirl = 1;
                    }
                    $ine = $data[5];
                    $birthdate = DateTime::createFromFormat('Y-m-d', $data[3]);
                    $refClassgroup = $data[17];
                    $section = $sectionRepository->findOneBy(['abbreviation'=>$data[16]]);
                    $classgroup = $classgroupRepository->findOneBy(['ref'=>$refClassgroup]);
                    $student = $students->findOneBy(['ine'=> $ine]);
                    if(isset($student)) {
                        $student
                            ->setClassgroup($classgroup)
                            ->setLastname($data[0])
                            ->setFirstname($data[2])
                            ->setIsGirl($isGirl)
                            ->setSection($section)
                            ->setDateModif(new DateTime())
                            ->setBirthdate($birthdate)
                        ;
                        $em->persist($student);
                        $iModif++;
                    }
                    else {
                        // add pupils
                        $student = new Student();
                        $student
                            ->setClassgroup($classgroup)
                            ->setLastname($data[0])
                            ->setFirstname($data[2])
                            ->setIne($data[5])
                            ->setIsGirl($isGirl)
                            ->setDateCreate(new DateTime())
                            ->setIsLead(0)
                            ->setXp(5)
                            ->setGold(10)
                            ->setElixir(10)
                            ->setBirthdate($birthdate)
                            ->setSection($section)
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
            return $this->redirectToRoute('school_show', [
                'id' => $school->getId(),
            ]);
        }

        // find all students
        $students = $students->findAll();

        return $this->render('admin/import.html.twig', [
            'form' => $form->createView(),
            'students' => $students,
            'school' => $school,
        ]);
    }
}
