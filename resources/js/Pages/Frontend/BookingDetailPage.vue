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
import { Link, router, useForm, usePage } from "@inertiajs/vue3";
import { carImageAlt, carImageUrl, formatMYR } from "@/lib/car";
import {
    formatRentalDateTime,
    rentalMethodLabel,
    rentalMethodTheme,
    rentalUnitLabel,
} from "@/lib/rental";
import { computed } from "vue";
import { paymentMethodLabel } from "@/lib/payment";
import { useI18n } from "@/lib/i18n";

const page = usePage();

const booking = computed(() => page.props.rental);
const payment = computed(() => booking.value.payment);
const displayReference = computed(() => page.props.displayReference || 1);
const { locale, t } = useI18n();
const theme = computed(() => rentalMethodTheme(booking.value.rental_method));

const proofForm = useForm({
    proof: null,
});

const submitProof = () => {
    proofForm.post(route("bookings.payment-proof.store", booking.value.id), {
        forceFormData: true,
        onSuccess: () => proofForm.reset("proof"),
    });
};

const cancelBooking = (id) => {
    router.post(`/rentals/${id}/cancel`);
};
</script>

<template>
    <GuestLayout>
        <section class="py-10">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Breadcrumb -->
                <nav
                    class="flex items-center gap-2 text-sm text-muted-foreground mb-8"
                >
                    <Link
                        to="/"
                        class="transition-colors"
                        :class="theme.mutedHover"
                        >{{ $t("nav.home") }}</Link
                    >
                    <svg
                        class="w-4 h-4"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24"
                    >
                        <path d="M9 5l7 7-7 7" />
                    </svg>
                    <Link
                        :href="route('bookings.index')"
                        class="transition-colors"
                        :class="theme.mutedHover"
                        >{{ $t("booking.my_bookings") }}</Link
                    >
                    <svg
                        class="w-4 h-4"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24"
                    >
                        <path d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="text-foreground font-medium">
                        {{
                            $t("booking.display_reference", {
                                number: displayReference,
                            })
                        }}
                    </span>
                </nav>

                <h1 class="text-3xl font-extrabold text-foreground mb-8">
                    {{ $t("booking.details") }}
                </h1>

                <div
                    class="overflow-hidden rounded-2xl border shadow-sm"
                    :class="[theme.card, theme.border]"
                >
                    <!-- Status Header -->
                    <div
                        class="flex items-center justify-between border-b px-8 py-5"
                        :class="[theme.surface, theme.border]"
                    >
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-full"
                                :class="theme.badge"
                            >
                                <svg
                                    class="h-5 w-5"
                                    :class="theme.text"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                                    />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-foreground">
                                    {{
                                        $t("booking.display_reference", {
                                            number: displayReference,
                                        })
                                    }}
                                </p>
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
                            </div>
                        </div>
                        <AlertDialog>
                            <AlertDialogTrigger as-child>
                                <button
                                    v-if="bookingCanBeCancelled(booking.status)"
                                    class="px-5 py-2 bg-red-500 text-white text-sm font-semibold rounded-xl hover:bg-red-600 transition-colors"
                                >
                                    {{ $t("booking.cancel_booking") }}
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

                                <AlertDialogFooter class="dialog-footer">
                                    <AlertDialogCancel>{{ $t("common.actions.cancel") }}</AlertDialogCancel>

                                    <AlertDialogAction
                                        @click="cancelBooking(booking.id)"
                                    >
                                        {{ $t("common.actions.confirm") }}
                                    </AlertDialogAction>
                                </AlertDialogFooter>
                            </AlertDialogContent>
                        </AlertDialog>
                    </div>

                    <div class="p-8 space-y-8">
                        <!-- Car Details -->
                        <div>
                            <h3
                                class="mb-4 text-xs font-semibold uppercase tracking-wider"
                                :class="theme.text"
                            >
                                {{ $t("booking.car_details") }}
                            </h3>
                            <div
                                class="flex items-center gap-4 rounded-xl p-4"
                                :class="theme.surface"
                            >
                                <div
                                    class="flex h-14 w-16 items-center justify-center rounded-lg border bg-white"
                                    :class="theme.border"
                                >
                                    <img
                                        :src="carImageUrl(booking.car)"
                                        :alt="carImageAlt(booking.car, t)"
                                        class="w-full h-full object-cover rounded-lg"
                                    />
                                </div>
                                <div>
                                    <p class="font-bold text-foreground">
                                        {{ booking.car.name }}
                                    </p>
                                    <p class="text-sm text-muted-foreground">
                                        {{ booking.car.brand }} •
                                        {{ booking.car.model }} •
                                        {{ booking.car.year }} •
                                        {{ booking.car.car_type }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Rental Period -->
                        <div>
                            <h3
                                class="mb-4 text-xs font-semibold uppercase tracking-wider"
                                :class="theme.text"
                            >
                                {{ $t("booking.rental_period") }}
                            </h3>
                            <div class="grid sm:grid-cols-2 gap-4">
                                <div class="rounded-xl p-4" :class="theme.surface">
                                    <p class="mb-1 text-xs" :class="theme.text">
                                        {{ $t("booking.rental_method") }}
                                    </p>
                                    <p
                                        class="inline-flex rounded-full px-3 py-1 text-sm font-semibold"
                                        :class="theme.badgeSoft"
                                    >
                                        {{
                                            rentalMethodLabel(
                                                booking.rental_method,
                                                t,
                                            )
                                        }}
                                    </p>
                                </div>
                                <div class="rounded-xl p-4" :class="theme.surface">
                                    <p class="mb-1 text-xs" :class="theme.text">
                                        {{
                                            booking.rental_method === "hourly"
                                                ? $t("booking.pickup_time")
                                                : $t("booking.preferred_pickup_time")
                                        }}
                                    </p>
                                    <p
                                        class="text-sm font-semibold text-foreground"
                                    >
                                        {{
                                            formatRentalDateTime(
                                                booking.pickup_at,
                                                locale,
                                            )
                                        }}
                                    </p>
                                </div>
                                <div class="rounded-xl p-4" :class="theme.surface">
                                    <p class="mb-1 text-xs" :class="theme.text">
                                        {{
                                            booking.rental_method === "hourly"
                                                ? $t("booking.return_time")
                                                : $t("booking.preferred_return_time")
                                        }}
                                    </p>
                                    <p
                                        class="text-sm font-semibold text-foreground"
                                    >
                                        {{
                                            formatRentalDateTime(
                                                booking.return_at,
                                                locale,
                                            )
                                        }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Payment -->
                        <div>
                            <h3
                                class="mb-4 text-xs font-semibold uppercase tracking-wider"
                                :class="theme.text"
                            >
                                {{ $t("booking.payment_summary") }}
                            </h3>
                            <div class="space-y-2 rounded-xl p-4" :class="theme.surface">
                                <div class="flex justify-between text-sm">
                                    <span class="text-muted-foreground"
                                        >{{
                                            booking.rental_method === "hourly"
                                                ? $t("booking.hourly_rate")
                                                : $t("booking.daily_rate")
                                        }}</span
                                    >
                                    <span class="text-foreground"
                                        >{{
                                            formatMYR(booking.applied_rate)
                                        }}</span
                                    >
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-muted-foreground">{{ $t("common.labels.duration") }}</span>
                                    <span class="text-foreground"
                                        >{{
                                            rentalUnitLabel(
                                                booking.rental_method,
                                                booking.duration_units,
                                                t,
                                            )
                                        }}</span
                                    >
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-muted-foreground"
                                        >{{ $t("payment.payment_method") }}</span
                                    >
                                    <span class="text-foreground">{{
                                        paymentMethodLabel(
                                            payment.payment_method,
                                            t,
                                        )
                                    }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-muted-foreground"
                                        >{{ $t("payment.payment_status") }}</span
                                    >
                                    <PaymentStatusBadge
                                        :status="payment.status"
                                    />
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-muted-foreground"
                                        >{{ $t("payment.amount_paid") }}</span
                                    >
                                    <span class="text-foreground">{{
                                        formatMYR(payment.amount_paid)
                                    }}</span>
                                </div>
                                <div
                                    class="mt-2 flex justify-between border-t pt-2"
                                    :class="theme.border"
                                >
                                    <span class="font-bold text-foreground"
                                        >{{ $t("common.labels.total_cost") }}</span
                                    >
                                    <span
                                        class="text-lg font-extrabold"
                                        :class="theme.text"
                                    >
                                        {{ formatMYR(booking.total_cost) }}</span
                                    >
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3
                                class="mb-4 text-xs font-semibold uppercase tracking-wider"
                                :class="theme.text"
                            >
                                {{ $t("payment.proof_of_payment") }}
                            </h3>
                            <div
                                class="rounded-xl border p-5"
                                :class="[theme.surfaceStrong, theme.border]"
                            >
                                <div
                                    class="flex flex-wrap items-start justify-between gap-3"
                                >
                                    <div>
                                        <p
                                            class="text-sm font-semibold text-foreground"
                                        >
                                            {{ $t("car.manual_payment_title") }}
                                        </p>
                                        <p class="mt-1 text-xs" :class="theme.text">
                                            {{ $t("payment.proof_copy") }}
                                        </p>
                                    </div>
                                    <PaymentStatusBadge
                                        :status="payment.status"
                                    />
                                </div>

                                <a
                                    v-if="payment.proof_path"
                                    :href="route('payments.proof', payment.id)"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="mt-4 inline-flex text-sm font-semibold transition-colors"
                                    :class="theme.link"
                                >
                                    {{ $t("payment.view_uploaded_proof") }}
                                </a>

                                <form
                                    v-if="payment.can_upload_proof"
                                    @submit.prevent="submitProof"
                                    class="mt-4 space-y-3 border-t pt-4"
                                    :class="theme.border"
                                >
                                    <input
                                        type="file"
                                        accept=".pdf,.jpg,.jpeg,.png"
                                        class="block w-full text-sm text-muted-foreground file:mr-4 file:rounded-lg file:border-0 file:px-4 file:py-2.5 file:font-semibold"
                                        :class="theme.fileInput"
                                        @change="
                                            proofForm.proof =
                                                $event.target.files[0]
                                        "
                                    />
                                    <p class="text-xs text-muted-foreground">
                                        {{ $t("common.files.accepted_payment") }}
                                    </p>
                                    <p
                                        v-if="proofForm.errors.proof"
                                        class="text-sm text-red-500"
                                    >
                                        {{ proofForm.errors.proof }}
                                    </p>
                                    <button
                                        type="submit"
                                        :disabled="
                                            proofForm.processing ||
                                            !proofForm.proof
                                        "
                                        class="rounded-xl px-5 py-2.5 text-sm font-semibold text-white transition-colors disabled:opacity-50"
                                        :class="theme.solidButton"
                                    >
                                        {{
                                            payment.proof_path
                                                ? $t("payment.replace_proof")
                                                : $t("payment.upload_proof")
                                        }}
                                    </button>
                                </form>
                                <p
                                    v-else-if="payment.proof_path"
                                    class="text-xs text-muted-foreground mt-4"
                                >
                                    {{ $t("payment.proof_locked") }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <Link
                        :href="route('bookings.index')"
                        class="inline-flex items-center gap-2 text-sm font-medium text-muted-foreground transition-colors"
                        :class="theme.mutedHover"
                    >
                        <svg
                            class="w-4 h-4"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                        >
                            <path d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        {{ $t("booking.back_to_bookings") }}
                    </Link>
                </div>
            </div>
        </section>
    </GuestLayout>
</template>
