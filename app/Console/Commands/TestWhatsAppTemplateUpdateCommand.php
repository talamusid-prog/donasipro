<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WhatsAppTemplate;

class TestWhatsAppTemplateUpdateCommand extends Command
{
    protected $signature = 'test:wa-template-update {id} {field} {value}';
    protected $description = 'Test update template WhatsApp secara langsung';

    public function handle()
    {
        $id = $this->argument('id');
        $field = $this->argument('field');
        $value = $this->argument('value');
        
        $template = WhatsAppTemplate::find($id);
        if (!$template) {
            $this->error('Template tidak ditemukan!');
            return 1;
        }
        
        $this->info("Template sebelum update:");
        $this->line("ID: {$template->id}");
        $this->line("Name: {$template->name}");
        $this->line("Template: {$template->template}");
        
        try {
            $template->update([$field => $value]);
            $template->refresh();
            
            $this->info("\nTemplate setelah update:");
            $this->line("ID: {$template->id}");
            $this->line("Name: {$template->name}");
            $this->line("Template: {$template->template}");
            
            $this->info("\nUpdate berhasil!");
            return 0;
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return 1;
        }
    }
} 