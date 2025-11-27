<?php

namespace App\Service;

use App\Entity\Provider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

/**
 * Class QuotesService
 *
 * Servicio encargado de simular las respuestas de proveedores externos y almacenar
 * en la base de datos el estado de cada respuesta.
 * 
 * @author Arely Dominguez
 * @date 25-11-2025
 */
class QuotesService
{
    private const STATUS_SUCCESS = 'success';
    private const STATUS_ERROR = 'error';

    private array $providersConfig;
    private HttpClientInterface $httpClient;
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;

    public function __construct(
        array $providersConfig,
        HttpClientInterface $httpClient,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    ) {
        $this->providersConfig = $providersConfig;
        $this->httpClient = $httpClient;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    /**
     * Consulta los proveedores configurados y devuelve un array de resultados.
     *
     * @param string $origin
     * @param string $destination
     * @return array<int, array{provider: string, status: string, response_raw?: string, message?: string}>
     */
    public function checkProviders(string $origin, string $destination): array
    {
        $providerResults = [];

        foreach ($this->providersConfig as $providerData) {
            if (!$this->isProviderConfigValid($providerData)) {
                $this->logger->warning('Provider config incomplete', ['provider' => $providerData]);
                continue;
            }

            $providerEntity = $this->createProviderEntity($providerData);

            $result = $this->callProvider($providerEntity, $origin, $destination);

            $providerResults[] = $result;

            $this->entityManager->persist($providerEntity);
        }

        $this->entityManager->flush();

        return $providerResults;
    }

    /**
     * Valida que la configuración de un proveedor tenga los campos necesarios.
     *
     * @param array $providerData Datos del proveedor
     * @return bool True si tiene 'name' y 'url', false en caso contrario
     */
    private function isProviderConfigValid(array $providerData): bool
    {
        return isset($providerData['name'], $providerData['url']);
    }

    
    /**
     * Crea una entidad Provider a partir de los datos del proveedor.
     *
     * @param array $providerData Datos del proveedor (name y url)
     * @return Provider Instancia de Provider lista para persistir
     */
    private function createProviderEntity(array $providerData): Provider
    {
        $provider = new Provider();
        $provider->setName($providerData['name']);
        $provider->setUrl($providerData['url']);
        return $provider;
    }

    /**
     * Realiza la petición HTTP al proveedor y devuelve el resultado.
     * En caso de error, captura la excepción, marca el proveedor como fallido y registra el log.
     *
     * @param Provider $provider Entidad del proveedor
     * @param string $origin Código postal de origen
     * @param string $destination Código postal de destino
     * @return array{provider: string, status: string, response_raw?: string, message?: string}
     */
    private function callProvider(Provider $provider, string $origin, string $destination): array
    {
        try {
            $response = $this->httpClient->request('POST', $provider->getUrl(), [
                'json' => [
                    'originZipcode' => $origin,
                    'destinationZipcode' => $destination
                ]
            ]);

            $statusCode = $response->getStatusCode();
            $isSuccess = $this->isSuccessStatus($statusCode);
            $provider->setStatus($isSuccess);

            $responseRaw = $response->getContent();

            return [
                'provider' => $provider->getName(),
                'status' => $isSuccess ? self::STATUS_SUCCESS : self::STATUS_ERROR,
                'response_raw' => $responseRaw
            ];

        } catch (\Throwable $e) {
            $provider->setStatus(false);
            $this->logProviderError($provider, $e);

            return [
                'provider' => $provider->getName(),
                'status' => self::STATUS_ERROR,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Determina si un código de estado HTTP indica éxito.
     *
     * @param int $statusCode Código de estado HTTP
     */
    private function isSuccessStatus(int $statusCode): bool
    {
        return $statusCode >= 200 && $statusCode < 300;
    }

    /**
     * Registra un error de petición a un proveedor en el logger.
     *
     * @param Provider $provider Entidad del proveedor que falló
     * @param \Throwable $exception Excepción capturada
     * @return void
     */
    private function logProviderError(Provider $provider, \Throwable $exception): void
    {
        $this->logger->error(
            sprintf('Error calling provider %s', $provider->getName()),
            [
                'exception' => $exception,
                'providerUrl' => $provider->getUrl()
            ]
        );
    }
}
