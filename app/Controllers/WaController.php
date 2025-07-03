<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;

class WaController extends BaseController
{
    public function send()
    {
        $no_hp   = $this->request->getPost('no_hp');
        $nama    = $this->request->getPost('nama');
        $alamat  = $this->request->getPost('alamat');
        $layanan = json_decode($this->request->getPost('layanan'), true);
        $items   = json_decode($this->request->getPost('items'), true);
        $total   = number_format((int) $this->request->getPost('total'), 0, ',', '.');

        $pesan = "*Pesanan Baru dari $nama*\n";
        $pesan .= "📍 *Alamat:* $alamat\n";
        $pesan .= "🚚 *Layanan:* {$layanan['description']} ({$layanan['etd']} hari)\n\n";
        $pesan .= "🛒 *Detail Pesanan:*\n";

        foreach ($items as $item) {
            $subtotal = number_format($item['price'] * $item['quantity'], 0, ',', '.');
            $pesan .= "- {$item['name']} ({$item['quantity']}x) : Rp $subtotal\n";
        }

        $pesan .= "\n💰 *Total:* Rp $total\n\n";
        $pesan .= "Terima kasih telah berbelanja di toko kami! 🙏";

        try {
            $token  = getenv('WABLAS_TOKEN');
            $url    = getenv('WABLAS_URL');

            $client = \Config\Services::curlrequest();
            $response = $client->request('POST', $url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type'  => 'application/json',
                ],
                'json' => [
                    'phone' => $no_hp,
                    'message' => $pesan
                ]
            ]);

            // Log hasil respon Wablas untuk debug
            log_message('debug', 'WA Response: ' . $response->getBody());

            return $this->response->setJSON(['success' => true, 'message' => 'Pesan berhasil dikirim']);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR)
                                  ->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
