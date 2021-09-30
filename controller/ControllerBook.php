<?php

require_once 'model/Member.php';
require_once 'model/Rental.php';
require_once 'model/Book.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';
require_once 'framework/Tools.php';
require_once 'framework/Utils.php';

class ControllerBook extends Controller {

    public function index() {
        if ($this->user_logged()) {
            $this->redirect("member", "profile");
        } else {
            (new View("index"))->show();
        }
    }

    public function isbn_available_service(){
        $res ="true";
        if (isset($_POST['isbn']) && $_POST['isbn'] !== ""){     
            $temp = $_POST['isbn'];
            $tempDigit = Book::digitEan13($_POST['isbn']); 
            (string)$isbnstr = $temp.$tempDigit;
            $toTest= book::get_book_by_isbn($isbnstr);
            
            
            if ($toTest){
                $res = "false";
            }
        }
        echo $res;
    }

    public function ean13_service(){
        $tempDigit = Book::digitEan13($_POST['isbn']); 
        echo json_encode($tempDigit);
    }

    public function searchService(){
            $res = 'false;';          
            if (isset($_POST['value']) && isset($_POST['user'])){
                $val = $_POST['value'];
                $user = $_POST['user'];
                $res = Book::search($val, $user);
              
               
              
            }
       echo json_encode($res);
    }

    public function book_list() {
        $member = $this->get_user_or_redirect(); 
        $other ='';  
        if (isset($_GET['param1']) && $_GET['param1'] !== ''){
            $other = Member::get_member_by_id($_GET['param1']);
            $isAv = Rental::is_available($other->id);
            $toBasket = Rental::rentMember($other->id);
        }
        $members = Member::get_members();        
        $errors='';

        $filter = ['filter'=>'', 'book' =>''];
        $bookFilter = '';
        if ($bookFilter !=='' && isset($_GET['param2'])){
            $isAv = $bookFilter;
        }  
        (new View("book_list"))->show(array("other"=>$other, "member" => $member,"members"=>$members ,"isAv" => $isAv, "toBasket" => $toBasket, "errors" =>$errors, "filter" =>$filter));
    }
    //afin d'avoir toujours le param1 rempli par l'user co ou pour celui qu'on crée le panier
    public function books(){
        
        
        
        $member = $this->get_user_or_redirect();
        
        $this->redirect('book/book_list', $member->id);
    }


    public function getBooksJson($user){
            $books = Rental::is_available($user);
            foreach ($books as $b){
                $data[] = array('id' =>$b->id, 'nbCopies' =>$b->nbCopies, 'isbn' => $b->isbn, 'title' => $b->title, 'author' =>$b->author, 'editor' =>$b->editor, 'picture' =>$b->picture);
            }
            echo json_encode($data);
    }



    public function basket_action() {

        if (isset($_GET["param2"])) {
            $filter = Utils::url_safe_decode($_GET["param2"]);
            
            }
        
        $member = $this->get_user_or_redirect();
        $max = Configuration::get("max");
        $errors = '';
        if (isset($_GET['param1'])){
            $userID = Member::get_member_by_id($_GET['param1']);
        }
        if (isset($_GET['param3'])){
            $bookID = Book::get_book_by_id($_GET['param3']);
        }
        //suppression du panier
        if (isset($_POST['clear'])) {
            Rental::clear_bask($userID->id);
            $this->redirect('book', 'book_list', $userID->id);
        }

        if (isset($_POST['confirm'])) {            
            if ($max < count(Rental::max5elseReject($userID->id))){
                $errors ='Max_'.$max.'_rent_per_member';
                
            } 
            if (!$errors){
                Rental::rent1mouth($userID->id);
                
            }
            $this->redirect('book/book_list', $userID->id, Utils::url_safe_encode($filter));
        }

        //filter bar recherche de livre non loué 
        if(isset($_POST['search']) && isset($_POST['toSearch'])){                       
            
            $filter['filter'] = $_POST['toSearch'];
            $toBasket = Rental::rentMember($userID->id);
            $bookFilter = Book::search($filter['filter'], $userID->id);
            
            $this->redirect('book','book_list', $userID->id, Utils::url_safe_encode($filter));

         
        }
        // Gestion du panier Ajout et suppression
        if (isset($_POST['addbask'])) {
            
            $toBasket = Rental::rental_bask($userID->id, $bookID->id);
            $toBasket->add_rent();
            $this->redirect('book/book_list', $userID->id, Utils::url_safe_encode($filter));
        }
        if (isset($_POST['delbask'])) {
            Rental::del_bask($userID->id, $bookID->id);
            $this->redirect('book/book_list', $userID->id,Utils::url_safe_encode($filter) );   
        }

        //redirect sur un autre user
        
        if (isset($_POST['confirmForSomeone']) &&  isset($_POST['forThisUser']) && ($member->role =='admin' || $member->role =='manager' )){
            $other = Member::get_member_by_id($_POST['forThisUser']);
            $this->redirect('book', 'book_list', $other->id);
        }
            
        //Permet Modifier le livre pour l'administrateur et visualier pour les autres
        if (isset($_POST['visualize'])) {          
            $book = Book::get_book_by_id($_GET['param1']);
            (new View("edit_book"))->show(array("member" => $member,
                "book" => $book));
        }
        //Création d'un livre
        if (isset($_POST['addbook']) && $member->role =='admin') {
            $get_id = $_POST['addbook'];
            $book = Book::get_book_by_id($get_id);
            (new View("edit_book"))->show(array("member" => $member));
        }
        //Suppression du livre + vérification si administrateur
        if (isset($_POST['delete']) && $member->role ==='admin') {
            $book = Book::get_book_by_id($_GET['param1']);
            (new View("delete_book"))->show(array("member" => $member,
                "book" => $book));
        } 
    }

