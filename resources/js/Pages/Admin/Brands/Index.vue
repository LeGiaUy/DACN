<template>
    <div>
        <Menu></Menu>
        <h1 style="text-align: center;font-size: 20px;">Quản lý thương hiệu</h1>

        <div v-if="loading" class="text-center text-blue-600">Loading brands...</div>
        <div v-else>
            <button @click="openModal(null)" class="bg-teal-500 text-white px-4 py-2 rounded mb-4">
                Thêm thương hiệu
            </button>

            <table class="table-auto w-full border-collapse border border-gray-200">
                <thead>
                    <tr class="bg-gray-100 text-left">
                        <th class="border border-gray-300 px-4 py-2">Id</th>
                        <th class="border border-gray-300 px-4 py-2">Tên</th>
                        <th class="border border-gray-300 px-4 py-2">Mô tả</th>
                        <th class="border border-gray-300 px-4 py-2">Quản lý</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="brand in brands" :key="brand.id">
                        <td class="border border-gray-300 px-4 py-2 text-center">{{ brand.id }}</td>
                        <td class="border border-gray-300 px-4 py-2 text-center">{{ brand.name }}</td>
                        <td class="border border-gray-300 px-4 py-2 text-center">{{ brand.description }}</td>
                        <td class="border border-gray-300 px-4 py-2 text-center">
                            <button @click="openModal(brand)" class="text-teal-500 hover:text-teal-700">Sửa</button>
                            |
                            <button @click="deleteBrand(brand.id)" class="text-red-500 hover:text-red-700">Xóa</button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Modal for adding/editing brands -->
            <div v-if="isModalOpen" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center">
                <div class="bg-white p-6 rounded shadow-lg w-1/3">
                    <h2 class="text-xl font-semibold mb-4">{{ isEditing ? 'Sửa thương hiệu' : 'Thêm thương hiệu' }}</h2>

                    <form @submit.prevent="handleSubmit">
                        <div v-if="isEditing" class="mb-4">
                            <label for="id" class="block text-sm font-semibold">ID</label>
                            <input type="number" id="id" v-model.number="form.id"
                                class="w-full p-2 border border-gray-300 rounded" placeholder="ID thương hiệu"
                                required />
                            <div v-if="idError" class="text-red-500 text-sm mt-1">{{ idError }}</div>
                        </div>
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-semibold">Tên</label>
                            <input type="text" id="name" v-model="form.name"
                                class="w-full p-2 border border-gray-300 rounded" placeholder="Tên thương hiệu"
                                required />
                        </div>

                        <div class="mb-4">
                            <label for="description" class="block text-sm font-semibold">Mô tả</label>
                            <input type="text" id="description" v-model="form.description"
                                class="w-full p-2 border border-gray-300 rounded" placeholder="Mô tả thương hiệu" />
                        </div>

                        <div class="flex justify-end">
                            <button type="button" @click="closeModal" class="mr-4 text-gray-500">Hủy</button>
                            <button type="submit" class="bg-teal-500 text-dark px-4 py-2 rounded">
                                {{ isEditing ? 'Lưu thay đổi' : 'Thêm thương hiệu' }}
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
            brands: [],
            loading: true,
            error: null,
            isModalOpen: false,
            isEditing: false,
            form: {
                id: null,
                name: '',
                description: '',
            },
            idError: null,
            originalId: null,
        }
    },
    mounted() {
        this.fetchBrands();
    },
    methods: {
        async fetchBrands() {
            try {
                const response = await axios.get("http://127.0.0.1:8000/api/brands");
                this.brands = response.data;
                console.log(this.brands);
            } catch (error) {
                console.error("Error fetching brands:", error);
                this.error = "Failed to load brands. Please try again later.";
            } finally {
                this.loading = false;
            }
        },
        async handleSubmit() {
            if (!this.validateId()) {
                return;
            }
            
            if (this.isEditing) {
                await this.updateBrand();
            } else {
                await this.addBrand();
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

            const existingBrand = this.brands.find(
                brand => brand.id === this.form.id && (!this.isEditing || brand.id !== this.originalId)
            );
            if (existingBrand) {
                this.idError = 'ID này đã tồn tại';
                return false;
            }

            return true;
        },
        async addBrand() {
            try {
                const formData = { ...this.form };
                delete formData.id;
                
                const response = await axios.post("http://127.0.0.1:8000/api/brands", formData);
                this.brands.push(response.data);
                alert('Thêm thương hiệu thành công');
            } catch (error) {
                console.error("Error adding brand:", error);
                if (error.response && error.response.data && error.response.data.errors) {
                    if (error.response.data.errors.id) {
                        this.idError = 'ID này đã tồn tại';
                    }
                } else {
                    alert('Lỗi khi thêm thương hiệu');
                }
            }
        },
        async updateBrand() {
            try {
                const response = await axios.put(`http://127.0.0.1:8000/api/brands/${this.originalId}`, this.form);
                
                const index = this.brands.findIndex(brand => brand.id === this.originalId);
                if (index !== -1) {
                    this.brands[index] = response.data;
                }
                alert('Cập nhật thương hiệu thành công');
            } catch (error) {
                console.error("Error updating brand:", error);
                if (error.response && error.response.data && error.response.data.errors) {
                    if (error.response.data.errors.id) {
                        this.idError = 'ID này đã tồn tại';
                    }
                } else {
                    alert('Lỗi khi cập nhật thương hiệu');
                }
            }
        },
        openModal(brand) {
            if (brand) {
                this.isEditing = true;
                this.form = { ...brand };
                this.originalId = brand.id;
            } else {
                this.isEditing = false;
                this.form = { id: null, name: '', description: '' };
                this.originalId = null;
            }
            this.idError = null;
            this.isModalOpen = true;
        },
        closeModal() {
            this.isModalOpen = false;
        },
        async deleteBrand(id) {
            try {
                if (confirm('Bạn có chắc chắn muốn xóa thương hiệu này không?')) {
                    await axios.delete(`http://127.0.0.1:8000/api/brands/${id}`);
                    this.brands = this.brands.filter(brand => brand.id !== id);
                    alert('Xóa thương hiệu thành công');
                }
            } catch (error) {
                console.error('Error deleting brand:', error);
                alert('Lỗi khi xóa thương hiệu');
            }
        }
    }
}
</script>

<style lang="scss" scoped></style>
