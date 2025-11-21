<template>
    <AdminLayout>
        <div class="space-y-6">
            <!-- Page Header -->
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Quản lý danh mục</h1>
                    <p class="mt-2 text-sm text-gray-600">Quản lý danh mục sản phẩm</p>
                </div>
                <button 
                    @click="openModal(null)" 
                    class="flex items-center space-x-2 bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg shadow-sm transition-colors duration-200"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Thêm danh mục</span>
                </button>
            </div>

            <!-- Statistics Cards -->
            <div v-if="!loading" class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="rounded-lg border border-gray-100 bg-white p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Tổng số danh mục</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">{{ categories.length }}</p>
                </div>
                <div class="rounded-lg border border-blue-100 bg-blue-50 p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Danh mục có mô tả</p>
                    <p class="mt-2 text-2xl font-bold text-blue-600">{{ categories.filter(c => c.description).length }}</p>
                </div>
                <div class="rounded-lg border border-green-100 bg-green-50 p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Danh mục hoạt động</p>
                    <p class="mt-2 text-2xl font-bold text-green-600">{{ categories.length }}</p>
                </div>
            </div>

            <!-- Loading State -->
            <div v-if="loading" class="bg-white rounded-lg shadow-sm p-12 text-center">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-teal-600"></div>
                <p class="mt-4 text-gray-600">Đang tải danh mục...</p>
            </div>

            <!-- Categories Table -->
            <div v-else class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mô tả</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="category in categories" :key="category.id" class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    #{{ category.id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ category.name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-500">{{ category.description || '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-3">
                                        <button 
                                            @click="openModal(category)"
                                            class="text-teal-600 hover:text-teal-900 font-medium"
                                        >
                                            Sửa
                                        </button>
                                        <button 
                                            @click="deleteCategory(category.id)"
                                            class="text-red-600 hover:text-red-900 font-medium"
                                        >
                                            Xóa
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="categories.length === 0">
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                    Chưa có danh mục nào. Hãy thêm danh mục đầu tiên!
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Modal for adding/editing categories -->
            <div 
                v-if="isModalOpen" 
                class="fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center z-50 p-4"
                @click.self="closeModal"
            >
                <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-900">
                            {{ isEditing ? 'Sửa danh mục' : 'Thêm danh mục mới' }}
                        </h2>
                    </div>

                    <form @submit.prevent="handleSubmit" class="px-6 py-4">
                        <div v-if="isEditing" class="mb-4">
                            <label for="id" class="block text-sm font-medium text-gray-700 mb-1">ID</label>
                            <input 
                                type="number" 
                                id="id" 
                                v-model.number="form.id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                                placeholder="ID danh mục"
                                required 
                            />
                            <div v-if="idError" class="mt-1 text-sm text-red-600">{{ idError }}</div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                Tên danh mục <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="name" 
                                v-model="form.name"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                                placeholder="Nhập tên danh mục"
                                required 
                            />
                        </div>

                        <div class="mb-6">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
                            <textarea 
                                id="description" 
                                v-model="form.description"
                                rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                                placeholder="Nhập mô tả danh mục (tùy chọn)"
                            ></textarea>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <button 
                                type="button" 
                                @click="closeModal" 
                                class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors"
                            >
                                Hủy
                            </button>
                            <button 
                                type="submit" 
                                class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg transition-colors"
                            >
                                {{ isEditing ? 'Lưu thay đổi' : 'Thêm danh mục' }}
                            </button>
                        </div>
                    </form>
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
            categories: [],
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
        this.fetchCategories();
    },
    methods: {
        async fetchCategories() {
            try {
                const response = await axios.get("http://127.0.0.1:8000/api/categories");
                this.categories = response.data;
            } catch (error) {
                console.error("Error fetching categories:", error);
                this.error = "Failed to load categories. Please try again later.";
            } finally {
                this.loading = false;
            }
        },
        async handleSubmit() {
            if (!this.validateId()) {
                return;
            }
            
            if (this.isEditing) {
                await this.updateCategory();
            } else {
                await this.addCategory();
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

            const existingCategory = this.categories.find(
                cat => cat.id === this.form.id && (!this.isEditing || cat.id !== this.originalId)
            );
            if (existingCategory) {
                this.idError = 'ID này đã tồn tại';
                return false;
            }

            return true;
        },
        async addCategory() {
            try {
                const formData = { ...this.form };
                delete formData.id;
                
                const response = await axios.post("http://127.0.0.1:8000/api/categories", formData);
                this.categories.push(response.data);
                alert('Thêm danh mục thành công');
            } catch (error) {
                console.error("Error adding category:", error);
                if (error.response && error.response.data && error.response.data.errors) {
                    if (error.response.data.errors.id) {
                        this.idError = 'ID này đã tồn tại';
                    }
                } else {
                    alert('Lỗi khi thêm danh mục');
                }
            }
        },
        async updateCategory() {
            try {
                const response = await axios.put(`http://127.0.0.1:8000/api/categories/${this.originalId}`, this.form);
                
                const index = this.categories.findIndex(category => category.id === this.originalId);
                if (index !== -1) {
                    this.categories[index] = response.data;
                }
                alert('Cập nhật danh mục thành công');
            } catch (error) {
                console.error("Error updating category:", error);
                if (error.response && error.response.data && error.response.data.errors) {
                    if (error.response.data.errors.id) {
                        this.idError = 'ID này đã tồn tại';
                    }
                } else {
                    alert('Lỗi khi cập nhật danh mục');
                }
            }
        },
        openModal(category) {
            if (category) {
                this.isEditing = true;
                this.form = { ...category };
                this.originalId = category.id;
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
        async deleteCategory(id) {
            try {
                if (confirm('Bạn có chắc chắn muốn xóa danh mục này không?')) {
                    await axios.delete(`http://127.0.0.1:8000/api/categories/${id}`);
                    this.categories = this.categories.filter(category => category.id !== id);
                    alert('Xóa danh mục thành công');
                }
            } catch (error) {
                console.error('Error deleting category:', error);
                alert('Lỗi khi xóa danh mục');
            }
        }
    }
}
</script>

<style lang="scss" scoped></style>
