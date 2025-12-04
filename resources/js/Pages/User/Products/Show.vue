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
                <!-- Product Image Gallery -->
                <div class="flex gap-4">
                    <!-- Thumbnails on the left (vertical scroll) -->
                    <div v-if="productImages.length > 1" class="flex-shrink-0 pt-1">
                        <div ref="thumbnailContainer"
                             class="flex flex-col gap-3 overflow-y-auto max-h-[600px] scrollbar-hide scroll-smooth px-2 pt-4 pb-4"
                             style="scrollbar-width: none; -ms-overflow-style: none;">
                            <button
                                v-for="(image, index) in productImages"
                                :key="index"
                                @click="selectImage(index)"
                                :class="[
                                    'flex-shrink-0 w-20 h-20 rounded-lg border-2 overflow-hidden transition-all cursor-pointer bg-gray-100 flex items-center justify-center',
                                    currentImageIndex === index
                                        ? 'border-blue-600 ring-2 ring-blue-200 scale-105 shadow-md'
                                        : 'border-gray-200 hover:border-gray-400 hover:scale-105'
                                ]"
                                :title="`Hình ${index + 1}`">
                                <img :src="image"
                                     :alt="`${product.name} - Hình ${index + 1}`"
                                     class="w-full h-full object-contain"
                                     @error="handleImageError($event, image)"
                                     @load="handleImageLoad($event)">
                            </button>
                        </div>
                    </div>

                    <!-- Main Image -->
                    <div class="flex-1 relative aspect-square overflow-hidden rounded-lg bg-gray-100 group flex items-center justify-center">
                        <img :src="selectedImage"
                             :alt="product.name"
                             class="w-full h-full object-contain transition-all duration-300 cursor-zoom-in"
                             @click="openImageModal(selectedImage)"
                             @error="handleImageError($event, selectedImage)"
                             @load="handleImageLoad($event)">
                        <div v-if="currentImageColor" class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-sm font-medium text-gray-700">
                            Màu: {{ currentImageColor }}
                        </div>
                        <!-- Image counter -->
                        <div v-if="productImages.length > 1" class="absolute bottom-4 right-4 bg-black/50 text-white px-3 py-1 rounded-full text-sm">
                            {{ currentImageIndex + 1 }} / {{ productImages.length }}
                        </div>
                        <!-- Navigation arrows -->
                        <button
                            v-if="productImages.length > 1"
                            @click="previousImage"
                            class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white rounded-full p-2 shadow-lg transition-all opacity-0 group-hover:opacity-100">
                            <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                        <button
                            v-if="productImages.length > 1"
                            @click="nextImage"
                            class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white rounded-full p-2 shadow-lg transition-all opacity-0 group-hover:opacity-100">
                            <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
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
                        <h4 class="font-semibold text-gray-900 mb-4">Chọn biến thể</h4>

                        <!-- Color Selection -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Màu sắc
                                <span v-if="selectedColor" class="text-gray-500 font-normal">({{ selectedColor }})</span>
                            </label>
                            <div v-if="availableColors.length > 0" class="flex flex-wrap gap-2">
                                <button
                                    v-for="color in availableColors"
                                    :key="color"
                                    @click="selectColor(color)"
                                    :class="[
                                        'px-4 py-2 rounded-lg border-2 font-medium text-sm transition-all duration-200',
                                        'hover:scale-105 hover:shadow-md',
                                        selectedColor === color
                                            ? 'border-blue-600 bg-blue-50 text-blue-700 ring-2 ring-blue-200'
                                            : 'border-gray-200 bg-white text-gray-700 hover:border-gray-300'
                                    ]">
                                    <div class="flex items-center gap-2">
                                        <div
                                            :style="{ backgroundColor: getColorHex(color) }"
                                            class="w-5 h-5 rounded-full border border-gray-300 flex-shrink-0"
                                            :class="getColorHex(color) ? '' : 'bg-gradient-to-br from-gray-200 to-gray-300'">
                                        </div>
                                        <span>{{ color }}</span>
                                    </div>
                                </button>
                            </div>
                            <p v-else class="text-sm text-gray-500">Không có màu sắc</p>
                        </div>

                        <!-- Size Selection -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Kích thước
                                <span v-if="selectedSize" class="text-gray-500 font-normal">({{ selectedSize }})</span>
                            </label>
                            <div v-if="availableSizes.length > 0 && (!variants.length || selectedColor)" class="flex flex-wrap gap-2">
                                <button
                                    v-for="size in availableSizes"
                                    :key="size"
                                    @click="selectSize(size)"
                                    :disabled="!canSelectSize(size)"
                                    :class="[
                                        'px-4 py-2 rounded-lg border-2 font-medium text-sm transition-all duration-200 min-w-[60px]',
                                        canSelectSize(size)
                                            ? selectedSize === size
                                                ? 'border-blue-600 bg-blue-600 text-white ring-2 ring-blue-200'
                                                : 'border-gray-200 bg-white text-gray-700 hover:border-blue-300 hover:bg-blue-50'
                                            : 'border-gray-100 bg-gray-50 text-gray-400 cursor-not-allowed opacity-50'
                                    ]">
                                    {{ size }}
                                </button>
                            </div>
                            <div v-else-if="variants.length > 0 && !selectedColor" class="text-sm text-amber-600 bg-amber-50 border border-amber-200 rounded-lg p-3">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Vui lòng chọn màu trước</span>
                                </div>
                            </div>
                            <p v-else-if="availableSizes.length === 0" class="text-sm text-gray-500">Không có kích thước</p>
                        </div>

                        <!-- Stock Status -->
                        <div v-if="currentVariant || (!variants.length && product.quantity)" class="mt-4 p-3 rounded-lg"
                             :class="getStockStatusClass()">
                            <div class="flex items-center gap-2">
                                <svg v-if="getStockStatusIcon()" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getStockStatusIcon()"></path>
                                </svg>
                                <span class="font-medium">{{ getStockStatusText() }}</span>
                            </div>
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
                        <div class="aspect-w-16 aspect-h-9 bg-gray-100 flex items-center justify-center">
                            <img :src="relatedProduct.img_url || '/images/placeholder.jpg'"
                                 :alt="relatedProduct.name"
                                 class="w-full h-48 object-contain"
                                 @error="handleImageError($event, relatedProduct.img_url)"
                                 @load="handleImageLoad($event)">
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

        <!-- Image Modal -->
        <div v-if="showImageModal"
             @click="closeImageModal"
             class="fixed inset-0 z-50 bg-black/90 flex items-center justify-center p-4">
            <button @click="closeImageModal"
                    class="absolute top-4 right-4 text-white hover:text-gray-300 z-10">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <img :src="modalImage"
                 :alt="product.name"
                 @click.stop
                 @error="handleImageError($event, modalImage)"
                 @load="handleImageLoad($event)"
                 class="max-w-full max-h-[90vh] object-contain rounded-lg">
        </div>
    </UserLayout>
