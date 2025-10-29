@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('header-title', 'Dashboard Admin')
@section('header-subtitle', 'Selamat datang, ' . auth()->user()->name . '!')

@section('content')
<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                <i data-lucide="heart" class="w-6 h-6"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Campaign</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $totalCampaigns }}</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600">
                <i data-lucide="gift" class="w-6 h-6"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Donasi</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $successfulDonationsCount }}</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                <i data-lucide="users" class="w-6 h-6"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total User</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $totalUsers }}</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                <i data-lucide="dollar-sign" class="w-6 h-6"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Dana Terkumpul</p>
                <p class="text-xl font-semibold text-gray-900">Rp {{ number_format($totalDonations, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Chart and Quick Confirmation Row -->
<div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mb-8">
    <!-- Donation Chart (3 columns) -->
    <div class="lg:col-span-3">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren Donasi (7 Hari Terakhir)</h3>
            <div style="height: 300px;">
                <canvas id="donationChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Quick Confirmation Card (2 columns) -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow p-6 h-full">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Quick Konfirmasi</h3>
                <a href="{{ route('admin.donations.index') }}" class="text-sm text-brand-blue hover:underline">Lihat Semua</a>
            </div>
            
            @php
                $pendingDonations = \App\Models\Donation::where('payment_status', 'waiting_confirmation')
                    ->where('payment_proof', '!=', null)
                    ->with(['campaign'])
                    ->latest()
                    ->take(4)
                    ->get();
            @endphp
            
            @if($pendingDonations->count() > 0)
                <div class="space-y-3">
                    @foreach($pendingDonations as $donation)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                            <div class="flex items-center space-x-3 flex-1 min-w-0">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-blue-600 text-xs font-medium">
                                        {{ strtoupper(substr($donation->donor_name, 0, 1)) }}
                                    </span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $donation->donor_name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ $donation->campaign->title }}</p>
                                    <p class="text-xs text-gray-400">{{ $donation->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2 flex-shrink-0">
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-gray-900">Rp {{ number_format($donation->amount, 0, ',', '.') }}</p>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Menunggu
                                    </span>
                                </div>
                                <div class="flex space-x-1">
                                    <a href="{{ route('admin.donations.show', $donation->id) }}" 
                                       class="inline-flex items-center justify-center w-7 h-7 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                       title="Lihat Detail">
                                        <i data-lucide="eye" class="w-3 h-3"></i>
                                    </a>
                                    <button onclick="confirmDonation({{ $donation->id }}, this)" 
                                            class="inline-flex items-center justify-center w-7 h-7 text-green-600 hover:bg-green-100 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                                            title="Konfirmasi">
                                        <i data-lucide="check" class="w-3 h-3"></i>
                                    </button>
                                    <button onclick="rejectDonation({{ $donation->id }}, this)" 
                                            class="inline-flex items-center justify-center w-7 h-7 text-red-600 hover:bg-red-100 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                            title="Tolak">
                                        <i data-lucide="x" class="w-3 h-3"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i data-lucide="check-circle" class="w-6 h-6 text-gray-400"></i>
                    </div>
                    <p class="text-gray-500 text-sm">Tidak ada donasi yang menunggu konfirmasi</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Recent Activities -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Recent Campaigns -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Campaign Terbaru</h3>
        </div>
        <div class="p-6">
            @php
                $recentCampaigns = \App\Models\Campaign::latest()->take(5)->get();
            @endphp
            @if($recentCampaigns->count() > 0)
                <div class="space-y-4">
                    @foreach($recentCampaigns as $campaign)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <img src="{{ $campaign->image_url ?? 'https://via.placeholder.com/32x32' }}" 
                                        class="w-8 h-8 rounded object-cover">
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $campaign->title }}</p>
                                    <p class="text-xs text-gray-500">{{ $campaign->organization ?? 'Tanpa Organizer' }}</p>
                                </div>
                            </div>
                            <span class="text-xs text-gray-500">{{ $campaign->created_at->diffForHumans() }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4">Belum ada campaign</p>
            @endif
        </div>
    </div>

    <!-- Recent Donations -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Donasi Terbaru</h3>
        </div>
        <div class="p-6">
            @php
                $recentDonations = \App\Models\Donation::with('campaign')->latest()->take(5)->get();
            @endphp
            @if($recentDonations->count() > 0)
                <div class="space-y-4">
                    @foreach($recentDonations as $donation)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                    <span class="text-green-600 text-xs font-medium">
                                        {{ strtoupper(substr($donation->donor_name, 0, 1)) }}
                                    </span>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $donation->donor_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $donation->campaign->title }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">Rp {{ number_format($donation->amount, 0, ',', '.') }}</p>
                                <p class="text-xs text-gray-500">{{ $donation->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4">Belum ada donasi</p>
            @endif
        </div>
    </div>
</div>

<!-- Floating Action Button -->
<div class="fixed bottom-6 right-6 z-[99999]">
    <!-- Main FAB Button -->
    <button id="fabButton" class="w-16 h-16 bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow-2xl hover:shadow-3xl transition-all duration-300 flex items-center justify-center group cursor-pointer" onclick="toggleFabMenu()">
        <i data-lucide="plus" class="w-7 h-7 transition-transform duration-300" id="fabIcon"></i>
    </button>
    
    <!-- FAB Menu Items -->
    <div id="fabMenu" class="absolute right-0 hidden min-w-[280px] bg-white rounded-xl shadow-2xl p-4 border border-gray-200" style="z-index: 99999; bottom: 150px;">
        <!-- Add Campaign -->
        <div class="mb-3" id="fabItem1">
            <a href="{{ route('admin.campaigns.create') }}" class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-all duration-300 group">
                <div class="p-3 bg-blue-100 rounded-lg mr-4">
                    <i data-lucide="plus" class="w-5 h-5 text-blue-600"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-gray-900">Tambah Campaign</p>
                    <p class="text-xs text-gray-500">Buat campaign donasi baru</p>
                </div>
            </a>
        </div>
        
        <!-- View Donations -->
        <div class="mb-3" id="fabItem2">
            <a href="{{ route('admin.donations.index') }}" class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-all duration-300 group">
                <div class="p-3 bg-green-100 rounded-lg mr-4">
                    <i data-lucide="list" class="w-5 h-5 text-green-600"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-gray-900">Lihat Donasi</p>
                    <p class="text-xs text-gray-500">Kelola data donasi</p>
                </div>
            </a>
        </div>
        
        <!-- Manage Users -->
        <div class="mb-3" id="fabItem3">
            <a href="{{ route('admin.users.index') }}" class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-all duration-300 group">
                <div class="p-3 bg-purple-100 rounded-lg mr-4">
                    <i data-lucide="users" class="w-5 h-5 text-purple-600"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-gray-900">Kelola User</p>
                    <p class="text-xs text-gray-500">Manajemen pengguna</p>
                </div>
            </a>
        </div>
    </div>
</div>

<!-- Modern Confirmation Modal -->
<div id="confirmationModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full transform transition-all duration-300 scale-95 opacity-0" id="modalContent">
        <div class="p-6">
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div id="modalIcon" class="w-10 h-10 rounded-full flex items-center justify-center">
                        <!-- Icon will be set dynamically -->
                    </div>
                    <div>
                        <h3 id="modalTitle" class="text-lg font-semibold text-gray-900"></h3>
                        <p id="modalSubtitle" class="text-sm text-gray-500"></p>
                    </div>
                </div>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <!-- Modal Body -->
            <div class="mb-6">
                <div id="donationInfo" class="bg-gray-50 rounded-lg p-4 mb-4 hidden">
                    <div class="flex items-center space-x-3">
                        <div id="donorAvatar" class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <span id="donorInitial" class="text-blue-600 text-sm font-medium"></span>
                        </div>
                        <div class="flex-1">
                            <p id="donorName" class="text-sm font-medium text-gray-900"></p>
                            <p id="campaignTitle" class="text-xs text-gray-500"></p>
                            <p id="donationAmount" class="text-sm font-semibold text-gray-900"></p>
                        </div>
                    </div>
                </div>
                
                <p id="modalMessage" class="text-gray-700"></p>
            </div>
            
            <!-- Modal Footer -->
            <div class="flex space-x-3">
                <button id="cancelBtn" onclick="closeModal()" class="flex-1 px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                    Batal
                </button>
                <button id="confirmBtn" class="flex-1 px-4 py-2 text-white rounded-lg font-medium transition-colors">
                    <!-- Button text and color will be set dynamically -->
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('donationChart');
    
    if (!ctx) {
        console.error('Canvas element not found');
        return;
    }

    const chartData = @json($chartData);
    
    // Debug: log data untuk memastikan data terkirim
    console.log('Chart Data:', chartData);
    
    // Pastikan data ada dan valid
    if (!chartData || !chartData.labels || !chartData.totals) {
        console.error('Invalid chart data');
        return;
    }

    // Fallback: jika semua data 0, tampilkan data dummy untuk demo
    const allZero = chartData.totals.every(total => total === 0);
    if (allZero) {
        console.log('All data is zero, showing demo data');
        chartData.labels = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
        chartData.totals = [150000, 200000, 180000, 250000, 300000, 220000, 280000];
    }

    try {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Total Donasi (Rp)',
                    data: chartData.totals,
                    borderColor: '#22d3ee',
                    backgroundColor: 'rgba(34, 211, 238, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    } catch (error) {
        console.error('Error creating chart:', error);
        // Fallback: tampilkan pesan error di canvas
        ctx.style.display = 'none';
        const errorDiv = document.createElement('div');
        errorDiv.innerHTML = '<p class="text-red-500 text-center py-8">Grafik tidak dapat ditampilkan</p>';
        ctx.parentNode.appendChild(errorDiv);
    }
});

// Quick Confirmation Functions
function confirmDonation(donationId, button) {
    // Get donation data from the DOM
    const donationCard = button.closest('.flex.items-center.justify-between');
    const donorName = donationCard.querySelector('p.text-sm.font-medium.text-gray-900').textContent;
    const campaignTitle = donationCard.querySelector('p.text-xs.text-gray-500').textContent;
    const amount = donationCard.querySelector('p.text-sm.font-semibold.text-gray-900').textContent;
    const donorInitial = donorName.charAt(0).toUpperCase();
    
    // Show confirmation modal
    showConfirmationModal({
        type: 'confirm',
        title: 'Konfirmasi Donasi',
        subtitle: 'Setujui pembayaran donasi',
        message: 'Apakah Anda yakin ingin mengkonfirmasi donasi ini? Pembayaran akan disetujui dan dana akan diterima.',
        donorName: donorName,
        campaignTitle: campaignTitle,
        amount: amount,
        donorInitial: donorInitial,
        confirmText: 'Konfirmasi',
        confirmColor: 'bg-green-600 hover:bg-green-700',
        onConfirm: () => {
            // Disable button and show loading
            button.disabled = true;
            button.innerHTML = '<i data-lucide="loader-2" class="w-3 h-3 animate-spin"></i>';
            
            fetch(`/admin/donations/${donationId}/confirm`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Donasi berhasil dikonfirmasi!', 'success');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showNotification('Gagal mengkonfirmasi donasi!', 'error');
                    // Reset button
                    button.disabled = false;
                    button.innerHTML = '<i data-lucide="check" class="w-3 h-3"></i>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Terjadi kesalahan!', 'error');
                // Reset button
                button.disabled = false;
                button.innerHTML = '<i data-lucide="check" class="w-3 h-3"></i>';
            });
        }
    });
}

