import Button from "../../components/shared/Button.jsx";

const BestSellerCard = ({data}) => {
  return (
    <div className="mb-10">
      <div className="grid grid-cols-1 sm:grid-cols-2 
      md:grid-cols-5 gap-5 place-items-center">
        {/* card section */}
        {data.map((item) => (
            <div data-aos="fade-up"
            data-aos-delay={item.aosDelay}
            className="group relative" key={item.id}>
                <div className="bg-gray-200 p-2 rounded-md">
                    <img src={item.image} alt={item.title} 
                    className=" px-12 h-[200px] w-full object-cover" />
                </div>
                {/* Hover Button */}
                <div className="hidden group-hover:flex absolute 
                top-1/2 -translate-y-1/2 left-1/2 -translate-x-1/2
                h-full w-full text-center
                group-hover:backdrop-blur-sm justify-center items-center
                duration-200 group-hover:rounded-md">
                    <Button 
                        text="Thêm vào giỏ hàng"
                        bgColor="bg-primary"
                        textColor="text-white"
                    />
                </div>
                <div className="mt-2 leading-7">
                    <h2 className="font-semibold">{item.title}</h2>
                    <p className="font-bold">{item.price}</p>
                </div>
            </div>
        ))}
      </div>
    </div>
  )
}

export default BestSellerCard
