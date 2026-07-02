<script setup>
import Select from "@/Components/ui/select/Select.vue";
import SelectContent from "@/Components/ui/select/SelectContent.vue";
import SelectGroup from "@/Components/ui/select/SelectGroup.vue";
import SelectItem from "@/Components/ui/select/SelectItem.vue";
import SelectLabel from "@/Components/ui/select/SelectLabel.vue";
import SelectTrigger from "@/Components/ui/select/SelectTrigger.vue";
import SelectValue from "@/Components/ui/select/SelectValue.vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import PaymentStatusBadge from "@/Components/PaymentStatusBadge.vue";
import { Link, useForm, usePage } from "@inertiajs/vue3";
import { formatMYR } from "@/lib/car";
import { paymentMethodLabel } from "@/lib/payment";
import { rentalMethodLabel, rentalPeriod } from "@/lib/rental";
import { useI18n } from "@/lib/i18n";

const page = usePage();
const payment = page.props.payment;
const { locale, t } = useI18n();
const customerRoute = (customerId) =>
    page.props.auth?.isAdmin
        ? route("customers.show", customerId)
        : route("staff.customers.show", customerId);

const form = useForm({
    amount_paid: payment.amount_paid,
    payment_method: payment.payment_method ?? "",
    payment_date: payment.payment_date?.slice(0, 16) ?? "",
    status: payment.status,
    notes: payment.notes ?? "",
});

const submit = () => form.post(route("payments.update", payment.id));
</script>

