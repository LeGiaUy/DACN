<template>
    <AdminLayout>
        <div class="space-y-6">
            <!-- Page Header -->
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Quản lý biến thể sản phẩm</h1>
                    <p class="mt-2 text-sm text-gray-600">Theo dõi tồn kho chi tiết theo màu sắc, kích thước và SKU</p>
                </div>
                <div class="flex flex-wrap items-center gap-3 text-sm text-gray-500">
                    <span>Hiển thị {{ variants.data?.length || 0 }} / {{ variants.total || 0 }} biến thể</span>
                    <button
                        @click="openModal(null)"
                        class="inline-flex items-center space-x-2 rounded-lg bg-teal-600 px-4 py-2 text-white shadow-sm transition hover:bg-teal-700"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Thêm biến thể</span>
                    </button>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-5">
                <div class="rounded-lg border border-gray-100 bg-white p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Tổng biến thể</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">{{ statistics.total || 0 }}</p>
                </div>
                <div class="rounded-lg border border-green-100 bg-green-50 p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Còn hàng</p>
                    <p class="mt-2 text-2xl font-bold text-green-600">{{ statistics.in_stock || 0 }}</p>
                </div>
                <div class="rounded-lg border border-yellow-100 bg-yellow-50 p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Sắp hết</p>
                    <p class="mt-2 text-2xl font-bold text-yellow-600">{{ statistics.low_stock || 0 }}</p>
                </div>
                <div class="rounded-lg border border-red-100 bg-red-50 p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Hết hàng</p>
                    <p class="mt-2 text-2xl font-bold text-red-600">{{ statistics.out_of_stock || 0 }}</p>
                </div>
                <div class="rounded-lg border border-blue-100 bg-blue-50 p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Tổng số lượng</p>
                    <p class="mt-2 text-2xl font-bold text-blue-600">{{ statistics.total_quantity || 0 }}</p>
                </div>
            </div>

            <!-- Filters -->
            <div class="rounded-lg border border-gray-100 bg-white p-6 shadow-sm">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tìm kiếm</label>
                        <input
                            type="text"
                            v-model="filters.search"
                            @input="applyFilters"
                            placeholder="Tên sản phẩm, SKU..."
                            class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Sản phẩm</label>
                        <select
                            v-model="filters.product_id"
                            @change="applyFilters"
                            class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                        >
                            <option value="">Tất cả sản phẩm</option>
                            <option v-for="product in products" :key="product.id" :value="product.id">
                                {{ product.name }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Màu sắc</label>
                        <input
                            type="text"
                            v-model="filters.color"
                            @input="applyFilters"
                            placeholder="Lọc theo màu..."
                            class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kích thước</label>
                        <input
                            type="text"
                            v-model="filters.size"
                            @input="applyFilters"
                            placeholder="Lọc theo size..."
                            class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                        />
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-4">
                    <label class="flex items-center space-x-2 text-sm text-gray-700">
                        <input type="checkbox" v-model="filters.low_stock" @change="applyFilters" class="rounded text-teal-600 focus:ring-teal-500" />
                        <span>Sắp hết hàng (&lt; 10)</span>
                    </label>
                    <label class="flex items-center space-x-2 text-sm text-gray-700">
                        <input type="checkbox" v-model="filters.out_of_stock" @change="applyFilters" class="rounded text-teal-600 focus:ring-teal-500" />
                        <span>Hết hàng</span>
                    </label>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Sắp xếp</label>
                        <select
                            v-model="filters.sort_by"
                            @change="applyFilters"
                            class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                        >
                            <option value="id">ID</option>
                            <option value="product_id">Sản phẩm</option>
                            <option value="quantity">Số lượng</option>
                            <option value="color">Màu sắc</option>
                            <option value="size">Kích thước</option>
                            <option value="created_at">Ngày tạo</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Thứ tự</label>
                        <select
                            v-model="filters.sort_order"
                            @change="applyFilters"
                            class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                        >
                            <option value="desc">Giảm dần</option>
                            <option value="asc">Tăng dần</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4 flex justify-end">
                    <button
                        @click="resetFilters"
                        class="inline-flex items-center space-x-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span>Đặt lại bộ lọc</span>
                    </button>
                </div>
            </div>

            <!-- Loading State -->
            <div v-if="loading" class="flex flex-col items-center justify-center rounded-lg border border-gray-100 bg-white py-16 shadow-sm">
                <div class="h-10 w-10 animate-spin rounded-full border-4 border-gray-200 border-t-teal-500"></div>
                <p class="mt-4 text-sm text-gray-500">Đang tải biến thể...</p>
            </div>

            <!-- Variants Table -->
            <div v-else class="rounded-lg border border-gray-100 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Sản phẩm</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Màu sắc</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Kích thước</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">SKU</th>
                                <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Số lượng</th>
                                <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Trạng thái</th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="variant in variants.data" :key="variant.id" :class="getVariantRowClass(variant)" class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">#{{ variant.id }}</td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ variant.product?.name }}</div>
                                    <div class="text-xs text-gray-500">
                                        {{ variant.product?.category?.name || 'Không rõ danh mục' }} •
                                        {{ variant.product?.brand?.name || 'Không rõ thương hiệu' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ variant.color || '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ variant.size || '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ variant.sku || '-' }}</td>
                                <td class="px-6 py-4 text-center text-sm font-semibold">
                                    <span :class="getQuantityClass(variant.quantity)">{{ variant.quantity }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span :class="getStatusClass(variant.quantity)" class="inline-flex rounded-full px-3 py-1 text-xs font-semibold">
                                        {{ getStatusText(variant.quantity) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium">
                                    <div class="flex justify-end gap-3">
                                        <button @click="openModal(variant)" class="text-teal-600 hover:text-teal-800">Sửa</button>
                                        <button @click="deleteVariant(variant.id)" class="text-red-600 hover:text-red-800">Xóa</button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!variants.data || variants.data.length === 0">
                                <td colspan="8" class="px-6 py-12 text-center text-sm text-gray-500">
                                    Không có biến thể nào phù hợp với bộ lọc hiện tại
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="!loading && variants" class="flex flex-wrap items-center justify-between gap-4 border-t border-gray-100 px-6 py-4 bg-gray-50">
                    <div class="text-sm text-gray-600">
                        <template v-if="variants.total !== undefined">
                            Hiển thị {{ variants.from || ((variants.current_page - 1) * (variants.per_page || perPage) + 1) || 1 }} - {{ variants.to || Math.min(variants.current_page * (variants.per_page || perPage), variants.total) }} trong {{ variants.total }} biến thể
                        </template>
                        <template v-else-if="variants.data && variants.data.length">
                            Hiển thị {{ variants.data.length }} biến thể
                        </template>
                        <template v-else>
                            Không có dữ liệu
                        </template>
                    </div>
                    <div v-if="variants.last_page && variants.last_page > 1" class="flex gap-2 flex-wrap">
                        <button
                            class="px-3 py-2 rounded-lg border border-gray-300 transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-40"
                            :disabled="variants.current_page === 1"
                            @click="goToPage(1)"
                            title="Trang đầu"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                            </svg>
                        </button>
                        <button
                            class="px-4 py-2 rounded-lg border border-gray-300 transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-40"
                            :disabled="variants.current_page === 1"
                            @click="goToPage(variants.current_page - 1)"
                        >
                            Trước
                        </button>
                        <template v-for="page in getPageNumbers()" :key="page">
                            <button
                                v-if="page !== '...'"
                                class="px-4 py-2 rounded-lg border transition"
                                :class="page === variants.current_page 
                                    ? 'bg-teal-600 text-white border-teal-600' 
                                    : 'border-gray-300 hover:bg-gray-50'"
                                @click="goToPage(page)"
                            >
                                {{ page }}
                            </button>
                            <span v-else class="px-2 py-2 text-gray-500">...</span>
                        </template>
                        <button
                            class="px-4 py-2 rounded-lg border border-gray-300 transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-40"
                            :disabled="variants.current_page === variants.last_page"
                            @click="goToPage(variants.current_page + 1)"
                        >
                            Sau
                        </button>
                        <button
                            class="px-3 py-2 rounded-lg border border-gray-300 transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-40"
                            :disabled="variants.current_page === variants.last_page"
                            @click="goToPage(variants.last_page)"
                            title="Trang cuối"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal for Add/Edit -->
            <div
                v-if="isModalOpen"
                class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50 px-4 py-8"
                @click.self="closeModal"
            >
                <div class="w-full max-w-2xl rounded-xl bg-white shadow-xl">
                    <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">
                                {{ isEditing ? 'Sửa biến thể' : 'Thêm biến thể mới' }}
                            </h2>
                            <p class="text-sm text-gray-500">Điền thông tin chi tiết cho biến thể</p>
                        </div>
                        <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="handleSubmit" class="max-h-[70vh] space-y-4 overflow-y-auto px-6 py-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Sản phẩm *</label>
                            <select
                                v-model="form.product_id"
                                required
                                class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                            >
                                <option value="">Chọn sản phẩm</option>
                                <option v-for="product in products" :key="product.id" :value="product.id">
                                    {{ product.name }}
                                </option>
                            </select>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Màu sắc</label>
                                <input
                                    type="text"
                                    v-model="form.color"
                                    placeholder="VD: Đỏ, Xanh..."
                                    class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Kích thước</label>
                                <input
                                    type="text"
                                    v-model="form.size"
                                    placeholder="VD: S, M, L..."
                                    class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                                />
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">SKU</label>
                            <input
                                type="text"
                                v-model="form.sku"
                                placeholder="Mã SKU (tùy chọn)"
                                class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Số lượng *</label>
                            <input
                                type="number"
                                v-model.number="form.quantity"
                                min="0"
                                required
                                class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                            />
                        </div>

                        <div v-if="formError" class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                            {{ formError }}
                        </div>
                    </form>

                    <div class="flex items-center justify-end gap-3 border-t border-gray-100 px-6 py-4">
                        <button
                            type="button"
                            @click="closeModal"
                            class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                        >
                            Hủy
                        </button>
                        <button
                            type="submit"
                            @click="handleSubmit"
                            :disabled="saving"
                            class="inline-flex items-center space-x-2 rounded-lg bg-teal-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-teal-700 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            <svg v-if="saving" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                            </svg>
                            <span>{{ saving ? 'Đang lưu...' : (isEditing ? 'Cập nhật biến thể' : 'Thêm biến thể') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script>
import axios from 'axios';
import AdminLayout from '@/Layouts/AdminLayout.vue';

export default {
    components: {
        AdminLayout
    },
    data() {
        return {
            variants: { data: [] },
            products: [],
            statistics: {},
            loading: true,
            saving: false,
            isModalOpen: false,
            isEditing: false,
            formError: null,
            form: {
                id: null,
                product_id: '',
                color: '',
                size: '',
                sku: '',
                quantity: 0,
            },
            filters: {
                search: '',
                product_id: '',
                color: '',
                size: '',
                sku: '',
                low_stock: false,
                out_of_stock: false,
                sort_by: 'id',
                sort_order: 'desc',
            },
            page: 1,
            perPage: 15,
        }
    },
    mounted() {
        this.fetchVariants();
        this.fetchProducts();
        this.fetchStatistics();
    },
    methods: {
        async fetchVariants() {
            this.loading = true;
            try {
                const params = {
                    page: this.page,
                    per_page: this.perPage,
                    ...this.filters
                };
                Object.keys(params).forEach(key => {
                    if (params[key] === '' || params[key] === false) {
                        delete params[key];
                    }
                });

                const response = await axios.get("http://127.0.0.1:8000/api/product-variants", { params });
                this.variants = response.data;
                
                // Đảm bảo có các thuộc tính pagination từ Laravel
                if (this.variants.data && Array.isArray(this.variants.data)) {
                    // Tính toán last_page nếu chưa có
                    if (this.variants.total !== undefined && this.variants.total > 0) {
                        const perPage = this.variants.per_page || parseInt(response.data.per_page) || this.perPage;
                        this.variants.last_page = this.variants.last_page || Math.ceil(this.variants.total / perPage);
                    } else {
                        // Nếu không có total, tính từ data length
                        this.variants.last_page = this.variants.last_page || 1;
                    }
                    
                    // Đảm bảo current_page có giá trị
                    this.variants.current_page = parseInt(this.variants.current_page) || parseInt(response.data.current_page) || this.page || 1;
                    
                    // Đảm bảo per_page có giá trị
                    this.variants.per_page = parseInt(this.variants.per_page) || parseInt(response.data.per_page) || this.perPage;
                    
                    // Tính from và to nếu chưa có
                    if (this.variants.total !== undefined) {
                        const perPage = this.variants.per_page;
                        this.variants.from = this.variants.from || ((this.variants.current_page - 1) * perPage + 1);
                        this.variants.to = this.variants.to || Math.min(this.variants.current_page * perPage, this.variants.total);
                    }
                }
            } catch (error) {
                console.error("Error fetching variants:", error);
                alert('Lỗi khi tải danh sách biến thể');
            } finally {
                this.loading = false;
            }
        },
        async fetchProducts() {
            try {
                const response = await axios.get("http://127.0.0.1:8000/api/products", { params: { per_page: 1000 } });
                this.products = response.data.data || [];
            } catch (error) {
                console.error("Error fetching products:", error);
            }
        },
        async fetchStatistics() {
            try {
                const response = await axios.get("http://127.0.0.1:8000/api/product-variants/statistics");
                this.statistics = response.data;
            } catch (error) {
                console.error("Error fetching statistics:", error);
            }
        },
        applyFilters() {
            this.page = 1;
            this.fetchVariants();
        },
        resetFilters() {
            this.filters = {
                search: '',
                product_id: '',
                color: '',
                size: '',
                sku: '',
                low_stock: false,
                out_of_stock: false,
                sort_by: 'id',
                sort_order: 'desc',
            };
            this.page = 1;
            this.fetchVariants();
        },
        goToPage(page) {
            if (page >= 1 && page <= this.variants.last_page) {
                this.page = page;
                this.fetchVariants();
            }
        },
        getPageNumbers() {
            const current = this.variants.current_page || 1;
            const last = this.variants.last_page || 1;
            const pages = [];
            
            if (last <= 7) {
                // Nếu tổng số trang <= 7, hiển thị tất cả
                for (let i = 1; i <= last; i++) {
                    pages.push(i);
                }
            } else {
                // Logic hiển thị thông minh với dấu ...
                if (current <= 3) {
                    // Ở đầu: 1 2 3 4 5 ... last
                    for (let i = 1; i <= 5; i++) pages.push(i);
                    pages.push('...');
                    pages.push(last);
                } else if (current >= last - 2) {
                    // Ở cuối: 1 ... last-4 last-3 last-2 last-1 last
                    pages.push(1);
                    pages.push('...');
                    for (let i = last - 4; i <= last; i++) pages.push(i);
                } else {
                    // Ở giữa: 1 ... current-1 current current+1 ... last
                    pages.push(1);
                    pages.push('...');
                    for (let i = current - 1; i <= current + 1; i++) pages.push(i);
                    pages.push('...');
                    pages.push(last);
                }
            }
            return pages;
        },
        getVariantRowClass(variant) {
            if (variant.quantity <= 0) return 'bg-red-50';
            if (variant.quantity < 10) return 'bg-yellow-50';
            return '';
        },
        getQuantityClass(quantity) {
            if (quantity <= 0) return 'text-red-600 font-bold';
            if (quantity < 10) return 'text-yellow-600 font-bold';
            return 'text-green-600';
        },
        getStatusClass(quantity) {
            if (quantity <= 0) return 'bg-red-100 text-red-800';
            if (quantity < 10) return 'bg-yellow-100 text-yellow-800';
            return 'bg-green-100 text-green-800';
        },
        getStatusText(quantity) {
            if (quantity <= 0) return 'Hết hàng';
            if (quantity < 10) return 'Sắp hết';
            return 'Còn hàng';
        },
        openModal(variant) {
            if (variant) {
                this.isEditing = true;
                this.form = {
                    id: variant.id,
                    product_id: variant.product_id,
                    color: variant.color || '',
                    size: variant.size || '',
                    sku: variant.sku || '',
                    quantity: variant.quantity || 0,
                };
            } else {
                this.isEditing = false;
                this.form = {
                    id: null,
                    product_id: '',
                    color: '',
                    size: '',
                    sku: '',
                    quantity: 0,
                };
            }
            this.formError = null;
            this.isModalOpen = true;
        },
        closeModal() {
            this.isModalOpen = false;
            this.isEditing = false;
            this.formError = null;
        },
        async handleSubmit() {
            this.saving = true;
            this.formError = null;

            try {
                const payload = {
                    product_id: this.form.product_id,
                    color: this.form.color || null,
                    size: this.form.size || null,
                    sku: this.form.sku || null,
                    quantity: this.form.quantity,
                };

                if (this.isEditing) {
                    await axios.put(`http://127.0.0.1:8000/api/product-variants/${this.form.id}`, payload);
                    alert('Cập nhật biến thể thành công');
                } else {
                    await axios.post("http://127.0.0.1:8000/api/product-variants", payload);
                    alert('Thêm biến thể thành công');
                }

                this.closeModal();
                this.fetchVariants();
                this.fetchStatistics();
            } catch (error) {
                console.error("Error saving variant:", error);
                if (error.response?.data?.message) {
                    this.formError = error.response.data.message;
                } else {
                    this.formError = 'Có lỗi xảy ra khi lưu biến thể';
                }
            } finally {
                this.saving = false;
            }
        },
        async deleteVariant(id) {
            if (!confirm('Bạn có chắc chắn muốn xóa biến thể này?')) {
                return;
            }

            try {
                await axios.delete(`http://127.0.0.1:8000/api/product-variants/${id}`);
                alert('Xóa biến thể thành công');
                this.fetchVariants();
                this.fetchStatistics();
            } catch (error) {
                console.error("Error deleting variant:", error);
                alert('Lỗi khi xóa biến thể');
            }
        },
    }
}
</script>

