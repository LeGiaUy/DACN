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
                    <span>Hiển thị {{ displayedProductsCount }} / {{ totalProductsCount }} sản phẩm</span>
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
                        <label class="block text-sm font-medium text-gray-700">Danh mục</label>
                        <select
                            v-model="filters.category_id"
                            @change="applyFilters"
                            class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                        >
                            <option value="">Tất cả danh mục</option>
                            <option v-for="category in categories" :key="category.id" :value="category.id">
                                {{ category.name }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Thương hiệu</label>
                        <select
                            v-model="filters.brand_id"
                            @change="applyFilters"
                            class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                        >
                            <option value="">Tất cả thương hiệu</option>
                            <option v-for="brand in brands" :key="brand.id" :value="brand.id">
                                {{ brand.name }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Sản phẩm</label>
                        <select
                            v-model="filters.product_id"
                            @change="applyFilters"
                            class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                        >
                            <option value="">Tất cả sản phẩm</option>
                            <option v-for="product in filteredProducts" :key="product.id" :value="product.id">
                                {{ product.name }}
                            </option>
                        </select>
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

            <!-- Variants Dropdown Layout -->
            <div v-else class="rounded-lg border border-gray-100 bg-white shadow-sm">
                <div v-if="!variants.data || variants.data.length === 0" class="px-6 py-12 text-center text-sm text-gray-500">
                    Không có biến thể nào phù hợp với bộ lọc hiện tại
                </div>
                <div v-else class="divide-y divide-gray-200">
                    <div
                        v-for="productGroup in groupedVariants"
                        :key="productGroup.productId"
                        class="p-6 hover:bg-gray-50 transition-colors"
                    >
                        <!-- Product Header with Dropdown -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4 flex-1">
                                <img
                                    :src="productGroup.productImage || '/images/placeholder.jpg'"
                                    :alt="productGroup.productName"
                                    class="h-16 w-16 rounded-lg object-cover border border-gray-200 flex-shrink-0"
                                    @error="$event.target.src='/images/placeholder.jpg'"
                                />
                                <div class="flex-1">
                                    <h3 class="text-base font-semibold text-gray-900">{{ productGroup.productName }}</h3>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ productGroup.categoryName || 'Không rõ danh mục' }} •
                                        {{ productGroup.brandName || 'Không rõ thương hiệu' }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-6 mr-4">
                                <div class="text-right">
                                    <p class="text-xs text-gray-500">Tổng màu</p>
                                    <p class="text-base font-semibold text-gray-900">{{ productGroup.totalColors || 0 }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-gray-500">Tổng biến thể</p>
                                    <p class="text-base font-semibold text-gray-900">{{ productGroup.totalVariants || 0 }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-gray-500">Tổng số lượng</p>
                                    <p class="text-base font-semibold text-gray-900">{{ new Intl.NumberFormat('vi-VN').format(productGroup.totalQuantity || 0) }}</p>
                                </div>
                            </div>
                            <button
                                @click="toggleProduct(productGroup.productId)"
                                class="flex items-center gap-2 rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 transition"
                            >
                                <span>{{ productGroup.isOpen ? 'Thu gọn' : 'Mở rộng' }}</span>
                                <svg
                                    class="h-4 w-4 transition-transform"
                                    :class="{ 'rotate-180': productGroup.isOpen }"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </div>

                        <!-- Color Dropdowns (shown when product is expanded) -->
                        <div v-if="productGroup.isOpen" class="mt-4 space-y-3 pl-20">
                            <div
                                v-for="colorGroup in productGroup.colors"
                                :key="`${productGroup.productId}-${colorGroup.color || 'no-color'}`"
                                class="border border-gray-200 rounded-lg p-4 bg-white"
                            >
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-3">
                                        <img
                                            v-if="colorGroup.colorImage"
                                            :src="colorGroup.colorImage"
                                            :alt="colorGroup.color || 'Không màu'"
                                            class="h-12 w-12 rounded-lg object-cover border border-gray-200 flex-shrink-0"
                                            @error="$event.target.style.display='none'"
                                        />
                                        <div v-else class="h-12 w-12 rounded-lg border border-gray-200 bg-gray-100 flex items-center justify-center flex-shrink-0">
                                            <span class="text-xs text-gray-400">No img</span>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-semibold text-gray-900">
                                                Màu: {{ colorGroup.color || 'Không màu' }}
                                            </h4>
                                            <p class="text-xs text-gray-500">
                                                {{ colorGroup.variants.length }} biến thể
                                            </p>
                                        </div>
                                    </div>
                                    <button
                                        @click="toggleColor(productGroup.productId, colorGroup.color || '')"
                                        class="flex items-center gap-1 rounded border border-gray-300 px-2 py-1 text-xs text-gray-600 hover:bg-gray-50"
                                    >
                                        <span>{{ colorGroup.isOpen ? 'Thu' : 'Mở' }}</span>
                                        <svg
                                            class="h-3 w-3 transition-transform"
                                            :class="{ 'rotate-180': colorGroup.isOpen }"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Size Dropdown (shown when color is expanded) -->
                                <div v-if="colorGroup.isOpen" class="mt-3 space-y-2 pl-4 border-l-2 border-gray-200">
                                    <div
                                        v-for="variant in colorGroup.variants"
                                        :key="variant.id"
                                        class="flex items-center justify-between p-3 rounded-lg border border-gray-200 bg-gray-50 hover:bg-gray-100 transition"
                                    >
                                        <div class="flex items-center gap-4 flex-1">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-3">
                                                    <span class="text-sm font-medium text-gray-900">
                                                        Size: {{ variant.size || 'Không size' }}
                                                    </span>
                                                    <span v-if="variant.sku" class="text-xs text-gray-500">
                                                        SKU: {{ variant.sku }}
                                                    </span>
                                                </div>
                                                <div class="mt-1 flex items-center gap-4 text-xs text-gray-500">
                                                    <span>ID: #{{ variant.id }}</span>
                                                    <span :class="getQuantityClass(variant.quantity)" class="font-semibold">
                                                        Số lượng: {{ variant.quantity }}
                                                    </span>
                                                    <span :class="getStatusClass(variant.quantity)" class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold">
                                                        {{ getStatusText(variant.quantity) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2 ml-4">
                                            <button
                                                @click="openModal(variant)"
                                                class="rounded-lg border border-teal-300 bg-teal-50 px-3 py-1.5 text-xs font-medium text-teal-700 hover:bg-teal-100 transition"
                                            >
                                                Sửa
                                            </button>
                                            <button
                                                @click="deleteVariant(variant.id)"
                                                class="rounded-lg border border-red-300 bg-red-50 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-100 transition"
                                            >
                                                Xóa
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="!loading && variants" class="flex flex-wrap items-center justify-between gap-4 border-t border-gray-100 px-6 py-4 bg-gray-50">
                    <div class="text-sm text-gray-600">
                        <template v-if="totalProductsCount > 0">
                            Hiển thị {{ variants.from || 0 }} - {{ variants.to || 0 }} trong {{ totalProductsCount }} sản phẩm
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
                        <!-- Filter Section in Modal -->
                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 space-y-3">
                            <h3 class="text-sm font-semibold text-gray-900">Lọc sản phẩm</h3>
                            <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Tìm kiếm</label>
                                    <input
                                        type="text"
                                        v-model="modalFilters.search"
                                        @input="filterModalProducts"
                                        placeholder="Tên sản phẩm..."
                                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-teal-500 focus:ring-teal-500"
                                    />
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Danh mục</label>
                                    <select
                                        v-model="modalFilters.category_id"
                                        @change="filterModalProducts"
                                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-teal-500 focus:ring-teal-500"
                                    >
                                        <option value="">Tất cả danh mục</option>
                                        <option v-for="category in categories" :key="category.id" :value="category.id">
                                            {{ category.name }}
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Thương hiệu</label>
                                    <select
                                        v-model="modalFilters.brand_id"
                                        @change="filterModalProducts"
                                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-teal-500 focus:ring-teal-500"
                                    >
                                        <option value="">Tất cả thương hiệu</option>
                                        <option v-for="brand in brands" :key="brand.id" :value="brand.id">
                                            {{ brand.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Sản phẩm *</label>
                            <select
                                v-model="form.product_id"
                                required
                                class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                            >
                                <option value="">Chọn sản phẩm</option>
                                <option v-for="product in filteredModalProducts" :key="product.id" :value="product.id">
                                    {{ product.name }}
                                    <template v-if="product.category || product.brand">
                                        ({{ product.category?.name || '' }}<template v-if="product.category && product.brand"> • </template>{{ product.brand?.name || '' }})
                                    </template>
                                </option>
                            </select>
                            <div v-if="form.product_id" class="mt-2">
                                <img
                                    :src="selectedProductImage"
                                    :alt="selectedProductName"
                                    class="h-20 w-20 rounded-lg object-cover border border-gray-200"
                                    @error="$event.target.style.display='none'"
                                />
                            </div>
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

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Hình ảnh (URL)</label>
                            <input
                                type="url"
                                v-model="form.img_url"
                                placeholder="https://example.com/image.jpg"
                                class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                            />
                            <p class="mt-1 text-xs text-gray-500">URL hình ảnh cho biến thể này (tùy chọn)</p>
                            <div v-if="form.img_url" class="mt-2">
                                <img :src="form.img_url" :alt="`${form.color} ${form.size}`" 
                                     class="h-24 w-24 object-cover rounded-lg border border-gray-200"
                                     @error="$event.target.style.display='none'">
                            </div>
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
            categories: [],
            brands: [],
            statistics: {
                total: 0,
                in_stock: 0,
                low_stock: 0,
                out_of_stock: 0,
                total_quantity: 0,
            },
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
                img_url: '',
            },
            filters: {
                search: '',
                category_id: '',
                brand_id: '',
                product_id: '',
                color: '',
                size: '',
                sku: '',
                low_stock: false,
                out_of_stock: false,
                sort_by: 'id',
                sort_order: 'desc',
            },
            modalFilters: {
                search: '',
                category_id: '',
                brand_id: '',
            },
            openProducts: new Set(),
            openColors: new Map(),
            page: 1,
            perPage: 15,
            productsPerPage: 6,
            allVariants: [],
        }
    },
    mounted() {
        this.fetchVariants();
        this.fetchProducts();
        this.fetchCategories();
        this.fetchBrands();
        this.fetchStatistics();
    },
    computed: {
        filteredProducts() {
            if (!this.filters.category_id && !this.filters.brand_id && !this.filters.search) {
                return this.products;
            }
            return this.products.filter(p => {
                const matchCategory = !this.filters.category_id || p.category_id == this.filters.category_id;
                const matchBrand = !this.filters.brand_id || p.brand_id == this.filters.brand_id;
                const matchSearch = !this.filters.search || 
                    (p.name && p.name.toLowerCase().includes(this.filters.search.toLowerCase()));
                return matchCategory && matchBrand && matchSearch;
            });
        },
        filteredModalProducts() {
            if (!this.modalFilters.search && !this.modalFilters.category_id && !this.modalFilters.brand_id) {
                return this.products;
            }
            return this.products.filter(p => {
                const matchCategory = !this.modalFilters.category_id || p.category_id == this.modalFilters.category_id;
                const matchBrand = !this.modalFilters.brand_id || p.brand_id == this.modalFilters.brand_id;
                const matchSearch = !this.modalFilters.search || 
                    (p.name && p.name.toLowerCase().includes(this.modalFilters.search.toLowerCase()));
                return matchCategory && matchBrand && matchSearch;
            });
        },
        selectedProductImage() {
            if (!this.form.product_id) return '';
            const product = this.products.find(p => p.id == this.form.product_id);
            return product?.img_url || '';
        },
        selectedProductName() {
            if (!this.form.product_id) return '';
            const product = this.products.find(p => p.id == this.form.product_id);
            return product?.name || '';
        },
        groupedVariants() {
            if (!this.allVariants || this.allVariants.length === 0) {
                return [];
            }

            const productMap = new Map();

            this.allVariants.forEach(variant => {
                const productId = variant.product_id;
                const product = variant.product || {};
                const color = variant.color || '';
                const colorKey = color || '__no_color__';

                if (!productMap.has(productId)) {
                    productMap.set(productId, {
                        productId,
                        productName: product.name || 'Không rõ tên',
                        productImage: product.img_url || '',
                        categoryName: product.category?.name || '',
                        brandName: product.brand?.name || '',
                        isOpen: this.openProducts.has(productId),
                        colors: new Map(),
                    });
                }

                const productGroup = productMap.get(productId);
                if (!productGroup.colors.has(colorKey)) {
                    // Lấy ảnh từ variant đầu tiên có màu này (ưu tiên variant có img_url)
                    const colorImage = variant.img_url || null;
                    productGroup.colors.set(colorKey, {
                        color: color || null,
                        colorImage: colorImage,
                        isOpen: this.openColors.get(`${productId}-${colorKey}`) || false,
                        variants: [],
                    });
                } else {
                    // Nếu variant này có ảnh mà variant trước chưa có, cập nhật
                    const colorGroup = productGroup.colors.get(colorKey);
                    if (!colorGroup.colorImage && variant.img_url) {
                        colorGroup.colorImage = variant.img_url;
                    }
                }

                productGroup.colors.get(colorKey).variants.push(variant);
            });

            const allProducts = Array.from(productMap.values()).map(group => {
                // Tính tổng màu, tổng biến thể và tổng số lượng cho sản phẩm này
                let totalColors = group.colors.size; // Số lượng màu unique
                let totalVariants = 0;
                let totalQuantity = 0;
                
                group.colors.forEach(colorGroup => {
                    colorGroup.variants.forEach(variant => {
                        totalVariants++;
                        totalQuantity += parseInt(variant.quantity || 0);
                    });
                });
                
                return {
                    ...group,
                    totalColors,
                    totalVariants,
                    totalQuantity,
                    colors: Array.from(group.colors.values()).map(colorGroup => ({
                        ...colorGroup,
                        variants: colorGroup.variants.sort((a, b) => {
                            const aSize = (a.size || '').toString();
                            const bSize = (b.size || '').toString();
                            return aSize.localeCompare(bSize);
                        }),
                    })),
                };
            });

            // Paginate products: 6 products per page
            const start = (this.page - 1) * this.productsPerPage;
            const end = start + this.productsPerPage;
            return allProducts.slice(start, end);
        },
        totalProductsCount() {
            if (!this.allVariants || this.allVariants.length === 0) {
                return 0;
            }
            const uniqueProductIds = new Set(this.allVariants.map(v => v.product_id));
            return uniqueProductIds.size;
        },
        displayedProductsCount() {
            return this.groupedVariants.length;
        },
    },
    methods: {
        async fetchVariants() {
            this.loading = true;
            try {
                // Fetch tất cả variants (không paginate) để group theo product
                const params = {
                    per_page: 10000, // Lấy tất cả để group
                    ...this.filters
                };
                Object.keys(params).forEach(key => {
                    if (params[key] === '' || params[key] === false) {
                        delete params[key];
                    }
                });

                const response = await axios.get("http://127.0.0.1:8000/api/product-variants", { params });
                this.allVariants = response.data.data || [];
                
                // Tính statistics từ dữ liệu variants đã fetch
                this.calculateStatistics();
                
                // Tính pagination cho products (6 products/page)
                const uniqueProductIds = new Set(this.allVariants.map(v => v.product_id));
                const totalProducts = uniqueProductIds.size;
                const totalPages = Math.ceil(totalProducts / this.productsPerPage);
                
                // Giữ lại structure variants để tương thích với code cũ
                this.variants = {
                    data: this.allVariants,
                    total: this.allVariants.length,
                    current_page: this.page,
                    last_page: totalPages,
                    per_page: this.productsPerPage,
                    from: totalProducts > 0 ? ((this.page - 1) * this.productsPerPage + 1) : 0,
                    to: Math.min(this.page * this.productsPerPage, totalProducts),
                };
            } catch (error) {
                console.error("Error fetching variants:", error);
                alert('Lỗi khi tải danh sách biến thể');
                this.allVariants = [];
            } finally {
                this.loading = false;
            }
        },
        async fetchProducts() {
            try {
                const response = await axios.get("http://127.0.0.1:8000/api/products", { params: { per_page: 1000 } });
                this.products = (response.data.data || []).map(p => ({
                    ...p,
                    category_id: p.category?.id || p.category_id,
                    brand_id: p.brand?.id || p.brand_id,
                }));
            } catch (error) {
                console.error("Error fetching products:", error);
            }
        },
        calculateStatistics() {
            if (!this.allVariants || this.allVariants.length === 0) {
                this.statistics = {
                    total: 0,
                    in_stock: 0,
                    low_stock: 0,
                    out_of_stock: 0,
                    total_quantity: 0,
                };
                return;
            }

            let total = this.allVariants.length;
            let inStock = 0;
            let lowStock = 0;
            let outOfStock = 0;
            let totalQuantity = 0;

            this.allVariants.forEach(variant => {
                const quantity = parseInt(variant.quantity || 0);
                totalQuantity += quantity;

                if (quantity > 0) {
                    inStock++;
                    if (quantity < 10) {
                        lowStock++;
                    }
                } else {
                    outOfStock++;
                }
            });

            this.statistics = {
                total: total,
                in_stock: inStock,
                low_stock: lowStock,
                out_of_stock: outOfStock,
                total_quantity: totalQuantity,
            };
        },
        async fetchStatistics() {
            // Nếu đã có allVariants, tính từ đó
            if (this.allVariants && this.allVariants.length > 0) {
                this.calculateStatistics();
                return;
            }

            // Nếu chưa có, thử gọi API
            try {
                const response = await axios.get("http://127.0.0.1:8000/api/product-variants/statistics");
                console.log("Statistics response:", response.data);
                if (response.data && typeof response.data === 'object') {
                    this.statistics = {
                        total: response.data.total || 0,
                        in_stock: response.data.in_stock || 0,
                        low_stock: response.data.low_stock || 0,
                        out_of_stock: response.data.out_of_stock || 0,
                        total_quantity: response.data.total_quantity || 0,
                    };
                } else {
                    this.calculateStatistics();
                }
            } catch (error) {
                console.error("Error fetching statistics:", error);
                console.error("Error details:", error.response?.data || error.message);
                // Fallback: tính từ allVariants nếu có
                this.calculateStatistics();
            }
        },
        applyFilters() {
            this.page = 1;
            this.fetchVariants();
        },
        resetFilters() {
            this.filters = {
                search: '',
                category_id: '',
                brand_id: '',
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
        async fetchCategories() {
            try {
                const response = await axios.get("http://127.0.0.1:8000/api/categories");
                this.categories = response.data || [];
            } catch (error) {
                console.error("Error fetching categories:", error);
            }
        },
        async fetchBrands() {
            try {
                const response = await axios.get("http://127.0.0.1:8000/api/brands");
                this.brands = response.data || [];
            } catch (error) {
                console.error("Error fetching brands:", error);
            }
        },
        filterModalProducts() {
            // Computed property sẽ tự động cập nhật
        },
        toggleProduct(productId) {
            if (this.openProducts.has(productId)) {
                this.openProducts.delete(productId);
            } else {
                this.openProducts.add(productId);
            }
        },
        toggleColor(productId, color) {
            const key = `${productId}-${color || '__no_color__'}`;
            if (this.openColors.has(key)) {
                this.openColors.delete(key);
            } else {
                this.openColors.set(key, true);
            }
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
                    img_url: variant.img_url || '',
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
                    img_url: '',
                };
                this.modalFilters = {
                    search: '',
                    category_id: '',
                    brand_id: '',
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
                    img_url: this.form.img_url || null,
                };

                if (this.isEditing) {
                    await axios.put(`http://127.0.0.1:8000/api/product-variants/${this.form.id}`, payload);
                    alert('Cập nhật biến thể thành công');
                } else {
                    await axios.post("http://127.0.0.1:8000/api/product-variants", payload);
                    alert('Thêm biến thể thành công');
                }

                this.closeModal();
                await this.fetchVariants();
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
                await this.fetchVariants();
            } catch (error) {
                console.error("Error deleting variant:", error);
                alert('Lỗi khi xóa biến thể');
            }
        },
    }
}
</script>

