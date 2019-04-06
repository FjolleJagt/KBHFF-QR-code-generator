<?php
namespace FjolleJagt\kbhffQRCode;

use InvalidArgumentException;
use Endroid\QrCode\QrCode;

class QRCodeGenerator {
    public static $urlBase = "https://www.mobilepay.dk/erhverv/betalingslink/betalingslink-svar?";

    private static $phonenumbers = array(
        "Amager" => "26027",
        "Fælles" => "42990",
        "Nørrebro" => "84267",
        "Østerbro" => "70495"
    );

    /**
     * Generates a Mobilepay QR code, saving it to a file in the ./img/ directory.
     *
     * ./img/ is assumed to exist and be writable.
     *
     * The below interface is just an example of what I would expect to implement; we would change the way the comment
     * field is generated based on our requirements.
     *
     * Caution: Mobilepay URLS are limited to a 25 character comment. Using a long transaction id / afdeling will cause
     * an error to be thrown.
     *
     * @param float|int|string $amount Amount to charge
     * @param string $afdeling The afdeling to which the member belongs
     * @param string $transactionId Unique identifier for this transation
     * @return string Filename of the generated QR code, relative to current directory.
     */
    public static function generate($amount, $afdeling, $transactionId){
        $identifier = "" . $transactionId . "." . $afdeling;
        $filename = "img/" . $identifier . ".png";

        if(!array_key_exists($afdeling, self::$phonenumbers))
            throw new InvalidArgumentException("Don't recognise afdeling " . $afdeling);
        $phonenumber = self::$phonenumbers[$afdeling];

        $mobilepayURL = self::getMobilepayLink($phonenumber, $amount, $identifier);

        $qrCode = new QrCode($mobilepayURL);
        $qrCode->setSize(300);
        $qrCode->writeFile($filename);

        return $filename;
    }

    /**
     * Generates a Mobilepay URL.
     *
     * Based on implementation deduced from https://www.mobilepay.dk/erhverv/betalingslink
     *
     * @param string $phonenumber Phone number to pay to, can only contain numerals
     * @param float|int|string|null $amount Amount to charge. Must be a positive number. Floats are rounded to 2dp.
     *                                      Set to null to omit.
     * @param string $comment Comment, max length is 25. Set to empty to omit.
     * @param bool $lockCommentField If true (default) then prevent user from editing the comment
     * @return string
     */
    public static function getMobilepayLink($phonenumber, $amount, $comment, $lockCommentField=true) {
        return self::$urlBase
            . self::getPhonenumberText($phonenumber)
            . self::getAmountText($amount)
            . self::getCommentText($comment)
            . self::getLockText($lockCommentField);
    }

    private static function getPhonenumberText($phonenumber){
        if(!(is_string($phonenumber) && preg_match("/^[0-9]+$/", $phonenumber) === 1)){
            throw new InvalidArgumentException("Phone number should be a string containing only numbers");
        }

        return sprintf("phone=%s", $phonenumber);
    }

    private static function getAmountText($amount){
        if(is_null($amount))
            return "";
        elseif ($amount < 0)
            throw new InvalidArgumentException("Amount should be positive");
        //Mobilepay's QR code generator doesn't include a decimal point for integer amounts
        elseif (is_integer($amount))
            return sprintf("&amount=%d", $amount);
        else
            return sprintf("&amount=%.2f", $amount);
    }

    private static function getCommentText($comment){
        if(strlen($comment) > 25)
            throw new InvalidArgumentException("Comment must be at most 25 characters long");

        if($comment === "")
            return "";
        else
            return sprintf("&comment=%s", rawurlencode($comment));
    }

    private static function getLockText($lockCommentField){
        if($lockCommentField)
            return "&lock=1";
        else
            return "";
    }
}

?>