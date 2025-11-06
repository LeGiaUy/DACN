<template>
    <div>
        <Menu></Menu>
        <div class="container mx-auto px-4 py-8">
            <h1 class="text-3xl font-bold mb-6 text-center">Quản lý biến thể sản phẩm</h1>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="text-sm text-gray-600">Tổng biến thể</div>
                    <div class="text-2xl font-bold">{{ statistics.total || 0 }}</div>
                </div>
                <div class="bg-green-50 rounded-lg shadow p-4">
                    <div class="text-sm text-gray-600">Còn hàng</div>
                    <div class="text-2xl font-bold text-green-600">{{ statistics.in_stock || 0 }}</div>
                </div>
                <div class="bg-yellow-50 rounded-lg shadow p-4">
                    <div class="text-sm text-gray-600">Sắp hết</div>
                    <div class="text-2xl font-bold text-yellow-600">{{ statistics.low_stock || 0 }}</div>
                </div>
                <div class="bg-red-50 rounded-lg shadow p-4">
                    <div class="text-sm text-gray-600">Hết hàng</div>
                    <div class="text-2xl font-bold text-red-600">{{ statistics.out_of_stock || 0 }}</div>
                </div>
                <div class="bg-blue-50 rounded-lg shadow p-4">
                    <div class="text-sm text-gray-600">Tổng số lượng</div>
                    <div class="text-2xl font-bold text-blue-600">{{ statistics.total_quantity || 0 }}</div>
                </div>
            </div>

            <!-- Filters and Search -->
            <div class="bg-white rounded-lg shadow p-4 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm</label>
                        <input 
                            type="text" 
                            v-model="filters.search" 
                            @input="applyFilters"
                            placeholder="Tên sản phẩm, SKU..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sản phẩm</label>
                        <select 
                            v-model="filters.product_id" 
                            @change="applyFilters"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                            <option value="">Tất cả sản phẩm</option>
                            <option v-for="product in products" :key="product.id" :value="product.id">
                                {{ product.name }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Màu sắc</label>
                        <input 
                            type="text" 
                            v-model="filters.color" 
                            @input="applyFilters"
                            placeholder="Lọc theo màu..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kích thước</label>
                        <input 
                            type="text" 
                            v-model="filters.size" 
                            @input="applyFilters"
                            placeholder="Lọc theo size..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
                    <div>
                        <label class="flex items-center">
                            <input 
                                type="checkbox" 
                                v-model="filters.low_stock" 
                                @change="applyFilters"
                                class="mr-2">
                            <span class="text-sm text-gray-700">Sắp hết hàng (< 10)</span>
                        </label>
                    </div>
                    <div>
                        <label class="flex items-center">
                            <input 
                                type="checkbox" 
                                v-model="filters.out_of_stock" 
                                @change="applyFilters"
                                class="mr-2">
                            <span class="text-sm text-gray-700">Hết hàng</span>
                        </label>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sắp xếp</label>
                        <select 
                            v-model="filters.sort_by" 
                            @change="applyFilters"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            <option value="id">ID</option>
                            <option value="product_id">Sản phẩm</option>
                            <option value="quantity">Số lượng</option>
                            <option value="color">Màu sắc</option>
                            <option value="size">Kích thước</option>
                            <option value="created_at">Ngày tạo</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Thứ tự</label>
                        <select 
                            v-model="filters.sort_order" 
                            @change="applyFilters"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            <option value="desc">Giảm dần</option>
                            <option value="asc">Tăng dần</option>
                        </select>
                    </div>
                </div>
                <div class="mt-4 flex justify-end">
                    <button 
                        @click="resetFilters" 
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                        Đặt lại bộ lọc
                    </button>
                </div>
            </div>

            <!-- Actions -->
            <div class="mb-4 flex justify-between items-center">
                <button 
                    @click="openModal(null)" 
                    class="bg-teal-500 text-white px-4 py-2 rounded hover:bg-teal-600">
                    Thêm biến thể
                </button>
                <div class="text-sm text-gray-600">
                    Hiển thị {{ variants.data?.length || 0 }} / {{ variants.total || 0 }} biến thể
                </div>
            </div>

            <!-- Loading -->
            <div v-if="loading" class="text-center text-blue-600 py-8">
                Đang tải...
            </div>

            <!-- Variants Table -->
            <div v-else class="bg-white rounded-lg shadow overflow-hidden">
                <table class="table-auto w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-100 text-left">
                            <th class="border border-gray-300 px-4 py-2">ID</th>
                            <th class="border border-gray-300 px-4 py-2">Sản phẩm</th>
                            <th class="border border-gray-300 px-4 py-2">Màu sắc</th>
                            <th class="border border-gray-300 px-4 py-2">Kích thước</th>
                            <th class="border border-gray-300 px-4 py-2">SKU</th>
                            <th class="border border-gray-300 px-4 py-2">Số lượng</th>
                            <th class="border border-gray-300 px-4 py-2">Trạng thái</th>
                            <th class="border border-gray-300 px-4 py-2">Quản lý</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="variant in variants.data" :key="variant.id" 
                            :class="getVariantRowClass(variant)">
                            <td class="border border-gray-300 px-4 py-2 text-center">{{ variant.id }}</td>
                            <td class="border border-gray-300 px-4 py-2">
                                <div class="font-medium">{{ variant.product?.name }}</div>
                                <div class="text-xs text-gray-500">
                                    {{ variant.product?.category?.name }} - {{ variant.product?.brand?.name }}
                                </div>
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                {{ variant.color || '-' }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                {{ variant.size || '-' }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                {{ variant.sku || '-' }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2 text-center">
                                <span :class="getQuantityClass(variant.quantity)">
                                    {{ variant.quantity }}
                                </span>
                            </td>
                            <td class="border border-gray-300 px-4 py-2 text-center">
                                <span :class="getStatusClass(variant.quantity)" class="px-2 py-1 rounded text-xs font-medium">
                                    {{ getStatusText(variant.quantity) }}
                                </span>
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                <div class="flex space-x-2">
                                    <button 
                                        @click="openModal(variant)" 
                                        class="text-blue-600 hover:text-blue-800">
                                        Sửa
                                    </button>
                                    <button 
                                        @click="deleteVariant(variant.id)" 
                                        class="text-red-600 hover:text-red-800">
                                        Xóa
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="variants.data && variants.data.length === 0">
                            <td colspan="8" class="border border-gray-300 px-4 py-8 text-center text-gray-500">
                                Không có biến thể nào
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Pagination -->
                <div v-if="variants.last_page > 1" class="p-4 border-t border-gray-200 flex justify-between items-center">
                    <div class="text-sm text-gray-600">
                        Trang {{ variants.current_page }} / {{ variants.last_page }}
                    </div>
                    <div class="flex space-x-2">
                        <button 
                            @click="goToPage(variants.current_page - 1)" 
                            :disabled="variants.current_page === 1"
                            class="px-3 py-1 border rounded disabled:opacity-50 disabled:cursor-not-allowed">
                            Trước
                        </button>
                        <button 
                            v-for="page in getPageNumbers()" 
                            :key="page"
                            @click="goToPage(page)"
                            :class="['px-3 py-1 border rounded', page === variants.current_page ? 'bg-blue-500 text-white' : '']">
                            {{ page }}
                        </button>
                        <button 
                            @click="goToPage(variants.current_page + 1)" 
                            :disabled="variants.current_page === variants.last_page"
                            class="px-3 py-1 border rounded disabled:opacity-50 disabled:cursor-not-allowed">
                            Sau
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal for Add/Edit -->
            <div v-if="isModalOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                    <h2 class="text-2xl font-bold mb-4">
                        {{ isEditing ? 'Sửa biến thể' : 'Thêm biến thể mới' }}
                    </h2>
                    
                    <form @submit.prevent="handleSubmit">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Sản phẩm *</label>
                                <select 
                                    v-model="form.product_id" 
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                    <option value="">Chọn sản phẩm</option>
                                    <option v-for="product in products" :key="product.id" :value="product.id">
                                        {{ product.name }}
                                    </option>
                                </select>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Màu sắc</label>
                                    <input 
                                        type="text" 
                                        v-model="form.color" 
                                        placeholder="VD: Đỏ, Xanh..."
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Kích thước</label>
                                    <input 
                                        type="text" 
                                        v-model="form.size" 
                                        placeholder="VD: S, M, L..."
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
                                <input 
                                    type="text" 
                                    v-model="form.sku" 
                                    placeholder="Mã SKU (tùy chọn)"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Số lượng *</label>
                                <input 
                                    type="number" 
                                    v-model.number="form.quantity" 
                                    min="0"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            </div>
                        </div>
                        
                        <div v-if="formError" class="mt-4 p-3 bg-red-100 text-red-700 rounded">
                            {{ formError }}
                        </div>
                        
                        <div class="mt-6 flex justify-end space-x-3">
                            <button 
                                type="button" 
                                @click="closeModal"
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                                Hủy
                            </button>
                            <button 
                                type="submit"
                                :disabled="saving"
                                class="px-4 py-2 bg-teal-500 text-white rounded hover:bg-teal-600 disabled:opacity-50">
                                {{ saving ? 'Đang lưu...' : (isEditing ? 'Cập nhật' : 'Thêm') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import Menu from '../../Includes/Menu.vue';

export default {
    components: {
        Menu
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
                // Remove empty filters
                Object.keys(params).forEach(key => {
                    if (params[key] === '' || params[key] === false) {
                        delete params[key];
                    }
                });
                
                const response = await axios.get("http://127.0.0.1:8000/api/product-variants", { params });
                this.variants = response.data;
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
            const range = 2;
            
            for (let i = Math.max(1, current - range); i <= Math.min(last, current + range); i++) {
                pages.push(i);
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

