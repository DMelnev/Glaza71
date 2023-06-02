<?php

namespace App\Controller;

use App\Service\UpdateSitemap;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class AdditionalController extends AbstractController
{
    /**
     * @Route("/update_sitemap", name="app_update_sitemap")
     */
    public function index(UpdateSitemap $updateSitemap): JsonResponse
    {
        $updateSitemap->execute();
        return $this->json([
            'message' => 'It has done',
        ]);
    }
}
