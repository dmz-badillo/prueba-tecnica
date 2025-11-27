<?php

namespace App\Provider;

/**
 * Interface ProviderInterface
 * 
 * Este interfaz permite que distintos proveedores sean intercambiables
 * dentro del sistema, asegurando consistencia en la forma de consultar cotizaciones.
 * 
 * @author Arely Dominguez
 * @date 25-11-2025
 */
interface ProviderInterface
{
    public function checkQuote(string $origin, string $destination): array;
}
