<?php

require_once "framework/Model.php";
require_once 'framework/Tools.php';

class Book extends Model {

    public $id;
    public $isbn;
    public $title;
    public $author;
    public $editor;
    public $picture;
    public $nbCopies;

    public function __construct($id, $isbn, $title, $author, $editor, $picture, $nbCopies) {
    $this->id = $id;
        $this->isbn = $isbn;
        $this->title = $title;
        $this->author = $author;
        $this->editor = $editor;
        $this->picture = $picture;
        $this->nbCopies = $nbCopies;
    }

    public static function search($valueToSearch, $user) {
        $query = self::execute("SELECT *
            from book WHERE (isbn LIKE '%" . $valueToSearch . "%'"
                        . "OR title LIKE '%" . $valueToSearch . "%'"
                        . "OR author LIKE '%" . $valueToSearch . "%'"
                        . "OR editor LIKE '%" . $valueToSearch . "%'"
                . ")and id not in (select book from rental where user =:user)", array("user" => $user));
        $data = $query->fetchAll();
        $bookToFiltre = [];
        foreach ($data as $book) {
            $bookToFiltre[] = new Book($book["id"], $book["isbn"], $book["title"], $book["author"], $book["editor"], $book["picture"], $book['nbCopies']);
        }
        return $bookToFiltre;
        
    }

    public static function get_book_by_id($id) {
        $query = self::execute("SELECT * FROM book where id = :id", array("id" => $id));
        $row = $query->fetch();

        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new Book($row['id'], $row['isbn'], $row['title'], $row['author'], $row['editor'], $row['picture'], $row['nbCopies']);
        }
    }

    public static function delete_book($id) {
        self::execute('DELETE FROM rental where book =:id', array("id" => $id));
        self::execute('DELETE FROM book WHERE id=:id', array("id" => $id));
        return true;
    }

    public static function delete_picture($id) {

        self::execute('UPDATE book SET picture = NULL WHERE id =:id', array('id' => $id));
        return true;
    }

