@extends('layouts.owner')

@section('title', 'Stock Material Report')

@section('content')
    <style>
        .filter-tab { padding: 8px 16px; border-radius: 20px; font-size: 13px; color: #555; cursor: pointer; transition: 0.2s; border: 1px solid transparent; }
        .filter-tab:hover { background: #f0f0f0; }
        .filter-tab.active { background: #2e5a40; color: white; font-weight: bold; }
    </style>

    <div id="pdf-area" style="padding: 10px; background: #f9f9f6;">
        
        <div id="pdf-header" style="display: none; text-align: center; margin-bottom: 20px;">
            <h2 style="margin: 0; color: #2e5a40;">EMBUN CAFE - STOCK REPORT</h2>
            <p style="margin: 5px 0 0 0; color: #888; font-size: 12px;">Tanggal Unduh: {{ \Carbon\Carbon::now()->format('d M Y, H:i') }}</p>
            <hr style="border: 1px dashed #ddd; margin-top: 15px;">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 2fr; gap: 20px; margin-bottom: 25px;">
            <div class="card" style="background: white; padding: 25px; border-radius: 12px; display: flex; flex-direction: column; justify-content: space-between;">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <span style="font-size: 20px;">📋</span>
                    <span style="font-size: 10px; color: #888; font-weight: bold; text-transform: uppercase;">Overall</span>
                </div>
                <div>
                    <span style="font-size: 12px; color: #888;">Total Items</span>
                    <h2 id="val-total-items" style="margin: 5px 0 0 0; font-size: 32px; color: #333;">0</h2>
                </div>
            </div>

            <div class="card" style="background: #fff5f5; border: 1px solid #ffe3e3; padding: 25px; border-radius: 12px; display: flex; flex-direction: column; justify-content: space-between;">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <span style="font-size: 20px;">⚠️</span>
                    <span style="font-size: 10px; color: #e74c3c; font-weight: bold; text-transform: uppercase;">Critical</span>
                </div>
                <div>
                    <span style="font-size: 12px; color: #e74c3c;">Needing Attention</span>
                    <h2 id="val-attention" style="margin: 5px 0 0 0; font-size: 32px; color: #e74c3c;">0</h2>
                </div>
            </div>

            <div class="card" style="background: white; padding: 25px; border-radius: 12px; display: flex; flex-direction: column; justify-content: space-between;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <span style="font-size: 13px; font-weight: bold; color: #333;">Stock Distribution</span>
                    <span style="font-size: 10px; color: #27ae60; font-weight: bold;">Updated just now</span>
                </div>
                
                <div style="display: flex; height: 12px; border-radius: 6px; overflow: hidden; margin-bottom: 15px; background: #eee;">
                    <div id="bar-safe" style="background: #2e5a40; width: 0%; transition: width 1s;"></div>
                    <div id="bar-low" style="background: #f1c40f; width: 0%; transition: width 1s;"></div>
                    <div id="bar-out" style="background: #e74c3c; width: 0%; transition: width 1s;"></div>
                </div>

                <div style="display: flex; gap: 20px; font-size: 11px; color: #555;">
                    <span><span style="color: #2e5a40;">●</span> Safe (<span id="txt-safe">0</span>%)</span>
                    <span><span style="color: #f1c40f;">●</span> Low (<span id="txt-low">0</span>%)</span>
                    <span><span style="color: #e74c3c;">●</span> Out (<span id="txt-out">0</span>%)</span>
                </div>
            </div>
        </div>

        <div class="card" style="background: white; padding: 25px; border-radius: 12px;">
            
            <div class="filter-row" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <div style="display: flex; gap: 5px;" id="category-tabs">
                    <div class="filter-tab active" onclick="setCategory('All Materials', this)">All Materials</div>
                    <div class="filter-tab" onclick="setCategory('Coffee Beans', this)">Coffee Beans</div>
                    <div class="filter-tab" onclick="setCategory('Dairy & Milk', this)">Dairy & Milk</div>
                    <div class="filter-tab" onclick="setCategory('Syrups', this)">Syrups</div>
                    <div class="filter-tab" onclick="setCategory('General', this)">General Food</div>
                </div>
                
                <div id="action-buttons" style="display: flex; gap: 10px;">
                    <button onclick="sortStock()" style="background: white; border: 1px solid #ddd; padding: 8px 15px; border-radius: 6px; font-size: 13px; cursor: pointer; color: #555; display: flex; align-items: center; gap: 5px;">
                        ⚙️ Urutkan Terendah
                    </button>
                    <button onclick="downloadPDF()" style="background: #e74c3c; border: none; padding: 8px 15px; border-radius: 6px; font-size: 13px; font-weight: bold; cursor: pointer; color: white; display: flex; align-items: center; gap: 5px;">
                        📥 Download PDF
                    </button>
                </div>
            </div>

            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="border-bottom: 2px solid #eee; font-size: 10px; color: #888; text-transform: uppercase;">
                        <th style="padding: 15px 5px;">Material Name</th>
                        <th style="padding: 15px 5px;">Category</th>
                        <th style="padding: 15px 5px;">Current Quantity</th>
                        <th style="padding: 15px 5px;">Unit</th>
                        <th style="padding: 15px 5px;">Status</th>
                    </tr>
                </thead>
                <tbody id="stock-tbody" style="font-size: 13px; color: #333;">
                    <tr><td colspan="5" style="text-align:center; padding: 20px;">⏳ Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
    const API_URL_STOCK = 'http://127.0.0.1:8000/api';
    const token = localStorage.getItem('embun_token');
    
    let allStock = [];
    let currentCategory = 'All Materials';
    let isSortedAsc = false;

    document.addEventListener('DOMContentLoaded', loadStockReports);

    async function loadStockReports() {
        try {
            const response = await fetch(`${API_URL_STOCK}/owner/stock/data`, {
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });
            const result = await response.json();

            if (response.ok) {
                allStock = (result.data || []).map(item => {
                    return { ...item, category: guessCategory(item.item_name) };
                });
                updateMetrics();
                renderTable();
            }
        } catch (error) {
            console.error("Gagal memuat stok:", error);
        }
    }

    function guessCategory(name) {
        const n = name.toLowerCase();
        if(n.includes('bean') || n.includes('espresso') || n.includes('kopi') || n.includes('arabica')) return 'Coffee Beans';
        if(n.includes('milk') || n.includes('susu') || n.includes('dairy') || n.includes('oat')) return 'Dairy & Milk';
        if(n.includes('syrup') || n.includes('sugar') || n.includes('gula') || n.includes('sweet')) return 'Syrups';
        return 'General';
    }

    function updateMetrics() {
        const total = allStock.length;
        document.getElementById('val-total-items').innerText = total;

        let safeCount = 0;
        let lowCount = 0;
        let outCount = 0;

        allStock.forEach(item => {
            const qty = parseFloat(item.quantity);
            if (qty <= 0) outCount++;
            else if (qty <= 500) lowCount++;
            else safeCount++;
        });

        document.getElementById('val-attention').innerText = (outCount + lowCount);

        const safePct = total === 0 ? 0 : Math.round((safeCount / total) * 100);
        const lowPct = total === 0 ? 0 : Math.round((lowCount / total) * 100);
        const outPct = total === 0 ? 0 : Math.round((outCount / total) * 100);

        document.getElementById('bar-safe').style.width = `${safePct}%`;
        document.getElementById('bar-low').style.width = `${lowPct}%`;
        document.getElementById('bar-out').style.width = `${outPct}%`;

        document.getElementById('txt-safe').innerText = safePct;
        document.getElementById('txt-low').innerText = lowPct;
        document.getElementById('txt-out').innerText = outPct;
    }

    window.setCategory = function(categoryName, element) {
        currentCategory = categoryName;
        const tabs = document.querySelectorAll('.filter-tab');
        tabs.forEach(tab => tab.classList.remove('active'));
        element.classList.add('active');
        renderTable();
    }

    window.sortStock = function() {
        isSortedAsc = !isSortedAsc;
        allStock.sort((a, b) => {
            const qtyA = parseFloat(a.quantity);
            const qtyB = parseFloat(b.quantity);
            return isSortedAsc ? (qtyA - qtyB) : (qtyB - qtyA);
        });
        renderTable();
    }

    function renderTable() {
        const tbody = document.getElementById('stock-tbody');
        tbody.innerHTML = '';

        const filteredStock = currentCategory === 'All Materials' 
            ? allStock 
            : allStock.filter(item => item.category === currentCategory);

        if (filteredStock.length > 0) {
            filteredStock.forEach(item => {
                const qty = parseFloat(item.quantity);
                
                let statusBadge = '';
                let qtyColor = '#333';
                
                if (qty <= 0) {
                    statusBadge = '<span style="color: #e74c3c; font-weight: bold; font-size: 11px;">Out of Stock</span>';
                    qtyColor = '#e74c3c';
                } else if (qty <= 500) {
                    statusBadge = '<span style="background: #fff3cd; color: #856404; padding: 4px 10px; border-radius: 20px; font-weight: bold; font-size: 11px;">Low</span>';
                } else {
                    statusBadge = '<span style="background: #e6f4ea; color: #1e8e3e; padding: 4px 10px; border-radius: 20px; font-weight: bold; font-size: 11px;">Safe</span>';
                }

                const formattedQty = new Intl.NumberFormat('en-US').format(qty);

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td style="padding: 15px 5px; border-bottom: 1px solid #f9f9f9;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 16px; height: 16px; border-radius: 50%; background: #e0d5c1;"></div>
                            <strong>${item.item_name}</strong>
                        </div>
                    </td>
                    <td style="padding: 15px 5px; border-bottom: 1px solid #f9f9f9; color: #888;">${item.category}</td>
                    <td style="padding: 15px 5px; border-bottom: 1px solid #f9f9f9; color: ${qtyColor}; font-weight: bold;">${formattedQty}</td>
                    <td style="padding: 15px 5px; border-bottom: 1px solid #f9f9f9; color: #888;">${item.unit}</td>
                    <td style="padding: 15px 5px; border-bottom: 1px solid #f9f9f9;">${statusBadge}</td>
                `;
                tbody.appendChild(row);
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 30px; color: #888;">Tidak ada bahan baku.</td></tr>';
        }
    }

    // Fungsi Ajaib untuk Generate & Download PDF Langsung
    window.downloadPDF = function() {
        const element = document.getElementById('pdf-area');
        const btnGroup = document.getElementById('action-buttons');
        const pdfHeader = document.getElementById('pdf-header');

        // 1. Sembunyikan tombol-tombol agar tidak ikut terfoto
        btnGroup.style.display = 'none';
        // 2. Munculkan kop surat / judul dokumen
        pdfHeader.style.display = 'block';

        // Opsi konfigurasi kertas PDF (A4, Landscape, Resolusi Tinggi)
        const opt = {
            margin:       0.3,
            filename:     'Embun_Stock_Report.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true },
            jsPDF:        { unit: 'in', format: 'a4', orientation: 'landscape' }
        };

        // Mulai proses generate
        html2pdf().set(opt).from(element).save().then(() => {
            // 3. Kembalikan tampilan seperti semula setelah selesai download
            btnGroup.style.display = 'flex';
            pdfHeader.style.display = 'none';
        });
    }
</script>
@endsection