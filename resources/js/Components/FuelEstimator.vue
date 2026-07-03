<script setup>
import { computed, ref } from "vue";
import { useI18n } from "@/lib/i18n";

const props = defineProps({
    fuelConsumptionRate: {
        type: [Number, String],
        default: null,
    },
});

const { t } = useI18n();

const distance = ref("");

const rate = computed(() => {
    const value = Number(props.fuelConsumptionRate);
    return Number.isFinite(value) && value > 0 ? value : null;
});

const estimatedLitres = computed(() => {
    const distanceValue = Number(distance.value);

    if (!rate.value || !Number.isFinite(distanceValue) || distanceValue <= 0) {
        return null;
    }

    return (distanceValue / rate.value).toFixed(1);
});
</script>

<template>
    <div class="p-4 bg-primary-50/40 rounded-xl border border-primary-100">
        <p class="text-sm font-semibold text-foreground mb-3">
            {{ t("car.fuel_estimator.title") }}
        </p>

        <template v-if="rate">
            <label
                class="block text-xs text-primary-500 uppercase tracking-wider mb-1.5"
            >
                {{ t("car.fuel_estimator.distance_label") }}
            </label>
            <input
                v-model="distance"
                type="number"
                min="0"
                step="0.1"
                :placeholder="t('car.fuel_estimator.distance_placeholder')"
                class="w-full px-4 py-2.5 rounded-xl border border-primary-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
            />
            <p
                v-if="estimatedLitres"
                class="mt-3 text-sm font-semibold text-primary-700"
            >
                {{ t("car.fuel_estimator.estimate_result", { litres: estimatedLitres }) }}
            </p>
        </template>
        <p v-else class="text-sm text-muted-foreground">
            {{ t("car.fuel_estimator.unavailable") }}
        </p>
    </div>
</template>
