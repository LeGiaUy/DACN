<template>
    <UserLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Thanh toán</h1>
                <p class="text-gray-600 mt-2">Vui lòng điền thông tin giao hàng</p>
            </div>

            <form @submit.prevent="submitCheckout" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Checkout Form -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Shipping Information -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">Thông tin giao hàng</h2>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Họ và tên <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       v-model="form.shipping_name"
                                       required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Số điện thoại <span class="text-red-500">*</span>
                                </label>
                                <input type="tel" 
                                       v-model="form.shipping_phone"
                                       required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Email
                                </label>
                                <input type="email" 
                                       v-model="form.shipping_email"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Địa chỉ giao hàng <span class="text-red-500">*</span>
                                </label>
                                <textarea v-model="form.shipping_address"
                                          required
                                          rows="3"
                                          class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Ghi chú (tùy chọn)
                                </label>
                                <textarea v-model="form.notes"
                                          rows="3"
                                          placeholder="Ghi chú cho người giao hàng..."
                                          class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">Phương thức thanh toán</h2>
                        
                        <div class="space-y-3">
                            <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition"
                                   :class="form.payment_method === 'cod' ? 'border-blue-500 bg-blue-50' : 'border-gray-200'">
                                <input type="radio" 
                                       v-model="form.payment_method" 
                                       value="cod"
                                       class="mr-3 h-4 w-4 text-blue-600 focus:ring-blue-500">
                                <div class="flex-1">
                                    <div class="font-semibold text-gray-900">Thanh toán khi nhận hàng (COD)</div>
                                    <div class="text-sm text-gray-600">Thanh toán bằng tiền mặt khi nhận được hàng</div>
                                </div>
                            </label>

                            <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition"
                                   :class="form.payment_method === 'qr_code' ? 'border-blue-500 bg-blue-50' : 'border-gray-200'">
                                <input type="radio" 
                                       v-model="form.payment_method" 
                                       value="qr_code"
                                       class="mr-3 h-4 w-4 text-blue-600 focus:ring-blue-500">
                                <div class="flex-1">
                                    <div class="font-semibold text-gray-900">QR Code</div>
                                    <div class="text-sm text-gray-600">Quét mã QR để thanh toán nhanh chóng</div>
                                </div>
                            </label>

                            <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition"
                                   :class="form.payment_method === 'bank_card' ? 'border-blue-500 bg-blue-50' : 'border-gray-200'">
                                <input type="radio" 
                                       v-model="form.payment_method" 
                                       value="bank_card"
                                       class="mr-3 h-4 w-4 text-blue-600 focus:ring-blue-500">
                                <div class="flex-1">
                                    <div class="font-semibold text-gray-900">Thẻ ngân hàng</div>
                                    <div class="text-sm text-gray-600">Thanh toán bằng thẻ Visa, Mastercard</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Shipping Method -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">Phương thức giao hàng</h2>
                        
                        <div class="space-y-3">
                            <label class="flex items-center justify-between p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition"
                                   :class="form.shipping_method === 'standard' ? 'border-blue-500 bg-blue-50' : 'border-gray-200'"
                                   @click="updateShippingFee('standard')">
                                <div class="flex items-center flex-1">
                                    <input type="radio" 
                                           v-model="form.shipping_method" 
                                           value="standard"
                                           class="mr-3 h-4 w-4 text-blue-600 focus:ring-blue-500">
                                    <div>
                                        <div class="font-semibold text-gray-900">Giao hàng tiết kiệm</div>
                                        <div class="text-sm text-gray-600">5-7 ngày làm việc</div>
                                    </div>
                                </div>
                                <div class="font-semibold text-blue-600">{{ formatPrice(20000) }}</div>
                            </label>

                            <label class="flex items-center justify-between p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition"
                                   :class="form.shipping_method === 'fast' ? 'border-blue-500 bg-blue-50' : 'border-gray-200'"
                                   @click="updateShippingFee('fast')">
                                <div class="flex items-center flex-1">
                                    <input type="radio" 
                                           v-model="form.shipping_method" 
                                           value="fast"
                                           class="mr-3 h-4 w-4 text-blue-600 focus:ring-blue-500">
                                    <div>
                                        <div class="font-semibold text-gray-900">Giao hàng nhanh</div>
                                        <div class="text-sm text-gray-600">2-3 ngày làm việc</div>
                                    </div>
                                </div>
                                <div class="font-semibold text-blue-600">{{ formatPrice(35000) }}</div>
                            </label>

                            <label class="flex items-center justify-between p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition"
                                   :class="form.shipping_method === 'express' ? 'border-blue-500 bg-blue-50' : 'border-gray-200'"
                                   @click="updateShippingFee('express')">
                                <div class="flex items-center flex-1">
                                    <input type="radio" 
                                           v-model="form.shipping_method" 
                                           value="express"
                                           class="mr-3 h-4 w-4 text-blue-600 focus:ring-blue-500">
                                    <div>
                                        <div class="font-semibold text-gray-900">Giao hàng hỏa tốc</div>
                                        <div class="text-sm text-gray-600">Trong ngày hoặc ngày hôm sau</div>
                                    </div>
                                </div>
                                <div class="font-semibold text-blue-600">{{ formatPrice(50000) }}</div>
                            </label>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">Sản phẩm đặt hàng</h2>
                        
                        <div class="space-y-4">
                            <div v-for="item in cartItems" :key="item.id" 
                                 class="flex gap-4 pb-4 border-b last:border-0">
                                <img :src="item.product?.img_url || '/images/placeholder.jpg'" 
                                     :alt="item.product?.name"
                                     class="w-20 h-20 object-cover rounded-lg">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900">{{ item.product?.name }}</h3>
                                    <p class="text-sm text-gray-600">{{ item.product?.category?.name }}</p>
                                    <div v-if="item.color || item.size" class="text-sm text-gray-500 mt-1">
                                        <span v-if="item.color">Màu: {{ item.color }}</span>
                                        <span v-if="item.color && item.size"> | </span>
                                        <span v-if="item.size">Size: {{ item.size }}</span>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-1">Số lượng: {{ item.quantity }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-blue-600">{{ formatPrice(item.subtotal) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">Tóm tắt đơn hàng</h2>
                        
                        <div class="space-y-4 mb-6">
                            <div class="flex justify-between text-gray-600">
                                <span>Tạm tính:</span>
                                <span>{{ formatPrice(subtotal) }}</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Phí vận chuyển:</span>
                                <span>{{ formatPrice(currentShippingFee) }}</span>
                            </div>
                            <div class="border-t pt-4 flex justify-between text-lg font-bold text-gray-900">
                                <span>Tổng cộng:</span>
                                <span class="text-blue-600">{{ formatPrice(currentTotal) }}</span>
                            </div>
                        </div>

                        <button type="submit" 
                                :disabled="processing"
                                class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-300 font-semibold disabled:opacity-50 disabled:cursor-not-allowed">
                            {{ processing ? 'Đang xử lý...' : 'Tiếp tục thanh toán' }}
                        </button>

                        <Link :href="route('user.cart')" 
                              class="block mt-4 text-center text-blue-600 hover:text-blue-800">
                            ← Quay lại giỏ hàng
                        </Link>
                    </div>
                </div>
            </form>
        </div>
    </UserLayout>
</template>

<script setup>
import { Link, router, useForm } from '@inertiajs/vue3'
import UserLayout from '@/Layouts/User/UserLayout.vue'
import { ref, computed } from 'vue'

const props = defineProps({
    cartItems: Array,
    subtotal: Number,
    shippingFee: Number,
    total: Number,
    user: Object
})

const form = useForm({
    shipping_name: props.user?.name || '',
    shipping_phone: props.user?.phone || '',
    shipping_email: props.user?.email || '',
    shipping_address: props.user?.address || '',
    payment_method: 'cod',
    shipping_method: 'standard',
    notes: '',
})

const processing = ref(false)

// Shipping fees
const shippingFees = {
    standard: 20000,
    fast: 35000,
    express: 50000
}

// Computed shipping fee
const currentShippingFee = computed(() => {
    return shippingFees[form.shipping_method] || 20000
})

// Computed total
const currentTotal = computed(() => {
    return props.subtotal + currentShippingFee.value
})

const formatPrice = (price) => {
    const numPrice = parseFloat(price) || 0
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(numPrice)
}

const updateShippingFee = (method) => {
    form.shipping_method = method
}

const submitCheckout = () => {
    processing.value = true
    form.post(route('user.checkout.store'), {
        onFinish: () => {
            processing.value = false
        }
    })
}
</script>

