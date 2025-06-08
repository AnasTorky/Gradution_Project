import React, { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import API from '../../../api';

const EditCategory = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const [category, setCategory] = useState({
    name: '',
    description: '',
    content: ''
  });
  const [loading, setLoading] = useState(true);
  const [isCreating, setIsCreating] = useState(!id);

  useEffect(() => {
    if (!isCreating) {
      const fetchCategory = async () => {
        try {
          const response = await API.getCategories();
          const categoryData = response.data.find(c => c.id == id);
          if (categoryData) {
            setCategory(categoryData);
          }
          setLoading(false);
        } catch (error) {
          console.error('Error fetching category:', error);
          setLoading(false);
        }
      };
      fetchCategory();
    } else {
      setLoading(false);
    }
  }, [id, isCreating]);

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      if (isCreating) {
        await API.createCategory(category);
      } else {
        await API.updateCategory(id, category);
      }
      navigate('/dashboard/categories');
    } catch (error) {
      console.error('Error saving category:', error);
    }
  };

  if (loading) return <div>Loading...</div>;

  return (
    <div className="edit-category">
      <h1>{isCreating ? 'Create' : 'Edit'} Category</h1>

      <form onSubmit={handleSubmit}>
        <div className="form-group">
          <label>Name</label>
          <input
            type="text"
            className="form-control"
            value={category.name}
            onChange={(e) => setCategory({...category, name: e.target.value})}
            required
          />
        </div>

        <div className="form-group">
          <label>Description</label>
          <textarea
            className="form-control"
            value={category.description}
            onChange={(e) => setCategory({...category, description: e.target.value})}
            required
          />
        </div>

        <div className="form-group">
          <label>Content</label>
          <textarea
            className="form-control"
            value={category.content}
            onChange={(e) => setCategory({...category, content: e.target.value})}
            required
          />
        </div>

        <button type="submit" className="btn btn-primary">Save Category</button>
        <button
          type="button"
          className="btn btn-secondary ms-2"
          onClick={() => navigate('/dashboard/categories')}
        >
          Cancel
        </button>
      </form>
    </div>
  );
};

export default EditCategory;
