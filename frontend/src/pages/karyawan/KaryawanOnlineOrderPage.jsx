import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { Clock, CheckCircle2, ChevronRight, AlertCircle } from 'lucide-react';
import { getOnlineOrders, updateOnlineOrderStatus } from '../../services/karyawanOnlineOrderService';
import { formatDate } from '../../utils/formatDate';
import { showToast } from '../../components/ui/Toast';
import './KaryawanOnlineOrderPage.css';

export default function KaryawanOnlineOrderPage() {
  const queryClient = useQueryClient();

  const { data: response, isLoading, error } = useQuery({
    queryKey: ['karyawanOnlineOrders'],
    queryFn: getOnlineOrders,
    refetchInterval: 10000 // auto-refresh every 10s for new orders
  });

  const orders = response?.data || [];
  
  // Note: Backend might only return Pending orders based on our controller check,
  // but we'll prepare for statuses if they change
  const pendingOrders = orders.filter(o => o.status === 'Pending' || o.status === 'Processing');

  const statusMutation = useMutation({
    mutationFn: ({ id, status }) => updateOnlineOrderStatus(id, status),
    onSuccess: () => {
      showToast('Status pesanan berhasil diupdate', 'success');
      queryClient.invalidateQueries(['karyawanOnlineOrders']);
      queryClient.invalidateQueries(['karyawanDashboard']);
      queryClient.invalidateQueries(['karyawanStocks']); // Stock may be deducted
    },
    onError: () => {
      showToast('Gagal mengupdate pesanan', 'error');
    }
  });

  const handleUpdateStatus = (id, newStatus) => {
    console.log(`Order ${id} updated to ${newStatus}`);
    statusMutation.mutate({ id, status: newStatus });
  };

  const isLate = (dateString) => {
    const orderTime = new Date(dateString).getTime();
    const now = new Date().getTime();
    const diffMins = (now - orderTime) / (1000 * 60);
    return diffMins > 15; // 15 mins
  };

  if (isLoading) return <div style={{ padding: '2rem' }}>Memuat pesanan masuk...</div>;
  if (error) return <div style={{ padding: '2rem', color: 'red' }}>Gagal memuat pesanan online.</div>;

  return (
    <div className="karyawan-online-page">
      <div className="karyawan-page-header">
        <div>
          <h1 className="karyawan-page-title">Pesanan Online Masuk</h1>
          <p className="karyawan-page-subtitle">Siapkan pesanan Delivery & Takeaway</p>
        </div>
      </div>

      <div className="online-order-board">
        {/* Kolom 1: Perlu Disiapkan */}
        <div className="order-column">
          <div className="order-column-header">
            <h3>Perlu Disiapkan</h3>
            <span className="order-count">{pendingOrders.length}</span>
          </div>
          <div className="order-column-content">
            {pendingOrders.length > 0 ? (
              pendingOrders.map(order => {
                const late = isLate(order.created_at);
                const isPriority = late || order.order_type === 'Delivery';
                
                return (
                  <div className={`online-order-card ${isPriority ? 'priority' : ''}`} key={order.order_id}>
                    <div className="order-card-header">
                      <div>
                        <div className="order-customer">{order.customer_name}</div>
                        <div className="order-id">#{order.order_id}</div>
                      </div>
                      <div className={`order-time ${late ? 'late' : ''}`}>
                        {late && <AlertCircle size={14} />}
                        <Clock size={14} />
                        {formatDate(order.created_at).split(' ')[1]}
                      </div>
                    </div>
                    
                    <div className="order-items-list">
                      {order.items && order.items.map((item, idx) => (
                        <div className="order-item" key={idx}>
                          <div>
                            <span className="order-item-qty">{item.quantity}x</span>
                            <span>{item.menuName}</span>
                          </div>
                        </div>
                      ))}
                    </div>

                    <div className="order-card-footer">
                      <span className={`order-type-badge ${String(order.order_type || 'Unknown').toLowerCase().replace(' ', '')}`}>
                        {order.order_type || 'Unknown'}
                      </span>
                      
                      <button 
                        className="btn-process"
                        onClick={() => handleUpdateStatus(order.order_id, 'Completed')}
                        disabled={statusMutation.isPending}
                      >
                        <CheckCircle2 size={16} /> Tandai Selesai
                      </button>
                    </div>
                  </div>
                )
              })
            ) : (
              <div style={{ textAlign: 'center', padding: '3rem 1rem', color: 'var(--color-text-muted)' }}>
                Tidak ada pesanan yang perlu disiapkan.
              </div>
            )}
          </div>
        </div>

        {/* Kolom 2: Siap Diambil / Selesai */}
        {/* Placeholder UI untuk menunjukkan alur kerja */}
        <div className="order-column" style={{ opacity: 0.6 }}>
          <div className="order-column-header">
            <h3>Siap Diambil / Selesai</h3>
            <span className="order-count" style={{ backgroundColor: 'var(--color-text-muted)' }}>-</span>
          </div>
          <div className="order-column-content" style={{ justifyContent: 'center', alignItems: 'center' }}>
            <p style={{ textAlign: 'center', color: 'var(--color-text-muted)' }}>
              Pesanan yang telah ditandai selesai (Completed)<br/>akan hilang dari antrian dan tercatat di laporan.
            </p>
          </div>
        </div>
      </div>
    </div>
  );
}
