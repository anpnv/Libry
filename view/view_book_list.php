<?php                         
            
            if (isset($other) && $other  !== ''){
            $id = $other->id;
            } else {
            $id = $member->id;
        }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Catalog book</title>
        <base href="<?= $web_root ?>"/>
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
        <script src="lib/jquery-3.4.0.min.js" type="text/javascript"></script>
        <script src="lib/url-tools-bundle.min.js"></script>
        

<script>   
    var id = "<?=$id?>";
    $(document).ready(function () {
        $("#myInput").on("keyup", function () {
            var value = $(this).val().toLowerCase();
            //console.log(value) -> affiche correctement la valeur en base64
            $.post("book/searchService", {
                value: value,
                user: id
            }, function (data) {  
                data = JSON.parse(data);
                var tab = $('#myTable');
                $("#myTable tr").filter(function () {
                    var filter = value;
                    
                    $(this).toggle($(this).text().toLowerCase().indexOf(filter) > -1);
                });
            });
        });
    });
    $(function () {
                $("#btnToHide, #btnToHide2").hide();
                $('#btnToHide').attr('disabled', true); 
            });

           
</script>

    </head>
    <body>
        <?php if (isset($_GET['param2']))
            $filter = Utils::url_safe_decode($_GET['param2'])?>
        <?php include('menu.php'); ?>
        <div class="container">
            <header>
                <h2>All book
                </h2>
            </header>
            <form action='book/basket_action' method='POST'>
                <input type="hidden" name="recoverID" value ="<?= $id ?>">
                <input type='text' name='toSearch' id='myInput' placeholder=' Filter bar ' value='<?=  $filter['filter']?>'>       
                <input
                    type='submit'
                    id='btnToHide'
                    name='search'
                    formaction="book/basket_action/<?= $id?>/"
                    value=' Apply filter '>
                <?php if (isset($_GET['param2'])): ?>
                <a href="book/books">
                    <input type="button" id="btnToHide2" value="back">
                </a>
                <?php endif; ?>
                <br>
                <br>
                <div class="listTabF">
                    <div class="listTab">
                        <table>
                            <thead>
                                <tr>
                                    <th width="5%">Copies
                                    </th>
                                    <th width="14%">ISBN</th>
                                    <th width="45%">Title</th>
                                    <th width="13.5%">Author</th>
                                    <th width="13.5%">Editor</th>
                                    <th width="13%">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="myTable">
                            <?php
                                if (isset($_GET['param2'])) {
                                    
                                    $bookFilter = Book::search($filter['filter'], $id);
                                    $books = $bookFilter;
                                } else {
                                    $books = $isAv;
                                }

                                foreach ($books as $book):
                                    ?>
                                <tr>
                                    <td><?= Book::bookAv($book->id)?></td>
                                    <td><?= Book::ean2isbn($book->isbn); ?></td>
                                    <td><?= $book->title ?></td>
                                    <td><?= $book->author ?></td>
                                    <td><?= $book->editor ?></td>
                                    <td>

                                    <?php  if (Book::bookAv($book->id) > 0 && count(Rental::max5elseReject($id)) < Configuration::get("max")):?>
                                        <input
                                            type="submit"
                                            class='addbaskIcon'
                                            title="Add to basket"
                                            formaction="book/basket_action/<?= $id?>/<?=Utils::url_safe_encode($filter)?>/<?= $book->id ?>"
                                            name="addbask"
                                            value=''>
                                    <?php elseif (Book::bookAv($book->id) == 0) :?>
                                        <img src="image/warning.png" class="icnWarning" title="No more availability">
                                    <?php elseif (count(Rental::max5elseReject($id)) == Configuration::get("max")) :?>
                                        <img src="image/max.png" class="icnWarning" title="max 5 rent per person">
                                        <?php endif;?>

                                        <input
                                            type="submit"
                                            formaction="book/basket_action/<?= $id?>/<?=Utils::url_safe_encode($filter)?>/<?= $book->id ?>"
                                            class='checkIcon'
                                            title="Visualize"
                                            name="visualize"
                                            value=''>
                                        <?php if ($member->role === 'admin') : ?>
                                        <input
                                            type="submit"
                                            formaction="book/basket_action/<?= $book->id ?>/<?=Utils::url_safe_encode($filter)?> "
                                            class='delIcon'
                                            title="Delete Book"
                                            name="delete"
                                            value=''>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php if ($member->role == "admin"): ?>
                <br>
                <input type="submit" name="addbook" value='Add Book'>
                <?php endif; ?>
                
                <br><br>

                <div class='divider'></div>
                <header>
                    <h2>Basket to rent
                    </h2>
                </header>

                <?php if ($member->role == 'admin' || $member->role == 'manager'): ?>
                This basket is for :
                <select class="selectBook" name='forThisUser'>
                    <?php foreach($members as $m ) : ?>
                    <option value='<?= $m->id?>' 
                    <?php if (isset($other->id)) : ?>
                        <?php if ($other->id == $m->id) echo 'selected'; ?>
                   
                    <?php endif;?>>
                        <?=$m->username . " # " . $m->id?> 
                    </option>
                    <?php endforeach;?>
                    <input type="submit" name="confirmForSomeone" class="switchUser" style='margin-left : 15px;' value=''> 
                    <?php  if (isset($other) && $other  !== ''): ?>
                    <a href="book/books">
                    <input type="button" value="Back">
                </a> 
                <?php endif;?>
                <br><br>
                </select>
                
                <?php endif; ?>

                <div class="listTab">
                    <table>
                        <thead>
                            <tr>
                                <th width="5%">Copies
                                </th>
                                <th width="14%">ISBN</th>
                                <th width="45%">Title</th>
                                <th width="13.5%">Author</th>
                                <th width="13.5%">Editor</th>
                                <th width="13%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($toBasket as $book): ?>

                            <tr>
                                <td><?= Book::bookAv($book->id)?></td>
                                <td><?= Book::ean2isbn($book->isbn) ?></td>
                                <td><?= $book->title ?></td>
                                <td><?= $book->author ?></td>
                                <td><?= $book->editor ?></td>
                                <td>
                                    <input
                                        type="submit"
                                        formaction="book/basket_action/<?= $id?>/<?=Utils::url_safe_encode($filter)?>/<?= $book->id ?>"
                                        class=' delbaskIcon'
                                        name="delbask"
                                        title="Remove from basket"
                                        value=''>
                                    <input
                                        type="submit"
                                        formaction="book/basket_action/<?= $book->id ?>"
                                        class="checkIcon"
                                        title="Visualize"
                                        name="visualize"
                                        value=''>
                                    <?php if ($member->role === 'admin') : ?>
                                    <input
                                        type="submit"
                                        formaction="book/basket_action/<?= $book->id ?>"
                                        class='delIcon'
                                        name="delete"
                                        title="Delete book"
                                        value=''>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>           
                <div class='bookBtn'>
                    <input type="submit" name="confirm" formaction="book/basket_action/<?=$id?>" class="confirmBask" value=''>         
                    <input type="submit" name="clear" formaction="book/basket_action/<?=$id?>" class="clearBask" value=''>        
                </div>
            </form>
        </div>



    </body>
</html>