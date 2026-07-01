<script setup>
import CarList from "@/Components/Car/CarList.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { Head, Link, usePage } from "@inertiajs/vue3";

const page = usePage();
</script>

<template>
    <Head :title="$t('home.title')" />
    <GuestLayout>
        <!-- Hero Section -->
        <section
            class="relative overflow-hidden bg-gradient-to-br from-primary-700 via-primary-500 to-primary-900"
        >
            <div class="absolute inset-0 opacity-10">
                <div
                    class="absolute top-0 left-1/4 w-96 h-96 bg-primary-300 rounded-full blur-3xl"
                ></div>
                <div
                    class="absolute bottom-0 right-1/4 w-80 h-80 bg-white rounded-full blur-3xl"
                ></div>
            </div>
            <div
                class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 lg:py-32"
            >
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <span
                            class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 text-primary-100 text-xs font-semibold uppercase tracking-wider mb-6"
                        >
                            <span
                                class="w-1.5 h-1.5 bg-primary-400 rounded-full animate-pulse"
                            ></span>
                            {{ $t("home.hero_badge") }}
                        </span>
                        <h1
                            class="text-4xl lg:text-6xl font-extrabold text-white leading-tight mb-6"
                        >
                            {{ $t("home.hero_title") }}
                            <span
                                class="block bg-gradient-to-r from-primary-100 via-white to-primary-200 bg-clip-text text-transparent"
                                >{{ $t("home.hero_title_highlight") }}</span
                            >
                        </h1>
                        <p
                            class="text-lg text-primary-100 leading-relaxed mb-8 max-w-xl"
                        >
                            {{ $t("home.hero_copy") }}
                        </p>
                        <div
                            class="flex flex-wrap gap-3 mb-10 text-sm text-primary-50"
                        >
                            <span
                                class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1.5"
                            >
                                <svg
                                    class="w-4 h-4"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    viewBox="0 0 24 24"
                                >
                                    <path d="M12 6v6l4 2" />
                                    <path
                                        d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                                {{ $t("home.badges.booking") }}
                            </span>
                            <span
                                class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1.5"
                            >
                                <svg
                                    class="w-4 h-4"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        d="M8 17h8M7 17a2 2 0 110-4 2 2 0 010 4zm10 0a2 2 0 110-4 2 2 0 010 4z"
                                    />
                                    <path d="M5 13l2-6h10l2 6" />
                                </svg>
                                {{ $t("home.badges.sedan") }}
                            </span>
                            <span
                                class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1.5"
                            >
                                <svg
                                    class="w-4 h-4"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        d="M12 21s7-4.35 7-11a7 7 0 10-14 0c0 6.65 7 11 7 11z"
                                    />
                                    <path d="M12 10.5h.01" />
                                </svg>
                                {{ $t("home.badges.location") }}
                            </span>
                        </div>
                        <div class="flex flex-wrap gap-4">
                            <Link
                                :href="route('cars')"
                                class="inline-flex items-center gap-2 px-8 py-3.5 bg-gradient-to-r from-primary-500 via-primary-600 to-primary-700 text-white font-semibold rounded-2xl shadow-xl shadow-primary-500/30 hover:shadow-2xl hover:shadow-primary-500/40 hover:from-primary-600 hover:to-primary-800 transition-all duration-300"
                            >
                                {{ $t("home.find_available_cars") }}
                                <svg
                                    class="w-5 h-5"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    viewBox="0 0 24 24"
                                >
                                    <path d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </Link>
                            <Link
                                :href="route('about')"
                                class="inline-flex items-center gap-2 px-8 py-3.5 border border-white/40 text-white font-semibold rounded-2xl hover:bg-white/10 transition-all duration-300"
                            >
                                {{ $t("home.how_it_works") }}
                            </Link>
                        </div>
                        <!-- Stats -->
                        <div
                            class="grid grid-cols-3 gap-4 sm:gap-8 mt-14 max-w-xl"
                        >
                            <div>
                                <p class="text-3xl font-extrabold text-white">
                                    2
                                </p>
                                <p class="text-sm text-primary-100 mt-1">
                                    {{ $t("home.stats.vehicles") }}
                                </p>
                            </div>
                            <div>
                                <p class="text-3xl font-extrabold text-white">
                                    183
                                </p>
                                <p class="text-sm text-primary-100 mt-1">
                                    {{ $t("home.stats.completed") }}
                                </p>
                            </div>
                            <div>
                                <p class="text-3xl font-extrabold text-white">
                                    24/7
                                </p>
                                <p class="text-sm text-primary-100 mt-1">
                                    {{ $t("home.stats.access") }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="hidden lg:block relative">
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-primary-500/20 to-white/10 rounded-3xl blur-2xl"
                        ></div>
                        <div
                            class="relative bg-white/10 backdrop-blur-sm rounded-3xl p-8 border border-white/15 shadow-2xl"
                        >
                            <div class="text-center">
                                <div
                                    class="w-24 h-24 mx-auto rounded-3xl bg-white/10 flex items-center justify-center mb-6"
                                >
                                    <svg
                                        class="w-14 h-14 text-white"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.5"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            d="M7 17a2 2 0 100-4 2 2 0 000 4zm10 0a2 2 0 100-4 2 2 0 000 4z"
                                        />
                                        <path
                                            d="M5 13l2.2-6.2A2 2 0 019.08 5h5.84a2 2 0 011.88 1.32L19 13"
                                        />
                                        <path
                                            d="M4 13h16v4a1 1 0 01-1 1h-1m-12 0H5a1 1 0 01-1-1v-4z"
                                        />
                                    </svg>
                                </div>
                                <p class="text-white font-semibold text-lg">
                                    {{ $t("home.fleet_card_title") }}
                                </p>
                                <p class="text-primary-100 text-sm mt-1">
                                    {{ $t("home.fleet_card_copy") }}
                                </p>
                                <div class="mt-6 grid grid-cols-3 gap-3">
                                    <div
                                        class="bg-white/10 rounded-xl p-4 text-center"
                                    >
                                        <p
                                            class="text-xl font-extrabold text-white"
                                        >
                                            2
                                        </p>
                                        <p class="text-xs text-primary-100 mt-1">
                                            {{ $t("home.sedans") }}
                                        </p>
                                    </div>
                                    <div
                                        class="bg-white/10 rounded-xl p-4 text-center"
                                    >
                                        <p
                                            class="text-xl font-extrabold text-white"
                                        >
                                            {{ $t("home.hourly") }}
                                        </p>
                                        <p class="text-xs text-primary-100 mt-1">
                                            {{ $t("home.daily") }}
                                        </p>
                                    </div>
                                    <div
                                        class="bg-white/10 rounded-xl p-4 text-center"
                                    >
                                        <p
                                            class="text-xl font-extrabold text-white"
                                        >
                                            {{ $t("home.daily") }}
                                        </p>
                                        <p class="text-xs text-primary-100 mt-1">
                                            {{ $t("home.daily") }}
                                        </p>
                                    </div>
                                </div>
                                <p
                                    class="mt-6 rounded-2xl bg-white/10 px-4 py-3 text-sm text-primary-50"
                                >
                                    {{ $t("common.customer_service_hours") }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works -->
        <section class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-14">
                    <span
                        class="text-primary-600 text-sm font-semibold uppercase tracking-wider"
                        >{{ $t("home.steps_eyebrow") }}</span
                    >
                    <h2
                        class="text-3xl lg:text-4xl font-extrabold text-foreground mt-2"
                    >
                        {{ $t("home.steps_title") }}
                    </h2>
                    <p class="text-muted-foreground mt-3 max-w-xl mx-auto">
                        {{ $t("home.steps_copy") }}
                    </p>
                </div>
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="text-center group">
                        <div
                            class="w-16 h-16 mx-auto rounded-2xl bg-primary-50 flex items-center justify-center mb-5 group-hover:bg-primary-100 transition-colors"
                        >
                            <svg
                                class="w-8 h-8 text-primary-600"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.5"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"
                                />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-foreground mb-2">
                            {{ $t("home.steps.check_title") }}
                        </h3>
                        <p class="text-muted-foreground text-sm leading-relaxed">
                            {{ $t("home.steps.check_copy") }}
                        </p>
                    </div>
                    <div class="text-center group">
                        <div
                            class="w-16 h-16 mx-auto rounded-2xl bg-accent-50 flex items-center justify-center mb-5 group-hover:bg-accent-100 transition-colors"
                        >
                            <svg
                                class="w-8 h-8 text-accent-600"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.5"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"
                                />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-foreground mb-2">
                            {{ $t("home.steps.book_title") }}
                        </h3>
                        <p class="text-muted-foreground text-sm leading-relaxed">
                            {{ $t("home.steps.book_copy") }}
                        </p>
                    </div>
                    <div class="text-center group">
                        <div
                            class="w-16 h-16 mx-auto rounded-2xl bg-green-50 flex items-center justify-center mb-5 group-hover:bg-green-100 transition-colors"
                        >
                            <svg
                                class="w-8 h-8 text-green-600"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.5"
                                viewBox="0 0 24 24"
                            >
                                <path d="M8 17h8" />
                                <path
                                    d="M7 17a2 2 0 110-4 2 2 0 010 4zm10 0a2 2 0 110-4 2 2 0 010 4z"
                                />
                                <path d="M5 13l2-6h10l2 6" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-foreground mb-2">
                            {{ $t("home.steps.pickup_title") }}
                        </h3>
                        <p class="text-muted-foreground text-sm leading-relaxed">
                            {{ $t("home.steps.pickup_copy") }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Featured Cars -->
        <section class="py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div
                    class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between mb-12"
                >
                    <div>
                        <span
                            class="text-primary-600 text-sm font-semibold uppercase tracking-wider"
                            >{{ $t("home.available_vehicles") }}</span
                        >
                        <h2
                            class="text-3xl lg:text-4xl font-extrabold text-foreground mt-2"
                        >
                            {{ $t("home.reliable_sedans") }}
                        </h2>
                        <p class="text-muted-foreground mt-3 max-w-2xl">
                            {{ $t("home.vehicles_copy") }}
                        </p>
                    </div>
                    <Link
                        :href="route('cars')"
                        class="hidden sm:inline-flex items-center gap-1.5 text-primary-600 text-sm font-semibold hover:text-primary-700 transition-colors"
                    >
                        {{ $t("home.view_vehicles") }}
                        <svg
                            class="w-4 h-4"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                        >
                            <path d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </Link>
                </div>
                <CarList />
            </div>
        </section>

        <!-- Why Choose Us -->
        <section class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-14">
                    <span
                        class="text-primary-600 text-sm font-semibold uppercase tracking-wider"
                        >{{ $t("home.why_eyebrow") }}</span
                    >
                    <h2
                        class="text-3xl lg:text-4xl font-extrabold text-foreground mt-2"
                    >
                        {{ $t("home.why_title") }}
                    </h2>
                    <p class="text-muted-foreground mt-3 max-w-2xl mx-auto">
                        {{ $t("home.why_copy") }}
                    </p>
                </div>
                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div
                        class="p-6 rounded-2xl border border-primary-100 bg-white hover:border-primary-200 hover:bg-primary-50/30 transition-all duration-300"
                    >
                        <div
                            class="w-12 h-12 rounded-xl bg-primary-100 flex items-center justify-center mb-4"
                        >
                            <svg
                                class="w-6 h-6 text-primary-600"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    d="M12 6v12m-4-8.5A3.5 3.5 0 0112 6c2.21 0 4 1.12 4 2.5S14.21 11 12 11s-4 1.12-4 2.5S9.79 16 12 16c1.65 0 3.07-.63 3.68-1.54"
                                />
                            </svg>
                        </div>
                        <h4 class="font-bold text-foreground mb-2">
                            {{ $t("home.benefits.rates_title") }}
                        </h4>
                        <p class="text-sm text-muted-foreground leading-relaxed">
                            {{ $t("home.benefits.rates_copy") }}
                        </p>
                    </div>
                    <div
                        class="p-6 rounded-2xl border border-primary-100 bg-white hover:border-primary-200 hover:bg-primary-50/30 transition-all duration-300"
                    >
                        <div
                            class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center mb-4"
                        >
                            <svg
                                class="w-6 h-6 text-green-600"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                viewBox="0 0 24 24"
                            >
                                <path d="M20 6L9 17l-5-5" />
                            </svg>
                        </div>
                        <h4 class="font-bold text-foreground mb-2">
                            {{ $t("home.benefits.booking_title") }}
                        </h4>
                        <p class="text-sm text-muted-foreground leading-relaxed">
                            {{ $t("home.benefits.booking_copy") }}
                        </p>
                    </div>
                    <div
                        class="p-6 rounded-2xl border border-primary-100 bg-white hover:border-primary-200 hover:bg-primary-50/30 transition-all duration-300"
                    >
                        <div
                            class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center mb-4"
                        >
                            <svg
                                class="w-6 h-6 text-amber-600"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    d="M8 2v4m8-4v4M3 10h18M5 5h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z"
                                />
                            </svg>
                        </div>
                        <h4 class="font-bold text-foreground mb-2">
                            {{ $t("home.benefits.flexible_title") }}
                        </h4>
                        <p class="text-sm text-muted-foreground leading-relaxed">
                            {{ $t("home.benefits.flexible_copy") }}
                        </p>
                    </div>
                    <div
                        class="p-6 rounded-2xl border border-primary-100 bg-white hover:border-primary-200 hover:bg-primary-50/30 transition-all duration-300"
                    >
                        <div
                            class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center mb-4"
                        >
                            <svg
                                class="w-6 h-6 text-slate-600"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    d="M9 12l2 2 4-4M12 3l7 4v5c0 5-3.5 8.5-7 9-3.5-.5-7-4-7-9V7l7-4z"
                                />
                            </svg>
                        </div>
                        <h4 class="font-bold text-foreground mb-2">
                            {{ $t("home.benefits.availability_title") }}
                        </h4>
                        <p class="text-sm text-muted-foreground leading-relaxed">
                            {{ $t("home.benefits.availability_copy") }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div
                    class="bg-gradient-to-br from-primary-500 via-primary-600 to-primary-800 rounded-3xl p-12 lg:p-16 text-center relative overflow-hidden"
                >
                    <div
                        class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"
                    ></div>
                    <div
                        class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"
                    ></div>
                    <div class="relative">
                        <h2
                            class="text-3xl lg:text-4xl font-extrabold text-white mb-4"
                        >
                            {{ $t("home.cta_title") }}
                        </h2>
                        <p
                            class="text-primary-100 text-lg mb-8 max-w-xl mx-auto"
                        >
                            {{ $t("home.cta_copy") }}
                        </p>
                        <div class="flex justify-center gap-4 flex-wrap">
                            <Link
                                :href="route('cars')"
                                class="px-8 py-3.5 bg-white text-primary-700 font-semibold rounded-2xl hover:bg-primary-50 transition-colors shadow-lg"
                                >{{ $t("home.find_available_cars") }}</Link
                            >
                            <Link
                                :href="route('cars')"
                                class="px-8 py-3.5 border-2 border-white/30 text-white font-semibold rounded-2xl hover:bg-white/10 transition-colors"
                                >{{ $t("home.view_vehicles") }}</Link
                            >
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </GuestLayout>
</template>
