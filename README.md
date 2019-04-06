# MobilepayQRGenerator

This code generates Mobilepay QR codes for KBHFF using the `endroid/qr-code` library.

## Dependencies

Dependencies are handled by `composer`; run `composer install` to install them. 

## Usage

Example code is as follows:

```html
<?php
require_once __DIR__ . "/../vendor/autoload.php";
use \FjolleJagt\kbhffQRCode\QRCodeGenerator;

$filename = QRCodeGenerator::generate(30.50, "Østerbro", "3142");
?>

<img src="<?php echo $filename; ?>" alt="QR code"/>
```

You can run the tests by running the command `./vendor/bin/phpunit`.
