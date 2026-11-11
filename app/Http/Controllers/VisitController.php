<?php

namespace Fickrr\Http\Controllers;

use Fickrr\Support\Visits\VisitRecorder;
use Illuminate\Http\Request;

class VisitController extends Controller
{
    public function store(Request $request)
    {
        try {
            VisitRecorder::capture($request);
        } catch (\Throwable $e) {
            \Log::warning('Visit beacon skipped: '.$e->getMessage());
        }

        return response('', 204);
    }
}
