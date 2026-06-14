import { useState } from 'react';
import { useMutation, useQueryClient } from '@tanstack/react-query';
import { Banknote, Info } from 'lucide-react';
import Modal from './Modal';
import { checkInShift } from '../../services/staffShiftService';
import { showToast } from './Toast';

export default function ShiftCheckInModal({ isOpen, onClose }) {
  const queryClient = useQueryClient();
  const [openingCash, setOpeningCash] = useState('');
  const [checkInNote, setCheckInNote] = useState('');

  const mutation = useMutation({
    mutationFn: checkInShift,
    onSuccess: () => {
      showToast('Shift berhasil dimulai.', 'success');
      queryClient.invalidateQueries({ queryKey: ['current-shift'] });
      onClose();
      setOpeningCash('');
      setCheckInNote('');
    },
    onError: (error) => {
      const msg = error.response?.data?.message || 'Shift gagal dimulai. Silakan coba lagi.';
      showToast(msg, 'error');
    }
  });

  const handleSubmit = (e) => {
    e.preventDefault();
    mutation.mutate({
      opening_cash: openingCash ? parseFloat(openingCash) : null,
      check_in_note: checkInNote
    });
  };

  return (
    <Modal isOpen={isOpen} onClose={onClose} title="Mulai Shift">
      <div style={{ display: 'flex', alignItems: 'flex-start', gap: '0.5rem', padding: '0.75rem', backgroundColor: 'var(--color-primary-light)', color: 'var(--color-primary)', borderRadius: '8px', fontSize: '0.85rem', marginBottom: '1.5rem' }}>
        <Info size={16} style={{ flexShrink: 0, marginTop: '2px' }} />
        <span>Pastikan Anda sudah siap memulai operasional. Waktu mulai akan dicatat berdasarkan waktu server saat ini.</span>
      </div>
      
      <form onSubmit={handleSubmit}>
        <div className="form-group" style={{ marginBottom: '1rem' }}>
          <label className="input-label" style={{ display: 'block', fontSize: '0.875rem', fontWeight: 600, color: 'var(--color-text-secondary)', marginBottom: '0.5rem' }}>Kas Awal di Laci (Opsional)</label>
          <div style={{ position: 'relative', display: 'flex', alignItems: 'center' }}>
            <Banknote size={18} style={{ left: '1rem', color: 'var(--color-text-muted)', position: 'absolute', pointerEvents: 'none' }} />
            <input 
              type="number" 
              className="input-field"
              style={{ paddingLeft: '2.5rem', width: '100%', padding: '0.75rem 1rem 0.75rem 2.5rem', background: 'var(--color-soft-neutral)', border: '1.5px solid transparent', borderRadius: '8px', fontSize: '0.875rem', color: 'var(--color-text-primary)' }}
              min="0"
              placeholder="Contoh: 100000"
              value={openingCash}
              onChange={(e) => setOpeningCash(e.target.value)}
            />
          </div>
        </div>
        
        <div className="form-group" style={{ marginBottom: '1.5rem' }}>
          <label className="input-label" style={{ display: 'block', fontSize: '0.875rem', fontWeight: 600, color: 'var(--color-text-secondary)', marginBottom: '0.5rem' }}>Catatan Awal Shift (Opsional)</label>
          <textarea 
            className="textarea-field"
            style={{ width: '100%', padding: '0.75rem 1rem', background: 'var(--color-soft-neutral)', border: '1.5px solid transparent', borderRadius: '8px', fontSize: '0.875rem', color: 'var(--color-text-primary)', resize: 'vertical', minHeight: '100px' }}
            placeholder="Masukkan catatan jika ada persiapan khusus atau operasional..."
            value={checkInNote}
            onChange={(e) => setCheckInNote(e.target.value)}
            rows={3}
            maxLength={255}
          />
        </div>

        <div style={{ display: 'flex', gap: '0.75rem', justifyContent: 'flex-end', paddingTop: '1rem', borderTop: '1px solid var(--color-border-soft)' }}>
          <button type="button" className="btn btn-outline" onClick={onClose} disabled={mutation.isPending}>
            Batal
          </button>
          <button type="submit" className="btn btn-primary" disabled={mutation.isPending}>
            {mutation.isPending ? 'Memulai shift...' : 'Mulai Shift Sekarang'}
          </button>
        </div>
      </form>
    </Modal>
  );
}
