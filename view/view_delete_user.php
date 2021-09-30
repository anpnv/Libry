<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Delete User</title>
        <base href="<?= $web_root ?>"/>
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <?php include('menu.php'); ?>

        <div class="container">
            <p>You are about to delete the user '
                <span style="font-weight: bold;">
                    <?= $user->username; ?>
                    '
                </span>
                .<br>If this is correct, please confirm.</p>
            <form
                class="formAction"
                style="text-align:center;"
                action="member/confirm_delete"
                method="post">
                <input type="hidden" name="SubmitButton" value='<?= $user->id ?>'/>
                <input type="submit" value='Confirm'/>
            </form>
            <form class="formAction" action="member/confirm_delete" method="post">
                <input type="submit" name="CancelButton" value='Cancel'/>
            </form>
        </div>
    </body>
</html>