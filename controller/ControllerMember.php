<?php

require_once 'model/Member.php';
require_once 'model/Rental.php';
require_once 'model/Book.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';
require_once 'framework/Tools.php';

class ControllerMember extends Controller {

     public function index() {
        if ($this->user_logged()) {
            $this->redirect("member", "profile");
        } else {
            (new View("index"))->show();
        }
    }

    public function profile() {
        $member = $this->get_user_or_redirect();
        if (isset($_GET["param1"]) && $_GET["param1"] !== "") {
            $member = Member::get_member_by_username($_GET["param1"]);            
        }
        $rentals = Rental::get_rentals($member->id);
        
        
        (new View("profile"))->show(array("member" => $member, "rentals" =>$rentals));
    }

    public function username_available_service(){
        $res ="true";
        if ( isset($_POST['username']) && $_POST['username'] !== ""){
            $member = Member::get_member_by_username($_POST['username']);
            
            if ($member){
                $res = "false";
            }
        }
        echo $res;
    }

    public function username_isAv_update_service(){
        $res ="true";
        if ( isset($_POST['username']) && $_POST['username'] !== ""){
            $member = Member::get_member_by_username($_POST['username']);
            if ($member){
                
                $res = "false";
            }
        }
        echo $res;
    }
    


    public function email_available_service(){
        $res ="true";
        if ( isset($_POST['email']) && $_POST['email'] !== ""){
            $email = Member::get_member_by_email($_POST['email']);
            if ($email){
                $res = "false";
            }
        }
        echo $res;
    }

    public function user_list() {
        $member = $this->get_user_or_redirect();
        if ($member->role =='admin' || $member->role =='manager') {
        $members = $member->get_members();
        $success = "";
        $errors = [];
        (new View("users_list"))->show(array("member" => $member,
            "members" => $members, "success" => $success, "errors" => $errors));
        }
    }

    public function delete_user() {
        $member = $this->get_user_or_redirect();

        if (isset($_POST['id']) && $member->role =='admin') {
            $get_id = $_POST['id'];
            $user = Member::get_member_by_id($get_id);
        }
        (new View("delete_user"))->show(array("member" => $member,
            "user" => $user));
    }

    public function confirm_delete() {
        $member = $this->get_user_or_redirect();

        $success = "";
        $errors = [];
        if (isset($_POST['SubmitButton']) && $member->role =='admin' ) {
            $member_toDel = $_POST['SubmitButton'];
            $id_toDel = Member::get_member_by_id($member_toDel);
            if ($id_toDel) {
                $success = " User deleted";
                $id_toDel->delete_member();
            }
        } elseif (isset($_POST['CancelButton'])) {
            $success = "Deleting cancel";
        }
        $members = $member->get_members();
        (new View("users_list"))->show(array("member" => $member,
            "members" => $members, "success" => $success, "errors" => $errors));

        
    }

    public function edit_user() {
        $member = $this->get_user_or_redirect();

        if (isset($_POST['id']) && $member->role =='admin' ) {
            $get_id = $_POST['id'];
            $user = Member::get_member_by_id($get_id);
            (new View("edit_profile"))->show(array("member" => $member,
                "user" => $user));
        } elseif (isset($_POST['new_User']))
            (new View("edit_profile"))->show(array("member" => $member));
    }

    public function update_profile() {
        $errors = [];
        $success = '';
        $fullname = '';
        $username = '';
        $email = '';
        $role = '';
        $birthdate = '';
        $member = $this->get_user_or_redirect();

        if (isset($_POST['recoverID']) && isset($_POST['username']) && isset($_POST['fullname']) && isset($_POST['birthdate']) && isset($_POST['role']) && isset($_POST['email']) && $member->role =='admin' ) {
            
            $id = $_POST['recoverID'];
            $user_toEdit = Member::get_member_by_id($id);
            $username = ($_POST['username']);
            $fullname = ($_POST['fullname']);
            $email = ($_POST['email']);
            $role = ($_POST['role']);
            $birthdate = ($_POST['birthdate']);
            if ($username != $user_toEdit->username) {
                $errors = Member::validate_unicity($username);
            }
            if ($email != $user_toEdit->email) {
                $errors = array_merge($errors, Member::validate_email_unicity($email));
            }
            $user_toEdit->username = $username;
            $user_toEdit->fullname = $fullname;
            $user_toEdit->email = $email;
            $user_toEdit->role = $role;
            $user_toEdit->birthdate = $birthdate;

            if (isset($_POST['confirmEdit']) && count($errors) == 0) {
                $user_toEdit->update();
                $success = "Success";
            }
        }

        $members = $member->get_members();
        (new View("users_list"))->show(array("member" => $member,
            "members" => $members, "errors" => $errors, "success" => $success));
    }

    public function new_user() {
        $member = $this->get_user_or_redirect();

        if (isset($_POST['new_User']) && ($member->role =='admin' || $member->role =='manager')) {
            $get_id = $_POST['new_User'];
            $user = Member::get_member_by_id($get_id);
        }
        (new View("edit_profile"))->show(array("member" => $member,
            "user" => $user));
    }

    public function create_user() {
        $member = $this->get_user_or_redirect();
        $errors = [];
        $success = '';
        $fullname = '';
        $username = '';
        $email = '';
        $role = '';
        $birthdate = '';
        $password = '';
        $id = '';

        if (isset($_POST['username']) && isset($_POST['fullname']) && isset($_POST['birthdate']) && isset($_POST['role']) && isset($_POST['email'])) {
            $username = trim($_POST['username']);
            $password = $username;
            $fullname = trim($_POST['fullname']);
            $email = trim($_POST['email']);
            $birthdate = Tools::sanitize($_POST['birthdate']);
            $role = $_POST['role'];

            $new_user = new Member($username, Tools::my_hash($password), $fullname, $email, $birthdate, $role, $id);
            $errors = Member::validate_unicity($username);
            $errors = array_merge($errors, Member::check_string($fullname));
            $errors = array_merge($errors, Member::check_username($username));
            $errors = array_merge($errors, Member::validate_email_unicity($email));

            if (count($errors) == 0) {

                $new_user->update();
                $success = 'The password is same like username';
            }

            $members = $member->get_members();
            (new View("users_list"))->show(array("member" => $member,
                "members" => $members, "errors" => $errors, "success" => $success));
        }
    }
    
    
    

}
