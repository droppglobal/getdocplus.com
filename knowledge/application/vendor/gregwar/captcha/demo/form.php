<?php
require_once __DIR__.'/../vendor/autoload.php';
use Gregwar\Captcha\PhraseBuilder;

session_start();
?>

<html>
<?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_SESSION['phrase']) && PhraseBuilder::comparePhrases($_SESSION['phrase'], $_POST['phrase'])) {
            echo "<h1>Captcha is valid !</h1>";
        } else {
            echo "<h1>Captcha is not valid!</h1>";
        }
                unset($_SESSION['phrase']);
    }
?>
    <form method="post">
        Copy the CAPTCHA:
        <?php 
                                ?>
        <img src="session.php" />
        <input type="text" name="phrase" />
        <input type="submit" />
    </form>
</html>
