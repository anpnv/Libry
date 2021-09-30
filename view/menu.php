<!DOCTYPE HTML>
<html>

    <head>
        <title>Libry</title>
        <meta charset="utf-8"/>
        <meta
            name="viewport"
            content="width=device-width, initial-scale=1, user-scalable=no"/>
            <link href="css/styles.css" rel="stylesheet" type="text/css"/>
    </head>

    <body>

        <!-- Header -->
        <div id="header">
            <div class="top">
                <div id="logo">
                    <a href="member/profile">
                        <img src="image/book.svg" alt="" width="175"/></a>
                    <h1 id="title">
                        <?= $member->username?>
                    </h1>
                    <p>
                        <?='fullname : ' . $member->fullname . '<br>'.'role : ' . $member->role?></p>
                    <p>
                        <?= 'id : ' .$member->id ?>
                    </p>
                </div>
                <!-- Nav -->
                <nav id="nav">
                    <ul>
                        <li class="active">
                            <a href="member/profile">Home</a>
                        </li>
                        <li>
                            <a href="book/books">Book list</a>
                        </li>
                        <li>
                            <?php if ($member->role == 'admin' || $member->role =='manager')  :?>
                            <a href="member/user_list">Users list</a>
                            <?php endif;?>
                        </li>
                        <li>
                            <?php if ($member->role == 'admin' || $member->role =='manager')  :?>
                            <a href="rental/management_returns">Returns</a>
                            <?php endif;?>
                        </li>
                        <li>
                            <a href="member/logout">Log out</a>
                        </li>
                    </ul>
                </nav>
            </div>

            <div class="bottom">
                <div id="footer">
                    made by Ponamarev
                </div>
            </div>

        </div>
    </body>

</html>