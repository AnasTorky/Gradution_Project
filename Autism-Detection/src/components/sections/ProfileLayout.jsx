import Img from "../common/Img";
import ProfileImg from "../../assets/Images/Profile/Profile.png";
import { FaCamera } from "react-icons/fa6";
import { GoPencil } from "react-icons/go";
const img = {
  imgUrl: ProfileImg,
  title: "ProfileImg",
};
function ProfileLayout({ user }) {
  if(!user){
    return <div  className="text-xl font-bold mb-2">Loading...</div>;
  }
  return (
    <div className="flex gap-3 bg-[var(--fifth)] w-full items-center h-[603px]">
      <div className="relative h-full">
        <button className="absolute top-0 left-0 z-50 bg-[var(--fifth)] py-2 px-3">
          <FaCamera className="w-[44px] h-[38px]" />
        </button>
        <Img imgs={img} imgHeight="h-full" />
      </div>
      <div className="relative w-[63%] h-full p-6 text-black">
  <h2 className="text-3xl font-extrabold mb-4 text-gray-800">{user.name}</h2>
  <p className="text-lg text-gray-600 mb-6">Email: {user.email}</p>

  {user.children?.length > 0 ? (
    user.children.map((child, index) => (
      <div
        key={child.id || index}
        className="bg-white shadow-lg rounded-2xl p-5 mb-6 border border-gray-200 transition-transform hover:scale-[1.02]"
      >
        <h3 className="text-xl font-bold text-green-600 mb-2">
          Your Child 
        </h3>
        <div className="space-y-1 text-gray-700">
          <p><span className="font-medium text-gray-900">Name:</span> {child.name}</p>
          <p><span className="font-medium text-gray-900">Age:</span> {child.age}</p>
          <p><span className="font-medium text-gray-900">Skill:</span> {child.skill || "N/A"}</p>
          <p><span className="font-medium text-gray-900">Preferred Activity:</span> {child.preferred_activities || "N/A"}</p>

        </div>
      </div>
    ))
  ) : (
    <p className="text-gray-500 italic mt-4">No children added yet.</p>
  )}


        <button className="absolute top-[523px] right-[36px] flex items-center gap-[10px] font-semibold text-[var(--secondary)] py-[10px] px-3 border-2 border-[var(--secondary)] hover:bg-[var(--secondary)] hover:text-[var(--primary)] transition-all duration-200">
          <GoPencil className="w-[18.8px] h-[18.8px]" />
          <span>Edit Profile</span>
        </button>
      </div>
    </div>
  );
}


export default ProfileLayout;