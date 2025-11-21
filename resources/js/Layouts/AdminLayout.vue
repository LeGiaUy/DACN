<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

const sidebarOpen = ref(false);

const menuItems = [
    {
        name: 'Dashboard',
        route: 'dashboard',
        icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'
    },
    {
        name: 'Danh mục',
        route: 'admin.categories.index',
        icon: 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z'
    },
    {
        name: 'Thương hiệu',
        route: 'admin.brands.index',
        icon: 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z'
    },
    {
        name: 'Sản phẩm',
        route: 'admin.products.index',
        icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'
    },
    {
        name: 'Biến thể',
        route: 'admin.product-variants.index',
        icon: 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z'
    },
    {
        name: 'Đơn hàng',
        route: 'admin.orders.index',
        icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'
    },
    {
        name: 'Người dùng',
        route: 'admin.users.index',
        icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'
    }
];

const isActive = (routeName) => {
    return route().current(routeName);
};
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Mobile sidebar backdrop -->
        <div 
            v-if="sidebarOpen" 
            @click="sidebarOpen = false"
            class="fixed inset-0 bg-gray-600 bg-opacity-75 z-40 lg:hidden"
        ></div>

        <!-- Sidebar -->
        <aside 
            :class="[
                'fixed top-0 left-0 z-50 h-full w-64 bg-white shadow-lg transform transition-transform duration-300 ease-in-out lg:translate-x-0',
                sidebarOpen ? 'translate-x-0' : '-translate-x-full'
            ]"
        >
            <div class="flex flex-col h-full">
                <!-- Logo -->
                <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200">
                    <Link :href="route('dashboard')" class="flex items-center space-x-3">
                        <ApplicationLogo class="block h-8 w-auto fill-current text-teal-600" />
                        <span class="text-xl font-bold text-gray-900">Admin</span>
                    </Link>
                    <button 
                        @click="sidebarOpen = false"
                        class="lg:hidden text-gray-500 hover:text-gray-700"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                    <Link
                        v-for="item in menuItems"
                        :key="item.route"
                        :href="route(item.route)"
                        :class="[
                            'flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors duration-200',
                            isActive(item.route)
                                ? 'bg-teal-50 text-teal-700 border-l-4 border-teal-600'
                                : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900'
                        ]"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                        </svg>
                        <span class="font-medium">{{ item.name }}</span>
                    </Link>
                </nav>

                <!-- User section -->
                <div class="p-4 border-t border-gray-200">
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="w-10 h-10 bg-teal-100 rounded-full flex items-center justify-center">
                            <span class="text-teal-600 font-semibold">
                                {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                            </span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">
                                {{ $page.props.auth.user.name }}
                            </p>
                            <p class="text-xs text-gray-500 truncate">
                                {{ $page.props.auth.user.email }}
                            </p>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <Link 
                            :href="route('user.account.index')"
                            class="flex-1 text-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition"
                        >
                            Tài khoản
                        </Link>
                        <Link 
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="flex-1 text-center px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg transition"
                        >
                            Đăng xuất
                        </Link>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main content -->
        <div class="lg:pl-64">
            <!-- Top bar -->
            <header class="sticky top-0 z-30 bg-white shadow-sm border-b border-gray-200">
                <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                    <button 
                        @click="sidebarOpen = !sidebarOpen"
                        class="lg:hidden p-2 text-gray-500 hover:text-gray-700"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <div class="flex-1"></div>

                    <!-- User dropdown -->
                    <div class="flex items-center space-x-4">
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <button class="flex items-center space-x-2 text-sm font-medium text-gray-700 hover:text-gray-900">
                                    <div class="w-8 h-8 bg-teal-100 rounded-full flex items-center justify-center">
                                        <span class="text-teal-600 font-semibold text-xs">
                                            {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                                        </span>
                                    </div>
                                    <span class="hidden md:block">{{ $page.props.auth.user.name }}</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </template>

                            <template #content>
                                <DropdownLink :href="route('user.account.index')">
                                    Tài khoản
                                </DropdownLink>
                                <DropdownLink :href="route('logout')" method="post" as="button">
                                    Đăng xuất
                                </DropdownLink>
                            </template>
                        </Dropdown>
                    </div>
                </div>
            </header>

            <!-- Page content -->
            <main class="py-6">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>