function rejectDonation(donationId, button) {
    // Get donation data from the DOM
    const donationCard = button.closest('.flex.items-center.justify-between');
    const donorName = donationCard.querySelector('p.text-sm.font-medium.text-gray-900').textContent;
    const campaignTitle = donationCard.querySelector('p.text-xs.text-gray-500').textContent;
    const amount = donationCard.querySelector('p.text-sm.font-semibold.text-gray-900').textContent;
    const donorInitial = donorName.charAt(0).toUpperCase();
    
    // Show rejection modal
    showConfirmationModal({
        type: 'reject',
        title: 'Tolak Donasi',
        subtitle: 'Tolak pembayaran donasi',
        message: 'Apakah Anda yakin ingin menolak donasi ini? Pembayaran akan ditolak dan donor akan diberitahu.',
        donorName: donorName,
        campaignTitle: campaignTitle,
        amount: amount,
        donorInitial: donorInitial,
        confirmText: 'Tolak',
        confirmColor: 'bg-red-600 hover:bg-red-700',
        onConfirm: () => {
            // Disable button and show loading
            button.disabled = true;
            button.innerHTML = '<i data-lucide="loader-2" class="w-3 h-3 animate-spin"></i>';
            
            fetch(`/admin/donations/${donationId}/reject`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Donasi berhasil ditolak!', 'success');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showNotification('Gagal menolak donasi!', 'error');
                    // Reset button
                    button.disabled = false;
                    button.innerHTML = '<i data-lucide="x" class="w-3 h-3"></i>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Terjadi kesalahan!', 'error');
                // Reset button
                button.disabled = false;
                button.innerHTML = '<i data-lucide="x" class="w-3 h-3"></i>';
            });
        }
    });
}

