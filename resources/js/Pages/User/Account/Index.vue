<template>
    <UserLayout>
        <Head title="Quản lý tài khoản" />
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Quản lý tài khoản</h1>
                <p class="mt-2 text-sm text-gray-600">Quản lý thông tin cá nhân và cài đặt tài khoản của bạn</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Sidebar Navigation -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-sm p-4">
                        <nav class="space-y-2">
                            <button
                                @click="activeTab = 'profile'"
                                :class="activeTab === 'profile' ? 'bg-teal-50 text-teal-700 border-teal-500' : 'text-gray-700 hover:bg-gray-50'"
                                class="w-full text-left px-4 py-3 rounded-lg border-2 transition"
                            >
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Thông tin cá nhân
                                </div>
                            </button>
                            <button
                                @click="activeTab = 'password'"
                                :class="activeTab === 'password' ? 'bg-teal-50 text-teal-700 border-teal-500' : 'text-gray-700 hover:bg-gray-50'"
                                class="w-full text-left px-4 py-3 rounded-lg border-2 transition"
                            >
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                    </svg>
                                    Đổi mật khẩu
                                </div>
                            </button>
                            <button
                                @click="activeTab = 'orders'"
                                :class="activeTab === 'orders' ? 'bg-teal-50 text-teal-700 border-teal-500' : 'text-gray-700 hover:bg-gray-50'"
                                class="w-full text-left px-4 py-3 rounded-lg border-2 transition"
                            >
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    Đơn hàng của tôi
                                </div>
                            </button>
                            <button
                                @click="activeTab = 'statistics'"
                                :class="activeTab === 'statistics' ? 'bg-teal-50 text-teal-700 border-teal-500' : 'text-gray-700 hover:bg-gray-50'"
                                class="w-full text-left px-4 py-3 rounded-lg border-2 transition"
                            >
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                    Thống kê
                                </div>
                            </button>
                        </nav>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="lg:col-span-2">
                    <!-- Profile Information Tab -->
                    <div v-show="activeTab === 'profile'" class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6">Thông tin cá nhân</h2>
                        
                        <form @submit.prevent="updateProfile">
                            <div class="space-y-6">
                                <!-- Name -->
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                        Họ và tên <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="name"
                                        v-model="profileForm.name"
                                        type="text"
                                        required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                                        :class="{ 'border-red-500': profileForm.errors.name }"
                                    />
                                    <div v-if="profileForm.errors.name" class="mt-1 text-sm text-red-600">
                                        {{ profileForm.errors.name }}
                                    </div>
                                </div>

                                <!-- Email -->
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                        Email <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="email"
                                        v-model="profileForm.email"
                                        type="email"
                                        required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                                        :class="{ 'border-red-500': profileForm.errors.email }"
                                    />
                                    <div v-if="profileForm.errors.email" class="mt-1 text-sm text-red-600">
                                        {{ profileForm.errors.email }}
                                    </div>
                                </div>

                                <!-- Phone -->
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                                        Số điện thoại
                                    </label>
                                    <input
                                        id="phone"
                                        v-model="profileForm.phone"
                                        type="text"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                                        :class="{ 'border-red-500': profileForm.errors.phone }"
                                    />
                                    <div v-if="profileForm.errors.phone" class="mt-1 text-sm text-red-600">
                                        {{ profileForm.errors.phone }}
                                    </div>
                                </div>

                                <!-- Address -->
                                <div>
                                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">
                                        Địa chỉ
                                    </label>
                                    <textarea
                                        id="address"
                                        v-model="profileForm.address"
                                        rows="3"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                                        :class="{ 'border-red-500': profileForm.errors.address }"
                                    ></textarea>
                                    <div v-if="profileForm.errors.address" class="mt-1 text-sm text-red-600">
                                        {{ profileForm.errors.address }}
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="flex justify-end">
                                    <button
                                        type="submit"
                                        :disabled="profileForm.processing"
                                        class="px-6 py-2 bg-teal-500 text-white rounded-lg hover:bg-teal-600 disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        {{ profileForm.processing ? 'Đang lưu...' : 'Lưu thay đổi' }}
                                    </button>
                                </div>

                                <!-- Success Message -->
                                <Transition
                                    enter-active-class="transition ease-in-out"
                                    enter-from-class="opacity-0"
                                    leave-active-class="transition ease-in-out"
                                    leave-to-class="opacity-0"
                                >
                                    <div
                                        v-if="profileForm.recentlySuccessful"
                                        class="p-4 bg-green-50 border border-green-200 rounded-lg text-green-800"
                                    >
                                        Đã cập nhật thông tin thành công!
                                    </div>
                                </Transition>
                            </div>
                        </form>
                    </div>

                    <!-- Change Password Tab -->
                    <div v-show="activeTab === 'password'" class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6">Đổi mật khẩu</h2>
                        
                        <form @submit.prevent="updatePassword">
                            <div class="space-y-6">
                                <!-- Current Password -->
                                <div>
                                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">
                                        Mật khẩu hiện tại <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="current_password"
                                        v-model="passwordForm.current_password"
                                        type="password"
                                        required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                                        :class="{ 'border-red-500': passwordForm.errors.current_password }"
                                    />
                                    <div v-if="passwordForm.errors.current_password" class="mt-1 text-sm text-red-600">
                                        {{ passwordForm.errors.current_password }}
                                    </div>
                                </div>

                                <!-- New Password -->
                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                        Mật khẩu mới <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="password"
                                        v-model="passwordForm.password"
                                        type="password"
                                        required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                                        :class="{ 'border-red-500': passwordForm.errors.password }"
                                    />
                                    <div v-if="passwordForm.errors.password" class="mt-1 text-sm text-red-600">
                                        {{ passwordForm.errors.password }}
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">Tối thiểu 8 ký tự</p>
                                </div>

                                <!-- Confirm Password -->
                                <div>
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                                        Xác nhận mật khẩu mới <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="password_confirmation"
                                        v-model="passwordForm.password_confirmation"
                                        type="password"
                                        required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                                    />
                                </div>

                                <!-- Submit Button -->
                                <div class="flex justify-end">
                                    <button
                                        type="submit"
                                        :disabled="passwordForm.processing"
                                        class="px-6 py-2 bg-teal-500 text-white rounded-lg hover:bg-teal-600 disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        {{ passwordForm.processing ? 'Đang đổi...' : 'Đổi mật khẩu' }}
                                    </button>
                                </div>

                                <!-- Success Message -->
                                <Transition
                                    enter-active-class="transition ease-in-out"
                                    enter-from-class="opacity-0"
                                    leave-active-class="transition ease-in-out"
                                    leave-to-class="opacity-0"
                                >
                                    <div
                                        v-if="passwordForm.recentlySuccessful"
                                        class="p-4 bg-green-50 border border-green-200 rounded-lg text-green-800"
                                    >
                                        Đã đổi mật khẩu thành công!
                                    </div>
                                </Transition>
                            </div>
                        </form>
                    </div>

                    <!-- Orders Tab -->
                    <div v-show="activeTab === 'orders'" class="bg-white rounded-lg shadow-sm p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-xl font-semibold text-gray-900">Đơn hàng của tôi</h2>
                            <Link :href="route('user.orders.index')" 
                                  class="text-teal-600 hover:text-teal-700 text-sm font-medium">
                                Xem tất cả →
                            </Link>
                        </div>
                        
                        <div v-if="user.orders && user.orders.length > 0" class="space-y-4">
                            <div v-for="order in user.orders.slice(0, 5)" :key="order.id"
                                 class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-semibold text-gray-900">Đơn hàng #{{ order.id }}</p>
                                        <p class="text-sm text-gray-600 mt-1">
                                            {{ formatDate(order.created_at) }}
                                        </p>
                                        <p class="text-sm font-medium mt-2">
                                            Tổng tiền: 
                                            <span class="text-teal-600">{{ formatCurrency(order.total_amount) }}</span>
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <span :class="getStatusClass(order.status)" 
                                              class="px-3 py-1 rounded-full text-xs font-semibold">
                                            {{ getStatusText(order.status) }}
                                        </span>
                                        <Link :href="route('user.orders.show', order.id)"
                                              class="block mt-2 text-sm text-teal-600 hover:text-teal-700">
                                            Xem chi tiết →
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-12">
                            <p class="text-gray-500">Bạn chưa có đơn hàng nào</p>
                            <Link :href="route('user.products.index')" 
                                  class="mt-4 inline-block text-teal-600 hover:text-teal-700">
                                Mua sắm ngay →
                            </Link>
                        </div>
                    </div>

                    <!-- Statistics Tab -->
                    <div v-show="activeTab === 'statistics'" class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6">Thống kê tài khoản</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-gray-50 rounded-lg p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-gray-600">Tổng đơn hàng</p>
                                        <p class="text-3xl font-bold text-gray-900 mt-2">
                                            {{ user.orders?.length || 0 }}
                                        </p>
                                    </div>
                                    <div class="w-12 h-12 bg-teal-100 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gray-50 rounded-lg p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-gray-600">Sản phẩm trong giỏ</p>
                                        <p class="text-3xl font-bold text-gray-900 mt-2">
                                            {{ user.cart_items?.length || 0 }}
                                        </p>
                                    </div>
                                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gray-50 rounded-lg p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-gray-600">Ngày tham gia</p>
                                        <p class="text-lg font-semibold text-gray-900 mt-2">
                                            {{ formatDate(user.created_at) }}
                                        </p>
                                    </div>
                                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gray-50 rounded-lg p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-gray-600">Đăng nhập cuối</p>
                                        <p class="text-lg font-semibold text-gray-900 mt-2">
                                            {{ formatDate(user.last_login_at) }}
                                        </p>
                                    </div>
                                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </UserLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/User/UserLayout.vue';

