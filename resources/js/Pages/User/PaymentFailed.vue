<template>
    <UserLayout>
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="text-center">
                <!-- Failed Icon -->
                <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-red-100 mb-6">
                    <svg class="h-12 w-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>

                <h1 class="text-3xl font-bold text-gray-900 mb-4">Thanh toán thất bại</h1>
                <p class="text-lg text-gray-600 mb-8">
                    Rất tiếc, thanh toán của bạn không thành công. Vui lòng thử lại.
                </p>

                <!-- Order Info -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6 text-left">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Thông tin đơn hàng</h2>
                    <div class="space-y-2 text-gray-600">
                        <div class="flex justify-between">
                            <span>Mã đơn hàng:</span>
                            <span class="font-semibold text-gray-900">{{ order.order_number }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Tổng tiền:</span>
                            <span class="font-bold text-blue-600 text-lg">{{ formatPrice(order.total) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Trạng thái:</span>
                            <span class="font-semibold text-red-600">Thanh toán thất bại</span>
                        </div>
                    </div>
                </div>

                <!-- Error Message -->
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <p class="text-red-800">
                        <strong>Lý do có thể:</strong>
                    </p>
                    <ul class="list-disc list-inside text-red-700 mt-2 space-y-1 text-left">
                        <li>Thông tin thẻ không hợp lệ</li>
                        <li>Số dư tài khoản không đủ</li>
                        <li>Thẻ đã bị khóa hoặc hết hạn</li>
                        <li>Lỗi kết nối với ngân hàng</li>
                    </ul>
                </div>

                <!-- Actions -->
                <div class="flex gap-4 justify-center">
                    <Link :href="route('user.checkout.payment', order.id)" 
                          class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-300 font-semibold">
                        Thử lại thanh toán
                    </Link>
                    <Link :href="route('user.cart')" 
                          class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition duration-300 font-semibold">
                        Quay lại giỏ hàng
                    </Link>
                </div>
            </div>
        </div>
    </UserLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import UserLayout from '@/Layouts/User/UserLayout.vue'

const props = defineProps({
    order: Object
})

const formatPrice = (price) => {
    const numPrice = parseFloat(price) || 0
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(numPrice)
}
</script>

