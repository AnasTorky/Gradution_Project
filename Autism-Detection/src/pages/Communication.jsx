import { useState, useContext } from "react";
import Sidebar from "../components/common/SideBar";
import { AuthContext } from "../contexts/AuthContext";

function Communication() {
    const [isListening, setIsListening] = useState(false);
    const [showVisualSupport, setShowVisualSupport] = useState(true);
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
                    "Authorization": `Bearer 
                    
                    
                    
                    `,
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
        utterance.rate = 0.9;
        window.speechSynthesis.speak(utterance);
    };

    return (
        <div className="flex pt-24 relative font-sans min-h-screen bg-[FFFFFF]">
            <Sidebar selectedItem={null} setSelectedItem={() => {}} />

            {/* Floating Visual Support Panel */}
            {showVisualSupport && (
                <div className="fixed top-28 right-4 bg-white border border-green-200 rounded-xl shadow-xl p-4 w-72 z-50">
                    <h3 className="font-bold text-green-800 text-xl mb-2">🧩 Visual Steps</h3>
                    <ul className="space-y-3 text-[#5D4037] text-sm">
                        <li className="flex items-center gap-2">🎤 <span>Press the mic</span></li>
                        <li className="flex items-center gap-2">🗣️ <span>Speak clearly</span></li>
                        <li className="flex items-center gap-2">⏳ <span>Wait for a reply</span></li>
                        <li className="flex items-center gap-2">🎧 <span>Listen to the AI</span></li>
                    </ul>
                </div>
            )}

            <div className="flex flex-col w-full px-6 sm:px-10 md:w-[80%] items-center justify-center text-center">
            <h2 className="text-4xl sm:text-5xl font-extrabold mb-6 text-[#2E7D32] drop-shadow-md flex items-center gap-3">
  <img
    src="https://img.icons8.com/emoji/48/waving-hand-emoji.png"
    alt="Waving Hand"
    className="w-12 h-12"
  />
  Talk to Your AI Friend
</h2>


                {/* Doctor Avatar with White Background */}
                <div className=" p-2 rounded-full shadow-lg mb-4">
                    <img
                        src="https://img.icons8.com/color/96/000000/doctor-male.png"
                        alt="Doctor Avatar"
                        className="w-32 h-32 sm:w-40 sm:h-40 rounded-full"
                    />
                </div>

                <p className="text-xl sm:text-2xl text-[000000] mb-6 max-w-xl leading-relaxed">
                    Press the button and speak. I’m here to listen and talk with you.
                </p>

                <button
                    onClick={handleSpeak}
                    className={`${
                        isListening ? "bg-[#AED581]" : "bg-[#C5E1A5] hover:bg-[#AED581]"
                    } text-[#33691E] text-xl sm:text-2xl px-10 py-4 rounded-full shadow-lg transition-all duration-300 ease-in-out`}
                >
                    🎤 {isListening ? "I'm Listening..." : "Press to Talk"}
                </button>

                {/* Moved to Bottom */}
                <div className="mt-8">
                    <label className="flex items-center gap-3 text-lg text-[#2E7D32]">
                        <input
                            type="checkbox"
                            checked={showVisualSupport}
                            onChange={() => setShowVisualSupport(!showVisualSupport)}
                            className="w-5 h-5 accent-[#8BC34A]"
                        />
                        Visual Support Mode
                    </label>
                </div>
            </div>
        </div>
    );
}

export default Communication;
