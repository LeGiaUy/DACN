<template>
    <UserLayout>
        <!-- Hero Section -->
        <section class="bg-gradient-to-r from-blue-600 to-purple-600 text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
                <div class="text-center">
                    <h1 class="text-4xl md:text-6xl font-bold mb-6">
                        Chào mừng đến với DACN Store
                    </h1>
                    <p class="text-xl md:text-2xl mb-8 text-blue-100">
                        Khám phá những sản phẩm chất lượng cao với giá cả hợp lý
                    </p>
                    <Link :href="route('user.products.index')" 
                          class="inline-block bg-white text-blue-600 px-8 py-3 rounded-lg text-lg font-semibold hover:bg-gray-100 transition duration-300">
                        Mua sắm ngay
                    </Link>
                </div>
            </div>
        </section>

        <!-- Featured Products -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">Sản phẩm nổi bật</h2>
                    <p class="text-lg text-gray-600">Những sản phẩm được yêu thích nhất</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div v-for="product in featuredProducts" :key="product.id" 
                         class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                        <div class="aspect-w-16 aspect-h-9">
                            <img :src="product.img_url || '/images/placeholder.jpg'" 
                                 :alt="product.name"
                                 class="w-full h-48 object-cover">
                        </div>
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ product.name }}</h3>
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ product.description }}</p>
                            <div class="flex items-center justify-between">
                                <span class="text-2xl font-bold text-blue-600">{{ formatPrice(product.price) }}</span>
                                <Link :href="route('user.products.show', product.id)" 
                                      class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">
                                    Xem chi tiết
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-12">
                    <Link :href="route('user.products.index')" 
                          class="inline-block bg-blue-600 text-white px-8 py-3 rounded-lg text-lg font-semibold hover:bg-blue-700 transition duration-300">
                        Xem tất cả sản phẩm
                    </Link>
                </div>
            </div>
        </section>

        <!-- Categories -->
        <section class="py-16 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">Danh mục sản phẩm</h2>
                    <p class="text-lg text-gray-600">Khám phá theo danh mục yêu thích</p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
                    <Link v-for="category in categories" :key="category.id" 
                          :href="route('user.categories.show', category.id)"
                          class="bg-white rounded-lg p-6 text-center hover:shadow-lg transition duration-300">
                        <div class="w-16 h-16 bg-blue-100 rounded-full mx-auto mb-4 flex items-center justify-center">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-2">{{ category.name }}</h3>
                        <p class="text-sm text-gray-600">{{ category.products_count }} sản phẩm</p>
                    </Link>
                </div>
            </div>
        </section>

        <!-- Brands -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">Thương hiệu nổi tiếng</h2>
                    <p class="text-lg text-gray-600">Những thương hiệu uy tín và chất lượng</p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
                    <Link v-for="brand in brands" :key="brand.id" 
                          :href="route('user.brands.show', brand.id)"
                          class="bg-gray-50 rounded-lg p-6 text-center hover:bg-gray-100 transition duration-300">
                        <div class="w-16 h-16 bg-white rounded-full mx-auto mb-4 flex items-center justify-center">
                            <span class="text-lg font-bold text-gray-700">{{ brand.name.charAt(0) }}</span>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-2">{{ brand.name }}</h3>
                        <p class="text-sm text-gray-600">{{ brand.products_count }} sản phẩm</p>
                    </Link>
                </div>
            </div>
        </section>
    </UserLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import UserLayout from '@/Layouts/User/UserLayout.vue'

defineProps({
    featuredProducts: Array,
    categories: Array,
    brands: Array
})

const formatPrice = (price) => {
    const numPrice = parseFloat(price) || 0
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(numPrice)
}
</script>