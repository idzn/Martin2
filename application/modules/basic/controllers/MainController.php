<?php


namespace basic\controllers;


use basic\extended\Controller;
use admin\extended\Components;

class MainController extends Controller
{
    public function action_index()
    {
        return $this->render('main/index', ['data' => 'basic/main/index']);
    }
} 