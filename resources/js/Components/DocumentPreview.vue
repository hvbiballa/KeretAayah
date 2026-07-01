<script setup>
import Modal from "@/Components/Modal.vue";
import { FileText, Image as ImageIcon, ExternalLink, X } from "@lucide/vue";
import { computed, ref } from "vue";

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    url: {
        type: String,
        default: "",
    },
    fileName: {
        type: String,
        default: "",
    },
    fileType: {
        type: String,
        default: "",
    },
    available: {
        type: Boolean,
        default: false,
    },
    missingText: {
        type: String,
        default: "",
    },
});

const isOpen = ref(false);

const normalizedType = computed(() => props.fileType?.toLowerCase() || "");
const isImage = computed(() => ["jpg", "jpeg", "png", "webp"].includes(normalizedType.value));
const isPdf = computed(() => normalizedType.value === "pdf");
const displayType = computed(() => (props.fileType ? props.fileType.toUpperCase() : "FILE"));
</script>

<template>
    <div
        v-if="available"
        class="overflow-hidden rounded-xl border border-primary-100 bg-white shadow-sm"
    >
        <button
            type="button"
            class="block w-full text-left transition-colors hover:bg-primary-50/40"
            @click="isOpen = true"
        >
            <div
                class="flex min-h-28 items-center gap-3 p-3"
                :class="isImage ? 'items-start' : ''"
            >
                <div
                    class="flex size-16 shrink-0 items-center justify-center overflow-hidden rounded-lg border border-primary-100 bg-primary-50"
                >
                    <img
                        v-if="isImage"
                        :src="url"
                        :alt="title"
                        class="h-full w-full object-cover"
                    />
                    <FileText v-else-if="isPdf" class="size-7 text-primary-600" />
                    <ImageIcon v-else class="size-7 text-primary-600" />
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-foreground">
                        {{ title }}
                    </p>
                    <p class="mt-1 text-xs text-muted-foreground">
                        {{ $t("verification.protected_document") }}
                    </p>
                    <p class="mt-2 text-xs font-semibold text-primary-600">
                        {{ $t("verification.view_upload") }}
                    </p>
                    <span
                        class="mt-2 inline-flex rounded-lg bg-primary-50 px-2 py-1 text-[11px] font-semibold text-primary-700"
                    >
                        {{ displayType }}
                    </span>
                </div>
            </div>
        </button>

        <Modal :show="isOpen" max-width="2xl" @close="isOpen = false">
            <div class="flex items-center justify-between border-b border-primary-100 px-4 py-3 sm:px-5">
                <div class="min-w-0">
                    <h3 class="truncate text-base font-bold text-foreground">
                        {{ title }}
                    </h3>
                    <p class="text-xs text-primary-500">
                        {{ $t("verification.protected_document") }}
                    </p>
                </div>
                <div class="flex items-center gap-1">
                    <a
                        :href="url"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex size-9 items-center justify-center rounded-lg text-primary-600 hover:bg-primary-50"
                        :aria-label="$t('common.actions.open_new_tab')"
                    >
                        <ExternalLink class="size-4" />
                    </a>
                    <button
                        type="button"
                        class="inline-flex size-9 items-center justify-center rounded-lg text-primary-600 hover:bg-primary-50"
                        :aria-label="$t('common.actions.close')"
                        @click="isOpen = false"
                    >
                        <X class="size-4" />
                    </button>
                </div>
            </div>
            <div class="bg-primary-50/40 p-4 sm:p-5">
                <img
                    v-if="isImage"
                    :src="url"
                    :alt="title"
                    class="max-h-[70vh] w-full rounded-xl border border-primary-100 bg-white object-contain"
                />
                <iframe
                    v-else-if="isPdf"
                    :src="url"
                    class="h-[70vh] w-full rounded-xl border border-primary-100 bg-white"
                    :title="title"
                ></iframe>
                <div
                    v-else
                    class="rounded-xl border border-primary-100 bg-white p-6 text-center"
                >
                    <FileText class="mx-auto size-10 text-primary-600" />
                    <p class="mt-3 text-sm font-semibold text-foreground">
                        {{ $t("verification.preview_unavailable") }}
                    </p>
                    <a
                        :href="url"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="mt-4 inline-flex rounded-xl bg-primary-600 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-700"
                    >
                        {{ $t("common.actions.open_new_tab") }}
                    </a>
                </div>
            </div>
        </Modal>
    </div>
    <div
        v-else
        class="rounded-xl border border-dashed border-primary-200 bg-primary-50/40 p-4 text-sm text-primary-500"
    >
        {{ missingText || $t("common.labels.not_submitted") }}
    </div>
</template>
