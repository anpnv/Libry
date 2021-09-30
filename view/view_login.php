<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Log In</title>
        <base href="<?= $web_root ?>"/>
        <meta
            name="viewport"
            content="width=device-width, initial-scale=1, user-scalable=no"/>
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>

        <div id="index">

            <header>
                <h2>Login
                </h2>
            </header>
            <div >
            <form action="main/login" method="post">
                <table class="loginForm">
                    <tr>
                        <td> Username : </td>                        
                        <td><input id="username" name="username" type="text" value="<?= $username; ?>" ></td>
                    </tr>
                    <tr> <td> Password : </td>
                        <td><input id="password" name="password" type="password" value="<?= $password; ?>" ></td>
                    </tr>
                </table>

                <input type="submit" value="Login">
                <a href="main/index">
                    <input type="button" value="back">
                </a>
            </form>
            </div>
            <?php if (count($errors) != 0): ?>
            <div class='errors'>
                <br><br>
                <p>Please correct the following error(s) :</p>
                <ul>
                    <?php foreach ($errors as $error): ?>
                    <li><?= $error ?></li>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

            </div>
        </body>
    </html>