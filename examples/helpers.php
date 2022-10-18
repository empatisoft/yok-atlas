<?php
/**
 * Author: Onur KAYA
 * Date: 17.10.2022 13:22
 */
if(!function_exists('getRequest')) {
    /**
     * @param string $url
     * @param array $headers
     * @param bool $json
     * @return mixed|string
     */
    function getRequest(string $url, array $headers = [], bool $json = true) {
        try {
            $client = new GuzzleHttp\Client(['verify' => false ]);
            $parameters = [];
            if(!empty($headers))
                $parameters['headers'] = $headers;

            $response = $client->request('GET', $url, $parameters);
            if($response->getStatusCode() == 200) {
                $body = $response->getBody()->getContents();
                $result = $body;
                if(!empty($body) && $json == true) {
                    $decode = json_decode($body, true);
                    if(json_last_error() === JSON_ERROR_NONE)
                        $result = $decode;
                }
            } else
                $result = ['success' => false, 'code' => $response->getStatusCode(), 'message' => $response->getReasonPhrase()];

        } catch (\GuzzleHttp\Exception\GuzzleException $exception) {
            $result = ['success' => false, 'code' => $exception->getCode(), 'message' => $exception->getMessage()];
        }

        return $result;
    }
}

if(!function_exists('postRequest')) {
    /**
     * @param string $url
     * @param array $data
     * @param string $type (form_params veya json)
     * @param array $headers
     * @return array
     */
    function postRequest(string $url, array $data, string $type = 'form_params', array $headers = []):array {

        try {
            $result = [];
            $client = new GuzzleHttp\Client(['verify' => false ]);
            $parameters = [];
            if(!empty($headers))
                $parameters['headers'] = $headers;

            $parameters[$type] = $data;
            $response = $client->request('POST', $url, $parameters)->getBody()->getContents();

            if(!empty($response)) {
                $response = json_decode($response, true);
                if(json_last_error() === JSON_ERROR_NONE)
                    $result = $response;

            }

        } catch (\GuzzleHttp\Exception\GuzzleException $exception) {
            $result = ['success' => false, 'code' => $exception->getCode(), 'message' => $exception->getMessage()];
        }
        return $result;
    }
}

if(!function_exists('json')) {
    /**
     * @param array|object $data
     * @param int $status
     * @param bool $echo
     * @return false|string
     */
    function json($data, int $status = 200, bool $echo = true) {
        $json = json_encode($data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        if($echo == true) {
            http_response_code($status);
            header('Content-Type: application/json; charset: utf-8');
            echo $json;
            exit();
        } else
            return $json;

    }
}

if(!function_exists('pd')) {
    /**
     * @param $data
     * @param bool $pre
     * print_r için
     */
    function pd($data, bool $pre = true) {
        if($pre == true)
            echo '<pre>';

        print_r($data);
        die;
    }
}

if(!function_exists('parseRows')) {
    /**
     * @param $elements
     * @return array
     */
    function parseRows($elements): array
    {
        $result = [];
        foreach ($elements as $element) {
            $result[] = $element->nodeValue;
        }
        return $result;
    }
}


if(!function_exists('parseTable')) {
    /**
     * @param string $content
     * @return array
     */
    function parseTable(string $content): array
    {
        $result = [];
        $DOM = new DOMDocument();
        $DOM->loadHTML( mb_convert_encoding($content, "HTML-ENTITIES", "UTF-8"));
        $items = $DOM->getElementsByTagName('tr');
        foreach ($items as $node) {
            $result[] = parseRows($node->childNodes);
        }
        return $result;
    }
}