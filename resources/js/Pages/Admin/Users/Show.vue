<template>
    <div>
        <Menu></Menu>
        <AuthenticatedLayout>
            <template #header>
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        Chi tiết người dùng: {{ user.name }}
                    </h2>
                    <div class="flex gap-2">
                        <Link :href="route('admin.users.edit', user.id)" 
                              class="px-4 py-2 bg-teal-500 text-white rounded-md hover:bg-teal-600">
                            Chỉnh sửa
                        </Link>
                        <Link :href="route('admin.users.index')" 
                              class="px-4 py-2 text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                            ← Quay lại
                        </Link>
                    </div>
                </div>
            </template>

            <div class="py-12">
                <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <!-- User Info -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Basic Information -->
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Thông tin cơ bản</h3>
                                    <dl class="space-y-3">
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">ID</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ user.id }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Tên</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ user.name }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ user.email }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Vai trò</dt>
                                            <dd class="mt-1">
                                                <span :class="getRoleBadgeClass(user.role)" 
                                                      class="px-2 py-1 text-xs font-semibold rounded-full">
                                                    {{ getRoleDisplayName(user.role) }}
                                                </span>
                                            </dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Trạng thái</dt>
                                            <dd class="mt-1">
                                                <span :class="user.is_active ? 'text-green-600' : 'text-red-600'"
                                                      class="px-2 py-1 text-xs font-semibold">
                                                    {{ user.is_active ? 'Hoạt động' : 'Đã khóa' }}
                                                </span>
                                            </dd>
                                        </div>
                                    </dl>
                                </div>

                                <!-- Contact Information -->
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Thông tin liên hệ</h3>
                                    <dl class="space-y-3">
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Số điện thoại</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ user.phone || '-' }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Địa chỉ</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ user.address || '-' }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Ngày tạo</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ formatDate(user.created_at) }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Cập nhật lần cuối</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ formatDate(user.updated_at) }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Đăng nhập cuối</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ formatDate(user.last_login_at) }}</dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>

                            <!-- Statistics -->
                            <div class="mt-8 pt-8 border-t border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Thống kê</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <div class="text-sm text-gray-500">Tổng đơn hàng</div>
                                        <div class="text-2xl font-bold text-gray-900">{{ user.orders?.length || 0 }}</div>
                                    </div>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <div class="text-sm text-gray-500">Sản phẩm trong giỏ</div>
                                        <div class="text-2xl font-bold text-gray-900">{{ user.cart_items?.length || 0 }}</div>
                                    </div>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <div class="text-sm text-gray-500">Quyền hạn</div>
                                        <div class="text-sm font-medium text-gray-900">
                                            <span v-if="user.permissions && user.permissions.length > 0">
                                                {{ user.permissions.length }} quyền
                                            </span>
                                            <span v-else class="text-gray-400">Không có quyền đặc biệt</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Permissions (if any) -->
                            <div v-if="user.permissions && user.permissions.length > 0" class="mt-8 pt-8 border-t border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quyền hạn</h3>
                                <div class="flex flex-wrap gap-2">
                                    <span v-for="permission in user.permissions" :key="permission"
                                          class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">
                                        {{ permission }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Menu from '../../Includes/Menu.vue';

const props = defineProps({
    user: {
        type: Object,
        required: true
    }
});

const getRoleBadgeClass = (role) => {
    const classes = {
        'admin': 'bg-purple-100 text-purple-800',
        'moderator': 'bg-blue-100 text-blue-800',
        'user': 'bg-gray-100 text-gray-800'
    };
    return classes[role] || 'bg-gray-100 text-gray-800';
};

const getRoleDisplayName = (role) => {
    const names = {
        'admin': 'Quản trị viên',
        'moderator': 'Điều hành viên',
        'user': 'Người dùng'
    };
    return names[role] || 'Không xác định';
};

const formatDate = (date) => {
    if (!date) return 'Chưa có';
    return new Date(date).toLocaleString('vi-VN');
};
</script>

<style scoped>
</style>

