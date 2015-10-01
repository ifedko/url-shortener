<?php

namespace AppCore\Controller;

class NotFoundController extends Controller
{
    public function indexAction()
    {
        $this->render('Default:index');
    }
}