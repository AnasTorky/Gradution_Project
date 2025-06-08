// src/layouts/Header.jsx
import { Link, useLocation, useNavigate } from "react-router-dom";
import { useState, useEffect, useContext, useRef } from "react";
import HeaderLink from "../components/common/HeaderLink";
import { MdAccountCircle } from "react-icons/md";
import { AuthContext } from "../contexts/AuthContext";
import { CiLogout } from "react-icons/ci";

function Header({ onShowSignIn }) {
    const { isAuthenticated, logout } = useContext(AuthContext);
    const [isScrolled, setIsScrolled] = useState(false);
    const [isDropdownOpen, setIsDropdownOpen] = useState(false);
    const dropdownRef = useRef(null);
    const location = useLocation();
    const navigate = useNavigate();

    // Reset scroll when location changes
    useEffect(() => {
        window.scrollTo(0, 0);
        setIsScrolled(false);
        setIsDropdownOpen(false); // Close dropdown on navigation
    }, [location]);

    // Handle scroll for header styling
    useEffect(() => {
        const handleScroll = () => {
            setIsScrolled(window.scrollY > 10);
        };

        window.addEventListener("scroll", handleScroll);
        return () => window.removeEventListener("scroll", handleScroll);
    }, []);

    // Close dropdown when clicking outside
    useEffect(() => {
        const handleClickOutside = (event) => {
            if (
                dropdownRef.current &&
                !dropdownRef.current.contains(event.target)
            ) {
                setIsDropdownOpen(false);
            }
        };

        document.addEventListener("mousedown", handleClickOutside);
        return () =>
            document.removeEventListener("mousedown", handleClickOutside);
    }, []);

    const handleLogout = () => {
        logout();
        setIsDropdownOpen(false);
        navigate("/"); // Redirect to home after logout
    };

    const toggleDropdown = () => {
        setIsDropdownOpen((prev) => !prev);
    };

    return (
        <nav
            className={`font-nunito fixed top-0 left-0 right-0 z-[60] transition-all duration-300 ${
                isScrolled
                    ? "bg-[var(--fifth)] shadow-md py-2"
                    : "bg-[var(--primary)] py-3"
            }`}
        >
            <div className="w-[90%] container mx-auto flex justify-between items-center py-5 px-6">
                <a href="#">
                    <h1 className="text-[32px] font-extrabold text-[var(--secondary)]">
                        Auti
                        <em className="not-italic text-[var(--quartery)]">
                            Mate
                        </em>
                    </h1>
                </a>
                <ul className="hidden w-[505px] md:flex justify-around space-x-6 font-semibold">
                    <HeaderLink to="/" pageName="Home" />
                    <HeaderLink to="/Activities" pageName="Activities" />
                    <HeaderLink to="/AboutUs" pageName="About us" />
                    <HeaderLink to="/ContactUs" pageName="Contact us" />
                </ul>
                {isAuthenticated ? (
                    <div className="relative" ref={dropdownRef}>
                        <MdAccountCircle
                            className="w-12 h-12 text-[var(--secondary)] cursor-pointer"
                            onClick={toggleDropdown}
                            title="Profile Menu"
                        />
                        {isDropdownOpen && (
                            <div className="absolute right-0 mt-2 w-48 bg-[var(--fifth)] border border-[var(--secondary)] rounded-lg shadow-lg z-50">
                                <Link
                                    to="/Profile"
                                    className=" flex items-center block px-4 py-2 text-[var(--secondary)] font-semibold hover:bg-[var(--secondary)] hover:text-[var(--primary)] transition-all duration-200"
                                    onClick={() => setIsDropdownOpen(false)}
                                >
                                    <MdAccountCircle className="mr-2" />
                                    Profile
                                </Link>
                                <button
                                    onClick={handleLogout}
                                    className="flex items-center w-full text-left px-4 py-2 text-[var(--secondary)] font-semibold hover:bg-[var(--secondary)] hover:text-[var(--primary)] transition-all duration-200"
                                >
                                    <CiLogout className="mr-2" />
                                    Logout
                                </button>
                            </div>
                        )}
                    </div>
                ) : (
                    <button
                        onClick={onShowSignIn}
                        className="font-semibold p-2 text-[var(--teriary)] border-2 border-[var(--secondary)] hover:bg-[var(--secondary)] hover:text-[var(--primary)] transition-all duration-200"
                    >
                        Sign in
                    </button>
                )}
            </div>
        </nav>
    );
}

export default Header;
