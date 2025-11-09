<template>
    <UserLayout>
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="text-center">
                <!-- Success Icon -->
                <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-100 mb-6">
                    <svg class="h-12 w-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>

                <h1 class="text-3xl font-bold text-gray-900 mb-4">Thanh toán thành công!</h1>
                <p class="text-lg text-gray-600 mb-8">
                    Cảm ơn bạn đã mua sắm. Đơn hàng của bạn đã được xác nhận.
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
                            <span>Mã giao dịch:</span>
                            <span class="font-semibold text-gray-900">{{ order.payment_transaction_id }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Tổng tiền:</span>
                            <span class="font-bold text-blue-600 text-lg">{{ formatPrice(order.total) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Thời gian thanh toán:</span>
                            <span class="font-semibold text-gray-900">{{ formatDateTime(order.paid_at) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6 text-left">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Sản phẩm đã đặt</h2>
                    <div class="space-y-4">
                        <div v-for="item in order.items" :key="item.id" 
                             class="flex gap-4 pb-4 border-b last:border-0">
                            <img :src="item.product?.img_url || '/images/placeholder.jpg'" 
                                 :alt="item.product_name"
                                 class="w-20 h-20 object-cover rounded-lg">
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900">{{ item.product_name }}</h3>
                                <div v-if="item.color || item.size" class="text-sm text-gray-500 mt-1">
                                    <span v-if="item.color">Màu: {{ item.color }}</span>
                                    <span v-if="item.color && item.size"> | </span>
                                    <span v-if="item.size">Size: {{ item.size }}</span>
                                </div>
                                <p class="text-sm text-gray-600 mt-1">Số lượng: {{ item.quantity }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-blue-600">{{ formatPrice(item.subtotal) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shipping Info -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6 text-left">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Thông tin giao hàng</h2>
                    <div class="space-y-2 text-gray-600">
                        <p><span class="font-semibold">Người nhận:</span> {{ order.shipping_name }}</p>
                        <p><span class="font-semibold">Số điện thoại:</span> {{ order.shipping_phone }}</p>
                        <p><span class="font-semibold">Email:</span> {{ order.shipping_email }}</p>
                        <p><span class="font-semibold">Địa chỉ:</span> {{ order.shipping_address }}</p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-4 justify-center">
                    <Link :href="route('user.home')" 
                          class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-300 font-semibold">
                        Về trang chủ
                    </Link>
                    <Link :href="route('user.products.index')" 
                          class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition duration-300 font-semibold">
                        Tiếp tục mua sắm
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
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(price)
}

const formatDateTime = (dateString) => {
    if (!dateString) return ''
    const date = new Date(dateString)
    return new Intl.DateTimeFormat('vi-VN', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }).format(date)
}
</script>

