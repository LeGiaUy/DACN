<template>
    <UserLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-6">
                <Link :href="route('user.orders.index')" 
                      class="text-blue-600 hover:text-blue-800 mb-4 inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Quay lại danh sách đơn hàng
                </Link>
                <h1 class="text-3xl font-bold text-gray-900">Chi tiết đơn hàng</h1>
                <p class="text-gray-600 mt-2">Mã đơn: {{ order.order_number }}</p>
            </div>

            <!-- Order Status Card -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <span :class="getStatusBadgeClass(order.status)" 
                              class="px-4 py-2 rounded-full text-sm font-medium">
                            {{ getStatusText(order.status) }}
                        </span>
                        <span :class="getPaymentStatusClass(order.payment_status)" 
                              class="px-4 py-2 rounded-full text-sm font-medium">
                            {{ getPaymentStatusText(order.payment_status) }}
                        </span>
                    </div>
                    <div class="text-sm text-gray-600">
                        Ngày đặt: {{ formatDate(order.created_at) }}
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Order Items -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="px-6 py-4 bg-gray-50 border-b">
                            <h2 class="text-lg font-semibold text-gray-900">Sản phẩm đã đặt</h2>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sản phẩm</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phân loại</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Số lượng</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Giá</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Tạm tính</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="item in order.items" :key="item.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ item.product_name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">
                                                <span v-if="item.color">Màu: {{ item.color }}</span>
                                                <span v-if="item.color && item.size">, </span>
                                                <span v-if="item.size">Size: {{ item.size }}</span>
                                                <span v-if="!item.color && !item.size">-</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="text-sm text-gray-900">{{ item.quantity }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <span class="text-sm text-gray-900">{{ formatCurrency(item.price) }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <span class="text-sm font-medium text-gray-900">{{ formatCurrency(item.subtotal) }}</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Order Summary -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Tóm tắt đơn hàng</h2>
                        <div class="space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Tạm tính:</span>
                                <span class="text-gray-900 font-medium">{{ formatCurrency(order.subtotal) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Phí vận chuyển:</span>
                                <span class="text-gray-900 font-medium">{{ formatCurrency(order.shipping_fee) }}</span>
                            </div>
                            <div class="border-t pt-3 flex justify-between">
                                <span class="text-base font-semibold text-gray-900">Tổng cộng:</span>
                                <span class="text-lg font-bold text-blue-600">{{ formatCurrency(order.total) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Info -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Thông tin giao hàng</h2>
                        <div class="space-y-2 text-sm">
                            <div>
                                <span class="font-medium text-gray-700">Người nhận:</span>
                                <p class="text-gray-900">{{ order.shipping_name }}</p>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Số điện thoại:</span>
                                <p class="text-gray-900">{{ order.shipping_phone }}</p>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Email:</span>
                                <p class="text-gray-900">{{ order.shipping_email || '-' }}</p>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Địa chỉ:</span>
                                <p class="text-gray-900">{{ order.shipping_address }}</p>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Phương thức vận chuyển:</span>
                                <p class="text-gray-900">{{ getShippingMethodText(order.shipping_method) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Info -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Thông tin thanh toán</h2>
                        <div class="space-y-2 text-sm">
                            <div>
                                <span class="font-medium text-gray-700">Phương thức:</span>
                                <p class="text-gray-900">{{ getPaymentMethodText(order.payment_method) }}</p>
                            </div>
                            <div v-if="order.payment_transaction_id">
                                <span class="font-medium text-gray-700">Mã giao dịch:</span>
                                <p class="text-gray-900 font-mono text-xs">{{ order.payment_transaction_id }}</p>
                            </div>
                            <div v-if="order.paid_at">
                                <span class="font-medium text-gray-700">Ngày thanh toán:</span>
                                <p class="text-gray-900">{{ formatDate(order.paid_at) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <button
                            v-if="['pending', 'processing'].includes(order.status)"
                            @click="cancelOrder"
                            class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition duration-300 font-medium">
                            Hủy đơn hàng
                        </button>
                        <p v-else class="text-sm text-gray-500 text-center">
                            Không thể hủy đơn hàng này
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </UserLayout>
</template>

<script setup>
import { router, Link } from '@inertiajs/vue3'
import UserLayout from '@/Layouts/User/UserLayout.vue'

const props = defineProps({
    order: { type: Object, required: true }
})

const cancelOrder = () => {
    if (confirm('Bạn có chắc muốn hủy đơn hàng này?')) {
        router.post(route('user.orders.cancel', props.order.id), {}, { preserveScroll: true })
    }
}

const formatCurrency = (v) => {
    if (v == null) return ''
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(Number(v))
}

const formatDate = (date) => {
    if (!date) return ''
    return new Date(date).toLocaleString('vi-VN', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    })
}

const getStatusText = (status) => {
    const statusMap = {
        'pending': 'Chờ xử lý',
        'processing': 'Đang xử lý',
        'shipped': 'Đã gửi hàng',
        'delivered': 'Đã giao hàng',
        'cancelled': 'Đã hủy'
    }
    return statusMap[status] || status
}

const getStatusBadgeClass = (status) => {
    const classMap = {
        'pending': 'bg-yellow-100 text-yellow-800',
        'processing': 'bg-blue-100 text-blue-800',
        'shipped': 'bg-purple-100 text-purple-800',
        'delivered': 'bg-green-100 text-green-800',
        'cancelled': 'bg-red-100 text-red-800'
    }
    return classMap[status] || 'bg-gray-100 text-gray-800'
}

const getPaymentStatusText = (status) => {
    const statusMap = {
        'pending': 'Chờ thanh toán',
        'paid': 'Đã thanh toán',
        'failed': 'Thất bại',
        'refunded': 'Đã hoàn tiền'
    }
    return statusMap[status] || status
}

const getPaymentStatusClass = (status) => {
    const classMap = {
        'pending': 'bg-yellow-100 text-yellow-800',
        'paid': 'bg-green-100 text-green-800',
        'failed': 'bg-red-100 text-red-800',
        'refunded': 'bg-orange-100 text-orange-800'
    }
    return classMap[status] || 'bg-gray-100 text-gray-800'
}

const getPaymentMethodText = (method) => {
    const methodMap = {
        'cod': 'Thanh toán khi nhận hàng',
        'bank_card': 'Thẻ ngân hàng',
        'qr_code': 'QR Code'
    }
    return methodMap[method] || method
}

const getShippingMethodText = (method) => {
    const methodMap = {
        'standard': 'Giao hàng tiết kiệm',
        'fast': 'Giao hàng nhanh',
        'express': 'Giao hàng hỏa tốc'
    }
    return methodMap[method] || method
}
</script>
