<?php

namespace PHPMVC\Controllers\Admin;

class NotFoundController extends \PHPMVC\Controllers\AbstractController
{
    public function notFoundAction()
    {
        $this->language->load('template.common');
        $this->_view();
    }
}