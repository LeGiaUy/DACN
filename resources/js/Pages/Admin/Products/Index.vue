<template>
    <div>
        <Menu></Menu>
        <h1 style="text-align: center;font-size: 20px;">Quản lý sản phẩm</h1>

        <div v-if="loading" class="text-center text-blue-600">Loading products...</div>
        <div v-else>
            <button @click="openModal(null)" class="bg-teal-500 text-white px-4 py-2 rounded mb-4">
                Thêm sản phẩm
            </button>

            <table class="table-auto w-full border-collapse border border-gray-200">
                <thead>
                    <tr class="bg-gray-100 text-left">
                        <th class="border border-gray-300 px-4 py-2">Id</th>
                        <th class="border border-gray-300 px-4 py-2">Tên</th>
                        <th class="border border-gray-300 px-4 py-2">Giá</th>
                        <th class="border border-gray-300 px-4 py-2">Danh mục</th>
                        <th class="border border-gray-300 px-4 py-2">Thương hiệu</th>
                        <th class="border border-gray-300 px-4 py-2">Hình ảnh</th>
                        <th class="border border-gray-300 px-4 py-2">Biến thể</th>
                        <th class="border border-gray-300 px-4 py-2">Số lượng</th>
                        <th class="border border-gray-300 px-4 py-2">Nổi bật</th>
                        <th class="border border-gray-300 px-4 py-2">Quản lý</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="product in (products.data || [])" :key="product.id">
                        <td class="border border-gray-300 px-4 py-2 text-center">{{ product.id }}</td>
                        <td class="border border-gray-300 px-4 py-2 text-center">{{ product.name }}</td>
                        <td class="border border-gray-300 px-4 py-2 text-center">{{ product.price }}</td>
                        <td class="border border-gray-300 px-4 py-2 text-center">{{ product.category?.name }}</td>
                        <td class="border border-gray-300 px-4 py-2 text-center">{{ product.brand?.name }}</td>
                        <td class="border border-gray-300 px-4 py-2 text-center">
                            <template v-if="product.img_url">
                                <img :src="product.img_url" alt="image" class="inline-block max-h-12 object-contain" />
                            </template>
                            <span v-else class="text-gray-400">Không có</span>
                        </td>
                        <td class="border border-gray-300 px-4 py-2 text-center">
                            <div class="flex flex-col items-center space-y-1">
                                <span class="text-sm">
                                    <span class="font-semibold">{{ product.variants?.length || 0 }}</span> biến thể
                                </span>
                                <a 
                                    :href="`/admin/product-variants?product_id=${product.id}`" 
                                    class="text-xs text-blue-600 hover:text-blue-800 underline">
                                    Quản lý
                                </a>
                            </div>
                        </td>
                        <td class="border border-gray-300 px-4 py-2 text-center">
                            <span :class="getQuantityClass(product.total_quantity ?? product.quantity)">
                                {{ product.total_quantity ?? product.quantity }}
                            </span>
                        </td>
                        <td class="border border-gray-300 px-4 py-2 text-center">
                            <span v-if="product.is_featured" class="text-green-600">Có</span>
                            <span v-else class="text-gray-500">Không</span>
                        </td>
                        <td class="border border-gray-300 px-4 py-2 text-center">
                            <div class="flex flex-col space-y-1">
                                <button @click="openModal(product)" class="text-teal-500 hover:text-teal-700 text-sm">Sửa</button>
                                <button @click="deleteProduct(product.id)" class="text-red-500 hover:text-red-700 text-sm">Xóa</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div v-if="products && products.data && products.data.length" class="mt-4 flex items-center justify-between">
                <div class="text-sm text-gray-600">
                    Hiển thị {{ products.from || 0 }} - {{ products.to || 0 }} trong {{ products.total || 0 }} sản phẩm
                </div>
                <div class="flex gap-1">
                    <button class="px-3 py-1 border rounded" :disabled="(products.current_page||1)===1" @click="goToPage((products.current_page||1)-1)">Trước</button>
                    <button v-for="page in pageNumbers" :key="page" class="px-3 py-1 border rounded" :class="{ 'bg-teal-500 text-white border-teal-500': page === products.current_page }" @click="goToPage(page)">{{ page }}</button>
                    <button class="px-3 py-1 border rounded" :disabled="(products.current_page||1)===(products.last_page||1)" @click="goToPage((products.current_page||1)+1)">Sau</button>
                </div>
            </div>
            <!-- Modal for adding/editing products -->
            <div v-if="isModalOpen" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center">
                <div class="bg-white p-6 rounded shadow-lg w-11/12 sm:w-3/4 md:w-1/2 max-w-2xl max-h-[80vh] overflow-y-auto">
                    <h2 class="text-xl font-semibold mb-4">{{ isEditing ? 'Sửa sản phẩm' : 'Thêm sản phẩm' }}</h2>

                    <form @submit.prevent="handleSubmit">
                        <div v-if="isEditing" class="mb-4">
                            <label for="id" class="block text-sm font-semibold">ID</label>
                            <input type="number" id="id" v-model.number="form.id"
                                class="w-full p-2 border border-gray-300 rounded" placeholder="ID sản phẩm"
                                required />
                            <div v-if="idError" class="text-red-500 text-sm mt-1">{{ idError }}</div>
                        </div>
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-semibold">Tên</label>
                            <input type="text" id="name" v-model="form.name"
                                class="w-full p-2 border border-gray-300 rounded" placeholder="Tên sản phẩm"
                                required />
                        </div>

                        <div class="mb-4">
                            <label for="price" class="block text-sm font-semibold">Giá</label>
                            <input type="number" id="price" v-model.number="form.price"
                                class="w-full p-2 border border-gray-300 rounded" placeholder="Giá sản phẩm" />
                        </div>

                        <div class="mb-4">
                            <label for="description" class="block text-sm font-semibold">Mô tả</label>
                            <textarea id="description" v-model="form.description"
                                class="w-full p-2 border border-gray-300 rounded" placeholder="Mô tả sản phẩm"></textarea>
                        </div>

                        <div class="mb-4">
                            <label for="category_id" class="block text-sm font-semibold">Danh mục</label>
                            <select id="category_id" v-model.number="form.category_id"
                                class="w-full p-2 border border-gray-300 rounded">
                                <option value="">Chọn danh mục</option>
                                <option v-for="category in categories" :key="category.id" :value="category.id">
                                    {{ category.name }}
                                </option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="brand_id" class="block text-sm font-semibold">Thương hiệu</label>
                            <select id="brand_id" v-model.number="form.brand_id"
                                class="w-full p-2 border border-gray-300 rounded">
                                <option value="">Chọn thương hiệu</option>
                                <option v-for="brand in brands" :key="brand.id" :value="brand.id">
                                    {{ brand.name }}
                                </option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="img_url" class="block text-sm font-semibold">Ảnh (URL)</label>
                            <input type="url" id="img_url" v-model="form.img_url"
                                class="w-full p-2 border border-gray-300 rounded" placeholder="https://..." />
                            <div v-if="form.img_url" class="mt-2">
                                <img :src="form.img_url" alt="preview" class="max-h-24 object-contain border rounded p-1" />
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="quantity" class="block text-sm font-semibold">Số lượng (nếu không dùng biến thể)</label>
                            <input type="number" id="quantity" v-model.number="form.quantity"
                                class="w-full p-2 border border-gray-300 rounded" min="0" placeholder="Số lượng trong kho" />
                            <p class="text-xs text-gray-500 mt-1">Nếu sản phẩm có biến thể, số lượng sẽ được tính từ tổng các biến thể</p>
                        </div>

                        <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3 flex-1">
                                    <h3 class="text-sm font-medium text-blue-800">Quản lý biến thể</h3>
                                    <div class="mt-2 text-sm text-blue-700">
                                        <p v-if="isEditing && form.id">
                                            Sau khi lưu sản phẩm, bạn có thể quản lý biến thể tại 
                                            <a :href="`/admin/product-variants?product_id=${form.id}`" target="_blank" class="underline font-semibold">
                                                trang quản lý biến thể
                                            </a>
                                        </p>
                                        <p v-else>
                                            Sau khi tạo sản phẩm, bạn có thể thêm biến thể tại 
                                            <a href="/admin/product-variants" target="_blank" class="underline font-semibold">
                                                trang quản lý biến thể
                                            </a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Optional: Quick variant creation (simplified) -->
                        <div v-if="isEditing && form.id" class="mb-4 p-3 bg-gray-50 border border-gray-200 rounded">
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-semibold">Tạo biến thể nhanh (tùy chọn)</label>
                                <button type="button" class="text-sm text-teal-600" @click="addVariantRow">+ Thêm dòng</button>
                            </div>
                            <p class="text-xs text-gray-500 mb-2">Bạn có thể tạo một vài biến thể cơ bản ở đây, hoặc quản lý chi tiết tại trang biến thể</p>
                            <div v-if="form.variants && form.variants.length > 0" class="mt-2 overflow-x-auto">
                                <table class="table-auto w-full border-collapse border border-gray-200 text-sm">
                                    <thead>
                                        <tr class="bg-gray-100 text-left">
                                            <th class="border border-gray-300 px-2 py-1">Màu</th>
                                            <th class="border border-gray-300 px-2 py-1">Kích thước</th>
                                            <th class="border border-gray-300 px-2 py-1">SKU</th>
                                            <th class="border border-gray-300 px-2 py-1">Số lượng</th>
                                            <th class="border border-gray-300 px-2 py-1"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(v, idx) in form.variants" :key="idx">
                                            <td class="border border-gray-300 px-2 py-1">
                                                <input type="text" v-model="v.color" class="w-full p-1 border rounded text-sm" placeholder="VD: Đen" />
                                            </td>
                                            <td class="border border-gray-300 px-2 py-1">
                                                <input type="text" v-model="v.size" class="w-full p-1 border rounded text-sm" placeholder="VD: S" />
                                            </td>
                                            <td class="border border-gray-300 px-2 py-1">
                                                <input type="text" v-model="v.sku" class="w-full p-1 border rounded text-sm" placeholder="Tùy chọn" />
                                            </td>
                                            <td class="border border-gray-300 px-2 py-1">
                                                <input type="number" min="0" v-model.number="v.quantity" class="w-full p-1 border rounded text-sm" />
                                            </td>
                                            <td class="border border-gray-300 px-2 py-1 text-center">
                                                <button type="button" class="text-red-600 text-xs" @click="removeVariantRow(idx)">Xóa</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <p v-else class="text-xs text-gray-400 italic">Chưa có biến thể nào. Click "Thêm dòng" để tạo.</p>
                        </div>

                        <div class="mb-4 flex items-center">
                            <input type="checkbox" id="is_featured" v-model="form.is_featured" class="mr-2" />
                            <label for="is_featured" class="text-sm font-semibold">Nổi bật</label>
                        </div>

                        <div class="flex justify-end">
                            <button type="button" @click="closeModal" class="mr-4 text-gray-500">Hủy</button>
                            <button type="submit" class="bg-teal-500 text-dark px-4 py-2 rounded">
                                {{ isEditing ? 'Lưu thay đổi' : 'Thêm sản phẩm' }}
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
import TagsInput from '../../../Components/TagsInput.vue';

