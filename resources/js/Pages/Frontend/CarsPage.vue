<script setup>
import CarList from "@/Components/Car/CarList.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { Head, usePage } from "@inertiajs/vue3";

const page = usePage();
const cars = page.props.cars;
</script>

<template>
    <Head :title="$t('car.cars_title')" />
    <GuestLayout>
        <!-- Page Header -->
        <section
            class="bg-gradient-to-br from-primary-700 via-primary-500 to-primary-900 py-20"
        >
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <span
                    class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-primary-500/15 text-primary-300 text-xs font-semibold uppercase tracking-wider mb-4"
                    >{{ $t("car.fleet_badge") }}</span
                >
                <h1 class="text-4xl lg:text-5xl font-extrabold text-white mb-4">
                    {{ $t("car.browse_available") }}
                </h1>
                <p class="text-primary-100 text-lg max-w-2xl mx-auto">
                    {{ $t("car.browse_copy") }}
                </p>
            </div>
        </section>

        <section class="py-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Filters and sorting are disabled until the catalogue filter redesign. -->

                <!-- Results Info -->
                <div class="flex items-center justify-between mb-6">
                    <p class="text-sm text-muted-foreground">
                        {{ $t("common.labels.showing") }}
                        <strong class="text-foreground">{{
                            $page.props.cars.length
                        }}</strong>
                        {{ $t("car.showing_cars") }}
                    </p>

                </div>

                <CarList :cars="cars" />
            </div>
        </section>
    </GuestLayout>
</template>
