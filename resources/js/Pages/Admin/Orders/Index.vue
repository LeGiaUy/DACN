<template>
    <div>
        <Menu></Menu>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <h1 class="text-2xl font-semibold mb-6 text-center">Quản lý đơn hàng</h1>

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <form @submit.prevent="applyFilters" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tìm kiếm</label>
                        <input v-model="local.q" 
                               type="text" 
                               placeholder="Mã đơn, tên khách..." 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái đơn</label>
                        <select v-model="local.status" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                            <option value="">Tất cả</option>
                            <option value="pending">Chờ xử lý</option>
                            <option value="processing">Đang xử lý</option>
                            <option value="shipped">Đã gửi hàng</option>
                            <option value="delivered">Đã giao hàng</option>
                            <option value="cancelled">Đã hủy</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái thanh toán</label>
                        <select v-model="local.payment_status" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                            <option value="">Tất cả</option>
                            <option value="pending">Chờ thanh toán</option>
                            <option value="paid">Đã thanh toán</option>
                            <option value="failed">Thất bại</option>
                            <option value="refunded">Hoàn tiền</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" 
                                class="w-full px-4 py-2 bg-teal-500 text-white rounded-lg hover:bg-teal-600 transition duration-300 font-medium">
                            Lọc
                        </button>
                    </div>
                </form>
            </div>

            <!-- Orders Table -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="table-auto w-full border-collapse border border-gray-200">
                        <thead>
                            <tr class="bg-gray-100 text-left">
                                <th class="border border-gray-300 px-4 py-3 text-sm font-semibold text-gray-700">Mã đơn</th>
                                <th class="border border-gray-300 px-4 py-3 text-sm font-semibold text-gray-700">Khách hàng</th>
                                <th class="border border-gray-300 px-4 py-3 text-sm font-semibold text-gray-700">Trạng thái</th>
                                <th class="border border-gray-300 px-4 py-3 text-sm font-semibold text-gray-700">Thanh toán</th>
                                <th class="border border-gray-300 px-4 py-3 text-sm font-semibold text-gray-700 text-right">Tổng tiền</th>
                                <th class="border border-gray-300 px-4 py-3 text-sm font-semibold text-gray-700">Ngày đặt</th>
                                <th class="border border-gray-300 px-4 py-3 text-sm font-semibold text-gray-700 text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="order in orders.data" :key="order.id" class="hover:bg-gray-50">
                                <td class="border border-gray-300 px-4 py-3">
                                    <span class="font-medium text-gray-900">{{ order.order_number }}</span>
                                </td>
                                <td class="border border-gray-300 px-4 py-3">
                                    <div class="text-sm">
                                        <div class="font-medium text-gray-900">{{ order.user?.name || order.shipping_name }}</div>
                                        <div class="text-gray-500">{{ order.shipping_phone }}</div>
                                    </div>
                                </td>
                                <td class="border border-gray-300 px-4 py-3">
                                    <span :class="getStatusBadgeClass(order.status)" 
                                          class="px-2 py-1 rounded text-xs font-medium">
                                        {{ getStatusText(order.status) }}
                                    </span>
                                </td>
                                <td class="border border-gray-300 px-4 py-3">
                                    <span :class="getPaymentStatusBadgeClass(order.payment_status)" 
                                          class="px-2 py-1 rounded text-xs font-medium">
                                        {{ getPaymentStatusText(order.payment_status) }}
                                    </span>
                                </td>
                                <td class="border border-gray-300 px-4 py-3 text-right">
                                    <span class="font-semibold text-gray-900">{{ formatCurrency(order.total) }}</span>
                                </td>
                                <td class="border border-gray-300 px-4 py-3">
                                    <span class="text-sm text-gray-600">{{ formatDate(order.created_at) }}</span>
                                </td>
                                <td class="border border-gray-300 px-4 py-3 text-center">
                                    <Link :href="route('admin.orders.show', order.id)" 
                                          class="text-teal-500 hover:text-teal-700 font-medium">
                                        Xem
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="orders.data.length === 0">
                                <td colspan="7" class="border border-gray-300 px-4 py-8 text-center text-gray-500">
                                    Không có đơn hàng nào
                                </td>
                            </tr>
                        </tbody>
                    </table>
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
                                ? 'bg-teal-500 text-white border-teal-500' 
                                : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                        ]"
                        v-html="link.label"
                        :preserve-state="true"
                        :preserve-scroll="true"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { reactive } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import Menu from '../../Includes/Menu.vue'

const props = defineProps({
    orders: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) }
})

const local = reactive({
    q: props.filters?.q || '',
    status: props.filters?.status || '',
    payment_status: props.filters?.payment_status || ''
})

const applyFilters = () => {
    router.get(route('admin.orders.index'), { ...local }, { preserveState: true, preserveScroll: true })
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
</script>
