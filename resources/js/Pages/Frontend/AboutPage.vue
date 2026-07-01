<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { Head } from "@inertiajs/vue3";
</script>

<template>
    <Head :title="$t('car.about_title')" />

    <GuestLayout>
        <!-- Page Header -->
        <section
            class="bg-gradient-to-br from-primary-700 via-primary-500 to-primary-900 py-20"
        >
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <span
                    class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-primary-500/15 text-primary-300 text-xs font-semibold uppercase tracking-wider mb-4"
                    >{{ $t("car.about_badge") }}</span
                >
                <h1 class="text-4xl lg:text-5xl font-extrabold text-white mb-4">
                    {{ $t("car.about_heading") }}
                </h1>
                <p class="text-primary-100 text-lg max-w-2xl mx-auto">
                    {{ $t("car.about_intro") }}
                </p>
            </div>
        </section>

        <!-- Story -->
        <section class="py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-2 gap-16 items-center">
                    <div>
                        <span
                            class="text-primary-600 text-sm font-semibold uppercase tracking-wider"
                            >{{ $t("car.purpose") }}</span
                        >
                        <h2
                            class="text-3xl font-extrabold text-foreground mt-2 mb-6"
                        >
                            {{ $t("car.simple_rental") }}
                        </h2>
                        <p class="text-muted-foreground leading-relaxed mb-4">
                            {{ $t("car.about_p1") }}
                        </p>
                        <p class="text-muted-foreground leading-relaxed mb-4">
                            {{ $t("car.about_p2") }}
                        </p>
                        <p class="text-muted-foreground leading-relaxed">
                            {{ $t("car.about_p3") }}
                        </p>
                    </div>
                    <div
                        class="bg-gradient-to-br from-primary-50 to-accent-50 rounded-3xl p-12 text-center"
                    >
                        <div
                            class="w-24 h-24 mx-auto rounded-3xl bg-white flex items-center justify-center mb-6 shadow-sm"
                        >
                            <img
                                src="/icon.png"
                                alt="KeretaAyah"
                                class="w-16 h-16 object-contain"
                            />
                        </div>
                        <div class="grid grid-cols-2 gap-6">
                            <div class="bg-white rounded-2xl p-5 shadow-sm">
                                <p
                                    class="text-3xl font-extrabold text-primary-600"
                                >
                                    2
                                </p>
                                <p class="text-sm text-muted-foreground mt-1">
                                    {{ $t("car.vehicles") }}
                                </p>
                            </div>
                            <div class="bg-white rounded-2xl p-5 shadow-sm">
                                <p
                                    class="text-3xl font-extrabold text-primary-600"
                                >
                                    183
                                </p>
                                <p class="text-sm text-muted-foreground mt-1">
                                    {{ $t("car.completed_rentals") }}
                                </p>
                            </div>
                            <div class="bg-white rounded-2xl p-5 shadow-sm">
                                <p
                                    class="text-3xl font-extrabold text-primary-600"
                                >
                                    1
                                </p>
                                <p class="text-sm text-muted-foreground mt-1">
                                    {{ $t("car.month_operating") }}
                                </p>
                            </div>
                            <div class="bg-white rounded-2xl p-5 shadow-sm">
                                <p
                                    class="text-3xl font-extrabold text-primary-600"
                                >
                                    24/7
                                </p>
                                <p class="text-sm text-muted-foreground mt-1">
                                    {{ $t("car.online_booking") }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Values -->
        <section class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-14">
                    <span
                        class="text-primary-600 text-sm font-semibold uppercase tracking-wider"
                        >{{ $t("car.values") }}</span
                    >
                    <h2 class="text-3xl font-extrabold text-foreground mt-2">
                        {{ $t("car.what_drives_us") }}
                    </h2>
                </div>
                <div class="grid md:grid-cols-3 gap-8">
                    <div
                        class="p-8 rounded-2xl border border-primary-100 text-center hover:shadow-lg transition-all duration-300"
                    >
                        <div
                            class="w-16 h-16 mx-auto rounded-2xl bg-primary-100 flex items-center justify-center mb-5"
                        >
                            <svg
                                class="w-8 h-8 text-primary-600"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m10-5.13a4 4 0 11-8 0 4 4 0 018 0zM7 9a4 4 0 108 0"
                                />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-foreground mb-3">
                            {{ $t("car.student_focused") }}
                        </h3>
                        <p class="text-muted-foreground text-sm leading-relaxed">
                            {{ $t("car.student_focused_copy") }}
                        </p>
                    </div>
                    <div
                        class="p-8 rounded-2xl border border-primary-100 text-center hover:shadow-lg transition-all duration-300"
                    >
                        <div
                            class="w-16 h-16 mx-auto rounded-2xl bg-green-100 flex items-center justify-center mb-5"
                        >
                            <svg
                                class="w-8 h-8 text-green-600"
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
                        <h3 class="text-lg font-bold text-foreground mb-3">
                            {{ $t("car.transparent_pricing") }}
                        </h3>
                        <p class="text-muted-foreground text-sm leading-relaxed">
                            {{ $t("car.transparent_pricing_copy") }}
                        </p>
                    </div>
                    <div
                        class="p-8 rounded-2xl border border-primary-100 text-center hover:shadow-lg transition-all duration-300"
                    >
                        <div
                            class="w-16 h-16 mx-auto rounded-2xl bg-amber-100 flex items-center justify-center mb-5"
                        >
                            <svg
                                class="w-8 h-8 text-amber-600"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                viewBox="0 0 24 24"
                            >
                                <path d="M8 17h8" />
                                <path
                                    d="M7 17a2 2 0 110-4 2 2 0 010 4zm10 0a2 2 0 110-4 2 2 0 010 4z"
                                />
                                <path d="M5 13l2-6h10l2 6" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-foreground mb-3">
                            {{ $t("car.simple_contactless") }}
                        </h3>
                        <p class="text-muted-foreground text-sm leading-relaxed">
                            {{ $t("car.simple_contactless_copy") }}
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </GuestLayout>
</template>
