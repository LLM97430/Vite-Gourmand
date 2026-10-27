//Controller created thanks to maker-bundle 

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class ViteGourmandController extends AbstractController
{
    #[Route('/vite/gourmand', name: 'app_vite_gourmand')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ViteGourmandController.php',
        ]);
    }
}
