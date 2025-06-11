import { Route, Routes } from "react-router-dom";
import { useState } from "react";
import { AuthProvider } from "./contexts/AuthContext";
import Home from "./pages/Home";
import AboutUs from "./pages/AboutUs";
import ContactUs from "./pages/ContactUs";
import Profile from "./pages/Profile";
import SignIn from "./pages/SignIn";
import UploadVideo from "./pages/UploadVideo";
import Activities from "./pages/Activities";
import ChatBot from "./components/chatbot/Chatbot";
import Header from "./layouts/Header";
import { games } from "./data/games";
import { videos } from "./data/videos";


import AdminDashboard from "./components/Dashboard/Dashboard";
import UsersManagement from "./components/Dashboard/Users/UsersManagement";
import EditRole from "./components/Dashboard/Users/EditRole";
import CategoriesManagement from "./components/Dashboard/Categories/CategoriesManagement";
import EditCategory from "./components/Dashboard/Categories/EditCategory";
import ActivitiesManagement from "./components/Dashboard/Activities/ActivitiesManagement";
import EditActivity from "./components/Dashboard/Activities/EditActivity";

function App() {
    const [showSignIn, setShowSignIn] = useState(false);
    function handleShowSignIn(e) {
        e?.preventDefault();
        setShowSignIn(true);
    }
    function handleCloseSignIn(e) {
        e?.preventDefault();
        setShowSignIn(false);
    }
    return (
        <AuthProvider>
            <div className="App">
                    <Header onShowSignIn={handleShowSignIn} />
                <ChatBot />
                <Routes>
                    <Route
                        path="/"
                        element={<Home onShowSignIn={handleShowSignIn} />}
                    />
                    <Route
                        path="/AboutUs"
                        element={<AboutUs onShowSignIn={handleShowSignIn} />}
                    />
                    <Route
                        path="/ContactUs"
                        element={<ContactUs onShowSignIn={handleShowSignIn} />}
                    />
                    {/* <Route path="/Activities" element={<Activities />} /> */}
                    <Route
                        path="/UploadVideo"
                        element={
                            <UploadVideo onShowSignIn={handleShowSignIn} />
                        }
                    />
                    <Route path="/Profile" element={<Profile />} />

                    <Route
                        path="/Activities"
                        element={
                            <Activities
                                items={games}
                                containerTitle="Explore Games & Educational Activities"
                                containerDesc="Choose a game to play"
                                type="game"
                                onShowSignIn={handleShowSignIn}
                            />
                        }
                    />
                    <Route
                        path="/Videos"
                        element={
                            <Activities
                                items={videos}
                                containerTitle="Explore Educational Videos"
                                containerDesc="Watch videos to support autism care and learning"
                                type="video"
                                onShowSignIn={handleShowSignIn}
                            />
                        }
                    />
                    <Route
                        path="/Communication"
                        element={
                            <Activities
                                items={{}}
                                containerTitle="Explore Educational Videos"
                                containerDesc="Watch videos to support autism care and learning"
                                type="video"
                            />
                        }
                    />
                    <Route path="/dashboard" element={<AdminDashboard />}>
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
            </div>
        </AuthProvider>

    );
}

export default App;
