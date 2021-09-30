<?php

require_once 'model/Member.php';
require_once 'model/Rental.php';
require_once 'model/Book.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';
require_once 'framework/Tools.php';

class ControllerMain extends Controller {

    //si l'utilisateur est conecté, redirige vers son profil.
    //sinon, produit la vue d'accueil.
    public function index() {
        if ($this->user_logged()) {
            $this->redirect("member", "profile");
        } else {
            (new View("index"))->show();
        }
    }

    // log the user
    public function login() {
        $username = '';
        $password = '';
        $errors = [];
        if (isset($_POST['username']) && isset($_POST['password'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $errors = Member::validate_login($username, $password);
            if (count($errors) == 0) {
                $this->log_user(Member::get_member_by_username($username));
            }
        }
        (new View("login"))->show(array("username" => $username, "password" => $password, "errors" => $errors));
    }

    //Signup the user
    public function signup() {
        $username = '';
        $fullname = '';
        $birthdate = '';
        $password = '';
        $password_confirm = '';
        $email = '';
        $role = '';
        $id ='';
        $errors = [];

        if (isset($_POST['username']) && isset($_POST['fullname']) && isset($_POST['password']) && isset($_POST['password_confirm']) &&
                isset($_POST['email']) && isset($_POST['birthdate'])) {

            $username = trim($_POST['username']);
            $password = $_POST['password'];
            $password_confirm = $_POST['password_confirm'];
            $fullname = trim($_POST['fullname']);
            $email = trim($_POST['email']);
            $birthdate = Tools::sanitize($_POST['birthdate']);
            $role  = 'member';
            
            $member = new Member($username, Tools::my_hash($password),$fullname,  $email, $birthdate, $role, $id);
            $errors = Member::validate_unicity($username);
            $errors = array_merge($errors, Member::check_string($fullname));
            $errors = array_merge($errors, Member::check_username($username));
            $errors = array_merge($errors, Member::validate_passwords($password, $password_confirm));

            if (count($errors) == 0) {
                
                $member->update();
                $member = Member::get_member_by_username($username);                
                $this->log_user($member);
            } 
        }
        
        (new View("signup"))->show(array("username" => $username, 
                                        "fullname" => $fullname, 
                                        "password" => $password, 
                                        "password_confirm" => $password_confirm,
                                        "birthdate" => $birthdate, 
                                        "email" => $email, 
                                        "errors" => $errors));
    }

}
