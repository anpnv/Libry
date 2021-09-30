<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Editor></title>
        <base href="<?= $web_root ?>"/>
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
        <script src="lib/jquery-3.4.0.min.js" type="text/javascript"></script>
        <script
            src="lib/jquery-validation-1.17.0/jquery.validate.min.js"
            type="text/javascript"></script>

            <script>
                var user = '<?= $member->username?>';
                var email = '<?=$member->email?>';
            $.validator.addMethod("regex", function (value, element, pattern) {
                if (pattern instanceof Array) {
                    for(p of pattern) {
                        if (!p.test(value))
                            return false;
                    }
                    return true;
                } else {
                    return pattern.test(value);
                }
            }, "Please enter a valid input.");
            $(function(){
                $('#CreateOrEdit').validate({
                    rules: {
                        username: {
                            remote: {
                                url: 'member/username_available_service',
                                type: 'post',
                                data:  {
                                    username: function() { 
                                        return $("#username").val();
                                    }
                                }
                            },
                            required: true,
                            minlength: 3,
                            maxlength: 16,
                            regex: /^[a-zA-Z][a-zA-Z0-9]*$/,
                        },
                        email: {
                            remote: {
                                url: 'member/email_available_service',
                                type: 'post',
                                data:  {
                                    email: function() { 
                                        return $("#email").val();
                                    }
                                }
                            },
                            required: true,                            
                        },
                        fullname : {
                            required: true,
                        }
                    },
                    messages: {
                        username: {
                            remote: 'this username is already taken',
                            required: 'required',
                            minlength: 'minimum 3 characters',
                            maxlength: 'maximum 16 characters',
                            regex: 'bad format for pseudo',
                        },
                        fullname : {
                            required: 'required',
                        },
                        email : {
                            remote: 'this email is already taken',
                            required : 'required',
                        }                        
                    }
                });
                $("input:text:first").focus();
            });   
        </script>
    </head>
    <body>

        <?php include('menu.php'); ?>
        <div class="container">
            <header>
                <h2>
                    <?php if (isset($_POST["id"])): ?>
                    You are about to edit '
                    <?= $user->username ?>
                    '
                <?php elseif (isset($_POST["new_User"])): ?>
                    You are about to create a new User
                    <?php endif; ?>
                </h2>
            </header>
            <form
                action=" <?php if (isset($_POST["id"])): ?>
                      member/update_profile
                  <?php elseif (isset($_POST['new_User'])): ?>
                      member/create_user
                  <?php endif; ?>
                  "
                method="post"
                
                id="CreateOrEdit">
                <table>
                    <tr>
                        <td>User name :
                        </td>
                        <td><input
                            name="username"
                            id ="username"
                            type="text"
                            value="<?php
                                if (isset($_POST["id"]))
                                    echo $user->username;
                                ?>"></td>
                    </tr>
                    <tr>
                        <td>Full name :
                        </td>
                        <td><input
                            name="fullname"
                            type="text"
                            value="<?php
                                if (isset($_POST["id"]))
                                    echo $user->fullname;
                                ?>"></td>
                    </tr>
                    <tr>
                        <td>Birthdate :
                        </td>
                        <td><input
                            type="date"
                            name="birthdate"
                            value="<?php if (isset($_POST["id"]))
                                    echo $user->birthdate;
                                ?>"></td>
                    </tr>
                    <tr>
                        <td>Email :
                        </td>
                        <td><input
                            name="email"
                            type="email"
                            id="email"
                            value="<?php if (isset($_POST["id"]))
                                    echo $user->email;
                                ?>"></td>
                    </tr>
                    <tr>
                        <td>
                            Role :
                        </td>
                        <td>
                        
                            <select name="role">
                                <option value="member" <?php if (isset($_POST["id"]))
                                    echo $user->role == 'member' ? 'selected' : ''
                                    ?>>Member</option>
                                      <?php if ($member->role == 'admin'):?> 
                                <option value="manager" <?php if (isset($_POST["id"]))
                                        echo $user->role == 'manager' ? 'selected' : ''
                                        ?>>Manager</option> 
                                <option value="admin" <?php if (isset($_POST["id"]))
                                        echo $user->role == 'admin' ? 'selected' : ''
                                        ?>>Admin</option>
                                        <?php endif;?>
                            </select>
                            
                        </td>
                    </tr>

                </table>                                        
                <input type="hidden" name='recoverID' value=' <?php if (isset($_POST["id"])) echo $user->id ?>'>
                <input type="submit" name ="confirmEdit" value='Save'>
                <a href="member/user_list"> <input type="button"  value="back"> </a>
            </form>                                                               
        </div>
    </body>
</html>