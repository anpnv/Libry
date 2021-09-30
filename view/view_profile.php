<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?= $member->username ?>'s Profile!</title>
        <base href="<?= $web_root ?>"/>
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <?php include('menu.php'); ?>
        <div class="container">
            <header>
                <h2>Welcome
                    <?= $member->fullname ?>
                </h2>
            </header>
            <div class="listTab">
                <table>
                    <thead>
                        <tr>
                            <th>Rental Date / time</th>
                            <th>Book</th>
                            <th>Was returned on</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rentals as $rent) :?>
                        <tr>

                            <td>
                                <?= $rent->rentaldate ?>
                            </td>
                            <td>
                                <?= Book::get_title($rent->book) ?></td>

                            <td>
                                <?php

                        if (Rental::isLate($rent->rentaldate))
                             echo '<div class="datePast">'  ;                          
                        ?>
                                <?= $rent->returndate ?>
                                <?php if (Rental::isLate($rent->rentaldate))  echo "</div>" ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </body>
</html>