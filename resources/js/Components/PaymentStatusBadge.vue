<script setup>
import {
    paymentStatusClasses,
    paymentStatusLabel,
} from "@/lib/payment";
import { useI18n } from "@/lib/i18n";

defineProps({
    status: String,
});

const { t } = useI18n();
</script>

<template>
    <span
        :class="[
            'inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-lg',
            paymentStatusClasses(status),
        ]"
    >
        <span class="w-1.5 h-1.5 rounded-full bg-current opacity-70"></span>
        {{ paymentStatusLabel(status, t) }}
    </span>
</template>