<template>
    <AdminLayout>
        <template #header-title>
            <h1 class="text-lg font-semibold text-foreground">
                {{ $t("payment.review_payment") }}
            </h1>
            <p class="text-sm text-primary-500">
                Rental # {{ payment.rental.id }}
            </p>
        </template>

        <div class="max-w-3xl mx-auto space-y-6">
            <div
                class="bg-white rounded-2xl border border-primary-100 p-6 sm:p-8 shadow-sm"
            >
                <div
                    class="flex flex-wrap items-start justify-between gap-4 mb-6"
                >
                    <div>
                        <h2 class="text-xl font-bold text-foreground">
                            Rental # {{ payment.rental.id }}
                        </h2>
                        <p class="text-sm text-muted-foreground mt-1">
                            {{ $t("payment.review_subtitle") }}
                        </p>
                    </div>
                    <PaymentStatusBadge :status="payment.status" />
                </div>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div class="p-4 bg-primary-50/40 rounded-xl">
                        <p class="text-xs text-primary-500 mb-1">{{ $t("common.labels.customer") }}</p>
                        <Link
                            :href="customerRoute(payment.rental.user.id)"
                            class="text-sm font-semibold text-primary-600 hover:text-primary-700"
                        >
                            {{ payment.rental.user.name }}
                        </Link>
                    </div>
                    <div class="p-4 bg-primary-50/40 rounded-xl">
                        <p class="text-xs text-primary-500 mb-1">{{ $t("common.labels.car") }}</p>
                        <p class="text-sm font-semibold text-foreground">
                            {{ payment.rental.car.brand }}
                            {{ payment.rental.car.model }}
                        </p>
                    </div>
                    <div class="p-4 bg-primary-50/40 rounded-xl">
                        <p class="text-xs text-primary-500 mb-1">{{ $t("booking.rental_method") }}</p>
                        <p class="text-sm font-semibold text-foreground">
                            {{ rentalMethodLabel(payment.rental.rental_method, t) }}
                        </p>
                    </div>
                    <div class="p-4 bg-primary-50/40 rounded-xl">
                        <p class="text-xs text-primary-500 mb-1">{{ $t("booking.rental_period") }}</p>
                        <p class="text-sm font-semibold text-foreground">
                            {{ rentalPeriod(payment.rental, locale) }}
                        </p>
                    </div>
                    <div class="p-4 bg-primary-50/40 rounded-xl">
                        <p class="text-xs text-primary-500 mb-1">{{ $t("payment.amount_due") }}</p>
                        <p class="text-base font-bold text-primary-600">
                            {{ formatMYR(payment.amount_due) }}
                        </p>
                    </div>
                    <div class="p-4 bg-primary-50/40 rounded-xl">
                        <p class="text-xs text-primary-500 mb-1">{{ $t("payment.amount_paid") }}</p>
                        <p class="text-sm font-semibold text-foreground">
                            {{ formatMYR(payment.amount_paid) }}
                        </p>
                    </div>
                    <div class="p-4 bg-primary-50/40 rounded-xl">
                        <p class="text-xs text-primary-500 mb-1">
                            {{ $t("payment.payment_method") }}
                        </p>
                        <p class="text-sm font-semibold text-foreground">
                            {{ paymentMethodLabel(payment.payment_method, t) }}
                        </p>
                    </div>
                    <div class="p-4 bg-primary-50/40 rounded-xl">
                        <p class="text-xs text-primary-500 mb-1">{{ $t("payment.proof_of_payment") }}</p>
                        <a
                            v-if="payment.proof_path"
                            :href="route('payments.proof', payment.id)"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-1 text-sm font-semibold text-primary-600 hover:text-primary-700 transition-colors"
                        >
                            {{ $t("payment.view_uploaded_proof") }}
                        </a>
                        <p v-else class="text-sm text-primary-500">
                            {{ $t("common.labels.not_submitted") }}
                        </p>
                    </div>
                </div>
            </div>

            <form
                @submit.prevent="submit"
                class="bg-white rounded-2xl border border-primary-100 p-6 sm:p-8 shadow-sm"
            >
                <div class="mb-6">
                    <h2 class="text-xl font-bold text-foreground">
                        {{ $t("payment.manual_review") }}
                    </h2>
                    <p class="text-sm text-muted-foreground mt-1">
                        {{ $t("payment.manual_review_copy") }}
                    </p>
                </div>

                <div class="grid sm:grid-cols-2 gap-5">
                    <div>
                        <label
                            class="block text-sm font-medium text-foreground mb-1.5"
                        >
                            {{ $t("payment.amount_paid_rm") }}
                        </label>
                        <input
                            v-model="form.amount_paid"
                            type="number"
                            min="0"
                            step="0.01"
                            placeholder="e.g. 150.00"
                            class="w-full px-4 py-3 rounded-xl border border-primary-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                        />
                        <p
                            v-if="form.errors.amount_paid"
                            class="text-sm text-red-500 mt-1"
                        >
                            {{ form.errors.amount_paid }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-foreground mb-1.5"
                        >
                            {{ $t("payment.payment_method") }}
                        </label>
                        <Select v-model="form.payment_method">
                            <SelectTrigger
                                class="w-full h-auto px-4 py-3 rounded-xl border-primary-200 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                            >
                                <SelectValue :placeholder="$t('payment.select_method')" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectGroup>
                                    <SelectLabel>{{ $t("payment.payment_method") }}</SelectLabel>
                                    <SelectItem value="cash">{{ $t("payment.methods.cash") }}</SelectItem>
                                    <SelectItem value="bank_transfer">
                                        {{ $t("payment.methods.bank_transfer") }}
                                    </SelectItem>
                                    <SelectItem value="duitnow_qr">
                                        {{ $t("payment.methods.duitnow_qr") }}
                                    </SelectItem>
                                    <SelectItem value="other">{{ $t("payment.methods.other") }}</SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                        <p
                            v-if="form.errors.payment_method"
                            class="text-sm text-red-500 mt-1"
                        >
                            {{ form.errors.payment_method }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-foreground mb-1.5"
                        >
                            {{ $t("payment.payment_date") }}
                        </label>
                        <input
                            v-model="form.payment_date"
                            type="datetime-local"
                            class="w-full px-4 py-3 rounded-xl border border-primary-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                        />
                        <p
                            v-if="form.errors.payment_date"
                            class="text-sm text-red-500 mt-1"
                        >
                            {{ form.errors.payment_date }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-foreground mb-1.5"
                        >
                            {{ $t("payment.payment_status") }}
                        </label>
                        <Select v-model="form.status">
                            <SelectTrigger
                                class="w-full h-auto px-4 py-3 rounded-xl border-primary-200 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                            >
                                <SelectValue :placeholder="$t('payment.select_status')" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectGroup>
                                    <SelectLabel>{{ $t("payment.payment_status") }}</SelectLabel>
                                    <SelectItem value="pending">
                                        {{ $t("payment.statuses.pending") }}
                                    </SelectItem>
                                    <SelectItem value="proof_submitted">
                                        {{ $t("payment.statuses.proof_submitted") }}
                                    </SelectItem>
                                    <SelectItem value="partially_paid">
                                        {{ $t("payment.statuses.partially_paid") }}
                                    </SelectItem>
                                    <SelectItem value="paid">{{ $t("payment.statuses.paid") }}</SelectItem>
                                    <SelectItem value="refunded">
                                        {{ $t("payment.statuses.refunded") }}
                                    </SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                        <p
                            v-if="form.errors.status"
                            class="text-sm text-red-500 mt-1"
                        >
                            {{ form.errors.status }}
                        </p>
                    </div>
                </div>

                <div class="mt-5">
                    <label
                        class="block text-sm font-medium text-foreground mb-1.5"
                    >
                        {{ $t("common.labels.notes") }}
                    </label>
                    <textarea
                        v-model="form.notes"
                        rows="4"
                        class="w-full px-4 py-3 rounded-xl border border-primary-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                        :placeholder="$t('payment.notes_placeholder')"
                    ></textarea>
                    <p v-if="form.errors.notes" class="text-sm text-red-500 mt-1">
                        {{ form.errors.notes }}
                    </p>
                </div>

                <div
                    class="flex flex-wrap items-center gap-3 pt-5 mt-6 border-t border-primary-100/70"
                >
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-6 py-3 bg-primary-600 text-white text-sm font-semibold rounded-xl hover:bg-primary-700 transition-colors shadow-md shadow-primary-500/20 disabled:opacity-50"
                    >
                        {{ form.processing ? $t("common.actions.updating") : $t("payment.update_payment") }}
                    </button>
                    <Link
                        :href="route('payments.index')"
                        class="px-6 py-3 text-muted-foreground text-sm font-medium rounded-xl hover:bg-primary-50 transition-colors"
                    >
                        {{ $t("common.actions.cancel") }}
                    </Link>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
