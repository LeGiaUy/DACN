
import AOS from "aos";
import "aos/dist/aos.css";
import { useEffect } from "react";
import Banner from "./components/Banner/Banner.jsx";
import BestSeller from "./components/BestSeller/BestSeller.jsx";
import Category from "./components/Category/Category.jsx";
import Footer from "./components/Footer/Footer.jsx";
import Hero from "./components/Hero/Hero.jsx";
import Navbar from "./components/Navbar/Navbar.jsx";

const App = () => {
  useEffect(() => {
    AOS.init({
      duration: 800,
      easing: "ease-in-sine",
      delay: 100,
      offset: 100,
    });
    AOS.refresh();
  }, []);
  return (
    <div className="bg-white dark:bg-gray-900 dark:text-white duration-200 overflow-hidden">
      <Navbar></Navbar>
      <Hero></Hero>
      <Category></Category>
      <Banner></Banner>
      <BestSeller></BestSeller>
      <Footer></Footer>
    </div>
  );
}

export default App

