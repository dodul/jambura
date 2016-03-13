<?php

#StudlyCaps
class ControllerWelcome extends jController
{
    #camelCase
    public function actionIndex()
    {
        $this->title = 'Welcome to Jambura';
        $this->hello = 'Hello World';
        $this->render('welcome');
    }
}
