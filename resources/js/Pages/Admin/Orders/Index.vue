<template>
    <AdminLayout>
        <div class="space-y-6">
            <!-- Page Header -->
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Quản lý đơn hàng</h1>
                <p class="mt-2 text-sm text-gray-600">Theo dõi và quản lý đơn hàng</p>
            </div>

            <!-- Statistics Cards -->
            <div v-if="orders.data" class="grid grid-cols-1 gap-4 md:grid-cols-5">
                <div class="rounded-lg border border-gray-100 bg-white p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Tổng số đơn hàng</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">{{ orders.total || orders.data.length }}</p>
                </div>
                <div class="rounded-lg border border-yellow-100 bg-yellow-50 p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Chờ xử lý</p>
                    <p class="mt-2 text-2xl font-bold text-yellow-600">
                        {{ orders.data.filter(o => o.status === 'pending').length }}
                    </p>
                </div>
                <div class="rounded-lg border border-blue-100 bg-blue-50 p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Đang xử lý</p>
                    <p class="mt-2 text-2xl font-bold text-blue-600">
                        {{ orders.data.filter(o => o.status === 'processing').length }}
                    </p>
                </div>
                <div class="rounded-lg border border-green-100 bg-green-50 p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Đã giao hàng</p>
                    <p class="mt-2 text-2xl font-bold text-green-600">
                        {{ orders.data.filter(o => o.status === 'delivered').length }}
                    </p>
                </div>
                <div class="rounded-lg border border-orange-100 bg-orange-50 p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Tổng doanh thu</p>
                    <p class="mt-2 text-lg font-bold text-orange-600">
                        {{ formatTotalRevenue(orders.data) }}
                    </p>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
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
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mã đơn</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Khách hàng</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thanh toán</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Tổng tiền</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày đặt</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="order in orders.data" :key="order.id" class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-medium text-gray-900">{{ order.order_number }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm">
                                        <div class="font-medium text-gray-900">{{ order.user?.name || order.shipping_name }}</div>
                                        <div class="text-gray-500">{{ order.shipping_phone }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="getStatusBadgeClass(order.status)" 
                                          class="px-2 py-1 rounded-full text-xs font-semibold">
                                        {{ getStatusText(order.status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="getPaymentStatusBadgeClass(order.payment_status)" 
                                          class="px-2 py-1 rounded-full text-xs font-semibold">
                                        {{ getPaymentStatusText(order.payment_status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <span class="text-sm font-semibold text-gray-900">{{ formatCurrency(order.total) }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-600">{{ formatDate(order.created_at) }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <Link :href="route('admin.orders.show', order.id)" 
                                          class="text-teal-600 hover:text-teal-900">
                                        Xem chi tiết
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="orders.data.length === 0">
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    Không có đơn hàng nào
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="orders.last_page > 1" class="bg-white rounded-lg shadow-sm border border-gray-200 px-6 py-4">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="text-sm text-gray-600">
                        Hiển thị {{ orders.from || 0 }} - {{ orders.to || 0 }} trong {{ orders.total || 0 }} đơn hàng
                    </div>
                    <div class="flex gap-2">
                        <Link
                            :href="orders.first_page_url || '#'"
                            :class="[
                                'px-3 py-2 rounded-lg border border-gray-300 transition hover:bg-gray-50',
                                !orders.prev_page_url ? 'opacity-50 cursor-not-allowed' : ''
                            ]"
                            :preserve-state="true"
                            :preserve-scroll="true"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                            </svg>
                        </Link>
                        <Link
                            :href="orders.prev_page_url || '#'"
                            :class="[
                                'px-4 py-2 rounded-lg border border-gray-300 transition hover:bg-gray-50',
                                !orders.prev_page_url ? 'opacity-50 cursor-not-allowed' : ''
                            ]"
                            :preserve-state="true"
                            :preserve-scroll="true"
                        >
                            Trước
                        </Link>
                        
                        <template v-for="page in getPageNumbers(orders)" :key="page">
                            <Link
                                v-if="page !== '...'"
                                :href="getPageUrl(page, orders)"
                                :class="[
                                    'px-4 py-2 rounded-lg border transition',
                                    page === orders.current_page
                                        ? 'bg-teal-600 text-white border-teal-600'
                                        : 'border-gray-300 hover:bg-gray-50'
                                ]"
                                :preserve-state="true"
                                :preserve-scroll="true"
                            >
                                {{ page }}
                            </Link>
                            <span v-else class="px-2 py-2 text-gray-500">...</span>
                        </template>
                        
                        <Link
                            :href="orders.next_page_url || '#'"
                            :class="[
                                'px-4 py-2 rounded-lg border border-gray-300 transition hover:bg-gray-50',
                                !orders.next_page_url ? 'opacity-50 cursor-not-allowed' : ''
                            ]"
                            :preserve-state="true"
                            :preserve-scroll="true"
                        >
                            Sau
                        </Link>
                        <Link
                            :href="orders.last_page_url || '#'"
                            :class="[
                                'px-3 py-2 rounded-lg border border-gray-300 transition hover:bg-gray-50',
                                !orders.next_page_url ? 'opacity-50 cursor-not-allowed' : ''
                            ]"
                            :preserve-state="true"
                            :preserve-scroll="true"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                            </svg>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { reactive } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

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

const formatTotalRevenue = (orders) => {
    if (!orders || !Array.isArray(orders)) return formatCurrency(0)
    const total = orders
        .filter(o => o.status !== 'cancelled')
        .reduce((sum, o) => sum + (Number(o.total) || 0), 0)
    return formatCurrency(total)
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

const getPageNumbers = (orders) => {
    if (!orders || !orders.last_page) return []
    const current = orders.current_page || 1
    const last = orders.last_page || 1
    const pages = []
    
    if (last <= 7) {
        for (let i = 1; i <= last; i++) {
            pages.push(i)
        }
    } else {
        if (current <= 3) {
            for (let i = 1; i <= 5; i++) pages.push(i)
            pages.push('...')
            pages.push(last)
        } else if (current >= last - 2) {
            pages.push(1)
            pages.push('...')
            for (let i = last - 4; i <= last; i++) pages.push(i)
        } else {
            pages.push(1)
            pages.push('...')
            for (let i = current - 1; i <= current + 1; i++) pages.push(i)
            pages.push('...')
            pages.push(last)
        }
    }
    return pages
}

const getQueryString = () => {
    const params = new URLSearchParams()
    if (local.q) params.append('q', local.q)
    if (local.status) params.append('status', local.status)
    if (local.payment_status) params.append('payment_status', local.payment_status)
    const query = params.toString()
    return query ? '&' + query : ''
}

const getPageUrl = (page, orders) => {
    const basePath = orders.path || route('admin.orders.index')
    const query = getQueryString()
    const separator = basePath.includes('?') ? '&' : '?'
    return `${basePath}${separator}page=${page}${query}`
}
</script>
