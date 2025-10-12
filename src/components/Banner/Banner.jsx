import Iphone15Pro from "../../assets/banner/15pro.png";

const BannerData = {
    discount: "Giảm 50%",
    title: "Super Sale",
    date: "Từ 20/10 đến 20/11",
    iamge: Iphone15Pro,
    title2: "Iphone 15 Pro",
    title3: "Winter Sale",
    title4: "Món quà tuyệt vời để tặng người thương",
    bgColor: "#f42c37",
};
const Banner = () => {
    
  return (
    <div id="sale-product" className="min-h-[550px] flex justify-center items-center py-12">
        <div className="container">
            <div 
                style={{ backgroundColor: BannerData.bgColor }}
                className="grid grid-cols-1 md:grid-cols-3 gap-6 items-center text-white rounded-3xl">
                {/* first col */}
                <div className="p-6 sm:p-8">
                    <p  data-aos="slide-right" className="text-sm">{BannerData.discount}</p>
                    <h1 data-aos="zoom-out" className="uppercase text-4xl lg:text-6xl
                                    font-bold">{BannerData.title}</h1>
                    <p data-aos="fade-up" className="text-sm">{BannerData.date}</p>
                </div>
                {/* second col */}
                <div data-aos="zoom-in" className="h-full flex items-center">
                    <img src={BannerData.iamge} alt="banner" className="scale-125 w-[200px]
                            md:w-[280px] mx-auto drop-shadow-2xl object-cover">
                    </img>
                </div>
                {/* third col */}
                <div className="flex flex-col justify-center gap-4 p-6 
                                sm:p-8">
                    <p data-aos="zoom-out" className="font-bold text-xl">{BannerData.title2}</p>
                    <p data-aos="fade-up" className="text-3xl sm:text-5xl font-bold">{BannerData.title3}</p>
                    <p data-aos="fade-up" className="text-sm tracking-wide leading-5">{BannerData.title4}</p>
                    <div data-aos="fade-up" data-aos-offset="0">
                        <button style={{color:BannerData.bgColor}}
                        className="bg-white rounded-full px-4 py-2">Mua Ngay</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
  )
}

export default Banner