<?php

namespace App\Util;

/**
 * Class Iota
 * @package App\Util
 */
class Iota
{
    private $nodeUrl = '';

    /**
     * Iota constructor.
     *
     * @param string $nodeUrl
     */
    public function __construct($nodeUrl = '')
    {
        // Set Node url
        if ($nodeUrl) {
            $this->nodeUrl = $nodeUrl;
        }elseif (config('services.iota.node_url')) {
            $this->nodeUrl = config('services.iota.node_url');
        }else {
            $this->nodeUrl = 'https://iotanode.prizziota.com/';
        }
    }

    /**
     * Generate Seed
     * @return string
     */
    public function generateSeed()
    {
        $seed = '';
        $allowed_characters = [
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'M',
            'N',
            'O',
            'P',
            'Q',
            'R',
            'S',
            'T',
            'U',
            'V',
            'W',
            'X',
            'Y',
            'Z',
            '9',
        ];

        for ($i = 0; $i < 81; $i++) {
            // Cryptographically secure. (7.1 + built in)
            // http://php.net/manual/en/function.random-int.php
            $seed .= $allowed_characters[random_int(0, count($allowed_characters) - 1)];
        }

        return $seed;
    }

    /**
     * Generate New Address
     *
     * @param $seed
     *
     * @return string
     */
    public function generateAddress($seed, $count)
    {
        $address = trim(shell_exec("python " . public_path('files') . "/iota_address_generator.py " . $seed . " " . $count));

        return $address;
    }


    /**
     * Get IOTA balance for an address
     *
     * @param        $address
     * @param string $unit
     *
     * @return array|float|mixed|null|\stdClass
     */
    public function getBalanceByAddress($address, $unit = 'MI')
    {
        $balance = $this->call([
            'URL'    => $this->nodeUrl,
            "METHOD" => "POST",
            'DATA'   => [
                "command"   => "getBalances",
                "addresses" => [
                    $address
                ],
                "threshold" => 100
            ]
        ]);

        if ($balance) {
            $balance = $balance->balances;
            $amountIota = $balance ? $balance['0'] : null;

            if ($balance) {

                $amountMiota = doubleval($amountIota / 1000000);

                if (strtoupper($unit) == 'MI') {
                    return $amountMiota;
                }elseif (strtoupper($unit) == 'I') {
                    return $amountIota;
                }else {
                    return [
                        'MI' => $amountMiota,
                        'I'  => $amountIota,
                    ];
                }
            }
        }

        return $balance;
    }

    /**
     * Get Price in IOTA/MIOTA eq to USD
     *
     * @param        $amountUsd
     * @param string $unit
     *
     * @return array|float|null
     */
    public function getPrice($amountUsd, $unit = 'MI')
    {
        $mIotaPrice = $this->call([
            "URL"     => "https://api.coinmarketcap.com/v1/ticker/iota/",
            "ALLDATA" => false
        ]);

        if ($mIotaPrice && isset($mIotaPrice[0]) && isset($mIotaPrice[0]->price_usd)) {
            $mIotaPriceUsd = (double)$mIotaPrice[0]->price_usd;

            if ($mIotaPriceUsd > 0 && $amountUsd > 0) {
                $amountMiota = doubleval($amountUsd / $mIotaPriceUsd);
                $amountIota = $this->convertToUnit($amountMiota, 'MI', 'I');

                if (strtoupper($unit) == 'MI') {
                    return $amountMiota;
                }elseif (strtoupper($unit) == 'I') {
                    return $amountIota;
                }else {
                    return [
                        'USD_PER_IOTA' => ($mIotaPrice[0]->price_usd / 1000000),
                        'MI'           => $amountMiota,
                        'I'            => $amountIota,
                    ];
                }
            }
        }

        return null;
    }

    /**
     * @param $amount
     * @param $fromCurrency
     * @param $toCurrency
     *
     * @return float
     */
    public function convertCurrency($amount, $fromCurrency, $toCurrency)
    {
        try{
            $url = "https://finance.google.com/finance/converter?a=$amount&from=$fromCurrency&to=$toCurrency";
            $data = $this->call([
                "URL"     => $url,
                'ALLDATA' => true
            ]);

            preg_match("/<span class=bld>(.*)<\/span>/", $data->_RESPONSE, $converted);
            $converted = trim(preg_replace("/[^0-9.]/", "", $converted[1]));

            return round($converted, 3);
        }catch (\Exception $e){

        }

        return null;
    }

    /**
     * Convert IOTA amount
     *
     * @param $amount
     * @param $fromUnit
     * @param $toUnit
     *
     * @return int
     */
    public function convertToUnit($amount, $fromUnit, $toUnit)
    {
        // Final amount
        $finalAmount = $fromUnit == $toUnit ? $amount : 0;

        // Get IOTA units
        $iotaUnits = config("services.iota.units");

        if ( ! $finalAmount) {
            if (isset($iotaUnits[$fromUnit]) && isset($iotaUnits[$toUnit])) {

                $convertedAmount = ($amount * $iotaUnits[$fromUnit]) / $iotaUnits[$toUnit];

                $finalAmount = number_format($convertedAmount, 6, '.', '');

                $finalAmount = $finalAmount > 0 ? $finalAmount : number_format($convertedAmount, 9, '.', '');
                $finalAmount = $finalAmount > 0 ? $finalAmount : number_format($convertedAmount, 12, '.', '');
                $finalAmount = $finalAmount > 0 ? $finalAmount : number_format($convertedAmount, 16, '.', '');
            }
        }

        return rtrim(rtrim($finalAmount, 0), '.');
    }


