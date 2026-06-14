import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { useMutation } from '@tanstack/react-query';
import useAuthStore from '../../stores/authStore';
import { loginOwner } from '../../services/authService';
import { getFriendlyError } from '../../utils/getFriendlyError';
import { showToast } from '../../components/ui/Toast';
import './LoginPage.css';

export default function LoginPage() {
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const navigate = useNavigate();
  const login = useAuthStore((state) => state.login);
  const logout = useAuthStore((state) => state.logout);

  useEffect(() => {
    logout();
  }, [logout]);

  const mutation = useMutation({
    mutationFn: loginOwner,
    onSuccess: (data) => {
      // Allow Owner, Admin, Staff, and Karyawan to login
      const normalizedRole = data.role?.charAt(0).toUpperCase() + data.role?.slice(1).toLowerCase();
      const validRoles = ['Owner', 'Admin', 'Staff', 'Karyawan'];
      if (validRoles.includes(normalizedRole)) {
        login({ ...data, role: normalizedRole });
        showToast('Login berhasil', 'success');
        
        if (normalizedRole === 'Owner') {
          navigate('/owner/dashboard');
        } else if (normalizedRole === 'Admin') {
          navigate('/admin/dashboard');
        } else {
          navigate('/karyawan/dashboard');
        }
      } else {
        showToast('Akun ini tidak memiliki akses ke Dashboard.', 'error');
      }
    },
    onError: (error) => {
      // Backend returns 401 for invalid credentials typically, or 404
      if (error.response?.status === 401 || error.response?.status === 404) {
        showToast('Username atau password salah.', 'error');
      } else {
        showToast(getFriendlyError(error), 'error');
      }
    },
  });

  const handleSubmit = (e) => {
    e.preventDefault();
    if (!username || !password) {
      showToast('Harap isi username dan password', 'error');
      return;
    }
    mutation.mutate({ username, password });
  };

  return (
    <div className="login-page">
      <div className="login-card">
        <div className="login-logo">Embun Cafe</div>
        <h1>Welcome Back</h1>
        <p>Sign in to access your dashboard.</p>

        <form className="login-form" onSubmit={handleSubmit}>
          <div>
            <label htmlFor="username">Username</label>
            <input
              type="text"
              id="username"
              value={username}
              onChange={(e) => setUsername(e.target.value)}
              placeholder="Enter your username"
              disabled={mutation.isPending}
            />
          </div>
          <div>
            <label htmlFor="password">Password</label>
            <input
              type="password"
              id="password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              placeholder="Enter your password"
              disabled={mutation.isPending}
            />
          </div>

          <button
            type="submit"
            className="btn btn-primary"
            disabled={mutation.isPending}
          >
            {mutation.isPending ? 'Signing in...' : 'Sign In'}
          </button>
        </form>
      </div>
    </div>
  );
}
