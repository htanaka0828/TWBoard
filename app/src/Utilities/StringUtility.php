<?php
namespace TWB\Utilities;


class StringUtility
{
    /**
     * @param $str
     * @return mixed
     */
    public static function camelize($str) {
        $str = ucwords($str, '_');
        return str_replace('_', '', $str);
    }

    /**
     * @param $str
     * @return string
     */
    public static function snakize($str) {
        $str = preg_replace('/[a-z]+(?=[A-Z])|[A-Z]+(?=[A-Z][a-z])/', '\0_', $str);
        return strtolower($str);
    }

    /**
     * @param $str
     * @return string
     */
    public static function upnize($str) {
        return strtoupper($str);
    }

    /**
     * @param $str
     * @return string
     */
    public static function lowernize($str) {
        return strtolower($str);
    }

    /**
     * @param $bearer
     * @return string
     */
    public static function bearerToken($bearer) {
        $pattern = '/^Bearer\s/';
        return preg_replace($pattern, '', $bearer);
    }

    /**
     * @param string $token
     * @return bool
     */
    public static function isBearerToken($token) {
        $pattern = '/^Bearer\s/';
        return (preg_match($pattern, $token));
    }

    /**
     * @param $length
     * @return string
     */
    public static function generateRandomString($length = 16) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * @param string $walletAddress
     * @return string
     */
    public static function formatWalletAddress(string $walletAddress) {
        $walletAddressPrefix = substr($walletAddress, 0, 10);
        $walletAddressSufix = substr($walletAddress, -10);
        return $walletAddressPrefix . '...' . $walletAddressSufix;
    }
}