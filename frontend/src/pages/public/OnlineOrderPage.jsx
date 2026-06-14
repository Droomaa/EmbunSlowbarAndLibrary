import { useState } from 'react';
import { useQuery } from '@tanstack/react-query';
import { ShoppingCart, Trash2, X, Minus, Plus } from 'lucide-react';
import { fetchMenus } from '../../services/menuService';
import { createOrder } from '../../services/orderService';
import { formatCurrency } from '../../utils/formatCurrency';
import { getFriendlyError } from '../../utils/getFriendlyError';
import { showToast } from '../../components/ui/Toast';
import useCartStore from '../../stores/cartStore';
import Skeleton from '../../components/ui/Skeleton';
import EmptyState from '../../components/ui/EmptyState';
import Modal from '../../components/ui/Modal';
import menuPlaceholder from '../../assets/images/menu-placeholder.png';
import './OnlineOrderPage.css';

const CATEGORIES = ['All Menu', 'Coffee', 'Non-Coffee', 'Main Course', 'Snacks'];

export default function OnlineOrderPage() {
  const [activeCategory, setActiveCategory] = useState('All Menu');
  const [showCheckout, setShowCheckout] = useState(false);
  const [showMobileCart, setShowMobileCart] = useState(false);
  const [customerName, setCustomerName] = useState('');
  const [tableNumber, setTableNumber] = useState('');
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [errors, setErrors] = useState({});

  const { data: menus, isLoading, isError } = useQuery({ queryKey: ['menus'], queryFn: fetchMenus });
  const { items, addItem, removeItem, updateQuantity, clearCart, getTotalItems, getSubtotal } = useCartStore();

  const totalItems = getTotalItems();
  const subtotal = getSubtotal();

  const menuList = Array.isArray(menus) ? menus : [];

  // Category filter: since API doesn't return category field, all categories show all menus
  const filteredMenus = menuList;

  const handleAddToCart = (menu, qty) => {
    for (let i = 0; i < qty; i++) addItem(menu);
  };

  const handleCheckout = async () => {
    const newErrors = {};
    if (!customerName.trim()) newErrors.name = 'Nama wajib diisi.';
    if (!tableNumber.trim()) newErrors.table = 'Nomor meja wajib diisi.';
    if (Object.keys(newErrors).length > 0) { setErrors(newErrors); return; }

    setIsSubmitting(true);
    try {
      await createOrder({
        customer_name: customerName.trim(),
        table_number: tableNumber.trim(),
        items: items.map((i) => ({ menu_id: i.menu_id, quantity: i.quantity })),
      });
      showToast('success', 'Pesanan berhasil dibuat!');
      clearCart();
      setShowCheckout(false);
      setCustomerName('');
      setTableNumber('');
      setErrors({});
    } catch (err) {
      showToast('error', getFriendlyError(err));
    } finally {
      setIsSubmitting(false);
    }
  };

  return (
    <div className="order-page">
      <div className="container order-layout">
        {/* LEFT: Menu Grid */}
        <div className="order-main">
          <div className="order-header">
            <h1>Freshly Brewed for You</h1>
            <p>Skip the line and enjoy our handcrafted beverages and artisanal snacks. Warmth in every sip, rooted in local flavor.</p>
          </div>

          {/* Category Pills */}
          <div className="category-pills">
            {CATEGORIES.map((cat) => (
              <button
                key={cat}
                className={`pill ${activeCategory === cat ? 'pill-active' : ''}`}
                onClick={() => setActiveCategory(cat)}
              >
                {cat}
              </button>
            ))}
          </div>

          {/* Menu Grid */}
          {isLoading && (
            <div className="menu-grid">
              {[1, 2, 3, 4, 5, 6].map((i) => (
                <div key={i} className="card" style={{ padding: 0 }}>
                  <Skeleton width="100%" height="180px" borderRadius="12px 12px 0 0" />
                  <div style={{ padding: '1rem' }}>
                    <Skeleton width="60%" height="18px" style={{ marginBottom: 8 }} />
                    <Skeleton width="40%" height="14px" />
                  </div>
                </div>
              ))}
            </div>
          )}

          {isError && (
            <EmptyState title="Menu gagal dimuat" description="Silakan coba lagi." />
          )}

          {!isLoading && !isError && filteredMenus.length === 0 && (
            <EmptyState title="Menu belum tersedia" />
          )}

          {!isLoading && !isError && filteredMenus.length > 0 && (
            <div className="menu-grid">
              {filteredMenus.map((menu) => (
                <MenuCard key={menu.id} menu={menu} onAddToCart={handleAddToCart} />
              ))}
            </div>
          )}
        </div>

        {/* RIGHT: Cart Panel (Desktop) */}
        <div className="cart-panel-desktop">
          <CartPanelContent
            items={items}
            totalItems={totalItems}
            subtotal={subtotal}
            updateQuantity={updateQuantity}
            removeItem={removeItem}
            onCheckout={() => { if (totalItems > 0) setShowCheckout(true); }}
          />
        </div>
      </div>

      {/* Mobile: Floating Cart Button */}
      {totalItems > 0 && (
        <button className="cart-mobile-trigger" onClick={() => setShowMobileCart(true)} aria-label="Buka keranjang">
          <ShoppingCart size={22} />
          <span>{totalItems} item — {formatCurrency(subtotal)}</span>
        </button>
      )}

      {/* Mobile: Cart Bottom Sheet */}
      {showMobileCart && (
        <div className="cart-bottom-sheet">
          <div className="cart-sheet-overlay" onClick={() => setShowMobileCart(false)} />
          <div className="cart-sheet-content">
            <div className="cart-sheet-header">
              <h3>Your Order</h3>
              <button onClick={() => setShowMobileCart(false)} aria-label="Tutup keranjang"><X size={20} /></button>
            </div>
            <CartPanelContent
              items={items}
              totalItems={totalItems}
              subtotal={subtotal}
              updateQuantity={updateQuantity}
              removeItem={removeItem}
              onCheckout={() => { setShowMobileCart(false); if (totalItems > 0) setShowCheckout(true); }}
            />
          </div>
        </div>
      )}

      {/* Checkout Modal */}
      <Modal isOpen={showCheckout} onClose={() => setShowCheckout(false)}>
        <div className="modal-header">
          <h2 style={{ fontFamily: 'var(--font-heading)' }}>Checkout</h2>
        </div>
        <div className="modal-body">
          <div style={{ marginBottom: 'var(--space-4)' }}>
            <label className="input-label">Customer Name</label>
            <input
              className={`input-field ${errors.name ? 'error' : ''}`}
              placeholder="Enter your full name"
              value={customerName}
              onChange={(e) => { setCustomerName(e.target.value); setErrors((p) => ({ ...p, name: '' })); }}
            />
            {errors.name && <div className="input-error">{errors.name}</div>}
          </div>
          <div style={{ marginBottom: 'var(--space-6)' }}>
            <label className="input-label">Table Number</label>
            <input
              className={`input-field ${errors.table ? 'error' : ''}`}
              placeholder="e.g. 5"
              value={tableNumber}
              onChange={(e) => { setTableNumber(e.target.value); setErrors((p) => ({ ...p, table: '' })); }}
            />
            {errors.table && <div className="input-error">{errors.table}</div>}
          </div>

          <div className="checkout-summary">
            {items.map((item) => (
              <div key={item.menu_id} className="checkout-item">
                <span>{item.menuName} × {item.quantity}</span>
                <span>{formatCurrency(item.price * item.quantity)}</span>
              </div>
            ))}
            <div className="checkout-total">
              <strong>Total</strong>
              <strong>{formatCurrency(subtotal)}</strong>
            </div>
          </div>
        </div>
        <div className="modal-footer">
          <button className="btn btn-outline" onClick={() => setShowCheckout(false)}>Cancel</button>
          <button className="btn btn-primary" onClick={handleCheckout} disabled={isSubmitting}>
            {isSubmitting ? 'Processing...' : 'Confirm Order'}
          </button>
        </div>
      </Modal>
    </div>
  );
}

