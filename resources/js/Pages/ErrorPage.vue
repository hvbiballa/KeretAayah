<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { Head, Link, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    status: {
        type: Number,
        default: 500,
    },
    title: {
        type: String,
        default: "",
    },
    message: {
        type: String,
        default: "",
    },
});

const page = usePage();

const homeHref = computed(() => {
    const role = page.props.auth?.user?.role;

    if (role === "admin") {
        return route("dashboard");
    }

    if (role === "staff") {
        return route("payments.index");
    }

    if (role === "customer") {
        return route("customer.dashboard");
    }

    return "/";
});

const goBack = () => {
    window.history.back();
};
</script>

<template>
    <Head :title="title" />

    <GuestLayout>
        <section class="py-16">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <div class="rounded-[2rem] border border-primary-100 bg-white p-8 text-center shadow-sm sm:p-12">
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-primary-500">
                        {{ status }}
                    </p>
                    <h1 class="mt-3 text-3xl font-extrabold tracking-tight text-foreground sm:text-4xl">
                        {{ title }}
                    </h1>
                    <p class="mx-auto mt-4 max-w-xl text-sm leading-7 text-muted-foreground sm:text-base">
                        {{ message }}
                    </p>

                    <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                        <button
                            type="button"
                            class="inline-flex min-h-11 items-center justify-center rounded-xl border border-primary-200 px-5 text-sm font-semibold text-muted-foreground transition-colors hover:bg-primary-50"
                            @click="goBack"
                        >
                            {{ $t("common.errors.go_back") }}
                        </button>
                        <Link
                            :href="homeHref"
                            class="inline-flex min-h-11 items-center justify-center rounded-xl bg-primary-600 px-5 text-sm font-semibold text-white transition-colors hover:bg-primary-700"
                        >
                            {{ $t("nav.back_to_home") }}
                        </Link>
                    </div>
                </div>
            </div>
        </section>
    </GuestLayout>
</template>
