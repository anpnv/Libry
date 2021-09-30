<?php

require_once 'model/Member.php';
require_once 'model/Rental.php';
require_once 'model/Book.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';
require_once 'framework/Tools.php';
require_once 'framework/Utils.php';

class ControllerRental extends Controller {

    public function index() {
        if ($this->user_logged()) {
            $this->redirect("member", "profile");
        } else {
            (new View("index"))->show();
        }
    }
    
    public function ressourceJson(){
        
            $member = $_POST['member'];
            $book = $_POST['book'];
            $date = $_POST['date'];
            $status = $_POST['state'];
            $filter =['memberS'=>$member, "bookS" =>$book, 'dateS' =>$date,"stateS" =>$status];        
            $filter = Rental::search($filter);        
            foreach($filter as $r ){
                $data[] = array('id' =>$r->id, 'fuser' => Member::get_name($r->user), 'fbook' => Book::get_title($r->book), 'start' =>$r->rentaldate);
            }       
            echo json_encode($data);       
    }

    public function eventsJson(){
        
        $rentals = Rental::getJsonEvents();
        echo $rentals;
 
    }

    public function delete_service(){
        $res = false;
        if(isset($_POST["rentalID"]) && $_POST['rentalID'] != ""){            
            Rental::del_rent($_POST['rentalID']);
            $res = true;
        }
        echo json_encode($res);
    }

    public function return_service(){
        $res = false;
        if(isset($_POST["rentalID"]) && $_POST['rentalID'] != ""){            
            Rental::returnBook($_POST['rentalID']);
            $res = true;
        }
        echo json_encode($res);
    }

    
 
    
    public function management_returns(){
        $member = $this->get_user_or_redirect();     
        if ($member->role =='admin' ||$member->role =='manager'){
        $rentals = Rental::all_rent();
        $filter = ['memberS' =>'', 'bookS' =>'', 'stateS'=> '', 'dateS'=>''];
        
        (new View("management"))->show(array("member" => $member, "rentals" => $rentals, 'filter'=>$filter));
        }

        
    }
    
public function returns_management() {
    $member = $this -> get_user_or_redirect();
    $filter = [
        'memberS' => '',
        'bookS' => '',
        'stateS' => '',
        'dateS' => ''
    ];
    if ($member -> role == 'admin' || $member -> role == 'manager') {

        if (isset($_GET["param1"])) {
            $filter = Utils::url_safe_decode($_GET["param1"]);
            if (!$filter) 
                Tools::abort("Bad url parameter");
            }
        
        if (isset($_POST['search']) || (isset($_POST['bookS']) || isset($_POST['memberS']) || isset($_POST['dateS']) || isset($_POST['stateS']))) {
            $filter['bookS'] = $_POST['bookS'];
            $filter['memberS'] = $_POST['memberS'];
            $filter['dateS'] = $_POST['dateS'];
            $filter['stateS'] = $_POST['stateS'];

            $this->redirect(
                "rental",
                "management_returns",
                Utils::url_safe_encode($filter)
            );
        }

        
    }
    

}

    public function gestion(){
        $member = $this -> get_user_or_redirect();
        $filter = [
            'memberS' => '',
            'bookS' => '',
            'stateS' => '',
            'dateS' => ''
        ];
        if (isset($_GET['param1'])) {
            $rent = Rental::get_rental_by_id($_GET['param1']);
            
        }
        if (isset($_POST['search']) || (isset($_POST['bookS']) || isset($_POST['memberS']) || isset($_POST['dateS']) || isset($_POST['stateS']))) {
            $filter['bookS'] = $_POST['bookS'];
            $filter['memberS'] = $_POST['memberS'];
            $filter['dateS'] = $_POST['dateS'];
            $filter['stateS'] = $_POST['stateS'];
        }     
        if (isset($_POST['delete']) && $member -> role == 'admin') {
            Rental::del_rent($rent->id);
            $this->redirect('rental', 'management_returns', Utils::url_safe_encode($filter));
        }
            
            
            if (isset($_POST['return'])) {
                Rental::returnBook($rent->id);
                $this->redirect('rental', 'management_returns', Utils::url_safe_encode($filter));
    
            }
    }

}
