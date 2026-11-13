<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SalesChartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SalesChartController extends Controller
{
    public function __construct(private SalesChartService $salesChart) {}

    public function __invoke(Request $request): JsonResponse
    {
        $chart = $this->salesChart->build($request->query('period'));

        return response()->json([
            ...$chart,
            'html' => view('admin.partials.sales-chart', ['chart' => $chart])->render(),
        ]);
    }
}
