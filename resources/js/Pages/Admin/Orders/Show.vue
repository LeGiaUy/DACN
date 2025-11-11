<template>
    <div>
        <Menu></Menu>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <!-- Header -->
            <div class="mb-6">
                <Link :href="route('admin.orders.index')" 
                      class="text-teal-600 hover:text-teal-800 mb-4 inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Quay lại danh sách
                </Link>
                <h1 class="text-2xl font-semibold mb-2">Chi tiết đơn hàng</h1>
                <p class="text-gray-600">Mã đơn: {{ order.order_number }}</p>
            </div>

            <!-- Order Info Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Thông tin đơn hàng</h2>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Mã đơn:</span>
                            <span class="font-medium text-gray-900">{{ order.order_number }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Trạng thái:</span>
                            <span :class="getStatusBadgeClass(order.status)" 
                                  class="px-2 py-1 rounded text-xs font-medium">
                                {{ getStatusText(order.status) }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Thanh toán:</span>
                            <span :class="getPaymentStatusBadgeClass(order.payment_status)" 
                                  class="px-2 py-1 rounded text-xs font-medium">
                                {{ getPaymentStatusText(order.payment_status) }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Phương thức:</span>
                            <span class="font-medium text-gray-900">{{ getPaymentMethodText(order.payment_method) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Vận chuyển:</span>
                            <span class="font-medium text-gray-900">{{ getShippingMethodText(order.shipping_method) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Ngày tạo:</span>
                            <span class="font-medium text-gray-900">{{ formatDate(order.created_at) }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Thông tin khách hàng</h2>
                    <div class="space-y-3 text-sm">
                        <div>
                            <span class="text-gray-600">Tên:</span>
                            <p class="font-medium text-gray-900">{{ order.user?.name || order.shipping_name }}</p>
                        </div>
                        <div>
                            <span class="text-gray-600">Số điện thoại:</span>
                            <p class="font-medium text-gray-900">{{ order.shipping_phone }}</p>
                        </div>
                        <div>
                            <span class="text-gray-600">Email:</span>
                            <p class="font-medium text-gray-900">{{ order.shipping_email || '-' }}</p>
                        </div>
                        <div>
                            <span class="text-gray-600">Địa chỉ:</span>
                            <p class="font-medium text-gray-900">{{ order.shipping_address }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Update -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6 border border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Cập nhật trạng thái</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái đơn hàng</label>
                        <div class="flex gap-2">
                            <select v-model="status" 
                                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                                <option value="pending">Chờ xử lý</option>
                                <option value="processing">Đang xử lý</option>
                                <option value="shipped">Đã gửi hàng</option>
                                <option value="delivered">Đã giao hàng</option>
                                <option value="cancelled">Đã hủy</option>
                            </select>
                            <button @click="saveStatus" 
                                    class="px-4 py-2 bg-teal-500 text-white rounded-lg hover:bg-teal-600 transition duration-300 font-medium">
                                Lưu
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái thanh toán</label>
                        <div class="flex gap-2">
                            <select v-model="paymentStatus" 
                                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                                <option value="pending">Chờ thanh toán</option>
                                <option value="paid">Đã thanh toán</option>
                                <option value="failed">Thất bại</option>
                                <option value="refunded">Hoàn tiền</option>
                            </select>
                            <button @click="savePayment" 
                                    class="px-4 py-2 bg-teal-500 text-white rounded-lg hover:bg-teal-600 transition duration-300 font-medium">
                                Lưu
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 mb-6">
                <div class="px-6 py-4 bg-gray-50 border-b">
                    <h2 class="text-lg font-semibold text-gray-900">Sản phẩm đã đặt</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="table-auto w-full border-collapse border border-gray-200">
                        <thead>
                            <tr class="bg-gray-100 text-left">
                                <th class="border border-gray-300 px-4 py-3 text-sm font-semibold text-gray-700">Sản phẩm</th>
                                <th class="border border-gray-300 px-4 py-3 text-sm font-semibold text-gray-700">Phân loại</th>
                                <th class="border border-gray-300 px-4 py-3 text-sm font-semibold text-gray-700 text-center">Số lượng</th>
                                <th class="border border-gray-300 px-4 py-3 text-sm font-semibold text-gray-700 text-right">Giá</th>
                                <th class="border border-gray-300 px-4 py-3 text-sm font-semibold text-gray-700 text-right">Tạm tính</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in order.items" :key="item.id" class="hover:bg-gray-50">
                                <td class="border border-gray-300 px-4 py-3">
                                    <span class="font-medium text-gray-900">{{ item.product_name }}</span>
                                </td>
                                <td class="border border-gray-300 px-4 py-3">
                                    <span class="text-sm text-gray-600">
                                        <span v-if="item.color">Màu: {{ item.color }}</span>
                                        <span v-if="item.color && item.size">, </span>
                                        <span v-if="item.size">Size: {{ item.size }}</span>
                                        <span v-if="!item.color && !item.size">-</span>
                                    </span>
                                </td>
                                <td class="border border-gray-300 px-4 py-3 text-center">
                                    <span class="text-gray-900">{{ item.quantity }}</span>
                                </td>
                                <td class="border border-gray-300 px-4 py-3 text-right">
                                    <span class="text-gray-900">{{ formatCurrency(item.price) }}</span>
                                </td>
                                <td class="border border-gray-300 px-4 py-3 text-right">
                                    <span class="font-medium text-gray-900">{{ formatCurrency(item.subtotal) }}</span>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="4" class="border border-gray-300 px-4 py-3 text-right font-medium text-gray-700">
                                    Tạm tính:
                                </td>
                                <td class="border border-gray-300 px-4 py-3 text-right font-medium text-gray-900">
                                    {{ formatCurrency(order.subtotal) }}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4" class="border border-gray-300 px-4 py-3 text-right font-medium text-gray-700">
                                    Phí vận chuyển:
                                </td>
                                <td class="border border-gray-300 px-4 py-3 text-right font-medium text-gray-900">
                                    {{ formatCurrency(order.shipping_fee) }}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4" class="border border-gray-300 px-4 py-3 text-right font-semibold text-gray-900">
                                    Tổng cộng:
                                </td>
                                <td class="border border-gray-300 px-4 py-3 text-right font-bold text-teal-600 text-lg">
                                    {{ formatCurrency(order.total) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import Menu from '../../Includes/Menu.vue'

const props = defineProps({
    order: { type: Object, required: true }
})

const status = ref(props.order.status)
const paymentStatus = ref(props.order.payment_status)

const saveStatus = () => {
    router.patch(route('admin.orders.update-status', props.order.id), { status: status.value }, { preserveScroll: true })
}

const savePayment = () => {
    router.patch(route('admin.orders.update-payment-status', props.order.id), { payment_status: paymentStatus.value }, { preserveScroll: true })
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

const getPaymentStatusBadgeClass = (status) => {
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