export default {
    components: {
        Menu,
        TagsInput
    },
    data() {
        return {
            products: { data: [] },
            perPage: 6,
            page: 1,
            categories: [],
            brands: [],
            loading: true,
            error: null,
            isModalOpen: false,
            isEditing: false,
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
            try {
                const response = await axios.get("http://127.0.0.1:8000/api/products", { 
                    params: { 
                        page: this.page, 
                        per_page: this.perPage 
                    } 
                });
                this.products = response.data;
            } catch (error) {
                console.error("Error fetching products:", error);
                this.error = "Failed to load products. Please try again later.";
            } finally {
                this.loading = false;
            }
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
            // Ensure nullable fields are truly null for backend validation
            if (!payload.img_url) payload.img_url = null;
            // Clean variants
            if (Array.isArray(payload.variants)) {
                payload.variants = payload.variants
                    .filter(v => (v.color || v.size) && v.quantity >= 0)
                    .map(v => ({ color: v.color || null, size: v.size || null, sku: v.sku || null, quantity: Number(v.quantity || 0) }));
            } else {
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
                
                await axios.post("http://127.0.0.1:8000/api/products", formData);
                await this.fetchProducts();
                alert('Thêm sản phẩm thành công');
            } catch (error) {
                console.error("Error adding product:", error);
                if (error.response && error.response.data && error.response.data.errors) {
                    if (error.response.data.errors.id) {
                        this.idError = 'ID này đã tồn tại';
                    }
                } else {
                    alert('Lỗi khi thêm sản phẩm');
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
                    await axios.delete(`http://127.0.0.1:8000/api/products/${id}`);
                    await this.fetchProducts();
                    alert('Xóa sản phẩm thành công');
                }
            } catch (error) {
                console.error('Error deleting product:', error);
                alert('Lỗi khi xóa sản phẩm');
            }
        }
    },
    computed: {
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