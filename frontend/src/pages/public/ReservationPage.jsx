import { useState } from 'react';
import { Lock, Calendar, Clock } from 'lucide-react';
import { createReservation } from '../../services/reservationService';
import { getFriendlyError } from '../../utils/getFriendlyError';
import { showToast } from '../../components/ui/Toast';
import reservationImg from '../../assets/images/reservation-cafe.png';
import './ReservationPage.css';

const TIME_SLOTS = ['10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00'];
const PAX_OPTIONS = [1, 2, 3, 4, 5, '6+'];

const initialForm = {
  customer_name: '',
  phone_number: '',
  date: '',
  time: '',
  pax: 2,
  notes: '',
};

export default function ReservationPage() {
  const [form, setForm] = useState(initialForm);
  const [errors, setErrors] = useState({});
  const [isSubmitting, setIsSubmitting] = useState(false);

  const handleChange = (field, value) => {
    setForm((prev) => ({ ...prev, [field]: value }));
    if (errors[field]) setErrors((prev) => ({ ...prev, [field]: '' }));
  };

  const validate = () => {
    const e = {};
    if (!form.customer_name.trim()) e.customer_name = 'Nama wajib diisi.';
    if (!form.phone_number.trim()) e.phone_number = 'Nomor telepon wajib diisi.';
    else if (form.phone_number.replace(/\D/g, '').length < 10) e.phone_number = 'Nomor telepon tidak valid.';
    if (!form.date) e.date = 'Tanggal reservasi wajib dipilih.';
    else {
      const today = new Date().toISOString().split('T')[0];
      if (form.date < today) e.date = 'Tanggal reservasi tidak boleh tanggal lampau.';
    }
    if (!form.time) e.time = 'Jam reservasi wajib dipilih.';
    if (!form.pax) e.pax = 'Jumlah tamu wajib dipilih.';
    if (form.notes.length > 255) e.notes = 'Catatan maksimal 255 karakter.';
    return e;
  };

  const handleSubmit = async (ev) => {
    ev.preventDefault();
    const validationErrors = validate();
    if (Object.keys(validationErrors).length > 0) { setErrors(validationErrors); return; }

    setIsSubmitting(true);
    try {
      // Combine date + time → "YYYY-MM-DD HH:mm:ss"
      const reservation_date = `${form.date} ${form.time}:00`;
      const pax = form.pax === '6+' ? 6 : Number(form.pax);

      await createReservation({
        customer_name: form.customer_name.trim(),
        phone_number: form.phone_number.trim(),
        reservation_date,
        pax,
        notes: form.notes.trim() || null,
      });

      showToast('success', 'Reservasi berhasil dibuat!');
      setForm(initialForm);
      setErrors({});
    } catch (err) {
      showToast('error', getFriendlyError(err));
    } finally {
      setIsSubmitting(false);
    }
  };

  const todayStr = new Date().toISOString().split('T')[0];

  return (
    <div className="reservation-page">
      <div className="container">
        <div className="reservation-card">
          {/* LEFT: Image Panel */}
          <div className="reservation-image">
            <img src={reservationImg} alt="Interior Embun Cafe" />
            <div className="reservation-image-overlay">
              <h2>Secure your spot at our table.</h2>
              <p>Join us for an experience rooted in flavor, surrounded by the tranquility of nature.</p>
            </div>
          </div>

          {/* RIGHT: Form Panel */}
          <div className="reservation-form-panel">
            <h2 className="reservation-form-title">Make a Reservation</h2>
            <p className="reservation-form-sub">Please fill in the details below to book your table.</p>

            <form onSubmit={handleSubmit} noValidate>
              <div className="form-row">
                <div className="form-group">
                  <label className="input-label" htmlFor="res-name">Customer Name</label>
                  <input id="res-name" className={`input-field ${errors.customer_name ? 'error' : ''}`}
                    placeholder="Enter your full name" value={form.customer_name}
                    onChange={(e) => handleChange('customer_name', e.target.value)} />
                  {errors.customer_name && <div className="input-error">{errors.customer_name}</div>}
                </div>
                <div className="form-group">
                  <label className="input-label" htmlFor="res-phone">Phone Number</label>
                  <input id="res-phone" type="tel" className={`input-field ${errors.phone_number ? 'error' : ''}`}
                    placeholder="+1 (555) 000-0000" value={form.phone_number}
                    onChange={(e) => handleChange('phone_number', e.target.value)} />
                  {errors.phone_number && <div className="input-error">{errors.phone_number}</div>}
                </div>
              </div>

              <div className="form-row">
                <div className="form-group">
                  <label className="input-label" htmlFor="res-date">Select Date</label>
                  <div className="input-with-icon">
                    <Calendar className="input-icon" size={18} />
                    <input id="res-date" type="date" className={`input-field has-icon ${errors.date ? 'error' : ''}`}
                      min={todayStr} value={form.date}
                      onChange={(e) => handleChange('date', e.target.value)} />
                  </div>
                  {errors.date && <div className="input-error">{errors.date}</div>}
                </div>
                <div className="form-group">
                  <label className="input-label" htmlFor="res-time">Preferred Time</label>
                  <div className="input-with-icon">
                    <Clock className="input-icon" size={18} />
                    <select id="res-time" className={`input-field has-icon ${errors.time ? 'error' : ''}`}
                      value={form.time} onChange={(e) => handleChange('time', e.target.value)}>
                      <option value="">Select a time</option>
                      {TIME_SLOTS.map((t) => <option key={t} value={t}>{t}</option>)}
                    </select>
                  </div>
                  {errors.time && <div className="input-error">{errors.time}</div>}
                </div>
              </div>

              <div className="form-group">
                <label className="input-label">Number of Guests</label>
                <div className="pax-buttons">
                  {PAX_OPTIONS.map((p) => (
                    <button type="button" key={p}
                      className={`pax-btn ${form.pax === p ? 'pax-active' : ''}`}
                      onClick={() => handleChange('pax', p)}
                    >{p}</button>
                  ))}
                </div>
                {errors.pax && <div className="input-error">{errors.pax}</div>}
              </div>

              <div className="form-group">
                <label className="input-label" htmlFor="res-notes">Special Requests</label>
                <textarea id="res-notes" className="textarea-field"
                  placeholder="Dietary requirements, birthday surprises, or window seating preferences..."
                  value={form.notes} maxLength={255}
                  onChange={(e) => handleChange('notes', e.target.value)} />
                {errors.notes && <div className="input-error">{errors.notes}</div>}
              </div>

              <button type="submit" className="btn btn-primary btn-block btn-lg" disabled={isSubmitting}>
                {isSubmitting ? 'Processing...' : 'Confirm Reservation'}
              </button>

              <p className="reservation-secure">
                <Lock size={14} /> Your booking information is secure
              </p>
            </form>
          </div>
        </div>
      </div>
    </div>
  );
}
