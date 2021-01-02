<?php
class Safety {

    const FILTER_WRAPPERS = ['0123456789', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz', '!#%&=?@$+']; // 0 = numbers, 1 = upper, 2 = lower, 3 = special

    static private $secret = null;
    static private $hashMethod = 'WHIRLPOOL';
    static private $cryptoMethod = 'AES-256-CFB';
    static private $cryptoIvLength = 16;

    static public function setSecret (string $secret) {
        self::$secret = hash(self::$hashMethod, $secret);
    }

    static public function setHashMethod (string $method) {
        if (in_array($method, hash_algos())) {
            self::$hashMethod = strtoupper($method);
            return true;
        } else {
            return false;
        }
    }

    static public function setCryptoMethod (string $method) {
        if (in_array($method, openssl_get_cipher_methods())) {
            self::$cryptoMethod = strtoupper($method);
            self::$cryptoIvLength = openssl_cipher_iv_length(self::$cryptoMethod);
            return true;
        } else {
            return false;
        }
    }

    static public function hash ($input) {
        $stringify = serialize($input);
        return hash(self::$hashMethod, $stringify);
    }

    static public function safeHash ($input, string $secret = null) {
        $stringify = serialize($input);
        return hash_hmac(self::$hashMethod, $stringify, ($secret ?? self::$secret));
    }

    static public function encrypt ($input, string $secret = null, bool $cloneable = false) {
        $packet = [null, null]; // Encryption , IV
        $secret = $secret ?? self::$secret;
        $stringify = serialize($input);
        if ($cloneable) {
            $packet[1] = substr(hash_hmac('whirlpool', $stringify, $secret), 0, self::$cryptoIvLength);
        } else {
            $packet[1] = self::random(self::$cryptoIvLength, '*012');
        }
        $packet[0] = openssl_encrypt($stringify, self::$cryptoMethod, $secret, 0, $packet[1]);
        return base64_encode(serialize($packet));
    }

    static public function decrypt ($input, string $secret = null) {
        $secret = $secret ?? self::$secret;
        $packet = unserialize(base64_decode($input));
        list($encData, $IV) = $packet;
        $data = openssl_decrypt($encData, self::$cryptoMethod, $secret, 0, $IV);
        return unserialize($data);
    }

    static public function random (int $length = 12, string $filter = '*0123') {
        if (substr($filter,0,1)=='*'){
            $selector = '';
            foreach (str_split(substr($filter, 1), 1) as $group) {
                if (array_key_exists($group, self::FILTER_WRAPPERS)){
                    $selector .= self::FILTER_WRAPPERS[$group];
                }
            }
        } else {
            $selector = trim($filter);
        }
        $random = '';
        while (strlen($random)<$length) {
            $random .= $selector[rand(0, strlen($selector)-1)];
        }
        return $random;
    }

}
