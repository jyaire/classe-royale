<?php

namespace App\Controller;

use App\Entity\Point;
use App\Entity\Purchase;
use App\Entity\Reason;
use App\Entity\Student;
use App\Form\PurchaseType;
use App\Repository\PurchaseRepository;
use App\Repository\ReasonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/purchase")
 * @IsGranted("ROLE_USER")
 */
class PurchaseController extends AbstractController
{
    /**
     * @Route("/all/{student}", name="purchase_index", methods={"GET"}, defaults={"student": null})
     * @IsGranted("ROLE_TEACHER")
     * @param ?Student $student
     */
    public function index(PurchaseRepository $purchaseRepository, ?Student $student): Response
    {
        return $this->render('purchase/index.html.twig', [
            'purchases' => $purchaseRepository->findAll(),
            'student' => $student,
        ]);
    }

    /**
     * @Route("/new", name="purchase_new", methods={"GET","POST"})
     * @IsGranted("ROLE_TEACHER")
     */
    public function new(Request $request): Response
    {
        $purchase = new Purchase();
        $form = $this->createForm(PurchaseType::class, $purchase);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($purchase);
            $entityManager->flush();

            return $this->redirectToRoute('purchase_index');
        }

        return $this->render('purchase/new.html.twig', [
            'purchase' => $purchase,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="purchase_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_TEACHER")
     */
    public function edit(Request $request, Purchase $purchase): Response
    {
        $form = $this->createForm(PurchaseType::class, $purchase);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('purchase_index');
        }

        return $this->render('purchase/edit.html.twig', [
            'purchase' => $purchase,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/{student}", name="purchase_show", methods={"GET"}, defaults={"student": null})
     * @param Purchase $purchase
     * @param ?Student $student
     */
    public function show(Purchase $purchase, ?Student $student): Response
    {
        return $this->render('purchase/show.html.twig', [
            'purchase' => $purchase,
            'student'=> $student,
        ]);
    }

    /**
     * @Route("/pay/{id}/{student}", name="purchase_pay", methods={"GET"})
     * @param Purchase $purchase
     * @param Student $student
     */
    public function pay(Purchase $purchase, Student $student): Response
    {
        return $this->render('purchase/pay.html.twig', [
            'purchase' => $purchase,
            'student'=> $student,
        ]);
    }

    /**
     * @Route("/confirm/{id}/{student}", name="purchase_confirm", methods={"GET"})
     * @param Purchase $purchase
     * @param Student $student
     * @param ReasonRepository $reasonRepository
     */
    public function confirm(Purchase $purchase, Student $student, ReasonRepository $reasonRepository): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        // add purchase to student
        $student->addPurchase($purchase);
        // add XP and remove price (gold or elixir)
        $student->setXp($student->getXp()+5);
        if ($purchase->getCurrency() == "gold") {
            $student->setGold($student->getGold()-$purchase->getPrice());
        }
        else {
            $student->setElixir($student->getElixir()-$purchase->getPrice());
        }
        // add point and reason
        $point = new Point;
        $sentence = 'Achat de "' . $purchase->getName() . '"';
        // search if reason already exists
        $search = $reasonRepository->findOneBy(['sentence'=>$sentence]);
        if(!empty($search)) {
            $point->setReason($search);
        } else {
            $reason = new Reason;
            $reason->setSentence($sentence);
            $entityManager->persist($reason);
            $point->setReason($reason);
        }
        $point
            ->setStudent($student)
            ->setQuantity(-$purchase->getPrice())
            ->setType($purchase->getCurrency())
            ->setDate(new \DateTime())
            ->setAuthor($this->getUser());
        
        $entityManager->persist($student);
        $entityManager->persist($point);
        $entityManager->flush();

        $message = $student->getFirstname() . ' a acheté "' . $purchase->getName() . '"';
        $this->addFlash('success', $message);
        return $this->redirectToRoute('student_show', [
            'id' => $student->getId(),
        ]);
    }

    /**
     * @Route("/{id}", name="purchase_delete", methods={"DELETE"})
     * @IsGranted("ROLE_TEACHER")
     */
    public function delete(Request $request, Purchase $purchase): Response
    {
        if ($this->isCsrfTokenValid('delete'.$purchase->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($purchase);
            $entityManager->flush();
        }

        return $this->redirectToRoute('purchase_index');
    }
}
