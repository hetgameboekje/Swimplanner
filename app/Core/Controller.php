<?php

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected function render(string $view, array $data = []): void
    {
        View::render($view, $data);
    }

    protected function renderZonderLayout(string $view, array $data = []): void
    {
        View::renderZonderLayout($view, $data);
    }

    protected function redirect(string $pad): never
    {
        header("Location: {$pad}");
        exit;
    }
}