</template>

<script setup>
import { Link, usePage, router } from '@inertiajs/vue3'
import UserLayout from '@/Layouts/User/UserLayout.vue'
import { computed, ref, watch, onMounted } from 'vue'
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
const currentImageIndex = ref(0)
const thumbnailContainer = ref(null)
const canScrollLeft = ref(false)
const canScrollRight = ref(false)
const showImageModal = ref(false)
const modalImage = ref('')
const imageErrors = ref(new Set())

// Handle image load errors
const handleImageError = (event, imageUrl) => {
    const img = event.target
    const originalSrc = imageUrl || img.src
    
    // Mark this image URL as errored
    if (originalSrc && originalSrc !== '/images/placeholder.jpg') {
        imageErrors.value.add(originalSrc)
    }
    
    // If image fails to load and it's not already placeholder, try placeholder
    if (img.src !== '/images/placeholder.jpg' && !img.src.includes('placeholder')) {
        img.src = '/images/placeholder.jpg'
        img.onerror = () => {
            // If placeholder also fails, hide the image
            img.style.display = 'none'
        }
    } else {
        // If placeholder also fails, hide the image
        img.style.display = 'none'
    }
}

// Handle successful image load
const handleImageLoad = (event) => {
    const img = event.target
    // Ensure image is visible
    img.style.display = ''
    // Remove from error set if it was there
    if (img.src && imageErrors.value.has(img.src)) {
        imageErrors.value.delete(img.src)
    }
}

