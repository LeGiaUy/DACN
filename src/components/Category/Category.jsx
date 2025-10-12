
import Samsung from "../../assets/hero/galaxyzfold6.png";
import Iphone from "../../assets/hero/iphone16promax.png";
import Oppo from "../../assets/hero/oppofindn3flip.png";
import Button from "../Shared/Button.jsx";

const Category = () => {
  const categories = [
    { gradient: "from-black/90 to-white/70", brand: "iPhone", image: Iphone },
    { gradient: "from-black/90 to-brandyellow/70", brand: "Samsung", image: Samsung },
    { gradient: "from-black/90 to-brandblue/70", brand: "Oppo", image: Oppo },
  ];

  return (
    <div className="py-8">
      <div className="container">
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
          {categories.map((category, index) => (
            <div key={index} className={`py-10 pl-5 bg-gradient-to-br ${category.gradient} text-white rounded-3xl relative h-[320px] flex items-end`}>
              <div>
                <div className="mb-4">
                  <p className="mb-[2px] text-gray-400">Tìm kiếm</p>
                  <p className="text-2xl font-semibold">Với</p>
                  <p className="text-4xl xl:text-5xl font-bold opacity-50 mb-2">{category.brand}</p>
                  <Button text="Khám phá" 
                          textColor="text-white" 
                          bgColor="bg-primary" />
                </div>
              </div>
              <img src={category.image} alt={category.brand}
                  className="h-[200px]
                            sm:block sm:h-[240px] w-auto absolute 
                            bottom-2 right-6" />
            </div>
          ))}
        </div>
      </div>
    </div>
  )
}

export default Category
