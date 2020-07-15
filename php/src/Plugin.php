<?php

namespace WP_BladeOne;

class Plugin
{
    private $compiler;


    public function run($file = '', $data = array())
    {
        return $this->compiler->run($file, $data);
    }

    public function setCompiler($compiler)
    {
        $this->compiler = $compiler;
        return $this;
    }

    public function getCompiler()
    {
        return $this->compiler;
    }

    public function isBladeTemplate($file)
    {
        return strpos($file, $this->compiler->getFileExtension()) !== false;
    }

    public function share($name, $attributs)
    {
        $this->compiler->share($name, $attributs);
    }
}
