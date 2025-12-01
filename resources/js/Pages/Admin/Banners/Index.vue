<template>
    <AdminLayout>
        <div class="space-y-6">
            <!-- Page Header -->
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Quản lý Banner</h1>
                    <p class="mt-2 text-sm text-gray-600">Quản lý banner hiển thị trên trang chủ</p>
                </div>
                <button 
                    @click="openModal(null)" 
                    class="flex items-center space-x-2 bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg shadow-sm transition-colors duration-200"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Thêm Banner</span>
                </button>
            </div>

            <!-- Statistics Cards -->
            <div v-if="!loading" class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="rounded-lg border border-gray-100 bg-white p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Tổng số banner</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">{{ banners.length }}</p>
                </div>
                <div class="rounded-lg border border-green-100 bg-green-50 p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Banner đang hoạt động</p>
                    <p class="mt-2 text-2xl font-bold text-green-600">{{ banners.filter(b => b.is_active).length }}</p>
                </div>
                <div class="rounded-lg border border-blue-100 bg-blue-50 p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Banner không hoạt động</p>
                    <p class="mt-2 text-2xl font-bold text-blue-600">{{ banners.filter(b => !b.is_active).length }}</p>
                </div>
            </div>

            <!-- Loading State -->
            <div v-if="loading" class="bg-white rounded-lg shadow-sm p-12 text-center">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-teal-600"></div>
                <p class="mt-4 text-gray-600">Đang tải banner...</p>
            </div>

            <!-- Banners Grid -->
            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div 
                    v-for="banner in banners" 
                    :key="banner.id"
                    class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow"
                >
                    <div class="relative">
                        <img 
                            :src="banner.image_url" 
                            :alt="banner.alt_text || banner.title"
                            class="w-full h-48 object-cover"
                            @error="$event.target.src='/images/placeholder.jpg'"
                        />
                        <div class="absolute top-2 right-2">
                            <span 
                                :class="[
                                    'px-2 py-1 rounded text-xs font-semibold',
                                    banner.is_active ? 'bg-green-500 text-white' : 'bg-gray-500 text-white'
                                ]"
                            >
                                {{ banner.is_active ? 'Hoạt động' : 'Tắt' }}
                            </span>
                        </div>
                        <div class="absolute top-2 left-2">
                            <span class="px-2 py-1 rounded text-xs font-semibold bg-teal-500 text-white">
                                Thứ tự: {{ banner.order }}
                            </span>
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                            {{ banner.title || 'Banner #' + banner.id }}
                        </h3>
                        <p class="text-sm text-gray-600 mb-2" v-if="banner.alt_text">
                            {{ banner.alt_text }}
                        </p>
                        <p class="text-xs text-gray-500 mb-4" v-if="banner.link_url">
                            Link: <a :href="banner.link_url" target="_blank" class="text-teal-600 hover:underline">{{ banner.link_url }}</a>
                        </p>
                        <div class="flex justify-end space-x-2">
                            <button 
                                @click="openModal(banner)"
                                class="px-3 py-1 text-sm bg-teal-600 hover:bg-teal-700 text-white rounded transition-colors"
                            >
                                Sửa
                            </button>
                            <button 
                                @click="deleteBanner(banner.id)"
                                class="px-3 py-1 text-sm bg-red-600 hover:bg-red-700 text-white rounded transition-colors"
                            >
                                Xóa
                            </button>
                        </div>
                    </div>
                </div>
                <div v-if="banners.length === 0" class="col-span-full text-center py-12 text-gray-500">
                    Chưa có banner nào. Hãy thêm banner đầu tiên!
                </div>
            </div>

            <!-- Modal for adding/editing banners -->
            <div 
                v-if="isModalOpen" 
                class="fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center z-50 p-4"
                @click.self="closeModal"
            >
                <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                    <div class="px-6 py-4 border-b border-gray-200 sticky top-0 bg-white">
                        <h2 class="text-xl font-semibold text-gray-900">
                            {{ isEditing ? 'Sửa Banner' : 'Thêm Banner mới' }}
                        </h2>
                    </div>

                    <form @submit.prevent="handleSubmit" class="px-6 py-4">
                        <div class="mb-4">
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                                Tiêu đề
                            </label>
                            <input 
                                type="text" 
                                id="title" 
                                v-model="form.title"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                                placeholder="Nhập tiêu đề banner (tùy chọn)"
                            />
                        </div>

                        <div class="mb-4">
                            <label for="image_url" class="block text-sm font-medium text-gray-700 mb-1">
                                URL hình ảnh <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="image_url" 
                                v-model="form.image_url"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                                placeholder="https://example.com/image.jpg hoặc /images/banner.jpg"
                                required 
                            />
                            <p class="mt-1 text-xs text-gray-500">Nhập URL đầy đủ hoặc đường dẫn tương đối từ thư mục public</p>
                            <div v-if="form.image_url" class="mt-2">
                                <img 
                                    :src="form.image_url" 
                                    :alt="form.alt_text || form.title"
                                    class="w-full h-48 object-cover rounded-lg border border-gray-200"
                                    @error="$event.target.style.display='none'"
                                />
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="alt_text" class="block text-sm font-medium text-gray-700 mb-1">
                                Alt text
                            </label>
                            <input 
                                type="text" 
                                id="alt_text" 
                                v-model="form.alt_text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                                placeholder="Mô tả hình ảnh (tùy chọn)"
                            />
                        </div>

                        <div class="mb-4">
                            <label for="link_url" class="block text-sm font-medium text-gray-700 mb-1">
                                Link URL
                            </label>
                            <input 
                                type="text" 
                                id="link_url" 
                                v-model="form.link_url"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                                placeholder="https://example.com (tùy chọn)"
                            />
                            <p class="mt-1 text-xs text-gray-500">URL sẽ được mở khi click vào banner</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="order" class="block text-sm font-medium text-gray-700 mb-1">
                                    Thứ tự hiển thị
                                </label>
                                <input 
                                    type="number" 
                                    id="order" 
                                    v-model.number="form.order"
                                    min="0"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                                    placeholder="0"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Trạng thái
                                </label>
                                <label class="flex items-center space-x-2 mt-2">
                                    <input 
                                        type="checkbox" 
                                        v-model="form.is_active"
                                        class="rounded text-teal-600 focus:ring-teal-500"
                                    />
                                    <span class="text-sm text-gray-700">Hoạt động</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                            <button 
                                type="button" 
                                @click="closeModal" 
                                class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors"
                            >
                                Hủy
                            </button>
                            <button 
                                type="submit" 
                                :disabled="saving"
                                class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                {{ saving ? 'Đang lưu...' : (isEditing ? 'Lưu thay đổi' : 'Thêm Banner') }}
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
import { router } from '@inertiajs/vue3';

