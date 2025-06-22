<?php

namespace App\View\Components\Filament\Tables\Columns;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Rating extends Component
{
    public $state;

    /**
     * Create a new component instance.
     */
    public function __construct($state)
    {
        // Ensure the state is converted to an integer
        $this->state = is_numeric($state) ? (int)$state : 0;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.filament.tables.columns.rating');
    }
}
