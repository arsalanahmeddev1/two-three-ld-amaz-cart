<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TestController extends Controller
{
    /**
     * Constructor to disable CSRF protection for this controller
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // Write to debug file that middleware is being processed
            file_put_contents(storage_path('logs/stripe_debug.txt'), 
                date('Y-m-d H:i:s') . " - TestController middleware running\n", 
                FILE_APPEND);
                
            return $next($request);
        });
    }
    public function test(Request $request)
    {
        // Log to the debug file
        file_put_contents(storage_path('logs/stripe_debug.txt'), 
            date('Y-m-d H:i:s') . " - Test controller reached!\n" .
            "Method: " . $request->method() . "\n" .
            "All data: " . json_encode($request->all()) . "\n\n", 
            FILE_APPEND);
        
        // Return a simple response
        return response()->json([
            'status' => 'success',
            'message' => 'Test controller reached successfully!',
            'data' => $request->all()
        ]);
    }
    
    public function testGet()
    {
        // Log to the debug file
        file_put_contents(storage_path('logs/stripe_debug.txt'), 
            date('Y-m-d H:i:s') . " - Test GET controller reached!\n", 
            FILE_APPEND);
        
        // Return a simple HTML response
        return '<html><body>
            <h1>Test Controller Reached!</h1>
            <p>The controller is working correctly.</p>
            <p>Debug file has been updated.</p>
            </body></html>';
    }
} 