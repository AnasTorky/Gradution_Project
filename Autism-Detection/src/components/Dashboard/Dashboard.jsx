import React, { useState, useEffect } from 'react';
import { Link, Outlet, useNavigate } from 'react-router-dom';
import { getAuthUser } from '../../utils/auth';
import './Dashboard.css';

const Dashboard = () => {
  const [activeTab, setActiveTab] = useState('dashboard');
  const [user, setUser] = useState(null);
  const navigate = useNavigate();

  useEffect(() => {
    const authUser = getAuthUser();
    if (!authUser) {
      navigate('/login');
    } else {
      setUser(authUser);
    }
  }, [navigate]);

  if (!user) return <div>Loading...</div>;

  return (
    <div className="dashboard-container">
      <div className="sidebar">
        <div className="admin-links">
          <Link to="/dashboard" pageName="Home">Admin Panel</Link>
          <Link
            to="/dashboard/users"
            className={`admin-link ${activeTab === 'users' ? 'active' : ''}`}
            onClick={() => setActiveTab('users')}
          >
            Manage Users
          </Link>
          <Link
            to="/dashboard/categories"
            className={`admin-link ${activeTab === 'categories' ? 'active' : ''}`}
            onClick={() => setActiveTab('categories')}
          >
            Manage Categories
          </Link>
          <Link
            to="/dashboard/activities"
            className={`admin-link ${activeTab === 'activities' ? 'active' : ''}`}
            onClick={() => setActiveTab('activities')}
          >
            Manage Activities
          </Link>
          <Link to="/" pageName="Home">Website</Link>
        </div>
      </div>

      <div className="main-content">
        <div className="content-header">
          <h1>Admin Dashboard</h1>
          <p>Welcome, {user.name}</p>
        </div>
        <Outlet />
      </div>
    </div>
  );
};

export default Dashboard;
