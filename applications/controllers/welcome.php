<?php 
class Controller_welcome extends jController {
    public function action_index() {
        $this->title = 'Welcome to Jambura';
        $this->hello = 'Hello World';
        $this->render('welcome');
    }
}