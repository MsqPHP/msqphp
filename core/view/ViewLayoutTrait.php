<?php declare(strict_types = 1);
namespace msqphp\core\view;

use msqphp\base;
use msqphp\core;

trait ViewLayoutTrait
{
    //布局是否开启
    protected $layout   = false;

    protected function initLayout()
    {
        $this->layout = true;
        $this->options['layout_begin'] = (array) $this->config['layout_begin'];
        $this->options['layout_end']   = (array) $this->config['layout_end'];
    }

    public function layout(bool $layout) : self
    {
        $this->layout = $layout;
        return $this;
    }
}