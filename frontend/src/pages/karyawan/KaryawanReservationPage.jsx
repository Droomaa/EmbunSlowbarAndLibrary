import { useState } from 'react';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { Calendar, Clock, Users, MessageSquare, Check, X } from 'lucide-react';
import { getReservations, verifyReservation, rejectReservation } from '../../services/karyawanReservationService';
import { formatDate } from '../../utils/formatDate';
import { showToast } from '../../components/ui/Toast';
import StatusBadge from '../../components/owner/StatusBadge';
import './KaryawanReservationPage.css';

export default function KaryawanReservationPage() {
  const queryClient = useQueryClient();
  const [filter, setFilter] = useState('Semua');

  const { data: response, isLoading, error } = useQuery({
    queryKey: ['karyawanReservations'],
    queryFn: getReservations
  });

  const reservations = response?.data?.reservations || [];

  const verifyMutation = useMutation({
    mutationFn: ({ id, status }) => verifyReservation(id, { status }),
    onSuccess: () => {
      showToast('Status reservasi berhasil diupdate', 'success');
      queryClient.invalidateQueries(['karyawanReservations']);
      queryClient.invalidateQueries(['karyawanDashboard']);
    },
    onError: () => {
      showToast('Gagal mengupdate reservasi', 'error');
    }
  });

  const handleUpdateStatus = (id, status) => {
    console.log(`Reservation ${id} updated to ${status}`);
    verifyMutation.mutate({ id, status });
  };

  const filteredReservations = reservations.filter(res => {
    if (filter === 'Semua') return true;
    return res.status === filter;
  });

  if (isLoading) return <div style={{ padding: '2rem' }}>Memuat data reservasi...</div>;
  if (error) return <div style={{ padding: '2rem', color: 'red' }}>Gagal memuat reservasi.</div>;

  return (
    <div className="karyawan-reservation-page">
      <div className="karyawan-page-header">
        <div>
          <h1 className="karyawan-page-title">Verifikasi Reservasi</h1>
          <p className="karyawan-page-subtitle">Kelola pesanan meja dari pelanggan</p>
        </div>
        
        <div style={{ display: 'flex', gap: '0.5rem' }}>
          {['Semua', 'Pending', 'Confirmed', 'Completed', 'Cancelled'].map(f => (
            <button 
              key={f}
              onClick={() => setFilter(f)}
              style={{
                padding: '0.4rem 1rem',
                borderRadius: '100px',
                border: filter === f ? 'none' : '1px solid var(--color-border)',
                backgroundColor: filter === f ? 'var(--color-primary)' : 'transparent',
                color: filter === f ? 'white' : 'var(--color-text-main)',
                cursor: 'pointer',
                fontWeight: 600,
                fontSize: '0.85rem'
              }}
            >
              {f}
            </button>
          ))}
        </div>
      </div>

      <div className="reservation-grid">
        {filteredReservations.map(res => (
          <div className={`reservation-card ${String(res.status || 'Pending').toLowerCase()}`} key={res.reservation_id}>
            <div className="reservation-card-header">
              <div className="reservation-customer">
                <h3>{res.customer_name}</h3>
                <span>{res.phone_number}</span>
              </div>
              <StatusBadge status={res.status} />
            </div>

            <div className="reservation-details">
              <div className="reservation-detail-item">
                <Calendar size={18} />
                <span>{formatDate(res.reservation_date).split(' ')[0]}</span>
              </div>
              <div className="reservation-detail-item">
                <Clock size={18} />
                <span>{formatDate(res.reservation_date).split(' ')[1] || res.reservation_date}</span>
              </div>
              <div className="reservation-detail-item">
                <Users size={18} />
                <span>{res.pax} Orang</span>
              </div>
              {res.notes && (
                <div className="reservation-detail-item" style={{ alignItems: 'flex-start' }}>
                  <MessageSquare size={18} style={{ marginTop: '0.2rem' }} />
                  <p className="reservation-notes">{res.notes}</p>
                </div>
              )}
            </div>

            {res.status === 'Pending' && (
              <div className="reservation-actions">
                <button 
                  className="btn-verify" 
                  onClick={() => handleUpdateStatus(res.reservation_id, 'Confirmed')}
                  disabled={verifyMutation.isPending}
                >
                  <Check size={18} /> Konfirmasi
                </button>
                <button 
                  className="btn-reject"
                  onClick={() => handleUpdateStatus(res.reservation_id, 'Cancelled')}
                  disabled={verifyMutation.isPending}
                >
                  <X size={18} /> Tolak
                </button>
              </div>
            )}
            
            {res.status === 'Confirmed' && (
              <div className="reservation-actions">
                <button 
                  className="btn-verify" 
                  onClick={() => handleUpdateStatus(res.reservation_id, 'Completed')}
                  disabled={verifyMutation.isPending}
                  style={{ backgroundColor: '#4b5563' }}
                >
                  <Check size={18} /> Tandai Selesai (Hadir)
                </button>
              </div>
            )}
          </div>
        ))}
        
        {filteredReservations.length === 0 && (
          <div style={{ gridColumn: '1 / -1', textAlign: 'center', padding: '4rem 0', color: 'var(--color-text-muted)' }}>
            Tidak ada reservasi ditemukan untuk filter ini.
          </div>
        )}
      </div>
    </div>
  );
}
