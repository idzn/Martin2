<?php


namespace admin\controllers;


use admin\extended\Controller;


class MainController extends Controller
{
    public function action_index()
    {
        $this->layout = 'admin/default';
        return $this->render('admin/main/index', ['data' => 'admin/main/index']);
    }
} 