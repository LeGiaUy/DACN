<template>
    <UserLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Breadcrumb -->
            <nav class="flex mb-8" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <Link :href="route('user.home')" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                            </svg>
                            Trang chủ
                        </Link>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <Link :href="route('user.products.index')" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">Sản phẩm</Link>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ product.name }}</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Product Image -->
                <div class="space-y-4">
                    <div class="aspect-w-1 aspect-h-1">
                        <img :src="product.img_url || '/images/placeholder.jpg'" 
                             :alt="product.name"
                             class="w-full h-96 object-cover rounded-lg">
                    </div>
                </div>

                <!-- Product Info -->
                <div class="space-y-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ product.name }}</h1>
                        <div class="mt-2 flex items-center space-x-4">
                            <span class="text-3xl font-bold text-blue-600">{{ formatPrice(product.price) }}</span>
                            <span class="text-sm text-gray-500">{{ product.category?.name }}</span>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Mô tả sản phẩm</h3>
                        <p class="text-gray-600">{{ product.description }}</p>
                    </div>

                    <!-- Product Details -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <h4 class="font-semibold text-gray-900">Thương hiệu</h4>
                            <p class="text-gray-600">{{ product.brand?.name }}</p>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Số lượng</h4>
                            <p class="text-gray-600">{{ (product.total_quantity ?? product.quantity) }} sản phẩm</p>
                        </div>
                    </div>

                    <!-- Variant selection -->
                    <div v-if="(product.variants && product.variants.length) || (product.colors?.length || product.sizes?.length)">
                        <h4 class="font-semibold text-gray-900 mb-2">Chọn biến thể</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Màu sắc</label>
                                <select v-model="selectedColor" class="w-full p-2 border rounded" :disabled="availableColors.length === 0">
                                    <option :value="''">Chọn màu</option>
                                    <option v-for="c in availableColors" :key="c" :value="c">{{ c }}</option>
                                </select>
                                <p v-if="availableColors.length === 0" class="text-xs text-gray-500 mt-1">Không có màu sắc</p>
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Kích thước</label>
                                <select v-model="selectedSize" class="w-full p-2 border rounded" :disabled="availableSizes.length === 0 || (variants.length > 0 && !selectedColor)">
                                    <option :value="''">Chọn kích thước</option>
                                    <option v-for="s in availableSizes" :key="s" :value="s">{{ s }}</option>
                                </select>
                                <p v-if="variants.length > 0 && !selectedColor" class="text-xs text-gray-500 mt-1">Vui lòng chọn màu trước</p>
                                <p v-else-if="availableSizes.length === 0" class="text-xs text-gray-500 mt-1">Không có kích thước</p>
                            </div>
                        </div>
                        <div class="mt-2 text-sm" v-if="currentVariant">
                            <span :class="currentVariant.quantity > 0 ? 'text-green-600' : 'text-red-600'">
                                {{ currentVariant.quantity > 0 ? `Còn ${currentVariant.quantity} sản phẩm` : 'Hết hàng' }}
                            </span>
                        </div>
                    </div>

                    <!-- Quantity selector -->
                    <div v-if="canAddToCart" class="flex items-center space-x-4">
                        <label class="text-sm font-semibold text-gray-900">Số lượng:</label>
                        <div class="flex items-center space-x-2">
                            <button @click="decreaseQuantity" :disabled="quantity <= 1" 
                                class="px-3 py-1 border rounded disabled:opacity-50 disabled:cursor-not-allowed">
                                -
                            </button>
                            <input type="number" v-model.number="quantity" min="1" :max="maxQuantity"
                                class="w-20 px-2 py-1 border rounded text-center" />
                            <button @click="increaseQuantity" :disabled="quantity >= maxQuantity"
                                class="px-3 py-1 border rounded disabled:opacity-50 disabled:cursor-not-allowed">
                                +
                            </button>
                        </div>
                    </div>

                    <!-- Add to Cart -->
                    <div class="space-y-4">
                        <div class="flex space-x-4">
                            <button @click="addToCart" :disabled="!canAddToCart || addingToCart" 
                                :class="['flex-1 px-6 py-3 rounded-lg transition duration-300 font-semibold', 
                                    canAddToCart && !addingToCart ? 'bg-blue-600 text-white hover:bg-blue-700' : 'bg-gray-300 text-gray-600 cursor-not-allowed']">
                                {{ addingToCart ? 'Đang thêm...' : 'Thêm vào giỏ hàng' }}
                            </button>
                            <button class="px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition duration-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                            </button>
                        </div>
                        <div v-if="cartMessage" :class="['text-sm p-2 rounded', cartMessage.type === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700']">
                            {{ cartMessage.text }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Products -->
            <div v-if="relatedProducts.length > 0" class="mt-16">
                <h2 class="text-2xl font-bold text-gray-900 mb-8">Sản phẩm liên quan</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div v-for="relatedProduct in relatedProducts" :key="relatedProduct.id" 
                         class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                        <div class="aspect-w-16 aspect-h-9">
                            <img :src="relatedProduct.img_url || '/images/placeholder.jpg'" 
                                 :alt="relatedProduct.name"
                                 class="w-full h-48 object-cover">
                        </div>
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ relatedProduct.name }}</h3>
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ relatedProduct.description }}</p>
                            <div class="flex items-center justify-between">
                                <span class="text-xl font-bold text-blue-600">{{ formatPrice(relatedProduct.price) }}</span>
                                <Link :href="route('user.products.show', relatedProduct.id)" 
                                      class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">
                                    Xem
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </UserLayout>
</template>

