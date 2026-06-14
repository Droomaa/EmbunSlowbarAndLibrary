import { ChevronLeft, ChevronRight } from 'lucide-react';
import './OwnerTable.css';

export default function OwnerTable({
  title,
  subtitle,
  actions,
  columns, // [{ header: 'Product', accessor: 'name' }]
  data,
  isLoading,
  emptyMessage = 'Data belum tersedia',
  renderRow, // function(item, index) => returns <tr>...</tr>
}) {
  return (
    <div className="owner-table-wrapper">
      {(title || actions) && (
        <div className="owner-table-header-row">
          <div>
            {title && <h2 className="owner-table-title">{title}</h2>}
            {subtitle && <p className="owner-table-subtitle">{subtitle}</p>}
          </div>
          {actions && <div className="owner-table-actions">{actions}</div>}
        </div>
      )}

      <div className="owner-table-container">
        <table className="owner-table">
          <thead>
            <tr>
              {columns.map((col, i) => (
                <th key={i}>{col.header}</th>
              ))}
            </tr>
          </thead>
          <tbody>
            {isLoading ? (
              <tr>
                <td colSpan={columns.length} className="owner-table-empty">
                  Loading data...
                </td>
              </tr>
            ) : data && data.length > 0 ? (
              data.map((item, index) => renderRow(item, index))
            ) : (
              <tr>
                <td colSpan={columns.length} className="owner-table-empty">
                  {emptyMessage}
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>

      {!isLoading && data && data.length > 0 && (
        <div className="owner-table-pagination">
          <span>Showing 1 to {data.length} of {data.length} results</span>
          <div className="pagination-controls">
            <button className="page-btn" disabled><ChevronLeft size={16} /></button>
            <button className="page-btn active">1</button>
            <button className="page-btn" disabled><ChevronRight size={16} /></button>
          </div>
        </div>
      )}
    </div>
  );
}
