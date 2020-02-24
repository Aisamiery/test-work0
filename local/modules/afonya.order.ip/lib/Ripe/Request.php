<?php
/**
 * Created by PhpStorm.
 * Author: Shabalin Pavel
 * Email: aisamiery@gmail.com
 * Date: 24.02.2020
 */
declare(strict_types=1);

namespace Afonay\Ripe;


use Bitrix\Main\Data\Cache;
use Bitrix\Main\SystemException;
use Bitrix\Main\Web\Json;

class Request
{
    public function getAddressInfo(string $ip): Response
    {
        $cache = Cache::createInstance();

        if ($cache->initCache(3600, md5('ripe_ip_' . $ip), 'ripe_ip')) {
            $data = $cache->getVars();
        } elseif ($cache->startDataCache()) {
            $data = $this->sendRequest($ip);
            $cache->endDataCache($data);
        }

        $response = new Response($data);
        return $response;
    }

    /**
     * @param string $ip
     * @return mixed
     * @throws SystemException
     * @throws \Bitrix\Main\ArgumentException
     */
    protected function sendRequest(string $ip): array
    {
        $url = 'https://rest.db.ripe.net/search.json?' . http_build_query(['query-string' => $ip]);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:54.0) Gecko/20100101 Firefox/73.0');
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        $out = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($code !== 200) {
            // Какая то ошибка
            throw new SystemException('При запросе RIPE возникла ошибка. IP: ' . $ip);
        }

        return Json::decode($out) ?: [];
    }
}