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

            <!-- Payment Method Tabs -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex border-b border-gray-200 mb-6">
                    <button type="button"
                            @click="paymentType = 'qr_code'"
                            class="flex-1 px-4 py-3 text-center font-semibold transition"
                            :class="paymentType === 'qr_code' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:text-gray-900'">
                        QR Code
                    </button>
                    <button type="button"
                            @click="paymentType = 'bank_card'"
                            class="flex-1 px-4 py-3 text-center font-semibold transition"
                            :class="paymentType === 'bank_card' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:text-gray-900'">
                        Thẻ ngân hàng
                    </button>
                </div>

                <!-- QR Code Payment -->
                <div v-show="paymentType === 'qr_code'" class="text-center">
                    <div class="mb-6">
                        <div class="inline-block p-4 bg-white border-2 border-gray-300 rounded-lg">
                            <!-- QR Code Placeholder - In production, generate actual QR code -->
                            <div class="w-64 h-64 bg-gray-100 flex items-center justify-center border-2 border-dashed border-gray-300 rounded mx-auto">
                                <div class="text-center">
                                    <svg class="w-32 h-32 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                                    </svg>
                                    <p class="text-sm text-gray-500 mt-2">QR Code</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-4">Quét mã QR bằng ứng dụng ngân hàng của bạn để thanh toán</p>
                    <p class="text-sm text-gray-500 mb-6">Số tiền: <span class="font-bold text-blue-600">{{ formatPrice(order.total) }}</span></p>
                    <button type="button"
                            @click="submitQRPayment"
                            :disabled="processing"
                            class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-300 font-semibold disabled:opacity-50 disabled:cursor-not-allowed">
                        {{ processing ? 'Đang xử lý...' : 'Đã thanh toán' }}
                    </button>
                </div>

                <!-- Bank Card Payment -->
                <form v-show="paymentType === 'bank_card'" @submit.prevent="submitPayment">
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
const paymentType = ref(props.order.payment_method === 'qr_code' ? 'qr_code' : 'bank_card')

const formatPrice = (price) => {
    const numPrice = parseFloat(price) || 0
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(numPrice)
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
        card_number: cardNumber,
        payment_type: 'bank_card'
    })).post(route('user.checkout.processPayment', props.order.id), {
        onFinish: () => {
            processing.value = false
        }
    })
}

const submitQRPayment = () => {
    processing.value = true
    
    form.transform((data) => ({
        ...data,
        payment_type: 'qr_code'
    })).post(route('user.checkout.processPayment', props.order.id), {
        onFinish: () => {
            processing.value = false
        }
    })
}
</script>

