<?php namespace Config;

use CodeIgniter\Config\BaseService;

class Services extends BaseService
{
    public static function rajaongkir($getShared = true)
    {
        if ($getShared) {
            return self::getSharedInstance('rajaongkir');
        }

        $config = new \Config\RajaOngkir();
        $options = ['baseURI' => $config->baseURI, 'timeout' => 5];
        return service('curlrequest', $options);
    }
}
