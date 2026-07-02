<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Link, useForm } from "@inertiajs/vue3";
import { imageUrl } from "@/lib/car";
import { computed, ref } from "vue";

const props = defineProps({
    car: Object,
});

const form = useForm({
    name: props.car?.name || "",
    number_plate: props.car?.number_plate || "",
    brand: props.car?.brand || "",
    model: props.car?.model || "",
    year: props.car?.year || "",
    car_type: props.car?.car_type || "",
    daily_rent_price: props.car?.daily_rent_price || "",
    hourly_rent_price: props.car?.hourly_rent_price || "",
    status: props.car?.status || "Available",
    images: [],
    removed_image_ids: [],
});

const existingImages = ref(props.car?.images || []);
const newImages = ref([]);

const imagePreviews = computed(() => [
    ...existingImages.value.map((image) => ({
        ...image,
        src: imageUrl(image.path),
        isExisting: true,
    })),
    ...newImages.value.map((image, index) => ({
        id: `new-${index}`,
        src: image.preview,
        isExisting: false,
        index,
    })),
]);

const handleImages = (event) => {
    const files = Array.from(event.target.files);

    newImages.value.push(
        ...files.map((file) => ({
            file,
            preview: URL.createObjectURL(file),
        })),
    );
    form.images = newImages.value.map((image) => image.file);
    event.target.value = "";
};

const removeImage = (image) => {
    if (image.isExisting) {
        existingImages.value = existingImages.value.filter(
            (existingImage) => existingImage.id !== image.id,
        );
        form.removed_image_ids.push(image.id);
        return;
    }

    URL.revokeObjectURL(newImages.value[image.index].preview);
    newImages.value.splice(image.index, 1);
    form.images = newImages.value.map((newImage) => newImage.file);
};

const handleSubmit = () => {
    if (props.car) {
        form.post(route("cars.update", props.car.id));
        return;
    } else {
        form.post(route("cars.store"));
        return;
    }
};
</script>

