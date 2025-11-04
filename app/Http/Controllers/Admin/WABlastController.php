<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WABlastController extends Controller
{
    protected $whatsappService;

    public function __construct()
    {
        $this->whatsappService = new WhatsAppService();
    }

    /**
     * Tampilkan dashboard WA Blast API
     */
    public function index()
    {
        // Read from database first, fallback to config (same as settings method)
        $config = [
            'base_url' => \App\Models\AppSetting::where('key', 'wa_blast_base_url')->value('value') 
                         ?? config('whatsapp.wa_blast_api.base_url'),
            'api_key' => \App\Models\AppSetting::where('key', 'wa_blast_api_key')->value('value') 
                        ?? config('whatsapp.wa_blast_api.api_key'),
            'session_id' => config('whatsapp.wa_blast_api.session_id'),
            'enabled' => (\App\Models\AppSetting::where('key', 'wa_blast_enabled')->value('value') ?? config('whatsapp.wa_blast_api.enabled')) ? '1' : '0',
        ];
        
        $status = $this->whatsappService->getWABlastStatus();
        $method = config('whatsapp.method');
        
        return view('admin.wa-blast.index', compact('status', 'method', 'config'));
    }

    /**
     * Test koneksi WA Blast API
     */
    public function testConnection()
    {
        try {
            $status = $this->whatsappService->getWABlastStatus();
            
            if ($status['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Koneksi WA Blast API berhasil',
                    'data' => $status['data']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Koneksi WA Blast API gagal: ' . $status['message']
                ]);
            }
        } catch (\Exception $e) {
            Log::error('WA Blast test connection error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Kirim pesan test
     */
    public function sendTestMessage(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'phone' => 'required|string|min:10',
                'message' => 'required|string|min:1'
            ], [
                'phone.required' => 'Nomor telepon wajib diisi',
                'phone.min' => 'Nomor telepon minimal 10 karakter',
                'message.required' => 'Pesan wajib diisi',
                'message.min' => 'Pesan minimal 1 karakter',
            ]);
            
            Log::info('WA Blast test message request', [
                'phone' => $request->phone,
                'message_length' => strlen($request->message)
            ]);

            // Validasi konfigurasi sebelum test
            $baseUrl = \App\Models\AppSetting::where('key', 'wa_blast_base_url')->value('value') 
                      ?? config('whatsapp.wa_blast_api.base_url');
            $apiKey = \App\Models\AppSetting::where('key', 'wa_blast_api_key')->value('value') 
                     ?? config('whatsapp.wa_blast_api.api_key');
            $enabled = \App\Models\AppSetting::where('key', 'wa_blast_enabled')->value('value');
            
            // Check boolean value properly
            if ($enabled === null) {
                $enabled = config('whatsapp.wa_blast_api.enabled', false);
            } else {
                $enabled = (bool) $enabled || $enabled === '1';
            }
            
            Log::info('WA Blast configuration check', [
                'base_url' => $baseUrl ? 'set' : 'not set',
                'api_key' => $apiKey ? 'set' : 'not set',
                'enabled' => $enabled
            ]);

            if (!$baseUrl || !$apiKey) {
                return response()->json([
                    'success' => false,
                    'message' => 'WA Blast API belum dikonfigurasi. Silakan setting Base URL dan API Key terlebih dahulu.',
                    'details' => [
                        'base_url' => $baseUrl ? '✓ Terkonfigurasi' : '✗ Belum dikonfigurasi',
                        'api_key' => $apiKey ? '✓ Terkonfigurasi' : '✗ Belum dikonfigurasi',
                    ]
                ], 400);
            }

            if (!$enabled) {
                return response()->json([
                    'success' => false,
                    'message' => 'WA Blast API tidak aktif. Silakan aktifkan di settings terlebih dahulu.',
                ], 400);
            }

            // Kirim pesan test
            $result = $this->whatsappService->sendNotification(
                $request->phone,
                $request->message,
                'test'
            );

            // Cek hasil dari service
            if (isset($result['success']) && $result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'] ?? 'Pesan berhasil dikirim',
                    'data' => $result,
                    'details' => [
                        'method' => $result['method'] ?? 'wa_blast_api',
                        'phone' => $request->phone,
                        'timestamp' => now()->format('Y-m-d H:i:s')
                    ]
                ]);
            } else {
                // Jika service mengembalikan error
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Gagal mengirim pesan',
                    'error_code' => $result['error_code'] ?? null,
                    'details' => $result
                ], 400);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('WA Blast test message validation error', [
                'errors' => $e->errors()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', array_map(function($errors) {
                    return implode(', ', $errors);
                }, $e->errors())),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('WA Blast test message error: ' . $e->getMessage());
            Log::error('WA Blast test message trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengirim pesan test',
                'error' => $e->getMessage(),
                'details' => [
                    'exception' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }

    /**
     * Kirim template message test
     */
    public function sendTemplateTest(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'template' => 'required|string',
            'variables' => 'array'
        ]);

        try {
            $result = $this->whatsappService->sendTemplateMessage(
                $request->phone,
                $request->template,
                $request->variables ?? []
            );

            return response()->json([
                'success' => true,
                'message' => 'Template message berhasil dikirim',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            Log::error('WA Blast template test error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tampilkan halaman template messages
     */
    public function templates()
    {
        $templates = \App\Models\WhatsAppTemplate::orderBy('created_at', 'desc')->get();
        
        return view('admin.wa-blast.templates', compact('templates'));
    }

    /**
     * Tampilkan halaman settings
     */
    public function settings()
    {
        // Read from database first, fallback to config
        $config = [
            'base_url' => \App\Models\AppSetting::where('key', 'wa_blast_base_url')->value('value') 
                         ?? config('whatsapp.wa_blast_api.base_url'),
            'api_key' => \App\Models\AppSetting::where('key', 'wa_blast_api_key')->value('value') 
                        ?? config('whatsapp.wa_blast_api.api_key'),
            'session_uuid' => \App\Models\AppSetting::where('key', 'wa_blast_session_uuid')->value('value') 
                             ?? config('whatsapp.wa_blast_api.session_uuid', '7d549d3d-a951-478e-b4d7-c90e465bd706'),
            'session_id' => config('whatsapp.wa_blast_api.session_id'),
            'enabled' => (\App\Models\AppSetting::where('key', 'wa_blast_enabled')->value('value') ?? config('whatsapp.wa_blast_api.enabled')) ? '1' : '0',
            'webhook_url' => config('whatsapp.wa_blast_api.webhook_url'),
        ];
        
        return view('admin.wa-blast.settings', compact('config'));
    }

    /**
     * Update settings
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'base_url' => 'required|url',
            'api_key' => 'required|string',
            'session_uuid' => 'required|string|regex:/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/',
            'session_id' => 'nullable|integer|min:1',
            'enabled' => 'boolean'
        ]);

        try {
            // Update UUID session di database
            \App\Models\AppSetting::updateOrCreate(
                ['key' => 'wa_blast_session_uuid'],
                [
                    'value' => $request->session_uuid,
                    'updated_at' => now()
                ]
            );

            // Update base URL di database
            \App\Models\AppSetting::updateOrCreate(
                ['key' => 'wa_blast_base_url'],
                [
                    'value' => $request->base_url,
                    'updated_at' => now()
                ]
            );

            // Update API key di database
            \App\Models\AppSetting::updateOrCreate(
                ['key' => 'wa_blast_api_key'],
                [
                    'value' => $request->api_key,
                    'updated_at' => now()
                ]
            );

            // Update enabled status di database
            \App\Models\AppSetting::updateOrCreate(
                ['key' => 'wa_blast_enabled'],
                [
                    'value' => $request->enabled ? '1' : '0',
                    'updated_at' => now()
                ]
            );

            // Clear all caches to ensure changes take effect
            \Artisan::call('config:clear');
            \Artisan::call('cache:clear');
            \Artisan::call('view:clear');
            
            // Clear application cache
            \Illuminate\Support\Facades\Cache::flush();

            return redirect()->route('admin.wa-blast.settings')
                ->with('success', 'Settings berhasil diperbarui. Cache telah di-clear.');
        } catch (\Exception $e) {
            Log::error('WA Blast settings update error: ' . $e->getMessage());
            return redirect()->route('admin.wa-blast.settings')
                ->with('error', 'Gagal memperbarui settings: ' . $e->getMessage());
        }
    }

    /**
     * Update environment file
     */
    protected function updateEnvironmentFile($data)
    {
        $path = base_path('.env');
        
        if (file_exists($path)) {
            $content = file_get_contents($path);
            
            foreach ($data as $key => $value) {
                if (strpos($content, $key . '=') !== false) {
                    $content = preg_replace(
                        "/^{$key}=.*/m",
                        "{$key}={$value}",
                        $content
                    );
                } else {
                    $content .= "\n{$key}={$value}";
                }
            }
            
            file_put_contents($path, $content);
        }
    }

    /**
     * Test template dengan ID
     */
    public function testTemplate(Request $request)
    {
        $request->validate([
            'template_id' => 'required|integer',
            'phone' => 'required|string',
            'variables' => 'required|array'
        ]);

        try {
            // Get template from database
            $template = \App\Models\WhatsAppTemplate::findOrFail($request->template_id);
            
            if (!$template->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Template tidak aktif'
                ], 400);
            }

            $result = $this->whatsappService->sendTemplateMessage(
                $request->phone,
                $template->content,
                $request->variables,
                'wa_blast_api'
            );

            return response()->json([
                'success' => true,
                'message' => 'Template berhasil dikirim',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            Log::error('WA Blast template test error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim template: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get template by ID
     */
    public function getTemplate($id)
    {
        try {
            $template = \App\Models\WhatsAppTemplate::findOrFail($id);
            return response()->json($template);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Template tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Store new template
     */
    public function storeTemplate(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'required|string',
            'variables' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        try {
            $template = \App\Models\WhatsAppTemplate::create([
                'name' => $request->name,
                'description' => $request->description,
                'content' => $request->content,
                'variables' => $request->variables,
                'is_active' => $request->has('is_active')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Template berhasil disimpan',
                'data' => $template
            ]);
        } catch (\Exception $e) {
            Log::error('WA Blast template store error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan template: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update template
     */
    public function updateTemplate(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'required|string',
            'variables' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        try {
            $template = \App\Models\WhatsAppTemplate::findOrFail($id);
            $template->update([
                'name' => $request->name,
                'description' => $request->description,
                'content' => $request->content,
                'variables' => $request->variables,
                'is_active' => $request->has('is_active')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Template berhasil diperbarui',
                'data' => $template
            ]);
        } catch (\Exception $e) {
            Log::error('WA Blast template update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui template: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete template
     */
    public function deleteTemplate($id)
    {
        try {
            $template = \App\Models\WhatsAppTemplate::findOrFail($id);
            $template->delete();

            return response()->json([
                'success' => true,
                'message' => 'Template berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error('WA Blast template delete error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus template: ' . $e->getMessage()
            ], 500);
        }
    }
} 