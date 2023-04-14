<?php

namespace App\Controller;

use App\Repository\MainPageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="app_index")
     */
    public function index(MainPageRepository $mainPageRepository): Response
    {
        return $this->render('main/index.html.twig', [
            'page' => $mainPageRepository->find(1),
        ]);
    }

    /**
     * @Route("/optic", name="app_optic")
     */
    public function optic(MainPageRepository $mainPageRepository): Response
    {
        return $this->render('main/index.html.twig', [
            'page' => $mainPageRepository->find(2),
        ]);
    }

    /**
     * @Route("/tomography", name="app_tomography")
     */
    public function tomography(MainPageRepository $mainPageRepository): Response
    {
        return $this->render('main/index.html.twig', [
            'page' => $mainPageRepository->find(3),
        ]);
    }

    /**
     * @Route("/contacts", name="app_contacts")
     */
    public function contacts(MainPageRepository $mainPageRepository): Response
    {
        return $this->render('main/index.html.twig', [
            'page' => $mainPageRepository->find(4),
        ]);
    }

    /**
     * @Route("/sitemap", name="app_sitemap")
     */
    public function sitemap(): Response
    {
        return $this->render('main/sitemap.html.twig', [

        ]);
    }

    /**
     * @Route("/search", name="app_search")
     */
    public function search(): Response
    {
        return $this->render('main/search.html.twig', [

        ]);
    }
}
