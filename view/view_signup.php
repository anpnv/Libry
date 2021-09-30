<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Sign up</title>
        <base href="<?= $web_root; ?>"/>
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>

        <script src="lib/jquery-3.4.0.min.js" type="text/javascript"></script>
        <script
            src="lib/jquery-validation-1.17.0/jquery.validate.min.js"
            type="text/javascript"></script>
        <script>
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
                $('#signupForm').validate({
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
                        },                   
                        password: {
                            required: true,
                            minlength: 8,
                            maxlength: 16,
                            regex: [/[A-Z]/, /\d/, /['";:,.\/?\\-]/],
                        },
                        password_confirm: {
                            required: true,
                            minlength: 8,
                            maxlength: 16,
                            equalTo: "#password",
                            regex: [/[A-Z]/, /\d/, /['";:,.\/?\\-]/],
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
                        },
                        password: {
                            required: 'required',
                            minlength: 'minimum 8 characters',
                            maxlength: 'maximum 16 characters',
                            regex: 'bad password format',
                        },
                        password_confirm: {
                            required: 'required',
                            minlength: 'minimum 8 characters',
                            maxlength: 'maximum 16 characters',
                            equalTo: 'must be identical to password above',
                            regex: 'bad password format',
                        }
                    }
                });
                $("input:text:first").focus();
            });   
        </script>
    </head>
    <body>
        <div id="index">
        
                <header> 
                    <h2>signup
                    </h2>
                </header>
                <div id='idForm'> 
                <form action="main/signup" method="post" id="signupForm">
                    <table class='loginForm'>
                    <tr>
                            <td>Username:</td>
                            <td><input 
                                    id="username" 
                                    name="username" 
                                    type="text" 
                                    value="<?= $username; ?>"></td>
                            
                        </tr>
                        <tr>
                            <td>Full name:</td>
                            <td><input 
                                    id="fullname" 
                                    name="fullname" 
                                    type="text" 
                                    value="<?= $fullname; ?>"></td>
                        </tr>
                        <tr>
                            <td>Birthdate :
                            </td>
                            <td><input 
                                    type="date" 
                                    id="birthdate" 
                                    name="birthdate" 
                                    value="<?= $birthdate; ?>"></td>
                        </tr>
                        <tr>
                            <td>Password :</td>
                            <td><input
                                    id="password"
                                    name="password"
                                    type="password"
                                    value="<?= $password; ?>"></td>
                        </tr>
                        <tr>
                            <td>Confirm password :
                            </td>
                            <td><input
                                    id="password_confirm"
                                    name="password_confirm"
                                    type="password"
                                    value="<?= $password_confirm; ?>">
                            </td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td><input id="email" name="email" type="email" value="<?=$email; ?>"></td>
                        </tr>
                    </table>
                    <input type="submit" value="Sign up">
                    <a href="main/index"> <input type="button"  value="Back"> </a>
                </form>
                </div>
                
                <?php if (count($errors) != 0): ?>
                    
                        <br><br>
                        <p>Please correct the following error(s) :</p>
                        <ul> 
                            <?php foreach ($errors as $error): ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>                    
                <?php endif; ?>                
            </div>
             
    </body>
</html>