<script setup>
import { Link, usePage, router } from '@inertiajs/vue3'
import UserLayout from '@/Layouts/User/UserLayout.vue'
import { computed, ref, watch } from 'vue'
import axios from 'axios'

const props = defineProps({
    product: Object,
    relatedProducts: Array
})

const page = usePage()
const selectedColor = ref('')
const selectedSize = ref('')
const quantity = ref(1)
const addingToCart = ref(false)
const cartMessage = ref(null)

const variants = computed(() => Array.isArray(props.product?.variants) ? props.product.variants : [])

const normalize = (val) => (val ?? '').toString().trim()

const availableColors = computed(() => {
    // When variants exist, always derive colors from variants to ensure accuracy
    if (variants.value.length > 0) {
        const colors = variants.value
            .map(v => normalize(v.color))
            .filter(c => c !== '')
        return Array.from(new Set(colors))
    }

    // Fallback to product.colors when there are no variants
    if (Array.isArray(props.product?.colors) && props.product.colors.length > 0) {
        return props.product.colors
            .map(c => normalize(c))
            .filter(c => c !== '')
    }

    return []
})

const availableSizes = computed(() => {
    if (variants.value.length > 0) {
        const selected = normalize(selectedColor.value)
        const filteredVariants = selected
            ? variants.value.filter(v => normalize(v.color) === selected)
            : []

        const sizes = filteredVariants
            .map(v => normalize(v.size))
            .filter(s => s !== '')
        return Array.from(new Set(sizes))
    }

    if (Array.isArray(props.product?.sizes) && props.product.sizes.length > 0) {
        return props.product.sizes
            .map(s => normalize(s))
            .filter(s => s !== '')
    }

    return []
})

const currentVariant = computed(() => {
    if (!variants.value.length) return null
    const selColor = normalize(selectedColor.value)
    const selSize = normalize(selectedSize.value)
    return variants.value.find(v => normalize(v.color) === selColor && normalize(v.size) === selSize) || null
})

const maxQuantity = computed(() => {
    if (!variants.value.length) return props.product?.quantity || 0
    if (!currentVariant.value) return 0
    return currentVariant.value.quantity || 0
})

const canAddToCart = computed(() => {
    if (!variants.value.length) return (props.product?.quantity || 0) > 0
    if (!currentVariant.value) return false
    return (currentVariant.value.quantity || 0) > 0
})

// Watch quantity to ensure it doesn't exceed max
watch(quantity, (newVal) => {
    if (newVal > maxQuantity.value) {
        quantity.value = maxQuantity.value
    }
    if (newVal < 1) {
        quantity.value = 1
    }
})

// Watch color selection to reset size (since available sizes depend on color)
watch(selectedColor, () => {
    selectedSize.value = ''
    quantity.value = 1
    cartMessage.value = null
})

// Watch variant selection to reset quantity
watch(selectedSize, () => {
    quantity.value = 1
    cartMessage.value = null
})

const increaseQuantity = () => {
    if (quantity.value < maxQuantity.value) {
        quantity.value++
    }
}

const decreaseQuantity = () => {
    if (quantity.value > 1) {
        quantity.value--
    }
}

const addToCart = async () => {
    if (!canAddToCart.value) return

    // Check if user is authenticated
    const user = page.props.auth?.user
    if (!user) {
        cartMessage.value = {
            type: 'error',
            text: 'Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng'
        }
        setTimeout(() => {
            cartMessage.value = null
        }, 3000)
        return
    }

    addingToCart.value = true
    cartMessage.value = null

    try {
        // Ensure CSRF cookie exists for session-protected routes
        await axios.get('/sanctum/csrf-cookie', { withCredentials: true })

        const response = await axios.post('/api/cart', {
            product_id: props.product.id,
            color: selectedColor.value || null,
            size: selectedSize.value || null,
            quantity: quantity.value
        }, {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            withCredentials: true
        })

        cartMessage.value = {
            type: 'success',
            text: response.data.message || 'Đã thêm vào giỏ hàng thành công!'
        }

        // Reset quantity after successful add
        quantity.value = 1

        // Reload page to update cart count in header
        router.reload({ only: ['cartCount'] })

        setTimeout(() => {
            cartMessage.value = null
        }, 3000)
    } catch (error) {
        console.error('Error adding to cart:', error)
        cartMessage.value = {
            type: 'error',
            text: error.response?.data?.message || 'Có lỗi xảy ra khi thêm vào giỏ hàng'
        }
        setTimeout(() => {
            cartMessage.value = null
        }, 3000)
    } finally {
        addingToCart.value = false
    }
}

const formatPrice = (price) => {
    const numPrice = parseFloat(price) || 0
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(numPrice)
}
</script>
