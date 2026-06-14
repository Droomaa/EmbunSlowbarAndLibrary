import { useState, useEffect, useCallback } from 'react';
import { CheckCircle, XCircle, AlertTriangle, X } from 'lucide-react';
import '../../styles/components.css';

let toastId = 0;
let globalAddToast = null;

export function useToast() {
  return {
    success: (message) => globalAddToast?.({ type: 'success', message }),
    error: (message) => globalAddToast?.({ type: 'error', message }),
    warning: (message) => globalAddToast?.({ type: 'warning', message }),
  };
}

export function showToast(type, message) {
  globalAddToast?.({ type, message });
}

const icons = {
  success: CheckCircle,
  error: XCircle,
  warning: AlertTriangle,
};

export default function ToastProvider() {
  const [toasts, setToasts] = useState([]);

  const addToast = useCallback((toast) => {
    const id = ++toastId;
    setToasts((prev) => [...prev, { ...toast, id }]);
    setTimeout(() => {
      setToasts((prev) => prev.filter((t) => t.id !== id));
    }, 4000);
  }, []);

  useEffect(() => {
    globalAddToast = addToast;
    return () => { globalAddToast = null; };
  }, [addToast]);

  return (
    <div className="toast-container">
      {toasts.map((toast) => {
        const Icon = icons[toast.type] || CheckCircle;
        return (
          <div key={toast.id} className={`toast toast-${toast.type}`}>
            <Icon size={20} />
            <span style={{ flex: 1 }}>{toast.message}</span>
            <button
              onClick={() => setToasts((prev) => prev.filter((t) => t.id !== toast.id))}
              style={{ background: 'none', color: 'inherit', padding: 4 }}
              aria-label="Tutup notifikasi"
            >
              <X size={16} />
            </button>
          </div>
        );
      })}
    </div>
  );
}
