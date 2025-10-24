<template>
    <div>
        <Menu></Menu>
        <h1 style="text-align: center;font-size: 20px;">Quản lý sản phẩm</h1>

        <div v-if="loading" class="text-center text-blue-600">Loading products...</div>
        <div v-else>
            <button @click="openProductModal(null)" class="bg-teal-500 text-white px-4 py-2 rounded mb-4">
                Thêm sản phẩm
            </button>

            <table class="table-auto w-full border-collapse border border-gray-200">
                <thead>
                    <tr class="bg-gray-100 text-left">
                        <th class="border border-gray-300 px-4 py-2">Id</th>
                        <th class="border border-gray-300 px-4 py-2">Tên</th>
                        <th class="border border-gray-300 px-4 py-2">Ảnh</th>
                        <th class="border border-gray-300 px-4 py-2">Mô tả</th>
                        <th class="border border-gray-300 px-4 py-2">Màu sắc</th>
                        <th class="border border-gray-300 px-4 py-2">Kích thước</th>
                        <th class="border border-gray-300 px-4 py-2">Số lượng</th>
                        <th class="border border-gray-300 px-4 py-2">Giá</th>
                        <th class="border border-gray-300 px-4 py-2">Danh mục</th>
                        <th class="border border-gray-300 px-4 py-2">Thương hiệu</th>
                        <th class="border border-gray-300 px-4 py-2">Quản lý</th>
                    </tr>
                </thead>
                <tbody>

                    <tr v-for="product in products" :key="product.id">
                        <td class="border border-gray-300 px-4 py-2 text-center">{{ product.id }}</td>
                        <td class="border border-gray-300 px-4 py-2 text-center">{{ product.name }}</td>
                        <td class="border border-gray-300 px-4 py-2 text-center">
                            <img
                                v-if="product.img_url"
                                :src="product.img_url"
                                alt="Ảnh sản phẩm"
                                class="w-16 h-16 object-cover mx-auto"
                            />
                            <span v-else>Không có ảnh</span>
                        </td>
                        <td class="border border-gray-300 px-4 py-2 text-center">{{ product.description }}</td>
                        <td class="border border-gray-300 px-4 py-2 text-center">
                            <span v-if="product.colors && product.colors.length > 0">
                                {{ product.colors.join(', ') }}
                            </span>
                            <span v-else>Chưa có màu</span>
                        </td>
                        <td class="border border-gray-300 px-4 py-2 text-center">
                            <span v-if="product.sizes && product.sizes.length > 0">
                                {{ product.sizes.join(', ') }}
                            </span>
                            <span v-else>Chưa có size</span>
                        </td>
                        <td class="border border-gray-300 px-4 py-2 text-center">
                            <span :class="{
                                'text-green-600 font-semibold': product.quantity > 10,
                                'text-yellow-600 font-semibold': product.quantity > 0 && product.quantity <= 10,
                                'text-red-600 font-semibold': product.quantity === 0
                            }">
                                {{ product.quantity || 0 }}
                            </span>
                        </td>
                        <td class="border border-gray-300 px-4 py-2 text-center">{{ product.price}}</td>
                        <td class="border border-gray-300 px-4 py-2 text-center">{{ product.category?.name }}</td>
                        <td class="border border-gray-300 px-4 py-2 text-center">{{ product.brand?.name }}</td>
                        <td class="border border-gray-300 px-4 py-2 text-center"> <button @click="openProductModal(product)"
                                class="text-teal-500 hover:text-teal-700">Sửa</button>
                            |
                            <button @click="deleteProduct(product.id)"
                                class="text-red-500 hover:text-red-700">Xóa</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- Product Modal -->
        <div v-if="isModalOpen" @click.self="closeModal"
                class="fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center">
                <div class="bg-white p-6 rounded shadow-lg w-1/3">
                    <h2 class="text-xl font-semibold mb-4">{{ isEditing ? 'Edit Product' : 'Add Product' }}</h2>
                    <form @submit.prevent="handleProductSubmit">
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-semibold">Tên sản phẩm</label>
                            <input type="text" id="name" v-model="form.name" class="w-full p-2 border rounded"
                                placeholder="Product Name" required />
                        </div>

                        <div class="mb-4">
                            <label for="description" class="block text-sm font-semibold">Mô tả sản phẩm</label>
                            <input type="text" id="name" v-model="form.description" class="w-full p-2 border rounded"
                                placeholder="Mô tả" required />
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-semibold">Màu sắc</label>
                            <div class="flex gap-4 flex-wrap">
                                <label><input type="checkbox" value="Đỏ" v-model="form.colors" /> Đỏ</label>
                                <label><input type="checkbox" value="Xanh" v-model="form.colors" /> Xanh</label>
                                <label><input type="checkbox" value="Vàng" v-model="form.colors" /> Vàng</label>
                                <label><input type="checkbox" value="Tím" v-model="form.colors" /> Tím</label>
                                <label><input type="checkbox" value="Hồng" v-model="form.colors" /> Hồng</label>
                                <label><input type="checkbox" value="Cam" v-model="form.colors" /> Cam</label>
                                <label><input type="checkbox" value="Xanh lá" v-model="form.colors" /> Xanh lá</label>
                                <label><input type="checkbox" value="Đen" v-model="form.colors" /> Đen</label>
                                <label><input type="checkbox" value="Trắng" v-model="form.colors" /> Trắng</label>
                                <label><input type="checkbox" value="Xám" v-model="form.colors" /> Xám</label>
                            </div>
                        </div>

                            <div class="mb-4">
                                <label class="block text-sm font-semibold">Size</label>
                                <div class="flex gap-4">
                                    <label><input type="checkbox" value="2/3" v-model="form.sizes" /> 2/3</label>
                                    <label><input type="checkbox" value="3/4" v-model="form.sizes" /> 3/4</label>
                                    <label><input type="checkbox" value="4/5" v-model="form.sizes" /> 4/5</label>
                                    <label><input type="checkbox" value="5/6" v-model="form.sizes" /> 5/6</label>
                                    <label><input type="checkbox" value="6/7" v-model="form.sizes" /> 6/7</label>
                                    <label><input type="checkbox" value="7/8" v-model="form.sizes" /> 7/8</label>
                                    <label><input type="checkbox" value="8/9" v-model="form.sizes" /> 8/9</label>
                                    <label><input type="checkbox" value="9/10" v-model="form.sizes" /> 9/10</label>
                                    <label><input type="checkbox" value="10/11" v-model="form.sizes" /> 10/11</label>
                                    <label><input type="checkbox" value="11/12" v-model="form.sizes" /> 11/12</label>
                                </div>
                            </div>

                        <div class="mb-4">
                            <label for="price" class="block text-sm font-semibold">Giá</label>
                            <input type="number" id="price" v-model="form.price" class="w-full p-2 border rounded"
                                placeholder="Giá" required />
                        </div>

                        <div class="mb-4">
                            <label for="quantity" class="block text-sm font-semibold">Số lượng</label>
                            <input type="number" id="quantity" v-model.number="form.quantity" class="w-full p-2 border rounded"
                                placeholder="Số lượng" min="0" required />
                        </div>

                        <div class="mb-4">
                            <label for="img_url" class="block text-sm font-semibold">Đường dẫn hình ảnh</label>
                            <input type="text" id="img_url" v-model="form.img_url" class="w-full p-2 border rounded"
                                placeholder="Image URL" />
                        </div>

                        <div class="mb-4">
                            <label for="category" class="block text-sm font-semibold">Danh mục</label>
                            <select id="category" v-model="form.category_id" class="w-full p-2 border rounded" required>
                                <option v-for="category in categories" :key="category.id" :value="category.id">
                                    {{ category.name }}
                                </option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="brand" class="block text-sm font-semibold">Thương hiệu</label>
                            <select id="brand" v-model="form.brand_id" class="w-full p-2 border rounded" required>
                                <option v-for="brand in brands" :key="brand.id" :value="brand.id">
                                    {{ brand.name }}
                                </option>
                            </select>
                        </div>

                        <div class="flex justify-end">
                            <button @click="closeModal" type="button"
                                class="mr-4 bg-gray-500 text-white px-4 py-2 rounded">
                                Hủy
                            </button>
                            <button type="submit" class="bg-teal-500 text-dark px-4 py-2 rounded">
                                {{ isEditing ? 'Lưu thay đổi' : 'Thêm sản phẩm' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
    </div>
</template>

<script>
import axios from 'axios';
import Menu from '../Includes/Menu.vue';
    export default {
        components: {
            Menu,
            axios
        },
        data() {
            return {
                products: [],
                categories: [],
                brands: [],
                loading: true,
                isModalOpen: false,
                isEditing: false,
                form: {
                    id: null,
                    name: '',
                    price: '',
                    img_url: '',
                    colors: [],
                    sizes: [],
                    quantity: 0,
                    category_id: null,
                    brand_id: null,
                }
            };
        }, 
        mounted() {
            this.fetchProducts();
            this.fetchBrands();
            this.fetchCategories();
        },
        methods: {
            async handleProductSubmit() {
                try {
                    if (this.isEditing) {
                        // Edit Product
                        const response = await axios.put(
                            `http://127.0.0.1:8000/api/products/${this.form.id}`,
                            this.form
                        );
                        const updatedProduct = response.data;
                        // Update the product in the local list
                        const index = this.products.findIndex(p => p.id === updatedProduct.id);
                        if (index !== -1) {
                            // Use Vue 3 reactivity
                            this.products.splice(index, 1, updatedProduct);
                        }


                    } else {
                        // Add New Product
                        const response = await axios.post('http://127.0.0.1:8000/api/products', this.form);
                        const newProduct = response.data;
                        this.products.push(newProduct);
                    }
                    this.closeModal();


                } catch (error) {
                    console.error('Error saving product:', error);
                }
            },
            async fetchProducts() {
                try {
                    const response = await axios.get('http://127.0.0.1:8000/api/products');
                    this.products = response.data;
                    this.loading = false;
                } catch (error) {
                    console.error('Error fetching products:', error);
                }
            },

            async fetchCategories() {
                try {
                    const response = await axios.get('http://127.0.0.1:8000/api/categories');
                    this.categories = response.data;
                } catch (error) {
                    console.error('Error fetching categories:', error);
                }
            },
            async fetchBrands() {
                try {
                    const response = await axios.get('http://127.0.0.1:8000/api/brands');
                    this.brands = response.data;
                } catch (error) {
                    console.error('Error fetching brands:', error);
                }
            },

            async deleteProduct(productId) {
                if (!confirm('Bạn có chắc muốn xóa sản phẩm này?')) {
                    return;
                }
                try {
                    await axios.delete(`http://127.0.0.1:8000/api/products/${productId}`);
                    this.products = this.products.filter(product => product.id !== productId);
                } catch (error) {
                    console.error('Error deleting product:', error);
                }
            },

            openProductModal(product = null) {
                this.isEditing = !!product;
                if (product) {
                    this.form = { 
                        ...product,
                        colors: product.colors || [], // colors đã là array trực tiếp
                        sizes: product.sizes || [] // sizes đã là array trực tiếp
                    };
                } else {
                    this.form = { 
                        id: null, 
                        name: '', 
                        description: '',
                        price: '', 
                        img_url: '', 
                        colors: [], // Khởi tạo mảng colors rỗng
                        sizes: [], // Khởi tạo mảng sizes rỗng
                        quantity: 0,
                        category_id: null, 
                        brand_id: null 
                    };
                }
                this.isModalOpen = true;
            },

            closeModal() {
                this.isModalOpen = false;
                // Reset form về trạng thái ban đầu
                this.form = {
                    id: null,
                    name: '',
                    description: '',
                    price: '',
                    img_url: '',
                    colors: [],
                    sizes: [],
                    quantity: 0,
                    category_id: null,
                    brand_id: null,
                };
            },
        }     
    }
</script>

<style lang="scss" scoped>

</style>
