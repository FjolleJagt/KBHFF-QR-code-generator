<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>QR Code Code Showcase</title>
</head>

<body>

<h1>The code</h1>

<pre><code>
    <?php
    echo htmlspecialchars("<?php
    include(\"../src/MobilepayQRGenerator.php\");
    use \MobilepayQRGenerator\QRCodeGenerator;

    \$filename = QRCodeGenerator::generate(30.50, \"Østerbro\", 3142);
    ?>

    <img src=\"<?php echo \$filename; ?>\" alt=\"QR code\"/>");
    ?>
</code></pre>

<h1>The result</h1>

<?php
include("../src/MobilepayQRGenerator.php");
use \MobilepayQRGenerator\QRCodeGenerator;

$filename = QRCodeGenerator::generate(30.50, "Østerbro", 3142);
?>

<img src="<?php echo $filename; ?>" alt="QR code"/>

</body>
</html>
