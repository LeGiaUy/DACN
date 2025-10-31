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
                        <th class="border border-gray-300 px-4 py-2">Màu sắc</th>
                        <th class="border border-gray-300 px-4 py-2">Kích thước</th>
                        <th class="border border-gray-300 px-4 py-2">Số lượng</th>
                        <th class="border border-gray-300 px-4 py-2">Nổi bật</th>
                        <th class="border border-gray-300 px-4 py-2">Quản lý</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="product in products" :key="product.id">
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
                            {{ Array.isArray(product.colors) ? product.colors.join(', ') : (product.colors || '') }}
                        </td>
                        <td class="border border-gray-300 px-4 py-2 text-center">
                            {{ Array.isArray(product.sizes) ? product.sizes.join(', ') : (product.sizes || '') }}
                        </td>
                        <td class="border border-gray-300 px-4 py-2 text-center">{{ product.quantity }}</td>
                        <td class="border border-gray-300 px-4 py-2 text-center">
                            <span v-if="product.is_featured" class="text-green-600">Có</span>
                            <span v-else class="text-gray-500">Không</span>
                        </td>
                        <td class="border border-gray-300 px-4 py-2 text-center">
                            <button @click="openModal(product)" class="text-teal-500 hover:text-teal-700">Sửa</button>
                            |
                            <button @click="deleteProduct(product.id)" class="text-red-500 hover:text-red-700">Xóa</button>
                        </td>
                    </tr>
                </tbody>
            </table>

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
                            <label class="block text-sm font-semibold">Màu sắc</label>
                            <TagsInput v-model="form.colors" :suggestions="colorSuggestions" placeholder="Nhập rồi Enter, Tab hoặc dấu phẩy" />
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-semibold">Kích thước</label>
                            <TagsInput v-model="form.sizes" :suggestions="sizeSuggestions" placeholder="Ví dụ: 1/2, 2/3, 3/4, 4/5, 5/6, 6/7, 7/8, 8/9" />
                        </div>

                        <div class="mb-4">
                            <label for="quantity" class="block text-sm font-semibold">Số lượng</label>
                            <input type="number" id="quantity" v-model.number="form.quantity"
                                class="w-full p-2 border border-gray-300 rounded" min="0" placeholder="Số lượng trong kho" />
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
            products: [],
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
                const response = await axios.get("http://127.0.0.1:8000/api/products");
                this.products = response.data;
                console.log(this.products);
            } catch (error) {
                console.error("Error fetching products:", error);
                this.error = "Failed to load products. Please try again later.";
            } finally {
                this.loading = false;
            }
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
            
            if (this.isEditing) {
                await this.updateProduct();
            } else {
                await this.addProduct();
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

            const existingProduct = this.products.find(
                product => product.id === this.form.id && (!this.isEditing || product.id !== this.originalId)
            );
            if (existingProduct) {
                this.idError = 'ID này đã tồn tại';
                return false;
            }

            return true;
        },
        async addProduct() {
            try {
                const formData = { ...this.form };
                delete formData.id;
                
                const response = await axios.post("http://127.0.0.1:8000/api/products", formData);
                this.products.push(response.data);
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
        async updateProduct() {
            try {
                const response = await axios.put(`http://127.0.0.1:8000/api/products/${this.originalId}`, this.form);
                
                const index = this.products.findIndex(product => product.id === this.originalId);
                if (index !== -1) {
                    this.products[index] = response.data;
                }
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
                    this.products = this.products.filter(product => product.id !== id);
                    alert('Xóa sản phẩm thành công');
                }
            } catch (error) {
                console.error('Error deleting product:', error);
                alert('Lỗi khi xóa sản phẩm');
            }
        }
    }
}
</script>

<style lang="scss" scoped></style>