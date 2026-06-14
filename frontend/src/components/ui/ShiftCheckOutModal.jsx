import { useState } from 'react';
import { useMutation, useQueryClient } from '@tanstack/react-query';
import { Clock, ShoppingBag, Banknote, AlertCircle } from 'lucide-react';
import Modal from './Modal';
import { checkOutShift } from '../../services/staffShiftService';
import { showToast } from './Toast';
import { formatCurrency } from '../../utils/formatCurrency';

export default function ShiftCheckOutModal({ isOpen, onClose, currentShift }) {
  const queryClient = useQueryClient();
  const [closingCash, setClosingCash] = useState('');
  const [checkOutNote, setCheckOutNote] = useState('');

  const mutation = useMutation({
    mutationFn: checkOutShift,
    onSuccess: (data) => {
      showToast('Shift berhasil diakhiri.', 'success');
      queryClient.invalidateQueries({ queryKey: ['current-shift'] });
      onClose();
      setClosingCash('');
      setCheckOutNote('');
    },
    onError: (error) => {
      const msg = error.response?.data?.message || 'Shift gagal diakhiri. Silakan coba lagi.';
      showToast(msg, 'error');
    }
  });

  const handleSubmit = (e) => {
    e.preventDefault();
    mutation.mutate({
      closing_cash: closingCash ? parseFloat(closingCash) : null,
      check_out_note: checkOutNote
    });
  };

  const startedAt = currentShift?.started_at ? new Date(currentShift.started_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '-';
  const ordersCount = currentShift?.current_orders_count || 0;
  const salesTotal = currentShift?.current_sales_total || 0;

  return (
    <Modal isOpen={isOpen} onClose={onClose} title="Akhiri Shift">
      <div style={{ marginBottom: '1.5rem' }}>
        <h4 style={{ marginBottom: '0.75rem', fontSize: '1rem', color: 'var(--color-text-primary)' }}>Ringkasan Shift Anda</h4>
        
        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '0.75rem', marginBottom: '1rem' }}>
          <div style={{ backgroundColor: 'var(--color-surface)', border: '1px solid var(--color-border-soft)', padding: '1rem', borderRadius: '12px', display: 'flex', flexDirection: 'column', gap: '0.5rem' }}>
            <div style={{ display: 'flex', alignItems: 'center', gap: '0.5rem', color: 'var(--color-text-secondary)', fontSize: '0.85rem', fontWeight: 500 }}>
              <Clock size={16} /> Waktu Mulai
            </div>
            <div style={{ fontSize: '1.25rem', fontWeight: 700, color: 'var(--color-text-primary)' }}>{startedAt}</div>
          </div>
          
          <div style={{ backgroundColor: 'var(--color-surface)', border: '1px solid var(--color-border-soft)', padding: '1rem', borderRadius: '12px', display: 'flex', flexDirection: 'column', gap: '0.5rem' }}>
            <div style={{ display: 'flex', alignItems: 'center', gap: '0.5rem', color: 'var(--color-text-secondary)', fontSize: '0.85rem', fontWeight: 500 }}>
              <ShoppingBag size={16} /> Pesanan Dilayani
            </div>
            <div style={{ fontSize: '1.25rem', fontWeight: 700, color: 'var(--color-text-primary)' }}>{ordersCount} <span style={{ fontSize: '0.85rem', fontWeight: 400, color: 'var(--color-text-muted)' }}>pesanan</span></div>
          </div>
        </div>

        <div style={{ display: 'flex', alignItems: 'flex-start', gap: '0.5rem', padding: '0.75rem', backgroundColor: 'var(--color-warning-bg)', color: 'var(--color-warning)', borderRadius: '8px', fontSize: '0.85rem' }}>
          <AlertCircle size={16} style={{ flexShrink: 0, marginTop: '2px' }} />
          <span>Sistem akan mencatat total penjualan <strong>{formatCurrency(salesTotal)}</strong> dari {ordersCount} pesanan setelah Anda mengakhiri shift ini.</span>
        </div>
      </div>
      
      <form onSubmit={handleSubmit}>
        <div className="form-group" style={{ marginBottom: '1rem' }}>
          <label className="input-label" style={{ display: 'block', fontSize: '0.875rem', fontWeight: 600, color: 'var(--color-text-secondary)', marginBottom: '0.5rem' }}>Kas Aktual di Laci (Opsional)</label>
          <div style={{ position: 'relative', display: 'flex', alignItems: 'center' }}>
            <Banknote size={18} style={{ left: '1rem', color: 'var(--color-text-muted)', position: 'absolute', pointerEvents: 'none' }} />
            <input 
              type="number" 
              className="input-field"
              style={{ paddingLeft: '2.5rem', width: '100%', padding: '0.75rem 1rem 0.75rem 2.5rem', background: 'var(--color-soft-neutral)', border: '1.5px solid transparent', borderRadius: '8px', fontSize: '0.875rem', color: 'var(--color-text-primary)' }}
              min="0"
              placeholder="Contoh: 500000"
              value={closingCash}
              onChange={(e) => setClosingCash(e.target.value)}
            />
          </div>
        </div>
        
        <div className="form-group" style={{ marginBottom: '1.5rem' }}>
          <label className="input-label" style={{ display: 'block', fontSize: '0.875rem', fontWeight: 600, color: 'var(--color-text-secondary)', marginBottom: '0.5rem' }}>Catatan Kendala / Laporan (Opsional)</label>
          <textarea 
            className="textarea-field"
            style={{ width: '100%', padding: '0.75rem 1rem', background: 'var(--color-soft-neutral)', border: '1.5px solid transparent', borderRadius: '8px', fontSize: '0.875rem', color: 'var(--color-text-primary)', resize: 'vertical', minHeight: '100px' }}
            placeholder="Ketik catatan jika ada kendala mesin, bahan habis, atau keluhan pelanggan..."
            value={checkOutNote}
            onChange={(e) => setCheckOutNote(e.target.value)}
            rows={3}
            maxLength={255}
          />
        </div>

        <div style={{ display: 'flex', gap: '0.75rem', justifyContent: 'flex-end', paddingTop: '1rem', borderTop: '1px solid var(--color-border-soft)' }}>
          <button type="button" className="btn btn-outline" onClick={onClose} disabled={mutation.isPending}>
            Batal
          </button>
          <button type="submit" className="btn btn-primary" disabled={mutation.isPending} style={{ backgroundColor: 'var(--color-error)', borderColor: 'var(--color-error)' }}>
            {mutation.isPending ? 'Memproses...' : 'Akhiri Shift Sekarang'}
          </button>
        </div>
      </form>
    </Modal>
  );
}
