import { useEffect, useState } from "react";
import AboutAndContact from "../components/sections/AboutAndContact";
import ProfileLayout from "../components/sections/ProfileLayout";

const AboutAndContactContent = {
  title: "Profile",
  desc: "",
};

function Profile() {
  const [user, setUser] = useState(null); 
  useEffect(() => {
    const fetchProfile = async () => {
      try {
        const token = localStorage.getItem("token");
        if (!token) return;

        const res = await fetch("http://localhost:8000/api/profile", {
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${token}`,
            "X-Requested-With": "XMLHttpRequest",
          },
          credentials: "include",
        });

        const data = await res.json();
        console.log("Fetched user:", data);

        setUser(data.user); 
      } catch (error) {
        console.error("Failed to fetch profile:", error);
      }
    };

    fetchProfile();
  }, []);

  return (
    <div className="pt-24 relative bg-[var(--primary)] font-nunito min-h-screen">
      <section className="h-[317px]">
        <AboutAndContact AboutAndContactContent={AboutAndContactContent} />
      </section>

      <section className="absolute z-50 top-[355px] w-[86%] left-1/2 transform -translate-x-1/2 flex justify-center shadow-xl">
        <ProfileLayout user={user} />
      </section>

      <section className="h-[941px]"></section>
    </div>
  );
}

export default Profile;



