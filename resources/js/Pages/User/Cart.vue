<template>
    <UserLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Giỏ hàng của tôi</h1>
                <p class="text-gray-600 mt-2">{{ count }} sản phẩm trong giỏ hàng</p>
            </div>

            <div v-if="cartItems.length === 0" class="text-center py-12">
                <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                </svg>
                <h2 class="mt-4 text-xl font-semibold text-gray-900">Giỏ hàng trống</h2>
                <p class="mt-2 text-gray-600">Bạn chưa có sản phẩm nào trong giỏ hàng</p>
                <Link :href="route('user.products.index')" 
                      class="mt-6 inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-300">
                    Tiếp tục mua sắm
                </Link>
            </div>

            <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Cart Items -->
                <div class="lg:col-span-2 space-y-4">
                    <div v-for="item in cartItems" :key="item.id" 
                         class="bg-white rounded-lg shadow-md p-6 flex flex-col md:flex-row gap-4">
                        <!-- Product Image -->
                        <div class="flex-shrink-0">
                            <img :src="item.product?.img_url || '/images/placeholder.jpg'" 
                                 :alt="item.product?.name"
                                 class="w-32 h-32 object-cover rounded-lg">
                        </div>

                        <!-- Product Info -->
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                <Link :href="route('user.products.show', item.product?.id)" 
                                      class="hover:text-blue-600">
                                    {{ item.product?.name }}
                                </Link>
                            </h3>
                            <p class="text-sm text-gray-600 mb-2">{{ item.product?.category?.name }}</p>
                            
                            <!-- Variant Info -->
                            <div v-if="item.color || item.size" class="text-sm text-gray-500 mb-2">
                                <span v-if="item.color">Màu: {{ item.color }}</span>
                                <span v-if="item.color && item.size"> | </span>
                                <span v-if="item.size">Size: {{ item.size }}</span>
                            </div>

                            <!-- Quantity Controls -->
                            <div class="flex items-center space-x-4 mt-4">
                                <label class="text-sm font-medium text-gray-700">Số lượng:</label>
                                <div class="flex items-center space-x-2">
                                    <button @click="updateQuantity(item.id, item.quantity - 1)" 
                                            :disabled="item.quantity <= 1 || updating === item.id"
                                            class="px-3 py-1 border rounded disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50">
                                        -
                                    </button>
                                    <span class="w-12 text-center">{{ item.quantity }}</span>
                                    <button @click="updateQuantity(item.id, item.quantity + 1)" 
                                            :disabled="updating === item.id"
                                            class="px-3 py-1 border rounded disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50">
                                        +
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Price and Actions -->
                        <div class="flex flex-col justify-between items-end">
                            <div class="text-right">
                                <p class="text-lg font-bold text-blue-600">{{ formatPrice((item.price || 0) * (item.quantity || 0)) }}</p>
                                <p class="text-sm text-gray-500">{{ formatPrice(item.price) }} x {{ item.quantity }}</p>
                            </div>
                            <button @click="removeItem(item.id)" 
                                    :disabled="removing === item.id"
                                    class="mt-4 text-red-600 hover:text-red-800 disabled:opacity-50">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Clear Cart Button -->
                    <div class="flex justify-end">
                        <button @click="clearCart" 
                                :disabled="clearing"
                                class="px-4 py-2 text-red-600 border border-red-600 rounded-lg hover:bg-red-50 disabled:opacity-50">
                            {{ clearing ? 'Đang xóa...' : 'Xóa tất cả' }}
                        </button>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">Tóm tắt đơn hàng</h2>
                        
                        <div class="space-y-4 mb-6">
                            <div class="flex justify-between text-gray-600">
                                <span>Tạm tính:</span>
                                <span>{{ formatPrice(total) }}</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Phí vận chuyển:</span>
                                <span>Miễn phí</span>
                            </div>
                            <div class="border-t pt-4 flex justify-between text-lg font-bold text-gray-900">
                                <span>Tổng cộng:</span>
                                <span class="text-blue-600">{{ formatPrice(total) }}</span>
                            </div>
                        </div>

                        <button class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-300 font-semibold">
                            Thanh toán
                        </button>

                        <Link :href="route('user.products.index')" 
                              class="block mt-4 text-center text-blue-600 hover:text-blue-800">
                            ← Tiếp tục mua sắm
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Messages -->
            <div v-if="message" 
                 :class="['fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50', 
                          message.type === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700']">
                {{ message.text }}
            </div>
        </div>
    </UserLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import UserLayout from '@/Layouts/User/UserLayout.vue'
import { ref } from 'vue'
import axios from 'axios'

const props = defineProps({
    cartItems: Array,
    total: Number,
    count: Number
})

const updating = ref(null)
const removing = ref(null)
const clearing = ref(false)
const message = ref(null)

const formatPrice = (price) => {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(price)
}

const showMessage = (type, text) => {
    message.value = { type, text }
    setTimeout(() => {
        message.value = null
    }, 3000)
}

const updateQuantity = async (itemId, newQuantity) => {
    if (newQuantity < 1) return
    
    updating.value = itemId
    try {
        await axios.get('/sanctum/csrf-cookie', { withCredentials: true })
        
        const response = await axios.put(`/api/cart/${itemId}`, {
            quantity: newQuantity
        }, {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            withCredentials: true
        })

        showMessage('success', response.data.message || 'Đã cập nhật số lượng')
        router.reload({ only: ['cartItems', 'total', 'count', 'cartCount'] })
    } catch (error) {
        showMessage('error', error.response?.data?.message || 'Có lỗi xảy ra khi cập nhật số lượng')
    } finally {
        updating.value = null
    }
}

const removeItem = async (itemId) => {
    removing.value = itemId
    try {
        await axios.get('/sanctum/csrf-cookie', { withCredentials: true })
        
        const response = await axios.delete(`/api/cart/${itemId}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            withCredentials: true
        })

        showMessage('success', response.data.message || 'Đã xóa khỏi giỏ hàng')
        router.reload({ only: ['cartItems', 'total', 'count', 'cartCount'] })
    } catch (error) {
        showMessage('error', error.response?.data?.message || 'Có lỗi xảy ra khi xóa sản phẩm')
    } finally {
        removing.value = null
    }
}

const clearCart = async () => {
    if (!confirm('Bạn có chắc chắn muốn xóa tất cả sản phẩm khỏi giỏ hàng?')) {
        return
    }

    clearing.value = true
    try {
        await axios.get('/sanctum/csrf-cookie', { withCredentials: true })
        
        const response = await axios.delete('/api/cart', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            withCredentials: true
        })

        showMessage('success', response.data.message || 'Đã xóa tất cả sản phẩm')
        router.reload({ only: ['cartItems', 'total', 'count', 'cartCount'] })
    } catch (error) {
        showMessage('error', error.response?.data?.message || 'Có lỗi xảy ra khi xóa giỏ hàng')
    } finally {
        clearing.value = false
    }
}
</script>

