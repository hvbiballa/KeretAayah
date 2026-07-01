<script setup>
defineProps({
    title: String,
    description: String,
});
</script>

<template>
    <section class="py-12">
        <div class="max-w-md mx-auto px-4 sm:px-6">
            <div
                class="bg-white rounded-3xl border border-primary-100 p-6 sm:p-8 shadow-xl shadow-primary-100/70"
            >
                <div class="mb-6">
                    <h1 class="text-2xl font-extrabold text-foreground">
                        {{ title }}
                    </h1>
                    <p v-if="description" class="text-sm text-muted-foreground mt-2">
                        {{ description }}
                    </p>
                </div>
                <slot />
            </div>
        </div>
    </section>
</template>
