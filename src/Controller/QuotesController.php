<?php

namespace App\Controller;

use App\Service\QuotesService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class QuotesController extends AbstractController
{
    #[Route('/quotes', methods: ['POST'])]
    public function check(Request $request, QuotesService $service): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $origin = $data['originZipcode'] ?? null;
        $destination = $data['destinationZipcode'] ?? null;

        if (!$origin || !$destination) {
            return new JsonResponse(['error' => 'Missing parameters'], 400);
        }

        $providers = $service->checkProviders($origin, $destination);

        return new JsonResponse([
            "originZipcode" => $origin,
            "destinationZipcode" => $destination,
            "providers" => $providers
        ]);
    }
}
