<?php

namespace App\Controller;

use App\Service\QuotesService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * QuotesController
 *
 * Controlador para manejar solicitudes de cotizaciones de envío de paqueterias.
 *
 * @author Arely Dominguez
 * @date 26/11/2025
 */
class QuotesController extends AbstractController
{
    /**
     * Endpoint para consultar cotizaciones de proveedores de paquetería.
     *
     * @param Request $request La solicitud HTTP que contiene los códigos postales en el body.
     * @param QuotesService $service Servicio que maneja la lógica de obtención de infomación de proveedores.
     *
     * @return JsonResponse Respuesta en formato JSON con los proveedores y sus cotizaciones,
     *         o un mensaje de error si falta algún parámetro.
     */
    #[Route('/api/v1/quotes', methods: ['POST'])]
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
