import Footer from "../layouts/Footer";
import AboutAndContact from "../components/sections/AboutAndContact";
import MissionAndVision from "../components/sections/MissionAndVision";
import WhatWeOffer from "../components/sections/WhatWeOffer";
import AboutUsImgs from "../components/sections/AboutUsImgs";
const AboutAndContactContent = {
    title: "About Us",
    desc: "Empowering Families, Supporting Children with Autism",
};
function AboutUs() {
    return (
        <div className="pt-24 relative bg-[var(--primary)] font-nunito min-h-screen">
            <section className="h-[600px]">
                <AboutAndContact
                    AboutAndContactContent={AboutAndContactContent}
                />
            </section>
            <section className="absolute z-50 top-[628px] left-1/2 transform -translate-x-1/2 w-[87%] flex justify-center">
                <AboutUsImgs />
            </section>
            <MissionAndVision />
            <WhatWeOffer />
            <section className="pt-20">
                <Footer />
            </section>
        </div>
    );
}

export default AboutUs;
