import { useState } from 'react';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { ShoppingCart, Minus, Plus, Trash2, AlertCircle } from 'lucide-react';
import { Link } from 'react-router-dom';
import { getCashierMenus, checkoutCashier } from '../../services/karyawanCashierService';
import { getCurrentShift } from '../../services/staffShiftService';
import useCashierStore from '../../stores/cashierStore';
import useAuthStore from '../../stores/authStore';
import { formatCurrency } from '../../utils/formatCurrency';
import { showToast } from '../../components/ui/Toast';
import './KaryawanCashierPage.css';

export default function KaryawanCashierPage() {
  const queryClient = useQueryClient();
  const [activeCategory, setActiveCategory] = useState('Semua');
  const [customerName, setCustomerName] = useState('Walk-in Customer');
  
  const { user } = useAuthStore();
  
  const { data: shiftResponse, isLoading: isShiftLoading } = useQuery({
    queryKey: ['current-shift'],
    queryFn: getCurrentShift,
    retry: false
  });
  
  const currentShift = shiftResponse?.data;
  
  // Zustand store
  const { 
    items, orderType, tableNumber, paymentMethod, amountPaid,
    addItem, removeItem, updateQuantity, updateNote, clearOrder,
    setOrderType, setTableNumber, setPaymentMethod, setAmountPaid,
    getSubtotal, getTax, getTotal, getChange
  } = useCashierStore();

  const { data: response, isLoading, error } = useQuery({
    queryKey: ['cashierMenus'],
    queryFn: getCashierMenus
  });

  const menus = response?.data || [];
  
  // Extract categories dynamically
  const categories = ['Semua', ...new Set(menus.map(m => m.category).filter(Boolean))];

  const filteredMenus = activeCategory === 'Semua' 
    ? menus 
    : menus.filter(m => m.category === activeCategory);

  const checkoutMutation = useMutation({
    mutationFn: checkoutCashier,
    onSuccess: (res) => {
      showToast(res.message || 'Transaksi berhasil!', 'success');
      clearOrder();
      setCustomerName('Walk-in Customer');
      queryClient.invalidateQueries(['karyawanDashboard']);
    },
    onError: () => {
      showToast('Gagal memproses transaksi. Coba lagi.', 'error');
    }
  });

  const handleCheckout = () => {
    if (items.length === 0) {
      showToast('Keranjang kosong', 'error');
      return;
    }
    if (paymentMethod === 'Tunai' && amountPaid < getTotal()) {
      showToast('Uang dibayar kurang dari total', 'error');
      return;
    }
    
    // Format payload for backend
    const payload = {
      customer_name: customerName,
      table_number: tableNumber,
      order_type: orderType,
      payment_method: paymentMethod,
      items: items.map(item => ({
        menu_id: item.menu_id,
        quantity: item.quantity,
        subtotal: item.price * item.quantity
      }))
    };
    
    checkoutMutation.mutate(payload);
  };

  if (isShiftLoading) {
    return <div style={{ padding: '3rem', textAlign: 'center' }}>Memverifikasi shift...</div>;
  }

  // Gate: Jika user Staff tapi belum absen shift
  if (user?.role === 'Staff' && (!currentShift || currentShift.status !== 'Active')) {
    return (
      <div style={{ display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center', height: '100%', padding: '3rem' }}>
        <AlertCircle size={64} color="var(--color-danger)" style={{ marginBottom: '1rem' }} />
        <h2 style={{ marginBottom: '0.5rem' }}>Akses Kasir Terkunci</h2>
        <p style={{ color: 'var(--color-text-muted)', marginBottom: '1.5rem', textAlign: 'center', maxWidth: '400px' }}>
          Anda harus melakukan absen shift terlebih dahulu sebelum dapat menggunakan kasir POS dan memproses pesanan.
        </p>
        <Link to="/karyawan/dashboard" className="btn btn-primary">
          Kembali ke Dashboard
        </Link>
      </div>
    );
  }

  if (isLoading) return <div style={{ padding: '2rem' }}>Memuat menu kasir...</div>;
  if (error) return <div style={{ padding: '2rem', color: 'red' }}>Gagal memuat menu.</div>;

  return (
    <div className="cashier-page">
      {/* 1. KIRI: Katalog Menu */}
      <section className="cashier-menu-section">
        <div className="cashier-filters">
          <div className="cashier-categories">
            {categories.map(cat => (
              <button 
                key={cat} 
                className={`cashier-category-btn ${activeCategory === cat ? 'active' : ''}`}
                onClick={() => setActiveCategory(cat)}
              >
                {cat}
              </button>
            ))}
          </div>
        </div>
        
        <div className="cashier-menu-grid">
          {filteredMenus.map(menu => (
            <div className="cashier-menu-card" key={menu.id || Math.random()} onClick={() => addItem(menu)}>
              <img 
                src={menu.image ? `http://127.0.0.1:8080/storage/${menu.image}` : 'https://placehold.co/400x300?text=No+Image'} 
                alt={menu.menuName || 'Unknown'} 
                className="cashier-menu-img"
                onError={(e) => {
                  e.target.onerror = null;
                  e.target.src = 'https://placehold.co/400x300?text=Error';
                }}
              />
              <div className="cashier-menu-info" style={{ display: 'flex', flexDirection: 'column', padding: '1rem', background: '#fff', color: '#333' }}>
                <h4 style={{ margin: 0, fontSize: '1.05rem', fontWeight: 'bold' }}>{menu.menuName || 'Nama Menu Kosong'}</h4>
                <div className="cashier-menu-price" style={{ marginTop: '0.5rem', fontWeight: 'bold', color: '#2d6a4f' }}>
                  {menu.price ? formatCurrency(menu.price) : 'Rp0'}
                </div>
              </div>
            </div>
          ))}
        </div>
      </section>

      {/* 2. TENGAH: Keranjang Pesanan Aktif */}
      <section className="cashier-cart-section">
        <div className="cart-header">
          <h3>Pesanan Saat Ini</h3>
          {items.length > 0 && (
            <button className="cart-clear-btn" onClick={clearOrder}>Batalkan (Clear)</button>
          )}
        </div>
        
        {items.length === 0 ? (
          <div className="empty-cart">
            <ShoppingCart size={48} style={{ opacity: 0.3, marginBottom: '1rem' }} />
            <p>Pilih menu dari katalog untuk memulai pesanan.</p>
          </div>
        ) : (
          <div className="cart-items">
            {items.map(item => (
              <div className="cart-item" key={item.menu_id}>
                <div className="cart-item-info">
                  <div className="cart-item-name">{item.menuName}</div>
                  <div className="cart-item-price">{formatCurrency(item.price)}</div>
                  <div className="cart-item-controls">
                    <button className="qty-btn" onClick={() => updateQuantity(item.menu_id, item.quantity - 1)}>
                      <Minus size={14} />
                    </button>
                    <span>{item.quantity}</span>
                    <button className="qty-btn" onClick={() => updateQuantity(item.menu_id, item.quantity + 1)}>
                      <Plus size={14} />
                    </button>
                    <button 
                      style={{ background: 'none', border: 'none', color: 'var(--color-danger)', cursor: 'pointer', marginLeft: 'auto' }}
                      onClick={() => removeItem(item.menu_id)}
                    >
                      <Trash2 size={16} />
                    </button>
                  </div>
                </div>
                <div className="cart-item-total">
                  {formatCurrency(item.price * item.quantity)}
                </div>
              </div>
            ))}
          </div>
        )}
      </section>

      {/* 3. KANAN: Form Detail & Pembayaran */}
      <section className="cashier-payment-section">
        <div className="payment-header">
          <h3>Pembayaran</h3>
        </div>
        
        <div className="payment-form">
          <div className="payment-form-group">
            <label>Tipe Pesanan</label>
            <select value={orderType} onChange={(e) => setOrderType(e.target.value)}>
              <option value="Dine In">Dine In (Makan di tempat)</option>
              <option value="Takeaway">Takeaway (Bungkus)</option>
            </select>
          </div>
          
          <div className="payment-form-group">
            <label>Nama Pelanggan</label>
            <input 
              type="text" 
              value={customerName} 
              onChange={(e) => setCustomerName(e.target.value)}
              placeholder="Walk-in Customer"
            />
          </div>
          
          {orderType === 'Dine In' && (
            <div className="payment-form-group">
              <label>Nomor Meja</label>
              <input 
                type="text" 
                value={tableNumber} 
                onChange={(e) => setTableNumber(e.target.value)}
                placeholder="Contoh: 12"
              />
            </div>
          )}
          
          <div className="payment-form-group">
            <label>Metode Pembayaran</label>
            <select value={paymentMethod} onChange={(e) => setPaymentMethod(e.target.value)}>
              <option value="Tunai">Tunai</option>
              <option value="QRIS">QRIS / E-Wallet</option>
              <option value="Kartu Debit/Kredit">Kartu Debit/Kredit</option>
            </select>
          </div>
          
          {paymentMethod === 'Tunai' && (
            <div className="payment-form-group">
              <label>Uang Dibayar (Rp)</label>
              <input 
                type="number" 
                value={amountPaid || ''} 
                onChange={(e) => setAmountPaid(Number(e.target.value))}
                placeholder="0"
              />
            </div>
          )}
          
          <div className="payment-summary">
            <div className="summary-row">
              <span>Subtotal</span>
              <span>{formatCurrency(getSubtotal())}</span>
            </div>
            <div className="summary-row">
              <span>Pajak (10%)</span>
              <span>{formatCurrency(getTax())}</span>
            </div>
            <div className="summary-row total">
              <span>Total Tagihan</span>
              <span>{formatCurrency(getTotal())}</span>
            </div>
            
            {paymentMethod === 'Tunai' && amountPaid > 0 && (
              <div className="summary-row change">
                <span>Kembalian</span>
                <span>{formatCurrency(getChange())}</span>
              </div>
            )}
          </div>
          
          <button 
            className="btn-checkout" 
            disabled={items.length === 0 || checkoutMutation.isPending}
            onClick={handleCheckout}
          >
            {checkoutMutation.isPending ? 'Memproses...' : 'Proses Checkout'}
          </button>
        </div>
      </section>
    </div>
  );
}