const variants = computed(() => {
    if (!Array.isArray(props.product?.variants)) {
        return [];
    }
    // Sort by id (or created order) so older variants appear first
    return [...props.product.variants].sort((a, b) => {
        const aId = a?.id ?? 0;
        const bId = b?.id ?? 0;
        return aId - bId;
    });
})

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

// Store color-image mapping (can be extended to fetch from API or product data)
const colorImageMap = ref({})

// Initialize color-image mapping from product variants
const initializeColorImages = () => {
    const map = {}
    const baseUrl = props.product.img_url || ''

    if (variants.value.length > 0) {
        // Priority: Use img_url from variants if available
        variants.value.forEach(variant => {
            if (variant.img_url && variant.color) {
                const color = normalize(variant.color)
                // Use the first variant's image for each color, or prefer variants with images
                if (!map[color] || variant.img_url) {
                    map[color] = variant.img_url
                }
            }
        })
    }

    // For colors without specific variant images, try to construct URL based on color name
    // This is a fallback if variants don't have img_url
    availableColors.value.forEach(color => {
        const normalizedColor = normalize(color)
        if (!map[normalizedColor] && baseUrl) {
            const colorSlug = normalizedColor.toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, '')
            // Try common patterns
            const extension = baseUrl.match(/\.(jpg|jpeg|png|webp|gif)$/i)?.[0] || '.jpg'
            const basePath = baseUrl.replace(/\.[^.]+$/, '')
            // Store potential URL (in production, you'd verify these exist)
            map[normalizedColor] = `${basePath}_${colorSlug}${extension}`
        }
    })

    colorImageMap.value = map
}

// Initialize on mount
onMounted(() => {
    initializeColorImages()

    // Set up scroll listener for thumbnails
    if (thumbnailContainer.value) {
        thumbnailContainer.value.addEventListener('scroll', checkScrollability)
    }

    // Check scrollability on resize
    window.addEventListener('resize', checkScrollability)

    // Initial check
    setTimeout(() => {
        checkScrollability()
    }, 100)
})

// Get all product images - only one image per color (not per variant)
const productImages = computed(() => {
    const images = []
    const baseImage = props.product.img_url || '/images/placeholder.jpg'
    const seenImages = new Set()
    const colorImageMap_local = {}

    // First, collect one image per color from variants
    if (variants.value.length > 0) {
        variants.value.forEach(variant => {
            if (variant.img_url && variant.color) {
                const color = normalize(variant.color)
                // Only keep the first image for each color
                if (!colorImageMap_local[color]) {
                    colorImageMap_local[color] = variant.img_url
                }
            }
        })
    }

    // Add images from color mapping (one per color)
    Object.entries(colorImageMap.value).forEach(([color, imgUrl]) => {
        if (imgUrl && !colorImageMap_local[normalize(color)]) {
            colorImageMap_local[normalize(color)] = imgUrl
        }
    })

    // Add main product image first if no color images
    if (Object.keys(colorImageMap_local).length === 0 && baseImage) {
        images.push(baseImage)
        seenImages.add(baseImage)
    } else {
        // Add one image per color
        Object.values(colorImageMap_local).forEach(imgUrl => {
            if (imgUrl && !seenImages.has(imgUrl)) {
                images.push(imgUrl)
                seenImages.add(imgUrl)
            }
        })
    }

    // If no images found, use placeholder
    if (images.length === 0) {
        images.push('/images/placeholder.jpg')
    }

    // Filter out invalid images (those that have errored)
    return images.filter(img => !imageErrors.value.has(img))
})

// Get color for current image
const currentImageColor = computed(() => {
    if (!selectedImage.value) return null

    // Find which color this image belongs to
    const currentImg = selectedImage.value

    // Check variants first
    if (variants.value.length > 0) {
        const variant = variants.value.find(v => v.img_url === currentImg)
        if (variant?.color) {
            return variant.color
        }
    }

    // Check color mapping
    for (const [color, imgUrl] of Object.entries(colorImageMap.value)) {
        if (imgUrl === currentImg) {
            return color
        }
    }

    // If selected color is set, use it
    if (selectedColor.value) {
        return selectedColor.value
    }

    return null
})

