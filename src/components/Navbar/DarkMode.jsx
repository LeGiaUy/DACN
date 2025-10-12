import React from 'react';
import DarkButton from "../../assets/DarkMode/DarkButton.png";
import LightButton from "../../assets/DarkMode/LightButton.png";

const DarkMode = () => {

  const [theme, setTheme] = React.useState(localStorage.getItem("theme") ? localStorage.getItem("theme") : "light");
  
  const element = document.documentElement ;// access to html element
  console.log (element);

  //adding theme to localStorage and html Element
  React.useEffect(() => {
    localStorage.setItem("theme", theme);
    if (theme === "dark") {
        element.classList.add("dark");
        element.classList.add("dark");
    }
    else {
        element.classList.remove("light");
        element.classList.remove("dark");
    }
    });
  
  return (
    <div className="relative w-12 h-12">
        <img 
            onClick={() => setTheme(theme === "dark" ? "light" : "dark")}
            src={LightButton}
            alt="Light mode" 
            className={`w-full h-full cursor-pointer absolute top-0 left-0 transition-opacity duration-300 ${theme === "dark" ? "opacity-0" : "opacity-100"}`}>
        </img>
        <img 
            onClick={() => setTheme(theme === "dark" ? "light" : "dark")}
            src={DarkButton}
            alt="Dark mode" 
            className={`w-full h-full cursor-pointer absolute top-0 left-0 transition-opacity duration-300 ${theme === "dark" ? "opacity-100" : "opacity-0"}`}>
        </img>
    </div>
  )
}

export default DarkMode
