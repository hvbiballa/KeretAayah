<script setup>
import DocumentPreview from "@/Components/DocumentPreview.vue";
import { useForm } from "@inertiajs/vue3";
import { computed } from "vue";
import { useI18n } from "@/lib/i18n";

const props = defineProps({
    verification: {
        type: Object,
        default: null,
    },
});

const form = useForm({
    government_id: null,
    driving_license: null,
});

const { t } = useI18n();
const currentVerification = computed(() => ({
    status: "Pending",
    display_status: "Pending",
    can_submit_documents: true,
    documents: {
        government_id: {
            available: false,
            url: null,
            file_name: null,
            file_type: null,
        },
        driving_license: {
            available: false,
            url: null,
            file_name: null,
            file_type: null,
        },
    },
    ...props.verification,
}));

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
        preserveScroll: true,
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
    <section
        id="verification"
        class="scroll-mt-24 rounded-2xl border border-primary-100 bg-white p-6 shadow-sm sm:p-8"
    >
        <div class="mb-6">
            <h2 class="text-xl font-bold text-foreground">
                {{ $t("profile.verification") }}
            </h2>
            <p class="mt-1 text-sm text-muted-foreground">
                {{ $t("profile.verification_copy") }}
            </p>
        </div>

        <div class="rounded-2xl border border-primary-100 bg-white p-6 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-xs uppercase tracking-wider text-primary-500">
                        {{ $t("verification.status") }}
                    </p>
                    <span
                        class="mt-2 inline-flex rounded-lg px-3 py-1 text-sm font-semibold"
                        :class="
                            statusClasses[currentVerification.display_status] ||
                            statusClasses.Pending
                        "
                    >
                        {{ verificationStatusLabel(currentVerification.display_status) }}
                    </span>
                </div>
                <div
                    v-if="currentVerification.verified_at"
                    class="text-sm text-muted-foreground"
                >
                    {{ $t("verification.verified") }}:
                    {{ new Date(currentVerification.verified_at).toLocaleDateString() }}
                </div>
            </div>

            <p
                v-if="currentVerification.review_note"
                class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-3 text-sm text-amber-800"
            >
                {{ $t("verification.admin_note") }}: {{ currentVerification.review_note }}
            </p>

            <div class="mt-5 grid gap-4 text-sm sm:grid-cols-2">
                <div class="rounded-xl bg-primary-50/50 p-4">
                    <p class="text-xs uppercase tracking-wider text-primary-500">
                        {{ $t("verification.government_id_expiry") }}
                    </p>
                    <p class="mt-1 font-semibold text-foreground">
                        {{
                            currentVerification.id_expiry_date
                                ? $t("verification.valid_until", {
                                      date: formattedDate(
                                          currentVerification.id_expiry_date,
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
                            currentVerification.driving_license_expiry_date
                                ? $t("verification.valid_until", {
                                      date: formattedDate(
                                          currentVerification.driving_license_expiry_date,
                                      ),
                                  })
                                : $t("verification.expiry_pending")
                        }}
                    </p>
                </div>
            </div>

            <div class="mt-5">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h3 class="text-sm font-bold text-foreground">
                        {{ $t("verification.uploaded_documents") }}
                    </h3>
                    <p class="text-xs text-muted-foreground">
                        {{ $t("verification.open_upload_hint") }}
                    </p>
                </div>
                <div class="mt-3 grid gap-3 sm:grid-cols-2">
                    <DocumentPreview
                        :title="$t('verification.government_id')"
                        :available="currentVerification.documents?.government_id?.available"
                        :url="currentVerification.documents?.government_id?.url"
                        :file-name="currentVerification.documents?.government_id?.file_name"
                        :file-type="currentVerification.documents?.government_id?.file_type"
                        :missing-text="$t('verification.government_id_missing')"
                    />
                    <DocumentPreview
                        :title="$t('verification.driving_license')"
                        :available="currentVerification.documents?.driving_license?.available"
                        :url="currentVerification.documents?.driving_license?.url"
                        :file-name="currentVerification.documents?.driving_license?.file_name"
                        :file-type="currentVerification.documents?.driving_license?.file_type"
                        :missing-text="$t('verification.driving_license_missing')"
                    />
                </div>
            </div>
        </div>

        <div
            v-if="currentVerification.status === 'Suspended'"
            class="mt-6 rounded-2xl border border-red-200 bg-red-50 p-5 text-sm text-red-700"
        >
            {{ $t("verification.suspended_customer") }}
        </div>

        <div
            v-else-if="currentVerification.can_submit_documents"
            class="mt-6 rounded-2xl border border-primary-100 bg-white p-6 shadow-sm"
        >
            <h3 class="text-xl font-bold text-foreground">
                {{
                    currentVerification.documents?.government_id?.available
                        ? $t("verification.submit_replacement_documents")
                        : $t("verification.submit_documents")
                }}
            </h3>
            <p class="mb-6 mt-1 text-sm text-muted-foreground">
                {{ $t("verification.submit_copy") }}
            </p>

            <form @submit.prevent="submit" class="space-y-5">
                <div>
                    <label class="mb-2 block text-sm font-medium text-foreground">
                        {{ $t("verification.government_id") }}
                    </label>
                    <input
                        type="file"
                        accept=".pdf,.jpg,.jpeg,.png"
                        class="block w-full text-sm text-muted-foreground file:mr-4 file:rounded-lg file:border-0 file:bg-primary-50 file:px-4 file:py-2.5 file:font-semibold file:text-primary-700 hover:file:bg-primary-100"
                        @input="form.government_id = $event.target.files[0]"
                    />
                    <p class="mt-2 text-xs text-red-500">
                        {{ form.errors.government_id }}
                    </p>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-foreground">
                        {{ $t("verification.driving_license") }}
                    </label>
                    <input
                        type="file"
                        accept=".pdf,.jpg,.jpeg,.png"
                        class="block w-full text-sm text-muted-foreground file:mr-4 file:rounded-lg file:border-0 file:bg-primary-50 file:px-4 file:py-2.5 file:font-semibold file:text-primary-700 hover:file:bg-primary-100"
                        @input="form.driving_license = $event.target.files[0]"
                    />
                    <p class="mt-2 text-xs text-red-500">
                        {{ form.errors.driving_license }}
                    </p>
                </div>

                <p class="text-xs text-primary-500">
                    {{ $t("common.files.accepted_documents") }}
                </p>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full rounded-xl bg-primary-600 py-3 font-semibold text-white transition-colors hover:bg-primary-700 disabled:opacity-50"
                >
                    {{ $t("verification.submit_documents") }}
                </button>
            </form>
        </div>

        <div
            v-else
            class="mt-6 rounded-2xl border border-primary-200 bg-primary-50 p-5 text-sm text-primary-700"
        >
            {{ $t("verification.already_submitted") }}
        </div>
    </section>
</template>
