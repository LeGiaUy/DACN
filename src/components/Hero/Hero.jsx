
import Slider from "react-slick";
import GalaxyZFold6 from "../../assets/hero/galaxyzfold6.png";
import Iphone16ProMax from "../../assets/hero/iphone16promax.png";
import OppoFindN3Flip from "../../assets/hero/oppofindn3flip.png";
import Button from '../shared/Button';
const HeroData = [
    {
        id: 1,
        img: Iphone16ProMax,
        subtitle: "Iphone",
        title: "New",
        title2: "16 Pro Max",
        description: "The latest iPhone model with advanced features and a sleek design.",
    },
    {
        id: 2,
        img: GalaxyZFold6,
        subtitle: "Samsung",
        title: "New",
        title2: "Galaxy Z Fold6",
        description: "The latest Samsung smartphone with a foldable screen and advanced features.",
    },
    {
        id: 3,
        img: OppoFindN3Flip,
        subtitle: "Oppo",
        title: "New",
        title2: "Find N3 Flip",
        description: "The latest Oppo smartphone with a flip screen and advanced features.",
    }
]

const Hero = () => {

    const settings = {
        dots: true, //Hiển thị dấu chấm
        infinite: true, //Lặp lại vô hạn
        speed: 500, //Tốc độ chuyển động
        slidesToShow: 1, //Số lượng slide hiển thị
        slidesToScroll: 1, //Số lượng slide trượt
        autoplay: true, //Tự động chuyển slide
        autoplaySpeed: 3000, //Tốc độ tự động chuyển slide
        cssEase: "ease-in-out", //Hiệu ứng chuyển động  
        pauseOnHover: "false", //Dừng chuyển động khi hover
        pauseOnFocus: "true", //Dừng chuyển động khi focus
      };
      
  return (
    <div className="mt-20 container">
        <div className="overflow-hidden rounded-3xl min-h-[550px]
        sm:min-h-[650px] hero-bg-color flex justify-center items-center">
            {/* Hero Section */}
            <div className="container pb-8 sm:pb-0">
                <Slider {...settings}> {/* thẻ <slider> dùng để tạo một thanh trượt tương tác.
            Nó cho phép người dùng chọn một giá trị trong một phạm vi xác định. */}
                    {
                        HeroData.map((data) => (
                            <div key={data.id}>
                                <div className="grid grid-col-1 sm:grid-cols-2">
                                    {/* text content section */}
                                    <div className="flex flex-col 
                                    justify-center gap-4 sm:pl-3 pt-12 
                                    sm:pt-0 text-center sm:text-left 
                                    order-2 sm:order-1 relative z-10"> 
                                        <h1 data-aos="zoom-out"
                                        data-aos-duration="500"
                                        data-aos-once="true"
                                        className="text-2xl sm:text-6xl 
                                        lg:text-2xl font-bold">{data.subtitle}</h1>
                                        <h1 data-aos="zoom-out"
                                        data-aos-duration="500"
                                        data-aos-once="true" 
                                        className="text-5xl sm:text-6xl 
                                        lg:text-7xl font-bold text-primary">{data.title}</h1>
                                        <h1 data-aos="zoom-out"
                                        data-aos-duration="500"
                                        data-aos-once="true"
                                        className="text-5xl uppercase text-white
                                        dark:text-white/5 sm:text-[80px] 
                                        md:text-100px] xl:text-[150px] font-bold">{data.title2}</h1>
                                        <div data-aos="fade-up"
                                        data-aos-offset="0"
                                        data-aos-delay="300"
                                        data-aos-duration="500">
                                            <Button text="Mua ngay" bgColor="bg-primary" textColor="text-white" />
                                        </div>

                                    </div>
                                    {/* img section */}
                                    <div className="order-1 sm:order-2">
                                        <div data-aos="zoom-in"
                                        data-aos-once="true"
                                        className="relative z-10">
                                            <img src={data.img} alt=""
                                            className="w-[280px] h-[280px] 
                                            sm:w-[360px] sm:h-[360px] 
                                            md:w-[440px] md:h-[440px] 
                                            object-contain mx-auto 
                                            drop-shadow-[-8px_4px_6px_rgba(0,0,0,0.4)] relative z-40"></img>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        ))
                    }
                </Slider>
            </div>          
        </div>
    </div>
  )
}

export default Hero
