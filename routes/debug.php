<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

Route::get('/debug-vercel', function () {
    try {
        $output = [];
        
        // Test environment variables
        $output['env_check'] = [
            'APP_KEY' => env('APP_KEY') ? '✅ Set' : '❌ Missing',
            'APP_URL' => env('APP_URL', 'not set'),
            'DB_CONNECTION' => env('DB_CONNECTION', 'not set'),
            'DB_HOST' => env('DB_HOST', 'not set'),
            'DB_DATABASE' => env('DB_DATABASE', 'not set'),
            'DB_USERNAME' => env('DB_USERNAME', 'not set'),
            'DB_PASSWORD' => env('DB_PASSWORD') ? '✅ Set' : '❌ Missing',
        ];
        
        // Test database connection
        try {
            DB::connection()->getPdo();
            $output['database'] = '✅ Connected';
            
            // Check if migrations table exists
            try {
                $tables = DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");
                $output['tables'] = array_map(fn($t) => $t->table_name, $tables);
            } catch (\Exception $e) {
                $output['tables_error'] = $e->getMessage();
            }
            
        } catch (\Exception $e) {
            $output['database'] = '❌ Failed: ' . $e->getMessage();
        }
        
        // Test storage paths
        $output['storage'] = [
            '/tmp/storage/framework/views' => is_writable('/tmp/storage/framework/views') ? '✅ Writable' : '❌ Not writable',
            '/tmp/bootstrap/cache' => is_writable('/tmp/bootstrap/cache') ? '✅ Writable' : '❌ Not writable',
        ];
        
        return response()->json($output, 200, [], JSON_PRETTY_PRINT);
        
    } catch (\Throwable $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => explode("\n", $e->getTraceAsString())
        ], 500, [], JSON_PRETTY_PRINT);
    }
});
