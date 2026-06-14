import { create } from 'zustand';
import { persist } from 'zustand/middleware';

const useAuthStore = create(
  persist(
    (set) => ({
      token: null,
      role: null,
      userId: null,
      isAuthenticated: false,
      login: (data) =>
        set({
          token: data.token,
          role: data.role,
          userId: data.user_id,
          isAuthenticated: true,
        }),
      logout: () =>
        set({
          token: null,
          role: null,
          userId: null,
          isAuthenticated: false,
        }),
    }),
    {
      name: 'embun_auth', // key for localStorage
    }
  )
);

export default useAuthStore;
