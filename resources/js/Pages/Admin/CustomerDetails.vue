<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import DocumentPreview from "@/Components/DocumentPreview.vue";
import PaymentStatusBadge from "@/Components/PaymentStatusBadge.vue";
import { bookingStatusLabelKey } from "@/lib/bookingStatus";
import { Link, usePage } from "@inertiajs/vue3";
import { formatMYR } from "@/lib/car";
import { paymentStatusLabel } from "@/lib/payment";
import { rentalMethodLabel, rentalPeriod } from "@/lib/rental";
import { computed, ref } from "vue";
import { useI18n } from "@/lib/i18n";

const page = usePage();
const { locale, t } = useI18n();

const customer = computed(() => page.props.customer);
const payments = computed(() => page.props.payments || []);
const canViewVerification = computed(() => page.props.canViewVerification);

const rentalSort = ref("pickup");
const rentalStatusFilter = ref("all");
const rentalMethodFilter = ref("all");
const paymentSort = ref("payment_date");
const paymentStatusFilter = ref("all");

const rentalStatuses = ["confirmed", "ongoing", "pending", "completed", "cancelled"];
const rentalMethods = ["hourly", "daily"];
const paymentStatuses = [
    "pending",
    "proof_submitted",
    "partially_paid",
    "paid",
    "refunded",
];

const verification = computed(() => customer.value?.verification);
const rentals = computed(() => customer.value?.rentals || []);

const sortedRentals = computed(() => {
    const filtered = rentals.value.filter((rental) => {
        const statusMatches =
            rentalStatusFilter.value === "all" ||
            rental.status === rentalStatusFilter.value;
        const methodMatches =
            rentalMethodFilter.value === "all" ||
            rental.rental_method === rentalMethodFilter.value;

        return statusMatches && methodMatches;
    });

    return [...filtered].sort((a, b) => {
        if (rentalSort.value === "car") {
            const first = `${a.car?.brand || ""} ${a.car?.model || ""}`;
            const second = `${b.car?.brand || ""} ${b.car?.model || ""}`;

            return first.localeCompare(second);
        }

        if (rentalSort.value === "return") {
            return new Date(a.return_at).getTime() - new Date(b.return_at).getTime();
        }

        if (rentalSort.value === "duration") {
            return Number(a.duration_units || 0) - Number(b.duration_units || 0);
        }

        if (rentalSort.value === "cost") {
            return Number(a.total_cost || 0) - Number(b.total_cost || 0);
        }

        return new Date(a.pickup_at).getTime() - new Date(b.pickup_at).getTime();
    });
});

const sortedPayments = computed(() => {
    const filtered = payments.value.filter((payment) => {
        if (paymentStatusFilter.value === "all") return true;

        return payment.status === paymentStatusFilter.value;
    });

    return [...filtered].sort((a, b) => {
        if (paymentSort.value === "amount_due") {
            return Number(a.amount_due || 0) - Number(b.amount_due || 0);
        }

        if (paymentSort.value === "amount_paid") {
            return Number(a.amount_paid || 0) - Number(b.amount_paid || 0);
        }

        if (paymentSort.value === "status") {
            return (a.status || "").localeCompare(b.status || "");
        }

        return (
            new Date(a.payment_date || a.created_at).getTime() -
            new Date(b.payment_date || b.created_at).getTime()
        );
    });
});

const paymentSummary = computed(() => {
    const totals = payments.value.reduce(
        (summary, payment) => {
            summary.amountDue += Number(payment.amount_due || 0);
            summary.amountPaid += Number(payment.amount_paid || 0);
            summary.byStatus[payment.status] =
                (summary.byStatus[payment.status] || 0) + 1;

            return summary;
        },
        {
            amountDue: 0,
            amountPaid: 0,
            byStatus: {},
        },
    );

    return totals;
});

const rentalStatusLabel = (status) =>
    t(bookingStatusLabelKey(status));

const verificationStatusLabel = (status) =>
    ({
        Pending: t("common.statuses.pending"),
        Approved: t("common.statuses.approved"),
        Rejected: t("common.statuses.rejected"),
        Suspended: t("common.statuses.suspended"),
        Expired: t("common.statuses.expired"),
    })[status] || status;
</script>

