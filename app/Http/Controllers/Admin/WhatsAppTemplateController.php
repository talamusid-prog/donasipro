<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $templates = WhatsAppTemplate::orderBy('type')->orderBy('name')->get();
        return view('admin.whatsapp-templates.index', compact('templates'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $types = [
            'donation_confirmation' => 'Konfirmasi Donasi',
            'payment_reminder' => 'Reminder Pembayaran',
            'payment_success' => 'Konfirmasi Pembayaran Berhasil'
        ];
        
        return view('admin.whatsapp-templates.create', compact('types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:whats_app_templates',
            'type' => 'required|string|in:donation_confirmation,payment_reminder,payment_success',
            'title' => 'required|string|max:255',
            'template' => 'required|string',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        try {
            WhatsAppTemplate::create([
                'name' => $request->name,
                'type' => $request->type,
                'title' => $request->title,
                'template' => $request->template,
                'variables' => $this->getVariablesForType($request->type),
                'description' => $request->description,
                'is_active' => $request->has('is_active')
            ]);

            return redirect()->route('admin.whatsapp-templates.index')
                           ->with('success', 'Template WhatsApp berhasil dibuat');
        } catch (\Exception $e) {
            Log::error('Error creating WhatsApp template: ' . $e->getMessage());
            return back()->with('error', 'Gagal membuat template WhatsApp');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $template = WhatsAppTemplate::findOrFail($id);
        return view('admin.whatsapp-templates.show', compact('template'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $template = WhatsAppTemplate::findOrFail($id);
        $types = [
            'donation_confirmation' => 'Konfirmasi Donasi',
            'payment_reminder' => 'Reminder Pembayaran',
            'payment_success' => 'Konfirmasi Pembayaran Berhasil'
        ];
        
        return view('admin.whatsapp-templates.edit', compact('template', 'types'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $template = WhatsAppTemplate::findOrFail($id);
        
        // Log request data untuk debugging
        Log::info('WhatsApp Template Update Request:', [
            'id' => $id,
            'request_data' => $request->all(),
            'template_before' => $template->toArray(),
            'method' => $request->method(),
            'url' => $request->url(),
            'headers' => $request->headers->all(),
            'session_id' => session()->getId(),
            'user_id' => auth()->id()
        ]);
        
        $request->validate([
            'name' => 'required|string|max:255|unique:whats_app_templates,name,' . $id,
            'type' => 'required|string|in:donation_confirmation,payment_reminder,payment_success',
            'title' => 'required|string|max:255',
            'template' => 'required|string',
            'description' => 'nullable|string',
            'is_active' => 'nullable'
        ]);

        try {
            $updateData = [
                'name' => $request->name,
                'type' => $request->type,
                'title' => $request->title,
                'template' => $request->template,
                'variables' => $this->getVariablesForType($request->type),
                'description' => $request->description,
                'is_active' => $request->has('is_active')
            ];
            
            // Log update data
            Log::info('WhatsApp Template Update Data:', $updateData);
            
            // Cek apakah data berubah
            $hasChanges = false;
            foreach ($updateData as $key => $value) {
                if ($template->$key != $value) {
                    Log::info("Field {$key} changed: '{$template->$key}' -> '{$value}'");
                    $hasChanges = true;
                }
            }
            
            if (!$hasChanges) {
                Log::info('No changes detected in template data');
                return redirect()->route('admin.whatsapp-templates.index')
                               ->with('info', 'Tidak ada perubahan pada template');
            }
            
            $result = $template->update($updateData);
            
            // Log result
            Log::info('WhatsApp Template Update Result:', [
                'result' => $result,
                'template_after' => $template->fresh()->toArray()
            ]);
            
            if ($result) {
                return redirect()->route('admin.whatsapp-templates.index')
                               ->with('success', 'Template WhatsApp berhasil diupdate');
            } else {
                Log::error('WhatsApp Template Update failed - no rows affected');
                return back()->with('error', 'Gagal mengupdate template WhatsApp - tidak ada perubahan');
            }
        } catch (\Exception $e) {
            Log::error('Error updating WhatsApp template: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Gagal mengupdate template WhatsApp: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $template = WhatsAppTemplate::findOrFail($id);
            $template->delete();

            return redirect()->route('admin.whatsapp-templates.index')
                           ->with('success', 'Template WhatsApp berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('Error deleting WhatsApp template: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus template WhatsApp');
        }
    }

    /**
     * Toggle active status
     */
    public function toggleActive(string $id)
    {
        try {
            $template = WhatsAppTemplate::findOrFail($id);
            $template->update(['is_active' => !$template->is_active]);

            $status = $template->is_active ? 'diaktifkan' : 'dinonaktifkan';
            return back()->with('success', "Template berhasil {$status}");
        } catch (\Exception $e) {
            Log::error('Error toggling template status: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengubah status template');
        }
    }

    /**
     * Get variables for template type
     */
    protected function getVariablesForType($type)
    {
        $variables = [
            'donation_confirmation' => [
                'donor_name' => 'Nama donatur',
                'campaign_title' => 'Judul kampanye',
                'amount' => 'Nominal donasi (format: 1.000.000)',
                'payment_method' => 'Metode pembayaran',
                'payment_status' => 'Status pembayaran',
                'donation_id' => 'ID donasi',
                'expired_at' => 'Batas waktu pembayaran',
                'payment_url' => 'URL halaman pembayaran'
            ],
            'payment_reminder' => [
                'donor_name' => 'Nama donatur',
                'campaign_title' => 'Judul kampanye',
                'amount' => 'Nominal donasi (format: 1.000.000)',
                'hours_left' => 'Sisa waktu dalam jam',
                'donation_id' => 'ID donasi',
                'payment_url' => 'URL halaman pembayaran'
            ],
            'payment_success' => [
                'donor_name' => 'Nama donatur',
                'campaign_title' => 'Judul kampanye',
                'amount' => 'Nominal donasi (format: 1.000.000)',
                'donation_id' => 'ID donasi'
            ]
        ];

        return $variables[$type] ?? [];
    }
}
