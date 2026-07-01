<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import DocumentPreview from "@/Components/DocumentPreview.vue";
import { useForm } from "@inertiajs/vue3";
import { useI18n } from "@/lib/i18n";

const props = defineProps({
    verification: Object,
});

const form = useForm({
    government_id: null,
    driving_license: null,
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

const formattedDate = (date) => {
    if (!date) {
        return t("verification.expiry_pending");
    }

    return new Intl.DateTimeFormat("en-MY", {
        day: "numeric",
        month: "short",
        year: "numeric",
    }).format(new Date(`${date}T00:00:00+08:00`));
};

const submit = () => {
    form.post(route("customer.verification.store"), {
        forceFormData: true,
        onSuccess: () => form.reset(),
    });
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
    <GuestLayout>
        <section class="py-10">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-8">
                    <h1 class="text-3xl font-extrabold text-foreground">
                        {{ $t("verification.customer_verification") }}
                    </h1>
                    <p class="text-sm text-muted-foreground mt-2">
                        {{ $t("verification.customer_intro") }}
                    </p>
                </div>

                <div
                    class="bg-white rounded-2xl border border-primary-100 p-6 mb-6 shadow-sm"
                >
                    <div
                        class="flex flex-wrap items-center justify-between gap-3"
                    >
                        <div>
                            <p
                                class="text-xs uppercase tracking-wider text-primary-500"
                            >
                                {{ $t("verification.status") }}
                            </p>
                            <span
                                class="inline-flex mt-2 px-3 py-1 rounded-lg text-sm font-semibold"
                                :class="
                                    statusClasses[
                                        verification.display_status
                                    ] || statusClasses.Pending
                                "
                            >
                                {{ verificationStatusLabel(verification.display_status) }}
                            </span>
                        </div>
                        <div
                            v-if="verification.verified_at"
                            class="text-sm text-muted-foreground"
                        >
                            {{ $t("verification.verified") }}:
                            {{
                                new Date(
                                    verification.verified_at,
                                ).toLocaleDateString()
                            }}
                        </div>
                    </div>

                    <p
                        v-if="verification.review_note"
                        class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-xl text-sm text-amber-800"
                    >
                        {{ $t("verification.admin_note") }}: {{ verification.review_note }}
                    </p>

                    <div class="grid sm:grid-cols-2 gap-4 mt-5 text-sm">
                        <div class="rounded-xl bg-primary-50/50 p-4">
                            <p class="text-xs uppercase tracking-wider text-primary-500">
                                {{ $t("verification.government_id_expiry") }}
                            </p>
                            <p class="mt-1 font-semibold text-foreground">
                                {{
                                    verification.id_expiry_date
                                        ? $t("verification.valid_until", {
                                              date: formattedDate(
                                                  verification.id_expiry_date,
                                              ),
                                          })
                                        : $t("verification.expiry_pending")
                                }}
                            </p>
                        </div>
                        <div class="rounded-xl bg-primary-50/50 p-4">
                            <p class="text-xs uppercase tracking-wider text-primary-500">
                                {{ $t("verification.driving_license_expiry") }}
                            </p>
                            <p class="mt-1 font-semibold text-foreground">
                                {{
                                    verification.driving_license_expiry_date
                                        ? $t("verification.valid_until", {
                                              date: formattedDate(
                                                  verification.driving_license_expiry_date,
                                              ),
                                          })
                                        : $t("verification.expiry_pending")
                                }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-5">
                        <h2 class="text-sm font-bold text-foreground">
                            {{ $t("verification.uploaded_documents") }}
                        </h2>
                        <div class="mt-3 grid gap-3 sm:grid-cols-2">
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
                    </div>
                </div>

                <div
                    v-if="verification.status === 'Suspended'"
                    class="p-5 bg-red-50 border border-red-200 rounded-2xl text-sm text-red-700"
                >
                    {{ $t("verification.suspended_customer") }}
                </div>

                <div
                    v-else-if="verification.can_submit_documents"
                    class="bg-white rounded-2xl border border-primary-100 p-6 shadow-sm"
                >
                    <h2 class="text-xl font-bold text-foreground">
                        {{
                            verification.documents?.government_id?.available
                                ? $t("verification.submit_replacement_documents")
                                : $t("verification.submit_documents")
                        }}
                    </h2>
                    <p class="text-sm text-muted-foreground mt-1 mb-6">
                        {{ $t("verification.submit_copy") }}
                    </p>

                    <form @submit.prevent="submit" class="space-y-5">
                        <div>
                            <label
                                class="block text-sm font-medium text-foreground mb-2"
                            >
                                {{ $t("verification.government_id") }}
                            </label>
                            <input
                                type="file"
                                accept=".pdf,.jpg,.jpeg,.png"
                                @input="
                                    form.government_id =
                                        $event.target.files[0]
                                "
                                class="block w-full text-sm text-muted-foreground file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:bg-primary-50 file:text-primary-700 file:font-semibold hover:file:bg-primary-100"
                            />
                            <p class="text-xs text-red-500 mt-2">
                                {{ form.errors.government_id }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-foreground mb-2"
                            >
                                {{ $t("verification.driving_license") }}
                            </label>
                            <input
                                type="file"
                                accept=".pdf,.jpg,.jpeg,.png"
                                @input="
                                    form.driving_license =
                                        $event.target.files[0]
                                "
                                class="block w-full text-sm text-muted-foreground file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:bg-primary-50 file:text-primary-700 file:font-semibold hover:file:bg-primary-100"
                            />
                            <p class="text-xs text-red-500 mt-2">
                                {{ form.errors.driving_license }}
                            </p>
                        </div>

                        <p class="text-xs text-primary-500">
                            {{ $t("common.files.accepted_documents") }}
                        </p>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full py-3 bg-primary-600 text-white font-semibold rounded-xl hover:bg-primary-700 transition-colors shadow-md shadow-primary-500/20 disabled:opacity-50"
                        >
                            {{ $t("verification.submit_documents") }}
                        </button>
                    </form>
                </div>

                <div
                    v-else
                    class="p-5 bg-primary-50 border border-primary-200 rounded-2xl text-sm text-primary-700"
                >
                    {{ $t("verification.already_submitted") }}
                </div>
            </div>
        </section>
    </GuestLayout>
</template>
