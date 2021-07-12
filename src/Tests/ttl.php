<?php

use Sim\Csrf\Csrf;

include_once '../../vendor/autoload.php';

$csrf = new Csrf();
// get a csrf field with expiration of 10 seconds from now
$field = $csrf->setExpiration(10)->getField('test', 'csrffield');

//$csrf->clear();

if(isset($_POST['submit_btn'])) {
    if(isset($_POST['csrffield'])) {
        if($csrf->validate($_POST['csrffield'], 'test')) {
            echo "Valid form.";
        } else {
            echo "Invalid form! Try again...";
        }
    } else {
        echo "Form is not valid. Maybe CSRF attack!!";
    }
}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Test csrf ttl</title>
</head>
<body>
<form action="" method="post">
    <?= $field; ?>

    <button type="submit" name="submit_btn">
        submit form
    </button>
</form>
</body>
</html>
