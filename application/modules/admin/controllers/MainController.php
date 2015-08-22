<?php


namespace admin\controllers;


use admin\extended\Controller;
use extended\Components;


class MainController extends Controller
{
    public function action_index()
    {
        Components::runtime()->pageTitle = 'admin/main/index';
        return $this->render('admin/main/index', ['data' => 'admin/main/index']);
    }
} 