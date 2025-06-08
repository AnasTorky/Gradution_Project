import React, { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import API from '../../../api';

const EditActivity = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const [activity, setActivity] = useState({
    name: '',
    description: '',
    content: '',
    category_id: ''
  });
  const [categories, setCategories] = useState([]);
  const [loading, setLoading] = useState(true);
  const [isCreating, setIsCreating] = useState(!id);

  useEffect(() => {
    const fetchData = async () => {
      try {
        // جلب الفئات
        const categoriesResponse = await API.getCategories();
        setCategories(categoriesResponse.data);

        // إذا كان تعديل وليس إنشاء، جلب بيانات النشاط
        if (!isCreating) {
          const activityResponse = await API.getActivities();
          const activityData = activityResponse.data.find(a => a.id == id);
          if (activityData) {
            setActivity({
              name: activityData.name,
              description: activityData.description,
              content: activityData.content,
              category_id: activityData.category_id
            });
          }
        }
        setLoading(false);
      } catch (error) {
        console.error('Error fetching data:', error);
        setLoading(false);
      }
    };
    fetchData();
  }, [id, isCreating]);

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      if (isCreating) {
        await API.createActivity(activity);
      } else {
        await API.updateActivity(id, activity);
      }
      navigate('/dashboard/activities');
    } catch (error) {
      console.error('Error saving activity:', error);
    }
  };

  if (loading) return <div>Loading...</div>;

  return (
    <div className="edit-activity">
      <h1>{isCreating ? 'Create' : 'Edit'} Activity</h1>

      <form onSubmit={handleSubmit}>
        <div className="form-group">
          <label>Name</label>
          <input
            type="text"
            className="form-control"
            value={activity.name}
            onChange={(e) => setActivity({...activity, name: e.target.value})}
            required
          />
        </div>

        <div className="form-group">
          <label>Description</label>
          <textarea
            className="form-control"
            value={activity.description}
            onChange={(e) => setActivity({...activity, description: e.target.value})}
            required
          />
        </div>

        <div className="form-group">
          <label>Content</label>
          <textarea
            className="form-control"
            value={activity.content}
            onChange={(e) => setActivity({...activity, content: e.target.value})}
            required
          />
        </div>

        <div className="form-group">
          <label>Category</label>
          <select
            className="form-select"
            value={activity.category_id}
            onChange={(e) => setActivity({...activity, category_id: e.target.value})}
            required
          >
            <option value="">Select Category</option>
            {categories.map(cat => (
              <option key={cat.id} value={cat.id}>{cat.name}</option>
            ))}
          </select>
        </div>

        <button type="submit" className="btn btn-primary">Save Activity</button>
        <button
          type="button"
          className="btn btn-secondary ms-2"
          onClick={() => navigate('/dashboard/activities')}
        >
          Cancel
        </button>
      </form>
    </div>
  );
};

export default EditActivity;
