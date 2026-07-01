<script setup>
import { Check, X } from "@lucide/vue";
import { computed } from "vue";

const props = defineProps({
    password: {
        type: String,
        default: "",
    },
});

const requirements = computed(() => [
    {
        key: "min",
        label: "auth.password_requirements.min",
        met: props.password.length >= 8,
    },
    {
        key: "uppercase",
        label: "auth.password_requirements.uppercase",
        met: /[A-Z]/.test(props.password),
    },
    {
        key: "lowercase",
        label: "auth.password_requirements.lowercase",
        met: /[a-z]/.test(props.password),
    },
    {
        key: "number",
        label: "auth.password_requirements.number",
        met: /\d/.test(props.password),
    },
    {
        key: "symbol",
        label: "auth.password_requirements.symbol",
        met: /[^A-Za-z0-9]/.test(props.password),
    },
]);
</script>

<template>
    <div class="rounded-xl border border-primary-100 bg-primary-50/40 p-3">
        <p class="text-xs font-semibold text-primary-700">
            {{ $t("auth.password_requirements.title") }}
        </p>
        <ul class="mt-2 grid gap-1.5 text-xs sm:grid-cols-2">
            <li
                v-for="requirement in requirements"
                :key="requirement.key"
                class="flex items-center gap-1.5"
                :class="requirement.met ? 'text-green-700' : 'text-primary-500'"
            >
                <span
                    class="inline-flex size-4 shrink-0 items-center justify-center rounded-full"
                    :class="
                        requirement.met
                            ? 'bg-green-100 text-green-700'
                            : 'bg-white text-primary-400'
                    "
                >
                    <Check v-if="requirement.met" class="size-3" />
                    <X v-else class="size-3" />
                </span>
                {{ $t(requirement.label) }}
            </li>
        </ul>
    </div>
</template>
