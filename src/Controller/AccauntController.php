<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccauntController extends AbstractController
{
    /**
     * @Route("/accaunt", name="app_accaunt")
     */
    public function index(): Response
    {
        return $this->render('accaunt/index.html.twig', [
        ]);
    }
}
