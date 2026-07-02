<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import PaymentStatusBadge from "@/Components/PaymentStatusBadge.vue";
import { Link, usePage } from "@inertiajs/vue3";
import { formatMYR } from "@/lib/car";
import { rentalMethodLabel, rentalPeriod } from "@/lib/rental";
import { computed } from "vue";
import { useI18n } from "@/lib/i18n";

const page = usePage();
const payments = computed(() => page.props.payments);
const { locale, t } = useI18n();

const customerRoute = (customerId) =>
    page.props.auth?.isAdmin
        ? route("customers.show", customerId)
        : route("staff.customers.show", customerId);

</script>

<template>
    <AdminLayout>
        <template #header-title>
            <h1 class="text-lg font-semibold text-foreground">
                {{ $t("payment.manage_title") }}
            </h1>
            <p class="text-sm text-primary-500">
                {{ $t("payment.manage_subtitle") }}
            </p>
        </template>

        <div class="flex items-center justify-between mb-6">
            <p class="text-sm text-muted-foreground">
                {{ $t("common.labels.showing") }}
                <strong class="text-foreground">{{ payments.length }}</strong>
                {{ $t("payment.payments") }}
            </p>
        </div>

        <div
            class="bg-white rounded-2xl border border-primary-100 overflow-auto shadow-sm"
        >
            <table class="w-full min-w-max">
                <thead>
                    <tr class="bg-primary-50/40 border-b border-primary-100/70">
                        <th class="px-6 py-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wider">
                            {{ $t("booking.rental_id") }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wider">
                            {{ $t("common.labels.customer") }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wider">
                            {{ $t("common.labels.car") }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wider">
                            {{ $t("booking.rental_period") }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wider">
                            {{ $t("common.labels.amount") }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wider">
                            {{ $t("payment.payment_status") }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wider">
                            {{ $t("payment.proof_of_payment") }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wider">
                            {{ $t("common.labels.actions") }}
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-primary-100/70">
                    <tr v-if="payments.length === 0">
                        <td colspan="8" class="px-6 py-10 text-center text-sm text-primary-500">
                            {{ $t("payment.no_records") }}
                        </td>
                    </tr>
                    <tr
                        v-for="payment in payments"
                        :key="payment.id"
                        class="hover:bg-primary-50/40 transition-colors"
                    >
                        <td class="px-6 py-4 text-sm font-semibold text-foreground">
                            # {{ payment.rental.id }}
                            <p class="text-xs font-normal text-primary-500">
                                {{ rentalMethodLabel(payment.rental.rental_method, t) }}
                            </p>
                        </td>
                        <td class="px-6 py-4 text-sm text-foreground">
                            <Link
                                :href="customerRoute(payment.rental.user.id)"
                                class="font-semibold text-primary-600 hover:text-primary-700"
                            >
                                {{ payment.rental.user.name }}
                            </Link>
                        </td>
                        <td class="px-6 py-4 text-sm text-foreground">
                            {{ payment.rental.car.brand }}
                            {{ payment.rental.car.model }}
                        </td>
                        <td class="px-6 py-4 text-sm text-muted-foreground">
                            {{ rentalPeriod(payment.rental, locale) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-foreground">
                            <p class="font-semibold">
                                {{ formatMYR(payment.amount_paid) }}
                            </p>
                            <p class="text-xs text-primary-500">
                                {{ $t("common.labels.of") }} {{ formatMYR(payment.amount_due) }}
                            </p>
                        </td>
                        <td class="px-6 py-4">
                            <PaymentStatusBadge :status="payment.status" />
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <a
                                v-if="payment.proof_path"
                                :href="route('payments.proof', payment.id)"
                                target="_blank"
                                class="inline-flex items-center gap-1 text-primary-600 hover:text-primary-700 font-medium transition-colors"
                            >
                                {{ $t("payment.view_proof") }}
                            </a>
                            <span v-else class="text-primary-500">{{ $t("common.labels.not_submitted") }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <Link
                                :href="route('payments.edit', payment.id)"
                                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-semibold text-primary-600 hover:bg-primary-50 hover:text-primary-700 transition-colors"
                            >
                                {{ $t("payment.review_payment_button") }}
                            </Link>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>