// Computed property for current product image based on selected color
const currentProductImage = computed(() => {
    const baseImage = props.product.img_url || '/images/placeholder.jpg'

    // If color is selected, try to get color-specific image from variant
    if (selectedColor.value) {
        const color = normalize(selectedColor.value)

        // Priority 1: Find variant with exact color match that has img_url
        const colorVariant = variants.value.find(v =>
            normalize(v.color) === color && v.img_url
        )
        if (colorVariant?.img_url) {
            return colorVariant.img_url
        }

        // Priority 2: Use color-image mapping (from initializeColorImages)
        const colorImage = colorImageMap.value[color]
        if (colorImage && colorImage !== baseImage) {
            return colorImage
        }
    }

    // Default to product image
    return baseImage
})

// Selected image (from gallery)
const selectedImage = computed(() => {
    if (productImages.value.length > 0 && currentImageIndex.value < productImages.value.length) {
        const img = productImages.value[currentImageIndex.value]
        // If image has errored, try next one or fallback
        if (imageErrors.value.has(img)) {
            // Try next image
            const nextIndex = (currentImageIndex.value + 1) % productImages.value.length
            if (nextIndex !== currentImageIndex.value && !imageErrors.value.has(productImages.value[nextIndex])) {
                return productImages.value[nextIndex]
            }
            // Fallback to placeholder
            return '/images/placeholder.jpg'
        }
        return img
    }
    const currentImg = currentProductImage.value
    // If current image has errored, use placeholder
    if (imageErrors.value.has(currentImg)) {
        return '/images/placeholder.jpg'
    }
    return currentImg
})

// Color to hex mapping for common Vietnamese color names
const colorMap = {
    'đỏ': '#DC2626', 'red': '#DC2626',
    'xanh dương': '#2563EB', 'xanh': '#2563EB', 'blue': '#2563EB',
    'xanh lá': '#16A34A', 'green': '#16A34A',
    'vàng': '#FACC15', 'yellow': '#FACC15',
    'cam': '#FB923C', 'orange': '#FB923C',
    'tím': '#9333EA', 'purple': '#9333EA',
    'hồng': '#EC4899', 'pink': '#EC4899',
    'đen': '#1F2937', 'black': '#1F2937',
    'trắng': '#FFFFFF', 'white': '#FFFFFF',
    'xám': '#6B7280', 'gray': '#6B7280', 'grey': '#6B7280', 'ghi': '#6B7280',
    'nâu': '#92400E', 'brown': '#92400E',
    'be': '#F5F5DC', 'beige': '#F5F5DC',
    'xanh navy': '#1E3A8A', 'navy': '#1E3A8A',
    'xanh ngọc': '#06B6D4', 'cyan': '#06B6D4',
}

const getColorHex = (colorName) => {
    if (!colorName) return null
    const normalized = normalize(colorName).toLowerCase()
    // Direct match
    if (colorMap[normalized]) return colorMap[normalized]
    // Partial match
    for (const [key, value] of Object.entries(colorMap)) {
        if (normalized.includes(key) || key.includes(normalized)) {
            return value
        }
    }
    return null
}

const selectColor = (color) => {
    selectedColor.value = color
    selectedSize.value = ''
    quantity.value = 1
    cartMessage.value = null
}

const selectSize = (size) => {
    if (canSelectSize(size)) {
        selectedSize.value = size
        quantity.value = 1
        cartMessage.value = null
    }
}

const canSelectSize = (size) => {
    if (variants.value.length === 0) return true
    if (!selectedColor.value) return false
    const selColor = normalize(selectedColor.value)
    return variants.value.some(v =>
        normalize(v.color) === selColor &&
        normalize(v.size) === normalize(size) &&
        (v.quantity || 0) > 0
    )
}

const getStockStatusClass = () => {
    const qty = currentVariant.value?.quantity ?? (props.product.quantity || 0)
    if (qty > 10) return 'bg-green-50 border border-green-200 text-green-700'
    if (qty > 0) return 'bg-amber-50 border border-amber-200 text-amber-700'
    return 'bg-red-50 border border-red-200 text-red-700'
}