const props = defineProps({
    user: {
        type: Object,
        required: true
    }
});

const activeTab = ref('profile');

const profileForm = useForm({
    name: props.user.name,
    email: props.user.email,
    phone: props.user.phone || '',
    address: props.user.address || '',
});

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updateProfile = () => {
    profileForm.patch(route('profile.update'), {
        preserveScroll: true,
    });
};

const updatePassword = () => {
    passwordForm.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => {
            passwordForm.reset();
        }
    });
};

const formatDate = (date) => {
    if (!date) return 'Chưa có';
    return new Date(date).toLocaleString('vi-VN');
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(amount);
};

const getStatusClass = (status) => {
    const classes = {
        'pending': 'bg-yellow-100 text-yellow-800',
        'processing': 'bg-blue-100 text-blue-800',
        'shipped': 'bg-purple-100 text-purple-800',
        'delivered': 'bg-green-100 text-green-800',
        'cancelled': 'bg-red-100 text-red-800',
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const getStatusText = (status) => {
    const texts = {
        'pending': 'Chờ xử lý',
        'processing': 'Đang xử lý',
        'shipped': 'Đã giao hàng',
        'delivered': 'Đã nhận hàng',
        'cancelled': 'Đã hủy',
    };
    return texts[status] || status;
};
</script>

<style scoped>
</style>

