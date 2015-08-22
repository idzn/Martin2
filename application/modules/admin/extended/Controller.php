<?php


namespace admin\extended;


class Controller extends \extended\Controller
{
    public function __construct() 
    {
        parent::__construct();
        $this->layout = 'admin/default';
    }

    public function __destruct()
    {
        parent::__destruct();
    }
} 