const getStockStatusText = () => {
    const qty = currentVariant.value?.quantity ?? (props.product.quantity || 0)
    if (qty > 10) return `Còn ${qty} sản phẩm`
    if (qty > 0) return `Chỉ còn ${qty} sản phẩm - Đặt hàng ngay!`
    return 'Hết hàng'
}

const getStockStatusIcon = () => {
    const qty = currentVariant.value?.quantity ?? (props.product.quantity || 0)
    if (qty > 0) {
        return 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
    }
    return 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'
}

// Watch color selection to reset size (since available sizes depend on color)
watch(selectedColor, () => {
    selectedSize.value = ''
    quantity.value = 1
    cartMessage.value = null

    // Update image index when color changes
    if (selectedColor.value) {
        const color = normalize(selectedColor.value)

        // Try to find variant image for this color
        const colorVariant = variants.value.find(v =>
            normalize(v.color) === color && v.img_url
        )

        if (colorVariant?.img_url) {
            const imageIndex = productImages.value.findIndex(img => img === colorVariant.img_url)
            if (imageIndex !== -1) {
                currentImageIndex.value = imageIndex
                setTimeout(() => scrollToThumbnail(imageIndex), 100)
                return
            }
        }

        // Try color mapping
        const colorImage = colorImageMap.value[color]
        if (colorImage) {
            const imageIndex = productImages.value.findIndex(img => img === colorImage)
            if (imageIndex !== -1) {
                currentImageIndex.value = imageIndex
                setTimeout(() => scrollToThumbnail(imageIndex), 100)
            }
        }
    }
})

// Methods for image gallery (defined before watchers to avoid initialization errors)
const checkScrollability = () => {
    if (thumbnailContainer.value) {
        const container = thumbnailContainer.value
        // For vertical scroll
        canScrollLeft.value = container.scrollTop > 0
        canScrollRight.value = container.scrollTop < (container.scrollHeight - container.clientHeight - 1)
    }
}

const scrollToThumbnail = (index) => {
    if (thumbnailContainer.value) {
        const container = thumbnailContainer.value
        const thumbnails = container.querySelectorAll('button')
        if (thumbnails[index]) {
            const thumbnail = thumbnails[index]
            const containerRect = container.getBoundingClientRect()
            const thumbnailRect = thumbnail.getBoundingClientRect()

            // For vertical scroll
            const scrollTop = thumbnail.offsetTop - container.offsetTop - (containerRect.height / 2) + (thumbnailRect.height / 2)
            container.scrollTo({
                top: container.scrollTop + scrollTop,
                behavior: 'smooth'
            })
        }
    }
    checkScrollability()
}

const selectImage = (index) => {
    currentImageIndex.value = index
    scrollToThumbnail(index)
}

const nextImage = () => {
    if (currentImageIndex.value < productImages.value.length - 1) {
        currentImageIndex.value++
        scrollToThumbnail(currentImageIndex.value)
    } else {
        currentImageIndex.value = 0
        scrollToThumbnail(0)
    }
}

const previousImage = () => {
    if (currentImageIndex.value > 0) {
        currentImageIndex.value--
        scrollToThumbnail(currentImageIndex.value)
    } else {
        currentImageIndex.value = productImages.value.length - 1
        scrollToThumbnail(currentImageIndex.value)
    }
}

// Watch productImages to update scroll indicators and reset index if needed
watch(productImages, (newImages) => {
    if (newImages.length > 0) {
        // Ensure currentImageIndex is within bounds
        if (currentImageIndex.value >= newImages.length) {
            currentImageIndex.value = 0
        }
        checkScrollability()
    }
}, { immediate: true })

// Watch colorImageMap to update product images
watch(colorImageMap, () => {
    // Force recomputation of productImages
    if (productImages.value.length > 0) {
        checkScrollability()
    }
}, { deep: true })

const openImageModal = (image) => {
    modalImage.value = image
    showImageModal.value = true
}

const closeImageModal = () => {
    showImageModal.value = false
}

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

<style scoped>
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

/* Smooth scrolling for thumbnails */
.scroll-smooth {
    scroll-behavior: smooth;
}
</style>
