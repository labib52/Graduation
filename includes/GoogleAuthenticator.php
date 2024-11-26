<?php
class PHPGangsta_GoogleAuthenticator
{
    protected $_codeLength = 6;

    public function createSecret($secretLength = 16)
    {
        $validChars = $this->_getBase32LookupTable();

        $secret = '';
        for ($i = 0; $i < $secretLength; $i++) {
            $secret .= $validChars[array_rand($validChars)];
        }
        return $secret;
    }

    public function getQRCodeGoogleUrl($name, $secret, $title = null, $params = [])
    {
        $urlencoded = urlencode("otpauth://totp/".$name."?secret=".$secret);
        if ($title) {
            $urlencoded .= urlencode("&issuer=".$title);
        }
        foreach ($params as $key => $value) {
            $urlencoded .= urlencode("&$key=$value");
        }
        return "https://chart.googleapis.com/chart?chs=200x200&chld=M|0&cht=qr&chl=".$urlencoded;
    }

    public function getCode($secret, $timeSlice = null)
    {
        if ($timeSlice === null) {
            $timeSlice = floor(time() / 30);
        }

        $secretkey = $this->_base32Decode($secret);

        $time = chr(0).chr(0).chr(0).chr(0).pack('N*', $timeSlice);
        $hm = hash_hmac('SHA1', $time, $secretkey, true);
        $offset = ord(substr($hm, -1)) & 0x0F;
        
        $hashpart = substr($hm, $offset, 4);

        $value = unpack("N", $hashpart)[1] & 0x7FFFFFFF;
        $modulo = pow(10, $this->_codeLength);

        return str_pad($value % $modulo, $this->_codeLength, '0', STR_PAD_LEFT);
    }

    public function verifyCode($secret, $code, $discrepancy = 1, $currentTimeSlice = null)
    {
        if ($currentTimeSlice === null) {
            $currentTimeSlice = floor(time() / 30);
        }

        for ($i = -$discrepancy; $i <= $discrepancy; $i++) {
            $calculatedCode = $this->getCode($secret, $currentTimeSlice + $i);
            if ($calculatedCode == $code) {
                return true;
            }
        }

        return false;
    }

    protected function _getBase32LookupTable()
    {
        return [
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', //  7
            'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', // 15
            'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', // 23
            'Y', 'Z', '2', '3', '4', '5', '6', '7', // 31
            '=',  // padding char
        ];
    }

    protected function _base32Decode($secret)
    {
        if (empty($secret)) return '';

        $base32chars = $this->_getBase32LookupTable();
        $base32charsFlipped = array_flip($base32chars);

        $paddingCharCount = substr_count($secret, '=');
        $allowedValues = [6, 4, 3, 1, 0];
        if (!in_array($paddingCharCount, $allowedValues)) return false;

        $secret = str_replace('=', '', $secret);
        $secret = str_split($secret);
        $binaryString = '';
        
        foreach ($secret as $char) {
            $binaryString .= str_pad(base_convert($base32charsFlipped[$char], 10, 2), 5, '0', STR_PAD_LEFT);
        }

        $eightBits = str_split($binaryString, 8);
        $decoded = '';
        foreach ($eightBits as $bits) {
            $decoded .= chr(base_convert($bits, 2, 10));
        }

        return $decoded;
    }
}
