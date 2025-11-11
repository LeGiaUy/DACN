<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <header class="bg-white shadow-sm sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo -->
                    <div class="flex-shrink-0">
                        <Link :href="route('user.home')" class="text-2xl font-bold text-gray-900">
                            DACN Store
                        </Link>
                    </div>

                    <!-- Navigation -->
                    <nav class="hidden md:flex space-x-8">
                        <Link :href="route('user.home')" 
                              :class="route().current('user.home') ? 'text-blue-600' : 'text-gray-500 hover:text-gray-900'"
                              class="px-3 py-2 text-sm font-medium">
                            Trang chủ
                        </Link>
                        <Link :href="route('user.products.index')" 
                              :class="route().current('user.products.*') ? 'text-blue-600' : 'text-gray-500 hover:text-gray-900'"
                              class="px-3 py-2 text-sm font-medium">
                            Sản phẩm
                        </Link>
                        <Link :href="route('user.categories.index')" 
                              :class="route().current('user.categories.*') ? 'text-blue-600' : 'text-gray-500 hover:text-gray-900'"
                              class="px-3 py-2 text-sm font-medium">
                            Danh mục
                        </Link>
                        <Link :href="route('user.brands.index')" 
                              :class="route().current('user.brands.*') ? 'text-blue-600' : 'text-gray-500 hover:text-gray-900'"
                              class="px-3 py-2 text-sm font-medium">
                            Thương hiệu
                        </Link>
                        <Link v-if="$page.props.auth.user" :href="route('user.orders.index')" 
                              :class="route().current('user.orders.*') ? 'text-blue-600' : 'text-gray-500 hover:text-gray-900'"
                              class="px-3 py-2 text-sm font-medium">
                            Đơn hàng
                        </Link>
                    </nav>

                    <!-- Search and User menu -->
                    <div class="flex items-center space-x-4">
                        <!-- Search -->
                        <div class="relative">
                            <input type="text" 
                                   v-model="searchQuery"
                                   @keyup.enter="performSearch"
                                   placeholder="Tìm kiếm sản phẩm..." 
                                   class="w-64 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <!-- Cart -->
                        <Link v-if="$page.props.auth.user" :href="route('user.cart')" class="relative p-2 text-gray-500 hover:text-gray-900">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                            </svg>
                            <span v-if="$page.props.cartCount > 0" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">{{ $page.props.cartCount }}</span>
                        </Link>

                        <!-- Auth buttons -->
                        <div v-if="!$page.props.auth.user" class="flex space-x-2">
                            <Link :href="route('login')" class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-900">
                                Đăng nhập
                            </Link>
                            <Link :href="route('register')" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                                Đăng ký
                            </Link>
                        </div>

                        <!-- User dropdown -->
                        <div v-else class="relative">
                            <button @click="showUserMenu = !showUserMenu" class="flex items-center space-x-2 text-sm font-medium text-gray-700 hover:text-gray-900">
                                <span>{{ $page.props.auth.user.name }}</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <div v-show="showUserMenu" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                <Link :href="route('profile.edit')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Hồ sơ
                                </Link>
                                <Link :href="route('user.orders.index')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Đơn hàng của tôi
                                </Link>
                                <Link :href="route('dashboard')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Admin Dashboard
                                </Link>
                                <Link :href="route('logout')" method="post" as="button" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-left w-full">
                                    Đăng xuất
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main content -->
        <main>
            <slot />
        </main>

        <!-- Footer -->
        <footer class="bg-gray-800 text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div>
                        <h3 class="text-lg font-semibold mb-4">DACN Store</h3>
                        <p class="text-gray-300">Cửa hàng trực tuyến uy tín với nhiều sản phẩm chất lượng cao.</p>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Liên kết</h3>
                        <ul class="space-y-2">
                            <li><Link :href="route('user.home')" class="text-gray-300 hover:text-white">Trang chủ</Link></li>
                            <li><Link :href="route('user.products.index')" class="text-gray-300 hover:text-white">Sản phẩm</Link></li>
                            <li><Link :href="route('user.categories.index')" class="text-gray-300 hover:text-white">Danh mục</Link></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Hỗ trợ</h3>
                        <ul class="space-y-2">
                            <li><a href="#" class="text-gray-300 hover:text-white">Liên hệ</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white">Hướng dẫn mua hàng</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white">Chính sách đổi trả</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Theo dõi chúng tôi</h3>
                        <div class="flex space-x-4">
                            <a href="#" class="text-gray-300 hover:text-white">Facebook</a>
                            <a href="#" class="text-gray-300 hover:text-white">Instagram</a>
                            <a href="#" class="text-gray-300 hover:text-white">Twitter</a>
                        </div>
                    </div>
                </div>
                <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-300">
                    <p>&copy; 2024 DACN Store. Tất cả quyền được bảo lưu.</p>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'

const showUserMenu = ref(false)
const searchQuery = ref('')

const performSearch = () => {
    if (searchQuery.value.trim()) {
        router.get(route('user.products.index'), { search: searchQuery.value })
    }
}
</script>