<?php

namespace MobilepayQRGenerator;
include "../src/MobilepayQRGenerator.php";

use PHPUnit\Framework\TestCase;

class MobilepayLinkGenerationTest extends TestCase {

    /** @test It escapes spaces and other entities correctly  */
    public function escapesSpaces(){
        $actual = QRCodeGenerator::getMobilepayLink("0", 0, "?@<{ing weird characters");
        $expected = QRCodeGenerator::$urlBase . "phone=0&amount=0&comment=%3F%40%3C%7Bing%20weird%20characters&lock=1";
        $this->assertSame($expected, $actual);
    }

    /** @test It formats integers without a decimal point, and formats floats with 2 decimal places
     *  The official online QR generator truncates integers so we implement this behaviour as well.
     */
    public function formatsDecimalsCorrectly(){
        $actual = QRCodeGenerator::getMobilepayLink("0", 10, "0");
        $expected = QRCodeGenerator::$urlBase . "phone=0&amount=10&comment=0&lock=1";
        $this->assertSame($expected, $actual);

        $actual = QRCodeGenerator::getMobilepayLink("0", 20.5, "0");
        $expected = QRCodeGenerator::$urlBase . "phone=0&amount=20.50&comment=0&lock=1";
        $this->assertSame($expected, $actual);

        $actual = QRCodeGenerator::getMobilepayLink("0", 20.50, "0");
        $expected = QRCodeGenerator::$urlBase . "phone=0&amount=20.50&comment=0&lock=1";
        $this->assertSame($expected, $actual);
    }

    /**
     * @test It errors on negative amounts
     */
    public function errorsOnNegativeAmount(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Amount should be positive");
        QRCodeGenerator::getMobilepayLink("0", "-5", "0");
    }

    /**
     * @test It can parse string amounts
     */
    public function parsesStringAmounts(){
        $actual = QRCodeGenerator::getMobilepayLink("0", "10", "0");
        $expected = QRCodeGenerator::$urlBase . "phone=0&amount=10.00&comment=0&lock=1";
        $this->assertSame($expected, $actual);

        $actual = QRCodeGenerator::getMobilepayLink("0", "20.50", "0");
        $expected = QRCodeGenerator::$urlBase . "phone=0&amount=20.50&comment=0&lock=1";
        $this->assertSame($expected, $actual);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Amount should be positive");
        QRCodeGenerator::getMobilepayLink("0", "-11.245", "0");
    }

    /** @test It truncates decimal amounts to 2 decimal places */
    public function roundsDecimalAmounts(){
        $actual = QRCodeGenerator::getMobilepayLink("0", 0.128456, "0");
        $expected = QRCodeGenerator::$urlBase . "phone=0&amount=0.13&comment=0&lock=1";
        $this->assertSame($expected, $actual);
    }

    /** @test It includes a passed phone number */
    public function includesPhoneNumber(){
        $actual = QRCodeGenerator::getMobilepayLink("12345678", 0, "0");
        $expected = QRCodeGenerator::$urlBase . "phone=12345678&amount=0&comment=0&lock=1";
        $this->assertSame($expected, $actual);
    }

    public function badPhoneNumbers(){
        return [
            [1],
            [[]],
            [""],
            ["-5"],
            ["12 23 45 67"],
            ["(+45) 12 85 03 94"],
            ["1234-5678"]
        ];
    }

    /**
     * @test It errors on phone numbers not passed as strings and incorrectly formatted phone numbers
     * @dataProvider badPhoneNumbers
     */
    public function validatesPhoneNumbers($badPhoneNumber){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Phone number should be a string containing only numbers");
        QRCodeGenerator::getMobilepayLink($badPhoneNumber, 5, "0");
    }

    /**
     * @test It can locks the comment field by default, but this can be overridden
     */
    public function locksCommentField(){
        $actual = QRCodeGenerator::getMobilepayLink("0", 0, "0");
        $expected = QRCodeGenerator::$urlBase . "phone=0&amount=0&comment=0&lock=1";
        $this->assertSame($expected, $actual);

        $actual = QRCodeGenerator::getMobilepayLink("0", 0, "0", false);
        $expected = QRCodeGenerator::$urlBase . "phone=0&amount=0&comment=0";
        $this->assertSame($expected, $actual);
    }

    /** @test It omits comment and lock if the comment is empty */
    public function omitsCommentIfEmpty(){
        $actual = QRCodeGenerator::getMobilepayLink("0", 0, "");
        $expected = QRCodeGenerator::$urlBase . "phone=0&amount=0&lock=1";
        $this->assertSame($expected, $actual);

        $actual = QRCodeGenerator::getMobilepayLink("0", 0, "", false);
        $expected = QRCodeGenerator::$urlBase . "phone=0&amount=0";
        $this->assertSame($expected, $actual);
    }

    /** @test It checks comment is at most 25 characters */
    public function checksCommentMaxLength(){
        $maxComment = str_repeat("0", 25);
        $actual = QRCodeGenerator::getMobilepayLink("0", null, $maxComment, false);
        $expected = QRCodeGenerator::$urlBase . "phone=0&comment=" . $maxComment;
        $this->assertSame($expected, $actual);

        $tooLongComment = str_repeat("0", 26);
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Comment must be at most 25 characters long");
        QRCodeGenerator::getMobilepayLink("0", null, $tooLongComment);
    }

    /** @test It omits amount if null is passed */
    public function omitsAmountIfNull(){
        $actual = QRCodeGenerator::getMobilepayLink("0", null, "", false);
        $expected = QRCodeGenerator::$urlBase . "phone=0";
        $this->assertSame($expected, $actual);

        $actual = QRCodeGenerator::getMobilepayLink("0", null, "0", false);
        $expected = QRCodeGenerator::$urlBase . "phone=0&comment=0";
        $this->assertSame($expected, $actual);
    }
}

?>