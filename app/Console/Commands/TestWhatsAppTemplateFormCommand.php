<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WhatsAppTemplate;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\WhatsAppTemplateController;

class TestWhatsAppTemplateFormCommand extends Command
{
    protected $signature = 'test:wa-template-form {id}';
    protected $description = 'Test form submission template WhatsApp secara langsung';

    public function handle()
    {
        $id = $this->argument('id');
        
        $template = WhatsAppTemplate::find($id);
        if (!$template) {
            $this->error('Template tidak ditemukan!');
            return 1;
        }
        
        $this->info("Template sebelum update:");
        $this->line("ID: {$template->id}");
        $this->line("Name: {$template->name}");
        $this->line("Template: {$template->template}");
        
        // Simulasi request data
        $requestData = [
            'name' => $template->name . ' (Updated)',
            'type' => $template->type,
            'title' => $template->title . ' (Updated)',
            'template' => $template->template . "\n\nUpdated at: " . now(),
            'description' => $template->description,
            'is_active' => $template->is_active
        ];
        
        $this->info("\nRequest data yang akan dikirim:");
        foreach ($requestData as $key => $value) {
            $this->line("{$key}: {$value}");
        }
        
        // Buat request object
        $request = new Request($requestData);
        
        // Buat controller instance
        $controller = new WhatsAppTemplateController();
        
        try {
            // Panggil method update
            $response = $controller->update($request, $id);
            
            $this->info("\nResponse:");
            $this->line($response);
            
            // Cek template setelah update
            $template->refresh();
            $this->info("\nTemplate setelah update:");
            $this->line("ID: {$template->id}");
            $this->line("Name: {$template->name}");
            $this->line("Template: {$template->template}");
            
            return 0;
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            $this->error("Trace: " . $e->getTraceAsString());
            return 1;
        }
    }
} 