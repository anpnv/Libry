<?php

require_once "framework/Model.php";
require_once 'framework/Tools.php';

class Rental extends Model {

    public $user;
    public $book;
    public $rentaldate;
    public $returndate;
    public $id;

    public function __construct($user, $book, $rentaldate, $returndate, $id) {

        $this->user = $user;
        $this->book = $book;
        $this->rentaldate = $rentaldate;
        $this->returndate = $returndate;
        $this->id = $id;
    }

    public static function max5elseReject($user){
        $query = self::execute("SELECT DISTINCT * FROM rental where user =:user and returndate is null", array("user" => $user));
        $data = $query->fetchAll();
        $rentals = [];
        foreach ($data as $row) {
            $rentals[] = new rental($row['user'], $row['book'], $row['rentaldate'], $row['returndate'], $row['id']);
        }
        return $rentals;
    }

    public static function get_rentals($user) {

        $query = self::execute("SELECT DISTINCT * FROM rental where rentaldate IS NOT NULL and user =:user", array("user" => $user));
        $data = $query->fetchAll();
        $rentals = [];
        foreach ($data as $row) {
            $rentals[] = new rental($row['user'], $row['book'], $row['rentaldate'], $row['returndate'], $row['id']);
        }
        return $rentals;
    }

    public static function all_rent() {

        $query = self::execute("SELECT * FROM rental where rentaldate is not null", array());
        $data = $query->fetchAll();
        $rentals = [];
        foreach ($data as $row) {
            $rentals[] = new Rental($row['user'], $row['book'], $row['rentaldate'], $row['returndate'], $row['id']);
        }
        return $rentals;
    }

    



    public function add_rent() {
        if (is_numeric($this->user)) {
            $this->user = Member::get_member_by_id($this->user);
        }
        if (is_numeric($this->book)) {
            $this->book = Book::get_book_by_id($this->book);
        }
        self::execute("INSERT INTO rental
            (user,book, rentaldate,returndate,id) VALUES(:user,:book,null,null,null)", 
            array("user" => $this->user->id, "book" => $this->book->id));
        return true;
    }

    public static function del_bask($user, $book) {
        return self::execute("DELETE FROM rental WHERE user =:user AND book=:book AND returndate IS NULL and rentaldate is null", array("user" => $user, "book" => $book));     
            }

    public static function rental_bask($user, $book) {
        $id = '';
        $rentaldate = '';
        $returndate = '';
        $bask_temp = [];
        $bask_temp = new Rental($user, $book, $rentaldate, $returndate, $id);
        return $bask_temp;
    }

    public static function rentMember($user) {
        $query = self::execute("SELECT DISTINCT book.id, book.isbn, book.title, book.author, book.editor, book.picture, book.nbCopies
                                FROM book, rental,  user
                                WHERE  rental.user = :user
                                AND user.id = rental.user 
                                AND book.id = rental.book
                                and rental.rentaldate is null
                                ", array("user" => $user));
        $data = $query->fetchAll();
        $bookToRent = [];

        foreach ($data as $book) {
            $bookToRent[] = new Book($book["id"], $book["isbn"], $book["title"], $book["author"], $book["editor"], $book["picture"], $book['nbCopies']);
        }
        return $bookToRent;
    }

    public static function is_available($user) {
        $query = self::execute("SELECT DISTINCT *
                                FROM book
                                WHERE id NOT IN (SELECT rental.book FROM book, rental, user
                                                        WHERE 
                                                        user.id = rental.user
                                                        AND rental.user = :user)"
                        , array("user" => $user));

        $data = $query->fetchAll();

        $book_av = [];
        foreach ($data as $av) {
            $book_av[] = new Book($av["id"], $av["isbn"], $av["title"], $av["author"], $av["editor"], $av["picture"], $av["nbCopies"]);
        }

        return $book_av;
    }

    public static function rent1mouth($user) {

        $date = new DateTime();
        $rentaldate = $date->format('Y-m-d H:i:s'); 

            self::execute('UPDATE rental SET rentaldate =:rentaldate WHERE user=:user and rentaldate is null', array("user" => $user, "rentaldate" => $rentaldate));
            return true; 
    }

    public static function returnBook($id){
        $date = new Datetime();
        $date = $date->format('Y-m-d H:i:s');
        $returndate = $date;
        self::execute('UPDATE rental SET returndate =:returndate WHERE id =:id', array('returndate'=>$returndate, 'id'=>$id ));
        return true;
    }

   


    public static function rentForOther($user, $other) {

        $date = new DateTime();   
        $rentaldate = $date->format('Y-m-d H:i:s'); 
            return self::execute('UPDATE rental SET rentaldate =:rentaldate, user =:other WHERE user=:user', array("user" => $user, "other"=>$other, "rentaldate" => $rentaldate));                            
    }

    
  
    public static function clear_bask($user) {
         
        
        self::execute("DELETE FROM rental WHERE rental.user =:user AND rental.returndate IS NULL", array("user" => $user));
 
        return true;
    }

    public static function search($filter) {
        $valMember = $filter['memberS'];  
        $valBook = $filter['bookS'];      
        $valDate = $filter['dateS'];
        $status = $filter['stateS'];       
        
        
        $stat ='';
        if ($status === 'open' ){
                $stat= 'AND returndate is NULL';
            } elseif ( $status === 'return'){
                $stat = 'AND returndate is not null';
            } else {
               $stat ='AND (returndate is null or returndate is  not null)';
            }
            
        $sql = "SELECT * from rental where  (returndate like :valDate or rentaldate like :valDate) and book  in (SELECT id from book "
                                                    . "where ( title like :valBook  "
                                                    . "OR isbn like :valBook "
                                                    . "OR author like :valBook "
                                                    . "OR editor like :valBook ))"
                                                    . "AND (user in (SELECT id from user where username like :valMember OR fullname like :valMember)"
                                                    . "".$stat.")"; 
        
        $query = self::execute($sql,
            array("valBook" => "%$valBook%", "valMember" => "%$valMember%","valDate" => "%$valDate%")                
        );             

        $data = $query->fetchAll();
        $rentToFiltre = [];
        foreach ($data as $rent) {
            $rentToFiltre[] = new Rental($rent["user"], $rent["book"], $rent["rentaldate"], $rent["returndate"], $rent["id"]);
        } 
        return $rentToFiltre;
    }
    
    public static function get_rental_by_id($id) {
        $query = self::execute("SELECT * FROM rental where id = :id", array("id" => $id));
        $row = $query->fetch();

        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new Rental($row['user'], $row['book'], $row['rentaldate'], $row['returndate'], $row['id']);
        }
    }
    
