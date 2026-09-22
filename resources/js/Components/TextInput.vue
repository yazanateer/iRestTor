<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="min-h-[44px] rounded-ds-md border-border px-3 py-2 shadow-ds-1 focus:border-brand focus:ring-brand"
        v-model="model"
        ref="input"
    />
</template>
