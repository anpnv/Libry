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
                $('#edit').validate({
                    rules: {
                        isbn: {
                            remote: {
                                url: 'book/isbn_available_service',
                                type: 'post',
                                data:  {
                                    isbn: function() { 
                                        return $("#isbn").val();
                                    
                                    }
                                }
                            },
                            regex: /^(\d+-?)+\d+$/,
                            minlength: 12,
                            maxlength: 15,
                            required: true,
                        },
                    },
                    messages: {
                        isbn: {
                            regex: 'Only number for isbn or dash',
                            remote: 'this isbn is already taken',
                            required: 'required',
                            minlength: 'minimum 12 characters',
                            maxlength: 'maximum 15 characters with " - " ',
                            
                        }
                    }
                });
                $("input:text:first").focus();
            });   

            var isbn = $('#isbn').val();
            $(document).ready(function(){
                $('#isbn').on("keyup",function(){
                    var isbn = $('#isbn').val();
                    if (isbn.length < 11){
                        $('#digit13').hide();
                    } else if (isbn.length > 11) {
                        $('#digit13').show();
                        $('#isbn').focusout(function(){
                            $('#isbn').val(deleteDashIsbn(isbn));
                            if ($('#isbn').val().length > 11){
                                $('#isbn').val(convertDashedIsbn(isbn));
                            }
                        }); 
                    }
                    $.post("book/ean13_service", {isbn : deleteDashIsbn(isbn)},
                    function(data){
                        $('#digit13').val(data);
                    });
                });

                function convertDashedIsbn(isbn){
                    
                    var newIsbn ='';
                    for (var i =0; i < 12; i++){
                        if (i ==2 || i ==3 ||i ==7){
                            var res = isbn.substr(i,1);
                            newIsbn += res;
                            newIsbn +='-';
                        } else {
                            res = isbn.substr(i,1);
                            newIsbn+= res;
                        }

                    }
                   return newIsbn;
                }
               
                function deleteDashIsbn(isbn){
                    var str = isbn;
                    str = str.replace(/-/g, '');
                    isbn = str;
                    return isbn;
                }

            })
        </script>

           

    </head>
    <body>
        <?php include('menu.php'); ?>       
        <div class="container">
            <header> 
                <h2> <?php if (isset($_POST['visualize'])) : ?>                   
                        <?php if ($member->role === 'admin'): ?>            
                            Edit book
                        <?php elseif ($member->role != 'admin'): ?>
                            Book informations  
                        <?php endif; ?>
                    <?php elseif ($_POST['addbook']) : ?>
                        Create a book  
                    <?php endif; ?>
                </h2>
            </header>         
            <form  id ="edit" action=" <?php if (isset($_POST['visualize'])) : ?>
                      book/update_book 
                  <?php elseif (isset($_POST['addbook'])): ?>
                      book/create_book
                  <?php endif; ?>"
                  method="post" enctype="multipart/form-data">
                <table class="loginForm">
                    <tr>
                        <td> Number of available books <?php if ($member->role === 'admin') : ?>
                                <span style='color:red;'>(*)</span>  <?php endif; ?></td>
                        <td><input
                                class='bookAv'
                                name="nbCopies"
                                type="number"
                                value="<?php if (isset($_GET['param1'])) echo $book->nbCopies; ?>"
                                <?php if ($member->role != 'admin'): ?>
                                    disabled <?php endif; ?> ></td>
                    </tr>
                    
                    <tr>
                        <td> ISBN  
                            <?php if ($member->role === 'admin') : ?>
                                <span style='color:red;'>(*)</span>  <?php endif; ?></td>
                        <td><input class='isbnwidth'                       
                                name="isbn"
                                type="text" 
                                id="isbn"
                                maxlength="12"                               
                                value="<?php if (isset($_GET['param1'])) echo Book::isbn12($book->isbn) ?>"
                                <?php if ($member->role != 'admin'): ?>
                                    disabled <?php endif; ?> >      
                                    <input  class='eanDigit' id ="digit13" type="text" disabled value='<?php if (isset($_GET['param1'])) echo Book::digit($book->isbn)?>'>
                                    
                        </td>
                    </tr>
                    <tr>
                        <td> Title <?php if ($member->role === 'admin') : ?>
                                <span style='color:red;'>(*)</span>  <?php endif; ?></td>
                        <td><input
                                name="title"
                                type="text"
                                id ="title"
                                value="<?php if (isset($_GET['param1'])) echo $book->title; ?>"
                                <?php if ($member->role != 'admin'): ?>
                                    disabled <?php endif; ?> ></td>
                    </tr>
                    <tr>
                        <td> Author <?php if ($member->role === 'admin') : ?>
                                <span style='color:red;'>(*)</span>  <?php endif; ?></td>
                        <td><input
                                name="author"
                                type="text"
                                id="author"
                                value="<?php if (isset($_GET['param1'])) echo $book->author; ?>"
                                <?php if ($member->role != 'admin'): ?>
                                    disabled <?php endif; ?> ></td>
                    </tr>
                    <tr>
                        <td> Editor <?php if ($member->role === 'admin') : ?>
                                <span style='color:red;'>(*)</span>  <?php endif; ?></td>
                        <td><input
                                name="editor"
                                type="text"
                                id="editor"
                                value="<?php if (isset($_GET['param1'])) echo $book->editor; ?>"
                                <?php if ($member->role != 'admin'): ?>
                                    disabled <?php endif; ?> ></td>
                    </tr>
                    <tr>
                        <td> Picture </td>                        
                        <td> <?php if ($member->role === 'admin') : ?>
                                <input type='file' name='image'accept="image/x-png, image/gif, image/jpeg" ><br>
                            <?php endif; ?>
                            <?php if (isset($book->picture)): ?>                           
                                <img src='upload/<?= $book->picture ?>' width="400" height="auto" alt="Book image"><br><br>                                
                                <?php if($member->role =='admin'): ?>
                                    <input type='submit'  name='clearPicture' value='clear' >
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr> 
                </table>                              
                <input type="hidden" name='recoverID' value='<?php if (isset($_POST['visualize'])) echo $book->id ?>'>
                <?php if ($member->role === 'admin'): ?>
                    <input type="submit" name ="confirmEdit" value='Save'>
                <?php endif; ?>

                <?php if (isset($_GET['param2'])) $filter =Utils::url_safe_decode($_GET['param2']) ?>
                <a href="book/book_list/<?=$_GET['param1']?>/<?= Utils::url_safe_encode($filter)?>"> <input type="button"  value="back"> </a>
            </form>
        </div>
    </body>
</html>