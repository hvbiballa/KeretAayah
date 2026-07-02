<script setup>
import AlertDialog from "@/Components/ui/alert-dialog/AlertDialog.vue";
import AlertDialogAction from "@/Components/ui/alert-dialog/AlertDialogAction.vue";
import AlertDialogCancel from "@/Components/ui/alert-dialog/AlertDialogCancel.vue";
import AlertDialogContent from "@/Components/ui/alert-dialog/AlertDialogContent.vue";
import AlertDialogDescription from "@/Components/ui/alert-dialog/AlertDialogDescription.vue";
import AlertDialogFooter from "@/Components/ui/alert-dialog/AlertDialogFooter.vue";
import AlertDialogHeader from "@/Components/ui/alert-dialog/AlertDialogHeader.vue";
import AlertDialogTitle from "@/Components/ui/alert-dialog/AlertDialogTitle.vue";
import AlertDialogTrigger from "@/Components/ui/alert-dialog/AlertDialogTrigger.vue";

import GuestLayout from "@/Layouts/GuestLayout.vue";
import PaymentStatusBadge from "@/Components/PaymentStatusBadge.vue";
import {
    bookingCanBeCancelled,
    bookingStatusLabelKey,
    bookingStatusTheme,
} from "@/lib/bookingStatus";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import { carImageAlt, carImageUrl, formatMYR } from "@/lib/car";
import { rentalMethodLabel, rentalMethodTheme, rentalPeriod } from "@/lib/rental";
import { useI18n } from "@/lib/i18n";
import { computed, ref } from "vue";

const page = usePage();
const { locale, t } = useI18n();
const sortBy = ref("pickup");
const paymentFilter = ref("all");

const rentals = computed(() => page.props.rentals || []);

const paymentStatuses = [
    "pending",
    "proof_submitted",
    "partially_paid",
    "paid",
    "refunded",
];

const sortOptions = [
    { value: "car", label: "booking.sort_options.car" },
    { value: "pickup", label: "booking.sort_options.pickup" },
    { value: "duration", label: "booking.sort_options.duration" },
    { value: "cost", label: "booking.sort_options.cost" },
    { value: "payment", label: "booking.sort_options.payment" },
];

const visibleBookings = computed(() => {
    const filtered = rentals.value.filter((booking) => {
        if (paymentFilter.value === "all") return true;

        return booking.payment?.status === paymentFilter.value;
    });

    return [...filtered].sort((a, b) => {
        if (sortBy.value === "car") {
            return (a.car?.name || "").localeCompare(b.car?.name || "");
        }

        if (sortBy.value === "duration") {
            return Number(a.duration_units || 0) - Number(b.duration_units || 0);
        }

        if (sortBy.value === "cost") {
            return Number(a.total_cost || 0) - Number(b.total_cost || 0);
        }

        if (sortBy.value === "payment") {
            return (a.payment?.status || "").localeCompare(b.payment?.status || "");
        }

        return new Date(a.pickup_at).getTime() - new Date(b.pickup_at).getTime();
    });
});
const methodTheme = (method) => rentalMethodTheme(method);

const cancelBooking = (id) => {
    router.post(`/rentals/${id}/cancel`);
};
</script>

