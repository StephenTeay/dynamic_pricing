<?php
// controllers/HomeController.php

require_once __DIR__ . '/../core/Controller.php';

class HomeController extends Controller {
    public function index() {
        $pageTitle = APP_NAME . ' - Home';
        require __DIR__ . '/../views/home.php';
    }
}