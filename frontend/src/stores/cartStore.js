import { create } from 'zustand';

const STORAGE_KEY = 'embun_cart';

function loadCart() {
  try {
    const raw = localStorage.getItem(STORAGE_KEY);
    return raw ? JSON.parse(raw) : [];
  } catch {
    return [];
  }
}

function saveCart(items) {
  localStorage.setItem(STORAGE_KEY, JSON.stringify(items));
}

const useCartStore = create((set, get) => ({
  items: loadCart(),

  addItem: (menu) => {
    const items = [...get().items];
    const existing = items.find((i) => i.menu_id === menu.id);
    if (existing) {
      existing.quantity += 1;
    } else {
      items.push({
        menu_id: menu.id,
        menuName: menu.menuName,
        price: menu.price,
        image_url: menu.image_url,
        quantity: 1,
      });
    }
    saveCart(items);
    set({ items });
  },

  removeItem: (menuId) => {
    const items = get().items.filter((i) => i.menu_id !== menuId);
    saveCart(items);
    set({ items });
  },

  updateQuantity: (menuId, qty) => {
    const items = [...get().items];
    const item = items.find((i) => i.menu_id === menuId);
    if (item) {
      item.quantity = Math.max(1, qty);
      saveCart(items);
      set({ items });
    }
  },

  clearCart: () => {
    localStorage.removeItem(STORAGE_KEY);
    set({ items: [] });
  },

  getTotalItems: () => get().items.reduce((sum, i) => sum + i.quantity, 0),

  getSubtotal: () => get().items.reduce((sum, i) => sum + i.price * i.quantity, 0),
}));

export default useCartStore;