    public function updateBook() {

        if (self::get_book_by_id($this->id)) {
            if (empty($this->picture))
                $this->picture = null;
            self::execute("UPDATE book SET isbn=:isbn, title=:title, author =:author, editor=:editor, picture =:picture, nbCopies =:nbCopies WHERE id=:id ", 
            array("id" => $this->id, "isbn" => $this->isbn, "title" => $this->title, "author" => $this->author, "editor" => $this->editor, "picture" => $this->picture,
                "nbCopies" => $this->nbCopies));
        
        
            } else {
            if (empty($this->picture))
                $this->picture = null;

            
            self::execute("INSERT INTO book (isbn, title, author, editor, picture, nbCopies)
                       VALUES(?,?,?,?,?,?)", array($this->isbn, $this->title, $this->author, $this->editor, $this->picture, $this->nbCopies));
        }
        return $this;
    }

    //renvoie un tableau d'erreur(s) 
    //le tableau est vide s'il n'y a pas d'erreur.
    public static function validate_photo($file) {
        $errors = [];
        if (isset($file['name']) && $file['name'] != '') {
            if ($file['error'] == 0) {
                $valid_types = array("image/gif", "image/jpeg", "image/png");
                if (!in_array($_FILES['image']['type'], $valid_types)) {
                    $errors[] = "Unsupported image format : gif, jpg/jpeg or png.";
                }
            } else {
                $errors[] = "Error while uploading file.";
            }
        }
        return $errors;
    }

    //pre : validate_photo($file) returns true
    public function  generate_photo_name($file) {
        //note : time() est utilisé pour que la nouvelle image n'aie pas
        //       le meme nom afin d'éviter que le navigateur affiche
        //       une ancienne image présente dans le cache
        if ($_FILES['image']['type'] == "image/gif") {
            $saveTo = $this->author . time() . ".gif";
        } else if ($_FILES['image']['type'] == "image/jpeg") {
            $saveTo = $this->author . time() . ".jpg";
        } else if ($_FILES['image']['type'] == "image/png") {
            $saveTo = $this->author . time() . ".png";
        }
        return $saveTo;
    }

    public static function  generate_photo_name_create($file, $author) {
        //note : time() est utilisé pour que la nouvelle image n'aie pas
        //       le meme nom afin d'éviter que le navigateur affiche
        //       une ancienne image présente dans le cache
        if ($_FILES['image']['type'] == "image/gif") {
            $saveTo = $author . time() . ".gif";
        } else if ($_FILES['image']['type'] == "image/jpeg") {
            $saveTo = $author . time() . ".jpg";
        } else if ($_FILES['image']['type'] == "image/png") {
            $saveTo = $author . time() . ".png";
        }
        return $saveTo;
    }

    public static function ean2isbn($x) {
        $x = str_replace(" ", "", str_replace("-", "", $x));
        if (strlen($x) == 13) {
            $a = substr($x, 0, 3);
            $b = substr($x, 3, 1);
            $cd = substr($x, 4, 8);
            $k1 = substr($x, 4, 1);
            $k3 = substr($x, 4, 3);
            $k4 = substr($x, 4, 4);
            $k5 = substr($x, 4, 5);
            $k6 = substr($x, 4, 6);
            $k7 = substr($x, 4, 7);
            $e = substr($x, 12, 1);
            if ($a == "978" and $b == "2") {
                if ($k1 == "0" or $k1 == "1")
                    $l = "2";
                elseif ($k3 >= "200" and $k3 <= "349")
                    $l = "3";
                elseif ($k5 >= "35000" and $k5 <= "39999")
                    $l = "5";
                elseif ($k3 >= "400" and $k3 <= "699")
                    $l = "3";
                elseif ($k4 >= "7000" and $k4 <= "8399")
                    $l = "4";
                elseif ($k5 >= "84000" and $k5 <= "89999")
                    $l = "5";
                elseif ($k6 >= "900000" and $k6 <= "949999")
                    $l = "6";
                elseif ($k7 >= "9500000" and $k7 <= "9999999")
                    $l = "7";
                $c = substr($cd, 0, $l);
                $d = substr($cd, $l, 8 - $l);
                return $a . "-" . $b . "-" . $c . "-" . $d . "-" . $e;
            }
        }
    }
    public static function isbn12($isbn){
        return substr($isbn, 0, -1);
    }
    public static function digit($isbn){
        return substr($isbn, -1,1);
    }


    public static function digitEan13($isbn){
        $odds =0; 
        $checkSum =0;
        $total = 0;
        $strIsbn = (string)$isbn;
        $evens =0;
        for($i = 0; $i < strlen($strIsbn); $i++){            
            if ($i %2 == 0 ){
                $evens += $strIsbn{$i};              
            } else {
                $odds += $strIsbn{$i};               
            }
        }
        $odds = $odds *3;
        $total = $evens+$odds;
        
        if ($total % 10 == 0 ){
            $checkSum = 0;
        } else {
            $checkSum = 10 - ($total%10);
        }
        
        return $checkSum;     
    }


    public static function validate_unicity($isbn) {
        $errors = [];
        $isbn = self::get_book_by_isbn($isbn);
        if ($isbn) {
            $errors[] = "This user already exists.";
        }
        return $errors;
    }

    public static function get_book_by_isbn($isbn) {
        $query = self::execute("SELECT * FROM book where isbn = :isbn", array("isbn" => $isbn));
        $data = $query->fetch(); 

        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new Book( $data["id"], $data["isbn"], $data["title"], $data["author"], $data["editor"], $data["picture"], $data["nbCopies"]);
        }
    }
    
    
    public static function get_title ($id){
         $query = self::execute("SELECT title FROM book where id =:id", array('id' =>$id));
         $data = $query->fetch(); 

        if ($query->rowCount() == 0) {
            return false;
        } else {
            return $data["title"];
        }
    }

    public static function get_author($id){
        $query = self::execute("SELECT author FROM book where id =:id", array('id' =>$id));
        $data = $query->fetch(); 

       if ($query->rowCount() == 0) {
           return false;
       } else {
           return $data["author"];
       }
   }
    
            
    public static function bookAv($bookID){
        $book = Book::get_book_by_id($bookID);
        return $book->nbCopiesAvailable();  
    }

   public function nbCopiesAvailable() {
 
    $copRent = self::execute("SELECT * 
                            FROM rental r, book b 
                            WHERE b.id =:id
                            and r.book =:id",
                            array("id"=>$this->id));

    $nbCopRent = $copRent->fetchAll();
    $res = $this->nbCopies - (count($nbCopRent));
    return $res;

    }

    public static function countRentBook($id){
        $copRent = self::execute("SELECT * 
                            FROM rental r, book b 
                            WHERE b.id =:id
                            and r.book = b.id",
                            array("id"=>$id));

                            $nbCopRent = $copRent->fetchAll();
        $res = count($nbCopRent);
        return $res;
    }

}
    
    
        
    
    
    


