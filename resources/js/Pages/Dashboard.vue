<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({}),
    },
    charts: {
        type: Object,
        default: () => ({
            monthly: [],
            status_breakdown: {},
        }),
    },
});

const page = usePage();

const selectedRange = ref('monthly'); // 'daily' | 'monthly' | 'yearly'

const formattedRevenue = computed(() => {
    const total = props.stats?.total_revenue ?? 0;
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND',
        maximumFractionDigits: 0,
    }).format(total);
});

const formattedRevenueToday = computed(() => {
    const total = props.stats?.revenue_today ?? 0;
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND',
        maximumFractionDigits: 0,
    }).format(total);
});

const formattedRevenueThisMonth = computed(() => {
    const total = props.stats?.revenue_this_month ?? 0;
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND',
        maximumFractionDigits: 0,
    }).format(total);
});

const formattedRevenueThisYear = computed(() => {
    const total = props.stats?.revenue_this_year ?? 0;
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND',
        maximumFractionDigits: 0,
    }).format(total);
});

const revenueChart = computed(() => {
    const charts = props.charts || {};
    return charts[selectedRange.value] || [];
});

const maxRevenue = computed(() => {
    const values = revenueChart.value.map((p) => p.revenue || 0);
    const max = Math.max(...values, 0);
    return max > 0 ? max : 1;
});

const statusBreakdown = computed(() => props.charts?.status_breakdown ?? {});

const totalOrdersForStatus = computed(() =>
    Object.values(statusBreakdown.value).reduce((sum, v) => sum + (v || 0), 0),
);

const statusDefinition = [
    { key: 'pending', label: 'Chờ xử lý', color: 'bg-amber-500' },
    { key: 'processing', label: 'Đang xử lý', color: 'bg-blue-500' },
    { key: 'delivered', label: 'Đã giao', color: 'bg-emerald-500' },
    { key: 'cancelled', label: 'Đã hủy', color: 'bg-rose-500' },
];

const getStatusPercent = (key) => {
    const total = totalOrdersForStatus.value || 1;
    const value = statusBreakdown.value[key] || 0;
    return Math.round((value / total) * 100);
};

const handleExportPdf = () => {
    window.open('/admin/dashboard/export-pdf', '_blank');
};

const rangeMeta = {
    daily: {
        title: 'Doanh thu 7 ngày gần nhất',
        subtitle: 'Đơn vị: VND (theo ngày)',
    },
    monthly: {
        title: 'Doanh thu 6 tháng gần nhất',
        subtitle: 'Đơn vị: VND (theo tháng)',
    },
    yearly: {
        title: 'Doanh thu 5 năm gần nhất',
        subtitle: 'Đơn vị: VND (theo năm)',
    },
};
</script>

