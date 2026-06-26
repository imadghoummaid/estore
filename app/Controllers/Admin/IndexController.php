<?php
namespace PHPMVC\Controllers\Admin;

class IndexController extends \PHPMVC\Controllers\AbstractController
{
    public function defaultAction()
    {
        $this->language->load('template.common');
        $this->language->load('index.default');
        $this->_view();
    }
}