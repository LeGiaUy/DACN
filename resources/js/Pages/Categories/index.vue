<template>
    <div>
        <Menu></Menu>
        <h1 style="text-align: center;font-size: 20px;">Quản lý danh mục</h1>

        <div v-if="loading" class="text-center text-blue-600">Loading categories...</div>
        <div v-else>
            <button @click="openModal(null)" class="bg-teal-500 text-white px-4 py-2 rounded mb-4">
                Thêm danh mục
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

                    <tr v-for="category in categories" :key="category.id">
                        <td class="border border-gray-300 px-4 py-2 text-center">{{ category.id }}</td>
                        <td class="border border-gray-300 px-4 py-2 text-center">{{ category.name }}</td>
                        <td class="border border-gray-300 px-4 py-2 text-center">{{ category.description }}</td>
                        <td class="border border-gray-300 px-4 py-2 text-center"> <button @click="openModal(category)"
                                class="text-teal-500 hover:text-teal-700">Sửa</button>
                            |
                            <button @click="deleteCategory(category.id)"
                                class="text-red-500 hover:text-red-700">Xóa</button>
                        </td>
                    </tr>

                </tbody>
            </table>
            <!-- Modal for adding/editing categories -->
            <div v-if="isModalOpen" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center">
                <div class="bg-white p-6 rounded shadow-lg w-1/3">
                    <h2 class="text-xl font-semibold mb-4">{{ isEditing ? 'Sửa danh mục' : 'Thêm danh mục' }}</h2>

                     <form @submit.prevent="handleSubmit">
                         <div v-if="isEditing" class="mb-4">
                             <label for="id" class="block text-sm font-semibold">ID</label>
                             <input type="number" id="id" v-model.number="form.id"
                                 class="w-full p-2 border border-gray-300 rounded" placeholder="ID danh mục"
                                 required />
                             <div v-if="idError" class="text-red-500 text-sm mt-1">{{ idError }}</div>
                         </div>
                         <div class="mb-4">
                             <label for="name" class="block text-sm font-semibold">Tên</label>
                             <input type="text" id="name" v-model="form.name"
                                 class="w-full p-2 border border-gray-300 rounded" placeholder="Tên danh mục"
                                 required />
                         </div>

                        <div class="mb-4">
                            <label for="description" class="block text-sm font-semibold">Mô tả</label>
                            <input type="text" id="description" v-model="form.description"
                                class="w-full p-2 border border-gray-300 rounded" placeholder="Mô tả danh mục" />
                        </div>

                        <div class="flex justify-end">
                            <button type="button" @click="closeModal" class="mr-4 text-gray-500">Hủy</button>
                            <button type="submit" class="bg-teal-500 text-dark px-4 py-2 rounded">
                                {{ isEditing ? 'Lưu thay đổi' : 'Thêm danh mục' }}
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
import Menu from '../Includes/Menu.vue';

export default {
    components: {
        Menu,
        axios
    },
    data() {
        return {
            categories: [],
            loading: true,
            error: null,
            isModalOpen: false, // Modal visibility state
            isEditing: false, // Whether we're editing an existing category
             form: {
                 id: null,
                 name: '',
                 description: '',
             },
             idError: null,
             originalId: null, // Store original ID when editing
        }

    },
    mounted() {
        this.fetchCategories(); // Fetch categories when component is mounted
    },
    methods: {
        async fetchCategories() {
            try {
                // Make the GET request to the API endpoint
                const response = await axios.get("http://127.0.0.1:8000/api/categories");
                this.categories = response.data; // Assign data to categories
                console.log(this.categories);
            } catch (error) {
                console.error("Error fetching categories:", error);
                this.error = "Failed to load categories. Please try again later.";
            } finally {
                this.loading = false; // Stop loading spinner
            }
        },
         async handleSubmit() {
             // Validate ID before submitting
             if (!this.validateId()) {
                 return;
             }
             
             if (this.isEditing) { //Edit
                 await this.updateCategory();
             } else { //Add
                 await this.addCategory();
             }
             this.closeModal();
         },
         validateId() {
            this.idError = null;

            if (!this.isEditing) return true; // Không cần check ID khi thêm

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
                 // Remove id from form data when adding new category
                 const formData = { ...this.form };
                 delete formData.id;
                 
                 const response = await axios.post("http://127.0.0.1:8000/api/categories", formData);
                 this.categories.push(response.data); // Add new category to local list
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
                // Use originalId for the API call
                const response = await axios.put(`http://127.0.0.1:8000/api/categories/${this.originalId}`, this.form);
                
                // Update the category in the local list
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
                 this.form = { ...category }; // Pre-fill form for editing
                 this.originalId = category.id; // Store original ID for validation
             } else {
                 this.isEditing = false;
                 this.form = { id: null, name: '', description: '' }; // Reset form for adding
                 this.originalId = null;
             }
             this.idError = null; // Clear any previous ID errors
             this.isModalOpen = true;
         },
        closeModal() {
            this.isModalOpen = false;
        },
        async deleteCategory(id) {
            try {
                if (confirm('Bạn có chắc chắn muốn xóa danh mục này không?')) {
                    // Make the DELETE request to the API
                    await axios.delete(`http://127.0.0.1:8000/api/categories/${id}`);
                    this.categories = this.categories.filter(category => category.id !== id); // Remove from local state
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