import Heading from "../Shared/Heading.jsx";
import BestSellerCard from "./BestSellerCard.jsx";

// image import
import findx5pro from "../../assets/BestSeller/findx5pro.png";
import galaxys23ultra from "../../assets/BestSeller/galaxys23ultra.png";
import galaxyzfold4 from "../../assets/BestSeller/galaxyzfold4.png";
import iphone14 from "../../assets/BestSeller/iphone14.png";
import iphone15pro from "../../assets/BestSeller/iphone15pro.png";

const BestSellerData = [
  {
    id: 1,
    title: "iPhone 15 Pro",
    price: "23.990.000 VND",
    image: iphone15pro,
    aosDelay: "0",
  },
  {
    id: 2,
    title: "Samsung Galaxy S23 Ultra",
    price: "28.790.000 VND",
    image: galaxys23ultra,
    aosDelay: "200",
  },
  {
    id: 3,
    title: "OPPO Find X5 Pro",
    price: "21.590.000 VND",
    image: findx5pro,
    aosDelay: "400",
  },
  {
    id: 4,
    title: "iPhone 14",
    price: "19.190.000 VND",
    image: iphone14,
    aosDelay: "600",
  },
  {
    id: 5,
    title: "Samsung Galaxy Z Fold4",
    price: "37.990.000 VND",
    image: galaxyzfold4,
    aosDelay: "800",
  },
  {
    id: 6,
    title: "iPhone 15 Pro",
    price: "23.990.000 VND",
    image: iphone15pro,
    aosDelay: "0",
  },
  {
    id: 7,
    title: "Samsung Galaxy S23 Ultra",
    price: "28.790.000 VND",
    image: galaxys23ultra,
    aosDelay: "200",
  },
  {
    id: 8,
    title: "OPPO Find X5 Pro",
    price: "21.590.000 VND",
    image: findx5pro,
    aosDelay: "400",
  },
  {
    id: 9,
    title: "iPhone 14",
    price: "19.190.000 VND",
    image: iphone14,
    aosDelay: "600",
  },
  {
    id: 10,
    title: "Samsung Galaxy Z Fold4",
    price: "37.990.000 VND",
    image: galaxyzfold4,
    aosDelay: "800",
  },
]

const BestSeller = () => {
  return (
    <div id="best-sellers">
      <div className="container">
        {/* Header Section */}
        <Heading title="Best Sellers" subtitle={"Top 5 Best Seller Products"} />
        {/* Body Section */}
        <BestSellerCard data={BestSellerData} />
      </div>
    </div>
  )
}

export default BestSeller
