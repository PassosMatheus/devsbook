<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\LoginHandler;

class HomeController extends Controller {
    
    private $loggedUser;

    public function __construct() {
        $this->loggedUser = LoginHandler::checkLogin();
        if($this->loggedUser === false) {
            $this->redirect('/'); 
        }
    }

    public function index() {
        $this->loggedUser->name;
        $this->render('home', ['nome' => 'Matheus']);
    }

}