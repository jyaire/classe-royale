<?php

namespace App\Controller;

use App\Entity\Point;
use App\Entity\Product;
use App\Entity\Purchase;
use App\Entity\Reason;
use App\Entity\Student;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Repository\ReasonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/product")
 * @IsGranted("ROLE_USER")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/all/{student}", name="product_index", methods={"GET"}, defaults={"student": null})
     * @IsGranted("ROLE_TEACHER")
     * @param ?Student $student
     */
    public function index(ProductRepository $productRepository, ?Student $student): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
            'student' => $student,
        ]);
    }

    /**
     * @Route("/new", name="product_new", methods={"GET","POST"})
     * @IsGranted("ROLE_TEACHER")
     */
    public function new(Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            if ($this->isGranted('ROLE_TEACHER')) {
                foreach ($this->getUser()->getClassgroups() as $classgroup) {
                    $classg = $classgroup;
                }
                $product
                    ->setClassgroup($classg)
                    ->setCreator($this->getUser())
                    ;
            }
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="product_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_TEACHER")
     */
    public function edit(Request $request, Product $product): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/{student}", name="product_show", methods={"GET"}, defaults={"student": null})
     * @param Product $product
     * @param ?Student $student
     */
    public function show(Product $product, ?Student $student): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
            'student'=> $student,
        ]);
    }

    /**
     * @Route("/pay/{id}/{student}", name="product_pay", methods={"GET"})
     * @param Product $product
     * @param Student $student
     */
    public function pay(Product $product, Student $student): Response
    {
        return $this->render('product/pay.html.twig', [
            'product' => $product,
            'student'=> $student,
        ]);
    }

    /**
     * @Route("/confirm/{id}/{student}", name="product_confirm", methods={"GET"})
     * @param Product $product
     * @param Student $student
     * @param ReasonRepository $reasonRepository
     */
    public function confirm(Product $product, Student $student, ReasonRepository $reasonRepository): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        // add product to student
        $purchase = new Purchase;
        $purchase
            ->setStudent($student)
            ->setProduct($product)
            ->setDate(new \DateTime());
        // add XP and remove price (gold or elixir)
        $student->setXp($student->getXp()+5);
        if ($product->getCurrency() == "gold") {
            $student->setGold($student->getGold()-$product->getPrice());
        }
        else {
            $student->setElixir($student->getElixir()-$product->getPrice());
        }
        // add point and reason
        $point = new Point;
        $sentence = 'Achat de "' . $product->getName() . '"';
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
            ->setQuantity(-$product->getPrice())
            ->setType($product->getCurrency())
            ->setDate(new \DateTime())
            ->setAuthor($this->getUser())
            ->setPurchase($purchase);
        
        $entityManager->persist($student);
        $entityManager->persist($point);
        $entityManager->persist($purchase);
        $entityManager->flush();

        $message = $student->getFirstname() . ' a acheté "' . $product->getName() . '"';
        $this->addFlash('success', $message);
        return $this->redirectToRoute('student_show', [
            'id' => $student->getId(),
        ]);
    }

    /**
     * @Route("/{id}", name="product_delete", methods={"DELETE"})
     * @IsGranted("ROLE_TEACHER")
     */
    public function delete(Request $request, Product $product): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('product_index');
    }
}
