<?php

namespace App\Provider;

use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class GenericProvider
 *
 * Implementación genérica de un proveedor de cotizaciones. Se encarga de enviar 
 * solicitudes HTTP a la URL del proveedor y retornar el resultado de la cotización, 
 * simulando éxito o error.
 *
 * @author Arely Dominguez
 * @date 25-11-2025
 */
class GenericProvider implements ProviderInterface
{
    private string $name;
    private string $url;
    private HttpClientInterface $client;

    public function __construct(string $name, string $url, HttpClientInterface $client)
    {
        $this->name = $name;
        $this->url = $url;
        $this->client = $client;
    }

    /**
     * Envía una solicitud al proveedor para consultar una cotización
     *
     * @param string $origin Código postal de origen.
     * @param string $destination Código postal de destino.
     *
     * @return array Arreglo con la información del proveedor
     */
    public function checkQuote(string $origin, string $destination): array
    {
        try {
            // Simulación de llamada HTTP
            $response = $this->client->request('POST', $this->url, [
                'json' => [
                    'originZipcode' => $origin,
                    'destinationZipcode' => $destination
                ]
            ]);

            $statusCode = $response->getStatusCode();
            $status = $statusCode >= 200 && $statusCode < 300 ? 'success' : 'error';
            $responseContent = $response->getContent(false);

        } catch (\Exception $e) {
            $status = 'error';
            $responseContent = $e->getMessage();
        }

        return [
            'provider' => $this->name,
            'status' => $status,
            'response_raw' => $responseContent
        ];
    }
}
