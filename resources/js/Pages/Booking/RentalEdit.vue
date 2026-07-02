<script setup>
import Select from "@/Components/ui/select/Select.vue";
import SelectContent from "@/Components/ui/select/SelectContent.vue";
import SelectGroup from "@/Components/ui/select/SelectGroup.vue";
import SelectItem from "@/Components/ui/select/SelectItem.vue";
import SelectLabel from "@/Components/ui/select/SelectLabel.vue";
import SelectTrigger from "@/Components/ui/select/SelectTrigger.vue";
import SelectValue from "@/Components/ui/select/SelectValue.vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Link, useForm, usePage } from "@inertiajs/vue3";
import { formatMYR } from "@/lib/car";
import {
    rentalMethodLabel,
    rentalPeriod,
    rentalUnitLabel,
} from "@/lib/rental";
import { useI18n } from "@/lib/i18n";

const rental = usePage().props.rental;
const { locale, t } = useI18n();

const form = useForm({
    status: rental.status,
});

const submit = () => {
    form.post(route("rentals.update", rental.id));
};
</script>

<template>
    <AdminLayout>
        <template #header-title>
            <h1 class="text-lg font-semibold text-foreground">
                {{ $t("admin.rentals.update_title") }}
            </h1>
            <p class="text-sm text-primary-500">
                {{ $t("admin.rentals.update_subtitle") }}
            </p>
        </template>

        <div class="max-w-3xl mx-auto">
            <div
                class="bg-white rounded-2xl border border-primary-100 p-6 sm:p-8 shadow-sm"
            >
                <div class="mb-6">
                    <h2 class="text-xl font-bold text-foreground">
                        Rental # {{ rental.id }}
                    </h2>
                    <p class="text-sm text-muted-foreground mt-1">
                        {{ $t("admin.rentals.keep_status_copy") }}
                    </p>
                </div>

                <div class="grid sm:grid-cols-2 gap-4 mb-6">
                    <div class="p-4 bg-primary-50/40 rounded-xl">
                        <p class="text-xs text-primary-500 mb-1">{{ $t("booking.rental_method") }}</p>
                        <p class="text-sm font-semibold text-foreground">
                            {{ rentalMethodLabel(rental.rental_method, t) }}
                        </p>
                    </div>
                    <div class="p-4 bg-primary-50/40 rounded-xl">
                        <p class="text-xs text-primary-500 mb-1">{{ $t("booking.rental_period") }}</p>
                        <p class="text-sm font-semibold text-foreground">
                            {{ rentalPeriod(rental, locale) }}
                        </p>
                    </div>
                    <div class="p-4 bg-primary-50/40 rounded-xl">
                        <p class="text-xs text-primary-500 mb-1">{{ $t("common.labels.duration") }}</p>
                        <p class="text-sm font-semibold text-foreground">
                            {{
                                rentalUnitLabel(
                                    rental.rental_method,
                                    rental.duration_units,
                                    t,
                                )
                            }}
                        </p>
                    </div>
                    <div class="p-4 bg-primary-50/40 rounded-xl">
                        <p class="text-xs text-primary-500 mb-1">{{ $t("common.labels.total_cost") }}</p>
                        <p class="text-sm font-semibold text-foreground">
                            {{ formatMYR(rental.total_cost) }}
                        </p>
                    </div>
                </div>

                <form @submit.prevent="submit" class="space-y-5">
                    <div>
                        <label
                            class="block text-sm font-medium text-foreground mb-1.5"
                        >
                            {{ $t("admin.rentals.rental_status") }}
                        </label>

                        <Select v-model="form.status">
                            <SelectTrigger
                                class="w-full h-auto px-4 py-3 rounded-xl border-primary-200 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                            >
                                <SelectValue :placeholder="$t('admin.rentals.select_status')" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectGroup>
                                    <SelectLabel>{{ $t("common.labels.status") }}</SelectLabel>
                                    <SelectItem value="confirmed">
                                        {{ $t("common.statuses.confirmed") }}
                                    </SelectItem>
                                    <SelectItem value="ongoing">
                                        {{ $t("common.statuses.ongoing") }}
                                    </SelectItem>
                                    <SelectItem value="pending">
                                        {{ $t("common.statuses.pending") }}
                                    </SelectItem>
                                    <SelectItem value="completed">
                                        {{ $t("common.statuses.completed") }}
                                    </SelectItem>
                                    <SelectItem value="cancelled">
                                        {{ $t("common.statuses.cancelled") }}
                                    </SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>

                        <p
                            v-if="form.errors.status"
                            class="text-red-500 text-sm mt-2"
                        >
                            {{ form.errors.status }}
                        </p>
                    </div>

                    <div
                        class="flex flex-wrap items-center gap-3 pt-5 border-t border-primary-100/70"
                    >
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-3 bg-primary-600 text-white text-sm font-semibold rounded-xl hover:bg-primary-700 transition-colors shadow-md shadow-primary-500/20 disabled:opacity-50"
                        >
                            {{
                                form.processing
                                    ? $t("common.actions.updating")
                                    : $t("admin.rentals.update_status")
                            }}
                        </button>
                        <Link
                            :href="route('rentals.index')"
                            class="px-6 py-3 text-muted-foreground text-sm font-medium rounded-xl hover:bg-primary-50 transition-colors"
                        >
                            {{ $t("common.actions.cancel") }}
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
