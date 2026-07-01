<script setup>
import { Link } from "@inertiajs/vue3";
import { useI18n } from "@/lib/i18n";

const { locale } = useI18n();

const options = [
    { value: "ms", label: "BM" },
    { value: "en", label: "EN" },
];
</script>

<template>
    <div
        class="inline-flex items-center rounded-full border border-primary-100 bg-white/80 p-1 shadow-sm"
        :aria-label="$t('nav.language')"
    >
        <Link
            v-for="option in options"
            :key="option.value"
            :href="route('language.switch', option.value)"
            preserve-scroll
            class="min-w-10 rounded-full px-2.5 py-1 text-center text-xs font-bold transition-colors"
            :class="
                locale === option.value
                    ? 'bg-primary-600 text-white shadow-sm'
                    : 'text-primary-600 hover:bg-primary-50'
            "
        >
            {{ option.label }}
        </Link>
    </div>
</template>
