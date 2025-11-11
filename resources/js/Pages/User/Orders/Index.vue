<template>
    <UserLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Đơn hàng của tôi</h1>
                <p class="text-gray-600 mt-2">Theo dõi và quản lý đơn hàng của bạn</p>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <form @submit.prevent="applyFilter" class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Lọc theo trạng thái</label>
                        <select v-model="localFilters.status" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Tất cả trạng thái</option>
                            <option value="pending">Chờ xử lý</option>
                            <option value="processing">Đang xử lý</option>
                            <option value="shipped">Đã gửi hàng</option>
                            <option value="delivered">Đã giao hàng</option>
                            <option value="cancelled">Đã hủy</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" 
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-300 font-medium">
                            Lọc
                        </button>
                    </div>
                </form>
            </div>

            <!-- Empty State -->
            <div v-if="orders.data.length === 0" class="text-center py-12 bg-white rounded-lg shadow-md">
                <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h2 class="mt-4 text-xl font-semibold text-gray-900">Chưa có đơn hàng</h2>
                <p class="mt-2 text-gray-600">Bạn chưa có đơn hàng nào</p>
                <Link :href="route('user.products.index')" 
                      class="mt-6 inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-300">
                    Tiếp tục mua sắm
                </Link>
            </div>

            <!-- Orders List -->
            <div v-else class="space-y-4">
                <div v-for="order in orders.data" :key="order.id" 
                     class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                    <div class="p-6">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <!-- Order Info -->
                            <div class="flex-1">
                                <div class="flex items-center gap-4 mb-3">
                                    <h3 class="text-lg font-semibold text-gray-900">Mã đơn: {{ order.order_number }}</h3>
                                    <span :class="getStatusBadgeClass(order.status)" 
                                          class="px-3 py-1 rounded-full text-xs font-medium">
                                        {{ getStatusText(order.status) }}
                                    </span>
                                </div>
                                
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-sm text-gray-600">
                                    <div>
                                        <span class="font-medium text-gray-700">Ngày đặt:</span>
                                        <p>{{ formatDate(order.created_at) }}</p>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-700">Thanh toán:</span>
                                        <p :class="getPaymentStatusClass(order.payment_status)">
                                            {{ getPaymentStatusText(order.payment_status) }}
                                        </p>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-700">Số sản phẩm:</span>
                                        <p>{{ order.items_count || order.items?.length || 0 }} sản phẩm</p>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-700">Tổng tiền:</span>
                                        <p class="text-lg font-bold text-blue-600">{{ formatCurrency(order.total) }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex gap-2">
                                <Link :href="route('user.orders.show', order.id)" 
                                      class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-300 font-medium">
                                    Xem chi tiết
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="orders.links && orders.links.length > 3" class="mt-6 flex justify-center">
                <div class="flex gap-2">
                    <Link
                        v-for="(link, idx) in orders.links"
                        :key="idx"
                        :href="link.url || '#'"
                        :class="[
                            'px-4 py-2 rounded-lg border transition duration-300',
                            link.active 
                                ? 'bg-blue-600 text-white border-blue-600' 
                                : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                        ]"
                        v-html="link.label"
                        :preserve-state="true"
                        :preserve-scroll="true"
                    />
                </div>
            </div>
        </div>
    </UserLayout>
</template>

<script setup>
import { reactive } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import UserLayout from '@/Layouts/User/UserLayout.vue'

const props = defineProps({
    orders: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) }
})

const localFilters = reactive({
    status: props.filters?.status || ''
})

const applyFilter = () => {
    router.get(route('user.orders.index'), { status: localFilters.status }, { preserveState: true, preserveScroll: true })
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
    if (status === 'paid') return 'text-green-600 font-semibold'
    if (status === 'failed') return 'text-red-600 font-semibold'
    if (status === 'refunded') return 'text-orange-600 font-semibold'
    return 'text-yellow-600 font-semibold'
}
</script>
