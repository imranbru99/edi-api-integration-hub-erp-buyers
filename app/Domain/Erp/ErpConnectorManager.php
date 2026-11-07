<?php

namespace App\Domain\Erp;

use App\Domain\Erp\Adapters\JsonApiErpAdapter;
use App\Domain\Erp\Adapters\MockErpAdapter;
use App\Domain\Erp\Contracts\ErpConnectorInterface;
use App\Models\IntegrationConnection;
use Illuminate\Support\Facades\Crypt;
use InvalidArgumentException;

class ErpConnectorManager
{
    public function resolve(IntegrationConnection $connection): ErpConnectorInterface
    {
        $adapter = match ($connection->connector_type) {
            'mock' => new MockErpAdapter,
            'json_api' => new JsonApiErpAdapter,
            'csv', 'xml', 'custom', 'sap', 'oracle', 'dynamics365', 'netsuite', 'odoo', 'business_central' => new JsonApiErpAdapter,
            default => throw new InvalidArgumentException("Unsupported connector type: {$connection->connector_type}"),
        };

        $config = [];
        if ($connection->config_encrypted) {
            $config = json_decode(Crypt::decryptString($connection->config_encrypted), true) ?: [];
        }

        $adapter->connect($config);

        return $adapter;
    }
}
