<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::get('/setup-database', function () {
    if (app()->environment('production')) {
        try {
            Artisan::call('migrate', ['--force' => true]);
            return response()->json([
                'status' => 'success',
                'message' => 'Database migrated successfully!',
                'output' => Artisan::output()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    return response()->json(['status' => 'error', 'message' => 'Only available in production']);
});
