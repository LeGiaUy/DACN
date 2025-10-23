<template>
    <div>
        <Menu></Menu>
        <h1>Trang Categories</h1>
        <div v-if="loading" class="text-center text-blue-600">Đang tải danh mục...</div>
        <div v-else>
            <table class="table-auto w-full border-collapse border border-gray-200">
                <thead class="bg-gray-100 text-center">
                    <tr>
                        <th class="border border-gray-300 px-4 py-2">ID</th>
                        <th class="border border-gray-300 px-4 py-2">Tên</th>
                        <th class="border border-gray-300 px-4 py-2">Mô tả</th>
                        <th class="border border-gray-300 px-4 py-2">Quản lý</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="category in categories" :key="category.id">
                        <td class="border border-gray-300 px4 py-2 text-center">{{ category.id }}</td>
                        <td class="border border-gray-300 px4 py-2 text-center">{{ category.name }}</td>
                        <td class="border border-gray-300 px4 py-2 text-center">{{ category.description }}</td>
                        <td class="border border-gray-300 px4 py-2 text-center">
                            <button @click="openModal(category)" class="text-teal-500 hover:text-700">
                                Edit
                            </button>
                            
                            <button @click="deleteCategory(category.id)" class="text-red-500 hover:text-red-700">
                                Delete
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import Menu from '../Includes/Menu.vue';
import axios from 'axios';

    export default {
        components: {
            Menu,
        },
        data() {
            return {
                categories: [],
                loading: true,
                error: null,
            };
        },
        mounted() {
            this.fetchCategories()
        },
        methods: {
            async fetchCategories() {
                try {
                    const respone = await axios.get("http://127.0.0.1:8000/api/categories");
                    this.categories = respone.data;
                    console.log(this.categories)
                } catch (error) {
                    console.error("Error fetching categories: ", error);
                    this.error = "Failed to load categories. Please try again later"
                } finally {
                    this.loading = false;
                }
            }
        }
    }
</script>

<style lang="scss" scoped>

</style>