    /**
     * Convert value to biggest unit
     *
     * @param $amountIota
     *
     * @return array
     */
    public function unit($amountIota, $lowest = 0.1, $return = 'text')
    {
        $finalAmount = [
            'amount' => $amountIota,
            'unit'   => 'I'
        ];

        // Get IOTA units
        $iotaUnits = config("services.iota.units");

        uasort($iotaUnits, function ($a, $b){
            return $a < $b;
        });

        foreach ($iotaUnits as $iotaUnit => $amountInIota) {
            $amount = $this->convertToUnit($amountIota, 'I', $iotaUnit);
            if ($amount >= $lowest) {

                $finalAmount = [
                    'amount' => $amount,
                    'unit'   => $iotaUnit
                ];

                break;
            }
        }

        if ($return == 'text') {
            $finalAmount = $finalAmount['amount'] . ' ' . $finalAmount['unit'];
        }

        return $finalAmount;
    }


    /**
     * Get working node
     *
     * @param bool $default
     *
     * @return mixed|string
     */
    public function getWorkingNode($check = true)
    {
        $nodes = config("services.iota.nodes");
        $response = [];

        if ($check) {
            foreach ($nodes as $node) {

                try{
                    $response = $this->call([
                        'URL'                  => $node,
                        'METHOD'               => 'GET',
                        'SKIP_DEFAULT_HEADERS' => true,
                    ]);

                }catch (\Exception $e){

                }

                if ($response) {
                    return $node;
                }
            }
        }

        return $this->nodeUrl;
    }

    /**
     * Call with Curl
     *
     * @param array $userData
     *
     * @return mixed|\stdClass
     * @throws \Exception
     */
    public function call($userData = [])
    {
        if (is_string($userData)) {
            $userData = ['URL' => $userData];
        }

        $request = array_merge([
            'CHARSET'     => 'UTF-8',
            'METHOD'      => 'GET',
            'URL'         => '/',
            'HEADERS'     => array(),
            'DATA'        => array(),
            'FAILONERROR' => false,
            'RETURNARRAY' => false,
            'ALLDATA'     => false
        ], $userData);


        // Send & accept JSON data
        $defaultHeaders = array();

        if (isset($request['SKIP_DEFAULT_HEADERS']) && $request['SKIP_DEFAULT_HEADERS']) {

        }else {
            $defaultHeaders[] = 'Content-Type: application/json; charset=' . $request['CHARSET'];
            $defaultHeaders[] = 'Accept: application/json';
        }


        $headers = array_merge($defaultHeaders, $request['HEADERS']);

        $url = $request['URL'];

        // cURL setup
        $ch = curl_init();
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL            => $url,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_CUSTOMREQUEST  => strtoupper($request['METHOD']),
            CURLOPT_ENCODING       => '',
            CURLOPT_USERAGENT      => 'PWI/PHP',
            CURLOPT_FAILONERROR    => $request['FAILONERROR'],
            CURLOPT_VERBOSE        => $request['ALLDATA'],
            CURLOPT_HEADER         => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        );

        // Checks if DATA is being sent
        if ( ! empty($request['DATA'])) {
            if (is_array($request['DATA'])) {
                $options[CURLOPT_POSTFIELDS] = json_encode($request['DATA']);
            }else {
                // Detect if already a JSON object
                json_decode($request['DATA']);
                if (json_last_error() == JSON_ERROR_NONE) {
                    $options[CURLOPT_POSTFIELDS] = $request['DATA'];
                }else {
                    throw new \Exception('DATA malformed.');
                }
            }
        }

        curl_setopt_array($ch, $options);

        $response = curl_exec($ch);

        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

        // Data returned
        $result = json_decode(substr($response, $headerSize), $request['RETURNARRAY']);

        // Headers
        $info = array_filter(array_map('trim', explode("\n", substr($response, 0, $headerSize))));

        foreach ($info as $k => $header) {
            if (strpos($header, 'HTTP/') > -1) {
                $_INFO['HTTP_CODE'] = $header;
                continue;
            }

            list($key, $val) = explode(':', $header);
            $_INFO[trim($key)] = trim($val);
        }


        // cURL Errors
        $_ERROR = array('NUMBER' => curl_errno($ch), 'MESSAGE' => curl_error($ch));

        curl_close($ch);

        if ($_ERROR['NUMBER']) {
            throw new \Exception('ERROR #' . $_ERROR['NUMBER'] . ': ' . $_ERROR['MESSAGE']);
        }

        // Send back in format that user requested
        if ($request['ALLDATA']) {
            if ($request['RETURNARRAY']) {
                $result['_ERROR'] = $_ERROR;
                $result['_RESPONSE'] = $response;
            }else {
                $result = $result ? $result : new \stdClass();
                $result->_ERROR = $_ERROR;
                $result->_RESPONSE = $response;
            }

            return $result;
        }else {
            return $result;
        }
    }
}