@extends('layouts.admin')

@section('title', 'WA Blast API Templates')

@section('header-title', 'WA Blast API Templates')
@section('header-subtitle', 'Kelola template pesan untuk notifikasi donasi')

@section('content')
<div class="space-y-6">
    <!-- Template List -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <i data-lucide="list" class="w-5 h-5 mr-2 text-blue-600"></i>
                Template Messages
            </h3>
            <button onclick="showAddTemplateModal()" class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                Tambah Template
            </button>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Template</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Variables</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($templates as $template)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $template->name }}</div>
                                <div class="text-sm text-gray-500">{{ $template->description }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-xs truncate">{{ $template->content }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    @if($template->variables)
                                        @foreach(json_decode($template->variables, true) as $var)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-1 mb-1">
                                                {{ $var }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-gray-500">Tidak ada variables</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $template->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $template->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button onclick="testTemplate({{ $template->id }})" 
                                            class="text-blue-600 hover:text-blue-900" title="Test Template">
                                        <i data-lucide="play" class="w-4 h-4"></i>
                                    </button>
                                    <button onclick="editTemplate({{ $template->id }})" 
                                            class="text-green-600 hover:text-green-900" title="Edit">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </button>
                                    <button onclick="deleteTemplate({{ $template->id }})" 
                                            class="text-red-600 hover:text-red-900" title="Delete">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                <div class="flex flex-col items-center py-8">
                                    <i data-lucide="file-text" class="w-12 h-12 text-gray-300 mb-4"></i>
                                    <p>Belum ada template pesan</p>
                                    <button onclick="showAddTemplateModal()" class="mt-2 text-blue-600 hover:text-blue-800">
                                        Tambah template pertama
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Test Section -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i data-lucide="zap" class="w-5 h-5 mr-2 text-blue-600"></i>
            Quick Test Template
        </h3>
        
        <form id="quick-test-form" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="quick-phone" class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon</label>
                    <input type="text" id="quick-phone" name="phone" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="6281234567890" required>
                </div>
                <div>
                    <label for="quick-template" class="block text-sm font-medium text-gray-700 mb-2">Template</label>
                    <select id="quick-template" name="template_id" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Pilih Template</option>
                        @foreach($templates as $template)
                            <option value="{{ $template->id }}">{{ $template->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="quick-variables" class="block text-sm font-medium text-gray-700 mb-2">Variables (JSON)</label>
                    <input type="text" id="quick-variables" name="variables" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder='{"name": "John Doe", "amount": "100000"}' required>
                </div>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="flex items-center px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i data-lucide="send" class="w-4 h-4 mr-2"></i>
                    Test Template
                </button>
            </div>
        </form>
        
        <div id="quick-test-result" class="mt-4"></div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i data-lucide="zap" class="w-5 h-5 mr-2 text-blue-600"></i>
            Quick Actions
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('admin.wa-blast.index') }}" class="flex items-center p-4 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors">
                <i data-lucide="home" class="w-6 h-6 text-blue-600 mr-3"></i>
                <div>
                    <h4 class="font-semibold text-blue-800">Dashboard</h4>
                    <p class="text-sm text-blue-600">Kembali ke dashboard</p>
                </div>
            </a>
            
            <a href="{{ route('admin.wa-blast.settings') }}" class="flex items-center p-4 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 transition-colors">
                <i data-lucide="settings" class="w-6 h-6 text-gray-600 mr-3"></i>
                <div>
                    <h4 class="font-semibold text-gray-800">Settings</h4>
                    <p class="text-sm text-gray-600">Konfigurasi API</p>
                </div>
            </a>
            
            <button onclick="showAddTemplateModal()" class="flex items-center p-4 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition-colors">
                <i data-lucide="plus" class="w-6 h-6 text-green-600 mr-3"></i>
                <div>
                    <h4 class="font-semibold text-green-800">Tambah Template</h4>
                    <p class="text-sm text-green-600">Buat template baru</p>
                </div>
            </button>
        </div>
    </div>
</div>

<!-- Add/Edit Template Modal -->
<div id="templateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800" id="modalTitle">Tambah Template</h3>
            </div>
            
            <form id="templateForm" class="p-6 space-y-4">
                @csrf
                <input type="hidden" id="template_id" name="id">
                
                <div>
                    <label for="template_name" class="block text-sm font-medium text-gray-700 mb-2">Nama Template</label>
                    <input type="text" id="template_name" name="name" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Template Notifikasi Donasi" required>
                </div>
                
                <div>
                    <label for="template_description" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                    <input type="text" id="template_description" name="description" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Template untuk notifikasi donasi baru">
                </div>
                
                <div>
                    <label for="template_content" class="block text-sm font-medium text-gray-700 mb-2">Content Template</label>
                    <textarea id="template_content" name="content" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Halo {donor_name}, terima kasih atas donasi Anda sebesar Rp {amount} untuk campaign {campaign_name}." required></textarea>
                    <p class="text-xs text-gray-500 mt-1">Gunakan {variable_name} untuk placeholder</p>
                </div>
                
                <div>
                    <label for="template_variables" class="block text-sm font-medium text-gray-700 mb-2">Variables (JSON Array)</label>
                    <input type="text" id="template_variables" name="variables" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder='["donor_name", "amount", "campaign_name"]'>
                    <p class="text-xs text-gray-500 mt-1">Daftar variable yang digunakan dalam template</p>
                </div>
                
                <div>
                    <label for="template_active" class="flex items-center">
                        <input type="checkbox" id="template_active" name="is_active" 
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Template Aktif</span>
                    </label>
                </div>
            </form>
            
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                <button onclick="closeTemplateModal()" 
                        class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    Batal
                </button>
                <button onclick="saveTemplate()" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Simpan Template
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Template Modal Functions
function showAddTemplateModal() {
    document.getElementById('modalTitle').textContent = 'Tambah Template';
    document.getElementById('templateForm').reset();
    document.getElementById('template_id').value = '';
    document.getElementById('templateModal').classList.remove('hidden');
}

function closeTemplateModal() {
    document.getElementById('templateModal').classList.add('hidden');
}

function editTemplate(id) {
    // Fetch template data and populate modal
    fetch(`/admin/wa-blast/templates/${id}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('modalTitle').textContent = 'Edit Template';
            document.getElementById('template_id').value = data.id;
            document.getElementById('template_name').value = data.name;
            document.getElementById('template_description').value = data.description;
            document.getElementById('template_content').value = data.content;
            document.getElementById('template_variables').value = data.variables;
            document.getElementById('template_active').checked = data.is_active;
            document.getElementById('templateModal').classList.remove('hidden');
        });
}

function saveTemplate() {
    const form = document.getElementById('templateForm');
    const formData = new FormData(form);
    
    fetch('/admin/wa-blast/templates', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeTemplateModal();
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
}

function deleteTemplate(id) {
    if (confirm('Apakah Anda yakin ingin menghapus template ini?')) {
        fetch(`/admin/wa-blast/templates/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        });
    }
}