<template>
    <AdminLayout>
        <template #header-title>
            <h1 class="text-lg font-semibold text-foreground">
                {{ props.car ? $t("car.admin.edit_title") : $t("car.admin.create_title") }}
            </h1>
            <p class="text-sm text-primary-500">
                {{
                    props.car
                        ? $t("car.admin.edit_subtitle")
                        : $t("car.admin.create_subtitle")
                }}
            </p>
        </template>

        <div class="max-w-3xl mx-auto">
            <div class="bg-white rounded-2xl border border-primary-100 p-8">
                <form class="space-y-6" @submit.prevent="handleSubmit">
                    <div class="grid sm:grid-cols-2 gap-5">
                        <div>
                            <label
                                class="block text-sm font-medium text-foreground mb-1.5"
                                >{{ $t("car.admin.car_name") }}</label
                            >
                            <input
                                v-model="form.name"
                                type="text"
                                placeholder="e.g. Camry"
                                class="w-full px-4 py-3 rounded-xl border border-primary-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                            />
                            <div
                                v-if="form.errors.name"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.name }}
                            </div>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-foreground mb-1.5"
                                >{{ $t("car.admin.number_plate") }}</label
                            >
                            <input
                                v-model="form.number_plate"
                                type="text"
                                placeholder="e.g. ABC 1234"
                                class="w-full px-4 py-3 rounded-xl border border-primary-200 text-sm uppercase focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                            />
                            <div
                                v-if="form.errors.number_plate"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.number_plate }}
                            </div>
                        </div>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-5">
                        <div>
                            <label
                                class="block text-sm font-medium text-foreground mb-1.5"
                                >{{ $t("common.labels.brand") }}</label
                            >
                            <input
                                v-model="form.brand"
                                type="text"
                                placeholder="e.g. Toyota"
                                class="w-full px-4 py-3 rounded-xl border border-primary-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                            />
                            <div
                                v-if="form.errors.brand"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.brand }}
                            </div>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-foreground mb-1.5"
                                >{{ $t("common.labels.model") }}</label
                            >
                            <input
                                v-model="form.model"
                                type="text"
                                placeholder="e.g. Camry XLE"
                                class="w-full px-4 py-3 rounded-xl border border-primary-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                            />
                            <div
                                v-if="form.errors.model"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.model }}
                            </div>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-foreground mb-1.5"
                                >{{ $t("car.admin.year_manufacture") }}</label
                            >
                            <input
                                v-model="form.year"
                                type="number"
                                placeholder="e.g. 2023"
                                min="1900"
                                class="w-full px-4 py-3 rounded-xl border border-primary-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                            />
                            <div
                                v-if="form.errors.year"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.year }}
                            </div>
                        </div>
                    </div>
                    <div class="grid sm:grid-cols-3 gap-5">
                        <div>
                            <label
                                class="block text-sm font-medium text-foreground mb-1.5"
                                >{{ $t("car.admin.car_type") }}</label
                            >
                            <input
                                v-model="form.car_type"
                                type="text"
                                placeholder="e.g. 2023"
                                class="w-full px-4 py-3 rounded-xl border border-primary-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                            />
                            <div
                                v-if="form.errors.car_type"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.car_type }}
                            </div>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-foreground mb-1.5"
                                >{{ $t("car.admin.daily_price") }}</label
                            >
                            <input
                                type="number"
                                v-model="form.daily_rent_price"
                                step="0.01"
                                placeholder="e.g. 75.00"
                                class="w-full px-4 py-3 rounded-xl border border-primary-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                            />
                            <div
                                v-if="form.errors.daily_rent_price"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.daily_rent_price }}
                            </div>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-foreground mb-1.5"
                                >{{ $t("car.admin.hourly_price") }}</label
                            >
                            <input
                                type="number"
                                v-model="form.hourly_rent_price"
                                step="0.01"
                                placeholder="e.g. 15.00"
                                class="w-full px-4 py-3 rounded-xl border border-primary-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                            />
                            <div
                                v-if="form.errors.hourly_rent_price"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.hourly_rent_price }}
                            </div>
                        </div>
                    </div>
                    <div>
                        <label
                            for="status"
                            class="block text-sm font-medium text-foreground mb-1.5"
                            >{{ $t("car.admin.car_status") }}</label
                        >
                        <select
                            id="status"
                            v-model="form.status"
                            class="w-full px-4 py-3 rounded-xl border border-primary-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                        >
                            <option value="Available">{{ $t("common.statuses.available") }}</option>
                            <option value="Under Maintenance">
                                {{ $t("common.statuses.under_maintenance") }}
                            </option>
                        </select>
                        <div
                            v-if="form.errors.status"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.status }}
                        </div>
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-foreground mb-1.5"
                            >{{ $t("car.admin.car_images") }}</label
                        >
                        <p class="text-xs text-primary-500 mb-3">
                            {{ $t("car.admin.primary_image_note") }}
                        </p>
                        <div
                            v-if="imagePreviews.length"
                            class="grid sm:grid-cols-3 gap-3 mb-4"
                        >
                            <div
                                v-for="(image, index) in imagePreviews"
                                :key="image.id"
                                class="relative"
                            >
                                <img
                                    :src="image.src"
                                    class="w-full aspect-video bg-gray-100 object-cover rounded-lg border border-primary-100"
                                />
                                <span
                                    v-if="index === 0"
                                    class="absolute top-2 left-2 px-2 py-1 rounded-md bg-primary-600 text-white text-xs font-semibold"
                                >
                                    {{ $t("car.admin.primary") }}
                                </span>
                                <button
                                    type="button"
                                    class="absolute top-2 right-2 px-2 py-1 rounded-md bg-white/90 text-red-600 text-xs font-semibold shadow"
                                    @click="removeImage(image)"
                                >
                                    {{ $t("common.actions.remove") }}
                                </button>
                            </div>
                        </div>
                        <label
                            for="images"
                            class="border-2 border-dashed border-primary-200 rounded-xl p-8 text-center hover:border-primary-400 transition-colors cursor-pointer block"
                        >
                            <svg
                                class="w-10 h-10 text-primary-200 mx-auto mb-3"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.5"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"
                                />
                            </svg>
                            <span class="text-sm text-muted-foreground block">
                                {{ $t("car.admin.upload_prompt") }}
                            </span>
                            <span class="text-xs text-primary-500 mt-1 block">
                                {{ $t("car.admin.upload_hint") }}
                            </span>
                        </label>
                        <input
                            id="images"
                            name="images"
                            type="file"
                            accept="image/*"
                            multiple
                            @change="handleImages"
                            class="w-full text-sm hidden"
                        />

                        <div
                            v-if="form.errors.images"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.images }}
                        </div>
                    </div>

                    <div
                        class="flex items-center gap-3 pt-4 border-t border-primary-100/70"
                    >
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-3 bg-primary-600 text-white text-sm font-semibold rounded-xl hover:bg-primary-700 transition-colors shadow-md shadow-primary-500/20"
                            :class="['disabled:opacity-50']"
                        >
                            {{
                                props.car
                                    ? form.processing
                                        ? $t("common.actions.updating")
                                        : $t("car.admin.update_car")
                                    : form.processing
                                      ? $t("common.actions.adding")
                                      : $t("car.admin.add_car")
                            }}
                        </button>
                        <Link
                            :href="route('cars.index')"
                            class="px-6 py-3 text-muted-foreground text-sm font-medium rounded-xl hover:bg-primary-50 transition-colors"
                            >{{ $t("common.actions.cancel") }}</Link
                        >
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
