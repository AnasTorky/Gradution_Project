import { useState, useContext } from "react";
import Sidebar from "../components/common/SideBar";
import { AuthContext } from "../contexts/AuthContext";

function Communication() {
    const [isListening, setIsListening] = useState(false);
    const { isAuthenticated } = useContext(AuthContext);

    const handleSpeak = () => {
        if (!isAuthenticated) {
            alert("Please sign in to talk with your AI friend.");
            return;
        }

        const recognition = new window.webkitSpeechRecognition();
        recognition.lang = "en-US";
        recognition.start();
        setIsListening(true);
        speak("I'm listening! Go ahead and talk to me.");

        recognition.onend = () => {
            setIsListening(false);
        };

        recognition.onresult = async (event) => {
            const userText = event.results[0][0].transcript;

            const res = await fetch("https://api.openai.com/v1/chat/completions", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Authorization": `Bearer put key`,
                },
                body: JSON.stringify({
                    model: "gpt-3.5-turbo",
                    messages: [
                        {
                            role: "system",
                            content:
                                "You are a cheerful, friendly assistant talking to a child. Always respond with clear, positive, and fun speech.",
                        },
                        {
                            role: "user",
                            content: userText,
                        },
                    ],
                }),
            });

            const data = await res.json();
            const reply =
                data.choices?.[0]?.message?.content ||
                "Hmm, I didn't catch that. Want to try again?";
            speak(reply);
        };
    };

    const speak = (text) => {
        const utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = "en-US";
        utterance.rate = 1; // Slightly slower for kids
        window.speechSynthesis.speak(utterance);
    };

    return (
        <div className="flex pt-24 relative font-nunito min-h-screen bg-gradient-to-br from-blue-100 via-pink-100 to-yellow-100">
            <Sidebar selectedItem={null} setSelectedItem={() => {}} />

            <div className="flex flex-col w-[80%] p-8 items-center justify-center text-center">
                <h2 className="text-4xl font-extrabold mb-4 text-pink-700 drop-shadow">
                    🧠 Talk to Your AI Friend!
                </h2>
                <p className="text-lg text-blue-800 mb-6">
                    Press the button and speak — your voice is all you need!
                </p>

                <button
                    onClick={handleSpeak}
                    className={`${
                        isListening ? "animate-pulse" : ""
                    } bg-gradient-to-r from-purple-500 to-pink-500 hover:from-pink-600 hover:to-purple-600 transition-all duration-300 text-white text-xl px-10 py-4 rounded-full shadow-xl`}
                >
                    🎤 {isListening ? "Listening..." : "Start Talking"}
                </button>
            </div>
        </div>
    );
}

export default Communication;