function showConfirmationModal(config) {
    const modal = document.getElementById('confirmationModal');
    const modalContent = document.getElementById('modalContent');
    const modalIcon = document.getElementById('modalIcon');
    const modalTitle = document.getElementById('modalTitle');
    const modalSubtitle = document.getElementById('modalSubtitle');
    const modalMessage = document.getElementById('modalMessage');
    const confirmBtn = document.getElementById('confirmBtn');
    const donationInfo = document.getElementById('donationInfo');
    const donorInitial = document.getElementById('donorInitial');
    const donorName = document.getElementById('donorName');
    const campaignTitle = document.getElementById('campaignTitle');
    const donationAmount = document.getElementById('donationAmount');
    
    // Set modal content based on type
    if (config.type === 'confirm') {
        modalIcon.className = 'w-10 h-10 bg-green-100 rounded-full flex items-center justify-center';
        modalIcon.innerHTML = '<i data-lucide="check-circle" class="w-6 h-6 text-green-600"></i>';
    } else {
        modalIcon.className = 'w-10 h-10 bg-red-100 rounded-full flex items-center justify-center';
        modalIcon.innerHTML = '<i data-lucide="x-circle" class="w-6 h-6 text-red-600"></i>';
    }
    
    // Set text content
    modalTitle.textContent = config.title;
    modalSubtitle.textContent = config.subtitle;
    modalMessage.textContent = config.message;
    confirmBtn.textContent = config.confirmText;
    confirmBtn.className = `flex-1 px-4 py-2 text-white rounded-lg font-medium transition-colors ${config.confirmColor}`;
    
    // Set donation info
    donorInitial.textContent = config.donorInitial;
    donorName.textContent = config.donorName;
    campaignTitle.textContent = config.campaignTitle;
    donationAmount.textContent = config.amount;
    
    // Show donation info
    donationInfo.classList.remove('hidden');
    
    // Set confirm button action
    confirmBtn.onclick = () => {
        closeModal();
        config.onConfirm();
    };
    
    // Show modal with animation
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    // Animate modal content
    setTimeout(() => {
        modalContent.classList.remove('scale-95', 'opacity-0');
        modalContent.classList.add('scale-100', 'opacity-100');
    }, 10);
    
    // Close modal when clicking outside
    modal.onclick = (e) => {
        if (e.target === modal) {
            closeModal();
        }
    };
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });
}

