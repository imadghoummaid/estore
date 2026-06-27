<?php
namespace PHPMVC\Controllers\Front;

use PHPMVC\Controllers\AbstractController;

class NotFoundController extends AbstractController
{
    public function notFoundAction()
    {
        $this->_view();
    }
}
