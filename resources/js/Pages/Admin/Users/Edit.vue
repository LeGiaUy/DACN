<template>
    <div>
        <Menu></Menu>
        <AuthenticatedLayout>
            <template #header>
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        Chỉnh sửa người dùng: {{ user.name }}
                    </h2>
                    <Link :href="route('admin.users.index')" 
                          class="text-gray-600 hover:text-gray-900">
                        ← Quay lại
                    </Link>
                </div>
            </template>

            <div class="py-12">
                <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <form @submit.prevent="submit" class="p-6">
                            <!-- Name -->
                            <div class="mb-4">
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                    Tên <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500"
                                    :class="{ 'border-red-500': form.errors.name }"
                                />
                                <div v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.name }}
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="mb-4">
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500"
                                    :class="{ 'border-red-500': form.errors.email }"
                                />
                                <div v-if="form.errors.email" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.email }}
                                </div>
                            </div>

                            <!-- Password (Optional) -->
                            <div class="mb-4">
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                    Mật khẩu mới (để trống nếu không đổi)
                                </label>
                                <input 
                                    id="password"
                                    v-model="form.password"
                                    type="password"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500"
                                    :class="{ 'border-red-500': form.errors.password }"
                                />
                                <div v-if="form.errors.password" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.password }}
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Chỉ điền nếu muốn thay đổi mật khẩu</p>
                            </div>

                            <!-- Password Confirmation (if password is filled) -->
                            <div v-if="form.password" class="mb-4">
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                                    Xác nhận mật khẩu mới
                                </label>
                                <input 
                                    id="password_confirmation"
                                    v-model="form.password_confirmation"
                                    type="password"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500"
                                />
                            </div>

                            <!-- Role -->
                            <div class="mb-4">
                                <label for="role" class="block text-sm font-medium text-gray-700 mb-1">
                                    Vai trò <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    id="role"
                                    v-model="form.role"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500"
                                    :class="{ 'border-red-500': form.errors.role }"
                                >
                                    <option value="user">Người dùng</option>
                                    <option value="moderator">Điều hành viên</option>
                                    <option value="admin">Quản trị viên</option>
                                </select>
                                <div v-if="form.errors.role" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.role }}
                                </div>
                            </div>

                            <!-- Phone -->
                            <div class="mb-4">
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                                    Số điện thoại
                                </label>
                                <input 
                                    id="phone"
                                    v-model="form.phone"
                                    type="text"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500"
                                    :class="{ 'border-red-500': form.errors.phone }"
                                />
                                <div v-if="form.errors.phone" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.phone }}
                                </div>
                            </div>

                            <!-- Address -->
                            <div class="mb-4">
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">
                                    Địa chỉ
                                </label>
                                <textarea 
                                    id="address"
                                    v-model="form.address"
                                    rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500"
                                    :class="{ 'border-red-500': form.errors.address }"
                                ></textarea>
                                <div v-if="form.errors.address" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.address }}
                                </div>
                            </div>

                            <!-- Is Active -->
                            <div class="mb-6">
                                <label class="flex items-center">
                                    <input 
                                        type="checkbox"
                                        v-model="form.is_active"
                                        class="rounded border-gray-300 text-teal-600 shadow-sm focus:border-teal-500 focus:ring focus:ring-teal-500 focus:ring-offset-0"
                                    />
                                    <span class="ml-2 text-sm text-gray-700">Tài khoản đang hoạt động</span>
                                </label>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="flex items-center justify-end gap-4">
                                <Link :href="route('admin.users.index')" 
                                      class="px-4 py-2 text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                                    Hủy
                                </Link>
                                <button 
                                    type="submit"
                                    :disabled="form.processing"
                                    class="px-4 py-2 bg-teal-500 text-white rounded-md hover:bg-teal-600 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    {{ form.processing ? 'Đang cập nhật...' : 'Cập nhật' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Menu from '../../Includes/Menu.vue';

const props = defineProps({
    user: {
        type: Object,
        required: true
    }
});

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    password: '',
    password_confirmation: '',
    role: props.user.role || 'user',
    phone: props.user.phone || '',
    address: props.user.address || '',
    is_active: props.user.is_active ?? true,
});

const submit = () => {
    form.put(route('admin.users.update', props.user.id), {
        preserveScroll: true,
        onSuccess: () => {
            // Redirect will happen automatically
        }
    });
};
</script>

<style scoped>
</style>

