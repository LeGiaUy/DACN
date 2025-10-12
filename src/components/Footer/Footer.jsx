import { FaFacebook, FaInstagram, FaLocationArrow, FaMobileAlt, FaTwitter } from "react-icons/fa";

const Footer = () => {
    const FooterLinks = [
        {
            name: "Trang chủ",
            link: "#",
        },
        {
            name: "Sản phẩm",
            link: "#",
        },
        {
            name: "Giới thiệu",
            link: "#",
        },
        {
            name: "Liên hệ",
            link: "#",
        },  
    ]
  return (
    <div id="contact" className="dark:bg-gray-950 bg-gray-200">
        <div className="container">
            <div className="grid md:grid-cols-5 pb-20 pt-5">
                {/* web detail */}
                <div className="col-span-2 py-8 px-4">
                    <a href="#"
                        className="text-primary font-semibold
                                    tracking-widest text-2xl 
                                    uppercase sm:text-3xl">
                        SPShop
                    </a>
                    <p className="text-gray-600 dark:text-white/70 pr-24 pt-3">
                        SPShop - Nơi mua sắm trực tuyến đáng tin cậy với đa dạng sản phẩm chất lượng cao và dịch vụ khách hàng xuất sắc.
                    </p>
                    <p className="text-gray-500 mt-4">
                        Made by Lê Gia Uy
                    </p>
                    <a href="https://www.facebook.com/legiauy.73/" target="blank"
                    className="inline-block bg-primary/90 text-white py-2 px-4
                    text-sm rounded-full hover:bg-primary duration-200">
                        Liên hệ tôi
                    </a>
                </div>
                {/* Footer links */}
                <div className="py-8 px-4">
                    <h1 className="text-xl font-bold sm:text-left mb-3">Liên kết</h1>
                    <ul className="space-y-3">
                        {FooterLinks.map(
                            (data, index) => (
                                <li key={index}>
                                    <a href={data.link} className="text-gray-600 hover:text-black dark:text-gray-400 hover:dark:text-white duration-300">
                                        {data.name}
                                    </a>
                                </li>
                            )
                        )}
                    </ul>
                </div>
                {/* Second col links */}
                <div className="py-8 px-4">
                    <h1 className="text-xl font-bold sm:text-left mb-3">Liên kết</h1>
                    <ul className="space-y-3">
                        {FooterLinks.map(
                            (data, index) => (
                                <li key={index}>
                                    <a href={data.link} className="text-gray-600 hover:text-black dark:text-gray-400 hover:dark:text-white duration-300">
                                        {data.name}
                                    </a>
                                </li>
                            )
                        )}
                    </ul>
                </div>
                {/* Address */}
                <div className="py-8 px-4 col-span-2 sm:col-auto">
                    <h1 className="text-xl font-bold sm:text-left mb-3">Địa chỉ</h1>
                    <div>
                        <div className="flex items-center gap-3">
                            <FaLocationArrow></FaLocationArrow>
                            <p>Phường A, Quận B, Hà Nội</p>
                        </div>
                        <div className="flex items-center gap-3">
                            <FaMobileAlt></FaMobileAlt>
                            <p>0961750846</p>
                        </div>
                        {/* Social links */}
                        <div className="flex items-center gap-3 mt-6">
                            <a href="#">
                                <FaInstagram className="text-3xl 
                                hover:text-primary duration-200"></FaInstagram>
                            </a>
                            <a href="#">
                                <FaFacebook className="text-3xl 
                                hover:text-primary duration-200"></FaFacebook>
                            </a>
                            <a href="#">
                                <FaTwitter className="text-3xl 
                                hover:text-primary duration-200"></FaTwitter>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
  )
}

export default Footer
