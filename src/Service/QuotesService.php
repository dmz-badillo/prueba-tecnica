<?php

namespace App\Service;

use App\Entity\Provider;
use App\Repository\ProviderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

/**
 * Class QuotesService
 *
 * Servicio encargado de simular las respuestas de diferentes proveedores externos utilizando
 * URLs generadas por Webhook.site. También se encarga de guardar en la base de datos la 
 * información del proveedor y el estadode la respuesta obtenida.
 * 
 * @author Arely Dominguez
 * @date 25-11-2025
 */
class QuotesService
{
    private array $providersConfig;
    private HttpClientInterface $httpClient;
    private EntityManagerInterface $em;
    private LoggerInterface $logger;

    public function __construct(
        array $providersConfig,
        HttpClientInterface $httpClient,
        EntityManagerInterface $em,
        LoggerInterface $logger
    ) {
        $this->providersConfig = $providersConfig;
        $this->httpClient = $httpClient;
        $this->em = $em;
        $this->logger = $logger;
    }

    /**
     * Consulta los proveedores configurados y guarda el resultado en la base de datos.
     *
     * @param string $origin
     * @param string $destination
     * @return array
     */
    public function checkProviders(string $origin, string $destination): array
    {
        $results = [];

        foreach ($this->providersConfig as $providerData) {

            if (!isset($providerData['url'], $providerData['name'])) {
                $this->logger->warning('Provider config incomplete', ['provider' => $providerData]);
                continue;
            }

            $providerName = $providerData['name'];
            $providerUrl = $providerData['url'];

            $providerEntity = new Provider();
            $providerEntity->setName($providerName);
            $providerEntity->setUrl($providerUrl);

            try {
                // Simulación de request
                $response = $this->httpClient->request('POST', $providerUrl, [
                    'json' => [
                        'originZipcode' => $origin,
                        'destinationZipcode' => $destination
                    ]
                ]);

                $statusCode = $response->getStatusCode();
                $providerEntity->setStatus($statusCode >= 200 && $statusCode < 300);

                $responseData = $response->getContent(); // texto crudo
                $results[] = [
                    'provider' => $providerName,
                    'status' => $statusCode >= 200 && $statusCode < 300 ? 'success' : 'error',
                    'response_raw' => $responseData
                ];

            } catch (\Throwable $e) {
                $providerEntity->setStatus(false);

                $results[] = [
                    'provider' => $providerName,
                    'status' => 'error',
                    'message' => $e->getMessage()
                ];

                // Log del error
                $this->logger->error("Error calling provider $providerName", ['exception' => $e]);
            }

            // Persistir el registro en BD
            $this->em->persist($providerEntity);
        }

        $this->em->flush();

        return $results;
    }
}
