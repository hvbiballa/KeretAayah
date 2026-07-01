<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import { carImageUrl, carStatusLabel, formatMYR, isCarAvailable } from "@/lib/car";
import { useI18n } from "@/lib/i18n";

const page = usePage();
const { t } = useI18n();
</script>

<template>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Car Card 1 -->
        <div
            v-for="car in page.props.cars"
            :key="car.id"
            class="bg-white rounded-2xl shadow-sm border border-primary-100/80 overflow-hidden group hover:shadow-lg hover:shadow-primary-100/70 transition-all duration-300"
        >
            <div
                class="relative h-48 bg-linear-to-br from-primary-50 to-primary-50 flex items-center justify-center overflow-hidden"
            >
                <!-- <span
                                class="text-7xl group-hover:scale-110 transition-transform duration-500"
                                >🚗</span
                            > -->
                <img :src="carImageUrl(car)" alt="Image" />
                <span
                    :class="[
                        'absolute top-3 left-3 px-2.5 py-1 text-xs font-semibold rounded-lg',
                        isCarAvailable(car)
                            ? 'bg-green-100 text-green-700'
                            : 'bg-red-100 text-red-700',
                    ]"
                    >{{ carStatusLabel(car.status, t) }}</span
                >
                <span
                    class="absolute top-3 right-3 px-2.5 py-1 bg-primary-50 text-muted-foreground text-xs font-medium rounded-lg"
                    >{{ car.car_type }}</span
                >
            </div>
            <div class="p-5">
                <div class="flex items-start justify-between mb-1">
                    <h3 class="text-lg font-bold text-foreground">
                        {{ car.name }}
                    </h3>
                </div>
                <p class="text-sm text-muted-foreground mb-3">
                    {{ car.model }} • {{ car.year }}
                </p>
                <p
                    v-if="car.number_plate"
                    class="inline-flex mb-4 px-2.5 py-1 rounded-lg bg-primary-50 text-xs font-semibold text-primary-700 uppercase"
                >
                    {{ car.number_plate }}
                </p>
                <div
                    class="flex items-center justify-between pt-4 border-t border-primary-100/70"
                >
                    <p class="text-xl font-extrabold text-primary-600">
                        {{ formatMYR(car.daily_rent_price)
                        }}<span class="text-sm font-normal text-primary-500"
                            >{{ $t("car.day_suffix") }}</span
                        >
                        <span
                            class="block text-xs font-medium text-primary-500 mt-1"
                            >{{ formatMYR(car.hourly_rent_price) }}{{ $t("car.hour_suffix") }}</span
                        >
                    </p>
                    <Link
                        :href="
                            page.props.auth?.user?.role === 'customer'
                                ? route('customer.dashboard')
                                : route('cars.show', car.id)
                        "
                        class="px-4 py-2 bg-primary-600 text-white text-sm font-semibold rounded-xl hover:bg-primary-700 transition-colors"
                        >{{
                            page.props.auth?.user?.role === "customer"
                                ? $t("booking.start_with_schedule_cta")
                                : $t("car.book_now")
                        }}</Link
                    >
                </div>
            </div>
        </div>
    </div>
</template>
