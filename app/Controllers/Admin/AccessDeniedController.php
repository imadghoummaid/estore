<?php

namespace PHPMVC\Controllers\Admin;

class AccessDeniedController extends \PHPMVC\Controllers\AbstractController
{
    public function defaultAction()
    {
        $this->language->load('template.common');
        $this->_view();
    }
}