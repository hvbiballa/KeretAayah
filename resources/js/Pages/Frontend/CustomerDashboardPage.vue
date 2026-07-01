<script setup>
import BookingPlannerForm from "@/Components/BookingPlannerForm.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import PaymentStatusBadge from "@/Components/PaymentStatusBadge.vue";
import { bookingStatusLabelKey, bookingStatusTheme } from "@/lib/bookingStatus";
import { Head, Link, usePage } from "@inertiajs/vue3";
import { formatMYR } from "@/lib/car";
import { rentalMethodLabel, rentalMethodTheme, rentalPeriod } from "@/lib/rental";
import { computed } from "vue";
import { useI18n } from "@/lib/i18n";

const page = usePage();
const { locale, t } = useI18n();

const cars = computed(() => page.props.cars || []);
const schedule = computed(() => page.props.schedule || {});
const hasSchedule = computed(() => page.props.hasSchedule);
const rentals = computed(() => page.props.rentals || []);
const methodTheme = (method) => rentalMethodTheme(method);
const recentRentals = computed(() => rentals.value.slice(0, 5));
</script>

<template>
    <Head :title="$t('booking.customer_dashboard')" />

    <GuestLayout>
        <section class="bg-[radial-gradient(circle_at_top,_rgba(239,68,68,0.16),_transparent_38%),linear-gradient(180deg,#fff7f7_0%,#ffffff_36%)] pb-12 pt-10">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="max-w-2xl">
                    <p class="text-xs font-semibold tracking-[0.18em] text-primary-500">
                        {{ $t("booking.customer_dashboard_eyebrow") }}
                    </p>
                    <h1 class="mt-2 text-2xl font-extrabold tracking-tight text-foreground sm:text-3xl">
                        {{ $t("booking.customer_dashboard_title") }}
                    </h1>
                    <p class="mt-1 text-sm leading-6 text-muted-foreground">
                        {{ $t("booking.customer_dashboard_copy") }}
                    </p>
                </div>

                <div class="mt-6">
                    <BookingPlannerForm
                        :action-route="route('customer.dashboard')"
                        :initial-schedule="schedule"
                        :has-schedule="hasSchedule"
                        :cars="cars"
                    />
                </div>
            </div>
        </section>

        <section class="pb-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div
                    class="rounded-[2rem] border border-primary-100 bg-white p-5 shadow-[0_20px_70px_-40px_rgba(15,23,42,0.45)]"
                >
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <h2 class="text-2xl font-extrabold tracking-tight text-foreground">
                            {{ $t("booking.recent_bookings_title") }}
                        </h2>
                        <Link
                            :href="route('bookings.index')"
                            class="text-sm font-semibold text-primary-600 transition-colors hover:text-primary-700"
                        >
                            {{ $t("booking.view_all_bookings") }}
                        </Link>
                    </div>

                    <div
                        v-if="recentRentals.length > 0"
                        class="mt-5 flex gap-4 overflow-x-auto pb-2"
                    >
                        <article
                            v-for="booking in recentRentals"
                            :key="booking.id"
                            class="min-w-[320px] flex-1 rounded-[2rem] border p-5 shadow-[0_20px_70px_-40px_rgba(15,23,42,0.45)]"
                            :class="[methodTheme(booking.rental_method).card, methodTheme(booking.rental_method).border]"
                        >
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <p
                                        class="inline-flex rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em]"
                                        :class="methodTheme(booking.rental_method).badgeSoft"
                                    >
                                        {{ rentalMethodLabel(booking.rental_method, t) }}
                                    </p>
                                    <h3 class="mt-2 text-lg font-bold text-foreground">
                                        {{ booking.car.name }}
                                    </h3>
                                    <p class="mt-2 text-sm leading-6 text-muted-foreground">
                                        {{ rentalPeriod(booking, locale) }}
                                    </p>
                                </div>
                                <PaymentStatusBadge :status="booking.payment.status" />
                            </div>

                            <div
                                class="mt-5 flex flex-wrap items-center justify-between gap-4 border-t pt-4"
                                :class="methodTheme(booking.rental_method).border"
                            >
                                <div class="flex flex-wrap items-center gap-3">
                                    <span
                                        class="inline-flex rounded-full px-3 py-1 text-xs font-semibold"
                                        :class="bookingStatusTheme(booking.status).badge"
                                    >
                                        {{ $t(bookingStatusLabelKey(booking.status)) }}
                                    </span>
                                    <span class="text-sm font-semibold text-foreground">
                                        {{ formatMYR(booking.total_cost) }}
                                    </span>
                                </div>

                                <div class="flex items-center gap-4">
                                    <Link
                                        v-if="booking.payment?.can_upload_proof"
                                        :href="route('bookings.payment', booking.id)"
                                        class="text-sm font-semibold transition-colors"
                                        :class="methodTheme(booking.rental_method).link"
                                    >
                                        {{ $t("booking.continue_payment") }}
                                    </Link>
                                    <Link
                                        :href="route('bookings.show', booking.id)"
                                        class="text-sm font-semibold text-muted-foreground transition-colors"
                                        :class="methodTheme(booking.rental_method).mutedHover"
                                    >
                                        {{ $t("common.actions.view") }}
                                    </Link>
                                </div>
                            </div>
                        </article>
                    </div>

                    <div
                        v-else
                        class="mt-5 rounded-[2rem] border border-dashed border-primary-200 bg-primary-50/50 px-6 py-10 text-center"
                    >
                        <h3 class="text-lg font-semibold text-foreground">
                            {{ $t("booking.no_recent_matches_title") }}
                        </h3>
                        <p class="mt-2 text-sm leading-6 text-muted-foreground">
                            {{ $t("booking.no_recent_matches_copy") }}
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </GuestLayout>
</template>
