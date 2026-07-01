<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { Head } from "@inertiajs/vue3";
</script>

<template>
    <Head :title="$t('contact.title')" />
    <GuestLayout>
        <!-- Page Header -->
        <section
            class="bg-gradient-to-br from-primary-700 via-primary-500 to-primary-900 py-20"
        >
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <span
                    class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-primary-500/15 text-primary-300 text-xs font-semibold uppercase tracking-wider mb-4"
                    >{{ $t("contact.badge") }}</span
                >
                <h1 class="text-4xl lg:text-5xl font-extrabold text-white mb-4">
                    {{ $t("contact.heading") }}
                </h1>
                <p class="text-primary-100 text-lg max-w-2xl mx-auto">
                    {{ $t("contact.intro") }}
                </p>
            </div>
        </section>

        <section class="py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-3 gap-10">
                    <!-- Contact Info Cards -->
                    <div class="space-y-6">
                        <div
                            class="p-6 bg-white rounded-2xl border border-primary-100 hover:shadow-lg transition-all duration-300"
                        >
                            <div
                                class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center mb-4"
                            >
                                <svg
                                    class="w-6 h-6 text-green-600"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        d="M7 11a5 5 0 0110 0v2a5 5 0 01-7.3 4.44L5 19l1.56-4.7A5 5 0 017 11z"
                                    />
                                    <path d="M10 10h.01M14 10h.01" />
                                </svg>
                            </div>
                            <h3 class="font-bold text-foreground mb-1">
                                WhatsApp
                            </h3>
                            <p class="text-sm font-semibold text-primary-600">
                                +60199694913<br />
                                +601166000438
                            </p>
                            <p class="text-sm text-muted-foreground mt-2">
                                {{ $t("contact.whatsapp_copy") }}
                            </p>
                        </div>
                        <div
                            class="p-6 bg-white rounded-2xl border border-primary-100 hover:shadow-lg transition-all duration-300"
                        >
                            <div
                                class="w-12 h-12 rounded-xl bg-primary-100 flex items-center justify-center mb-4"
                            >
                                <svg
                                    class="w-6 h-6 text-primary-600"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
                                    />
                                </svg>
                            </div>
                            <h3 class="font-bold text-foreground mb-1">
                                Phone
                            </h3>
                            <p class="text-sm font-semibold text-primary-600">
                                +60199694913<br />
                                +601166000438
                            </p>
                            <p class="text-sm text-muted-foreground mt-2">
                                {{ $t("contact.phone_copy") }}
                            </p>
                        </div>
                        <div
                            class="p-6 bg-white rounded-2xl border border-primary-100 hover:shadow-lg transition-all duration-300"
                        >
                            <div
                                class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center mb-4"
                            >
                                <svg
                                    class="w-6 h-6 text-amber-600"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                    />
                                </svg>
                            </div>
                            <h3 class="font-bold text-foreground mb-1">
                                Email
                            </h3>
                            <p class="text-sm font-semibold text-primary-600">
                                info@keretaayah.myuni.agency
                            </p>
                            <p class="text-sm text-muted-foreground mt-2">
                                {{ $t("contact.email_copy") }}
                            </p>
                        </div>
                        <div
                            class="p-6 bg-white rounded-2xl border border-primary-100 hover:shadow-lg transition-all duration-300"
                        >
                            <div
                                class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center mb-4"
                            >
                                <svg
                                    class="w-6 h-6 text-slate-600"
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
                            </div>
                            <h3 class="font-bold text-foreground mb-1">
                                {{ $t("contact.booking_hours") }}
                            </h3>
                            <p class="text-sm font-semibold text-primary-600">
                                {{ $t("contact.online_booking") }}
                            </p>
                            <p class="text-sm text-muted-foreground mt-2">
                                {{ $t("contact.online_booking_copy") }}
                            </p>
                        </div>
                    </div>

                    <!-- Support Info -->
                    <div class="lg:col-span-2">
                        <div
                            class="bg-white rounded-2xl border border-primary-100 p-8"
                        >
                            <h2 class="text-2xl font-bold text-foreground mb-3">
                                {{ $t("contact.before_title") }}
                            </h2>
                            <p class="text-muted-foreground mb-8">
                                {{ $t("contact.before_copy") }}
                            </p>

                            <div class="space-y-5">
                                <div class="flex gap-4">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-primary-100 flex items-center justify-center shrink-0"
                                    >
                                        <span
                                            class="text-primary-600 font-bold text-sm"
                                            >1</span
                                        >
                                    </div>
                                    <div>
                                        <h3
                                            class="font-semibold text-foreground mb-1"
                                        >
                                            {{ $t("contact.checks.availability_title") }}
                                        </h3>
                                        <p
                                            class="text-sm text-muted-foreground leading-relaxed"
                                        >
                                            {{ $t("contact.checks.availability_copy") }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex gap-4">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-primary-100 flex items-center justify-center shrink-0"
                                    >
                                        <span
                                            class="text-primary-600 font-bold text-sm"
                                            >2</span
                                        >
                                    </div>
                                    <div>
                                        <h3
                                            class="font-semibold text-foreground mb-1"
                                        >
                                            {{ $t("contact.checks.verification_title") }}
                                        </h3>
                                        <p
                                            class="text-sm text-muted-foreground leading-relaxed"
                                        >
                                            {{ $t("contact.checks.verification_copy") }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex gap-4">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-primary-100 flex items-center justify-center shrink-0"
                                    >
                                        <span
                                            class="text-primary-600 font-bold text-sm"
                                            >3</span
                                        >
                                    </div>
                                    <div>
                                        <h3
                                            class="font-semibold text-foreground mb-1"
                                        >
                                            {{ $t("contact.checks.proof_title") }}
                                        </h3>
                                        <p
                                            class="text-sm text-muted-foreground leading-relaxed"
                                        >
                                            {{ $t("contact.checks.proof_copy") }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex gap-4">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-primary-100 flex items-center justify-center shrink-0"
                                    >
                                        <span
                                            class="text-primary-600 font-bold text-sm"
                                            >4</span
                                        >
                                    </div>
                                    <div>
                                        <h3
                                            class="font-semibold text-foreground mb-1"
                                        >
                                            {{ $t("contact.checks.hours_title") }}
                                        </h3>
                                        <p
                                            class="text-sm text-muted-foreground leading-relaxed"
                                        >
                                            {{ $t("contact.checks.hours_copy") }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-4 mt-10">
                                <a
                                    :href="route('cars')"
                                    class="inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-primary-500 via-primary-600 to-primary-700 text-white font-semibold rounded-xl shadow-md shadow-primary-500/25 hover:shadow-lg hover:shadow-primary-500/30 hover:from-primary-600 hover:to-primary-800 transition-all duration-200"
                                >
                                    {{ $t("home.find_available_cars") }}
                                </a>
                                <a
                                    href="https://wa.me/60199694913"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center justify-center px-8 py-3 border border-primary-200 text-primary-700 font-semibold rounded-xl hover:bg-primary-50 transition-colors"
                                >
                                    {{ $t("contact.message_whatsapp") }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </GuestLayout>
</template>
