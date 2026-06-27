<?php
namespace PHPMVC\Controllers\Front;

use PHPMVC\Controllers\AbstractController;

class IndexController extends AbstractController
{
    public function defaultAction()
    {
        $this->_view();
    }
}
