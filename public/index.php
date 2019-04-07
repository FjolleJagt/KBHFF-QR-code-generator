<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>QR Code Code Showcase</title>
    <link rel="stylesheet" href="style.css" />
</head>

<body>

<div id="page">
    <div id="content" class="narrow-column">
        <h1>KBHFF QR Code Code Showcase</h1>
        <h2>The code</h2>
<pre class="prettyprint">
<?php
echo htmlspecialchars("<?php
 //enable composer's auto-loading of PHP classes:
 require_once __DIR__ . \"/../vendor/autoload.php\"; 
 use \FjolleJagt\kbhffQRCode\QRCodeGenerator;
 //Ask for 30,50kr to the Østerbro account with transaction id 3142
 \$filename = QRCodeGenerator::generate(30.50, \"Østerbro\", \"3142\");
?>

<img src=\"<?php echo \$filename; ?>\" alt=\"QR code\" class=\"centre\"/>");
?>
</pre>

<p>You can find the <a href="https://github.com/FjolleJagt/KBHFF-QR-code-generator">code base</a> on GitHub.</p>

        <h2>The result</h2>

        <?php
        require_once __DIR__ . "/../vendor/autoload.php";
        use \FjolleJagt\kbhffQRCode\QRCodeGenerator;

        $filename = QRCodeGenerator::generate(30.50, "Østerbro", "3142");
        ?>

        <img src="<?php echo $filename; ?>" alt="QR code" class="centre frame"/>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/gh/google/code-prettify@master/loader/run_prettify.js?lang=php"></script>
</body>
</html>
