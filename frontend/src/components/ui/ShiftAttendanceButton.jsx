import { useState } from 'react';
import { useQuery } from '@tanstack/react-query';
import { Clock, Play, Square } from 'lucide-react';
import { getCurrentShift } from '../../services/staffShiftService';
import ShiftCheckInModal from './ShiftCheckInModal';
import ShiftCheckOutModal from './ShiftCheckOutModal';

export default function ShiftAttendanceButton({ className }) {
  const [isCheckInOpen, setIsCheckInOpen] = useState(false);
  const [isCheckOutOpen, setIsCheckOutOpen] = useState(false);

  const { data: response, isLoading } = useQuery({
    queryKey: ['current-shift'],
    queryFn: getCurrentShift,
    retry: false
  });

  const currentShift = response?.data;

  if (isLoading) {
    return (
      <button className={`btn btn-secondary ${className || ''}`} disabled>
        <Clock size={18} />
        Memuat status shift...
      </button>
    );
  }

  if (currentShift && currentShift.status === 'Active') {
    return (
      <>
        <button 
          className={`btn btn-primary ${className || ''}`} 
          style={{ backgroundColor: 'var(--color-warning)' }}
          onClick={() => setIsCheckOutOpen(true)}
        >
          <Square size={18} />
          Akhiri Shift
        </button>
        <ShiftCheckOutModal 
          isOpen={isCheckOutOpen} 
          onClose={() => setIsCheckOutOpen(false)} 
          currentShift={currentShift} 
        />
      </>
    );
  }

  return (
    <>
      <button 
        className={`btn btn-primary ${className || ''}`} 
        onClick={() => setIsCheckInOpen(true)}
      >
        <Play size={18} />
        Absen Shift
      </button>
      <ShiftCheckInModal 
        isOpen={isCheckInOpen} 
        onClose={() => setIsCheckInOpen(false)} 
      />
    </>
  );
}