export default {
    components: {
        AdminLayout
    },
    props: {
        banners: {
            type: Array,
            default: () => []
        }
    },
    data() {
        return {
            loading: false,
            isModalOpen: false,
            isEditing: false,
            saving: false,
            form: {
                title: '',
                image_url: '',
                alt_text: '',
                link_url: '',
                order: 0,
                is_active: true,
            },
        }
    },
    methods: {
        async handleSubmit() {
            this.saving = true;
            try {
                if (this.isEditing) {
                    await this.updateBanner();
                } else {
                    await this.addBanner();
                }
                this.closeModal();
                router.reload();
            } catch (error) {
                console.error("Error saving banner:", error);
                alert('Lỗi khi lưu banner: ' + (error.response?.data?.message || error.message));
            } finally {
                this.saving = false;
            }
        },
        async addBanner() {
            const response = await axios.post('/admin/banners', this.form, {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });
            return response.data;
        },
        async updateBanner() {
            const response = await axios.put(`/admin/banners/${this.form.id}`, this.form, {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });
            return response.data;
        },
        openModal(banner) {
            if (banner) {
                this.isEditing = true;
                this.form = { ...banner };
            } else {
                this.isEditing = false;
                this.form = {
                    title: '',
                    image_url: '',
                    alt_text: '',
                    link_url: '',
                    order: 0,
                    is_active: true,
                };
            }
            this.isModalOpen = true;
        },
        closeModal() {
            this.isModalOpen = false;
            this.isEditing = false;
        },
        async deleteBanner(id) {
            if (!confirm('Bạn có chắc chắn muốn xóa banner này không?')) {
                return;
            }
            try {
                await axios.delete(`/admin/banners/${id}`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                router.reload();
            } catch (error) {
                console.error('Error deleting banner:', error);
                alert('Lỗi khi xóa banner: ' + (error.response?.data?.message || error.message));
            }
        }
    }
}
</script>

<style lang="scss" scoped></style>
