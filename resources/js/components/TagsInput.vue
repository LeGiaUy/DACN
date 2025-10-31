<template>
    <div>
        <div class="flex flex-wrap items-center gap-2 border border-gray-300 rounded p-2">
            <span v-for="(tag, index) in internalTags" :key="index" class="inline-flex items-center bg-teal-50 text-teal-700 border border-teal-200 rounded px-2 py-1 text-sm">
                {{ tag }}
                <button type="button" class="ml-1 text-teal-600 hover:text-teal-800" @click="removeTag(index)">×</button>
            </span>
            <input
                ref="inputRef"
                type="text"
                :placeholder="placeholder"
                class="flex-1 min-w-[8rem] outline-none"
                v-model="current"
                @keydown.enter.prevent="commitCurrent"
                @keydown.tab.prevent="commitCurrent"
                @keydown.",".prevent="commitCurrent"
                @blur="commitCurrent"
                @paste="onPaste"
            />
        </div>
        <div v-if="showSuggestions" class="mt-2 flex flex-wrap gap-2">
            <button
                v-for="(sug, i) in filteredSuggestions"
                :key="i"
                type="button"
                class="text-xs px-2 py-1 border rounded hover:bg-gray-50"
                @click="addTag(sug)"
            >
                + {{ sug }}
            </button>
        </div>
    </div>
</template>

<script>
export default {
    name: 'TagsInput',
    props: {
        modelValue: {
            type: Array,
            default: () => []
        },
        placeholder: {
            type: String,
            default: ''
        },
        suggestions: {
            type: Array,
            default: () => []
        },
        allowDuplicates: {
            type: Boolean,
            default: false
        }
    },
    emits: ['update:modelValue'],
    data() {
        return {
            current: '',
            internalTags: Array.isArray(this.modelValue) ? [...this.modelValue] : []
        }
    },
    watch: {
        modelValue(newVal) {
            this.internalTags = Array.isArray(newVal) ? [...newVal] : [];
        }
    },
    computed: {
        showSuggestions() {
            return this.filteredSuggestions.length > 0;
        },
        filteredSuggestions() {
            const lower = this.internalTags.map(t => ('' + t).toLowerCase());
            return (this.suggestions || []).filter(s => {
                if (!s) return false;
                if (!this.allowDuplicates && lower.includes(('' + s).toLowerCase())) return false;
                if (this.current && !('' + s).toLowerCase().includes(('' + this.current).toLowerCase())) return false;
                return true;
            }).slice(0, 12);
        }
    },
    methods: {
        normalize(value) {
            return ('' + value).trim();
        },
        commitCurrent() {
            if (!this.current) return;
            const parts = this.current.split(',').map(p => this.normalize(p)).filter(Boolean);
            parts.forEach(p => this.addTag(p));
            this.current = '';
        },
        addTag(val) {
            const v = this.normalize(val);
            if (!v) return;
            if (!this.allowDuplicates && this.internalTags.map(t => ('' + t).toLowerCase()).includes(v.toLowerCase())) return;
            this.internalTags.push(v);
            this.$emit('update:modelValue', [...this.internalTags]);
        },
        removeTag(index) {
            this.internalTags.splice(index, 1);
            this.$emit('update:modelValue', [...this.internalTags]);
        },
        onPaste(e) {
            const text = (e.clipboardData || window.clipboardData).getData('text');
            if (!text) return;
            e.preventDefault();
            text.split(',').map(s => this.normalize(s)).filter(Boolean).forEach(this.addTag);
        }
    }
}
</script>

<style scoped>
</style>


