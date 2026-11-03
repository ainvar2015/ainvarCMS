<?php

namespace App\Controllers\Admin;

abstract class ModuleController extends BaseController
{
    /** Namespace del módulo, p. ej. 'App\\Modules\\News' */
    abstract protected function moduleNamespace(): string;

    protected function renderModule(string $view, array $data = [])
    {
        return view($this->moduleNamespace() . '\Views\admin\\' . $view, array_merge($this->data ?? [], $data));
    }
}