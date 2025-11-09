<template>
    <UserLayout>
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8 text-center">
                <div class="flex items-center justify-center mb-4">
                    <div class="bg-red-600 text-white px-6 py-3 rounded-lg font-bold text-xl">
                        VIETCOMBANK
                    </div>
                </div>
                <h1 class="text-3xl font-bold text-gray-900">Thanh toán trực tuyến</h1>
                <p class="text-gray-600 mt-2">Mã đơn hàng: {{ order.order_number }}</p>
            </div>

            <!-- Order Summary -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Thông tin đơn hàng</h2>
                <div class="space-y-2 text-gray-600">
                    <div class="flex justify-between">
                        <span>Tạm tính:</span>
                        <span>{{ formatPrice(order.subtotal) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Phí vận chuyển:</span>
                        <span>{{ formatPrice(order.shipping_fee) }}</span>
                    </div>
                    <div class="border-t pt-2 flex justify-between text-lg font-bold text-gray-900">
                        <span>Tổng cộng:</span>
                        <span class="text-blue-600">{{ formatPrice(order.total) }}</span>
                    </div>
                </div>
            </div>

            <!-- Payment Form -->
            <form @submit.prevent="submitPayment" class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Thông tin thẻ thanh toán</h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Số thẻ <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               v-model="form.card_number"
                               placeholder="1234 5678 9012 3456"
                               maxlength="19"
                               @input="formatCardNumber"
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-1">Nhập số thẻ 16-19 chữ số</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tên chủ thẻ <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               v-model="form.card_name"
                               placeholder="NGUYEN VAN A"
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent uppercase">
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Ngày hết hạn <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-2">
                                <select v-model="form.expiry_month"
                                        required
                                        class="flex-1 px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Tháng</option>
                                    <option v-for="month in 12" :key="month" :value="month">
                                        {{ String(month).padStart(2, '0') }}
                                    </option>
                                </select>
                                <select v-model="form.expiry_year"
                                        required
                                        class="flex-1 px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Năm</option>
                                    <option v-for="year in 10" :key="year" :value="currentYear + year - 1">
                                        {{ currentYear + year - 1 }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                CVV <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   v-model="form.cvv"
                                   placeholder="123"
                                   maxlength="4"
                                   required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>

                    <!-- Demo Card Info -->
                    <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mt-6">
                        <p class="text-sm font-semibold text-blue-900 mb-2">💡 Thông tin thẻ demo:</p>
                        <p class="text-xs text-blue-800">Số thẻ: Bất kỳ (16-19 số)</p>
                        <p class="text-xs text-blue-800">Tên: Bất kỳ</p>
                        <p class="text-xs text-blue-800">Ngày hết hạn: Bất kỳ (từ năm hiện tại trở đi)</p>
                        <p class="text-xs text-blue-800">CVV: Bất kỳ (3-4 số)</p>
                        <p class="text-xs text-blue-600 mt-2">Hệ thống sẽ tự động mô phỏng thanh toán (90% thành công)</p>
                    </div>

                    <div class="flex gap-4 mt-6">
                        <button type="submit" 
                                :disabled="processing"
                                class="flex-1 px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition duration-300 font-semibold disabled:opacity-50 disabled:cursor-not-allowed">
                            {{ processing ? 'Đang xử lý...' : 'Xác nhận thanh toán' }}
                        </button>
                        <Link :href="route('user.cart')" 
                              class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition duration-300 font-semibold">
                            Hủy
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
    order: Object
})

const form = useForm({
    card_number: '',
    card_name: '',
    expiry_month: '',
    expiry_year: '',
    cvv: '',
})

const processing = ref(false)
const currentYear = new Date().getFullYear()

const formatPrice = (price) => {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(price)
}

const formatCardNumber = (event) => {
    let value = event.target.value.replace(/\s/g, '')
    let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value
    form.card_number = formattedValue
}

const submitPayment = () => {
    // Remove spaces from card number
    const cardNumber = form.card_number.replace(/\s/g, '')
    
    if (cardNumber.length < 16 || cardNumber.length > 19) {
        alert('Số thẻ phải có từ 16 đến 19 chữ số')
        return
    }

    processing.value = true
    
    form.transform((data) => ({
        ...data,
        card_number: cardNumber
    })).post(route('user.checkout.processPayment', props.order.id), {
        onFinish: () => {
            processing.value = false
        }
    })
}
</script>

