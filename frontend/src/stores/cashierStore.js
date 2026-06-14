import { create } from 'zustand';
import { persist } from 'zustand/middleware';

const useCashierStore = create(
  persist(
    (set, get) => ({
      items: [],
      orderType: 'Dine In',
      tableNumber: '',
      paymentMethod: 'Tunai',
      amountPaid: 0,
      
      addItem: (menu) => set((state) => {
        const existing = state.items.find((item) => item.menu_id === menu.id);
        if (existing) {
          return {
            items: state.items.map((item) => 
              item.menu_id === menu.id 
                ? { ...item, quantity: item.quantity + 1 }
                : item
            )
          };
        }
        return {
          items: [...state.items, { menu_id: menu.id, menuName: menu.menuName, price: menu.price, quantity: 1, note: '' }]
        };
      }),
      
      removeItem: (menuId) => set((state) => ({
        items: state.items.filter((item) => item.menu_id !== menuId)
      })),
      
      updateQuantity: (menuId, quantity) => set((state) => ({
        items: state.items.map((item) => 
          item.menu_id === menuId ? { ...item, quantity: Math.max(1, quantity) } : item
        )
      })),
      
      updateNote: (menuId, note) => set((state) => ({
        items: state.items.map((item) => 
          item.menu_id === menuId ? { ...item, note } : item
        )
      })),
      
      clearOrder: () => set({
        items: [],
        orderType: 'Dine In',
        tableNumber: '',
        paymentMethod: 'Tunai',
        amountPaid: 0
      }),
      
      setOrderType: (type) => set({ orderType: type }),
      setTableNumber: (number) => set({ tableNumber: number }),
      setPaymentMethod: (method) => set({ paymentMethod: method }),
      setAmountPaid: (amount) => set({ amountPaid: amount }),
      
      getSubtotal: () => {
        return get().items.reduce((total, item) => total + (item.price * item.quantity), 0);
      },
      
      getTax: () => {
        return get().getSubtotal() * 0.10;
      },
      
      getTotal: () => {
        return get().getSubtotal() + get().getTax();
      },
      
      getChange: () => {
        return Math.max(0, get().amountPaid - get().getTotal());
      }
    }),
    {
      name: 'embun_cashier_cart',
    }
  )
);

export default useCashierStore;
