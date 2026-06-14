/**
 * Format a number to Indonesian Rupiah currency format.
 * Example: 8000 → "Rp8.000"
 */
export function formatCurrency(amount) {
  const numericAmount = parseFloat(amount);
  if (isNaN(numericAmount)) return 'Rp0';
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(numericAmount);
}