    public static function del_rent($id){
        self::execute("DELETE FROM rental WHERE id =:id ", array("id" => $id));
        return true;
        
    }

    public static function getJsonRessource() {

        $ressources = self::all_rent();
        foreach ($ressources as $r){
            $data[] = array('id' =>$r->id, 'fuser' => Member::get_name($r->user), 'fbook' => Book::get_title($r->book), 'start' =>$r->rentaldate);
        }
        echo json_encode($data);
    }

    public function rented_service(){
        $toReturn = $this->toReturn();
        echo $toReturn ? "true" : "false";
    }

    private function toReturn() {
        $user = $this->get_user_or_redirect();

        if (isset($_POST['book']) && $_POST['book'] != "") {
            $post_id = $_POST['book'];
            $bookToReturn = Book::get_book_by_id($post_id);
            if ($bookToReturn) {
                return returnBook($bookToReturn);
            } 
        }
        return false;
    }


    public static function getJsonEvents() {
        
        $today = new DateTime(); 
        $events = self::all_rent();
        foreach ($events as $r){
            if (Rental::checkEmptyDate($r->returndate) && !Rental::isLate($r->rentaldate) ){
                $dateToShow = Rental::getReturnDate($r->rentaldate)->format('Y-m-d H:i:s');
            }else if (Rental::checkEmptyDate($r->returndate) && Rental::isLate($r->rentaldate)){
                $dateToShow = $today->format('Y-m-d H:i:s');
            } 
            else {
                $dateToShow = $r->returndate;
            }
            
            $data[] = array('id' =>$r->id,'resourceId'=>$r->id ,'start'=>$r->rentaldate, 'end' => $dateToShow, 'book' =>Book::get_title($r->book),'author' =>Book::get_author($r->book) ,
            'user' =>Member::get_name($r->user), 'color'=>Rental::getColor($r->rentaldate, $r->returndate), 'getColorTxt' =>Rental::getColor($r->rentaldate, $r->returndate));
        }
        echo json_encode($data);
    }

    public static function getColor($rentaldate, $returndate){
        $today = new DateTime();
        $today = $today->format('Y-m-d H:i:s');
        $color ='';
        if (!Rental::isLate($rentaldate) && !Rental::checkEmptyDate($returndate)) {
           $color  = '#84ffa9'; // vert clair -> location cloturée a l'avance 
        } else if (!Rental::isLate($rentaldate)){
          $color =  '#00d67c'; // vert foncé -> location ouverte et non en retard
        } else if (Rental::isLate($rentaldate) && !Rental::checkEmptyDate($returndate)){
           $color ='#feb4c0'; // rouge clair -> location fermée et cloturtée en retard

        } else if (Rental::isLate($rentaldate) && Rental::checkEmptyDate($returndate) && $today < Rental::getReturnDate($rentaldate)) {
            $color =  '#770215'; // rouge foncé -> ouvert et en retard
        }
        return $color;
    }

    public static function isLate($rentaldate){      
        $today = new DateTime(); 
        if ($today < Rental::getReturnDate($rentaldate)){
            return false;
        } else {
            return true;
        }
    }

    public static function getReturnDate($rentaldate){
        $month = new DateInterval(Configuration::get("month"));
        $rentaldate = new DateTime($rentaldate);
        $toReturn = $rentaldate->add($month);
        return $toReturn;
    }

    public static function checkEmptyDate($returndate){
        if ($returndate == null){
            return true;
        } else {
            return false;
        }

    }
}
