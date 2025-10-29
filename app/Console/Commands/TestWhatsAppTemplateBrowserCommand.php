<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WhatsAppTemplate;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\WhatsAppTemplateController;

class TestWhatsAppTemplateBrowserCommand extends Command
{
    protected $signature = 'test:wa-template-browser {id}';
    protected $description = 'Test form submission dengan data browser';

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
        $this->line("Title: {$template->title}");
        $this->line("Template: {$template->template}");
        
        // Simulasi data yang dikirim dari browser
        $requestData = [
            '_token' => csrf_token(),
            '_method' => 'PUT',
            'debug' => '1',
            'name' => $template->name,
            'type' => $template->type,
            'title' => '🎉 Terima Kasih Atas Donasi Anda!',
            'template' => $template->template . "\n\n<!-- Updated via browser -->",
            'description' => $template->description,
            'is_active' => 'on'
        ];
        
        $this->info("\nRequest data yang akan dikirim:");
        foreach ($requestData as $key => $value) {
            if ($key === 'template') {
                $this->line("{$key}: [TEMPLATE CONTENT]");
            } else {
                $this->line("{$key}: {$value}");
            }
        }
        
        // Buat request object
        $request = new Request($requestData);
        $request->setMethod('PUT');
        
        // Buat controller instance
        $controller = new WhatsAppTemplateController();
        
        try {
            // Panggil method update
            $response = $controller->update($request, $id);
            
            $this->info("\nResponse status: " . $response->getStatusCode());
            
            // Cek template setelah update
            $template->refresh();
            $this->info("\nTemplate setelah update:");
            $this->line("ID: {$template->id}");
            $this->line("Name: {$template->name}");
            $this->line("Title: {$template->title}");
            $this->line("Template: {$template->template}");
            
            return 0;
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            $this->error("Trace: " . $e->getTraceAsString());
            return 1;
        }
    }
} 