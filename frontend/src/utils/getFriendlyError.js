/**
 * Map HTTP error status codes to user-friendly Indonesian messages.
 * Frontend must NEVER show raw error messages from backend.
 */
export function getFriendlyError(error) {
  if (!error?.response) {
    return 'Koneksi ke server gagal. Periksa koneksi Anda.';
  }

  const status = error.response.status;

  switch (status) {
    case 400:
      return 'Data yang dikirim belum sesuai.';
    case 422:
      return 'Mohon periksa kembali data yang Anda isi.';
    case 429:
      return 'Terlalu banyak request. Silakan coba beberapa saat lagi.';
    case 500:
      return 'Terjadi kesalahan server. Silakan coba lagi.';
    default:
      return 'Terjadi kesalahan. Silakan coba lagi.';
  }
}
