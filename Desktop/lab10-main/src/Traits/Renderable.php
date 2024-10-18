<?php

namespace App\Traits;

trait Renderable
{
    public function render($template, $data = [])
    {
        global $mustache;
        $tpl = $mustache->loadTemplate($template);

        echo $tpl->render($data);
    }
}