import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import API from '../../../api';

const ActivitiesManagement = () => {
  const [activities, setActivities] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchActivities();
  }, []);

  const fetchActivities = async () => {
    try {
      const response = await API.getActivities();
      setActivities(response.data);
      setLoading(false);
    } catch (error) {
      console.error('Error fetching activities:', error);
      setLoading(false);
    }
  };

  const deleteActivity = async (id) => {
    if (window.confirm('Are you sure you want to delete this activity?')) {
      try {
        await API.deleteActivity(id);
        setActivities(activities.filter(act => act.id !== id));
      } catch (error) {
        console.error('Error deleting activity:', error);
      }
    }
  };

  if (loading) return <div>Loading...</div>;

  return (
    <div className="activities-management">
      <h1>Activities Management</h1>
      <Link to="/dashboard/activities/create" className="btn btn-primary mb-3">Add Activity</Link>

      <div className="table-responsive">
        <table className="table table-striped table-hover">
          <thead>
            <tr>
              <th>Name</th>
              <th>Description</th>
              <th>Content</th>
              <th>Category</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            {activities.map(activity => (
              <tr key={activity.id}>
                <td>{activity.name}</td>
                <td>{activity.description}</td>
                <td>{activity.content.substring(0, 30)}...</td>
                <td>{activity.category?.name || 'No Category'}</td>
                <td>
                  <Link to={`/dashboard/activities/edit/${activity.id}`} className="btn btn-sm btn-success">Edit</Link>
                  <button
                    onClick={() => deleteActivity(activity.id)}
                    className="btn btn-sm btn-danger"
                  >
                    Delete
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
};

export default ActivitiesManagement;
