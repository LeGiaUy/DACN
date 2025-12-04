<template>
    <UserLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">Sản phẩm</h1>
                <p class="text-gray-600">Khám phá tất cả sản phẩm của chúng tôi</p>
            </div>

            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Sidebar Filters -->
                <div class="lg:w-1/4">
                    <div class="bg-white rounded-lg shadow-sm border p-6 sticky top-24">
                        <h3 class="text-lg font-semibold mb-6 text-gray-900">Bộ lọc</h3>
                        
                        <!-- Search -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tìm kiếm</label>
                            <input type="text" 
                                   v-model="filters.search"
                                   @keyup.enter="applyFilters"
                                   placeholder="Nhập tên sản phẩm..."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                        </div>

                        <!-- Category Filter -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Danh mục</label>
                            <select v-model="filters.category" @change="applyFilters" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                <option value="">Tất cả danh mục</option>
                                <option v-for="category in categories" :key="category.id" :value="category.id">
                                    {{ category.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Brand Filter -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Thương hiệu</label>
                            <select v-model="filters.brand" @change="applyFilters" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                <option value="">Tất cả thương hiệu</option>
                                <option v-for="brand in brands" :key="brand.id" :value="brand.id">
                                    {{ brand.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Price Range -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Khoảng giá</label>
                            <div class="space-y-2">
                                <input type="number" 
                                       v-model="filters.min_price"
                                       @change="applyFilters"
                                       placeholder="Giá tối thiểu"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                <input type="number" 
                                       v-model="filters.max_price"
                                       @change="applyFilters"
                                       placeholder="Giá tối đa"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                            </div>
                        </div>

                        <!-- Sort -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Sắp xếp</label>
                            <select v-model="filters.sort" @change="applyFilters" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                <option value="created_at">Mới nhất</option>
                                <option value="name">Tên A-Z</option>
                                <option value="price">Giá thấp đến cao</option>
                                <option value="price_desc">Giá cao đến thấp</option>
                            </select>
                        </div>

                        <!-- Clear Filters -->
                        <button @click="clearFilters" 
                                class="w-full bg-gray-200 text-gray-900 px-4 py-2 rounded-md hover:bg-gray-300 transition duration-300 font-medium">
                            Xóa bộ lọc
                        </button>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="lg:w-3/4">
                    <!-- Results Count and Sort -->
                    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <p class="text-gray-600">
                            Hiển thị <span class="font-semibold">{{ products.data.length }}</span> trong 
                            <span class="font-semibold">{{ products.total }}</span> sản phẩm
                        </p>
                    </div>

                    <!-- Products Grid -->
                    <div v-if="products.data.length > 0" 
                         class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6">
                        <div v-for="product in products.data" :key="product.id" 
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
                                          class="bg-white text-gray-900 px-4 py-2 rounded font-semibold hover:bg-gray-100 text-sm">
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
                                <div class="mt-auto">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span v-if="product.original_price" class="text-xs md:text-sm text-gray-400 line-through">
                                            {{ formatPrice(product.original_price) }}
                                        </span>
                                        <span class="text-base md:text-lg font-bold text-blue-700">
                                            {{ formatPrice(product.price) }}
                                        </span>
                                    </div>
                                    <div class="mt-1">
                                        <span class="text-xs text-gray-500">{{ product.category?.name }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- No Products -->
                    <div v-else class="text-center py-16 bg-white rounded-lg">
                        <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Không tìm thấy sản phẩm</h3>
                        <p class="text-sm text-gray-500 mb-4">Thử thay đổi bộ lọc để tìm sản phẩm khác.</p>
                        <button @click="clearFilters" 
                                class="inline-block bg-gray-900 text-white px-6 py-2 rounded-lg hover:bg-gray-800 transition duration-300">
                            Xóa bộ lọc
                        </button>
                    </div>

                    <!-- Pagination -->
                    <div v-if="products.links && products.data.length > 0" class="mt-10">
                        <nav class="flex items-center justify-between">
                            <div class="flex-1 flex justify-between sm:hidden">
                                <Link v-if="products.prev_page_url" :href="products.prev_page_url" 
                                      class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Trước
                                </Link>
                                <Link v-else class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-300 bg-white cursor-not-allowed">
                                    Trước
                                </Link>
                                <Link v-if="products.next_page_url" :href="products.next_page_url" 
                                      class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Sau
                                </Link>
                                <Link v-else class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-300 bg-white cursor-not-allowed">
                                    Sau
                                </Link>
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700">
                                        Hiển thị <span class="font-medium">{{ products.from }}</span> đến 
                                        <span class="font-medium">{{ products.to }}</span> trong 
                                        <span class="font-medium">{{ products.total }}</span> kết quả
                                    </p>
                                </div>
                                <div>
                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                        <Link v-for="link in products.links" :key="link.label" 
                                              :href="link.url" 
                                              v-html="link.label"
                                              :class="[
                                                  'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                                                  link.active 
                                                      ? 'z-10 bg-gray-900 border-gray-900 text-white' 
                                                      : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                                                  link.url === null ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'
                                              ]">
                                        </Link>
                                    </nav>
                                </div>
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </UserLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import UserLayout from '@/Layouts/User/UserLayout.vue'

const props = defineProps({
    products: Object,
    categories: Array,
    brands: Array,
    filters: Object
})

const filters = ref({ ...props.filters })

const applyFilters = () => {
    router.get(route('user.products.index'), filters.value, {
        preserveState: true,
        replace: true
    })
}

const clearFilters = () => {
    filters.value = {}
    applyFilters()
}

const formatPrice = (price) => {
    const numPrice = parseFloat(price) || 0
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(numPrice)
}
</script>
