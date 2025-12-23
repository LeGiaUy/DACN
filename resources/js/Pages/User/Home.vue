<template>
    <UserLayout>
        <!-- Banner Carousel Section -->
        <section class="relative">
            <div class="overflow-hidden">
                <div class="flex transition-transform duration-500 ease-in-out" :style="{ transform: `translateX(-${currentBanner * 100}%)` }">
                    <div v-for="(banner, index) in banners" :key="index" 
                         class="min-w-full h-96 md:h-[500px] relative bg-gray-100 flex items-center justify-center">
                        <a v-if="banner.link_url" :href="banner.link_url" class="block w-full h-full flex items-center justify-center">
                            <img :src="banner.image" :alt="banner.alt" 
                                 class="w-full h-full object-contain">
                        </a>
                        <img v-else :src="banner.image" :alt="banner.alt" 
                             class="w-full h-full object-contain">
                    </div>
                </div>
            </div>
            <!-- Banner Navigation -->
            <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
                <button v-for="(banner, index) in banners" :key="index"
                        @click="currentBanner = index"
                        :class="[
                            'w-3 h-3 rounded-full transition-all',
                            currentBanner === index ? 'bg-white w-8' : 'bg-white/50'
                        ]">
                </button>
            </div>
            <!-- Previous/Next buttons -->
            <button @click="prevBanner" 
                    class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-white/80 hover:bg-white p-2 rounded-full">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
            <button @click="nextBanner" 
                    class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-white/80 hover:bg-white p-2 rounded-full">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        </section>

        <!-- Service Highlights -->
        <section class="bg-white py-6 border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-3">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-1">Giao hàng toàn quốc</h3>
                        <p class="text-sm text-gray-600">Freeship cho đơn hàng từ 299k</p>
                    </div>
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-3">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-1">30 ngày đổi trả</h3>
                        <p class="text-sm text-gray-600">Với bất kì lý do gì</p>
                    </div>
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mb-3">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-1">Chất lượng đảm bảo</h3>
                        <p class="text-sm text-gray-600">Sản phẩm chính hãng</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- HÀNG MỚI VỀ Section -->
        <section class="py-12 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-10">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">HÀNG MỚI VỀ</h2>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 md:gap-6">
                    <div v-for="product in newProducts" :key="product.id" 
                         class="bg-white rounded-lg overflow-hidden hover:shadow-lg transition-all duration-300 group flex flex-col">
                        <div class="relative w-full aspect-square overflow-hidden bg-gray-100 flex items-center justify-center">
                            <Link :href="route('user.products.show', product.id)" class="block w-full h-full flex items-center justify-center">
                                <img :src="product.img_url || '/images/placeholder.jpg'" 
                                     :alt="product.name"
                                     class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300">
                            </Link>
                            <!-- Discount Badge -->
                            <div v-if="product.discount_percent" 
                                 class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">
                                -{{ product.discount_percent }}%
                            </div>
                            <!-- Quick View Button -->
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-all duration-300 flex items-center justify-center opacity-0 group-hover:opacity-100">
                                <Link :href="route('user.products.show', product.id)"
                                      class="bg-white text-gray-900 px-4 py-2 rounded font-semibold hover:bg-gray-100">
                                    Chọn sản phẩm
                                </Link>
                            </div>
                        </div>
                        <div class="p-3 md:p-4 flex flex-col flex-1">
                            <Link :href="route('user.products.show', product.id)" class="flex-1 flex flex-col">
                                <h3 class="text-sm md:text-base font-semibold text-gray-900 mb-2 line-clamp-2 min-h-[2.5rem] hover:text-blue-600">
                                    {{ product.name }}
                                </h3>
                            </Link>
                            <div class="flex items-center gap-2 mt-auto">
                                <span v-if="product.original_price" class="text-sm text-gray-400 line-through">
                                    {{ formatPrice(product.original_price) }}
                                </span>
                                <span class="text-base md:text-lg font-bold text-blue-700">
                                    {{ formatPrice(product.price) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-10">
                    <Link :href="route('user.products.index')" 
                          class="inline-block bg-gray-900 text-white px-8 py-3 rounded-lg font-semibold hover:bg-gray-800 transition duration-300">
                        Xem tất cả sản phẩm
                    </Link>
                </div>
            </div>
        </section>

        <!-- Featured Products Section (if needed) -->
        <section v-if="featuredProducts.length > 0" class="py-12 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-10">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">Sản phẩm nổi bật</h2>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 md:gap-6">
                    <div v-for="product in featuredProducts" :key="product.id" 
                         class="bg-white rounded-lg overflow-hidden hover:shadow-lg transition-all duration-300 group flex flex-col">
                        <div class="relative w-full aspect-square overflow-hidden bg-gray-100 flex items-center justify-center">
                            <Link :href="route('user.products.show', product.id)" class="block w-full h-full flex items-center justify-center">
                                <img :src="product.img_url || '/images/placeholder.jpg'" 
                                     :alt="product.name"
                                     class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300">
                            </Link>
                            <div v-if="product.discount_percent" 
                                 class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">
                                -{{ product.discount_percent }}%
                            </div>
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-all duration-300 flex items-center justify-center opacity-0 group-hover:opacity-100">
                                <Link :href="route('user.products.show', product.id)"
                                      class="bg-white text-gray-900 px-4 py-2 rounded font-semibold hover:bg-gray-100">
                                    Chọn sản phẩm
                                </Link>
                            </div>
                        </div>
                        <div class="p-3 md:p-4 flex flex-col flex-1">
                            <Link :href="route('user.products.show', product.id)" class="flex-1 flex flex-col">
                                <h3 class="text-sm md:text-base font-semibold text-gray-900 mb-2 line-clamp-2 min-h-[2.5rem] hover:text-blue-600">
                                    {{ product.name }}
                                </h3>
                            </Link>
                            <div class="flex items-center gap-2 mt-auto">
                                <span v-if="product.original_price" class="text-sm text-gray-400 line-through">
                                    {{ formatPrice(product.original_price) }}
                                </span>
                                <span class="text-base md:text-lg font-bold text-blue-700">
                                    {{ formatPrice(product.price) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </UserLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import UserLayout from '@/Layouts/User/UserLayout.vue'

const props = defineProps({
    newProducts: Array,
    featuredProducts: Array,
    categories: Array,
    brands: Array,
    banners: {
        type: Array,
        default: () => []
    }
})

const currentBanner = ref(0)

// Chuyển đổi banners từ database sang format cho carousel
const banners = computed(() => {
    if (props.banners && props.banners.length > 0) {
        return props.banners.map(banner => ({
            image: banner.image_url,
            alt: banner.alt_text || banner.title || 'Banner',
            link_url: banner.link_url || null
        }))
    }
    // Fallback nếu không có banner
    return [
        {
            image: 'https://via.placeholder.com/1200x500/4F46E5/FFFFFF?text=Banner+1',
            alt: 'Banner 1',
            link_url: null
        }
    ]
})

let bannerInterval = null

const nextBanner = () => {
    if (banners.value.length > 0) {
        currentBanner.value = (currentBanner.value + 1) % banners.value.length
    }
}

const prevBanner = () => {
    if (banners.value.length > 0) {
        currentBanner.value = currentBanner.value === 0 ? banners.value.length - 1 : currentBanner.value - 1
    }
}

onMounted(() => {
    // Auto-play banner chỉ khi có banner
    if (banners.value.length > 1) {
        bannerInterval = setInterval(() => {
            nextBanner()
        }, 5000)
    }
})

onUnmounted(() => {
    if (bannerInterval) {
        clearInterval(bannerInterval)
    }
})

const formatPrice = (price) => {
    const numPrice = parseFloat(price) || 0
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(numPrice)
}
</script>