    public function confirm_delete() {
        $member = $this->get_user_or_redirect();

        if (isset($_POST['confirm']) && $member->role =='admin' ) {
            $book_toDel = $_GET['param1'];
            $id_toDel = Book::get_book_by_id($book_toDel);
            if ($id_toDel) {

                $id_toDel->delete_book($book_toDel);               
            }
        } elseif (isset($_POST['Cancel'])) {
            $success = "Delete cancel";
        }
        $this->redirect('book', 'books');

        
    }

    const UPLOAD_ERR_OK = 0;

    public function update_book() {
        $member = $this->get_user_or_redirect();
        $errors = [];

        if (isset($_POST['recoverID']) && isset($_POST['isbn']) && isset($_POST['title']) && isset($_POST['author']) && isset($_POST['editor']) && isset($_FILES['image']) && isset($_POST['nbCopies'])) {
            
            $book = Book::get_book_by_id($_POST['recoverID']);

            $isbn = $_POST['isbn'];
            $temp = Book::digitEan13($_POST['isbn']);
            

            $nbCopie = $_POST['nbCopies'];
            (string)$isbnstr = $isbn.$temp;
            $book->isbn = $isbnstr;
            $book->title = $_POST['title'];
            $book->editor = $_POST['editor'];
            $book->author = $_POST['author'];
            
            if ($nbCopie <= Book::countRentBook($book->id) ){
                $errors ='You_can_not_decrease_the_number_of_copies_under_the_rented_copies';
            }
            
            
            if (!$errors) {
                  $book->nbCopies = $nbCopie;
            }
               
                              
            if (count($errors) != 0){
                $this->redirect('book', 'book_list');
            }

            if (isset($_FILES['image']) && $_FILES['image']['error'] === self::UPLOAD_ERR_OK) {
                $errors = Book::validate_photo($_FILES['image']);
                if (empty($errors)) {
                    $saveTo = $book->generate_photo_name($_FILES['image']);
                    $oldFileName = $book->picture;
                    if ($oldFileName && file_exists("upload/" . $oldFileName)) {
                        unlink("upload/" . $oldFileName);
                    }
    
                    move_uploaded_file($_FILES['image']['tmp_name'], "upload/$saveTo");
                    $book->picture = $saveTo;
                    $book->updateBook();
                }
            }
            if ( isset($_POST['clearPicture'])) {
                $oldFileName = $book->picture;
                $book->picture = NULL;
                unlink("upload/" . $oldFileName);
                $book->updateBook();
            }

    
            if (isset($_POST['confirmEdit']) && (count($errors) == 0) && $member->role =='admin' ) {
                $book->updateBook();    
            }


            //$this->redirect('book/books');
           
        }
            

        

        

        
    }

    public function create_book() {

        $member = $this->get_user_or_redirect();
        $id = '';
        $errors = [];
        $isbn = '';
        $title = '';
        $author = '';
        $editor = '';
        $picture = '';
        $nbCopies = '';

        if (isset($_POST['isbn']) && isset($_POST['title']) && isset($_POST['author']) && isset($_POST['editor']) && isset($_FILES['image'])&& isset($_POST['nbCopies']) && $member->role =='admin' ) {
            $isbn = $_POST['isbn'];
            $temp = Book::digitEan13($_POST['isbn']);
            

            (string)$isbnstr = $isbn.$temp;
            $isbn = $isbnstr;
            $title = trim($_POST['title']);
            $author = trim($_POST['author']);
            $editor = trim($_POST['editor']);
            if ($_POST['nbCopies'] <=0 ) {
                $errors ='not lower than 0';
            } else {
                $nbCopies = $_POST['nbCopies'];
            }


            $pic = $_FILES['image'];

            if (isset($_FILES['image']) && $_FILES['image']['error'] === self::UPLOAD_ERR_OK) {
                $errors = Book::validate_photo($_FILES['image']);
                if (empty($errors)) {
                                   
                    $saveTo = Book::generate_photo_name_create($pic, $author);

                    move_uploaded_file($_FILES['image']['tmp_name'], "upload/$saveTo");
                    $picture = $saveTo;
                   
                }
            } else {
                $picture = null;
            }
            $new_book = new Book($id, $isbn, $title, $author, $editor, $picture, $nbCopies);
            $errors = Book::validate_unicity($isbn);
            $errors = array_merge($errors, Member::check_string($title));
            $errors = array_merge($errors, Member::check_string($author));
            $errors = array_merge($errors, Member::check_string($editor));
            $errors = array_merge($errors, Member::check_string($nbCopies));


            if (count($errors) != 0) {
                $errors ='Errors_when_you_try_to_creat_a_book';
                $this->redirect('book', 'book_list', $errors);
            } else {
                $new_book->updateBook();
                $this->redirect('book', 'book_list');
            }
            
        }
    }

}