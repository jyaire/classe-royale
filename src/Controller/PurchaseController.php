<?php

namespace App\Controller;

use App\Entity\Purchase;
use App\Entity\Student;
use App\Form\PurchaseType;
use App\Repository\PurchaseRepository;
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
     * @Route("/", name="purchase_index", methods={"GET"})
     * @IsGranted("ROLE_TEACHER")
     */
    public function index(PurchaseRepository $purchaseRepository): Response
    {
        return $this->render('purchase/index.html.twig', [
            'purchases' => $purchaseRepository->findAll(),
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
     */
    public function confirm(Purchase $purchase, Student $student): Response
    {
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