<template>
    <Head title="Admin Dashboard" />
    <AdminLayout>
        <div class="space-y-6">
            <!-- Page Header -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                        Bảng điều khiển
                        <span class="inline-flex items-center rounded-full bg-teal-50 px-3 py-1 text-xs font-medium text-teal-700">
                            Admin
                        </span>
                    </h1>
                    <p class="mt-2 text-sm text-gray-600">
                        Tổng quan số liệu bán hàng, đơn hàng và người dùng.
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        @click="handleExportPdf"
                        class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50"
                    >
                        <svg
                            class="mr-2 h-4 w-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 4v12m0 0l-4-4m4 4l4-4M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2"
                            />
                        </svg>
                        Xuất PDF
                    </button>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4">
                <!-- Revenue -->
                <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Tổng doanh thu</p>
                            <p class="mt-2 text-3xl font-semibold text-gray-900">
                                {{ formattedRevenue }}
                            </p>
                        </div>
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600"
                        >
                            <svg
                                class="h-6 w-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V6m0 10v2m9-10a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Orders -->
                <Link
                    :href="route('admin.orders.index')"
                    class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm hover:shadow-md transition-shadow"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Tổng đơn hàng</p>
                            <p class="mt-2 text-3xl font-semibold text-gray-900">
                                {{ stats?.total_orders ?? 0 }}
                            </p>
                        </div>
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600"
                        >
                            <svg
                                class="h-6 w-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 17v2a2 2 0 002 2h2a2 2 0 002-2v-2M9 8h10l-1 9H10L9 8zm0 0H5a2 2 0 00-2 2v5h4"
                                />
                            </svg>
                        </div>
                    </div>
                </Link>

                <!-- Products -->
                <Link
                    :href="route('admin.products.index')"
                    class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm hover:shadow-md transition-shadow"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Sản phẩm</p>
                            <p class="mt-2 text-3xl font-semibold text-gray-900">
                                {{ stats?.total_products ?? 0 }}
                            </p>
                            <p class="mt-1 text-xs text-gray-500">
                                {{ stats?.total_categories ?? 0 }} danh mục ·
                                {{ stats?.total_brands ?? 0 }} thương hiệu
                            </p>
                        </div>
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-50 text-purple-600"
                        >
                            <svg
                                class="h-6 w-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                                />
                            </svg>
                        </div>
                    </div>
                </Link>

                <!-- Users -->
                <Link
                    :href="route('admin.users.index')"
                    class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm hover:shadow-md transition-shadow"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Người dùng</p>
                            <p class="mt-2 text-3xl font-semibold text-gray-900">
                                {{ stats?.total_users ?? 0 }}
                            </p>
                        </div>
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-xl bg-sky-50 text-sky-600"
                        >
                            <svg
                                class="h-6 w-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87M12 12a4 4 0 100-8 4 4 0 000 8zm0 0a4 4 0 014 4v1"
                                />
                            </svg>
                        </div>
                    </div>
                </Link>
            </div>

            <!-- Charts -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Revenue chart (bar chart style) -->
                <div class="lg:col-span-2 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                    <div class="mb-4 flex items-center justify-between gap-4">
                        <div>
                            <h2 class="text-base font-semibold text-gray-900">
                                {{ rangeMeta[selectedRange].title }}
                            </h2>
                            <p class="mt-1 text-xs text-gray-500">
                                {{ rangeMeta[selectedRange].subtitle }}
                            </p>
                        </div>
                        <div class="inline-flex rounded-full bg-gray-100 p-1 text-xs font-medium text-gray-600">
                            <button
                                type="button"
                                class="px-3 py-1 rounded-full transition-colors"
                                :class="selectedRange === 'daily' ? 'bg-white shadow-sm text-gray-900' : ''"
                                @click="selectedRange = 'daily'"
                            >
                                Ngày
                            </button>
                            <button
                                type="button"
                                class="px-3 py-1 rounded-full transition-colors"
                                :class="selectedRange === 'monthly' ? 'bg-white shadow-sm text-gray-900' : ''"
                                @click="selectedRange = 'monthly'"
                            >
                                Tháng
                            </button>
                            <button
                                type="button"
                                class="px-3 py-1 rounded-full transition-colors"
                                :class="selectedRange === 'yearly' ? 'bg-white shadow-sm text-gray-900' : ''"
                                @click="selectedRange = 'yearly'"
                            >
                                Năm
                            </button>
                        </div>
                    </div>
                    <div class="mt-4 h-56">
                        <div
                            v-if="revenueChart.length"
                            class="flex h-full items-end justify-between gap-3"
                        >
                            <div
                                v-for="point in revenueChart"
                                :key="point.key"
                                class="flex flex-1 flex-col items-center justify-end gap-2"
                            >
                                <div class="relative flex h-40 w-full items-end">
                                    <div
                                        class="w-full rounded-t-xl bg-gradient-to-t from-teal-500 to-emerald-400 shadow-sm"
                                        :style="{
                                            height: `${Math.max(
                                                (point.revenue || 0) / maxRevenue * 100,
                                                4,
                                            ).toFixed(0)}%`,
                                        }"
                                    ></div>
                                </div>
                                <div class="text-[11px] text-gray-500">
                                    {{ point.label }}
                                </div>
                                <div class="text-[11px] font-medium text-gray-700">
                                    {{
                                        new Intl.NumberFormat('vi-VN', {
                                            maximumFractionDigits: 0,
                                        }).format(point.revenue || 0)
                                    }}
                                </div>
                            </div>
                        </div>
                        <div
                            v-else
                            class="flex h-full items-center justify-center text-sm text-gray-500"
                        >
                            Chưa có dữ liệu doanh thu.
                        </div>
                    </div>
                </div>

                <!-- Order status distribution -->
                <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                    <h2 class="text-base font-semibold text-gray-900">
                        Trạng thái đơn hàng
                    </h2>
                    <p class="mt-1 text-xs text-gray-500">
                        Phân bố theo trạng thái gần đây.
                    </p>

                    <div class="mt-4 space-y-3">
                        <div
                            v-for="s in statusDefinition"
                            :key="s.key"
                            class="space-y-1"
                        >
                            <div class="flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2">
                                    <span :class="['h-2 w-2 rounded-full', s.color]"></span>
                                    <span class="font-medium text-gray-700">
                                        {{ s.label }}
                                    </span>
                                </div>
                                <span class="text-gray-500">
                                    {{ statusBreakdown[s.key] || 0 }} đơn
                                </span>
                            </div>
                            <div class="h-2 w-full rounded-full bg-gray-100">
                                <div
                                    class="h-2 rounded-full"
                                    :class="s.color"
                                    :style="{ width: `${getStatusPercent(s.key)}%` }"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Thao tác nhanh</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <Link
                        :href="route('admin.categories.index')"
                        class="flex items-center space-x-3 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition"
                    >
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg
                                class="w-5 h-5 text-blue-600"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 4v16m8-8H4"
                                />
                            </svg>
                        </div>
                        <span class="font-medium text-gray-900">Thêm danh mục</span>
                    </Link>
                    <Link
                        :href="route('admin.brands.index')"
                        class="flex items-center space-x-3 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition"
                    >
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg
                                class="w-5 h-5 text-green-600"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 4v16m8-8H4"
                                />
                            </svg>
                        </div>
                        <span class="font-medium text-gray-900">Thêm thương hiệu</span>
                    </Link>
                    <Link
                        :href="route('admin.products.index')"
                        class="flex items-center space-x-3 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition"
                    >
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg
                                class="w-5 h-5 text-purple-600"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 4v16m8-8H4"
                                />
                            </svg>
                        </div>
                        <span class="font-medium text-gray-900">Thêm sản phẩm</span>
                    </Link>
                    <Link
                        :href="route('admin.users.index')"
                        class="flex items-center space-x-3 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition"
                    >
                        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <svg
                                class="w-5 h-5 text-indigo-600"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 4v16m8-8H4"
                                />
                            </svg>
                        </div>
                        <span class="font-medium text-gray-900">Thêm người dùng</span>
                    </Link>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
