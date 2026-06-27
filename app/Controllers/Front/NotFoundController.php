<?php
namespace PHPMVC\Controllers\Front;

class NotFoundController extends \PHPMVC\Controllers\AbstractController
{
    public function notFoundAction()
    {
        $this->_view();
    }
}