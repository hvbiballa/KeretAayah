<script setup>
import { useForm } from "@inertiajs/vue3";
import { useI18n } from "@/lib/i18n";

const props = defineProps({
    emailVerified: {
        type: Boolean,
        default: false,
    },
    verification: {
        type: Object,
        default: null,
    },
});

const { t } = useI18n();

const emailForm = useForm({});
const documentForm = useForm({
    government_id: null,
    driving_license: null,
});

const verificationStatusLabel = (status) =>
    ({
        Pending: t("common.statuses.pending"),
        Approved: t("common.statuses.approved"),
        Rejected: t("common.statuses.rejected"),
        Suspended: t("common.statuses.suspended"),
        Expired: t("common.statuses.expired"),
    })[status] || status;

const resendVerification = () => {
    emailForm.post(route("verification.send"), {
        preserveScroll: true,
    });
};

const submitDocuments = () => {
    documentForm.post(route("customer.verification.store"), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => documentForm.reset(),
    });
};
</script>

<template>
    <section class="rounded-[2rem] border border-primary-100 bg-white p-6 shadow-[0_24px_80px_-36px_rgba(30,41,59,0.35)]">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="max-w-2xl">
                <p class="text-sm font-semibold tracking-[0.18em] text-primary-500">
                    {{ $t("booking.verification_step") }}
                </p>
                <h2 class="mt-2 text-2xl font-extrabold tracking-tight text-foreground">
                    {{ $t("booking.verification_gate_title") }}
                </h2>
                <p class="mt-2 text-sm leading-6 text-muted-foreground">
                    {{ $t("booking.verification_gate_copy") }}
                </p>
            </div>
            <span
                class="inline-flex rounded-full px-3 py-1 text-xs font-semibold"
                :class="
                    emailVerified && verification?.can_book
                        ? 'bg-green-50 text-green-700'
                        : 'bg-amber-50 text-amber-700'
                "
            >
                {{
                    emailVerified && verification?.can_book
                        ? $t("booking.ready_to_confirm")
                        : $t("booking.action_required")
                }}
            </span>
        </div>

        <div class="mt-6 grid gap-5 lg:grid-cols-2">
            <div class="rounded-[1.5rem] border border-primary-100 bg-primary-50/40 p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-primary-500">
                    {{ $t("booking.email_check_title") }}
                </p>
                <p class="mt-3 text-lg font-semibold text-foreground">
                    {{
                        emailVerified
                            ? $t("booking.email_check_done")
                            : $t("booking.email_check_pending")
                    }}
                </p>
                <p class="mt-2 text-sm leading-6 text-muted-foreground">
                    {{
                        emailVerified
                            ? $t("booking.email_check_done_copy")
                            : $t("booking.email_check_pending_copy")
                    }}
                </p>
                <button
                    v-if="!emailVerified"
                    type="button"
                    :disabled="emailForm.processing"
                    class="mt-4 inline-flex min-h-11 items-center justify-center rounded-xl bg-primary-600 px-4 text-sm font-semibold text-white transition-colors hover:bg-primary-700 disabled:opacity-60"
                    @click="resendVerification"
                >
                    {{ $t("auth.resend_verification") }}
                </button>
            </div>

            <div class="rounded-[1.5rem] border border-primary-100 bg-primary-50/40 p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-primary-500">
                    {{ $t("booking.identity_check_title") }}
                </p>
                <div class="mt-3 flex flex-wrap items-center gap-3">
                    <p class="text-lg font-semibold text-foreground">
                        {{
                            verificationStatusLabel(
                                verification?.display_status || "Pending",
                            )
                        }}
                    </p>
                    <span
                        class="inline-flex rounded-full px-3 py-1 text-xs font-semibold"
                        :class="
                            verification?.can_book
                                ? 'bg-green-50 text-green-700'
                                : 'bg-amber-50 text-amber-700'
                        "
                    >
                        {{
                            verification?.can_book
                                ? $t("booking.identity_check_done")
                                : $t("booking.identity_check_pending")
                        }}
                    </span>
                </div>
                <p class="mt-2 text-sm leading-6 text-muted-foreground">
                    {{
                        verification?.can_book
                            ? $t("booking.identity_check_done_copy")
                            : verification?.booking_restriction_message ||
                              $t("verification.restriction.pending")
                    }}
                </p>

                <p
                    v-if="verification?.review_note"
                    class="mt-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800"
                >
                    {{ $t("verification.admin_note") }}: {{ verification.review_note }}
                </p>
            </div>
        </div>

        <form
            v-if="verification?.can_submit_documents"
            class="mt-6 grid gap-5 rounded-[1.5rem] border border-primary-100 bg-white p-5 lg:grid-cols-2"
            @submit.prevent="submitDocuments"
        >
            <div>
                <label class="text-sm font-semibold text-foreground">
                    {{ $t("verification.government_id") }}
                </label>
                <input
                    type="file"
                    accept=".pdf,.jpg,.jpeg,.png"
                    class="mt-2 block w-full text-sm text-muted-foreground file:mr-4 file:rounded-xl file:border-0 file:bg-primary-50 file:px-4 file:py-2.5 file:font-semibold file:text-primary-700 hover:file:bg-primary-100"
                    @input="documentForm.government_id = $event.target.files[0]"
                />
                <p v-if="documentForm.errors.government_id" class="mt-2 text-xs text-red-500">
                    {{ documentForm.errors.government_id }}
                </p>
            </div>

            <div>
                <label class="text-sm font-semibold text-foreground">
                    {{ $t("verification.driving_license") }}
                </label>
                <input
                    type="file"
                    accept=".pdf,.jpg,.jpeg,.png"
                    class="mt-2 block w-full text-sm text-muted-foreground file:mr-4 file:rounded-xl file:border-0 file:bg-primary-50 file:px-4 file:py-2.5 file:font-semibold file:text-primary-700 hover:file:bg-primary-100"
                    @input="documentForm.driving_license = $event.target.files[0]"
                />
                <p v-if="documentForm.errors.driving_license" class="mt-2 text-xs text-red-500">
                    {{ documentForm.errors.driving_license }}
                </p>
            </div>

            <div class="lg:col-span-2 flex flex-wrap items-center justify-between gap-4 rounded-[1.25rem] bg-primary-50/60 px-4 py-4">
                <p class="text-xs leading-6 text-primary-700">
                    {{ $t("verification.submit_copy") }}
                </p>
                <button
                    type="submit"
                    :disabled="documentForm.processing"
                    class="inline-flex min-h-11 items-center justify-center rounded-xl bg-primary-600 px-4 text-sm font-semibold text-white transition-colors hover:bg-primary-700 disabled:opacity-60"
                >
                    {{ $t("verification.submit_documents") }}
                </button>
            </div>
        </form>
    </section>
</template>
