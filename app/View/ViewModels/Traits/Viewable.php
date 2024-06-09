<?php

declare(strict_types=1);

namespace App\View\ViewModels\Traits;

use Illuminate\Contracts\View\View;

trait Viewable
{
    abstract protected function viewData(): array;

    public function view(string $view): View
    {
        return view($view, $this->viewData());
    }
}
