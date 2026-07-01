<script setup>
import { CircleHelp } from "@lucide/vue";

defineProps({
    text: {
        type: String,
        required: true,
    },
    iconClass: {
        type: String,
        default: "text-primary-500",
    },
    tooltipClass: {
        type: String,
        default: "border-primary-100 bg-white text-foreground",
    },
});
</script>

<template>
    <span class="group relative inline-flex">
        <button
            type="button"
            tabindex="0"
            class="inline-flex h-5 w-5 items-center justify-center rounded-full transition-colors hover:bg-black/5 focus:outline-none focus:ring-2 focus:ring-primary-300"
            :aria-label="text"
        >
            <CircleHelp class="h-4 w-4" :class="iconClass" />
        </button>
        <span
            role="tooltip"
            class="pointer-events-none absolute left-1/2 top-full z-30 mt-2 hidden w-60 -translate-x-1/2 rounded-xl border px-3 py-2 text-left text-xs leading-5 shadow-lg group-hover:block group-focus-within:block"
            :class="tooltipClass"
        >
            {{ text }}
        </span>
    </span>
</template>
