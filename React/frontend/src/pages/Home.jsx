import { Typography } from '@mui/material';
import { useAuth } from '../context/AuthContext';

const Home = () => {
  const { user } = useAuth();

  return (
    <div>
      <Typography variant="h4">
        مرحباً {user?.name}!
      </Typography>
      <Typography paragraph>
        أنت الآن في الصفحة الرئيسية
      </Typography>
    </div>
  );
};

export default Home;
