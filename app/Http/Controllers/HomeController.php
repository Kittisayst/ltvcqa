<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Standard;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

class HomeController extends Controller
{
    /**
     * Public landing page: shows which academic year is active, the QA
     * framework it uses, and a summary of that framework's structure
     * (Standard -> Indicator -> BasisMain counts) for visitors who are
     * not yet logged in.
     */
    public function __invoke(): View
    {
        $year = AcademicYear::active();
        $framework = $year?->framework;

        /** @var Collection<int, Standard> $standards */
        $standards = $framework
            ? $framework->standards()
                ->withCount(['indicators', 'basisMains'])
                ->orderBy('order')
                ->get()
            : collect();

        return view('welcome', [
            'year' => $year,
            'framework' => $framework,
            'standards' => $standards,
            'standardCount' => $standards->count(),
            'indicatorCount' => (int) $standards->sum('indicators_count'),
            'basisMainCount' => (int) $standards->sum('basis_mains_count'),
        ]);
    }
}
