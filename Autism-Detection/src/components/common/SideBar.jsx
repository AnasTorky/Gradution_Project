import { useState } from "react";
import { NavLink } from "react-router-dom";

function Sidebar() {
    const [selectedItem, setSelectedItem] = useState(0);

    function handleActivityPageclick(index) {
        setSelectedItem(index);
    }
    const menuItems = [
        { id: 0, label: "Cognitive Games", to: "/Activities" },
        { id: 1, label: "Educational Videos", to: "/Videos" },
        { id: 2, label: "Communication", to: "/Communication" },
    ];

    return (
        <div className="w-[20%] h-svh">
            <ul className="bg-[var(--secondary)] text-[var(--fifth)] h-full">
                <li className="h-[65px] text-lg leading-[60px] pl-6 pt-[10px] pb-[130px]"></li>
                {menuItems.map((item, index) => (
                    <NavLink
                        to={item.to}
                        onClick={() => handleActivityPageclick(index)}
                    >
                        <li
                            key={item.id}
                            className={`group pl-9 leading-[4.75] transition-all  text-[var(--fifth)] duration-300  ${
                                selectedItem === index
                                    ? `bg-[var(--primary)] text-[#000] `
                                    : `hover:bg-[#019d47]`
                            } cursor-pointer`}
                        >
                            <a
                                className={`font-bold ${
                                    selectedItem === index
                                        ? "font-extrabold text-[#000] pl-11 transition-all  duration-300"
                                        : ""
                                }`}
                            >
                                {item.label}
                            </a>
                        </li>
                    </NavLink>
                ))}
            </ul>
        </div>
    );
}

export default Sidebar;
