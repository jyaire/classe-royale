<?php

namespace App\Controller;

use App\Repository\PointRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LastController extends AbstractController
{
    /**
     * @Route("/last", name="last_index", methods={"GET"})
     * @param PointRepository $pointRepository
     * @return Response
     */
    public function index(PointRepository $pointRepository): Response
    {
        return $this->render('point/index.html.twig', [
            'points' => $pointRepository->findAll(),
        ]);
    }
}
