<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>User list</title>
        <base href="<?= $web_root ?>"/>
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <?php include('menu.php'); ?>
        <div class="container">
            <header>
                <h2>All user
                </h2>
            </header>
            <div class="listTabU">
                <table>
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Birth Date</th>
                            <th>Role</th>
                            <?php if ($member->role == "admin"): ?>
                            <th>Actions</th>
                            <?php endif;?>
                        </tr>
                    </thead>
                    <tbody >
                        <?php foreach ($members as $user): ?>
                        <tr>
                            <td><?= $user->username ?></td>
                            <td><?= $user->fullname ?></td>
                            <td><?= $user->email ?></td>
                            <td><?= $user->birthdate ?></td>
                            <td><?= $user->role ?></td>
                            <td>
                                <?php if ($member->role == "admin"): ?>
                                <form class="formAction" action='member/edit_user' method='POST'>
                                    <input type="hidden" name="id" value="<?= $user->id ?>">
                                    <input type="submit" class="checkIcon" value="">
                                    <?php endif; ?>
                                </form>
                                <?php if ($member->role == "admin"): ?>
                                <form class="formAction" action='member/delete_user' method='POST'>
                                    <input type="hidden" name="id" value="<?= $user->id ?>">
                                    <?php  if ($member->id != $user->id && ($member->role !=='manager' || $member->role !=='member') ) : ?>
                                    <input type="submit" class="delIcon" value="">
                                    <?php endif; ?>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </tbody>
                    <?php endforeach; ?>
                </table>
            </div>
            <?php if (count($errors) != 0): ?>

            <p>Please correct the following error(s) :</p>
            <ul>
                <?php foreach ((array) $errors as $error): ?>
                <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>

        <?php elseif (strlen($success) != 0): ?>
            <p class="success"><?= $success ?></p>
            <?php endif; ?>

            <?php if ($member->role != "member"): ?>
            <form action="member/new_user" method="POST">
                <input type="submit" name="new_User" value="New User">
                <?php endif; ?>
            </form>

        </body>
    </html>