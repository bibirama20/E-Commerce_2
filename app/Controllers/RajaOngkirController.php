<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use GuzzleHttp\Client;

class RajaOngkirController extends Controller
{
    private $client;
    private $apiKey;

    public function __construct()
    {
        helper(['number', 'form']);
        $this->client = new Client();
        $this->apiKey = env('COST_KEY');
    }

    public function getLocation()
    {
        $search = $this->request->getGet('search');

        $response = $this->client->request(
            'GET',
            'https://rajaongkir.komerce.id/api/v1/destination/domestic-destination?search=' . $search . '&limit=50', [
                'headers' => [
                    'accept' => 'application/json',
                    'key'    => $this->apiKey,
                ],
            ]
        );

        $body = json_decode($response->getBody(), true);
        return $this->response->setJSON($body['data']);
    }
public function getCost()
{
    $destination = $this->request->getGet('destination');
    $weight = $this->request->getGet('weight');

    log_message('debug', '💡 Berat dari AJAX: ' . $weight);

    if (!$weight || $weight < 1) {
        $weight = 1000;
    }

    $couriers = ['jne', 'pos', 'tiki', 'sicepat', 'anteraja', 'sap', 'jnt']; // ✅ tambahkan J&T

    $allResults = [];

    foreach ($couriers as $courier) {
        try {
            $response = $this->client->request(
                'POST',
                'https://rajaongkir.komerce.id/api/v1/calculate/domestic-cost', [
                    'multipart' => [
                        ['name' => 'origin', 'contents' => '64999'],
                        ['name' => 'destination', 'contents' => $destination],
                        ['name' => 'weight', 'contents' => $weight],
                        ['name' => 'courier', 'contents' => $courier]
                    ],
                    'headers' => [
                        'accept' => 'application/json',
                        'key' => $this->apiKey,
                    ],
                ]
            );

            $body = json_decode($response->getBody(), true);

            if (isset($body['data']) && is_array($body['data'])) {
                foreach ($body['data'] as $item) {
                    $allResults[] = $item;
                }
            }

        } catch (\Exception $e) {
            log_message('error', "Courier $courier gagal: " . $e->getMessage());
            continue;
        }
    }

    log_message('debug', '📦 Semua layanan: ' . json_encode($allResults));

    return $this->response->setJSON($allResults);
}

}