/* ---- MenuCard Sub-Component ---- */
function MenuCard({ menu, onAddToCart }) {
  const [qty, setQty] = useState(1);

  return (
    <div className="card menu-card">
      <div className="menu-card-img-wrap">
        <img
          src={menu.image_url || menuPlaceholder}
          alt={menu.menuName}
          loading="lazy"
          onError={(e) => { e.target.src = menuPlaceholder; }}
        />
      </div>
      <div className="menu-card-body">
        <h3 className="menu-card-name">{menu.menuName}</h3>
        <p className="menu-card-desc">{menu.description || 'Freshly prepared by Embun Cafe.'}</p>
        <div className="menu-card-footer">
          <span className="menu-card-price">{formatCurrency(menu.price)}</span>
          <div className="qty-stepper">
            <button onClick={() => setQty((q) => Math.max(1, q - 1))} aria-label="Kurangi jumlah"><Minus size={14} /></button>
            <span>{qty}</span>
            <button onClick={() => setQty((q) => q + 1)} aria-label="Tambah jumlah"><Plus size={14} /></button>
          </div>
        </div>
        <button className="btn btn-primary btn-block menu-card-add" onClick={() => { onAddToCart(menu, qty); setQty(1); }}>
          <ShoppingCart size={16} /> Add to Cart
        </button>
      </div>
    </div>
  );
}

/* ---- CartPanelContent Sub-Component ---- */
function CartPanelContent({ items, totalItems, subtotal, updateQuantity, removeItem, onCheckout }) {
  return (
    <div className="cart-panel-inner">
      <div className="cart-panel-header">
        <h3>Your Order</h3>
        {totalItems > 0 && <span className="badge badge-primary">{totalItems} Items</span>}
      </div>

      {items.length === 0 ? (
        <EmptyState title="Your cart is empty" description="Start adding your favorite menu." />
      ) : (
        <>
          <div className="cart-items">
            {items.map((item) => (
              <div key={item.menu_id} className="cart-item">
                <div className="cart-item-img">
                  <img src={item.image_url || menuPlaceholder} alt={item.menuName} onError={(e) => { e.target.src = menuPlaceholder; }} />
                </div>
                <div className="cart-item-info">
                  <span className="cart-item-name">{item.menuName}</span>
                  <span className="cart-item-price">{formatCurrency(item.price)}</span>
                </div>
                <div className="cart-item-actions">
                  <span>Qty: {item.quantity}</span>
                  <button onClick={() => removeItem(item.menu_id)} className="cart-item-delete" aria-label={`Hapus ${item.menuName}`}>
                    <Trash2 size={14} />
                  </button>
                </div>
              </div>
            ))}
          </div>

          <div className="cart-totals">
            <div className="cart-total-row"><span>Subtotal</span><span>{formatCurrency(subtotal)}</span></div>
            <div className="cart-total-row"><span>Service Fee</span><span>{formatCurrency(0)}</span></div>
            <div className="cart-total-row cart-total-final"><strong>Total</strong><strong>{formatCurrency(subtotal)}</strong></div>
          </div>

          <button className="btn btn-primary btn-block btn-lg" onClick={onCheckout}>Proceed to Checkout</button>
        </>
      )}
    </div>
  );
}
