document.addEventListener('DOMContentLoaded', function() {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    // Tab switching logic
    const tabButtons = document.querySelectorAll('.tab-btn-app');
    
    // Show first tab by default
    const firstTab = document.querySelector('.tab-btn-app');
    if (firstTab) {
        const firstTabName = firstTab.dataset.tab;
        document.getElementById(`content-${firstTabName}`).classList.remove('hidden');
    }
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabName = this.dataset.tab;
            document.querySelectorAll('.tab-btn-app').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.add('hidden'));
            this.classList.add('active');
            document.getElementById(`content-${tabName}`).classList.remove('hidden');
        });
    });
    
    // Zakat Mal calculation
    const zakatMalForm = document.getElementById('zakat-mal-form');
    if (zakatMalForm) {
        zakatMalForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const emas = parseFormattedNumber(document.getElementById('emas').value);
            const uang = parseFormattedNumber(document.getElementById('uang').value);
            
            const hargaEmas = 1145000;
            const nishabDalamRupiah = 85 * hargaEmas;
            const totalHarta = (emas * hargaEmas) + uang;
            const zakatMalResult = document.getElementById('zakat-mal-result');

            if (totalHarta >= nishabDalamRupiah) {
                const zakat = totalHarta * 0.025;
                const campaignsUrl = `/donasi-zakat-mal/${Math.round(zakat)}`;
                zakatMalResult.innerHTML = createResultCard(
                    'check-circle', 'green', 'Anda Wajib Membayar Zakat Mal',
                    `Total Harta Anda: <strong>Rp ${totalHarta.toLocaleString('id-ID')}</strong>`,
                    `Jumlah Zakat (2.5%): <strong class="text-lg">Rp ${zakat.toLocaleString('id-ID')}</strong>`,
                    campaignsUrl
                );
            } else {
                zakatMalResult.innerHTML = createInfoCard(
                    'x-circle', 'yellow', 'Anda Belum Wajib Zakat',
                    `Total harta Anda (<strong>Rp ${totalHarta.toLocaleString('id-ID')}</strong>) belum mencapai nishab.`,
                    `Nishab saat ini: <strong>Rp ${nishabDalamRupiah.toLocaleString('id-ID')}</strong>`
                );
            }
            if (typeof lucide !== 'undefined') lucide.createIcons();
        });
    }
    
    // Zakat Penghasilan calculation
    const zakatPenghasilanForm = document.getElementById('zakat-penghasilan-form');
    if (zakatPenghasilanForm) {
        zakatPenghasilanForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const penghasilanBulanan = parseFormattedNumber(document.getElementById('penghasilan-bulanan').value);
            const pengeluaranBulanan = parseFormattedNumber(document.getElementById('pengeluaran-bulanan').value);
            
            const penghasilanTahunan = penghasilanBulanan * 12;
            const pengeluaranTahunan = pengeluaranBulanan * 12;
            const penghasilanBersih = penghasilanTahunan - pengeluaranTahunan;
            
            // Nishab zakat penghasilan (setara dengan 85 gram emas)
            const hargaEmas = 1145000;
            const nishab = 85 * hargaEmas;
            const zakatPenghasilanResult = document.getElementById('zakat-penghasilan-result');

            if (penghasilanBersih >= nishab) {
                const zakat = penghasilanBersih * 0.025; // 2.5%
                const campaignsUrl = `/donasi-zakat-penghasilan/${Math.round(zakat)}`;
                zakatPenghasilanResult.innerHTML = createResultCard(
                    'briefcase', 'blue', 'Anda Wajib Membayar Zakat Penghasilan',
                    `Penghasilan Bersih Tahunan: <strong>Rp ${penghasilanBersih.toLocaleString('id-ID')}</strong>`,
                    `Jumlah Zakat (2.5%): <strong class="text-lg">Rp ${zakat.toLocaleString('id-ID')}</strong>`,
                    campaignsUrl
                );
            } else {
                zakatPenghasilanResult.innerHTML = createInfoCard(
                    'x-circle', 'yellow', 'Anda Belum Wajib Zakat Penghasilan',
                    `Penghasilan bersih Anda (<strong>Rp ${penghasilanBersih.toLocaleString('id-ID')}</strong>) belum mencapai nishab.`,
                    `Nishab saat ini: <strong>Rp ${nishab.toLocaleString('id-ID')}</strong>`
                );
            }
            if (typeof lucide !== 'undefined') lucide.createIcons();
        });
    }

    // Helper functions to create result cards
    function createResultCard(icon, color, title, line1, line2, url) {
        return `
            <div class="result-card border-${color}-500 bg-${color}-50 animate-fade-in">
                <div class="flex items-start">
                    <i data-lucide="${icon}" class="w-6 h-6 text-${color}-500 mr-3 mt-1 flex-shrink-0"></i>
                    <div>
                        <h4 class="font-bold text-gray-800 text-base mb-2">${title}</h4>
                        <p class="text-sm text-gray-600">${line1}</p>
                        <p class="text-sm font-semibold text-gray-700 mt-1">${line2}</p>
                    </div>
                </div>
                <a href="${url}" class="btn-calculate-green group mt-3 w-full text-center block">
                    <span>Tunaikan Zakat Sekarang</span>
                    <i data-lucide="arrow-right" class="w-4 h-4 ml-2 transition-transform duration-300 group-hover:translate-x-1"></i>
                </a>
            </div>
        `;
    }

    function createInfoCard(icon, color, title, line1, line2) {
        return `
            <div class="result-card border-${color}-500 bg-${color}-50 animate-fade-in">
                <div class="flex items-start">
                    <i data-lucide="${icon}" class="w-6 h-6 text-${color}-500 mr-3 mt-1 flex-shrink-0"></i>
                    <div>
                        <h4 class="font-bold text-gray-800 text-base mb-2">${title}</h4>
                        <p class="text-sm text-gray-600">${line1}</p>
                        <p class="text-xs text-gray-500 mt-1">${line2}</p>
                    </div>
                </div>
            </div>
        `;
    }

    // Format number with thousand separators
    function formatNumber(num) {
        if (num === 0) return '0';
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // Parse formatted number back to number
    function parseFormattedNumber(str) {
        return parseFloat(str.replace(/\./g, '')) || 0;
    }

    // Add input formatting for number fields
    function setupNumberFormatting() {
        const numberInputs = [
            'emas',
            'uang', 
            'penghasilan-bulanan',
            'pengeluaran-bulanan'
        ];

        numberInputs.forEach(inputId => {
            const input = document.getElementById(inputId);
            if (input) {
                // Format on input (real-time formatting)
                input.addEventListener('input', function() {
                    // Remove all non-digit characters
                    let value = this.value.replace(/[^\d]/g, '');
                    
                    // If empty, keep it empty
                    if (value === '') {
                        this.value = '';
                        return;
                    }
                    
                    // Convert to number and format
                    const numValue = parseInt(value);
                    if (!isNaN(numValue)) {
                        this.value = formatNumber(numValue);
                    }
                });
            }
        });
    }

    // Initialize number formatting
    setupNumberFormatting();
});

const style = document.createElement('style');
style.innerHTML = `
    .result-card {
        @apply border-l-4 rounded-r-lg p-4 space-y-3;
    }
    .btn-tunaikan, .btn-calculate-green {
        @apply w-full flex items-center justify-center text-white font-semibold py-2 px-4 rounded-lg shadow hover:bg-green-700 bg-green-600 transition-all duration-300 text-base;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fadeIn 0.4s ease-out forwards;
    }
`;
document.head.appendChild(style);