function closeModal() {
    const modal = document.getElementById('confirmationModal');
    const modalContent = document.getElementById('modalContent');
    
    // Animate modal content out
    modalContent.classList.add('scale-95', 'opacity-0');
    modalContent.classList.remove('scale-100', 'opacity-100');
    
    // Hide modal after animation
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }, 300);
}

function showNotification(message, type) {
    const bgColor = type === 'success' ? '#10b981' : '#ef4444';
    const messageEl = document.createElement('div');
    messageEl.textContent = message;
    messageEl.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${bgColor};
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        font-size: 14px;
        z-index: 9999;
        animation: slideIn 0.3s ease-out;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    `;
    
    document.body.appendChild(messageEl);
    
    setTimeout(() => {
        messageEl.style.animation = 'slideOut 0.3s ease-in';
        setTimeout(() => {
            if (messageEl.parentNode) {
                document.body.removeChild(messageEl);
            }
        }, 300);
    }, 3000);
}

// Add CSS animations for notifications
const notificationStyle = document.createElement('style');
notificationStyle.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    @keyframes spin {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }
    
    .animate-spin {
        animation: spin 1s linear infinite;
    }
    
    button:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    
    button:disabled:hover {
        background-color: transparent !important;
    }
    
    /* FAB Styles */
    .shadow-3xl {
        box-shadow: 0 35px 60px -12px rgba(0, 0, 0, 0.25);
    }
    
    /* Ensure FAB is always visible */
    #fabButton {
        position: fixed !important;
        bottom: 24px !important;
        right: 24px !important;
        z-index: 99999 !important;
    }
    
    #fabMenu {
        z-index: 99999 !important;
        position: absolute !important;
        bottom: 150px !important;
        right: 0 !important;
        background: white !important;
        border-radius: 12px !important;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
        border: 1px solid #e5e7eb !important;
        min-width: 280px !important;
        padding: 16px !important;
        transform-origin: bottom right !important;
    }
    
    /* FAB Menu Animation */
    #fabMenu {
        transform-origin: bottom right;
    }
    
    #fabItem1, #fabItem2, #fabItem3 {
        transform-origin: center;
    }
    
    /* Debug styles */
    #fabMenu:not(.hidden) {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }
    
    /* Force menu visibility */
    #fabMenu[style*="display: block"] {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        z-index: 9998 !important;
    }
    
    /* Ensure menu is above everything */
    #fabMenu {
        position: absolute !important;
        z-index: 9998 !important;
        background: white !important;
        border: 1px solid #e5e7eb !important;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1) !important;
    }
    
    /* Override any conflicting styles */
    #fabMenu:not(.hidden) {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }
    
    /* Force visibility when display is block */
    #fabMenu[style*="display: block"] {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        pointer-events: auto !important;
        z-index: 99999 !important;
    }
    
    /* Additional force styles */
    #fabMenu {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        z-index: 99999 !important;
        transform-origin: bottom right !important;
    }
    
    /* When menu is supposed to be visible */
    #fabMenu:not(.hidden) {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        pointer-events: auto !important;
        transform: scale(1) translateY(0) !important;
        z-index: 99999 !important;
    }
    
    /* When menu is hidden */
    #fabMenu.hidden {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        pointer-events: none !important;
        transform: scale(0.8) translateY(20px) !important;
    }
    
    /* Ensure FAB container is above everything */
    .fixed.bottom-6.right-6 {
        z-index: 99999 !important;
        position: fixed !important;
    }
    
    /* Override any sidebar z-index */
    #fabMenu, #fabButton {
        z-index: 99999 !important;
    }
    
    /* Force above sidebar and any other elements */
    #fabMenu {
        z-index: 999999 !important;
        position: absolute !important;
        background: white !important;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
    }
    
    /* Ensure FAB button is also above everything */
    #fabButton {
        z-index: 999999 !important;
        position: fixed !important;
    }
    
    /* Override any existing z-index from parent elements */
    .fixed.bottom-6.right-6 {
        z-index: 999999 !important;
    }
    
    /* Most aggressive z-index override */
    div[id="fabMenu"] {
        z-index: 999999 !important;
        position: absolute !important;
        background: white !important;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
        border: 2px solid #e5e7eb !important;
        bottom: 150px !important;
        right: 0 !important;
        transform-origin: bottom right !important;
    }
    
    button[id="fabButton"] {
        z-index: 999999 !important;
        position: fixed !important;
    }
    
    /* Override any sidebar or navigation z-index */
    * {
        z-index: auto !important;
    }
    
    #fabMenu, #fabButton, .fixed.bottom-6.right-6 {
        z-index: 999999 !important;
    }
    
    /* Force menu to be on the right side */
    #fabMenu {
        left: auto !important;
        right: 0 !important;
        transform: translateX(0) !important;
        position: absolute !important;
    }
    
    /* Override any left positioning */
    #fabMenu[style*="left"] {
        left: auto !important;
        right: 0 !important;
    }
    
    /* Most specific override for positioning */
    div[id="fabMenu"] {
        left: auto !important;
        right: 0 !important;
        bottom: 150px !important;
        position: absolute !important;
        transform: translateX(0) !important;
    }
    
    /* Ensure parent container doesn't affect positioning */
    .fixed.bottom-6.right-6 {
        position: relative !important;
    }
    
    /* Force right alignment */
    #fabMenu {
        margin-left: auto !important;
        margin-right: 0 !important;
        left: auto !important;
        right: 0 !important;
    }
    
    /* Ensure menu appears above FAB button */
    #fabMenu {
        bottom: 150px !important;
        right: 0 !important;
        margin-bottom: 4px !important;
        position: absolute !important;
        transform: translateX(0) !important;
    }
    
    /* Add arrow pointing to FAB button */
    #fabMenu::after {
        content: '';
        position: absolute;
        bottom: -6px;
        right: 20px;
        width: 0;
        height: 0;
        border-left: 6px solid transparent;
        border-right: 6px solid transparent;
        border-top: 6px solid white;
        filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.1));
    }
`;
document.head.appendChild(notificationStyle);

// FAB Menu Functions
let fabMenuOpen = false;

function toggleFabMenu() {
    const fabMenu = document.getElementById('fabMenu');
    const fabIcon = document.getElementById('fabIcon');
    const fabButton = document.getElementById('fabButton');
    
    console.log('FAB Button clicked!'); // Debug log
    console.log('FAB Menu element:', fabMenu); // Debug log
    console.log('Current classes:', fabMenu.className); // Debug log
    
    if (!fabMenuOpen) {
        // Open menu
        console.log('Opening FAB menu...'); // Debug log
        
        // Remove hidden class
        fabMenu.classList.remove('hidden');
        
        // Change button appearance
        fabIcon.style.transform = 'rotate(45deg)';
        fabButton.classList.add('bg-red-600', 'hover:bg-red-700');
        fabButton.classList.remove('bg-blue-600', 'hover:bg-blue-700');
        
        fabMenuOpen = true;
        console.log('FAB menu opened!'); // Debug log
        console.log('Menu classes after opening:', fabMenu.className); // Debug log
    } else {
        // Close menu
        console.log('Closing FAB menu...'); // Debug log
        
        // Change button appearance back
        fabIcon.style.transform = 'rotate(0deg)';
        fabButton.classList.remove('bg-red-600', 'hover:bg-red-700');
        fabButton.classList.add('bg-blue-600', 'hover:bg-blue-700');
        
        // Hide menu
        fabMenu.classList.add('hidden');
        
        fabMenuOpen = false;
        console.log('FAB menu closed!'); // Debug log
    }
}

// Initialize FAB on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('FAB initialized!'); // Debug log
    const fabButton = document.getElementById('fabButton');
    const fabMenu = document.getElementById('fabMenu');
    
    if (fabButton) {
        console.log('FAB button found!'); // Debug log
    } else {
        console.error('FAB button not found!'); // Debug log
    }
    
    if (fabMenu) {
        console.log('FAB menu found!'); // Debug log
        console.log('FAB menu classes:', fabMenu.className); // Debug log
    } else {
        console.error('FAB menu not found!'); // Debug log
    }
    
    // Test menu visibility
    setTimeout(() => {
        if (fabMenu) {
            console.log('Testing menu visibility...');
            fabMenu.classList.remove('hidden');
            console.log('Menu should be visible now');
            console.log('Menu classes:', fabMenu.className);
            
            setTimeout(() => {
                fabMenu.classList.add('hidden');
                console.log('Menu hidden again');
            }, 3000);
        }
    }, 1000);
});

// Close FAB menu when clicking outside
document.addEventListener('click', function(e) {
    const fabButton = document.getElementById('fabButton');
    const fabMenu = document.getElementById('fabMenu');
    
    if (fabMenuOpen && !fabButton.contains(e.target) && !fabMenu.contains(e.target)) {
        toggleFabMenu();
    }
});

// Close FAB menu with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && fabMenuOpen) {
        toggleFabMenu();
    }
});
</script>
@endpush 