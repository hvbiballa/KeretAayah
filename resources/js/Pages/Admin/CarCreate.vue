<script setup>
import { Head } from "@inertiajs/vue3";
import CarForm from "./partials/CarForm.vue";
</script>

<template>
    <Head title="Dashboard" />
    <CarForm :car="null" />
</template>
