import React, { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import API from '../../../api';

const EditRole = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const [role, setRole] = useState('user');
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchUser = async () => {
      try {
        const response = await API.getUsers();
        const userData = response.data.find(u => u.id == id);
        if (userData) {
          setUser(userData);
          setRole(userData.role);
        }
      } catch (error) {
        console.error('Failed to fetch user:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchUser();
  }, [id]);

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      await API.updateUserRole(id, role);
      navigate('/dashboard/users');
    } catch (error) {
      console.error('Failed to update role:', error);
    }
  };

  if (loading) return <div>Loading user data...</div>;
  if (!user) return <div>User not found</div>;

  return (
    <div className="edit-role">
      <h2>Edit Role for {user.name}</h2>

      <form onSubmit={handleSubmit}>
        <select
          value={role}
          onChange={(e) => setRole(e.target.value)}
          className="form-select"
        >
          <option value="user">User</option>
          <option value="admin">Admin</option>
        </select>

        <button type="submit" className="btn btn-primary mt-3">Update Role</button>
      </form>
    </div>
  );
};

export default EditRole;
