<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InfoHint from "@/Components/InfoHint.vue";
import PaymentStatusBadge from "@/Components/PaymentStatusBadge.vue";
import { Link, useForm, usePage } from "@inertiajs/vue3";
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
const { locale, t } = useI18n();

const booking = computed(() => page.props.rental);
const payment = computed(() => booking.value.payment);
const displayReference = computed(() => page.props.displayReference || 1);
const theme = computed(() => rentalMethodTheme(booking.value.rental_method));
const tooltipClass = computed(() => `${theme.value.card} ${theme.value.border}`);

const proofForm = useForm({
    proof: null,
});

const submitProof = () => {
    proofForm.post(route("bookings.payment-proof.store", booking.value.id), {
        forceFormData: true,
        onSuccess: () => proofForm.reset("proof"),
    });
};
</script>

<template>
    <GuestLayout>
        <section class="py-10" :class="theme.pageBackground">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                <nav class="flex flex-wrap items-center gap-2 text-sm text-muted-foreground">
                    <Link :href="route('customer.dashboard')" class="transition-colors" :class="theme.link">
                        {{ $t("booking.customer_dashboard") }}
                    </Link>
                    <span>/</span>
                    <Link :href="route('bookings.index')" class="transition-colors" :class="theme.link">
                        {{ $t("booking.my_bookings") }}
                    </Link>
                    <span>/</span>
                    <span>{{ $t("booking.payment_next_step") }}</span>
                </nav>

                <div class="mt-4 max-w-2xl">
                    <p class="text-xs font-semibold tracking-[0.18em]" :class="theme.text">
                        {{ $t("booking.payment_next_step") }}
                    </p>
                    <h1 class="mt-1.5 text-2xl font-extrabold tracking-tight text-foreground sm:text-3xl">
                        {{ $t("booking.payment_page_title") }}
                    </h1>
                    <p class="mt-1 text-sm leading-6 text-muted-foreground">
                        {{ $t("booking.payment_page_copy") }}
                    </p>
                </div>

                <div class="mt-6 grid gap-8 lg:grid-cols-[1fr_0.92fr]">
                    <section
                        class="rounded-[2rem] border p-6 shadow-[0_24px_80px_-36px_rgba(30,41,59,0.35)]"
                        :class="[theme.card, theme.border]"
                    >
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.16em]" :class="theme.text">
                                    {{
                                        $t("booking.display_reference", {
                                            number: displayReference,
                                        })
                                    }}
                                </p>
                                <h2 class="mt-2 text-2xl font-extrabold tracking-tight text-foreground">
                                    {{ booking.car.name }}
                                </h2>
                                <p class="mt-2 text-sm leading-6 text-muted-foreground">
                                    {{ booking.car.brand }} · {{ booking.car.model }} · {{ booking.car.year }}
                                </p>
                            </div>
                            <PaymentStatusBadge :status="payment.status" />
                        </div>

                        <div class="mt-6 overflow-hidden rounded-[1.75rem]" :class="theme.surface">
                            <img
                                :src="carImageUrl(booking.car)"
                                :alt="carImageAlt(booking.car, $t)"
                                class="h-64 w-full object-cover"
                            />
                        </div>

                        <div class="mt-6 grid gap-4 sm:grid-cols-2">
                            <div class="rounded-[1.5rem] p-4" :class="theme.surface">
                                <p class="text-xs font-semibold uppercase tracking-[0.16em]" :class="theme.text">
                                    {{ $t("booking.rental_method") }}
                                </p>
                                <p class="mt-1 text-sm font-semibold text-foreground">
                                    {{ rentalMethodLabel(booking.rental_method, t) }}
                                </p>
                            </div>

                            <div class="rounded-[1.5rem] p-4" :class="theme.surface">
                                <p class="text-xs font-semibold uppercase tracking-[0.16em]" :class="theme.text">
                                    {{ $t("common.labels.duration") }}
                                </p>
                                <p class="mt-1 text-sm font-semibold text-foreground">
                                    {{
                                        rentalUnitLabel(
                                            booking.rental_method,
                                            booking.duration_units,
                                            t,
                                        )
                                    }}
                                </p>
                            </div>

                            <div class="rounded-[1.5rem] p-4" :class="theme.surface">
                                <p class="text-xs font-semibold uppercase tracking-[0.16em]" :class="theme.text">
                                    {{ $t("booking.pickup_time") }}
                                </p>
                                <p class="mt-1 text-sm font-semibold text-foreground">
                                    {{ formatRentalDateTime(booking.pickup_at, locale) }}
                                </p>
                            </div>

                            <div class="rounded-[1.5rem] p-4" :class="theme.surface">
                                <p class="text-xs font-semibold uppercase tracking-[0.16em]" :class="theme.text">
                                    {{ $t("booking.return_time") }}
                                </p>
                                <p class="mt-1 text-sm font-semibold text-foreground">
                                    {{ formatRentalDateTime(booking.return_at, locale) }}
                                </p>
                            </div>
                        </div>
                    </section>

                    <section
                        class="rounded-[2rem] border p-6 shadow-[0_24px_80px_-36px_rgba(30,41,59,0.35)]"
                        :class="[theme.card, theme.border]"
                    >
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-semibold tracking-[0.18em]" :class="theme.text">
                                {{ $t("payment.proof_of_payment") }}
                            </p>
                            <InfoHint
                                :text="$t('booking.tooltips.payment_upload')"
                                :icon-class="theme.text"
                                :tooltip-class="tooltipClass"
                            />
                        </div>
                        <h2 class="mt-2 text-2xl font-extrabold tracking-tight text-foreground">
                            {{ $t("booking.payment_panel_title") }}
                        </h2>
                        <p class="mt-2 text-sm leading-6 text-muted-foreground">
                            {{ $t("payment.proof_copy") }}
                        </p>

                        <div class="mt-6 space-y-4 rounded-[1.75rem] p-5 text-white" :class="theme.panel">
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-sm" :class="theme.panelSubtext">
                                    {{ $t("payment.amount_due") }}
                                </span>
                                <span class="text-2xl font-extrabold">
                                    {{ formatMYR(payment.amount_due) }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-sm" :class="theme.panelSubtext">
                                    {{ $t("payment.payment_method") }}
                                </span>
                                <span class="text-sm font-semibold">
                                    {{ paymentMethodLabel(payment.payment_method, t) }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-sm" :class="theme.panelSubtext">
                                    {{ $t("payment.amount_paid") }}
                                </span>
                                <span class="text-sm font-semibold">
                                    {{ formatMYR(payment.amount_paid) }}
                                </span>
                            </div>
                        </div>

                        <a
                            v-if="payment.proof_path"
                            :href="route('payments.proof', payment.id)"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="mt-6 inline-flex text-sm font-semibold transition-colors"
                            :class="theme.link"
                        >
                            {{ $t("payment.view_uploaded_proof") }}
                        </a>

                        <form
                            v-if="payment.can_upload_proof"
                            class="mt-6 space-y-4"
                            @submit.prevent="submitProof"
                        >
                            <input
                                type="file"
                                accept=".pdf,.jpg,.jpeg,.png"
                                class="block w-full text-sm text-muted-foreground file:mr-4 file:rounded-xl file:border-0 file:px-4 file:py-2.5 file:font-semibold"
                                :class="theme.fileInput"
                                @change="proofForm.proof = $event.target.files[0]"
                            />
                            <p class="text-xs leading-6 text-muted-foreground">
                                {{ $t("common.files.accepted_payment") }}
                            </p>
                            <p v-if="proofForm.errors.proof" class="text-xs text-red-500">
                                {{ proofForm.errors.proof }}
                            </p>
                            <button
                                type="submit"
                                :disabled="proofForm.processing || !proofForm.proof"
                                class="inline-flex min-h-12 w-full items-center justify-center rounded-2xl px-5 text-sm font-semibold transition-colors disabled:cursor-not-allowed disabled:opacity-60"
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
                            class="mt-6 rounded-[1.5rem] px-4 py-4 text-sm text-muted-foreground"
                            :class="theme.surface"
                        >
                            {{ $t("payment.proof_locked") }}
                        </p>

                        <div class="mt-6 flex flex-wrap items-center gap-4 border-t pt-5" :class="theme.border">
                            <Link
                                :href="route('bookings.show', booking.id)"
                                class="text-sm font-semibold transition-colors"
                                :class="theme.link"
                            >
                                {{ $t("booking.view_booking") }}
                            </Link>
                            <Link
                                :href="route('bookings.index')"
                                class="text-sm font-semibold text-muted-foreground transition-colors"
                                :class="theme.mutedHover"
                            >
                                {{ $t("booking.back_to_bookings") }}
                            </Link>
                        </div>
                    </section>
                </div>
            </div>
        </section>
    </GuestLayout>
</template>
