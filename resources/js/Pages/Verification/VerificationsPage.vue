<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import DocumentPreview from "@/Components/DocumentPreview.vue";
import TouchDateTimePicker from "@/Components/TouchDateTimePicker.vue";
import { router } from "@inertiajs/vue3";
import { reactive } from "vue";
import { useI18n } from "@/lib/i18n";

const props = defineProps({
    customers: Array,
});
const { t } = useI18n();

const verificationStatusLabel = (status) =>
    ({
        Pending: t("common.statuses.pending"),
        Approved: t("common.statuses.approved"),
        Rejected: t("common.statuses.rejected"),
        Suspended: t("common.statuses.suspended"),
        Expired: t("common.statuses.expired"),
    })[status] || status;

const expiryDates = reactive({});
const notes = reactive({});

const verification = (customer) =>
    customer.verification || {
        status: "Pending",
        display_status: "Pending",
    };

const approve = (customer) => {
    router.post(route("verifications.approve", customer.id), {
        id_expiry_date: expiryValue(customer, "id"),
        driving_license_expiry_date: expiryValue(customer, "license"),
    });
};

const updateStatus = (customer, action) => {
    router.post(route("verifications.status", customer.id), {
        action,
        review_note: notes[customer.id] || "",
    });
};

const expiryValue = (customer, field) =>
    expiryDates[customer.id]?.[field] ||
    verification(customer)[
        field === "id" ? "id_expiry_date" : "driving_license_expiry_date"
    ] ||
    "";

const setExpiryValue = (customer, field, value) => {
    expiryDates[customer.id] = {
        ...expiryDates[customer.id],
        [field]: value,
    };
};

const statusClasses = {
    Pending: "bg-amber-50 text-amber-700",
    Approved: "bg-green-50 text-green-700",
    Rejected: "bg-red-50 text-red-700",
    Suspended: "bg-primary-50 text-foreground",
    Expired: "bg-red-50 text-red-700",
};
</script>

<template>
    <AdminLayout>
        <template #header-title>
            <h1 class="text-lg font-semibold text-foreground">
                {{ $t("verification.management_title") }}
            </h1>
            <p class="text-sm text-primary-500">
                {{ $t("verification.management_subtitle") }}
            </p>
        </template>

        <div class="space-y-5">
            <div
                v-for="customer in customers"
                :key="customer.id"
                class="bg-white rounded-2xl border border-primary-100 p-6 shadow-sm"
            >
                <div
                    class="flex flex-wrap items-start justify-between gap-4 mb-5"
                >
                    <div>
                        <h2 class="text-base font-bold text-foreground">
                            {{ customer.name }}
                        </h2>
                        <p class="text-sm text-muted-foreground">
                            {{ customer.email }}
                        </p>
                    </div>
                    <span
                        class="px-3 py-1 rounded-lg text-xs font-semibold"
                        :class="
                            statusClasses[
                                verification(customer).display_status
                            ] || statusClasses.Pending
                        "
                    >
                        {{ verificationStatusLabel(verification(customer).display_status) }}
                    </span>
                </div>

                <div class="flex flex-wrap gap-4 mb-5 text-sm">
                    <DocumentPreview
                        class="w-full sm:w-[calc(50%-0.5rem)]"
                        :title="$t('verification.government_id')"
                        :available="verification(customer).documents?.government_id?.available"
                        :url="verification(customer).documents?.government_id?.url"
                        :file-name="verification(customer).documents?.government_id?.file_name"
                        :file-type="verification(customer).documents?.government_id?.file_type"
                        :missing-text="$t('verification.government_id_missing')"
                    />
                    <DocumentPreview
                        class="w-full sm:w-[calc(50%-0.5rem)]"
                        :title="$t('verification.driving_license')"
                        :available="verification(customer).documents?.driving_license?.available"
                        :url="verification(customer).documents?.driving_license?.url"
                        :file-name="verification(customer).documents?.driving_license?.file_name"
                        :file-type="verification(customer).documents?.driving_license?.file_type"
                        :missing-text="$t('verification.driving_license_missing')"
                    />
                </div>

                <p
                    v-if="verification(customer).review_note"
                    class="p-3 mb-5 rounded-xl bg-amber-50 text-sm text-amber-800"
                >
                    {{ $t("verification.current_note") }}: {{ verification(customer).review_note }}
                </p>

                <div class="grid md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs text-muted-foreground mb-1">
                            {{ $t("verification.government_id_expiry") }}
                        </label>
                        <TouchDateTimePicker
                            type="date"
                            :model-value="expiryValue(customer, 'id')"
                            :placeholder="$t('verification.select_id_expiry')"
                            :aria-label="$t('verification.government_id_expiry')"
                            @update:model-value="
                                setExpiryValue(customer, 'id', $event)
                            "
                        />
                    </div>
                    <div>
                        <label class="block text-xs text-muted-foreground mb-1">
                            {{ $t("verification.driving_license_expiry") }}
                        </label>
                        <TouchDateTimePicker
                            type="date"
                            :model-value="expiryValue(customer, 'license')"
                            :placeholder="$t('verification.select_license_expiry')"
                            :aria-label="$t('verification.driving_license_expiry')"
                            @update:model-value="
                                setExpiryValue(customer, 'license', $event)
                            "
                        />
                    </div>
                </div>

                <div class="mb-4">
                    <label
                        class="block text-sm font-medium text-foreground mb-1.5"
                    >
                        {{ $t("verification.admin_note_label") }}
                    </label>
                    <textarea
                        v-model="notes[customer.id]"
                        rows="2"
                        :placeholder="$t('verification.admin_note_placeholder')"
                        class="w-full px-4 py-3 rounded-xl border border-primary-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                    ></textarea>
                </div>

                <div class="flex flex-wrap gap-2">
                    <button
                        v-if="verification(customer).status !== 'Suspended'"
                        @click="approve(customer)"
                        class="px-4 py-2.5 rounded-xl bg-green-600 text-white text-sm font-semibold hover:bg-green-700 transition-colors"
                    >
                        {{ $t("common.actions.approve") }}
                    </button>
                    <button
                        v-if="verification(customer).status !== 'Suspended'"
                        @click="updateStatus(customer, 'reject')"
                        class="px-4 py-2.5 rounded-xl bg-red-600 text-white text-sm font-semibold hover:bg-red-700 transition-colors"
                    >
                        {{ $t("common.actions.reject") }}
                    </button>
                    <button
                        v-if="verification(customer).status !== 'Suspended'"
                        @click="updateStatus(customer, 'request_documents')"
                        class="px-4 py-2.5 rounded-xl bg-amber-500 text-white text-sm font-semibold hover:bg-amber-600 transition-colors"
                    >
                        {{ $t("verification.request_documents") }}
                    </button>
                    <button
                        v-if="verification(customer).status !== 'Suspended'"
                        @click="updateStatus(customer, 'suspend')"
                        class="px-4 py-2.5 rounded-xl bg-primary-900 text-white text-sm font-semibold hover:bg-primary-950 transition-colors"
                    >
                        {{ $t("verification.suspend") }}
                    </button>
                    <button
                        v-else
                        @click="updateStatus(customer, 'lift_suspension')"
                        class="px-4 py-2.5 rounded-xl bg-primary-600 text-white text-sm font-semibold hover:bg-primary-700 transition-colors shadow-md shadow-primary-500/20"
                    >
                        {{ $t("verification.lift_suspension") }}
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
