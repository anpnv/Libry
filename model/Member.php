<?php

require_once "framework/Model.php";
require_once 'framework/Tools.php';

class Member extends Model {

    public $username;
    public $h_password;
    public $fullname;
    public $email;
    public $birthdate;
    public $role;
    public $id;

    public function __construct($username, $hashed_password, $fullname, $email, $birthdate, $role, $id= null) {
        $this->username = $username;
        $this->hashed_password = $hashed_password;
        $this->fullname = $fullname;
        $this->email = $email;
        $this->birthdate = $birthdate;
        $this->role = $role;
        $this->id = $id;
    }

   

    public static function validate_unicity($username) {
        $errors = [];
        $member = self::get_member_by_username($username);
        if ($member) {
            $errors[] = "This user already exists.";
        }
        return $errors;
    }
    
    public static function validate_email_unicity($email) {
        $errors = [];
        $member = self::get_member_by_email($email);
        if ($member) {
            $errors[] = "This email is already used.";
        }
        return $errors;
    }

    public static function validate_passwords($password, $password_confirm) {
        $errors = Member::validate_password($password);
        if ($password != $password_confirm) {
            $errors[] = "You have to enter twice the same password.";
        }
        return $errors;
    }

    private static function validate_password($password) {
        $errors = [];
        if (strlen($password) < 4 || strlen($password) > 16) {
            $errors[] = "Password length must be between 4 and 16.";
        }
        return $errors;
    }

    public static function check_string($string) {
        $errors = [];
        if (strlen($string) == 0) {
            $errors [] = "Is required. ";
        }
        return $errors;
    }

    public static function check_username($username) {
        $errors = [];
        if (strlen($username) < 3) {
            $errors [] = "Username short, min 3 char. ";
        }
        return $errors;
    }

    public static function validate_login($username, $password) {
        $errors = [];
        $member = Member::get_member_by_username($username);
        if ($member) {
            if (!self::check_password($password, $member->hashed_password)) {
                $errors[] = "Wrong password. Please try again.";
            }
        } else {
            $errors[] = "Can't find a member with the pseudo '$username'. Please sign up.";
        }
        return $errors;
    }

    private static function check_password($clear_password, $hash) {
        return $hash === Tools::my_hash($clear_password);
    }

    public static function get_members() {
        $query = self::execute("SELECT * FROM user", array());
        $data = $query->fetchAll();
        $members = [];
        foreach ($data as $row) {
            $members[] = new Member($row['username'], $row['password'], $row['fullname'], $row['email'], $row['birthdate'], $row['role'], $row['id']);
        }
        return $members;
    }

    public static function get_member_by_username($username) {
        $query = self::execute("SELECT * FROM user where username = :username", array("username" => $username));
        $data = $query->fetch(); // un seul résultat au maximum
        
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new Member($data["username"], $data["password"], $data["fullname"], $data["email"], $data["birthdate"], $data["role"], $data["id"]);
        }
    }

    public static function get_validate($username){
        $query = self::execute("SELECT * FROM user where username =:username and is not null", array("username" => $username));
        $data = $query->fetch();
        
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new Member($data["username"], $data["password"], $data["fullname"], $data["email"], $data["birthdate"], $data["role"], $data["id"]);
        }
    

    }

    public static function get_member_by_id($id) {
        $query = self::execute("SELECT * FROM user where id = :id", array("id" => $id));
        $data = $query->fetch(); // un seul résultat au maximum
       
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new Member($data["username"], $data["password"], $data["fullname"], $data["email"], $data["birthdate"], $data["role"], $data["id"]);
        }
        
    }
    
    public static function get_member_by_email($email) {
        $query = self::execute("SELECT * FROM user where email = :email", array("email" => $email));
        $data = $query->fetch(); // un seul résultat au maximum
        
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new Member($data["username"], $data["password"], $data["fullname"], $data["email"], $data["birthdate"], $data["role"], $data["id"]);
        }
    }

    
    public function update() {
        if (self::get_member_by_id($this->id)) {
            if (empty($this->birthdate))
                $this->birthdate = null;
            self::execute("UPDATE user SET username =:username, fullname=:fullname, "
                    . "email=:email, birthdate =:birthdate, role=:role WHERE id=:id ", 
                    array("username"=>$this->username, "fullname" => $this->fullname, 
                        "email" => $this->email,
                "birthdate" => $this->birthdate, "role" => $this->role, "id" => $this->id));
        } else {
            if (empty($this->birthdate))
                $this->birthdate = null;
            $new  = self::execute("INSERT INTO user (username, password, fullname, email, birthdate, role)
                       VALUES(?,?,?,?,?,?)", array($this->username, $this->hashed_password, $this->fullname, $this->email, $this->birthdate, $this->role));
            
            
        }
        return $this;
    }
    
    public  function delete_member(){
        self::execute('DELETE FROM rental where user =:id', array("id" => $this->id));
        self::execute('DELETE FROM user WHERE id=:id', array("id" => $this->id));
        return true;
    }
    
    public static function get_name($id){
         $query = self::execute("SELECT username FROM user where id =:id", array('id' =>$id));
         $data = $query->fetch(); // un seul résultat au maximum

        if ($query->rowCount() == 0) {
            return false;
        } else {
            return $data["username"];
        }
    }

    
    
    

}
