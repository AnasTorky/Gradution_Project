import { useState } from 'react';
import { Route, Routes } from 'react-router-dom';
import Home from './pages/Home';
import AboutUs from './pages/AboutUs';
import ContactUs from './pages/ContactUs';
import Profile from './pages/Profile';
import SignIn from './pages/SignIn';
import UploadVideo from './pages/UploadVideo';
import Activities from './pages/Activities';

function App() {
  const [showSignIn, setShowSignIn] = useState(false);

  const handleShowSignIn = (e) => {
    e.preventDefault();
    setShowSignIn(true);
  };

  const handleCloseSignIn = (e) => {
    e.preventDefault();
    setShowSignIn(false);
  };

  return (
    <div className="App">
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
      </Routes>
      {showSignIn && <SignIn onCloseSignIn={handleCloseSignIn} />}
    </div>
  );
}

export default App;
