import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { TextField, Button, Container, Typography, Box } from '@mui/material';

const RegisterPage = () => {
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
  });
  const [errors, setErrors] = useState({});
  const { register } = useAuth();
  const navigate = useNavigate();

  const handleChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const [loading, setLoading] = useState(false);

  const handleSubmit = async (e) => {
    e.preventDefault();
    console.log('الفورم اتبعتت ودي الداتا:', formData); // ضيف السطر ده
    setLoading(true);

    try {
      const result = await register(formData);
      if (result.success) {
        navigate('/');
      } else {
        setErrors(result.error?.errors || {});
      }
    } finally {
      setLoading(false);
    }
  };

  return (
    <Container maxWidth="sm">
      <Box sx={{ mt: 4 }}>
        <Typography variant="h4" gutterBottom>تسجيل مستخدم جديد</Typography>
        <form onSubmit={handleSubmit}>
        <TextField
            fullWidth
            required
            margin="normal"
            label="الاسم"
            name="name"
            value={formData.name}
            onChange={handleChange}
            error={!!errors.name}
            helperText={errors.name?.[0]}
        />
          <TextField
            fullWidth
            margin="normal"
            label="البريد الإلكتروني"
            name="email"
            type="email"
            value={formData.email}
            onChange={handleChange}
            error={!!errors.email}
            helperText={errors.email?.[0]}
          />
          <TextField
            fullWidth
            margin="normal"
            label="كلمة المرور"
            name="password"
            type="password"
            value={formData.password}
            onChange={handleChange}
            error={!!errors.password}
            helperText={errors.password?.[0]}
          />
          <TextField
            fullWidth
            margin="normal"
            label="تأكيد كلمة المرور"
            name="password_confirmation"
            type="password"
            value={formData.password_confirmation}
            onChange={handleChange}
          />
          <Button
            type="submit"
            disabled={loading}
            variant="contained"
            fullWidth
            sx={{ mt: 2 }}
          >
            {loading ? 'جاري التسجيل...' : 'تسجيل'}
          </Button>
        </form>
      </Box>
    </Container>
  );
};

export default RegisterPage;
