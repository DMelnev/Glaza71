<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="app_index")
     */
    public function index(): Response
    {
        return $this->render('main/index.html.twig', [

        ]);
    }

    /**
     * @Route("/optic", name="app_optic")
     */
    public function optic(): Response
    {
        return $this->render('main/optic.html.twig', [

        ]);
    }

    /**
     * @Route("/tomography", name="app_tomography")
     */
    public function tomography(): Response
    {
        return $this->render('main/tomography.html.twig', [

        ]);
    }
    /**
     * @Route("/contacts", name="app_contacts")
     */
    public function contacts(): Response
    {
        return $this->render('main/contacts.html.twig', [

        ]);
    }
}
