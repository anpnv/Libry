<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Delete book</title>
        <base href="<?= $web_root ?>"/>
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <?php include('menu.php'); ?>

        <div class="container">
            <p>You are about to delete the book '
                <span style="font-weight: bold;">
                    <?= $book->title; ?>
                    '
                </span>
                .<br>If this is correct, please confirm.</p>
            <form class="button" action="book/confirm_delete/<?=$book->id?>" method="post">
                <input type="submit" name="confirm" value='Confirm'/>
                <input type="submit" name="CancelButton" value='Cancel'/>
            </form>
        </div>
    </div>
</body>
</html>