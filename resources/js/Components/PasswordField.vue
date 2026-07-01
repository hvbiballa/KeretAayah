<script setup>
import { Eye, EyeOff } from "@lucide/vue";
import { computed, ref } from "vue";

const model = defineModel({
    type: String,
    required: true,
});

const props = defineProps({
    id: {
        type: String,
        required: true,
    },
    autocomplete: {
        type: String,
        default: "new-password",
    },
    required: {
        type: Boolean,
        default: false,
    },
    placeholder: {
        type: String,
        default: "",
    },
});

const input = ref(null);
const isVisible = ref(false);

const inputType = computed(() => (isVisible.value ? "text" : "password"));

defineExpose({ focus: () => input.value?.focus() });
</script>

<template>
    <div class="relative mt-1.5">
        <input
            :id="id"
            ref="input"
            v-model="model"
            :type="inputType"
            :autocomplete="autocomplete"
            :required="required"
            :placeholder="placeholder"
            class="w-full rounded-xl border border-primary-200 px-4 py-3 pr-12 text-sm transition-all focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500"
        />
        <button
            type="button"
            class="absolute inset-y-1.5 right-1.5 inline-flex w-10 items-center justify-center rounded-lg text-primary-500 transition-colors hover:bg-primary-50 hover:text-primary-700"
            :aria-label="
                isVisible
                    ? $t('auth.hide_password')
                    : $t('auth.show_password')
            "
            @click="isVisible = !isVisible"
        >
            <EyeOff v-if="isVisible" class="size-4" />
            <Eye v-else class="size-4" />
        </button>
    </div>
</template>
