<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import Menu from './Includes/Menu.vue';
</script>

<template>
    <Head title="Admin Dashboard" />
    <Menu />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Admin Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Categories Card -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                        <span class="text-white font-bold">C</span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-medium text-gray-900">Danh mục</h3>
                                    <p class="text-sm text-gray-500">Quản lý danh mục sản phẩm</p>
                                </div>
                            </div>
                            <div class="mt-4">
                                <Link :href="route('admin.categories.index')" 
                                      class="text-blue-600 hover:text-blue-800 font-medium">
                                    Xem danh mục →
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Brands Card -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                        <span class="text-white font-bold">B</span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-medium text-gray-900">Thương hiệu</h3>
                                    <p class="text-sm text-gray-500">Quản lý thương hiệu sản phẩm</p>
                                </div>
                            </div>
                            <div class="mt-4">
                                <Link :href="route('admin.brands.index')" 
                                      class="text-green-600 hover:text-green-800 font-medium">
                                    Xem thương hiệu →
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Products Card -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                        <span class="text-white font-bold">P</span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-medium text-gray-900">Sản phẩm</h3>
                                    <p class="text-sm text-gray-500">Quản lý sản phẩm</p>
                                </div>
                            </div>
                            <div class="mt-4">
                                <Link :href="route('admin.products.index')" 
                                      class="text-purple-600 hover:text-purple-800 font-medium">
                                    Xem sản phẩm →
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Welcome Message -->
                <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Chào mừng đến với Admin Dashboard!</h3>
                        <p class="text-gray-600">
                            Bạn có thể quản lý danh mục, thương hiệu và sản phẩm từ đây. 
                            Sử dụng các thẻ bên trên để truy cập vào các chức năng quản lý.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