<template>
    <Head :title="$t('booking.my_bookings')" />
    <GuestLayout>
        <section class="py-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div
                    class="flex flex-wrap gap-y-5 items-center justify-between mb-8"
                >
                    <h1 class="text-3xl font-extrabold text-foreground">
                        {{ $t("booking.my_bookings") }}
                    </h1>
                    <Link
                        :href="route('cars')"
                        class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-primary-600 text-white text-sm font-semibold rounded-xl hover:bg-primary-700 transition-colors"
                    >
                        <svg
                            class="w-4 h-4"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                        >
                            <path d="M12 4v16m8-8H4" />
                        </svg>
                        {{ $t("booking.new_booking") }}
                    </Link>
                </div>

                <div
                    class="mb-5 grid gap-3 rounded-2xl border border-primary-100 bg-white p-4 shadow-sm sm:grid-cols-2"
                >
                    <label class="text-sm font-medium text-foreground">
                        {{ $t("common.labels.sort_by") }}
                        <select
                            v-model="sortBy"
                            class="mt-1.5 w-full rounded-xl border-primary-200 text-sm focus:border-primary-500 focus:ring-primary-500"
                        >
                            <option
                                v-for="option in sortOptions"
                                :key="option.value"
                                :value="option.value"
                            >
                                {{ $t(option.label) }}
                            </option>
                        </select>
                    </label>

                    <label class="text-sm font-medium text-foreground">
                        {{ $t("common.labels.payment_filter") }}
                        <select
                            v-model="paymentFilter"
                            class="mt-1.5 w-full rounded-xl border-primary-200 text-sm focus:border-primary-500 focus:ring-primary-500"
                        >
                            <option value="all">
                                {{ $t("booking.filters.all_payments") }}
                            </option>
                            <option
                                v-for="status in paymentStatuses"
                                :key="status"
                                :value="status"
                            >
                                {{ $t(`payment.statuses.${status}`) }}
                            </option>
                        </select>
                    </label>
                </div>

                <!-- Bookings Table -->
                <div
                    class="bg-white rounded-2xl border border-primary-100 overflow-auto shadow-sm"
                >
                    <table class="w-full min-w-max">
                        <thead>
                            <tr class="bg-primary-50/40 border-b border-primary-100">
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wider"
                                >
                                    {{ $t("common.labels.car") }}
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wider"
                                >
                                    {{ $t("booking.rental_period") }}
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wider"
                                >
                                    {{ $t("booking.method") }}
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wider"
                                >
                                    {{ $t("common.labels.total_cost") }}
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wider"
                                >
                                    {{ $t("common.labels.status") }}
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wider"
                                >
                                    {{ $t("payment.payments") }}
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wider"
                                >
                                    {{ $t("common.labels.actions") }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-primary-100/70">
                            <!-- Booking 1 - Ongoing -->
                            <tr v-if="visibleBookings.length === 0">
                                <th
                                    class="p-4 py-10 text-center text-primary-500"
                                    colspan="7"
                                >
                                    {{ $t("booking.no_booking_yet") }}
                                </th>
                            </tr>
                            <tr
                                class="hover:bg-primary-50/40 transition-colors"
                                v-for="(booking, index) in visibleBookings"
                                :key="booking.id"
                            >
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-lg bg-primary-50 flex items-center justify-center shrink-0"
                                        >
                                            <img
                                                :src="carImageUrl(booking.car)"
                                                :alt="carImageAlt(booking.car, t)"
                                                class="w-full h-full object-contain rounded-lg"
                                            />
                                        </div>
                                        <div>
                                            <p class="text-xs font-semibold text-primary-500">
                                                {{
                                                    $t(
                                                        "booking.display_reference",
                                                        { number: index + 1 },
                                                    )
                                                }}
                                            </p>
                                            <p
                                                class="text-sm font-semibold text-foreground"
                                            >
                                                <Link
                                                    :href="
                                                        route(
                                                            'bookings.show',
                                                            booking.id,
                                                        )
                                                    "
                                                    class="transition-colors"
                                                    :class="methodTheme(booking.rental_method).link"
                                                >
                                                    {{ booking.car.name }}
                                                </Link>
                                            </p>
                                            <p class="text-xs text-muted-foreground">
                                                {{ booking.car.brand }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-foreground">
                                        {{ rentalPeriod(booking, locale) }}
                                    </p>
                                </td>
                                <td class="px-6 py-4 text-sm text-foreground">
                                    <span
                                        class="inline-flex rounded-full px-3 py-1 text-xs font-semibold"
                                        :class="methodTheme(booking.rental_method).badgeSoft"
                                    >
                                        {{ rentalMethodLabel(booking.rental_method, t) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm font-bold text-foreground">
                                        {{ formatMYR(booking.total_cost) }}
                                    </p>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        :class="[
                                            'inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-lg',
                                            bookingStatusTheme(booking.status).badge,
                                        ]"
                                    >
                                        <span
                                            :class="[
                                                'w-1.5 h-1.5 rounded-full',
                                                bookingStatusTheme(booking.status).dot,
                                            ]"
                                        ></span>
                                        {{ $t(bookingStatusLabelKey(booking.status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <PaymentStatusBadge
                                        :status="booking.payment.status"
                                    />
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <Link
                                            :href="
                                                route(
                                                    'bookings.show',
                                                    booking.id,
                                                )
                                            "
                                            class="text-sm font-medium transition-colors"
                                            :class="methodTheme(booking.rental_method).link"
                                            >{{ $t("common.actions.view") }}</Link
                                        >
                                        <span class="text-primary-200">|</span>

                                        <AlertDialog>
                                            <AlertDialogTrigger as-child>
                                                <button
                                                    v-if="
                                                        bookingCanBeCancelled(
                                                            booking.status,
                                                        )
                                                    "
                                                    class="text-red-500 hover:text-red-600 text-sm font-medium"
                                                >
                                                    {{ $t("common.actions.cancel") }}
                                                </button>
                                            </AlertDialogTrigger>
                                            <AlertDialogContent>
                                                <AlertDialogHeader>
                                                    <AlertDialogTitle
                                                        >{{ $t("booking.cancel_title") }}</AlertDialogTitle
                                                    >
                                                    <AlertDialogDescription>
                                                        {{ $t("common.dialog.cannot_be_undone") }}
                                                        {{ $t("booking.cancel_copy") }}
                                                    </AlertDialogDescription>
                                                </AlertDialogHeader>

                                                <AlertDialogFooter
                                                    class="dialog-footer"
                                                >
                                                    <AlertDialogCancel
                                                        >{{ $t("common.actions.cancel") }}</AlertDialogCancel
                                                    >

                                                    <AlertDialogAction
                                                        @click="
                                                            cancelBooking(
                                                                booking.id,
                                                            )
                                                        "
                                                    >
                                                        {{ $t("common.actions.confirm") }}
                                                    </AlertDialogAction>
                                                </AlertDialogFooter>
                                            </AlertDialogContent>
                                        </AlertDialog>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </GuestLayout>
</template>
