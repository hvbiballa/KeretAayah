<script setup>
import CarForm from "./partials/CarForm.vue";

defineProps({
    car: Object,
});
</script>

<template>
    <CarForm :car="car" />
</template>
