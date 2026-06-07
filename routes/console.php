<?php

use App\Application\Services\Automation\AutomationRunnerService;
use App\Models\ChatbotConversation;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;


use Illuminate\Support\Facades\DB;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Ejecutar en desarrollo php artisan schedule:work
// Schedule::call(function () {
//   ChatbotConversation::where('updated_at', '<', now()->subDay())->delete();
// })->hourly();

Schedule::call(function () {
  app(AutomationRunnerService::class)->runPending();
})->everyMinute();

Artisan::command('automation:test', function () {
    $this->info("=== CONFIGURACIÓN DE PRUEBA DE AUTOMATIZACIÓN ===");
    
    // 1. Preguntar por el número de teléfono
    $phone = $this->ask("Introduce el número de teléfono (Obligatorio, ej: 944340427)");
    while (empty($phone)) {
        $this->error("El número de teléfono no puede estar vacío.");
        $phone = $this->ask("Introduce el número de teléfono (Obligatorio, ej: 944340427)");
    }
    
    // 2. Consultar productos con plantillas/overrides configurados
    $products = DB::table('template_variant_product_overrides')
        ->join('products', 'template_variant_product_overrides.product_id', '=', 'products.id')
        ->select('products.id', 'products.name')
        ->distinct()
        ->get();
        
    $this->info("\nProductos configurados con plantillas de WhatsApp:");
    foreach ($products as $p) {
        $this->line("  [ID: {$p->id}] - {$p->name}");
    }
    
    // 3. Preguntar por el ID de producto
    $productId = $this->ask("Introduce el ID del producto [Por defecto: 1]", 1);
    
    // Validar que el producto existe en base de datos
    $productExists = DB::table('products')->where('id', $productId)->first();
    if (!$productExists) {
        $this->warn("\n¡ADVERTENCIA! El ID del producto {$productId} no existe en la tabla de productos.");
        if (!$this->confirm("¿Deseas continuar de todas formas?", false)) {
            $this->info("Prueba cancelada.");
            return;
        }
    } else {
        $this->info("\nProducto seleccionado: {$productExists->name} (ID: {$productId})");
    }
    
    $this->info("\n=== INICIANDO TEST ===");
    
    // Obtener o crear una fuente de leads
    $source = \App\Models\LeadSource::firstOrCreate(
        ['name' => 'Test Source'],
        ['active' => true]
    );
    $this->line("Lead Source ID: {$source->id}");
    
    $email = 'test_gonzalo_' . time() . '@example.com';
    $name = 'Gonzalo Test';
    
    $lead = \App\Models\Lead::create([
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'product_id' => $productId,
        'source_id' => $source->id,
    ]);
    $this->line("Created Test Lead ID: {$lead->id} | Name: {$lead->name} | Phone: {$lead->phone}");
    
    // Iniciar la automatización de PRODUCTO
    $this->line("Starting automation execution...");
    $executionService = app(\App\Application\Services\Automation\AutomationExecutionService::class);
    $executionService->start($lead, 'PRODUCTO');
    
    // Ejecutar el runner de automatizaciones para encolar los trabajos pendientes
    $this->line("Running AutomationRunnerService->runPending()...");
    $runner = app(\App\Application\Services\Automation\AutomationRunnerService::class);
    $runner->runPending();
    
    // Verificar si se agregaron tareas en la cola de trabajos (jobs table)
    $jobsCount = DB::table('jobs')->count();
    $this->line("Jobs currently in queue: {$jobsCount}");
    
    if ($jobsCount > 0) {
        $this->line("Running queue worker once to process the job...");
        Artisan::call('queue:work', ['--once' => true]);
        $this->info("Queue job executed.");
        
        // Verificamos si falló
        $failedCount = DB::table('failed_jobs')->where('failed_at', '>', now()->subMinutes(1))->count();
        if ($failedCount > 0) {
            $lastFailed = DB::table('failed_jobs')->latest('id')->first();
            $this->error("FAIL DETECTED in failed_jobs table!");
            $this->error("Exception: " . substr($lastFailed->exception, 0, 300) . "...");
        } else {
            $this->info("Job ran successfully without throwing exceptions to the queue worker.");
        }
    } else {
        $this->warn("No jobs were queued. Check database migrations, template status, etc.");
    }
})->purpose('Ejecuta una prueba interactiva de flujo de WhatsApp para un lead');
