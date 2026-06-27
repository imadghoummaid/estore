<?php

namespace PHPMVC\Controllers\Admin;

class NotFoundController extends AbstractController
{
    public function notFoundAction()
    {
        $this->language->load('template.common');
        $this->_view();
    }
}