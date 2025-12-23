<template>
    <AdminLayout>
        <div class="space-y-6">
            <!-- Page Header -->
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Quản lý sản phẩm</h1>
                    <p class="mt-2 text-sm text-gray-600">Quản lý sản phẩm và biến thể</p>
                </div>
                <button 
                    @click="openModal(null)" 
                    class="flex items-center space-x-2 bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg shadow-sm transition-colors duration-200"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Thêm sản phẩm</span>
                </button>
            </div>

            <!-- Statistics Cards -->
            <div v-if="!loading" class="grid grid-cols-1 gap-4 md:grid-cols-4">
                <div class="rounded-lg border border-gray-100 bg-white p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Tổng số sản phẩm</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">{{ statistics.total || 0 }}</p>
                </div>
                <div class="rounded-lg border border-purple-100 bg-purple-50 p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Sản phẩm nổi bật</p>
                    <p class="mt-2 text-2xl font-bold text-purple-600">{{ statistics.featured || 0 }}</p>
                </div>
                <div class="rounded-lg border border-blue-100 bg-blue-50 p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Sản phẩm có biến thể</p>
                    <p class="mt-2 text-2xl font-bold text-blue-600">{{ statistics.with_variants || 0 }}</p>
                </div>
                <div class="rounded-lg border border-green-100 bg-green-50 p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Tổng số lượng tồn kho</p>
                    <p class="mt-2 text-2xl font-bold text-green-600">
                        {{ new Intl.NumberFormat('vi-VN').format(statistics.total_quantity || 0) }}
                    </p>
                </div>
            </div>

            <!-- Filters -->
            <div class="rounded-lg border border-gray-100 bg-white p-6 shadow-sm">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm</label>
                        <input
                            v-model="filters.search"
                            @input="applyFilters"
                            type="text"
                            placeholder="Tên sản phẩm..."
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Danh mục</label>
                        <select
                            v-model="filters.category_id"
                            @change="applyFilters"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm"
                        >
                            <option value="">Tất cả danh mục</option>
                            <option v-for="category in categories" :key="category.id" :value="category.id">
                                {{ category.name }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Thương hiệu</label>
                        <select
                            v-model="filters.brand_id"
                            @change="applyFilters"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm"
                        >
                            <option value="">Tất cả thương hiệu</option>
                            <option v-for="brand in brands" :key="brand.id" :value="brand.id">
                                {{ brand.name }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                        <select
                            v-model="filters.status"
                            @change="applyFilters"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm"
                        >
                            <option value="">Tất cả</option>
                            <option value="featured">Nổi bật</option>
                            <option value="with_variants">Có biến thể</option>
                            <option value="no_variants">Không có biến thể</option>
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
            <div v-if="loading" class="bg-white rounded-lg shadow-sm p-12 text-center">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-teal-600"></div>
                <p class="mt-4 text-gray-600">Đang tải sản phẩm...</p>
            </div>

            <!-- Products Table -->
            <div v-else class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <!-- Bulk Actions Bar -->
                <div v-if="selectedProducts.length > 0" class="px-6 py-3 bg-teal-50 border-b border-gray-200 flex items-center justify-between">
                    <span class="text-sm text-teal-800 font-medium">
                        Đã chọn {{ selectedProducts.length }} sản phẩm
                    </span>
                    <button
                        @click="deleteSelectedProducts"
                        class="px-4 py-1.5 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors"
                    >
                        Xóa đã chọn
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left">
                                    <input
                                        type="checkbox"
                                        :checked="isAllSelected"
                                        @change="toggleSelectAll"
                                        class="rounded border-gray-300 text-teal-600 focus:ring-teal-500"
                                    />
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sản phẩm</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giá</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Danh mục</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thương hiệu</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số lượng</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nổi bật</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="product in (products.data || [])" :key="product.id" class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input
                                        type="checkbox"
                                        :value="product.id"
                                        v-model="selectedProducts"
                                        class="rounded border-gray-300 text-teal-600 focus:ring-teal-500"
                                    />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    #{{ product.id }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <img v-if="product.img_url" :src="product.img_url" alt="image" class="w-12 h-12 object-contain rounded bg-gray-100" />
                                        <div v-else class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ product.name }}</div>
                                            <div class="text-xs text-gray-500">
                                                {{ product.variants?.length || 0 }} biến thể
                                                <a :href="`/admin/product-variants?product_id=${product.id}`" class="text-teal-600 hover:text-teal-800 ml-2">Quản lý</a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(product.price) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ product.category?.name || '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ product.brand?.name || '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="getQuantityClass(product.total_quantity ?? product.quantity)" class="text-sm font-medium">
                                        {{ product.total_quantity ?? product.quantity }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span v-if="product.is_featured" class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Có</span>
                                    <span v-else class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">Không</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-3">
                                        <button @click="openModal(product)" class="text-teal-600 hover:text-teal-900">Sửa</button>
                                        <button @click="deleteProduct(product.id)" class="text-red-600 hover:text-red-900">Xóa</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="products && products.data && products.data.length" class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        Hiển thị {{ products.from || 0 }} - {{ products.to || 0 }} trong {{ products.total || 0 }} sản phẩm
                    </div>
                    <div class="flex gap-2">
                        <button 
                            class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed" 
                            :disabled="(products.current_page||1)===1" 
                            @click="goToPage((products.current_page||1)-1)"
                        >
                            Trước
                        </button>
                        <button 
                            v-for="page in pageNumbers" 
                            :key="page" 
                            class="px-3 py-2 border rounded-lg" 
                            :class="{ 'bg-teal-600 text-white border-teal-600': page === products.current_page, 'border-gray-300 hover:bg-gray-50': page !== products.current_page }" 
                            @click="goToPage(page)"
                        >
                            {{ page }}
                        </button>
                        <button 
                            class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed" 
                            :disabled="(products.current_page||1)===(products.last_page||1)" 
                            @click="goToPage((products.current_page||1)+1)"
                        >
                            Sau
                        </button>
                    </div>
                </div>
            </div>
            <!-- Modal for adding/editing products -->
            <div 
                v-if="isModalOpen" 
                class="fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center z-50 p-4"
                @click.self="closeModal"
            >
                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col">
                    <!-- Header -->
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">
                                {{ isEditing ? 'Sửa sản phẩm' : 'Thêm sản phẩm' }}
                            </h2>
                            <p class="mt-1 text-xs text-gray-500">
                                Nhập thông tin cơ bản, phân loại và tồn kho cho sản phẩm.
                            </p>
                        </div>
                        <button
                            type="button"
                            class="text-gray-400 hover:text-gray-600 transition"
                            @click="closeModal"
                        >
                            <span class="sr-only">Đóng</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="px-6 py-5 overflow-y-auto">
                        <form @submit.prevent="handleSubmit" class="space-y-6">
                            <!-- ID chỉ khi sửa -->
                            <div v-if="isEditing" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label for="id" class="block text-xs font-medium text-gray-700 mb-1">ID sản phẩm</label>
                                    <input
                                        type="number"
                                        id="id"
                                        v-model.number="form.id"
                                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                                        placeholder="ID sản phẩm"
                                        required
                                    />
                                    <div v-if="idError" class="text-xs text-red-500 mt-1">{{ idError }}</div>
                                </div>
                            </div>

                            <!-- Thông tin cơ bản -->
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900 mb-3">Thông tin cơ bản</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="md:col-span-2">
                                        <label for="name" class="block text-xs font-medium text-gray-700 mb-1">Tên sản phẩm</label>
                                        <input
                                            type="text"
                                            id="name"
                                            v-model="form.name"
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                                            placeholder="Ví dụ: Áo thun bé gái..."
                                            required
                                        />
                                    </div>

                                    <div>
                                        <label for="price" class="block text-xs font-medium text-gray-700 mb-1">Giá bán (VND)</label>
                                        <input
                                            type="number"
                                            id="price"
                                            v-model.number="form.price"
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                                            placeholder="Ví dụ: 199000"
                                            min="0"
                                        />
                                    </div>

                                    <div>
                                        <label for="quantity" class="block text-xs font-medium text-gray-700 mb-1">
                                            Số lượng tổng (không dùng biến thể)
                                        </label>
                                        <input
                                            type="number"
                                            id="quantity"
                                            v-model.number="form.quantity"
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                                            min="0"
                                            placeholder="Ví dụ: 50"
                                        />
                                        <p class="text-[11px] text-gray-500 mt-1">
                                            Nếu sản phẩm có biến thể, số lượng sẽ được tính từ tổng các biến thể.
                                        </p>
                                    </div>

                                    <div class="md:col-span-2">
                                        <label for="description" class="block text-xs font-medium text-gray-700 mb-1">Mô tả</label>
                                        <textarea
                                            id="description"
                                            v-model="form.description"
                                            rows="3"
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 resize-none"
                                            placeholder="Mô tả ngắn gọn về chất liệu, kiểu dáng, độ tuổi phù hợp..."
                                        ></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Phân loại & hiển thị -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-4">
                                    <h3 class="text-sm font-semibold text-gray-900">Phân loại</h3>
                                    <div>
                                        <label for="category_id" class="block text-xs font-medium text-gray-700 mb-1">Danh mục</label>
                                        <select
                                            id="category_id"
                                            v-model.number="form.category_id"
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                                        >
                                            <option value="">Chọn danh mục</option>
                                            <option
                                                v-for="category in categories"
                                                :key="category.id"
                                                :value="category.id"
                                            >
                                                {{ category.name }}
                                            </option>
                                        </select>
                                    </div>

                                    <div>
                                        <label for="brand_id" class="block text-xs font-medium text-gray-700 mb-1">Thương hiệu</label>
                                        <select
                                            id="brand_id"
                                            v-model.number="form.brand_id"
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                                        >
                                            <option value="">Chọn thương hiệu</option>
                                            <option
                                                v-for="brand in brands"
                                                :key="brand.id"
                                                :value="brand.id"
                                            >
                                                {{ brand.name }}
                                            </option>
                                        </select>
                                    </div>

                                    <div class="flex items-center space-x-2 rounded-lg border border-gray-200 px-3 py-2 bg-gray-50">
                                        <input
                                            type="checkbox"
                                            id="is_featured"
                                            v-model="form.is_featured"
                                            class="rounded border-gray-300 text-teal-600 focus:ring-teal-500"
                                        />
                                        <div>
                                            <label for="is_featured" class="text-xs font-medium text-gray-800">Đánh dấu là sản phẩm nổi bật</label>
                                            <p class="text-[11px] text-gray-500">
                                                Sản phẩm nổi bật sẽ được ưu tiên hiển thị ở trang chủ.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Ảnh & preview -->
                                <div class="space-y-3">
                                    <h3 class="text-sm font-semibold text-gray-900">Hình ảnh</h3>
                                    <div>
                                        <label for="img_url" class="block text-xs font-medium text-gray-700 mb-1">Ảnh sản phẩm (URL)</label>
                                        <input
                                            type="url"
                                            id="img_url"
                                            v-model="form.img_url"
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                                            placeholder="https://..."
                                        />
                                        <p class="text-[11px] text-gray-500 mt-1">
                                            Bạn có thể dán link ảnh từ CDN / Storage.
                                        </p>
                                    </div>

                                    <div v-if="form.img_url" class="mt-2">
                                        <p class="text-[11px] text-gray-500 mb-1">Xem trước:</p>
                                        <div class="border border-dashed border-gray-300 rounded-lg p-2 flex items-center justify-center bg-gray-50">
                                            <img
                                                :src="form.img_url"
                                                alt="preview"
                                                class="max-h-40 object-contain rounded"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Quản lý biến thể -->
                            <div class="space-y-3">
                                <div class="p-3 rounded-lg bg-blue-50 border border-blue-200 flex">
                                    <div class="flex-shrink-0 mt-0.5">
                                        <svg class="h-5 w-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3 text-xs text-blue-800">
                                        <h3 class="font-semibold mb-1">Quản lý biến thể</h3>
                                        <p v-if="isEditing && form.id">
                                            Sau khi lưu sản phẩm, bạn có thể quản lý chi tiết biến thể tại
                                            <a
                                                :href="`/admin/product-variants?product_id=${form.id}`"
                                                target="_blank"
                                                class="underline font-semibold"
                                            >
                                                trang quản lý biến thể
                                            </a>.
                                        </p>
                                        <p v-else>
                                            Sau khi tạo sản phẩm, bạn có thể thêm biến thể tại
                                            <a
                                                href="/admin/product-variants"
                                                target="_blank"
                                                class="underline font-semibold"
                                            >
                                                trang quản lý biến thể
                                            </a>.
                                        </p>
                                    </div>
                                </div>

                                <!-- Quick variant creation: nhiều màu, nhiều size, số lượng cho mỗi size -->
                                <div
                                    class="p-3 rounded-lg bg-gray-50 border border-gray-200 space-y-2"
                                >
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-xs font-semibold text-gray-800">Tạo biến thể nhanh (tùy chọn)</p>
                                            <p class="text-[11px] text-gray-500">
                                                Dùng cho vài biến thể cơ bản. Quản lý chi tiết hơn tại trang biến thể riêng.
                                            </p>
                                        </div>
                                        <button
                                            type="button"
                                            class="text-xs font-medium text-teal-600 hover:text-teal-700"
                                            @click="addVariantRow"
                                        >
                                            + Thêm dòng
                                        </button>
                                    </div>

                                    <div
                                        v-if="form.variants && form.variants.length > 0"
                                        class="mt-2 overflow-x-auto"
                                    >
                                        <table class="min-w-full border-collapse border border-gray-200 text-xs">
                                            <thead>
                                                <tr class="bg-gray-100 text-left">
                                                    <th class="border border-gray-300 px-2 py-1">Màu</th>
                                                    <th class="border border-gray-300 px-2 py-1">Kích thước</th>
                                                    <th class="border border-gray-300 px-2 py-1">SKU</th>
                                                    <th class="border border-gray-300 px-2 py-1">Số lượng</th>
                                                    <th class="border border-gray-300 px-2 py-1 w-12"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr
                                                    v-for="(v, idx) in form.variants"
                                                    :key="idx"
                                                    class="bg-white"
                                                >
                                                    <td class="border border-gray-300 px-2 py-1">
                                                        <input
                                                            type="text"
                                                            v-model="v.color"
                                                            class="w-full rounded border border-gray-300 px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500"
                                                            placeholder="VD: Đen"
                                                        />
                                                    </td>
                                                    <td class="border border-gray-300 px-2 py-1">
                                                        <input
                                                            type="text"
                                                            v-model="v.size"
                                                            class="w-full rounded border border-gray-300 px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500"
                                                            placeholder="VD: S"
                                                        />
                                                    </td>
                                                    <td class="border border-gray-300 px-2 py-1">
                                                        <input
                                                            type="text"
                                                            v-model="v.sku"
                                                            class="w-full rounded border border-gray-300 px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500"
                                                            placeholder="Tùy chọn"
                                                        />
                                                    </td>
                                                    <td class="border border-gray-300 px-2 py-1">
                                                        <input
                                                            type="number"
                                                            min="0"
                                                            v-model.number="v.quantity"
                                                            class="w-full rounded border border-gray-300 px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500"
                                                        />
                                                    </td>
                                                    <td class="border border-gray-300 px-2 py-1 text-center">
                                                        <button
                                                            type="button"
                                                            class="text-[11px] text-red-600 hover:text-red-700"
                                                            @click="removeVariantRow(idx)"
                                                        >
                                                            Xóa
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <p
                                        v-else
                                        class="text-[11px] text-gray-400 italic"
                                    >
                                        Chưa có biến thể nào. Click "Thêm dòng" để tạo.
                                    </p>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Footer -->
                    <div class="px-6 py-3 border-t border-gray-100 bg-gray-50 flex items-center justify-end space-x-3">
                        <button
                            type="button"
                            @click="closeModal"
                            class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800"
                        >
                            Hủy
                        </button>
                        <button
                            type="submit"
                            form="__dummy" 
                            class="hidden"
                        ></button>
                        <button
                            type="button"
                            @click="handleSubmit"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg bg-teal-600 text-white hover:bg-teal-700 shadow-sm transition-colors"
                        >
                            {{ isEditing ? 'Lưu thay đổi' : 'Thêm sản phẩm' }}
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
import TagsInput from '@/Components/TagsInput.vue';

export default {
    components: {
        AdminLayout,
        TagsInput
    },
    data() {
        return {
            products: { data: [] },
            allProducts: [], // Lưu tất cả products để tính statistics
            perPage: 6,
            page: 1,
            categories: [],
            brands: [],
            loading: true,
            error: null,
            isModalOpen: false,
            isEditing: false,
            selectedProducts: [],
            filters: {
                search: '',
                category_id: '',
                brand_id: '',
                status: '', // 'featured', 'with_variants', 'no_variants'
            },
            statistics: {
                total: 0,
                featured: 0,
                with_variants: 0,
                total_quantity: 0,
            },
            form: {
                id: null,
                name: '',
                price: 0,
                description: '',
                category_id: null,
                brand_id: null,
                img_url: '',
                quantity: 0,
                is_featured: false,
                colors: [],
                sizes: [],
                variants: [],
            },
            colorSuggestions: ['Đỏ','Xanh','Vàng','Trắng','Đen','Hồng','Tím','Nâu','Cam','Xám'],
            sizeSuggestions: ['1/2', '2/3', '3/4', '4/5', '5/6', '6/7', '7/8', '8/9', '9/10', '10/11','11/12', '12/13'],
            idError: null,
            originalId: null,
        }
    },
    mounted() {
        this.fetchProducts();
        this.fetchCategories();
        this.fetchBrands();
    },
    methods: {
        async fetchProducts() {
            this.loading = true;
            try {
                // Fetch tất cả products để tính statistics
                const allResponse = await axios.get("http://127.0.0.1:8000/api/products", { 
                    params: { 
                        per_page: 10000 // Lấy tất cả để tính statistics
                    } 
                });
                this.allProducts = allResponse.data.data || [];
                this.calculateStatistics();

                // Fetch products với filters và pagination
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

                const response = await axios.get("http://127.0.0.1:8000/api/products", { params });
                this.products = response.data;
            } catch (error) {
                console.error("Error fetching products:", error);
                this.error = "Failed to load products. Please try again later.";
            } finally {
                this.loading = false;
            }
        },
        calculateStatistics() {
            if (!this.allProducts || this.allProducts.length === 0) {
                this.statistics = {
                    total: 0,
                    featured: 0,
                    with_variants: 0,
                    total_quantity: 0,
                };
                return;
            }

            let total = this.allProducts.length;
            let featured = 0;
            let withVariants = 0;
            let totalQuantity = 0;

            this.allProducts.forEach(product => {
                if (product.is_featured) {
                    featured++;
                }
                if (product.variants && product.variants.length > 0) {
                    withVariants++;
                }
                totalQuantity += parseInt(product.total_quantity || product.quantity || 0);
            });

            this.statistics = {
                total: total,
                featured: featured,
                with_variants: withVariants,
                total_quantity: totalQuantity,
            };
        },
        applyFilters() {
            this.page = 1;
            this.fetchProducts();
        },
        resetFilters() {
            this.filters = {
                search: '',
                category_id: '',
                brand_id: '',
                status: '',
            };
            this.page = 1;
            this.fetchProducts();
        },
        getQuantityClass(quantity) {
            if (quantity <= 0) return 'text-red-600 font-bold';
            if (quantity < 10) return 'text-yellow-600 font-bold';
            return 'text-green-600';
        },
        addVariantRow() {
            if (!Array.isArray(this.form.variants)) this.form.variants = [];
            this.form.variants.push({ color: '', size: '', sku: '', quantity: 0 });
        },
        removeVariantRow(idx) {
            if (!Array.isArray(this.form.variants)) return;
            this.form.variants.splice(idx, 1);
        },
        generateVariantsFromOptions() {
            const colors = Array.isArray(this.form.colors) && this.form.colors.length ? this.form.colors : [null];
            const sizes = Array.isArray(this.form.sizes) && this.form.sizes.length ? this.form.sizes : [null];
            const combos = [];
            colors.forEach(c => {
                sizes.forEach(s => {
                    combos.push({ color: c || '', size: s || '', sku: '', quantity: 0 });
                });
            });
            // Merge with existing by key (color+size)
            const map = new Map();
            (this.form.variants || []).forEach(v => {
                const key = `${v.color || ''}__${v.size || ''}`;
                map.set(key, { ...v });
            });
            combos.forEach(v => {
                const key = `${v.color || ''}__${v.size || ''}`;
                if (!map.has(key)) map.set(key, v);
            });
            this.form.variants = Array.from(map.values());
        },
        goToPage(p) {
            if (!p) return;
            const last = this.products.last_page || 1;
            this.page = Math.max(1, Math.min(p, last));
            this.fetchProducts();
        },
        async fetchCategories() {
            try {
                const response = await axios.get("http://127.0.0.1:8000/api/categories");
                this.categories = response.data;
            } catch (error) {
                console.error("Error fetching categories:", error);
            }
        },
        async fetchBrands() {
            try {
                const response = await axios.get("http://127.0.0.1:8000/api/brands");
                this.brands = response.data;
            } catch (error) {
                console.error("Error fetching brands:", error);
            }
        },
        async handleSubmit() {
            if (!this.validateId()) {
                return;
            }
            
            const payload = { ...this.form };
            // Ensure nullable fields are properly formatted
            if (!payload.img_url || payload.img_url.trim() === '') {
                payload.img_url = null;
            }
            // Ensure description is not empty
            if (!payload.description || payload.description.trim() === '') {
                payload.description = '';
            }
            // Ensure arrays are properly formatted
            if (!Array.isArray(payload.colors) || payload.colors.length === 0) {
                payload.colors = [];
            }
            if (!Array.isArray(payload.sizes) || payload.sizes.length === 0) {
                payload.sizes = [];
            }
            // Clean variants: nhiều màu, nhiều size, số lượng cho mỗi size
            if (Array.isArray(payload.variants)) {
                payload.variants = payload.variants
                    .filter(v => (v.color || v.size) && v.quantity !== null && v.quantity !== undefined)
                    .map(v => ({ 
                        color: v.color && v.color.trim() ? v.color.trim() : null, 
                        size: v.size && v.size.trim() ? v.size.trim() : null, 
                        sku: v.sku && v.sku.trim() ? v.sku.trim() : null, 
                        quantity: Number(v.quantity || 0) 
                    }));

                // Nếu đang thêm sản phẩm mới, bắt buộc phải có ít nhất 1 biến thể
                if (!this.isEditing && payload.variants.length === 0) {
                    alert('Vui lòng tạo ít nhất 1 biến thể (màu, size, số lượng) trước khi lưu sản phẩm.');
                    return;
                }

                if (payload.variants.length === 0) {
                    delete payload.variants;
                }
            } else {
                // Không có mảng variants
                if (!this.isEditing) {
                    alert('Vui lòng tạo ít nhất 1 biến thể (màu, size, số lượng) trước khi lưu sản phẩm.');
                    return;
                }
                delete payload.variants;
            }

            if (this.isEditing) {
                await this.updateProduct(payload);
            } else {
                await this.addProduct(payload);
            }
            this.closeModal();
        },
        validateId() {
            this.idError = null;

            if (!this.isEditing) return true;

            if (!this.form.id) {
                this.idError = 'ID là bắt buộc';
                return false;
            }
            if (this.form.id <= 0) {
                this.idError = 'ID phải lớn hơn 0';
                return false;
            }

            const list = (this.products && this.products.data) ? this.products.data : [];
            const existingProduct = list.find(
                product => product.id === this.form.id && (!this.isEditing || product.id !== this.originalId)
            );
            if (existingProduct) {
                this.idError = 'ID này đã tồn tại';
                return false;
            }

            return true;
        },
        async addProduct(payload) {
            try {
                const formData = { ...payload };
                delete formData.id;
                
                console.log('Sending payload:', formData);
                
                const response = await axios.post("http://127.0.0.1:8000/api/products", formData);
                await this.fetchProducts();
                alert('Thêm sản phẩm thành công');
            } catch (error) {
                console.error("Error adding product:", error);
                console.error("Error response:", error.response?.data);
                
                if (error.response) {
                    if (error.response.status === 422 && error.response.data?.errors) {
                        const errors = error.response.data.errors;
                        let errorMessage = 'Lỗi validation:\n';
                        Object.keys(errors).forEach(key => {
                            errorMessage += `${key}: ${errors[key].join(', ')}\n`;
                        });
                        alert(errorMessage);
                    } else if (error.response.data?.message) {
                        alert('Lỗi: ' + error.response.data.message);
                    } else {
                        alert('Lỗi khi thêm sản phẩm: ' + (error.response.statusText || 'Unknown error'));
                    }
                } else {
                    alert('Lỗi khi thêm sản phẩm: ' + (error.message || 'Network error'));
                }
            }
        },
        async updateProduct(payload) {
            try {
                const response = await axios.put(`http://127.0.0.1:8000/api/products/${this.originalId}`, payload);
                
                await this.fetchProducts();
                alert('Cập nhật sản phẩm thành công');
            } catch (error) {
                console.error("Error updating product:", error);
                if (error.response && error.response.data && error.response.data.errors) {
                    if (error.response.data.errors.id) {
                        this.idError = 'ID này đã tồn tại';
                    }
                } else {
                    alert('Lỗi khi cập nhật sản phẩm');
                }
            }
        },
        openModal(product) {
            if (product) {
                this.isEditing = true;
                this.form = { ...product };
                this.originalId = product.id;
                if (typeof this.form.quantity !== 'number') this.form.quantity = Number(this.form.quantity || 0);
                if (typeof this.form.is_featured !== 'boolean') this.form.is_featured = !!this.form.is_featured;
                if (!Array.isArray(this.form.variants)) this.form.variants = (product.variants || []).map(v => ({ color: v.color || '', size: v.size || '', sku: v.sku || '', quantity: Number(v.quantity || 0) }));
            } else {
                this.isEditing = false;
                this.form = { 
                    id: null, 
                    name: '', 
                    price: 0, 
                    description: '', 
                    category_id: null, 
                    brand_id: null,
                    img_url: '',
                    quantity: 0,
                    is_featured: false,
                    colors: [],
                    sizes: [],
                    variants: [],
                };
                this.originalId = null;
            }
            this.idError = null;
            this.isModalOpen = true;
        },
        closeModal() {
            this.isModalOpen = false;
        },
        async deleteProduct(id) {
            try {
                if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?')) {
                    await axios.delete(`http://127.0.0.1:8000/api/products/${id}`)
                    this.selectedProducts = this.selectedProducts.filter(
                      selectedId => selectedId !== id
                    )
                    await this.fetchProducts()
                    alert('Xóa sản phẩm thành công')
                }
            } catch (error) {
                console.error('Error deleting product:', error)
                alert('Lỗi khi xóa sản phẩm')
            }
        },
        toggleSelectAll() {
            const productList = this.products.data || []
            if (this.isAllSelected) {
                this.selectedProducts = []
            } else {
                this.selectedProducts = productList.map(product => product.id)
            }
        },
        async deleteSelectedProducts() {
            if (this.selectedProducts.length === 0) return
            
            const count = this.selectedProducts.length
            const message = `Bạn có chắc chắn muốn xóa ${count} sản phẩm đã chọn không?`
            
            if (!confirm(message)) return
            
            try {
                const response = await axios.post(
                  'http://127.0.0.1:8000/api/products/bulk-delete',
                  { ids: this.selectedProducts }
                )
                alert(response.data?.message || 'Xóa sản phẩm thành công')
                this.selectedProducts = []
                await this.fetchProducts()
            } catch (error) {
                console.error('Error deleting products:', error)
                alert(
                  error.response?.data?.message ||
                    'Lỗi khi xóa sản phẩm'
                )
            }
        }
    },
    computed: {
        isAllSelected() {
            const productList = this.products.data || []
            return productList.length > 0 && 
                   this.selectedProducts.length === productList.length
        },
        pageNumbers() {
            const last = this.products.last_page || 1;
            const current = this.products.current_page || 1;
            const spread = 2;
            const start = Math.max(1, current - spread);
            const end = Math.min(last, current + spread);
            const arr = [];
            for (let i = start; i <= end; i++) arr.push(i);
            return arr;
        }
    }
}
</script>

<style lang="scss" scoped></style>