function testTemplate(id) {
    const phone = prompt('Masukkan nomor telepon untuk test:');
    if (!phone) return;
    
    const variables = prompt('Masukkan variables (JSON):', '{"donor_name": "Test User", "amount": "100000", "campaign_name": "Test Campaign"}');
    if (!variables) return;
    
    try {
        JSON.parse(variables);
    } catch (e) {
        alert('Format JSON tidak valid');
        return;
    }
    
    fetch('/admin/wa-blast/test-template', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            template_id: id,
            phone: phone,
            variables: JSON.parse(variables)
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Template berhasil dikirim!');
        } else {
            alert('Error: ' + data.message);
        }
    });
}

// Quick Test Form
document.getElementById('quick-test-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = {
        template_id: document.getElementById('quick-template').value,
        phone: document.getElementById('quick-phone').value,
        variables: document.getElementById('quick-variables').value
    };
    
    if (!formData.template_id || !formData.phone || !formData.variables) {
        alert('Semua field harus diisi');
        return;
    }
    
    try {
        JSON.parse(formData.variables);
    } catch (e) {
        alert('Format JSON variables tidak valid');
        return;
    }
    
    fetch('/admin/wa-blast/test-template', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            template_id: formData.template_id,
            phone: formData.phone,
            variables: JSON.parse(formData.variables)
        })
    })
    .then(response => response.json())
    .then(data => {
        const resultDiv = document.getElementById('quick-test-result');
        if (data.success) {
            resultDiv.innerHTML = `
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">
                    <div class="flex items-center">
                        <i data-lucide="check-circle" class="w-5 h-5 mr-2"></i>
                        Template berhasil dikirim!
                    </div>
                </div>
            `;
        } else {
            resultDiv.innerHTML = `
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                    <div class="flex items-center">
                        <i data-lucide="x-circle" class="w-5 h-5 mr-2"></i>
                        Error: ${data.message}
                    </div>
                </div>
            `;
        }
        lucide.createIcons();
    });
});
</script>
@endpush 