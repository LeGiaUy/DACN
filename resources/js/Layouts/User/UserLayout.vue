<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <header class="bg-white shadow-sm sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Top Bar -->
                <div class="flex justify-between items-center h-16">
                    <!-- Logo -->
                    <div class="flex-shrink-0">
                        <Link :href="route('user.home')" class="text-2xl md:text-3xl font-bold text-gray-900 hover:text-gray-700">
                            DACN STORE
                        </Link>
                    </div>

                    <!-- Desktop Navigation -->
                    <nav class="hidden lg:flex items-center space-x-2 flex-1 justify-center max-w-5xl mx-4">
                        <Link :href="route('user.home')"
                              :class="route().current('user.home') ? 'text-gray-900 font-semibold' : 'text-gray-600 hover:text-gray-900'"
                              class="px-2 py-2 text-sm font-medium transition whitespace-nowrap">
                            Trang chủ
                        </Link>

                        <!-- Products Dropdown -->
                        <div class="relative group">
                            <button :class="route().current('user.products.*') ? 'text-gray-900 font-semibold' : 'text-gray-600 hover:text-gray-900'"
                                    class="px-2 py-2 text-sm font-medium flex items-center transition whitespace-nowrap">
                                Sản phẩm
                                <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div class="absolute left-0 mt-2 w-56 bg-white rounded-md shadow-lg py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                <Link :href="route('user.products.index', { category: 'new' })"
                                      class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    HÀNG MỚI VỀ
                                </Link>
                                <Link :href="route('user.products.index')"
                                      class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Tất cả sản phẩm
                                </Link>
                                <div class="border-t my-1"></div>
                                <Link v-for="category in categories" :key="category.id"
                                      :href="route('user.categories.show', category.id)"
                                      class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    {{ category.name }}
                                </Link>
                            </div>
                        </div>

                        <Link :href="route('user.categories.index')"
                              :class="route().current('user.categories.*') ? 'text-gray-900 font-semibold' : 'text-gray-600 hover:text-gray-900'"
                              class="px-2 py-2 text-sm font-medium transition whitespace-nowrap">
                            Danh mục
                        </Link>
                        <Link :href="route('user.brands.index')"
                              :class="route().current('user.brands.*') ? 'text-gray-900 font-semibold' : 'text-gray-600 hover:text-gray-900'"
                              class="px-2 py-2 text-sm font-medium transition whitespace-nowrap">
                            Thương hiệu
                        </Link>
                        <Link :href="route('user.about')"
                              :class="route().current('user.about') ? 'text-gray-900 font-semibold' : 'text-gray-600 hover:text-gray-900'"
                              class="px-2 py-2 text-sm font-medium transition whitespace-nowrap">
                            Giới thiệu
                        </Link>
                        <Link :href="route('user.contact')"
                              :class="route().current('user.contact') ? 'text-gray-900 font-semibold' : 'text-gray-600 hover:text-gray-900'"
                              class="px-2 py-2 text-sm font-medium transition whitespace-nowrap">
                            Liên hệ
                        </Link>
                        <Link v-if="$page.props.auth.user" :href="route('user.orders.index')"
                              :class="route().current('user.orders.*') ? 'text-gray-900 font-semibold' : 'text-gray-600 hover:text-gray-900'"
                              class="px-2 py-2 text-sm font-medium transition whitespace-nowrap">
                            Đơn hàng
                        </Link>
                    </nav>

                    <!-- Right Side Actions -->
                    <div class="flex items-center space-x-2 flex-shrink-0">
                        <!-- Search -->
                        <div class="hidden md:block relative">
                            <input type="text"
                                   v-model="searchQuery"
                                   @keyup.enter="performSearch"
                                   placeholder="Tìm kiếm..."
                                   class="w-52 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent text-sm">
                            <button @click="performSearch" class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Mobile Search Button -->
                        <button @click="showMobileSearch = !showMobileSearch" class="md:hidden p-2 text-gray-600 hover:text-gray-900">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>

                        <!-- Cart -->
                        <Link v-if="$page.props.auth.user" :href="route('user.cart')"
                              class="relative p-2 text-gray-600 hover:text-gray-900">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                            </svg>
                            <span v-if="$page.props.cartCount > 0"
                                  class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-semibold">
                                {{ $page.props.cartCount }}
                            </span>
                        </Link>

                        <!-- Auth buttons -->
                        <div v-if="!$page.props.auth.user" class="flex items-center space-x-2">
                            <Link :href="route('login')"
                                  class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">
                                Đăng nhập
                            </Link>
                            <Link :href="route('register')"
                                  class="px-4 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800">
                                Đăng ký
                            </Link>
                        </div>

                        <!-- User dropdown -->
                        <div v-else class="relative">
                            <button @click="showUserMenu = !showUserMenu"
                                    class="flex items-center space-x-2 text-sm font-medium text-gray-700 hover:text-gray-900">
                                <span>{{ $page.props.auth.user.name }}</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <div v-show="showUserMenu"
                                 @click.away="showUserMenu = false"
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border">
                                <Link :href="route('user.account.index')"
                                      class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Quản lý tài khoản
                                </Link>
                                <Link :href="route('user.orders.index')"
                                      class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Đơn hàng của tôi
                                </Link>
                                <Link v-if="$page.props.auth.user?.is_admin" :href="route('dashboard')"
                                      class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Admin Dashboard
                                </Link>
                                <div class="border-t my-1"></div>
                                <Link :href="route('logout')" method="post" as="button"
                                      class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-left w-full">
                                    Đăng xuất
                                </Link>
                            </div>
                        </div>

                        <!-- Mobile Menu Button -->
                        <button @click="showMobileMenu = !showMobileMenu" class="lg:hidden p-2 text-gray-600 hover:text-gray-900">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Mobile Search Bar -->
                <div v-show="showMobileSearch" class="md:hidden pb-4">
                    <div class="relative">
                        <input type="text"
                               v-model="searchQuery"
                               @keyup.enter="performSearch"
                               placeholder="Tìm kiếm..."
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent text-sm">
                        <button @click="performSearch" class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Mobile Menu -->
                <div v-show="showMobileMenu" class="lg:hidden pb-4 border-t mt-2 pt-4">
                    <nav class="flex flex-col space-y-2">
                        <Link :href="route('user.home')"
                              @click="showMobileMenu = false"
                              :class="route().current('user.home') ? 'text-gray-900 font-semibold' : 'text-gray-600'"
                              class="px-3 py-2 text-sm font-medium">
                            Trang chủ
                        </Link>
                        <Link :href="route('user.products.index')"
                              @click="showMobileMenu = false"
                              :class="route().current('user.products.*') ? 'text-gray-900 font-semibold' : 'text-gray-600'"
                              class="px-3 py-2 text-sm font-medium">
                            Sản phẩm
                        </Link>
                        <Link :href="route('user.categories.index')"
                              @click="showMobileMenu = false"
                              :class="route().current('user.categories.*') ? 'text-gray-900 font-semibold' : 'text-gray-600'"
                              class="px-3 py-2 text-sm font-medium">
                            Danh mục
                        </Link>
                        <Link :href="route('user.brands.index')"
                              @click="showMobileMenu = false"
                              :class="route().current('user.brands.*') ? 'text-gray-900 font-semibold' : 'text-gray-600'"
                              class="px-3 py-2 text-sm font-medium">
                            Thương hiệu
                        </Link>
                        <Link :href="route('user.about')"
                              @click="showMobileMenu = false"
                              :class="route().current('user.about') ? 'text-gray-900 font-semibold' : 'text-gray-600'"
                              class="px-3 py-2 text-sm font-medium">
                            Giới thiệu
                        </Link>
                        <Link :href="route('user.contact')"
                              @click="showMobileMenu = false"
                              :class="route().current('user.contact') ? 'text-gray-900 font-semibold' : 'text-gray-600'"
                              class="px-3 py-2 text-sm font-medium">
                            Liên hệ
                        </Link>
                        <Link v-if="$page.props.auth.user" :href="route('user.orders.index')"
                              @click="showMobileMenu = false"
                              :class="route().current('user.orders.*') ? 'text-gray-900 font-semibold' : 'text-gray-600'"
                              class="px-3 py-2 text-sm font-medium">
                            Đơn hàng
                        </Link>
                    </nav>
                </div>
            </div>
        </header>

        <!-- Main content -->
        <main>
            <slot />
        </main>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white mt-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <!-- About -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Về chúng tôi</h3>
                        <p class="text-gray-300 text-sm leading-relaxed">
                            DACN STORE là cửa hàng trực tuyến uy tín với nhiều sản phẩm chất lượng cao.
                            Với phương châm "tinh tế đến từng chi tiết", DACN STORE luôn đem đến cho khách hàng những sản phẩm tốt nhất với giá cả hợp lý.
                        </p>
                    </div>

                    <!-- Links -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4">VỀ DACN</h3>
                        <ul class="space-y-2 text-sm">
                            <li><Link :href="route('user.home')" class="text-gray-300 hover:text-white">Trang chủ</Link></li>
                            <li><Link :href="route('user.about')" class="text-gray-300 hover:text-white">Giới thiệu</Link></li>
                            <li><Link :href="route('user.products.index')" class="text-gray-300 hover:text-white">Sản phẩm</Link></li>
                            <li><a href="#" class="text-gray-300 hover:text-white">Tin tức</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white">Mời hợp tác</a></li>
                            <li><Link :href="route('user.contact')" class="text-gray-300 hover:text-white">Liên hệ</Link></li>
                        </ul>
                    </div>

                    <!-- Categories -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4">KHÁM PHÁ DACN</h3>
                        <ul class="space-y-2 text-sm">
                            <li><Link :href="route('user.products.index')" class="text-gray-300 hover:text-white">HÀNG MỚI VỀ</Link></li>
                            <li><a href="#" class="text-gray-300 hover:text-white">ĐỒ ĐI CHƠI ( BÉ GÁI )</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white">ĐỒ ĐI CHƠI ( BÉ TRAI )</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white">ĐỒ ĐI HỌC ( BÉ GÁI )</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white">ĐỒ ĐI HỌC ( BÉ TRAI)</a></li>
                        </ul>
                    </div>

                    <!-- Support -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4">HỖ TRỢ KHÁCH HÀNG</h3>
                        <ul class="space-y-2 text-sm">
                            <li><a href="#" class="text-gray-300 hover:text-white">Chính sách đại lý</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white">Chính sách đổi hàng trong 30 ngày</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white">Giao hàng & phí giao hàng</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white">CÁCH ĐẶT HÀNG TRÊN WEBSITE</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Contact Info -->
                <div class="mt-12 pt-8 border-t border-gray-800">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <h4 class="font-semibold mb-3">Kết nối với DACN STORE</h4>
                            <div class="flex space-x-4">
                                <a href="#" class="text-gray-300 hover:text-white">
                                    <span class="sr-only">Facebook</span>
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                    </svg>
                                </a>
                                <a href="#" class="text-gray-300 hover:text-white">
                                    <span class="sr-only">Zalo</span>
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                        <div>
                            <h4 class="font-semibold mb-3">Thông tin liên hệ</h4>
                            <p class="text-gray-300 text-sm mb-1">Đ/c: Số 298 Đ. Cầu Diễn, Minh Khai, Bắc Từ Liêm, Hà Nội, Việt Nam</p>
                            <p class="text-gray-300 text-sm mb-1">Email: test@gmail.com</p>
                            <p class="text-gray-300 text-sm">Hotline: 0912345667</p>
                        </div>
                    </div>
                </div>

                <!-- Copyright -->
                <div class="mt-8 pt-8 border-t border-gray-800 text-center text-gray-400 text-sm">
                    <p>&copy; 2024 DACN STORE. Tất cả quyền được bảo lưu.</p>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'

const page = usePage()

const showUserMenu = ref(false)
const showMobileMenu = ref(false)
const showMobileSearch = ref(false)
const searchQuery = ref('')

const categories = computed(() => page.props.categories || [])

const performSearch = () => {
    if (searchQuery.value.trim()) {
        router.get(route('user.products.index'), { search: searchQuery.value })
        showMobileSearch.value = false
    }
}

// Close dropdowns when clicking outside
const closeDropdowns = () => {
    showUserMenu.value = false
}
</script>
