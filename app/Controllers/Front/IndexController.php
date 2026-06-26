<?php
namespace PHPMVC\Controllers\Front;

class IndexController extends \PHPMVC\Controllers\AbstractController
{
    public function defaultAction()
    {
        $this->_view();
    }
}