import Home from "./pages/Home";
import AboutUs from "./pages/AboutUs";
import ContactUs from "./pages/ContactUs";
import { Route, Routes } from "react-router-dom";
import Profile from "./pages/Profile";
import SignIn from "./pages/SignIn";
import { useState } from "react";
import UploadVideo from "./pages/UploadVideo";
import Activities from "./pages/Activities";
import ChatBot from "./components/chatbot/Chatbot";
import Dashboard from './components/Dashboard/Dashboard';
import UsersManagement from './components/Dashboard/Users/UsersManagement';
import EditRole from './components/Dashboard/Users/EditRole';
import CategoriesManagement from './components/Dashboard/Categories/CategoriesManagement';
import EditCategory from './components/Dashboard/Categories/EditCategory';
import ActivitiesManagement from './components/Dashboard/Activities/ActivitiesManagement';
import EditActivity from './components/Dashboard/Activities/EditActivity';

function App() {
  const [showSignIn, setShowSignIn] = useState(false);

  function handleShowSignIn(e) {
    e.preventDefault();
    setShowSignIn(true);
  }
  function handleCloseSignIn(e) {
    e.preventDefault();
    setShowSignIn(false);
  }
  // const handleShowSignIn = () => setShowSignIn(true);
  return (
    <div className="App">
      <ChatBot />
      <Routes>
        <Route path="/" element={<Home onShowSignIn={handleShowSignIn} />} />
        <Route
          path="/AboutUs"
          element={<AboutUs onShowSignIn={handleShowSignIn} />}
        />
        <Route
          path="/ContactUs"
          element={<ContactUs onShowSignIn={handleShowSignIn} />}
        />
        <Route path="/Activities" element={<Activities />} />
        <Route
          path="/UploadVideo"
          element={<UploadVideo onShowSignIn={handleShowSignIn} />}
        />
        <Route path="/dashboard" element={<Dashboard />}>
          <Route path="users" element={<UsersManagement />} />
          <Route path="users/edit/:id" element={<EditRole />} />

          <Route path="categories" element={<CategoriesManagement />} />
          <Route path="categories/edit/:id" element={<EditCategory />} />
          <Route path="categories/create" element={<EditCategory />} />

          <Route path="activities" element={<ActivitiesManagement />} />
          <Route path="activities/edit/:id" element={<EditActivity />} />
          <Route path="activities/create" element={<EditActivity />} />
        </Route>
      </Routes>
      {showSignIn && <SignIn onCloseSignIn={handleCloseSignIn} />}
      {/* <Profile onShowSignIn={handleShowSignIn}/> */}
    </div>
  );
}

export default App;
