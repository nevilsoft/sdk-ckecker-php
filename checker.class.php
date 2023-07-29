<?php

/**
 * COPYRIGHT Ⓒ 2023 NEVILSOFT - ALL RIGHTS RESERVED.
 * DO NOT EDIT OR MODIFY FROM THE ORIGINAL WITHOUT PERMISSION. PUNISHABLE BY LAW
 * 
 * TERMS AND CONDITIONS:
 * 1. THE USE OF THIS CODE IS PERMITTED WITHOUT MODIFICATION OR MODIFICATION.
 * 2. THIS CODE IS STRICTLY PROHIBITED TO BE SOLD OR PART OF OTHER PRODUCTS. WITHOUT PRIOR PERMISSION FROM NEVILSOFT
 * 3. DO NOT INFRINGE COPYRIGHT OF NEVILSOFT
 * 
 * FACE RESPONSIBILITIES:
 * THE AUTHOR HAS LEFT THAT MUST BE USED EVERY TIME OR THERE IS A PROBLEM FROM THIS CODE.
 * THE AUTHOR MUST COMPENSATE OR REIMBURSE ANY ARISING FROM THE USE OF THIS CODE
 * 
 * ABOUT US
 * WEBSITE: HTTPS://NEVILSOFT.COM
 * EMAIL: CONTACT@NEVILSOFT.COM
 * FACEBOOK: HTTPS://FACEBOOK.COM/NEVILSOFT
 */

namespace nevilsoft;

use Error;

class ApiChecker
{
    private $ApiKey;
    private $Token;
    private $BaseUrl;

    #Constants Error Code
    const PERMANENT_BAN_ERR_CODE = "1100";
    const TWOFA_ERR_CODE = "1202";
    const TEMPORARILY_BANNED_ERR_CODE = "5106";
    const USERNAEM_OR_PASSWORD_INVALID_CODE = "4015";
    const API_KEY_EXPIRED_CODE = "1440";

    #Constants Error Message 
    const PERMANENT_BAN_ERR_MSG = "PERMANENT_BAN";
    const PERMA_BAN_ERR_MSG = "PERMA_BAN";
    const TWOFA_ERR_MSG = "2FA Auth Enabled";
    const AUTH_FAILURE_ERR_MSG = "auth_failure";
    const TIME_BAN_ERR_MSG = "TIME_BAN";
    const LEGACY_BAN_ERR_MSG = "LEGACY_BAN";
    const PBE_LOGIN_TIME_BAN_ERR_MSG = "PBE_LOGIN_TIME_BAN";
    const API_KEY_EXPIRED_ERR_MSG = "API_KEY_EXPIRED";

    #Constants Message 
    const USERNAEM_OR_PASSWORD_INVALID = "username or password invalid";
    const API_KEY_EXPIRED_MSG = "error for owner site plaese contact us";


    function __construct($ApiKey, $Token)
    {
        $this->BaseUrl = "localhost:8080/api";
        $this->ApiKey = $ApiKey;
        $this->Token = $Token;

        $result = $this->Call("POST", $this->BaseUrl . "/valorant/v1/siteverify", array("token" => $this->Token));
        if (!$result["success"]) {
            throw new Error($result["message"]);
        }
    }

    private function EncryptPayload($payload)
    {
        $ivLength = 16;
        $iv = openssl_random_pseudo_bytes($ivLength);

        $encryptedData = openssl_encrypt(
            $payload,
            'AES-256-CBC',
            substr($this->ApiKey, 10, 42),
            OPENSSL_RAW_DATA,
            $iv
        );

        $encryptedBytes = $iv . $encryptedData;

        return base64_encode($encryptedBytes);
    }

    /**
     * this function using to call api by default
     * - Method default "GET"
     * - Url default "null"
     * - Data default "null"
     */
    private function Call($method = "GET", $url = null, $data = null)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->Token,
                'X-API-KEY:' . $this->ApiKey
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($http_code == 200 && ($result = json_decode($response, true))) {
            return $result;
        }
    }

    /** 
     * Check your valorant game account using your Username and Password.
     * 
     * Status Code 
     * - 1100 : PERMANENT_BAN
     * - 4015 : auth_failure
     * - 1202 : 2FA Auth Enabled
     * - 1440 : "Api Key Expired"
     */
    public function CheckBanned($username, $password): array
    {
        $result = $this->Call("POST", $this->BaseUrl . "/valorant/v1/checkban", array(
            "payload" => $this->EncryptPayload(
                json_encode(
                    array(
                        "username" => $username,
                        "password" => $password
                    )
                )
            )
        ));
        return $result;
    }

    /**
     * Coming soon, please keep an eye out for updates from us.
     */
    public function GetNumberOfSkin(): int
    {
        return 0;
    }

    /**
     * Coming soon, please keep an eye out for updates from us.
     */
    public function GetSkinList(): array
    {
        return [];
    }

    /**
     * Coming soon, please keep an eye out for updates from us.
     */
    public function GenerateSkinImages(): array
    {
        return [];
    }

    /**
     * Coming soon, please keep an eye out for updates from us.
     */
    public function GetStoreShop(): array
    {
        return [];
    }

    /**
     * Coming soon, please keep an eye out for updates from us.
     */
    public function GetStoreNightMarket(): array
    {
        return [];
    }

    /**
     * Coming soon, please keep an eye out for updates from us.
     * return started, expired
     */
    public function GetApiKeyInfo(): string
    {
        return "";
    }
}