<template>
    <AdminLayout>
        <template #header-title>
            <h1 class="text-lg font-semibold text-foreground">
                {{ $t("admin.customers.profile_title") }}
            </h1>
            <p class="text-sm text-primary-500">
                {{ $t("admin.customers.profile_subtitle") }}
            </p>
        </template>

        <div class="space-y-6">
            <section class="rounded-2xl border border-primary-100 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-5 sm:flex-row sm:items-start">
                    <div
                        class="flex size-16 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-primary-500 to-accent-500 text-xl font-bold text-white"
                    >
                        {{ customer?.name?.charAt(0) }}
                    </div>
                    <div class="grid flex-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <div>
                            <p class="text-xs uppercase tracking-wider text-primary-500">
                                {{ $t("admin.customers.customer_name") }}
                            </p>
                            <p class="mt-1 text-sm font-semibold text-foreground">
                                {{ customer?.name }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wider text-primary-500">
                                {{ $t("common.labels.email") }}
                            </p>
                            <p class="mt-1 text-sm font-semibold text-foreground">
                                {{ customer?.email }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wider text-primary-500">
                                {{ $t("common.labels.status") }}
                            </p>
                            <p class="mt-1 text-sm font-semibold text-foreground">
                                {{
                                    customer?.email_verified_at
                                        ? $t("common.labels.verified")
                                        : $t("common.labels.unverified")
                                }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wider text-primary-500">
                                {{ $t("common.labels.account_status") }}
                            </p>
                            <p class="mt-1 text-sm font-semibold text-foreground">
                                {{ $t("common.labels.active") }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <section
                v-if="canViewVerification && verification"
                class="rounded-2xl border border-primary-100 bg-white p-6 shadow-sm"
            >
                <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-base font-bold text-foreground">
                            {{ $t("admin.customers.verification_info") }}
                        </h2>
                        <p class="text-xs text-primary-500">
                            {{ $t("verification.document_status") }}
                        </p>
                    </div>
                    <span class="rounded-lg bg-primary-50 px-3 py-1 text-xs font-semibold text-primary-700">
                        {{ verificationStatusLabel(verification.display_status) }}
                    </span>
                </div>

                <div class="grid gap-4 text-sm sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <p class="text-primary-500">
                            {{ $t("verification.government_id") }}
                        </p>
                        <p class="font-semibold text-foreground">
                            {{
                                verification.documents?.government_id?.available
                                    ? $t("common.labels.submitted")
                                    : $t("common.labels.not_submitted")
                            }}
                        </p>
                    </div>
                    <div>
                        <p class="text-primary-500">
                            {{ $t("verification.driving_license") }}
                        </p>
                        <p class="font-semibold text-foreground">
                            {{
                                verification.documents?.driving_license?.available
                                    ? $t("common.labels.submitted")
                                    : $t("common.labels.not_submitted")
                            }}
                        </p>
                    </div>
                    <div>
                        <p class="text-primary-500">
                            {{ $t("verification.government_id_expiry") }}
                        </p>
                        <p class="font-semibold text-foreground">
                            {{ verification.id_expiry_date || $t("common.labels.pending") }}
                        </p>
                    </div>
                    <div>
                        <p class="text-primary-500">
                            {{ $t("verification.driving_license_expiry") }}
                        </p>
                        <p class="font-semibold text-foreground">
                            {{
                                verification.driving_license_expiry_date ||
                                $t("common.labels.pending")
                            }}
                        </p>
                    </div>
                </div>

                <p
                    v-if="verification.review_note"
                    class="mt-5 rounded-xl border border-amber-200 bg-amber-50 p-3 text-sm text-amber-800"
                >
                    {{ $t("verification.current_note") }}:
                    {{ verification.review_note }}
                </p>

                <div class="mt-5 grid gap-3 sm:grid-cols-2">
                    <DocumentPreview
                        :title="$t('verification.government_id')"
                        :available="verification.documents?.government_id?.available"
                        :url="verification.documents?.government_id?.url"
                        :file-name="verification.documents?.government_id?.file_name"
                        :file-type="verification.documents?.government_id?.file_type"
                        :missing-text="$t('verification.government_id_missing')"
                    />
                    <DocumentPreview
                        :title="$t('verification.driving_license')"
                        :available="verification.documents?.driving_license?.available"
                        :url="verification.documents?.driving_license?.url"
                        :file-name="verification.documents?.driving_license?.file_name"
                        :file-type="verification.documents?.driving_license?.file_type"
                        :missing-text="$t('verification.driving_license_missing')"
                    />
                </div>
            </section>

            <section class="rounded-2xl border border-primary-100 bg-white p-6 shadow-sm">
                <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-base font-bold text-foreground">
                            {{ $t("admin.customers.payment_summary") }}
                        </h2>
                        <p class="text-xs text-primary-500">
                            {{ $t("common.labels.summary") }}
                        </p>
                    </div>
                </div>

                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-xl bg-primary-50/40 p-4">
                        <p class="text-xs text-primary-500">
                            {{ $t("payment.amount_due") }}
                        </p>
                        <p class="mt-1 text-lg font-bold text-foreground">
                            {{ formatMYR(paymentSummary.amountDue) }}
                        </p>
                    </div>
                    <div class="rounded-xl bg-primary-50/40 p-4">
                        <p class="text-xs text-primary-500">
                            {{ $t("payment.amount_paid") }}
                        </p>
                        <p class="mt-1 text-lg font-bold text-foreground">
                            {{ formatMYR(paymentSummary.amountPaid) }}
                        </p>
                    </div>
                    <div
                        v-for="status in paymentStatuses"
                        :key="status"
                        class="rounded-xl bg-primary-50/40 p-4"
                    >
                        <p class="text-xs text-primary-500">
                            {{ paymentStatusLabel(status, t) }}
                        </p>
                        <p class="mt-1 text-lg font-bold text-foreground">
                            {{ paymentSummary.byStatus[status] || 0 }}
                        </p>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-primary-100 bg-white shadow-sm">
                <div class="border-b border-primary-100/70 p-6">
                    <div class="flex flex-wrap items-end justify-between gap-3">
                        <div>
                            <h2 class="text-base font-bold text-foreground">
                                {{ $t("admin.customers.rental_history") }}
                            </h2>
                            <p class="text-xs text-primary-500">
                                {{ $t("admin.customers.history_subtitle") }}
                            </p>
                        </div>
                        <div class="grid w-full gap-2 sm:w-auto sm:grid-cols-3">
                            <select
                                v-model="rentalSort"
                                class="rounded-xl border-primary-200 text-sm focus:border-primary-500 focus:ring-primary-500"
                            >
                                <option value="pickup">
                                    {{ $t("booking.sort_options.pickup") }}
                                </option>
                                <option value="return">
                                    {{ $t("booking.return_date") }}
                                </option>
                                <option value="car">
                                    {{ $t("booking.sort_options.car") }}
                                </option>
                                <option value="duration">
                                    {{ $t("booking.sort_options.duration") }}
                                </option>
                                <option value="cost">
                                    {{ $t("booking.sort_options.cost") }}
                                </option>
                            </select>
                            <select
                                v-model="rentalStatusFilter"
                                class="rounded-xl border-primary-200 text-sm focus:border-primary-500 focus:ring-primary-500"
                            >
                                <option value="all">
                                    {{ $t("admin.filters.all_statuses") }}
                                </option>
                                <option
                                    v-for="status in rentalStatuses"
                                    :key="status"
                                    :value="status"
                                >
                                    {{ rentalStatusLabel(status) }}
                                </option>
                            </select>
                            <select
                                v-model="rentalMethodFilter"
                                class="rounded-xl border-primary-200 text-sm focus:border-primary-500 focus:ring-primary-500"
                            >
                                <option value="all">
                                    {{ $t("admin.filters.all_methods") }}
                                </option>
                                <option
                                    v-for="method in rentalMethods"
                                    :key="method"
                                    :value="method"
                                >
                                    {{ rentalMethodLabel(method, t) }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="overflow-auto">
                    <table class="w-full min-w-max">
                        <thead>
                            <tr class="border-b border-primary-100/70 bg-primary-50/40">
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                                    {{ $t("common.labels.car") }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                                    {{ $t("booking.rental_period") }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                                    {{ $t("booking.method") }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                                    {{ $t("common.labels.total_cost") }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                                    {{ $t("common.labels.status") }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-primary-100/70">
                            <tr v-if="sortedRentals.length === 0">
                                <td colspan="5" class="px-6 py-10 text-center text-sm text-primary-500">
                                    {{ $t("admin.customers.no_rentals") }}
                                </td>
                            </tr>
                            <tr
                                v-for="rental in sortedRentals"
                                :key="rental.id"
                                class="hover:bg-primary-50/40"
                            >
                                <td class="px-6 py-4 text-sm font-semibold text-foreground">
                                    {{ rental.car?.brand }} {{ rental.car?.model }}
                                </td>
                                <td class="px-6 py-4 text-sm text-muted-foreground">
                                    {{ rentalPeriod(rental, locale) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-muted-foreground">
                                    {{ rentalMethodLabel(rental.rental_method, t) }}
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-foreground">
                                    {{ formatMYR(rental.total_cost) }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="rounded-lg bg-primary-50 px-2.5 py-1 text-xs font-semibold text-primary-700">
                                        {{ rentalStatusLabel(rental.status) }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="rounded-2xl border border-primary-100 bg-white shadow-sm">
                <div class="border-b border-primary-100/70 p-6">
                    <div class="flex flex-wrap items-end justify-between gap-3">
                        <div>
                            <h2 class="text-base font-bold text-foreground">
                                {{ $t("admin.customers.payment_records") }}
                            </h2>
                            <p class="text-xs text-primary-500">
                                {{ $t("payment.manage_subtitle") }}
                            </p>
                        </div>
                        <div class="grid w-full gap-2 sm:w-auto sm:grid-cols-2">
                            <select
                                v-model="paymentSort"
                                class="rounded-xl border-primary-200 text-sm focus:border-primary-500 focus:ring-primary-500"
                            >
                                <option value="amount_due">
                                    {{ $t("payment.amount_due") }}
                                </option>
                                <option value="amount_paid">
                                    {{ $t("payment.amount_paid") }}
                                </option>
                                <option value="payment_date">
                                    {{ $t("payment.payment_date") }}
                                </option>
                                <option value="status">
                                    {{ $t("payment.payment_status") }}
                                </option>
                            </select>
                            <select
                                v-model="paymentStatusFilter"
                                class="rounded-xl border-primary-200 text-sm focus:border-primary-500 focus:ring-primary-500"
                            >
                                <option value="all">
                                    {{ $t("booking.filters.all_payments") }}
                                </option>
                                <option
                                    v-for="status in paymentStatuses"
                                    :key="status"
                                    :value="status"
                                >
                                    {{ paymentStatusLabel(status, t) }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="overflow-auto">
                    <table class="w-full min-w-max">
                        <thead>
                            <tr class="border-b border-primary-100/70 bg-primary-50/40">
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                                    {{ $t("common.labels.car") }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                                    {{ $t("payment.amount_due") }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                                    {{ $t("payment.amount_paid") }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                                    {{ $t("payment.payment_date") }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                                    {{ $t("payment.payment_status") }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                                    {{ $t("common.labels.actions") }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-primary-100/70">
                            <tr v-if="sortedPayments.length === 0">
                                <td colspan="6" class="px-6 py-10 text-center text-sm text-primary-500">
                                    {{ $t("admin.customers.no_payments") }}
                                </td>
                            </tr>
                            <tr
                                v-for="payment in sortedPayments"
                                :key="payment.id"
                                class="hover:bg-primary-50/40"
                            >
                                <td class="px-6 py-4 text-sm font-semibold text-foreground">
                                    {{ payment.rental?.car?.brand }}
                                    {{ payment.rental?.car?.model }}
                                </td>
                                <td class="px-6 py-4 text-sm text-foreground">
                                    {{ formatMYR(payment.amount_due) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-foreground">
                                    {{ formatMYR(payment.amount_paid) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-muted-foreground">
                                    {{
                                        payment.payment_date
                                            ? new Date(payment.payment_date).toLocaleDateString()
                                            : $t("common.labels.not_set")
                                    }}
                                </td>
                                <td class="px-6 py-4">
                                    <PaymentStatusBadge :status="payment.status" />
                                </td>
                                <td class="px-6 py-4">
                                    <Link
                                        :href="route('payments.edit', payment.id)"
                                        class="text-sm font-semibold text-primary-600 hover:text-primary-700"
                                    >
                                        {{ $t("payment.review_payment_button") }}
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <div v-if="page.props.canDeleteCustomer" class="pt-1">
                <Link
                    :href="route('customers.index')"
                    class="inline-flex items-center gap-2 text-sm font-medium text-muted-foreground transition-colors hover:text-primary-600"
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
                    {{ $t("admin.customers.back_to_customers") }}
                </Link>
            </div>
        </div>
    </AdminLayout>
</template>
