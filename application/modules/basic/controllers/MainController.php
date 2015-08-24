<?php


namespace basic\controllers;


use basic\extended\Controller;
use admin\extended\Components;

class MainController extends Controller
{
    public function action_index()
    {
        Components::runtime()->pageTitle = 'basic/main/index';
        return $this->render('main/index', ['data' => 'basic/main/index']);
    }

    public function action_about()
    {
        Components::runtime()->pageTitle = 'About';
        return $this->render();
    }
} 