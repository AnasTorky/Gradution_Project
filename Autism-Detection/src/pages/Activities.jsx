import { useContext, useState } from "react";
import Sidebar from "../components/common/SideBar";
import GamesContainer from "../components/common/GamesContainer";
import GameModal from "../components/common/GameModal";
import Game from "../components/common/Game";
import Button from "../components/common/Button";
import { AuthContext } from "../contexts/AuthContext";

function Activities({ items, containerTitle, containerDesc, onShowSignIn }) {
    const { isAuthenticated } = useContext(AuthContext);
    const [selectedItem, setSelectedItem] = useState(null);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const itemsExists = items && items.length > 0;
    function handleItemClick(itemId) {
        if (!items) return;
        if (isAuthenticated) {
            const item = items.find((i) => i.id === itemId);
            setSelectedItem(item);
            setIsModalOpen(true);
        } else onShowSignIn();
    }

    function handleModalClose() {
        setIsModalOpen(false);
    }

    return (
        <div className="flex pt-24 relative font-nunito min-h-screen">
            <Sidebar
                selectedItem={selectedItem}
                setSelectedItem={setSelectedItem}
            />
            {isModalOpen ? (
                <GameModal
                    onCloseGame={handleModalClose}
                    selectedGame={selectedItem}
                />
            ) : itemsExists ? (
                <GamesContainer
                    title={containerTitle}
                    description={containerDesc}
                >
                    {items.map((item) => (
                        <Game
                            img={item.imgUrl}
                            desc={item.desc}
                            title={item.title}
                            key={item.id}
                        >
                            <Button
                                padding="px-8"
                                onClick={() => handleItemClick(item.id)}
                            >
                                Play
                            </Button>
                        </Game>
                    ))}
                </GamesContainer>
            ) : (
                <div className="w-[80%]"></div>
            )}
        </div>
    );
}

export default Activities;
