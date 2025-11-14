<template>
    <div>
        <Menu></Menu>
        <AuthenticatedLayout>
            <template #header>
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        Quản lý người dùng
                    </h2>
                    <Link :href="route('admin.users.create')" 
                          class="bg-teal-500 hover:bg-teal-600 text-white px-4 py-2 rounded">
                        Thêm người dùng
                    </Link>
                </div>
            </template>

            <div class="py-12">
                <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <!-- Search and Filter -->
                    <div class="mb-4 flex gap-4">
                        <input 
                            v-model="searchQuery"
                            @input="filterUsers"
                            type="text" 
                            placeholder="Tìm kiếm theo tên, email..."
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                        />
                        <select 
                            v-model="roleFilter"
                            @change="filterUsers"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500"
                        >
                            <option value="">Tất cả vai trò</option>
                            <option value="admin">Quản trị viên</option>
                            <option value="moderator">Điều hành viên</option>
                            <option value="user">Người dùng</option>
                        </select>
                        <select 
                            v-model="statusFilter"
                            @change="filterUsers"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500"
                        >
                            <option value="">Tất cả trạng thái</option>
                            <option value="active">Đang hoạt động</option>
                            <option value="inactive">Đã khóa</option>
                        </select>
                    </div>

                    <!-- Users Table -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            ID
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tên
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Email
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Vai trò
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Số điện thoại
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Trạng thái
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Đăng nhập cuối
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Thao tác
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="user in filteredUsers" :key="user.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ user.id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ user.email }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="getRoleBadgeClass(user.role)" 
                                                  class="px-2 py-1 text-xs font-semibold rounded-full">
                                                {{ getRoleDisplayName(user.role) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ user.phone || '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="user.is_active ? 'text-green-600' : 'text-red-600'"
                                                  class="px-2 py-1 text-xs font-semibold">
                                                {{ user.is_active ? 'Hoạt động' : 'Đã khóa' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ formatDate(user.last_login_at) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex gap-2">
                                                <Link :href="route('admin.users.show', user.id)" 
                                                      class="text-blue-600 hover:text-blue-900">
                                                    Xem
                                                </Link>
                                                <Link :href="route('admin.users.edit', user.id)" 
                                                      class="text-teal-600 hover:text-teal-900">
                                                    Sửa
                                                </Link>
                                                <button 
                                                    @click="toggleActive(user)"
                                                    :disabled="user.id === page.props.auth.user.id"
                                                    :class="user.is_active ? 'text-orange-600 hover:text-orange-900' : 'text-green-600 hover:text-green-900'"
                                                    class="disabled:opacity-50 disabled:cursor-not-allowed"
                                                >
                                                    {{ user.is_active ? 'Khóa' : 'Mở khóa' }}
                                                </button>
                                                <button 
                                                    @click="deleteUser(user)"
                                                    :disabled="user.id === page.props.auth.user.id"
                                                    class="text-red-600 hover:text-red-900 disabled:opacity-50 disabled:cursor-not-allowed"
                                                >
                                                    Xóa
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Empty State -->
                        <div v-if="filteredUsers.length === 0" class="text-center py-12">
                            <p class="text-gray-500">Không tìm thấy người dùng nào.</p>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                            <div class="text-sm text-gray-500">Tổng số người dùng</div>
                            <div class="text-2xl font-bold text-gray-900">{{ users.length }}</div>
                        </div>
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                            <div class="text-sm text-gray-500">Đang hoạt động</div>
                            <div class="text-2xl font-bold text-green-600">{{ activeUsersCount }}</div>
                        </div>
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                            <div class="text-sm text-gray-500">Đã khóa</div>
                            <div class="text-2xl font-bold text-red-600">{{ inactiveUsersCount }}</div>
                        </div>
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                            <div class="text-sm text-gray-500">Quản trị viên</div>
                            <div class="text-2xl font-bold text-purple-600">{{ adminUsersCount }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Menu from '../../Includes/Menu.vue';

const page = usePage();
const props = defineProps({
    users: {
        type: Array,
        default: () => []
    }
});

const searchQuery = ref('');
const roleFilter = ref('');
const statusFilter = ref('');

const filteredUsers = computed(() => {
    let result = props.users;

    // Search filter
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(user => 
            user.name.toLowerCase().includes(query) ||
            user.email.toLowerCase().includes(query) ||
            (user.phone && user.phone.includes(query))
        );
    }

    // Role filter
    if (roleFilter.value) {
        result = result.filter(user => user.role === roleFilter.value);
    }

    // Status filter
    if (statusFilter.value === 'active') {
        result = result.filter(user => user.is_active);
    } else if (statusFilter.value === 'inactive') {
        result = result.filter(user => !user.is_active);
    }

    return result;
});

const activeUsersCount = computed(() => {
    return props.users.filter(u => u.is_active).length;
});

const inactiveUsersCount = computed(() => {
    return props.users.filter(u => !u.is_active).length;
});

const adminUsersCount = computed(() => {
    return props.users.filter(u => u.role === 'admin').length;
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
    if (!date) return 'Chưa đăng nhập';
    return new Date(date).toLocaleString('vi-VN');
};

const filterUsers = () => {
    // Computed property will automatically update
};

const toggleActive = (user) => {
    if (user.id === page.props.auth.user.id) {
        alert('Bạn không thể thay đổi trạng thái của chính mình.');
        return;
    }

    if (confirm(`Bạn có chắc chắn muốn ${user.is_active ? 'khóa' : 'mở khóa'} người dùng này không?`)) {
        router.patch(route('admin.users.toggle-active', user.id), {}, {
            preserveScroll: true,
            onSuccess: () => {
                // Success message will be shown via flash message
            }
        });
    }
};

const deleteUser = (user) => {
    if (user.id === page.props.auth.user.id) {
        alert('Bạn không thể xóa chính mình.');
        return;
    }

    if (confirm(`Bạn có chắc chắn muốn xóa người dùng "${user.name}" không? Hành động này không thể hoàn tác.`)) {
        router.delete(route('admin.users.destroy', user.id), {
            preserveScroll: true,
            onSuccess: () => {
                // Success message will be shown via flash message
            }
        });
    }
};
</script>

<style scoped>
</style>

