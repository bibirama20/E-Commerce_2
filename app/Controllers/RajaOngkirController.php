<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class RajaOngkirController extends Controller
{
    private $apiKey = 'M3Oana6U78d8d8d57a31a1c3E6LlB6tO'; // ganti dengan API key rajaongkir kamu

   public function getKota()
{
    $client = \Config\Services::curlrequest();
    $response = $client->get('https://api.rajaongkir.com/starter/city', [
        'headers' => ['key' => 'M3Oana6U78d8d8d57a31a1c3E6LlB6tO']
    ]);

    $json = json_decode($response->getBody(), true);
    return $this->response->setJSON(['results' => $json['rajaongkir']['results']]);
}


    public function cekOngkir()
    {
        $origin = 78; // Semarang
        $destination = $this->request->getPost('destination');
        $weight = $this->request->getPost('weight');
        $courier = $this->request->getPost('courier');

        $client = \Config\Services::curlrequest();
        $response = $client->post('https://api.rajaongkir.com/starter/cost', [
            'headers' => ['key' => $this->apiKey],
            'form_params' => [
                'origin' => $origin,
                'destination' => $destination,
                'weight' => $weight,
                'courier' => $courier
            ]
        ]);

        $data = json_decode($response->getBody(), true);
        $ongkir = $data['rajaongkir']['results'][0]['costs'][0]['cost'][0]['value'];
        $layanan = $data['rajaongkir']['results'][0]['costs'][0]['service'];
        $kodeKurir = strtoupper($data['rajaongkir']['results'][0]['code']);

        return $this->response->setJSON([
            'success' => true,
            'ongkir' => $ongkir,
            'service' => $kodeKurir . ' - ' . $layanan
        ]);
    }
}
