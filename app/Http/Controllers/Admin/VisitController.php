<?php

namespace Fickrr\Http\Controllers\Admin;

use Fickrr\Http\Controllers\Controller;
use Fickrr\Support\Visits\VisitReport;

class VisitController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function live()
    {
        try {
            return response()->json(VisitReport::realtime());
        } catch (\Throwable $e) {
            return response()->json(['minutes' => 5, 'count' => 0, 'visitors' => []]);
        }
    }
}
